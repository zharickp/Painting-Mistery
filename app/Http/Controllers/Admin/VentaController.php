<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Services\OrdenEstadoService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $estado = $request->query('estado');
        $fecha  = $request->query('fecha');

        $ventas = Venta::with(['usuario', 'envio'])
            ->when($buscar !== '', function ($q) use ($buscar) {
                // "#0015", "15" o "PM-ORD-000015" encuentran la orden; también cliente y correo.
                $numero = ltrim(Str::of($buscar)->replace('#', '')->trim()->toString(), '0');
                $q->where(function ($sub) use ($buscar, $numero) {
                    $sub->whereHas('envio', fn ($e) => $e->where('numero_orden', 'ilike', "%{$buscar}%")
                            ->orWhere('nombre_envio', 'ilike', "%{$buscar}%"))
                        ->orWhereHas('usuario', fn ($u) => $u->where('primer_nombre', 'ilike', "%{$buscar}%")
                            ->orWhere('primer_apellido', 'ilike', "%{$buscar}%")
                            ->orWhere('correo', 'ilike', "%{$buscar}%"));
                    if ($numero !== '' && ctype_digit($numero)) {
                        $sub->orWhere('venta.id', (int) $numero);
                    }
                });
            })
            ->when(array_key_exists((string) $estado, Venta::ESTADOS_ORDEN), fn ($q) => $q->where('estado', $estado))
            ->when($fecha, fn ($q) => $q->whereDate('fecha', $fecha))
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        $conteos = Venta::selectRaw('estado, count(*) as n')->groupBy('estado')->pluck('n', 'estado');

        return view('admin.ventas', compact('ventas', 'buscar', 'estado', 'fecha', 'conteos'));
    }

    public function show(int $ventaId): View
    {
        $venta = Venta::with(['envio.canceladaPor', 'envio.pagoConfirmadoPor', 'usuario', 'detalleProductos.producto'])->findOrFail($ventaId);

        return view('admin.venta-detalle', compact('venta'));
    }

    public function confirmarPago(int $ventaId, OrdenEstadoService $svc): RedirectResponse
    {
        $venta = Venta::with('envio')->findOrFail($ventaId);

        if ($venta->estado !== 'pendiente') {
            return back()->with('error', 'Solo se puede confirmar el pago de una orden pendiente.');
        }

        $svc->confirmarPago($venta->envio, auth()->user(), 'panel administrativo', 'Confirmación manual');

        return back()->with('success', "Pago confirmado. La orden {$venta->envio->numero_orden} quedó como Pagada.");
    }

    public function cancelar(Request $request, int $ventaId, OrdenEstadoService $svc): RedirectResponse
    {
        $data = $request->validate(['motivo' => ['required', 'string', 'min:3', 'max:255']]);

        $venta = Venta::with('envio')->findOrFail($ventaId);

        if ($venta->estado === 'cancelada') {
            return back()->with('error', 'La orden ya estaba cancelada.');
        }

        try {
            $svc->cancelar($venta->envio, auth()->user(), $data['motivo'], 'panel administrativo');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Orden {$venta->envio->numero_orden} cancelada. Se conserva en el historial.");
    }

    public function orden(int $ventaId): View
    {
        $venta = Venta::with(['envio', 'usuario', 'detalleProductos.producto'])->findOrFail($ventaId);

        abort_unless($venta->envio, 404);

        $volver = route('admin.ventas.show', $venta->id);

        return view('cliente.orden-venta', compact('venta', 'volver'));
    }
}
