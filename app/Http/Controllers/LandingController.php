<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Curso;
use App\Models\DetalleVentaProducto;
use App\Models\Inscripcion;
use App\Models\Producto;
use App\Models\Resena;

class LandingController extends Controller
{
    public function index()
    {
        $productosDestacados = $this->masVendidos(6);

        $cursosDestacados = Curso::where('estado', true)
            ->with('fechasDisponibles')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $banners = Banner::activos()->get();
        // Reseñas del inicio: las más recientes que estén activas, con cualquier calificación.
        $resenasClientes = Resena::with('usuario')
            ->activas()
            ->latest()
            ->take(3)
            ->get();

        // La reseña del cliente que está viendo la página, para que pueda editarla.
        $miResena = auth()->user()?->resena;

        return view('landing', compact('productosDestacados', 'cursosDestacados', 'banners', 'resenasClientes', 'miResena'));
    }

    /**
     * Productos destacados = los más vendidos: unidades vendidas en ventas
     * pagadas (las pendientes o canceladas no cuentan). Si todavía hay pocas
     * ventas, se completa con los productos más recientes.
     */
    private function masVendidos(int $cantidad)
    {
        $relaciones = ['categoria', 'imagenes', 'inventario', 'colores'];

        $ranking = DetalleVentaProducto::query()
            ->join('venta', 'venta.id', '=', 'detalle_venta_producto.venta_id')
            ->where('venta.estado', 'pagada')
            ->groupBy('detalle_venta_producto.producto_id')
            ->orderByRaw('sum(detalle_venta_producto.cantidad) desc')
            ->limit(30)
            ->pluck('detalle_venta_producto.producto_id')
            ->all();

        $vendidos = Producto::where('estado', true)
            ->whereIn('id', $ranking ?: [0])
            ->with($relaciones)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ranking))
            ->take($cantidad)
            ->values();

        if ($vendidos->count() < $cantidad) {
            $vendidos = $vendidos->concat(
                Producto::where('estado', true)
                    ->whereNotIn('id', $vendidos->pluck('id')->all() ?: [0])
                    ->with($relaciones)
                    ->orderByDesc('created_at')
                    ->take($cantidad - $vendidos->count())
                    ->get()
            );
        }

        return $vendidos;
    }

    public function nosotros()
    {
        return view('nosotros');
    }

    public function academia()
    {
        $cursos = Curso::where('estado', true)->with(['fechas' => fn ($q) => $q->whereDate('fecha', '>=', today())])->orderBy('id')->get();

        $misInscripciones = auth()->check()
            ? Inscripcion::with('cursoFecha')->where('usuario_id', auth()->id())->get()->keyBy('curso_id')
            : collect();

        return view('academia', compact('cursos', 'misInscripciones'));
    }

    public function contacto()
    {
        return view('contacto');
    }
}
