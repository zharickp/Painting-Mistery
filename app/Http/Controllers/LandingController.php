<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Producto;
use App\Models\Resena;

class LandingController extends Controller
{
    public function index()
    {
        $productosDestacados = Producto::where('estado', true)
            ->with(['categoria', 'imagenes', 'inventario', 'colores'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

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
