<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CursoController extends Controller
{
    #[OA\Get(
        path: "/cursos",
        summary: "Listar cursos",
        tags: ["Cursos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de cursos")
        ]
    )]
    public function index()
    {
        return response()->json(Curso::all());
    }

    #[OA\Post(
        path: "/cursos",
        summary: "Crear curso",
        tags: ["Cursos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre","costo"],
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "costo", type: "number"),
                    new OA\Property(property: "fecha_inicio", type: "string", format: "date"),
                    new OA\Property(property: "fecha_fin", type: "string", format: "date"),
                    new OA\Property(property: "cupos", type: "integer"),
                    new OA\Property(property: "estado", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Curso creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required',
            'costo' => 'required|numeric'
        ]);

        return response()->json(Curso::create($data), 201);
    }

    #[OA\Get(
        path: "/cursos/{id}",
        summary: "Obtener curso",
        tags: ["Cursos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Curso encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        return response()->json(Curso::findOrFail($id));
    }

    #[OA\Put(
        path: "/cursos/{id}",
        summary: "Actualizar curso",
        tags: ["Cursos"],
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
        $curso = Curso::findOrFail($id);
        $curso->update($request->all());

        return response()->json($curso);
    }

    #[OA\Delete(
        path: "/cursos/{id}",
        summary: "Eliminar curso",
        tags: ["Cursos"],
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
        Curso::destroy($id);

        return response()->noContent();
    }
}
