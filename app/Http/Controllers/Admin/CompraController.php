<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\StockInsuficienteException;
use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Compras a proveedores: cada compra registrada suma sus unidades al
 * inventario. No se editan ni se borran; si se registró mal, se anula y las
 * unidades se restan otra vez.
 */
class CompraController extends Controller
{
    public function index(Request $request): View
    {
        $proveedorId = $request->query('proveedor');

        $compras = Compra::with('proveedor')
            ->withCount('detalles')
            ->when($proveedorId, fn ($q) => $q->where('proveedor_id', $proveedorId))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.compras.index', compact('compras', 'proveedores', 'proveedorId'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $proveedores = Proveedor::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        if ($proveedores->isEmpty()) {
            return redirect()->route('admin.proveedores.create')
                ->with('error', 'Primero registra un proveedor para poder cargar una compra.');
        }

        $productos = Producto::with('inventario')->orderBy('nombre')->get()
            ->map(fn (Producto $p) => [
                'id'     => $p->id,
                'nombre' => $p->nombre . ($p->estado ? '' : ' (inactivo)'),
                'stock'  => $p->stockActual(),
            ]);

        return view('admin.compras.create', [
            'proveedores' => $proveedores,
            'productos'   => $productos,
            'proveedorId' => $request->query('proveedor'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'proveedor_id'          => ['required', Rule::exists('proveedor', 'id')->where('estado', true)],
            'numero_factura'        => ['nullable', 'string', 'max:60'],
            'fecha'                 => ['required', 'date', 'before_or_equal:today'],
            'observaciones'         => ['nullable', 'string', 'max:1000'],
            'items'                 => ['required', 'array', 'min:1', 'max:100'],
            'items.*.producto_id'   => ['required', 'integer', 'exists:producto,id', 'distinct'],
            'items.*.cantidad'      => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.costo_unitario'=> ['required', 'numeric', 'min:0', 'max:999999999'],
        ], [
            'proveedor_id.exists'        => 'Elige un proveedor activo.',
            'fecha.before_or_equal'      => 'La fecha de la compra no puede ser futura.',
            'items.required'             => 'Agrega al menos un producto a la compra.',
            'items.*.producto_id.distinct' => 'Un producto aparece repetido: deja una sola línea por producto.',
            'items.*.producto_id.required' => 'Elige el producto en cada línea.',
            'items.*.cantidad.min'       => 'La cantidad debe ser al menos 1.',
            'items.*.costo_unitario.min' => 'El costo no puede ser negativo.',
        ]);

        $compra = DB::transaction(function () use ($data) {
            $compra = Compra::create([
                'proveedor_id'   => $data['proveedor_id'],
                'usuario_id'     => auth()->id(),
                'numero_factura' => $data['numero_factura'] ?? null,
                'fecha'          => $data['fecha'],
                'observaciones'  => $data['observaciones'] ?? null,
                'estado'         => 'registrada',
                'total'          => 0,
            ]);

            $total = 0;
            $cantidades = [];
            foreach ($data['items'] as $item) {
                $subtotal = round($item['cantidad'] * $item['costo_unitario'], 2);
                $total += $subtotal;
                DetalleCompra::create([
                    'compra_id'      => $compra->id,
                    'producto_id'    => $item['producto_id'],
                    'cantidad'       => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'subtotal'       => $subtotal,
                ]);
                $cantidades[$item['producto_id']] = (int) $item['cantidad'];
            }

            $compra->forceFill(['total' => $total])->saveQuietly();
            InventarioService::sumar($cantidades);

            return $compra;
        });

        return redirect()->route('admin.compras.show', $compra)
            ->with('success', 'Compra registrada. Se actualizó el inventario de cada producto.');
    }

    public function show(Compra $compra): View
    {
        $compra->load(['proveedor', 'usuario', 'anuladaPor', 'detalles.producto.inventario']);

        return view('admin.compras.show', compact('compra'));
    }

    /** Anula una compra mal registrada: sus unidades salen otra vez del inventario. */
    public function anular(Request $request, Compra $compra): RedirectResponse
    {
        $data = $request->validate(['motivo' => ['required', 'string', 'min:3', 'max:255']]);

        if ($compra->estaAnulada()) {
            return back()->with('error', 'Esta compra ya estaba anulada.');
        }

        try {
            DB::transaction(function () use ($compra, $data) {
                $compra->load('detalles');
                $cantidades = [];
                foreach ($compra->detalles as $d) {
                    $cantidades[$d->producto_id] = ($cantidades[$d->producto_id] ?? 0) + $d->cantidad;
                }
                // Si parte de esas unidades ya se vendió, no se puede anular.
                InventarioService::descontar($cantidades, 'la compra trae');

                $compra->update([
                    'estado'           => 'anulada',
                    'fecha_anulacion'  => now(),
                    'anulada_por'      => auth()->id(),
                    'motivo_anulacion' => $data['motivo'],
                ]);
            });
        } catch (StockInsuficienteException $e) {
            return back()->with('error', 'No se puede anular: parte de esa mercancía ya se vendió. ' . $e->getMessage());
        }

        return back()->with('success', "Compra #{$compra->id} anulada. Sus unidades se restaron del inventario.");
    }
}
