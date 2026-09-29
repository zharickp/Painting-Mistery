<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\ProductoColor;
use App\Models\ProductoImagen;
use App\Models\TipoIva;
use App\Services\AuditoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(): View
    {
        $productos = Producto::with('categoria', 'tipoIva', 'imagenes')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.productos.index', compact('productos'));
    }

    public function create(): View
    {
        $categorias = CategoriaProducto::where('estado', true)->orderBy('nombre')->get();
        $tiposIva   = TipoIva::activos()->orderBy('porcentaje')->get();

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
            'tipo_iva_id'                => ['required', Rule::exists('tipo_iva', 'id')->where('estado', true)],
            'imagen'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'imagenes.*'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'grupos_color.*.nombre'      => 'nullable|string|max:40',
            'grupos_color.*.hex'         => 'nullable|string|max:7',
            'grupos_color.*.archivos.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $producto = Producto::create([
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'precio'                => $request->precio,
            'precio_anterior'       => $request->precio_anterior,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
            'estado'                => true,
        ]);

        $this->guardarFotoPortada($request, $producto);
        $this->guardarGaleriaSinColor($request, $producto);
        $this->guardarGaleriaPorColor($request, $producto);

        Inventario::create([
            'producto_id'          => $producto->id,
            'stock_actual'         => 0,
            'stock_minimo'         => 5,
        ]);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Foto subida en el campo "Foto de portada": se guarda como foto general
     * del producto y queda marcada como portada.
     */
    private function guardarFotoPortada(Request $request, Producto $producto): void
    {
        if (! $request->hasFile('imagen')) {
            return;
        }

        $archivo       = $request->file('imagen');
        $nombreArchivo = time() . '_portada_' . $archivo->getClientOriginalName();
        $archivo->move(public_path('images/productos'), $nombreArchivo);

        $orden = (int) $producto->imagenes()->min('orden');

        $producto->imagenes()->update(['es_portada' => false]);
        ProductoImagen::create([
            'producto_id' => $producto->id,
            'ruta'        => '/images/productos/' . $nombreArchivo,
            'orden'       => $orden - 1,
            'es_portada'  => true,
        ]);
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
    private function buscarOCrearColor(Producto $producto, string $nombre, ?string $hex): ProductoColor
    {
        $color = $producto->colores()->whereRaw('LOWER(nombre) = ?', [mb_strtolower($nombre)])->first();

        if ($color) {
            return $color;
        }

        $orden = (int) $producto->colores()->max('orden');

        return $producto->colores()->create([
            'nombre' => $nombre,
            'hex'    => $hex,
            'orden'  => $orden + 1,
        ]);
    }

    /**
     * Guarda grupos de fotos subidos junto con un color (grupos_color[clave][nombre|hex|archivos][]),
     * permitiendo cargar las fotos de un color en un solo paso.
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

            $color = $this->buscarOCrearColor($producto, $nombre, $datos['hex'] ?? null);

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
     * Actualiza nombre y color de las variantes de color ya existentes
     * (colores_existentes[{id}][nombre|hex]).
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
            DB::transaction(function () use ($producto, $imagen) {
                $producto->imagenes()->update(['es_portada' => false]);
                $imagen->update(['es_portada' => true]);
            });
        }
    }

    public function edit(Producto $producto): View
    {
        $categorias = CategoriaProducto::where('estado', true)->orderBy('nombre')->get();
        $tiposIva   = TipoIva::activos()->orWhere('id', $producto->tipo_iva_id)->orderBy('porcentaje')->get();
        $producto->load(['imagenes', 'colores.imagenes']);

        return view('admin.productos.edit', compact('producto', 'categorias', 'tiposIva'));
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'nombre'                     => 'required|string|max:150',
            'descripcion'                => 'nullable|string|max:500',
            'precio'                     => 'required|numeric|min:0',
            'precio_anterior'            => 'nullable|numeric|gt:precio',
            'categoria_producto_id'      => 'required|exists:categoria_producto,id',
            'tipo_iva_id'                => ['required', Rule::exists('tipo_iva', 'id')->where(fn ($q) => $q->where('estado', true)->orWhere('id', $producto->tipo_iva_id))],
            'imagen'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'imagenes.*'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'grupos_color.*.nombre'      => 'nullable|string|max:40',
            'grupos_color.*.hex'         => 'nullable|string|max:7',
            'grupos_color.*.archivos.*'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'colores_existentes.*.nombre' => 'nullable|string|max:40',
            'colores_existentes.*.hex'    => 'nullable|string|max:7',
            'fotos_color.*.*'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'orden_imagenes.*'           => 'nullable|exists:producto_imagen,id',
            'imagen_portada'             => 'nullable|exists:producto_imagen,id',
        ]);

        $producto->update([
            'nombre'                => $request->nombre,
            'descripcion'           => $request->descripcion,
            'precio'                => $request->precio,
            'precio_anterior'       => $request->precio_anterior,
            'categoria_producto_id' => $request->categoria_producto_id,
            'tipo_iva_id'           => $request->tipo_iva_id,
        ]);

        $this->actualizarColoresExistentes($request, $producto);
        $this->guardarGaleriaSinColor($request, $producto);
        $this->guardarFotosColoresExistentes($request, $producto);
        $this->guardarGaleriaPorColor($request, $producto);
        $this->guardarOrden($request, $producto);
        $this->guardarPortada($request, $producto);
        // Una foto nueva subida como portada tiene prioridad sobre la elegida en la lista.
        $this->guardarFotoPortada($request, $producto);

        return redirect()->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Fotos nuevas para un color que ya existe (fotos_color[{colorId}][]).
     */
    private function guardarFotosColoresExistentes(Request $request, Producto $producto): void
    {
        $archivosPorColor = $request->file('fotos_color', []);
        if (empty($archivosPorColor)) {
            return;
        }

        $orden = (int) $producto->imagenes()->max('orden');

        foreach ($archivosPorColor as $colorId => $archivos) {
            $color = $producto->colores()->whereKey($colorId)->first();
            if (! $color) {
                continue;
            }

            foreach ((array) $archivos as $archivo) {
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
     * Elimina una foto al instante (se llama con fetch desde la edición del producto).
     * Si era la última foto de su color, el color también se elimina para que
     * en la tienda no quede un círculo de color sin fotos.
     */
    public function eliminarImagen(Producto $producto, ProductoImagen $imagen): JsonResponse
    {
        abort_unless($imagen->producto_id === $producto->id, 404);

        $colorId = $imagen->producto_color_id;
        $this->borrarArchivoImagen($producto, $imagen);
        $imagen->delete();

        $colorEliminado = null;
        if ($colorId && ! ProductoImagen::where('producto_color_id', $colorId)->exists()) {
            $color = ProductoColor::find($colorId);
            $colorEliminado = $color?->nombre;
            $color?->delete();
        }

        AuditoriaService::registrar([
            'accion'            => 'eliminado',
            'modulo'            => 'Producto',
            'registro_id'       => $producto->id,
            'registro_etiqueta' => $producto->nombre,
            'descripcion'       => "Eliminó una foto del producto \"{$producto->nombre}\""
                                   . ($colorEliminado ? " y el color \"{$colorEliminado}\", que quedó sin fotos" : ''),
        ]);

        return response()->json([
            'ok'             => true,
            'color_eliminado'=> $colorEliminado ? $colorId : null,
        ]);
    }

    /**
     * Elimina un color con todas sus fotos.
     */
    public function eliminarColor(Producto $producto, ProductoColor $color): JsonResponse
    {
        abort_unless($color->producto_id === $producto->id, 404);

        $fotos = ProductoImagen::where('producto_color_id', $color->id)->get();
        foreach ($fotos as $foto) {
            $this->borrarArchivoImagen($producto, $foto);
            $foto->delete();
        }

        $nombre = $color->nombre;
        $color->delete();

        AuditoriaService::registrar([
            'accion'            => 'eliminado',
            'modulo'            => 'Producto',
            'registro_id'       => $producto->id,
            'registro_etiqueta' => $producto->nombre,
            'descripcion'       => "Eliminó el color \"{$nombre}\" ({$fotos->count()} foto(s)) del producto \"{$producto->nombre}\"",
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Borra el archivo físico de una foto. Si era la portada, el producto pasa
     * a usar como portada su primera foto general.
     */
    private function borrarArchivoImagen(Producto $producto, ProductoImagen $imagen): void
    {
        $ruta = public_path($imagen->ruta);
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    public function toggleEstado(Producto $producto): RedirectResponse
    {
        $producto->update(['estado' => ! $producto->estado]);

        $mensaje = $producto->estado ? 'Producto activado.' : 'Producto desactivado.';

        return redirect()->route('admin.productos.index')
            ->with('success', $mensaje);
    }
}
