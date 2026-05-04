<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\Request;
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
        return response()->json(Rol::all());
    }

    #[OA\Post(
        path: "/roles",
        summary: "Crear rol",
        tags: ["Roles"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Rol creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required'
        ]);

        return response()->json(Rol::create($data), 201);
    }

    #[OA\Get(
        path: "/roles/{id}",
        summary: "Obtener rol con permisos",
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
        return response()->json(
            Rol::with('permisos')->findOrFail($id)
        );
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
        responses: [
            new OA\Response(response: 200, description: "Actualizado")
        ]
    )]
    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->all());

        return response()->json($rol);
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
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Eliminado")
        ]
    )]
    public function destroy($id)
    {
        Rol::destroy($id);

        return response()->noContent();
    }
}
