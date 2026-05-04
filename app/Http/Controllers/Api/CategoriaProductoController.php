<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class CategoriaProductoController extends Controller
{
    #[OA\Get(
        path: "/categorias",
        summary: "Listar categorías",
        tags: ["Categorías"],
        responses: [
            new OA\Response(response: 200, description: "Lista de categorías"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(CategoriaProducto::all(), 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener categorías'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/categorias",
        summary: "Crear categoría",
        tags: ["Categorías"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Pinturas"),
                    new OA\Property(property: "descripcion", type: "string", example: "Categoría de productos"),
                    new OA\Property(property: "estado", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Creada"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:100|unique:categoria_producto,nombre',
                'descripcion' => 'nullable|string',
                'estado' => 'nullable|boolean'
            ]);

            $categoria = CategoriaProducto::create($data);

            return response()->json($categoria, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
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
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Encontrada"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function show($id)
    {
        try {
            $categoria = CategoriaProducto::findOrFail($id);

            return response()->json($categoria, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }
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
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Pinturas"),
                    new OA\Property(property: "descripcion", type: "string", example: "Descripción"),
                    new OA\Property(property: "estado", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Actualizada"),
            new OA\Response(response: 404, description: "No encontrada"),
            new OA\Response(response: 422, description: "Error de validación"),
            new OA\Response(response: 400, description: "Sin datos")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $categoria = CategoriaProducto::findOrFail($id);

            $data = $request->only(['nombre', 'descripcion', 'estado']);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:100|unique:categoria_producto,nombre,' . $id,
                'descripcion' => 'nullable|string',
                'estado' => 'nullable|boolean'
            ]);

            $categoria->update($validated);

            return response()->json([
                'message' => 'Actualizada correctamente',
                'data' => $categoria->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar categoría'
            ], 500);
        }
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
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Eliminada"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function destroy($id)
    {
        try {
            $categoria = CategoriaProducto::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'message' => 'Eliminada correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }
    }
}
