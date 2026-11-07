<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\CursoFecha;
use App\Models\MetodoPago;
use App\Models\DetalleVentaCurso;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Usuario;
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

        $mias = $this->inscripcionesDelCliente($request->user(), $curso);
        if ($mias->contains(fn (Inscripcion $i) => $i->estaVigente())) {
            return $volverAlCurso->with('error', 'Ya tienes una inscripción activa para este curso. Revísala en "Mis cursos".');
        }

        $fechas = $curso->fechasDisponibles()->get();
        if ($fechas->isEmpty()) {
            return $volverAlCurso->with('error', 'Este curso aún no tiene fechas publicadas.');
        }

        // Cupos libres de cada fecha (la reserva antigua del cliente ya ocupa uno en su fecha).
        $reserva = $mias->first(fn (Inscripcion $i) => $i->esReservaAntigua());
        $libres  = $fechas->mapWithKeys(fn (CursoFecha $f) => [$f->id => $this->libresPara($f, $reserva)]);

        if ($libres->every(fn ($n) => $n !== null && $n <= 0)) {
            return $volverAlCurso->with('error', 'No quedan cupos disponibles para este curso por ahora.');
        }

        return view('cursos.inscripcion', [
            'curso'       => $curso,
            'fechas'      => $fechas,
            'libres'      => $libres,
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

        $usuario = $request->user();
        $fecha   = null;

        try {
            $venta = DB::transaction(function () use ($curso, $usuario, $request, &$fecha) {
                // Se bloquean el cliente y la fecha: así un doble clic no crea dos
                // inscripciones y dos personas no se quedan con el último cupo.
                Usuario::whereKey($usuario->id)->lockForUpdate()->first();
                $fecha = CursoFecha::whereKey($request->curso_fecha_id)->lockForUpdate()->firstOrFail();

                $mias = $this->inscripcionesDelCliente($usuario, $curso);
                if ($mias->contains(fn (Inscripcion $i) => $i->estaVigente())) {
                    return 'ya_inscrito';
                }

                $reserva = $mias->first(fn (Inscripcion $i) => $i->esReservaAntigua());
                $libres  = $this->libresPara($fecha, $reserva);
                if ($libres !== null && $libres <= 0) {
                    return 'sin_cupos';
                }

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

                // Cada vez que toma el curso queda una inscripción nueva (las anteriores
                // se conservan). Solo una reserva antigua sin pago se reutiliza.
                $datos = ['estado' => 'pendiente', 'curso_fecha_id' => $fecha->id, 'indicaciones' => null];
                $reserva
                    ? $reserva->update($datos)
                    : Inscripcion::create($datos + ['usuario_id' => $usuario->id]);                return $venta;
            });
        } catch (\Throwable $e) {
            Log::error('Inscripción falló: ' . $e->getMessage());
            return back()->with('error', 'No se pudo registrar la inscripción. Inténtalo de nuevo.');
        }

        if ($venta === 'ya_inscrito') {
            return back()->with('error', 'Ya tienes una inscripción activa para este curso. Revísala en "Mis cursos".');
        }
        if ($venta === 'sin_cupos') {
            return back()->withInput()->with('error', 'Esa fecha ya no tiene cupos. Elige otra.');
        }        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Venta',
            'registro_afectado' => $venta->id,
            'nombre_registro'   => $venta->numero_orden,
            'descripcion'       => "Inscripción al curso \"{$curso->nombre}\" ({$venta->numero_orden}), pendiente de pago",
            'valores_nuevos'    => ['total' => $curso->precio, 'fecha' => $fecha->fecha->format('Y-m-d')],
        ]);

        return redirect()->route('checkout.pago', $venta->numero_orden);
    }

    /** Inscripciones del cliente a este curso, de la más vieja a la más nueva. */
    private function inscripcionesDelCliente(Usuario $usuario, Curso $curso)
    {
        return Inscripcion::with('cursoFecha.curso')
            ->where('usuario_id', $usuario->id)
            ->delCurso($curso->id)
            ->orderBy('id')
            ->get();
    }

    /** Cupos libres de la fecha; si la reserva antigua del cliente está en esa fecha, su cupo cuenta como libre. */
    private function libresPara(CursoFecha $fecha, ?Inscripcion $reserva): ?int
    {
        $libres = $fecha->cuposLibres();

        return $libres !== null && $reserva && $reserva->curso_fecha_id === $fecha->id ? $libres + 1 : $libres;
    }
}
