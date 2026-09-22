<?php

namespace App\Http\Controllers;

use App\Models\VentaEnvio;
use App\Services\PagoOrdenService;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Recibe eventos de Wompi (transacción actualizada) y sincroniza la orden.
 * Es la fuente de verdad PRINCIPAL del estado de pago en producción.
 * Doc: https://docs.wompi.co/docs/colombia/eventos/
 *
 * Nota para desarrollo local: Wompi no puede alcanzar http://127.0.0.1,
 * así que este endpoint solo recibirá tráfico real si el proyecto está
 * detrás de una URL pública (túnel o servidor desplegado). Como respaldo
 * para pruebas locales, CheckoutController::resultado() consulta la API
 * de Wompi directamente cuando el usuario regresa del pago.
 */
class WompiWebhookController extends Controller
{
    public function __construct(
        private WompiService $wompi,
        private PagoOrdenService $pagoOrden,
    ) {}

    public function __invoke(Request $request)
    {
        $payload  = $request->all();
        $checksum = strtolower((string) ($payload['signature']['checksum'] ?? ''));

        if (!$this->wompi->verificarEvento($payload, $checksum)) {
            Log::warning('Wompi webhook: firma inválida', ['ref' => $payload['data']['transaction']['reference'] ?? null]);
            return response()->json(['error' => 'invalid signature'], 401);
        }

        $event   = $payload['event'] ?? '';
        $trx     = $payload['data']['transaction'] ?? [];
        $ref     = $trx['reference'] ?? null;
        $trxId   = $trx['id']        ?? null;
        $wStatus = $trx['status']    ?? 'PENDING';
        $method  = $trx['payment_method_type'] ?? null;

        if ($event !== 'transaction.updated' || !$ref) {
            return response()->json(['ok' => true, 'ignored' => true]);
        }

        $envio = VentaEnvio::where('wompi_reference', $ref)->first();
        if (!$envio) {
            Log::info('Wompi webhook: referencia desconocida', ['ref' => $ref]);
            return response()->json(['ok' => true, 'unknown_reference' => true]);
        }

        $this->pagoOrden->aplicar($envio, $wStatus, $trxId, $method, 'webhook');

        return response()->json(['ok' => true]);
    }
}
