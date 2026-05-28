<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RolesPermisos;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class RolesPermisosController extends Controller
{
    #[OA\Get(
        path: "/roles-permisos",
        summary: "Listar permisos de roles",
        tags: ["Roles Permisos"],
        responses: [
            new OA\Response(response: 200, description: "Lista obtenida")
        ]
    )]
    public function index()
    {
        try {

            return response()->json(
                RolesPermisos::with(['rol', 'permiso'])->get(),
                200
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al obtener asignaciones'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/roles-permisos",
        summary: "Asignar permiso a rol",
        tags: ["Roles Permisos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rol_id", "permiso_id"],
                properties: [
                    new OA\Property(property: "rol_id", type: "integer", example: 1),
                    new OA\Property(property: "permiso_id", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Asignación creada"),
            new OA\Response(response: 422, description: "Error validación")
        ]
    )]
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'rol_id' => 'required|exists:rol,id',
                'permiso_id' => 'required|exists:permiso,id'
            ]);

            $existe = RolesPermisos::where('rol_id', $data['rol_id'])
                ->where('permiso_id', $data['permiso_id'])
                ->exists();

            if ($existe) {

                return response()->json([
                    'message' => 'El rol ya tiene ese permiso'
                ], 409);
            }

            $asignacion = RolesPermisos::create($data);

            return response()->json($asignacion, 201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al asignar permiso'
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/roles-permisos/{id}",
        summary: "Eliminar asignación rol-permiso",
        tags: ["Roles Permisos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
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

            $asignacion = RolesPermisos::findOrFail($id);

            $asignacion->delete();

            return response()->json([
                'message' => 'Asignación eliminada correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Asignación no encontrada'
            ], 404);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al eliminar asignación'
            ], 500);
        }
    }
}
