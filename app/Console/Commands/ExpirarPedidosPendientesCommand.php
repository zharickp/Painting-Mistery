<?php

namespace App\Console\Commands;

use App\Models\VentaEnvio;
use App\Services\AuditoriaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cancela automáticamente las órdenes cuyo pago nunca se confirmó
 * (siguen en PENDING para Wompi) después de un tiempo límite de espera.
 * No descuenta ni restaura inventario: como el stock solo se descuenta
 * al aprobar el pago (ver PagoOrdenService), estas órdenes nunca lo
 * tocaron — no hay nada que revertir.
 */
class ExpirarPedidosPendientesCommand extends Command
{
    protected $signature = 'pedidos:expirar-pendientes {--horas=24 : Horas de espera antes de cancelar}';

    protected $description = 'Cancela automáticamente órdenes con pago pendiente que superaron el tiempo límite';

    public function handle(): int
    {
        $horas  = (int) $this->option('horas');
        $limite = now()->subHours($horas);

        $envios = VentaEnvio::where('payment_status', 'PENDING')
            ->where('estado_pedido', '!=', 'cancelado')
            ->whereHas('venta', fn($q) => $q->where('fecha', '<', $limite))
            ->with('venta')
            ->get();

        foreach ($envios as $envio) {
            DB::transaction(function () use ($envio) {
                $envio->update(['estado_pedido' => 'cancelado']);
                $envio->venta->update(['estado' => 'cancelada']);
            });

            AuditoriaService::registrar([
                'accion'            => 'actualizado',
                'modulo'            => 'Venta',
                'registro_id'       => $envio->venta_id,
                'registro_etiqueta' => $envio->numero_orden,
                'descripcion'       => "Orden {$envio->numero_orden} cancelada automáticamente: sin confirmación de pago tras {$horas}h",
                'valores_anteriores'=> ['estado_pedido' => 'pendiente'],
                'valores_nuevos'    => ['estado_pedido' => 'cancelado', 'motivo' => 'expiracion_automatica'],
            ]);
        }

        $this->info("Órdenes canceladas por expiración: {$envios->count()}");
        return self::SUCCESS;
    }
}
