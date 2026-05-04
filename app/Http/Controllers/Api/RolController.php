<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class RolController extends Controller
{
    #[OA\Get(
        path: "/roles",
        summary: "Listar roles",
        tags: ["Roles"],
        responses: [
            new OA\Response(response: 200, description: "Lista de roles")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(Rol::all(), 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener roles'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/roles",
        summary: "Crear rol",
        tags: ["Roles"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Administrador"),
                    new OA\Property(property: "descripcion", type: "string", example: "Control total del sistema")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Rol creado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:50|unique:rol,nombre',
                'descripcion' => 'nullable|string|max:255'
            ]);

            $rol = Rol::create($data);

            return response()->json($rol, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear rol'
            ], 500);
        }
    }

    #[OA\Get(
        path: "/roles/{id}",
        summary: "Obtener rol en especifico",
        tags: ["Roles"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Rol encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        try {
            $rol = Rol::with('permisos')->findOrFail($id);

            return response()->json($rol, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/roles/{id}",
        summary: "Actualizar rol",
        tags: ["Roles"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Administrador"),
                    new OA\Property(property: "descripcion", type: "string", example: "Actualización de descripción")
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
            $rol = Rol::findOrFail($id);

            $data = $request->only(['nombre', 'descripcion']);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:50|unique:rol,nombre,' . $id,
                'descripcion' => 'nullable|string|max:255'
            ]);

            $rol->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $rol->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar rol'
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/roles/{id}",
        summary: "Eliminar rol",
        tags: ["Roles"],
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
            $rol = Rol::findOrFail($id);
            $rol->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar rol'
            ], 500);
        }
    }
}
