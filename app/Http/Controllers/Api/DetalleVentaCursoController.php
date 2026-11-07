<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVentaCurso;
use App\Models\Curso;
use App\Models\Venta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class DetalleVentaCursoController extends Controller
{
    #[OA\Get(
        path: "/detalle-venta-curso",
        summary: "Listar cursos vendidos",
        tags: ["Detalle Venta Curso"],
        responses: [
            new OA\Response(response: 200, description: "Lista")
        ]
    )]
    public function index()
    {
        return response()->json(
            DetalleVentaCurso::with(['venta', 'curso'])->get(),
            200
        );
    }

    #[OA\Get(
        path: "/detalle-venta-curso/{id}",
        summary: "Obtener detalle",
        tags: ["Detalle Venta Curso"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        try {
            return response()->json(
                DetalleVentaCurso::with(['venta', 'curso'])
                    ->findOrFail($id),
                200
            );
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }
}
