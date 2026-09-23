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

        $enviosUsuario = fn() => VentaEnvio::whereHas('venta', fn($q) => $q->where('usuario_id', $userId));

        $stats = [
            'total'           => Venta::where('usuario_id', $userId)->count(),
            // "Pendientes" reales: en espera de pago Y no expiradas todavía.
            'pendientes'      => $enviosUsuario()->where('payment_status', 'PENDING')
                                     ->where('estado_pedido', '!=', 'cancelado')->count(),
            'aprobadas'       => $enviosUsuario()->where('payment_status', 'APPROVED')->count(),
            'entregadas'      => $enviosUsuario()->where('estado_pedido', 'entregado')->count(),
            // Total invertido: SOLO lo realmente pagado (antes se sumaba todo,
            // incluyendo pedidos pendientes o cancelados, lo cual era engañoso).
            'total_pagado'    => (float) $enviosUsuario()->where('payment_status', 'APPROVED')->sum('total'),
            'total_pendiente' => (float) $enviosUsuario()->where('payment_status', 'PENDING')
                                     ->where('estado_pedido', '!=', 'cancelado')->sum('total'),
        ];

        $tieneCursos = auth()->user()->inscripciones()->exists();

        return view('cliente.inicio', compact('stats', 'ventas', 'tieneCursos'));
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

    /**
     * Factura/comprobante imprimible de una orden. Solo disponible una
     * vez el pago quedó aprobado (una orden pendiente o rechazada no
     * genera un comprobante de compra válido).
     */
    public function factura(int $ventaId): View
    {
        $venta = Venta::with(['envio', 'usuario', 'detalleProductos.producto'])
            ->where('id', $ventaId)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        abort_unless($venta->envio && $venta->envio->payment_status === 'APPROVED', 404);

        return view('cliente.factura', compact('venta'));
    }
}
