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
}
