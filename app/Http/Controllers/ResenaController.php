<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Reseña del cliente sobre el taller. Cada cliente tiene una sola y puede editarla. */
class ResenaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'required|string|min:5|max:1000',
        ], [
            'calificacion.required' => 'Elige de 1 a 5 estrellas.',
            'comentario.required'   => 'Escribe tu opinión.',
            'comentario.min'        => 'Escribe un poco más en tu opinión.',
        ]);

        // Nueva o editada, la reseña vuelve a revisión antes de publicarse.
        Resena::updateOrCreate(
            ['usuario_id' => $request->user()->id],
            ['calificacion' => $request->calificacion, 'comentario' => $request->comentario, 'estado' => 'pendiente']
        );

        return redirect()->to(route('inicio') . '#resenas')
            ->with('resena_ok', 'Gracias por tu reseña. La publicaremos cuando la revisemos.');
    }
}
