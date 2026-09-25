<?php

namespace App\Http\Controllers;

use App\Models\ResenaSitio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResenaSitioController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:80'],
            'calificacion' => ['required', 'integer', 'between:1,5'],
            'comentario'   => ['required', 'string', 'min:10', 'max:600'],
        ]);

        ResenaSitio::create($data + ['estado' => 'pendiente']);

        return redirect(route('inicio') . '#resenas')
            ->with('resena_ok', '¡Gracias por tu reseña! La publicaremos en cuanto la revisemos.');
    }
}
