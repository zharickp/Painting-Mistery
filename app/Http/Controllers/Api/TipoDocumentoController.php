<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class TipoDocumentoController extends Controller
{
    // LISTAR
    #[OA\Get(
        path: "/tipo-documento",
        summary: "Listar tipos de documento",
        tags: ["TipoDocumento"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de tipos de documento"
            )
        ]
    )]
    public function index()
    {
        try {
            $data = TipoDocumento::all();
            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener datos'
            ], 500);
        }
    }

    // CREAR
    #[OA\Post(
        path: "/tipo-documento",
        summary: "Crear tipo de documento",
        tags: ["TipoDocumento"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre", "abreviatura"],
                properties: [
                    new OA\Property(property: "nombre", type: "string", example: "Cédula de ciudadanía"),
                    new OA\Property(property: "abreviatura", type: "string", example: "CC")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Tipo de documento creado"
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación"
            )
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:40',
                'abreviatura' => 'required|string|max:10|unique:tipo_documento,abreviatura',
            ]);

            $tipo = TipoDocumento::create($data);

            return response()->json($tipo, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear tipo de documento'
            ], 500);
        }
    }

    // MOSTRAR
    #[OA\Get(
        path: "/tipo-documento/{id}",
        summary: "Obtener tipo de documento",
        tags: ["TipoDocumento"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
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
            $tipo = TipoDocumento::findOrFail($id);
            return response()->json($tipo, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tipo de documento no encontrado'
            ], 404);
        }
    }

   //ACTUALIZAR
   #[OA\Put(
    path: "/tipo-documento/{id}",
    summary: "Actualizar tipo de documento",
    tags: ["TipoDocumento"],
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
                new OA\Property(property: "nombre", type: "string", example: "Cédula de ciudadanía"),
                new OA\Property(property: "abreviatura", type: "string", example: "CC")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Actualizado correctamente"),
        new OA\Response(response: 400, description: "Sin datos para actualizar"),
        new OA\Response(response: 404, description: "No encontrado"),
        new OA\Response(response: 422, description: "Error de validación")
    ]
)]
public function update(Request $request, $id)
{
    try {
        $tipo = TipoDocumento::findOrFail($id);

        $data = $request->only(['nombre', 'abreviatura']);

        if (empty(array_filter($data))) {
            return response()->json([
                'message' => 'No se enviaron datos para actualizar'
            ], 400);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:40',
            'abreviatura' => 'sometimes|string|max:10|unique:tipo_documento,abreviatura,' . $id,
        ]);

        $tipo->update($validated);

        return response()->json([
            'message' => 'Actualizado correctamente',
            'data' => $tipo->fresh()
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Tipo de documento no encontrado'
        ], 404);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar'
        ], 500);
    }
}

    // ELIMINAR
    #[OA\Delete(
        path: "/tipo-documento/{id}",
        summary: "Eliminar tipo de documento",
        tags: ["TipoDocumento"],
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
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 409, description: "Conflicto")
        ]
    )]
    public function destroy($id)
    {
        try {
            $tipo = TipoDocumento::findOrFail($id);

            if ($tipo->usuarios()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar, tiene usuarios asociados'
                ], 409);
            }

            $tipo->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tipo de documento no encontrado'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar'
            ], 500);
        }
    }
}
