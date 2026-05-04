<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CategoriaProductoController extends Controller
{
    #[OA\Get(
        path: "/categorias",
        summary: "Listar categorías",
        tags: ["Categorías"],
        responses: [
            new OA\Response(response: 200, description: "Lista de categorías")
        ]
    )]
    public function index()
    {
        return response()->json(CategoriaProducto::all());
    }

    #[OA\Post(
        path: "/categorias",
        summary: "Crear categoría",
        tags: ["Categorías"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "estado", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Creada")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required'
        ]);

        return response()->json(CategoriaProducto::create($data), 201);
    }

    #[OA\Get(
        path: "/categorias/{id}",
        summary: "Obtener categoría",
        tags: ["Categorías"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Encontrada"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function show($id)
    {
        return response()->json(CategoriaProducto::findOrFail($id));
    }

    #[OA\Put(
        path: "/categorias/{id}",
        summary: "Actualizar categoría",
        tags: ["Categorías"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Actualizada")
        ]
    )]
    public function update(Request $request, $id)
    {
        $categoria = CategoriaProducto::findOrFail($id);
        $categoria->update($request->all());

        return response()->json($categoria);
    }

    #[OA\Delete(
        path: "/categorias/{id}",
        summary: "Eliminar categoría",
        tags: ["Categorías"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Eliminada")
        ]
    )]
    public function destroy($id)
    {
        CategoriaProducto::destroy($id);

        return response()->noContent();
    }
}
