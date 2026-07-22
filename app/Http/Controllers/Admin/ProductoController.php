<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\ProductoImagen;
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
            'precio_anterior'       => 'nullable|numeric|gt:precio',
            'categoria_producto_id' => 'required|exists:categoria_producto,id',
            'tipo_iva_id'           => 'required|exists:tipo_iva,id',
            'imagen'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'imagenes.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $nombreArchivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('images/productos'), $nombreArchivo);
            $rutaImagen = '/images/productos/' . $nombreArchivo;
        }

        $producto = Producto::create([
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'precio'                => $request->precio,
            'precio_anterior'       => $request->precio_anterior,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
            'imagen'                => $rutaImagen,
            'estado'                => true,
        ]);

        $this->guardarGaleria($request, $producto);

        Inventario::create([
            'producto_id'          => $producto->id,
            'stock_actual'         => 0,
            'stock_minimo'         => 5,
            'ultima_actualizacion' => now(),
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    private function guardarGaleria(Request $request, Producto $producto): void
    {
        if (!$request->hasFile('imagenes')) {
            return;
        }

        $orden = (int) $producto->imagenes()->max('orden');

        foreach ($request->file('imagenes') as $archivo) {
            $orden++;
            $nombreArchivo = time() . '_' . $orden . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path('images/productos'), $nombreArchivo);

            ProductoImagen::create([
                'producto_id' => $producto->id,
                'ruta'        => '/images/productos/' . $nombreArchivo,
                'orden'       => $orden,
            ]);
        }
    }

    private function guardarColores(Request $request, Producto $producto): void
    {
        if (! $request->has('colores')) {
            return;
        }

        foreach ($request->input('colores') as $imagenId => $datos) {
            $nombre = trim((string) ($datos['nombre'] ?? ''));

            ProductoImagen::where('id', $imagenId)
                ->where('producto_id', $producto->id)
                ->update([
                    'color_nombre' => $nombre !== '' ? $nombre : null,
                    'color_hex'    => $nombre !== '' ? ($datos['hex'] ?? null) : null,
                ]);
        }
    }

    public function edit(Producto $producto): View
    {
        $categorias = CategoriaProducto::where('estado', true)->orderBy('nombre')->get();
        $tiposIva   = TipoIva::orderBy('porcentaje')->get();
        $producto->load('imagenes');

        return view('admin.productos.edit', compact('producto', 'categorias', 'tiposIva'));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string|max:500',
            'precio'                => 'required|numeric|min:0',
            'precio_anterior'       => 'nullable|numeric|gt:precio',
            'categoria_producto_id' => 'required|exists:categoria_producto,id',
            'tipo_iva_id'           => 'required|exists:tipo_iva,id',
            'imagen'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'imagenes.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        if ($request->hasFile('imagen')) {
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
            'precio_anterior'       => $request->precio_anterior,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
            'imagen'                => $producto->imagen,
        ]);

        if ($request->filled('eliminar_imagenes')) {
            $aEliminar = ProductoImagen::where('producto_id', $producto->id)
                ->whereIn('id', $request->eliminar_imagenes)
                ->get();

            foreach ($aEliminar as $img) {
                if (file_exists(public_path($img->ruta))) {
                    unlink(public_path($img->ruta));
                }
                $img->delete();
            }
        }

        $this->guardarColores($request, $producto);
        $this->guardarGaleria($request, $producto);

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
