<?php

namespace App\Services;

use App\Models\VentaEnvio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Aplica el resultado de una transacción Wompi a una orden (VentaEnvio + Venta).
 * Lógica compartida entre:
 *  - WompiWebhookController (fuente de verdad, confirmado por Wompi vía POST)
 *  - CheckoutController::resultado() (respaldo: consulta directa a la API de
 *    Wompi cuando el usuario regresa, útil en desarrollo local donde Wompi
 *    no puede alcanzar el webhook porque no hay una URL pública)
 *
 * Idempotente: aplicar el mismo estado dos veces no descuenta inventario
 * dos veces, porque solo actúa si el estado realmente cambió.
 */
class PagoOrdenService
{
    public function aplicar(VentaEnvio $envio, string $status, ?string $trxId, ?string $method, string $origen = 'desconocido'): bool
    {
        // Idempotencia: si ya estaba en este estado, no repetir efectos secundarios
        // (como descontar inventario dos veces).
        if ($envio->payment_status === $status && $envio->wompi_transaction_id === $trxId) {
            return false;
        }

        $estadoAnterior = $envio->payment_status;

        try {
            DB::transaction(function () use ($envio, $status, $trxId, $method, $estadoAnterior) {
                $envio->update([
                    'payment_status'       => $status,
                    'wompi_transaction_id' => $trxId ?: $envio->wompi_transaction_id,
                    'wompi_payment_method' => $method ?: $envio->wompi_payment_method,
                    'fecha_pago'           => $status === 'APPROVED' ? now() : $envio->fecha_pago,
                    'estado_pedido'        => match ($status) {
                        'APPROVED' => 'confirmado',
                        'DECLINED', 'VOIDED' => 'cancelado',
                        default    => $envio->estado_pedido,
                    },
                ]);

                $envio->venta->update([
                    'estado' => match ($status) {
                        'APPROVED' => 'pagada',
                        'DECLINED', 'VOIDED' => 'cancelada',
                        default    => $envio->venta->estado,
                    },
                ]);

                // Descontar inventario SOLO en la transición hacia APPROVED
                // (nunca si ya estaba aprobado antes: evita doble descuento).
                if ($status === 'APPROVED' && $estadoAnterior !== 'APPROVED') {
                    foreach ($envio->venta->detalleProductos as $d) {
                        $inv = $d->producto?->inventario;
                        if ($inv) {
                            $inv->decrement('stock_actual', $d->cantidad);
                        }
                    }
                }
            });

            AuditoriaService::registrar([
                'accion'            => 'actualizado',
                'modulo'            => 'Venta',
                'registro_id'       => $envio->venta_id,
                'registro_etiqueta' => $envio->numero_orden,
                'descripcion'       => "Pago {$status} para {$envio->numero_orden} (origen: {$origen})",
                'valores_anteriores'=> ['payment_status' => $estadoAnterior],
                'valores_nuevos'    => ['payment_status' => $status, 'transaction_id' => $trxId],
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error("PagoOrdenService::aplicar falló ({$origen}): " . $e->getMessage());
            return false;
        }
    }
}
