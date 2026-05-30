<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Producto;

class LandingController extends Controller
{
    public function index()
    {
        $productosDestacados = Producto::where('estado', true)
            ->with('categoria')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $cursosDestacados = Curso::where('estado', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('landing', compact('productosDestacados', 'cursosDestacados'));
    }
}
