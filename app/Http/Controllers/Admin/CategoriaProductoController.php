<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaProductoController extends Controller
{
    public function index(): View
    {
        $categorias = CategoriaProducto::withCount('productos')
            ->orderBy('nombre')
            ->paginate(10);

        return view('admin.categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'      => 'required|string|max:100|unique:categoria_producto,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        CategoriaProducto::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado'      => true,
        ]);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(CategoriaProducto $categoria): View
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, CategoriaProducto $categoria): RedirectResponse
    {
        $request->validate([
            'nombre'      => 'required|string|max:100|unique:categoria_producto,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        $categoria->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function toggleEstado(CategoriaProducto $categoria): RedirectResponse
    {
        $categoria->update(['estado' => ! $categoria->estado]);

        $mensaje = $categoria->estado ? 'Categoría activada.' : 'Categoría desactivada.';

        return redirect()->route('admin.categorias.index')
            ->with('success', $mensaje);
    }
}
