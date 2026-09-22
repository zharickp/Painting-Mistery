<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaEnvio;
use App\Services\AuditoriaService;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Recibe eventos de Wompi (transacción actualizada) y sincroniza la orden.
 * Es la ÚNICA fuente de verdad del estado de pago — el redirect del usuario
 * no marca nada como pagado.
 * Doc: https://docs.wompi.co/docs/colombia/eventos/
 */
class WompiWebhookController extends Controller
{
    public function __construct(private WompiService $wompi) {}

    public function __invoke(Request $request)
    {
        $payload  = $request->all();
        $checksum = strtolower((string) ($payload['signature']['checksum'] ?? ''));

        // 1) Verificar firma
        if (!$this->wompi->verificarEvento($payload, $checksum)) {
            Log::warning('Wompi webhook: firma inválida', ['ref' => $payload['data']['transaction']['reference'] ?? null]);
            return response()->json(['error' => 'invalid signature'], 401);
        }

        $event = $payload['event'] ?? '';
        $trx   = $payload['data']['transaction'] ?? [];
        $ref   = $trx['reference']  ?? null;
        $trxId = $trx['id']         ?? null;
        $wStatus = $trx['status']   ?? 'PENDING'; // APPROVED|DECLINED|VOIDED|ERROR|PENDING
        $method  = $trx['payment_method_type'] ?? null;

        if ($event !== 'transaction.updated' || !$ref) {
            return response()->json(['ok' => true, 'ignored' => true]);
        }

        $envio = VentaEnvio::where('wompi_reference', $ref)->first();
        if (!$envio) {
            Log::info('Wompi webhook: referencia desconocida', ['ref' => $ref]);
            return response()->json(['ok' => true, 'unknown_reference' => true]);
        }

        try {
            DB::transaction(function () use ($envio, $wStatus, $trxId, $method) {
                $envio->update([
                    'payment_status'       => $wStatus,
                    'wompi_transaction_id' => $trxId,
                    'wompi_payment_method' => $method,
                    'fecha_pago'           => $wStatus === 'APPROVED' ? now() : $envio->fecha_pago,
                    'estado_pedido'        => match ($wStatus) {
                        'APPROVED' => 'confirmado',
                        'DECLINED', 'VOIDED' => 'cancelado',
                        'ERROR'    => $envio->estado_pedido,
                        default    => $envio->estado_pedido,
                    },
                ]);

                $envio->venta->update([
                    'estado' => match ($wStatus) {
                        'APPROVED' => 'pagada',
                        'DECLINED', 'VOIDED' => 'cancelada',
                        default    => $envio->venta->estado,
                    },
                ]);

                // Descontar inventario solo cuando el pago está APROBADO
                if ($wStatus === 'APPROVED') {
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
                'descripcion'       => "Wompi webhook: transacción {$wStatus} para {$envio->numero_orden}",
                'valores_anteriores'=> ['payment_status' => 'previo'],
                'valores_nuevos'    => ['payment_status' => $wStatus, 'transaction_id' => $trxId],
            ]);

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Wompi webhook fallo interno: ' . $e->getMessage());
            return response()->json(['error' => 'internal'], 500);
        }
    }
}
