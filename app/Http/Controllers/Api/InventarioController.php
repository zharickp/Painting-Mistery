<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
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

    #[OA\Post(
        path: "/inventario",
        summary: "Crear inventario",
        tags: ["Inventario"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["producto_id","stock_actual"],
                properties: [
                    new OA\Property(property: "producto_id", type: "integer"),
                    new OA\Property(property: "stock_actual", type: "integer"),
                    new OA\Property(property: "stock_minimo", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Creado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'producto_id' => 'required|exists:producto,id|unique:inventario,producto_id',
                'stock_actual' => 'required|integer|min:0',
                'stock_minimo' => 'nullable|integer|min:0'
            ]);

            $data['ultima_actualizacion'] = now();

            $inventario = Inventario::create($data);

            return response()->json($inventario, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
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

    #[OA\Put(
        path: "/inventario/{id}",
        summary: "Actualizar inventario",
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "stock_actual", type: "integer"),
                    new OA\Property(property: "stock_minimo", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Actualizado"),
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $inventario = Inventario::findOrFail($id);

            $data = $request->only([
                'stock_actual',
                'stock_minimo'
            ]);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'stock_actual' => 'sometimes|integer|min:0',
                'stock_minimo' => 'nullable|integer|min:0'
            ]);

            $validated['ultima_actualizacion'] = now();

            $inventario->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $inventario->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Inventario no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/inventario/{id}",
        summary: "Eliminar inventario",
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
            new OA\Response(response: 200, description: "Eliminado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function destroy($id)
    {
        try {
            $inventario = Inventario::findOrFail($id);
            $inventario->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Inventario no encontrado'
            ], 404);
        }
    }
}
