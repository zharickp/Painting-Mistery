<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class InscripcionController extends Controller
{
    #[OA\Get(
        path: "/inscripciones",
        summary: "Listar inscripciones",
        tags: ["Inscripciones"],
        responses: [
            new OA\Response(response: 200, description: "Lista de inscripciones")
        ]
    )]
    public function index()
    {
        return response()->json(
            Inscripcion::with(['usuario','curso'])->get()
        );
    }

    #[OA\Post(
        path: "/inscripciones",
        summary: "Crear inscripción",
        tags: ["Inscripciones"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["usuario_id","curso_id"],
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer"),
                    new OA\Property(property: "curso_id", type: "integer"),
                    new OA\Property(property: "estado", type: "string", example: "inscrito")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Inscripción creada")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'curso_id' => 'required|exists:curso,id'
        ]);

        return response()->json(Inscripcion::create($data), 201);
    }

    #[OA\Get(
        path: "/inscripciones/{id}",
        summary: "Obtener inscripción",
        tags: ["Inscripciones"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Encontrada"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function show($id)
    {
        return response()->json(
            Inscripcion::with(['usuario','curso'])->findOrFail($id)
        );
    }

    #[OA\Put(
        path: "/inscripciones/{id}",
        summary: "Actualizar inscripción",
        tags: ["Inscripciones"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Actualizada")
        ]
    )]
    public function update(Request $request, $id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $inscripcion->update($request->all());

        return response()->json($inscripcion);
    }

    #[OA\Delete(
        path: "/inscripciones/{id}",
        summary: "Eliminar inscripción",
        tags: ["Inscripciones"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Eliminada")
        ]
    )]
    public function destroy($id)
    {
        Inscripcion::destroy($id);

        return response()->noContent();
    }
}
