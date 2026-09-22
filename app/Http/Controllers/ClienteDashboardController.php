<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaEnvio;
use Illuminate\View\View;

class ClienteDashboardController extends Controller
{
    /**
     * Inicio del cliente: resumen + últimos pedidos.
     */
    public function index(): View
    {
        $userId = auth()->id();

        $ventas = Venta::where('usuario_id', $userId)
            ->with('envio')
            ->orderByDesc('fecha')
            ->limit(5)
            ->get();

        $stats = [
            'total'      => Venta::where('usuario_id', $userId)->count(),
            'pendientes' => VentaEnvio::whereHas('venta', fn($q) => $q->where('usuario_id', $userId))
                                ->where('payment_status', 'PENDING')->count(),
            'aprobadas'  => VentaEnvio::whereHas('venta', fn($q) => $q->where('usuario_id', $userId))
                                ->where('payment_status', 'APPROVED')->count(),
            'entregadas' => VentaEnvio::whereHas('venta', fn($q) => $q->where('usuario_id', $userId))
                                ->where('estado_pedido', 'entregado')->count(),
        ];

        return view('cliente.inicio', compact('stats', 'ventas'));
    }

    public function pedidos(): View
    {
        $ventas = Venta::where('usuario_id', auth()->id())
            ->with('envio')
            ->orderByDesc('fecha')
            ->paginate(10);

        return view('cliente.pedidos', compact('ventas'));
    }

    public function pedido(int $ventaId): View
    {
        $venta = Venta::with(['envio', 'detalleProductos.producto'])
            ->where('id', $ventaId)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        return view('cliente.pedido-detalle', compact('venta'));
    }
}
