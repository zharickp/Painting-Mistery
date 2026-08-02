<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\ProductoColor;
use App\Models\ProductoImagen;
use App\Models\TipoIva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
            'nombre'                     => 'required|string|max:150',
            'descripcion'                => 'nullable|string|max:500',
            'precio'                     => 'required|numeric|min:0',
            'precio_anterior'            => 'nullable|numeric|gt:precio',
            'categoria_producto_id'      => 'required|exists:categoria_producto,id',
            'tipo_iva_id'                => 'required|exists:tipo_iva,id',
            'imagen'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'imagenes.*'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'grupos_color.*.nombre'      => 'nullable|string|max:40',
            'grupos_color.*.hex'         => 'nullable|string|max:7',
            'grupos_color.*.stock'       => 'nullable|integer|min:0',
            'grupos_color.*.archivos.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
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

        $this->guardarGaleriaSinColor($request, $producto);
        $this->guardarGaleriaPorColor($request, $producto);

        Inventario::create([
            'producto_id'          => $producto->id,
            'stock_actual'         => 0,
            'stock_minimo'         => 5,
            'ultima_actualizacion' => now(),
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    private function guardarGaleriaSinColor(Request $request, Producto $producto): void
    {
        if (! $request->hasFile('imagenes')) {
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

    /**
     * Busca una variante de color existente por nombre (sin importar mayúsculas)
     * o crea una nueva, para poder agregar fotos a un color ya creado.
     */
    private function buscarOCrearColor(Producto $producto, string $nombre, ?string $hex, int $stock): ProductoColor
    {
        $color = $producto->colores()->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])->first();

        if ($color) {
            return $color;
        }

        $orden = (int) $producto->colores()->max('orden');

        return $producto->colores()->create([
            'nombre' => $nombre,
            'hex'    => $hex,
            'stock'  => $stock,
            'orden'  => $orden + 1,
        ]);
    }

    /**
     * Guarda grupos de fotos subidos junto con un color (grupos_color[clave][nombre|hex|stock|archivos][]),
     * permitiendo cargar una variante de color completa (varias fotos y su stock) en un solo paso.
     */
    private function guardarGaleriaPorColor(Request $request, Producto $producto): void
    {
        $grupos         = $request->input('grupos_color', []);
        $archivosGrupos = $request->file('grupos_color', []);

        if (empty($grupos)) {
            return;
        }

        $orden = (int) $producto->imagenes()->max('orden');

        foreach ($grupos as $clave => $datos) {
            $archivos = $archivosGrupos[$clave]['archivos'] ?? [];
            $nombre   = trim((string) ($datos['nombre'] ?? ''));

            if (empty($archivos) || $nombre === '') {
                continue;
            }

            $color = $this->buscarOCrearColor($producto, $nombre, $datos['hex'] ?? null, (int) ($datos['stock'] ?? 0));

            foreach ($archivos as $archivo) {
                if (! $archivo instanceof UploadedFile) {
                    continue;
                }

                $orden++;
                $nombreArchivo = time() . '_' . $orden . '_' . $archivo->getClientOriginalName();
                $archivo->move(public_path('images/productos'), $nombreArchivo);

                ProductoImagen::create([
                    'producto_id'       => $producto->id,
                    'ruta'              => '/images/productos/' . $nombreArchivo,
                    'orden'             => $orden,
                    'producto_color_id' => $color->id,
                ]);
            }
        }
    }

    /**
     * Actualiza nombre, color y stock de las variantes de color ya existentes
     * (colores_existentes[{id}][nombre|hex|stock]).
     */
    private function actualizarColoresExistentes(Request $request, Producto $producto): void
    {
        if (! $request->has('colores_existentes')) {
            return;
        }

        foreach ($request->input('colores_existentes') as $colorId => $datos) {
            $nombre = trim((string) ($datos['nombre'] ?? ''));
            if ($nombre === '') {
                continue;
            }

            ProductoColor::where('id', $colorId)
                ->where('producto_id', $producto->id)
                ->update([
                    'nombre' => $nombre,
                    'hex'    => $datos['hex'] ?? null,
                    'stock'  => (int) ($datos['stock'] ?? 0),
                ]);
        }
    }

    private function guardarOrden(Request $request, Producto $producto): void
    {
        if (! $request->filled('orden_imagenes')) {
            return;
        }

        foreach ($request->input('orden_imagenes') as $posicion => $imagenId) {
            ProductoImagen::where('id', $imagenId)
                ->where('producto_id', $producto->id)
                ->update(['orden' => $posicion + 1]);
        }
    }

    private function guardarPortada(Request $request, Producto $producto): void
    {
        if (! $request->filled('imagen_portada')) {
            return;
        }

        // La portada general es independiente de los colores: solo se puede
        // promover una foto que no pertenezca a ninguna variante de color.
        $imagen = ProductoImagen::where('id', $request->input('imagen_portada'))
            ->where('producto_id', $producto->id)
            ->whereNull('producto_color_id')
            ->first();

        if ($imagen) {
            $producto->update(['imagen' => $imagen->ruta]);
        }
    }

    private function guardarRelacionados(Request $request, Producto $producto): void
    {
        $ids = collect($request->input('relacionados', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id !== $producto->id)
            ->unique()
            ->values();

        $sync = [];
        foreach ($ids as $posicion => $id) {
            $sync[$id] = ['orden' => $posicion + 1];
        }

        $producto->relacionadosManual()->sync($sync);
    }

    public function edit(Producto $producto): View
    {
        $categorias = CategoriaProducto::where('estado', true)->orderBy('nombre')->get();
        $tiposIva   = TipoIva::orderBy('porcentaje')->get();
        $producto->load(['imagenes', 'colores.imagenes']);

        $productosDisponibles = Producto::where('estado', true)
            ->where('id', '!=', $producto->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'imagen']);

        $relacionadosActuales = $producto->relacionadosManual;

        return view('admin.productos.edit', compact(
            'producto', 'categorias', 'tiposIva', 'productosDisponibles', 'relacionadosActuales'
        ));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'nombre'                     => 'required|string|max:150',
            'descripcion'                => 'nullable|string|max:500',
            'precio'                     => 'required|numeric|min:0',
            'precio_anterior'            => 'nullable|numeric|gt:precio',
            'categoria_producto_id'      => 'required|exists:categoria_producto,id',
            'tipo_iva_id'                => 'required|exists:tipo_iva,id',
            'imagen'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'imagenes.*'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'grupos_color.*.nombre'      => 'nullable|string|max:40',
            'grupos_color.*.hex'         => 'nullable|string|max:7',
            'grupos_color.*.stock'       => 'nullable|integer|min:0',
            'grupos_color.*.archivos.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'colores_existentes.*.nombre' => 'nullable|string|max:40',
            'colores_existentes.*.hex'    => 'nullable|string|max:7',
            'colores_existentes.*.stock'  => 'nullable|integer|min:0',
            'orden_imagenes.*'           => 'nullable|exists:producto_imagen,id',
            'imagen_portada'             => 'nullable|exists:producto_imagen,id',
            'relacionados.*'             => 'nullable|exists:producto,id',
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

        $this->actualizarColoresExistentes($request, $producto);
        $this->guardarGaleriaSinColor($request, $producto);
        $this->guardarGaleriaPorColor($request, $producto);
        $this->guardarOrden($request, $producto);
        $this->guardarPortada($request, $producto);
        $this->guardarRelacionados($request, $producto);

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
