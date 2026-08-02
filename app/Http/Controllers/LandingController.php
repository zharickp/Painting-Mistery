<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Curso;
use App\Models\Producto;

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

        return view('landing', compact('productosDestacados', 'cursosDestacados', 'banners'));
    }
}
