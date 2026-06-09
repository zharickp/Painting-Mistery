<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\TipoIva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        $productos = Producto::with('categoria', 'tipoIva')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.productos.index', compact('productos'));
    }

    public function create(): View
    {
        $categorias = CategoriaProducto::where('estado', true)->orderBy('nombre')->get();
        $tiposIva   = TipoIva::orderBy('porcentaje')->get();

        return view('admin.productos.create', compact('categorias', 'tiposIva'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string|max:500',
            'precio'                => 'required|numeric|min:0',
            'categoria_producto_id' => 'required|exists:categoria_producto,id',
            'tipo_iva_id'           => 'required|exists:tipo_iva,id',
            'imagen'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create([
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'precio'                => $request->precio,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
            'imagen'                => $rutaImagen ? Storage::url($rutaImagen) : null,
            'estado'                => true,
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto): View
    {
        $categorias = CategoriaProducto::where('estado', true)->orderBy('nombre')->get();
        $tiposIva   = TipoIva::orderBy('porcentaje')->get();

        return view('admin.productos.edit', compact('producto', 'categorias', 'tiposIva'));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string|max:500',
            'precio'                => 'required|numeric|min:0',
            'categoria_producto_id' => 'required|exists:categoria_producto,id',
            'tipo_iva_id'           => 'required|exists:tipo_iva,id',
            'imagen'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                $rutaAnterior = str_replace('/storage/', '', $producto->imagen);
                Storage::disk('public')->delete($rutaAnterior);
            }
            $rutaImagen = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = Storage::url($rutaImagen);
        }

        $producto->update([
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'precio'                => $request->precio,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
            'imagen'                => $producto->imagen,
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function toggleEstado(Producto $producto): RedirectResponse
    {
        $producto->update(['estado' => ! $producto->estado]);

        $mensaje = $producto->estado ? 'Producto activado.' : 'Producto desactivado.';

        return redirect()->route('admin.productos.index')
            ->with('success', $mensaje);
    }
}
