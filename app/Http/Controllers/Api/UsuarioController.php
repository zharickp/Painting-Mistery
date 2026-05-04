<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UsuarioController extends Controller
{
    #[OA\Get(
        path: "/usuarios",
        summary: "Listar usuarios",
        tags: ["Usuarios"],
        responses: [
            new OA\Response(response: 200, description: "Lista de usuarios")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(Usuario::all(), 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener usuarios'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/usuarios",
        summary: "Crear usuario",
        tags: ["Usuarios"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: [
                    "tipo_documento_id",
                    "numero_documento",
                    "primer_nombre",
                    "primer_apellido",
                    "correo",
                    "password"
                ],
                properties: [
                    new OA\Property(property: "tipo_documento_id", type: "integer", example: 1),
                    new OA\Property(property: "numero_documento", type: "string", example: "123456789"),
                    new OA\Property(property: "primer_nombre", type: "string", example: "Juan"),
                    new OA\Property(property: "segundo_nombre", type: "string", example: "Carlos"),
                    new OA\Property(property: "primer_apellido", type: "string", example: "Pérez"),
                    new OA\Property(property: "segundo_apellido", type: "string", example: "Gómez"),
                    new OA\Property(property: "correo", type: "string", example: "juan@mail.com"),
                    new OA\Property(property: "password", type: "string", example: "123456"),
                    new OA\Property(property: "telefono", type: "string", example: "3001234567")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Usuario creado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'tipo_documento_id' => 'required|exists:tipo_documento,id',
                'numero_documento' => 'required|unique:usuario,numero_documento',
                'primer_nombre' => 'required|string|max:50',
                'segundo_nombre' => 'nullable|string|max:50',
                'primer_apellido' => 'required|string|max:50',
                'segundo_apellido' => 'nullable|string|max:50',
                'correo' => 'required|email|unique:usuario,correo',
                'password' => 'required|string|min:6',
                'telefono' => 'nullable|string|max:20'
            ]);

            $data['password'] = Hash::make($data['password']);

            $usuario = Usuario::create($data);

            return response()->json($usuario, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear usuario'
            ], 500);
        }
    }

    #[OA\Get(
        path: "/usuarios/{id}",
        summary: "Obtener usuario",
        tags: ["Usuarios"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Usuario encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        try {
            $usuario = Usuario::with('tipoDocumento')->findOrFail($id);

            return response()->json($usuario, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

#[OA\Put(
    path: "/usuarios/{id}",
    summary: "Actualizar usuario",
    tags: ["Usuarios"],
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
                new OA\Property(property: "tipo_documento_id", type: "integer", example: 1),
                new OA\Property(property: "numero_documento", type: "string", example: "123456"),
                new OA\Property(property: "primer_nombre", type: "string", example: "Juan"),
                new OA\Property(property: "segundo_nombre", type: "string", example: "Carlos"),
                new OA\Property(property: "primer_apellido", type: "string", example: "Pérez"),
                new OA\Property(property: "segundo_apellido", type: "string", example: "Gómez"),
                new OA\Property(property: "correo", type: "string", example: "juan@mail.com"),
                new OA\Property(property: "password", type: "string", example: "123456"),
                new OA\Property(property: "telefono", type: "string", example: "3001234567")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Actualizado correctamente"),
        new OA\Response(response: 404, description: "Usuario no encontrado"),
        new OA\Response(response: 422, description: "Error de validación"),
        new OA\Response(response: 400, description: "Sin datos")
    ]
)]
public function update(Request $request, $id)
{
    try {
        $usuario = Usuario::findOrFail($id);

        $data = $request->only([
            'tipo_documento_id',
            'numero_documento',
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'correo',
            'password',
            'telefono'
        ]);

        if (empty(array_filter($data))) {
            return response()->json([
                'message' => 'No se enviaron datos para actualizar'
            ], 400);
        }

        $validated = $request->validate([
            'tipo_documento_id' => 'sometimes|exists:tipo_documento,id',
            'numero_documento' => 'sometimes|unique:usuario,numero_documento,' . $id,
            'primer_nombre' => 'sometimes|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'sometimes|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'correo' => 'sometimes|email|unique:usuario,correo,' . $id,
            'password' => 'sometimes|string|min:6',
            'telefono' => 'nullable|string|max:20'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $usuario->update($validated);

        return response()->json([
            'message' => 'Actualizado correctamente',
            'data' => $usuario->fresh()
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Usuario no encontrado'
        ], 404);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar usuario'
        ], 500);
    }
}

    #[OA\Delete(
        path: "/usuarios/{id}",
        summary: "Eliminar usuario",
        tags: ["Usuarios"],
        responses: [
            new OA\Response(response: 200, description: "Eliminado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar usuario'
            ], 500);
        }
    }
}
