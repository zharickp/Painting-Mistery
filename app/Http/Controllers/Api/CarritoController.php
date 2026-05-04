<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class CarritoController extends Controller
{
    #[OA\Get(
        path: "/carrito",
        summary: "Listar carritos",
        tags: ["Carrito"],
        responses: [
            new OA\Response(response: 200, description: "Lista de carritos"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(
                Carrito::with(['usuario','detalles'])->get(),
                200
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener carritos'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/carrito",
        summary: "Crear carrito",
        tags: ["Carrito"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["usuario_id"],
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer"),
                    new OA\Property(property: "estado", type: "string", example: "activo")
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
                'usuario_id' => 'required|exists:usuario,id',
                'estado' => 'nullable|string|max:20'
            ]);

            $data['estado'] = $data['estado'] ?? 'activo';

            $carrito = Carrito::create($data);

            return response()->json($carrito, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Get(
        path: "/carrito/{id}",
        summary: "Obtener carrito",
        tags: ["Carrito"],
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
                Carrito::with(['usuario','detalles.producto'])->findOrFail($id),
                200
            );

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Carrito no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/carrito/{id}",
        summary: "Actualizar carrito",
        tags: ["Carrito"],
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
                    new OA\Property(property: "estado", type: "string")
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
            $carrito = Carrito::findOrFail($id);

            $data = $request->only(['estado']);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'estado' => 'sometimes|string|max:20'
            ]);

            $carrito->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $carrito->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Carrito no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/carrito/{id}",
        summary: "Eliminar carrito",
        tags: ["Carrito"],
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
            $carrito = Carrito::findOrFail($id);
            $carrito->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Carrito no encontrado'
            ], 404);
        }
    }
}
