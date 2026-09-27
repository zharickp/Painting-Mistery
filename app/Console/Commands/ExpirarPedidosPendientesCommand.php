<?php

namespace App\Console\Commands;

use App\Models\Venta;
use App\Services\OrdenEstadoService;
use Illuminate\Console\Command;

/**
 * Cancela las ventas que se crearon pero nunca se pagaron (el cliente llegó a
 * la pantalla de pago y no terminó). No toca el inventario: el stock
 * solo se descuenta cuando el pago se aprueba.
 */
class ExpirarPedidosPendientesCommand extends Command
{
    protected $signature = 'pedidos:expirar-pendientes {--horas=24 : Horas de espera antes de cancelar}';

    protected $description = 'Cancela las ventas sin pagar que superaron el tiempo límite';

    public function handle(): int
    {
        $horas  = (int) $this->option('horas');
        $limite = now()->subHours($horas);

        // Solo ventas de la tienda (con número de orden) que siguen pendientes.
        $ventas = Venta::whereNotNull('numero_orden')
            ->where('estado', 'pendiente')
            ->where('fecha', '<', $limite)
            ->get();

        $svc = app(OrdenEstadoService::class);

        foreach ($ventas as $venta) {
            $svc->cancelar($venta, null, "Expiración automática: sin confirmación de pago tras {$horas} h", 'expiracion');
        }

        $this->info("Órdenes canceladas por expiración: {$ventas->count()}");
        return self::SUCCESS;
    }
}
