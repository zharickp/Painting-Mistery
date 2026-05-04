<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PermisoController extends Controller
{
    #[OA\Get(
        path: "/permisos",
        summary: "Listar permisos",
        tags: ["Permisos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de permisos")
        ]
    )]
    public function index()
    {
        return response()->json(Permiso::all());
    }

    #[OA\Post(
        path: "/permisos",
        summary: "Crear permiso",
        tags: ["Permisos"],
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
            new OA\Response(response: 201, description: "Permiso creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required'
        ]);

        return response()->json(Permiso::create($data), 201);
    }

    #[OA\Get(
        path: "/permisos/{id}",
        summary: "Obtener permiso con roles",
        tags: ["Permisos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Permiso encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        return response()->json(
            Permiso::with('roles')->findOrFail($id)
        );
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
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Actualizado")
        ]
    )]
    public function update(Request $request, $id)
    {
        $permiso = Permiso::findOrFail($id);
        $permiso->update($request->all());

        return response()->json($permiso);
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
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Eliminado")
        ]
    )]
    public function destroy($id)
    {
        Permiso::destroy($id);

        return response()->noContent();
    }
}
