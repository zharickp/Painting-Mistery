<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('buscar', ''));
        $pago   = $request->query('pago');

        $ventas = Venta::with(['usuario', 'envio'])
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->whereHas('envio', fn ($e) => $e->where('numero_orden', 'ilike', "%{$buscar}%")
                            ->orWhere('nombre_envio', 'ilike', "%{$buscar}%"))
                        ->orWhereHas('usuario', fn ($u) => $u->where('primer_nombre', 'ilike', "%{$buscar}%")
                            ->orWhere('primer_apellido', 'ilike', "%{$buscar}%")
                            ->orWhere('correo', 'ilike', "%{$buscar}%"));
                });
            })
            ->when(in_array($pago, ['APPROVED', 'PENDING', 'DECLINED'], true), fn ($q) => $q->whereHas('envio', fn ($e) => $e->where('payment_status', $pago)))
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('admin.ventas', compact('ventas', 'buscar', 'pago'));
    }

    public function orden(int $ventaId): View
    {
        $venta = Venta::with(['envio', 'usuario', 'detalleProductos.producto'])->findOrFail($ventaId);

        abort_unless($venta->envio && $venta->envio->payment_status === 'APPROVED', 404);

        $volver = route('admin.ventas');

        return view('cliente.orden-venta', compact('venta', 'volver'));
    }
}
