<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TiendaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar     = trim((string) $request->query('buscar', ''));
        $categoria  = $request->query('categoria');
        $orden      = $request->query('orden', 'relevancia');
        $porPagina  = (int) $request->query('por_pagina', 12);
        $precioMin  = $request->query('precio_min');
        $precioMax  = $request->query('precio_max');
        $soloStock  = $request->boolean('en_stock');
        $soloOferta = $request->boolean('oferta');

        if (! in_array($porPagina, [9, 12, 18, 24], true)) {
            $porPagina = 12;
        }

        // Normaliza: sin tildes, minúsculas y espacios de más; cada palabra debe coincidir en algún campo.
        $normalizado = mb_strtolower(Str::ascii(preg_replace('/\s+/u', ' ', $buscar)));
        $terminos    = $normalizado === '' ? [] : array_values(array_filter(explode(' ', $normalizado)));

        $productos = Producto::where('producto.estado', true)
            ->with(['categoria', 'imagenes', 'inventario', 'colores'])
            ->when($terminos !== [], function ($query) use ($terminos) {
                foreach ($terminos as $t) {
                    $like = '%' . addcslashes($t, '\\%_') . '%';
                    $query->where(function ($sub) use ($t, $like) {
                        $sub->whereRaw("translate(lower(producto.nombre), 'áéíóúüñ', 'aeiouun') like ?", [$like])
                            ->orWhereRaw("translate(lower(coalesce(producto.descripcion, '')), 'áéíóúüñ', 'aeiouun') like ?", [$like])
                            ->orWhereHas('categoria', fn ($c) => $c->whereRaw("translate(lower(nombre), 'áéíóúüñ', 'aeiouun') like ?", [$like]))
                            ->orWhereHas('colores', fn ($c) => $c->whereRaw("translate(lower(nombre), 'áéíóúüñ', 'aeiouun') like ?", [$like]));
                        if (ctype_digit($t)) {
                            $sub->orWhere('producto.id', (int) $t);
                        }
                    });
                }
            })
            ->when($categoria, fn ($query) => $query->where('categoria_producto_id', $categoria))
            ->when(is_numeric($precioMin), fn ($query) => $query->where('precio', '>=', (float) $precioMin))
            ->when(is_numeric($precioMax), fn ($query) => $query->where('precio', '<=', (float) $precioMax))
            ->when($soloOferta, fn ($query) => $query->whereNotNull('precio_anterior')->whereColumn('precio_anterior', '>', 'precio'))
            ->when($soloStock, fn ($query) => $query->whereHas('inventario', fn ($q) => $q->where('stock_actual', '>', 0)))
            ->when($orden === 'precio_asc', fn ($query) => $query->orderBy('precio', 'asc'))
            ->when($orden === 'precio_desc', fn ($query) => $query->orderBy('precio', 'desc'))
            ->when($orden === 'nombre', fn ($query) => $query->orderBy('nombre', 'asc'))
            ->when($orden === 'recientes', fn ($query) => $query->orderByDesc('created_at'))
            // Más vendidos: unidades vendidas en ventas pagadas.
            ->when($orden === 'vendidos', fn ($query) => $query->orderByDesc(
                \App\Models\DetalleVentaProducto::selectRaw('coalesce(sum(detalle_venta_producto.cantidad), 0)')
                    ->join('venta', 'venta.id', '=', 'detalle_venta_producto.venta_id')
                    ->whereColumn('detalle_venta_producto.producto_id', 'producto.id')
                    ->where('venta.estado', 'pagada')
            )->orderByDesc('producto.created_at'))
            ->when(! in_array($orden, ['precio_asc', 'precio_desc', 'nombre', 'recientes', 'vendidos'], true), function ($query) use ($normalizado) {
                if ($normalizado !== '') {
                    $query->orderByRaw("case when translate(lower(producto.nombre), 'áéíóúüñ', 'aeiouun') like ? then 0 else 1 end", ['%' . $normalizado . '%']);
                }
                $query->orderByDesc('producto.created_at');
            })
            ->paginate($porPagina)
            ->withQueryString();

        $categorias = CategoriaProducto::where('estado', true)
            ->withCount(['productos' => fn ($query) => $query->where('estado', true)])
            ->orderBy('nombre')
            ->get();

        return view('tienda.index', compact(
            'productos', 'categorias', 'buscar', 'categoria', 'orden', 'porPagina',
            'precioMin', 'precioMax', 'soloStock', 'soloOferta'
        ));
    }

    /**
     * Existencias y precio actuales de los productos del carrito del navegador.
     * El carrito se guarda en el navegador y puede tener datos viejos: con esto
     * se muestran las unidades disponibles reales y no se deja pedir más.
     * Solo responde stock, precio, nombre y si el producto sigue a la venta.
     */
    public function existencias(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(100)
            ->values();

        $productos = Producto::with('inventario')->whereIn('id', $ids)->get();

        $datos = $productos->mapWithKeys(fn (Producto $p) => [$p->id => [
            'stock'      => $p->estado ? max(0, $p->stockActual()) : 0,
            'disponible' => (bool) $p->estado,
            'precio'     => (float) $p->precio,
            'nombre'     => $p->nombre,
        ]]);

        // Los que ya no existen se marcan como no disponibles.
        foreach ($ids as $id) {
            if (! $datos->has($id)) {
                $datos[$id] = ['stock' => 0, 'disponible' => false, 'precio' => null, 'nombre' => null];
            }
        }

        return response()->json($datos);
    }
}
