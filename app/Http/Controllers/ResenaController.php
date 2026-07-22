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
        $usuario = $request->user();

        $reglas = [
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'required|string|min:5|max:1000',
        ];

        if (! $usuario) {
            $reglas['nombre'] = 'required|string|max:100';
            $reglas['correo'] = 'required|email|max:150';
        }

        $request->validate($reglas);

        if ($usuario) {
            $resena = Resena::updateOrCreate(
                ['producto_id' => $producto->id, 'usuario_id' => $usuario->id],
                ['calificacion' => $request->calificacion, 'comentario' => $request->comentario]
            );
        } else {
            $resena = Resena::create([
                'producto_id'     => $producto->id,
                'usuario_id'      => null,
                'nombre_invitado' => $request->nombre,
                'correo_invitado' => $request->correo,
                'calificacion'    => $request->calificacion,
                'comentario'      => $request->comentario,
            ]);
        }

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
            ],
            'resumen' => $producto->resumenResenas(),
        ]);
    }
}
