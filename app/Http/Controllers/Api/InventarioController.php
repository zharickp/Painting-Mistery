<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class InventarioController extends Controller
{
    #[OA\Get(
        path: "/inventario",
        summary: "Listar inventario",
        tags: ["Inventario"],
        responses: [
            new OA\Response(response: 200, description: "Lista de inventario"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(
                Inventario::with('producto')->get(),
                200
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener inventario'
            ], 500);
        }
    }

    #[OA\Get(
        path: "/inventario/{id}",
        summary: "Obtener inventario",
        tags: ["Inventario"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                example: 1
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
                Inventario::with('producto')->findOrFail($id),
                200
            );

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Inventario no encontrado'
            ], 404);
        }
    }
}
