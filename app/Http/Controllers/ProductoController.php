<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function show(Producto $producto): View
    {
        abort_if(! $producto->estado, 404);

        $producto->load(['categoria', 'imagenes', 'resenas.usuario', 'inventario']);

        $recomendados = Producto::where('estado', true)
            ->where('id', '!=', $producto->id)
            ->with(['categoria', 'imagenes', 'inventario'])
            ->orderByRaw('CASE WHEN categoria_producto_id = ? THEN 0 ELSE 1 END', [$producto->categoria_producto_id])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('productos.show', compact('producto', 'recomendados'));
    }
}
