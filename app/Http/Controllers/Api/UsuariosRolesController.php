<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsuariosRoles;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class UsuariosRolesController extends Controller
{
    #[OA\Get(
        path: "/usuarios-roles",
        summary: "Listar asignaciones usuario-rol",
        tags: ["Usuarios Roles"],
        responses: [
            new OA\Response(response: 200, description: "Lista obtenida")
        ]
    )]
    public function index()
    {
        try {

            return response()->json(
                UsuariosRoles::with(['usuario', 'rol'])->get(),
                200
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al obtener asignaciones'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/usuarios-roles",
        summary: "Asignar rol a usuario",
        tags: ["Usuarios Roles"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["usuario_id", "rol_id"],
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer", example: 1),
                    new OA\Property(property: "rol_id", type: "integer", example: 1)
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
                'usuario_id' => 'required|exists:usuario,id',
                'rol_id' => 'required|exists:rol,id'
            ]);

            $existe = UsuariosRoles::where('usuario_id', $data['usuario_id'])
                ->where('rol_id', $data['rol_id'])
                ->exists();

            if ($existe) {

                return response()->json([
                    'message' => 'El usuario ya tiene ese rol'
                ], 409);
            }

            $asignacion = UsuariosRoles::create($data);

            return response()->json($asignacion, 201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al asignar rol'
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/usuarios-roles/{id}",
        summary: "Eliminar asignación usuario-rol",
        tags: ["Usuarios Roles"],
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

            $asignacion = UsuariosRoles::findOrFail($id);

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
