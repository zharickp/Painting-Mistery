<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventarioController extends Controller
{
    public function index(): View
    {
        $inventarios = Inventario::with('producto.categoria')
            ->orderBy('stock_actual')
            ->paginate(15);

        return view('admin.inventario', compact('inventarios'));
    }

    public function actualizar(Request $request, Inventario $inventario): RedirectResponse
    {
        $request->validate([
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ], [
            'stock_actual.min' => 'El stock no puede ser negativo.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
        ]);

        $inventario->update([
            'stock_actual'         => $request->stock_actual,
            'stock_minimo'         => $request->stock_minimo,
            'ultima_actualizacion' => now(),
        ]);

        return redirect()->route('admin.inventario')
            ->with('success', "Stock de \"{$inventario->producto->nombre}\" actualizado correctamente.");
    }
}
