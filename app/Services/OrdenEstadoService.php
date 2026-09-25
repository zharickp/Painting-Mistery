<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Venta;
use App\Models\VentaEnvio;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Único punto de cambio de estado de una orden.
 *
 *   pendiente -> pagada      (confirmarPago)
 *   pendiente -> cancelada   (cancelar)
 *   pagada    -> cancelada   (cancelar: devuelve el stock; solo si aún no se envió)
 *
 * Nunca borra ni modifica la fecha de creación. Inventario: solo se descuenta al
 * pasar a pagada y solo se devuelve si la orden estaba pagada.
 */
class OrdenEstadoService
{
    public function confirmarPago(VentaEnvio $envio, ?Usuario $actor = null, string $origen = 'sistema', ?string $metodo = null, ?string $trxId = null): bool
    {
        $venta = $envio->venta()->with('detalleProductos.producto.inventario')->first();

        if ($venta->estado === 'pagada') {
            return false;
        }

        $anterior = $venta->estado;

        DB::transaction(function () use ($envio, $venta, $actor, $metodo, $trxId) {
            $envio->update([
                'payment_status'       => 'APPROVED',
                'estado_pedido'        => 'confirmado',
                'fecha_pago'           => now(),
                'pago_confirmado_por'  => $actor?->id,
                'wompi_payment_method' => $metodo ?: $envio->wompi_payment_method,
                'wompi_transaction_id' => $trxId ?: $envio->wompi_transaction_id,
                'cancelada_at'         => null,
                'cancelada_por'        => null,
                'motivo_cancelacion'   => null,
            ]);

            Venta::withoutEvents(fn () => $venta->update(['estado' => 'pagada']));

            foreach ($venta->detalleProductos as $d) {
                $inv = $d->producto?->inventario;
                if ($inv) {
                    $inv->decrement('stock_actual', $d->cantidad);
                }
            }
        });

        $this->auditar($envio, $anterior, 'pagada', $origen, $actor, null);

        return true;
    }

    /**
     * @param string|null $paymentStatus Estado de pago a registrar (p. ej. DECLINED). Por defecto se
     *                                   conserva; una orden pagada que se cancela pasa a VOIDED.
     */
    public function cancelar(VentaEnvio $envio, ?Usuario $actor = null, ?string $motivo = null, string $origen = 'sistema', ?string $paymentStatus = null): bool
    {
        $venta = $envio->venta()->with('detalleProductos.producto.inventario')->first();

        if ($venta->estado === 'cancelada') {
            return false;
        }

        if (in_array($envio->estado_pedido, ['enviado', 'entregado'], true)) {
            throw new DomainException('La orden ya fue enviada o entregada y no se puede cancelar.');
        }

        $estabaPagada = $venta->estado === 'pagada' || $envio->payment_status === 'APPROVED';
        $anterior     = $venta->estado;

        DB::transaction(function () use ($envio, $venta, $actor, $motivo, $paymentStatus, $estabaPagada) {
            $envio->update([
                'payment_status'     => $estabaPagada ? 'VOIDED' : ($paymentStatus ?: $envio->payment_status),
                'estado_pedido'      => 'cancelado',
                'cancelada_at'       => now(),
                'cancelada_por'      => $actor?->id,
                'motivo_cancelacion' => $motivo ? mb_substr($motivo, 0, 255) : null,
            ]);

            Venta::withoutEvents(fn () => $venta->update(['estado' => 'cancelada']));

            if ($estabaPagada) {
                foreach ($venta->detalleProductos as $d) {
                    $inv = $d->producto?->inventario;
                    if ($inv) {
                        $inv->increment('stock_actual', $d->cantidad);
                    }
                }
            }
        });

        $this->auditar($envio, $anterior, 'cancelada', $origen, $actor, $motivo);

        return true;
    }

    private function auditar(VentaEnvio $envio, string $anterior, string $nuevo, string $origen, ?Usuario $actor, ?string $motivo): void
    {
        $etq = fn (string $e) => Venta::ESTADOS_ORDEN[$e] ?? ucfirst($e);

        $texto = "Orden {$envio->numero_orden} cambió de {$etq($anterior)} a {$etq($nuevo)}.";
        if ($motivo) {
            $texto .= " Motivo: {$motivo}";
        }

        $datos = [
            'accion'             => 'actualizado',
            'modulo'             => 'Venta',
            'registro_id'        => $envio->venta_id,
            'registro_etiqueta'  => $envio->numero_orden,
            'descripcion'        => $texto . " (origen: {$origen})",
            'valores_anteriores' => ['estado' => $anterior],
            'valores_nuevos'     => array_filter(['estado' => $nuevo, 'motivo' => $motivo], fn ($v) => $v !== null),
        ];

        // Sin sesión (webhook, tarea programada) el actor queda vacío y el origen lo explica.
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
