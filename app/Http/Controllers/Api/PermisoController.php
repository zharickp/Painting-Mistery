<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class PermisoController extends Controller
{
    #[OA\Get(
        path: "/permisos",
        summary: "Listar permisos",
        tags: ["Permisos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de permisos"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(Permiso::all(), 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener permisos'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/permisos",
        summary: "Crear permiso",
        tags: ["Permisos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "crear_usuario"),
                    new OA\Property(property: "descripcion", type: "string", example: "Permite crear usuarios")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Permiso creado"),
            new OA\Response(response: 422, description: "Error de validación"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:50|unique:permiso,nombre',
                'descripcion' => 'nullable|string|max:255'
            ]);

            $permiso = Permiso::create($data);

            return response()->json($permiso, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear permiso'
            ], 500);
        }
    }

    #[OA\Get(
        path: "/permisos/{id}",
        summary: "Obtener permiso",
        tags: ["Permisos"],
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
            new OA\Response(response: 200, description: "Permiso encontrado"),
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function show($id)
    {
        try {
            $permiso = Permiso::findOrFail($id);

            return response()->json($permiso, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Permiso no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener permiso'
            ], 500);
        }
    }

    #[OA\Put(
        path: "/permisos/{id}",
        summary: "Actualizar permiso",
        tags: ["Permisos"],
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
                    new OA\Property(property: "nombre", type: "string", example: "editar_usuario"),
                    new OA\Property(property: "descripcion", type: "string", example: "Permite editar usuarios")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Actualizado"),
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 422, description: "Error de validación"),
            new OA\Response(response: 400, description: "Sin datos")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $permiso = Permiso::findOrFail($id);

            $data = $request->only(['nombre', 'descripcion']);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:50|unique:permiso,nombre,' . $id,
                'descripcion' => 'nullable|string|max:255'
            ]);

            $permiso->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $permiso->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Permiso no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar permiso'
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/permisos/{id}",
        summary: "Eliminar permiso",
        tags: ["Permisos"],
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
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function destroy($id)
    {
        try {
            $permiso = Permiso::findOrFail($id);
            $permiso->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Permiso no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar permiso'
            ], 500);
        }
    }
}
