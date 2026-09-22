<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarritoController extends Controller
{
    private function carritoActivo(): Carrito
    {
        return Carrito::firstOrCreate(
            ['usuario_id' => auth()->id(), 'estado' => 'activo'],
            ['usuario_id' => auth()->id(), 'estado' => 'activo']
        );
    }

    public function index(): View
    {
        $carrito  = $this->carritoActivo();
        $detalles = $carrito->detalles()->with('producto')->get();
        $total    = $detalles->sum(fn($d) => $d->cantidad * $d->precio_unitario);

        return view('cliente.carrito', compact('carrito', 'detalles', 'total'));
    }

    public function agregar(Request $request): RedirectResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:producto,id',
            'cantidad'    => 'required|integer|min:1|max:99',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        if (!$producto->estado) {
            return back()->with('error', 'Este producto no está disponible.');
        }

        // Verificar stock disponible
        $stockDisponible = $producto->inventario?->stock_actual ?? 0;
        if ($request->cantidad > $stockDisponible) {
            return back()->with('error', "Solo hay {$stockDisponible} unidades disponibles de \"{$producto->nombre}\".");
        }

        $carrito = $this->carritoActivo();

        $detalle = CarritoDetalle::where('carrito_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if ($detalle) {
            $nuevaCantidad = $detalle->cantidad + $request->cantidad;
            if ($nuevaCantidad > $stockDisponible) {
                return back()->with('error', "No puedes agregar más de {$stockDisponible} unidades de \"{$producto->nombre}\".");
            }
            $detalle->update(['cantidad' => $nuevaCantidad]);
        } else {
            CarritoDetalle::create([
                'carrito_id'      => $carrito->id,
                'producto_id'     => $producto->id,
                'cantidad'        => $request->cantidad,
                'precio_unitario' => $producto->precio,
            ]);
        }

        return back()->with('success', '"' . $producto->nombre . '" agregado al carrito.');
    }

    public function actualizar(Request $request, CarritoDetalle $detalle): RedirectResponse
    {
        if ($detalle->carrito->usuario_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['cantidad' => 'required|integer|min:1|max:99']);

        $stockDisponible = $detalle->producto->inventario?->stock_actual ?? 0;
        if ($request->cantidad > $stockDisponible) {
            return back()->with('error', "Solo hay {$stockDisponible} unidades disponibles.");
        }

        $detalle->update(['cantidad' => $request->cantidad]);

        return back()->with('success', 'Cantidad actualizada.');
    }

    public function eliminar(CarritoDetalle $detalle): RedirectResponse
    {
        if ($detalle->carrito->usuario_id !== auth()->id()) {
            abort(403);
        }

        $detalle->delete();

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar(): RedirectResponse
    {
        $carrito = $this->carritoActivo();
        $carrito->detalles()->delete();

        return back()->with('success', 'Carrito vaciado.');
    }

    /**
     * Sincroniza el carrito visual (localStorage, usado en toda la tienda
     * pública) con el carrito real en base de datos que consume el checkout.
     * Reemplaza el contenido del carrito activo por lo que llega del cliente,
     * pero SIEMPRE revalida precio, estado y stock contra la BD — nunca
     * confía en los valores que manda el navegador.
     */
    public function sincronizar(Request $request)
    {
        $data = $request->validate([
            'items'            => 'present|array',
            'items.*.id'       => 'required|integer|exists:producto,id',
            'items.*.qty'      => 'required|integer|min:1|max:99',
        ]);

        $carrito = $this->carritoActivo();
        $avisos  = [];
        $idsValidos = [];

        foreach ($data['items'] as $item) {
            $producto = Producto::with('inventario')->find($item['id']);
            if (!$producto || !$producto->estado) {
                $avisos[] = "\"{$producto?->nombre}\" ya no está disponible y fue omitido.";
                continue;
            }

            $stock = $producto->inventario?->stock_actual ?? 0;
            $cantidad = min((int) $item['qty'], $stock);

            if ($cantidad < 1) {
                $avisos[] = "\"{$producto->nombre}\" está agotado y fue omitido.";
                continue;
            }
            if ($cantidad < $item['qty']) {
                $avisos[] = "Solo hay {$stock} unidades de \"{$producto->nombre}\", se ajustó la cantidad.";
            }

            CarritoDetalle::updateOrCreate(
                ['carrito_id' => $carrito->id, 'producto_id' => $producto->id],
                ['cantidad' => $cantidad, 'precio_unitario' => $producto->precio]
            );
            $idsValidos[] = $producto->id;
        }

        // Elimina del carrito real cualquier producto que ya no venga en la lista actual
        $carrito->detalles()->whereNotIn('producto_id', $idsValidos ?: [0])->delete();

        if ($carrito->detalles()->count() === 0) {
            return response()->json(['ok' => false, 'message' => 'No hay productos válidos para pagar.'], 422);
        }

        return response()->json(['ok' => true, 'avisos' => $avisos]);
    }
}
