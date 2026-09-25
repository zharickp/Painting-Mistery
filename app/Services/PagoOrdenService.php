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

        try {
            $svc = app(OrdenEstadoService::class);

            if ($status === 'APPROVED') {
                $svc->confirmarPago($envio, null, $origen, $method, $trxId);
            } elseif (in_array($status, ['DECLINED', 'VOIDED'], true)) {
                $svc->cancelar($envio, null, "Pago {$status} en la pasarela", $origen, $status);
            } else {
                // PENDING / ERROR: solo se registra el estado del pago, la orden sigue pendiente.
                $envio->update([
                    'payment_status'       => $status,
                    'wompi_transaction_id' => $trxId ?: $envio->wompi_transaction_id,
                    'wompi_payment_method' => $method ?: $envio->wompi_payment_method,
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("PagoOrdenService::aplicar falló ({$origen}): " . $e->getMessage());
            return false;
        }
    }
}
