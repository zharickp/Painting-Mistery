<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->orderByDesc('fecha')
            ->limit(5)
            ->get();

        $stats = [
            'total'           => Venta::where('usuario_id', $userId)->count(),
            // "Pendientes" reales: en espera de pago Y no expiradas todavía.
            'pendientes'      => Venta::where('usuario_id', $userId)->where('estado', 'pendiente')->count(),
            'aprobadas'       => Venta::where('usuario_id', $userId)->where('estado', 'pagada')->count(),
            'canceladas'      => Venta::where('usuario_id', $userId)->where('estado', 'cancelada')->count(),
            // Total invertido: SOLO lo realmente pagado (antes se sumaba todo,
            // incluyendo pedidos pendientes o cancelados, lo cual era engañoso).
            'total_pagado'    => (float) Venta::where('usuario_id', $userId)->where('estado', 'pagada')->sum('total'),
            'total_pendiente' => (float) Venta::where('usuario_id', $userId)->where('estado', 'pendiente')->sum('total'),
        ];

        $inscripciones = Inscripcion::with('curso')
            ->where('usuario_id', $userId)->activas()->latest()->get();
        $proximoCurso = $inscripciones->where('estado', 'confirmada')
            ->filter(fn ($i) => $i->fecha_confirmada && $i->fecha_confirmada->gte(today()))
            ->sortBy(fn ($i) => $i->fecha_confirmada)->first();
        $inscritosIds = $inscripciones->pluck('curso_id');
        $cursosDisponibles = Curso::where('estado', true)->whereNotIn('id', $inscritosIds)->orderBy('costo')->limit(3)->get();
        $ultimaOrden = $ventas->first(fn ($v) => $v->estado === 'pagada');
        $recomendados = Producto::where('estado', true)->with('categoria')->latest()->limit(4)->get();

        return view('cliente.inicio', compact(
            'stats', 'ventas', 'inscripciones', 'proximoCurso', 'cursosDisponibles', 'ultimaOrden', 'recomendados'
        ));
    }

    public function pedidos(): View
    {
        $ventas = Venta::where('usuario_id', auth()->id())
            ->orderByDesc('fecha')
            ->paginate(10);

        return view('cliente.pedidos', compact('ventas'));
    }

    public function pedido(int $ventaId): View
    {
        $venta = Venta::with(['detalleProductos.producto', 'pago.metodoPago'])
            ->where('id', $ventaId)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        return view('cliente.pedido-detalle', compact('venta'));
    }

    /**
     * Orden de venta imprimible. Refleja el estado actual (pendiente, pagada o
     * cancelada). Documento interno del sistema, no es una factura.
     */
    public function ordenVenta(int $ventaId): View
    {
        $venta = Venta::with(['usuario', 'detalleProductos.producto', 'pago.metodoPago'])
            ->where('id', $ventaId)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        abort_unless($venta->numero_orden, 404);

        $volver = route('mi-cuenta.pedido', $venta->id);

        return view('cliente.orden-venta', compact('venta', 'volver'));
    }

    public function cursos(): View
    {
        $userId = auth()->id();

        $inscripciones = Inscripcion::with('curso')
            ->where('usuario_id', $userId)->latest()->get();

        $inscritosActivos = $inscripciones->whereIn('estado', ['pendiente', 'confirmada', 'completada'])->pluck('curso_id');
        $disponibles = Curso::where('estado', true)->whereNotIn('id', $inscritosActivos)->orderBy('costo')->get();

        return view('cliente.cursos', compact('inscripciones', 'disponibles'));
    }

    public function perfil(): View
    {
        return view('cliente.perfil', ['usuario' => auth()->user()]);
    }

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'primer_nombre'    => ['required', 'string', 'max:60'],
            'segundo_nombre'   => ['nullable', 'string', 'max:60'],
            'primer_apellido'  => ['required', 'string', 'max:60'],
            'segundo_apellido' => ['nullable', 'string', 'max:60'],
            'telefono'         => ['nullable', 'string', 'max:20'],
        ]);

        auth()->user()->update($data);

        return back()->with('success', 'Tu perfil se actualizó correctamente.');
    }

    public function cancelarPedido(int $ventaId, \App\Services\OrdenEstadoService $svc): RedirectResponse
    {
        $venta = Venta::where('id', $ventaId)->where('usuario_id', auth()->id())->firstOrFail();

        abort_unless($venta->numero_orden, 404);

        if ($venta->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes cancelar compras que aún están pendientes de pago.');
        }

        $svc->cancelar($venta, auth()->user(), 'Cancelado por el cliente', 'cliente');

        return back()->with('success', 'Tu compra fue cancelada. Sigue apareciendo en tu historial.');
    }

    public function comprobanteCurso(int $inscripcionId): View
    {
        $inscripcion = Inscripcion::with(['curso', 'usuario'])
            ->where('id', $inscripcionId)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        abort_if($inscripcion->estado === 'cancelada', 404);

        return view('cliente.comprobante-curso', ['ins' => $inscripcion]);
    }
}
