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
            ->with(['categoria', 'imagenes', 'inventario', 'resenas', 'colores'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $cursosDestacados = Curso::where('estado', true)
            ->with('fechasDisponibles')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $banners = Banner::activos()->get();
        // Reseñas del inicio: las más recientes de 4 o 5 estrellas ya aprobadas.
        $resenasClientes = Resena::with(['usuario', 'producto'])
            ->aprobadas()
            ->whereNotNull('usuario_id')
            ->where('calificacion', '>=', 4)
            ->latest()
            ->take(3)
            ->get();

        return view('landing', compact('productosDestacados', 'cursosDestacados', 'banners', 'resenasClientes'));
    }

    public function nosotros()
    {
        return view('nosotros');
    }

    public function academia()
    {
        $cursos = Curso::where('estado', true)->with(['fechas' => fn ($q) => $q->whereDate('fecha', '>=', today())])->orderBy('id')->get();

        $misInscripciones = auth()->check()
            ? Inscripcion::where('usuario_id', auth()->id())->get()->keyBy('curso_id')
            : collect();

        return view('academia', compact('cursos', 'misInscripciones'));
    }

    public function contacto()
    {
        return view('contacto');
    }
}
