<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\MetodoPago;
use App\Models\DetalleVentaCurso;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Venta;
use App\Services\AuditoriaService;
use App\Services\OrdenEstadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Inscripción a un curso. Se paga completa, como una compra de la tienda:
 * se crea una venta (con su detalle de curso y su pago pendiente) y la
 * inscripción queda pendiente con su cupo reservado. Al pagar, la inscripción
 * se confirma; si no se paga en 24 horas, se cancela y el cupo se libera.
 */
class InscripcionController extends Controller
{
    public function __construct(private OrdenEstadoService $estados) {}

    /**
     * Página para inscribirse a un curso: el cliente elige la fecha y cómo va a pagar.
     * Sin sesión, primero se pide iniciarla y luego se vuelve aquí.
     */
    public function create(Request $request, Curso $curso): View|RedirectResponse
    {
        abort_unless($curso->estado, 404);

        $volverAqui = '/cursos/' . $curso->id . '/inscribirse';

        if (! $request->user()) {
            return redirect()->route('login', ['volver' => $volverAqui]);
        }

        $volverAlCurso = redirect()->to(route('academia') . '#curso-' . $curso->id);

        if (! $request->user()->tieneRol('Cliente')) {
            return $volverAlCurso->with('error', 'Las inscripciones se hacen desde una cuenta de cliente.');
        }

        $existente = Inscripcion::where('usuario_id', $request->user()->id)->delCurso($curso->id)->first();
        if ($existente && in_array($existente->estado, Inscripcion::ESTADOS_ACTIVOS, true) && ! $existente->esReservaAntigua()) {
            return $volverAlCurso->with('error', 'Ya tienes una inscripción activa para este curso. Revísala en "Mis cursos".');
        }

        $fechas = $curso->fechasDisponibles()->get();
        if ($fechas->isEmpty()) {
            return $volverAlCurso->with('error', 'Este curso aún no tiene fechas publicadas.');
        }

        $libres = $curso->cuposDisponibles();
        if ($libres !== null && $libres <= 0 && ! $existente?->esReservaAntigua()) {
            return $volverAlCurso->with('error', 'No quedan cupos disponibles para este curso por ahora.');
        }

        return view('cursos.inscripcion', [
            'curso'       => $curso,
            'fechas'      => $fechas,
            'metodosPago' => MetodoPago::paraCheckout()->get(),
        ]);
    }

    public function store(Request $request, Curso $curso): RedirectResponse
    {
        $fechasIds = $curso->fechasDisponibles()->pluck('id');

        if ($fechasIds->isEmpty()) {
            return back()->with('error', 'Este curso aún no tiene fechas publicadas. Escríbenos por WhatsApp y te avisamos apenas se abra la próxima.');
        }

        $request->validate([
            'curso_fecha_id' => ['required', 'integer', 'in:' . $fechasIds->implode(',')],
            'metodo_pago_id' => ['required', Rule::exists('metodo_pago', 'id')->where('estado', true)],
        ], [
            'curso_fecha_id.required' => 'Elige una de las fechas disponibles.',
            'curso_fecha_id.in'       => 'Esa fecha ya no está disponible. Elige otra.',
            'metodo_pago_id.required' => 'Elige cómo vas a pagar.',
        ]);

        $fecha   = $curso->fechasDisponibles()->findOrFail($request->curso_fecha_id);
        $usuario = $request->user();

        // Un cliente tiene una sola inscripción por curso (en cualquiera de sus fechas).
        $existente = Inscripcion::where('usuario_id', $usuario->id)
            ->delCurso($curso->id)
            ->first();

        if ($existente && in_array($existente->estado, Inscripcion::ESTADOS_ACTIVOS, true) && ! $existente->esReservaAntigua()) {
            return back()->with('error', 'Ya tienes una inscripción activa para este curso. Revísala en "Mis cursos".');
        }

        // La reserva antigua ya ocupa un cupo, así que no se cuenta dos veces.
        $disponibles = $curso->cuposDisponibles();
        if ($disponibles !== null && $existente?->esReservaAntigua()) {
            $disponibles++;
        }
        if ($disponibles !== null && $disponibles <= 0) {
            return back()->with('error', 'No quedan cupos disponibles para este curso por ahora.');
        }

        try {
            $venta = DB::transaction(function () use ($curso, $fecha, $usuario, $request, $existente) {
                $venta = Venta::create([
                    'usuario_id'       => $usuario->id,
                    'total'            => $curso->precio,
                    'estado'           => 'pendiente',
                    'fecha'            => now(),
                    'nombre_cliente'   => $usuario->nombreCompleto(),
                    'telefono_cliente' => $usuario->telefono,
                    'correo_cliente'   => $usuario->correo,
                    'tipo_documento'   => $usuario->tipoDocumento?->abreviatura,
                    'numero_documento' => $usuario->numero_documento,
                ]);
                $venta->forceFill(['numero_orden' => sprintf('PM-ORD-%06d', $venta->id)])->saveQuietly();

                DetalleVentaCurso::create([
                    'venta_id'        => $venta->id,
                    'curso_id'        => $curso->id,
                    'precio_unitario' => $curso->precio,
                    'subtotal'        => $curso->precio,
                ]);

                Pago::create([
                    'venta_id'           => $venta->id,
                    'metodo_pago_id'     => $request->metodo_pago_id,
                    'numero_comprobante' => $this->estados->nuevoComprobante($venta),
                    'valor'              => $curso->precio,
                    'estado'             => 'pendiente',
                ]);

                // Si ya tenía una inscripción a este curso (cancelada o de antes), se reutiliza.
                $datos = ['estado' => 'pendiente', 'curso_fecha_id' => $fecha->id, 'indicaciones' => null];
                $existente
                    ? $existente->update($datos)
                    : Inscripcion::create($datos + ['usuario_id' => $usuario->id]);

                return $venta;
            });
        } catch (\Throwable $e) {
            Log::error('Inscripción falló: ' . $e->getMessage());
            return back()->with('error', 'No se pudo registrar la inscripción. Inténtalo de nuevo.');
        }

        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Venta',
            'registro_afectado' => $venta->id,
            'nombre_registro'   => $venta->numero_orden,
            'descripcion'       => "Inscripción al curso \"{$curso->nombre}\" ({$venta->numero_orden}), pendiente de pago",
            'valores_nuevos'    => ['total' => $curso->precio, 'fecha' => $fecha->fecha->format('Y-m-d')],
        ]);

        return redirect()->route('checkout.pago', $venta->numero_orden);
    }
}
