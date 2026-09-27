<?php

namespace App\Services;

use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

/**
 * Único punto de cambio de estado de una venta.
 *
 *   pendiente -> pagada      (confirmarPago)
 *   pendiente -> cancelada   (cancelar)
 *   pagada    -> cancelada   (cancelar: devuelve el stock)
 *
 * Cada intento de pago queda en la tabla `pago`.
 * Inventario: solo se descuenta al pasar a pagada y solo se devuelve si la
 * venta estaba pagada.
 */
class OrdenEstadoService
{
    /**
     * @param int|null $metodoPagoId Método con el que se pagó. Si es null se usa el del
     *                               pago pendiente de la venta o, si no hay, "Efectivo"
     *                               (confirmación manual desde el panel).
     */
    public function confirmarPago(Venta $venta, ?Usuario $actor = null, string $origen = 'sistema', ?int $metodoPagoId = null): bool
    {
        $venta = Venta::with('detalleProductos.producto.inventario')->findOrFail($venta->id);

        if ($venta->estado !== 'pendiente') {
            return false;
        }

        DB::transaction(function () use ($venta, $actor, $metodoPagoId) {
            $pago = $venta->pagos()->where('estado', 'pendiente')->latest('id')->first();

            if (! $pago) {
                $pago = new Pago([
                    'venta_id'           => $venta->id,
                    'numero_comprobante' => $this->nuevoComprobante($venta),
                    'valor'              => $venta->total,
                ]);
            }

            $pago->metodo_pago_id = $metodoPagoId
                ?? $pago->metodo_pago_id
                ?? MetodoPago::where('nombre', 'Efectivo')->value('id');
            $pago->estado     = 'aprobado';
            $pago->fecha_pago = now();
            $pago->save();

            // Sin eventos: la auditoría de este cambio se registra abajo con más detalle.
            Venta::withoutEvents(fn () => $venta->update([
                'estado'              => 'pagada',
                'pago_confirmado_por' => $actor?->id,
                'cancelada_at'        => null,
                'cancelada_por'       => null,
                'motivo_cancelacion'  => null,
            ]));

            foreach ($venta->detalleProductos as $d) {
                $d->producto?->inventario?->decrement('stock_actual', $d->cantidad);
            }
        });

        $this->auditar($venta, 'pendiente', 'pagada', $origen, $actor, null);

        return true;
    }

    public function cancelar(Venta $venta, ?Usuario $actor = null, ?string $motivo = null, string $origen = 'sistema'): bool
    {
        $venta = Venta::with('detalleProductos.producto.inventario')->findOrFail($venta->id);

        if ($venta->estado === 'cancelada') {
            return false;
        }

        $anterior     = $venta->estado;
        $estabaPagada = $anterior === 'pagada';

        DB::transaction(function () use ($venta, $actor, $motivo, $estabaPagada) {
            // Un pago que seguía pendiente queda rechazado.
            $venta->pagos()->where('estado', 'pendiente')->update([
                'estado'     => 'rechazado',
                'updated_at' => now(),
            ]);

            Venta::withoutEvents(fn () => $venta->update([
                'estado'             => 'cancelada',
                'cancelada_at'       => now(),
                'cancelada_por'      => $actor?->id,
                'motivo_cancelacion' => $motivo ? mb_substr($motivo, 0, 255) : null,
            ]));

            if ($estabaPagada) {
                foreach ($venta->detalleProductos as $d) {
                    $d->producto?->inventario?->increment('stock_actual', $d->cantidad);
                }
            }
        });

        $this->auditar($venta, $anterior, 'cancelada', $origen, $actor, $motivo);

        return true;
    }

    public function nuevoComprobante(Venta $venta): string
    {
        return 'PAGO-' . str_pad((string) $venta->id, 6, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    private function auditar(Venta $venta, string $anterior, string $nuevo, string $origen, ?Usuario $actor, ?string $motivo): void
    {
        $etq = fn (string $e) => Venta::ESTADOS_ORDEN[$e] ?? ucfirst($e);

        $texto = "Venta {$venta->numero_orden} cambió de {$etq($anterior)} a {$etq($nuevo)}.";
        if ($motivo) {
            $texto .= " Motivo: {$motivo}";
        }

        $datos = [
            'accion'             => 'actualizado',
            'modulo'             => 'Venta',
            'registro_id'        => $venta->id,
            'registro_etiqueta'  => $venta->numero_orden,
            'descripcion'        => $texto . " (origen: {$origen})",
            'valores_anteriores' => ['estado' => $anterior],
            'valores_nuevos'     => array_filter(['estado' => $nuevo, 'motivo' => $motivo], fn ($v) => $v !== null),
        ];

        // Sin sesión (tarea programada) el actor queda vacío y el origen lo explica.
        if ($actor) {
            $datos += [
                'usuario_id'     => $actor->id,
                'usuario_nombre' => trim($actor->primer_nombre . ' ' . $actor->primer_apellido),
                'usuario_correo' => $actor->correo,
            ];
        }

        AuditoriaService::registrar($datos);
    }
}
