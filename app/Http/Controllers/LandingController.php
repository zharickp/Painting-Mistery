<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Producto;
use App\Models\ResenaSitio;

class LandingController extends Controller
{
    public function index()
    {
        $productosDestacados = Producto::where('estado', true)
            ->with(['categoria', 'imagenes', 'resenas.usuario', 'colores'])
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        $cursosDestacados = Curso::where('estado', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $banners = Banner::activos()->get();
        $resenasSitio = ResenaSitio::aprobadas()->latest()->take(12)->get();

        return view('landing', compact('productosDestacados', 'cursosDestacados', 'banners', 'resenasSitio'));
    }

    public function nosotros()
    {
        return view('nosotros');
    }

    public function academia()
    {
        $cursos = Curso::where('estado', true)->with('info')->orderBy('id')->get();

        $misInscripciones = auth()->check()
            ? Inscripcion::with('agenda')->where('usuario_id', auth()->id())->get()->keyBy('curso_id')
            : collect();

        return view('academia', compact('cursos', 'misInscripciones'));
    }
}
