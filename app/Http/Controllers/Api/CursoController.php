<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class CursoController extends Controller
{
    #[OA\Get(
        path: "/cursos",
        summary: "Listar cursos",
        tags: ["Cursos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de cursos"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(Curso::all(), 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener cursos'
            ], 500);
        }
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
            new OA\Response(response: 201, description: "Creado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'costo' => 'required|numeric|min:0',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'cupos' => 'nullable|integer|min:0',
                'estado' => 'nullable|boolean'
            ]);

            $curso = Curso::create($data);

            return response()->json($curso, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
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
            return response()->json(Curso::findOrFail($id), 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Curso no encontrado'
            ], 404);
        }
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
                schema: new OA\Schema(type: "integer"),
                example: 1
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
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
            new OA\Response(response: 200, description: "Actualizado"),
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $curso = Curso::findOrFail($id);

            $data = $request->only([
                'nombre',
                'descripcion',
                'costo',
                'fecha_inicio',
                'fecha_fin',
                'cupos',
                'estado'
            ]);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:100',
                'descripcion' => 'nullable|string',
                'costo' => 'sometimes|numeric|min:0',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'cupos' => 'nullable|integer|min:0',
                'estado' => 'nullable|boolean'
            ]);

            $curso->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $curso->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Curso no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
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
            $curso = Curso::findOrFail($id);
            $curso->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Curso no encontrado'
            ], 404);
        }
    }
}
