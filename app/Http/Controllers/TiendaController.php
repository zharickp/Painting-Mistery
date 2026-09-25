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
            ->with(['categoria', 'imagenes', 'inventario', 'resenas', 'colores'])
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
            ->when(! in_array($orden, ['precio_asc', 'precio_desc', 'nombre', 'recientes'], true), function ($query) use ($normalizado) {
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
            ->get()
            ->map(function (CategoriaProducto $cat) {
                $cat->imagenRepresentativa = $cat->productos()
                    ->where('estado', true)
                    ->whereNotNull('imagen')
                    ->value('imagen');

                return $cat;
            });

        return view('tienda.index', compact(
            'productos', 'categorias', 'buscar', 'categoria', 'orden', 'porPagina',
            'precioMin', 'precioMax', 'soloStock', 'soloOferta'
        ));
    }
}
