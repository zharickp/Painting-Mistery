<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TiendaController extends Controller
{
    public function index(Request $request): View
    {
        $buscar    = trim((string) $request->query('buscar', ''));
        $categoria = $request->query('categoria');
        $orden     = $request->query('orden', 'relevancia');
        $porPagina = (int) $request->query('por_pagina', 12);

        if (! in_array($porPagina, [9, 12, 18, 24], true)) {
            $porPagina = 12;
        }

        $productos = Producto::where('estado', true)
            ->with(['categoria', 'imagenes', 'inventario', 'resenas', 'colores'])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'ilike', "%{$buscar}%")
                        ->orWhere('descripcion', 'ilike', "%{$buscar}%");
                });
            })
            ->when($categoria, fn ($query) => $query->where('categoria_producto_id', $categoria))
            ->when($orden === 'precio_asc', fn ($query) => $query->orderBy('precio', 'asc'))
            ->when($orden === 'precio_desc', fn ($query) => $query->orderBy('precio', 'desc'))
            ->when($orden === 'nombre', fn ($query) => $query->orderBy('nombre', 'asc'))
            ->when($orden === 'recientes', fn ($query) => $query->orderByDesc('created_at'))
            ->when(! in_array($orden, ['precio_asc', 'precio_desc', 'nombre', 'recientes'], true), fn ($query) => $query->orderByDesc('created_at'))
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

        return view('tienda.index', compact('productos', 'categorias', 'buscar', 'categoria', 'orden', 'porPagina'));
    }
}
