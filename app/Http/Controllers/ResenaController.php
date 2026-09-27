<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Resena;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function store(Request $request, Producto $producto): JsonResponse
    {
        // La ruta exige sesión (middleware auth): toda reseña queda ligada a un usuario.
        $usuario = $request->user();

        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'required|string|min:5|max:1000',
        ]);

        // Nueva o editada, la reseña vuelve a revisión antes de publicarse.
        $resena = Resena::updateOrCreate(
            ['producto_id' => $producto->id, 'usuario_id' => $usuario->id],
            ['calificacion' => $request->calificacion, 'comentario' => $request->comentario, 'estado' => 'pendiente']
        );

        $resena->load('usuario');
        $producto->load('resenas');

        return response()->json([
            'resena' => [
                'id'           => $resena->id,
                'usuario_id'   => $resena->usuario_id,
                'nombre'       => $resena->nombreMostrar(),
                'calificacion' => $resena->calificacion,
                'comentario'   => $resena->comentario,
                'fecha'        => $resena->created_at->diffForHumans(),
                'propia'       => true,
                'estado'       => $resena->estado,
            ],
            'resumen' => $producto->resumenResenas(),
        ]);
    }
}
