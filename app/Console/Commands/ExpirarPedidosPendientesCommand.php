<?php

namespace App\Console\Commands;

use App\Models\VentaEnvio;
use App\Services\OrdenEstadoService;
use Illuminate\Console\Command;

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

        $svc = app(OrdenEstadoService::class);

        foreach ($envios as $envio) {
            $svc->cancelar($envio, null, "Expiración automática: sin confirmación de pago tras {$horas} h", 'expiracion');
        }

        $this->info("Órdenes canceladas por expiración: {$envios->count()}");
        return self::SUCCESS;
    }
}
