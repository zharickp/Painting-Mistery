<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
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
            Inscripcion::with(['usuario', 'curso'])->get(),
            200
        );
    }

    #[OA\Post(
        path: "/inscripciones",
        summary: "Crear inscripción",
        tags: ["Inscripciones"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["usuario_id", "curso_id"],
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer"),
                    new OA\Property(property: "curso_id", type: "integer"),
                    new OA\Property(property: "estado", type: "string", example: "inscrito")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Creada"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'usuario_id' => 'required|exists:usuario,id',
                'curso_id' => 'required|exists:curso,id',
                'estado' => 'nullable|in:inscrito,cancelado'
            ]);

            $data['estado'] = $data['estado'] ?? 'inscrito';

            $inscripcion = Inscripcion::create($data);

            return response()->json($inscripcion, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear inscripción'
            ], 500);
        }
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
        try {
            $inscripcion = Inscripcion::with(['usuario', 'curso'])->findOrFail($id);

            return response()->json($inscripcion, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Inscripción no encontrada'
            ], 404);
        }
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "estado", type: "string", example: "cancelado")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Actualizada"),
            new OA\Response(response: 404, description: "No encontrada"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $inscripcion = Inscripcion::findOrFail($id);

            $data = $request->validate([
                'estado' => 'sometimes|in:inscrito,cancelado'
            ]);

            $inscripcion->update($data);

            return response()->json([
                'message' => 'Actualizada correctamente',
                'data' => $inscripcion->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Inscripción no encontrada'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar inscripción'
            ], 500);
        }
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
            new OA\Response(response: 200, description: "Eliminada"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function destroy($id)
    {
        try {
            $inscripcion = Inscripcion::findOrFail($id);
            $inscripcion->delete();

            return response()->json([
                'message' => 'Eliminada correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Inscripción no encontrada'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar inscripción'
            ], 500);
        }
    }
}
