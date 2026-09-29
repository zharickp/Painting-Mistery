<?php

namespace App\Http\Controllers;

use App\Models\Curso;
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

/**
 * Inscripción a un curso. Se paga completa, como una compra de la tienda:
 * se crea una venta (con su detalle de curso y su pago pendiente) y la
 * inscripción queda pendiente con su cupo reservado. Al pagar, la inscripción
 * se confirma; si no se paga en 24 horas, se cancela y el cupo se libera.
 */
class InscripcionController extends Controller
{
    public function __construct(private OrdenEstadoService $estados) {}

    public function store(Request $request, Curso $curso): RedirectResponse
    {
        $fechasIds = $curso->fechasDisponibles()->pluck('id');

        if ($fechasIds->isEmpty()) {
            return back()->with('error', 'Este curso aún no tiene fechas publicadas. Escríbenos por WhatsApp y te avisamos apenas se abra la próxima.');
        }

        $request->validate([
            'curso_fecha_id' => ['required', 'integer', 'in:' . $fechasIds->implode(',')],
            'metodo_pago_id' => ['required', Rule::exists('metodo_pago', 'id')->where('estado', true)->where('en_linea', true)],
        ], [
            'curso_fecha_id.required' => 'Elige una de las fechas disponibles.',
            'curso_fecha_id.in'       => 'Esa fecha ya no está disponible. Elige otra.',
            'metodo_pago_id.required' => 'Elige cómo vas a pagar.',
        ]);

        $fecha   = $curso->fechasDisponibles()->findOrFail($request->curso_fecha_id);
        $usuario = $request->user();

        $existente = Inscripcion::where('usuario_id', $usuario->id)
            ->where('curso_id', $curso->id)
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
            $venta = DB::transaction(function () use ($curso, $fecha, $usuario, $request) {
                $venta = Venta::create([
                    'usuario_id'       => $usuario->id,
                    'total'            => $curso->costo,
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
                    'precio_unitario' => $curso->costo,
                    'subtotal'        => $curso->costo,
                ]);

                Pago::create([
                    'venta_id'           => $venta->id,
                    'metodo_pago_id'     => $request->metodo_pago_id,
                    'numero_comprobante' => $this->estados->nuevoComprobante($venta),
                    'valor'              => $curso->costo,
                    'estado'             => 'pendiente',
                ]);

                Inscripcion::updateOrCreate(
                    ['usuario_id' => $usuario->id, 'curso_id' => $curso->id],
                    [
                        'estado'           => 'pendiente',
                        'fecha_preferida'  => $fecha->fecha,
                        'fecha_confirmada' => null,
                        'notas'            => null,
                        'venta_id'         => $venta->id,
                    ]
                );

                return $venta;
            });
        } catch (\Throwable $e) {
            Log::error('Inscripción falló: ' . $e->getMessage());
            return back()->with('error', 'No se pudo registrar la inscripción. Inténtalo de nuevo.');
        }

        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Venta',
            'registro_id'       => $venta->id,
            'registro_etiqueta' => $venta->numero_orden,
            'descripcion'       => "Inscripción al curso \"{$curso->nombre}\" ({$venta->numero_orden}), pendiente de pago",
            'valores_nuevos'    => ['total' => $curso->costo, 'fecha' => $fecha->fecha->format('Y-m-d')],
        ]);

        return redirect()->route('checkout.pago', $venta->numero_orden);
    }
}
