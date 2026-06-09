<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Models\TipoIva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $nombreArchivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('images/productos'), $nombreArchivo);
            $rutaImagen = '/images/productos/' . $nombreArchivo;
        }

        Producto::create([
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'precio'                => $request->precio,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
            'imagen'                => $rutaImagen,
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
            // Elimina imagen anterior si existe
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }
            $nombreArchivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('images/productos'), $nombreArchivo);
            $producto->imagen = '/images/productos/' . $nombreArchivo;
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
