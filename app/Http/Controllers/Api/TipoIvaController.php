<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoIva;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class TipoIvaController extends Controller
{
    #[OA\Get(
        path: "/tipo-iva",
        summary: "Listar tipos de IVA",
        tags: ["Tipo IVA"],
        responses: [
            new OA\Response(response: 200, description: "Lista de tipos de IVA"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(TipoIva::all(), 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener tipos de IVA'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/tipo-iva",
        summary: "Crear tipo de IVA",
        tags: ["Tipo IVA"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["descripcion","porcentaje"],
                properties: [
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "porcentaje", type: "number", example: 19)
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
                'descripcion' => 'required|string|max:100',
                'porcentaje' => 'required|numeric|min:0'
            ]);

            $iva = TipoIva::create($data);

            return response()->json($iva, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Get(
        path: "/tipo-iva/{id}",
        summary: "Obtener tipo de IVA",
        tags: ["Tipo IVA"],
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
            return response()->json(TipoIva::findOrFail($id), 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tipo de IVA no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/tipo-iva/{id}",
        summary: "Actualizar tipo de IVA",
        tags: ["Tipo IVA"],
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
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "porcentaje", type: "number")
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
            $iva = TipoIva::findOrFail($id);

            $data = $request->only(['descripcion', 'porcentaje']);

            if (empty(array_filter($data))) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar'
                ], 400);
            }

            $validated = $request->validate([
                'descripcion' => 'sometimes|string|max:100',
                'porcentaje' => 'sometimes|numeric|min:0'
            ]);

            $iva->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $iva->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tipo de IVA no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/tipo-iva/{id}",
        summary: "Eliminar tipo de IVA",
        tags: ["Tipo IVA"],
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
            $iva = TipoIva::findOrFail($id);
            $iva->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Tipo de IVA no encontrado'
            ], 404);
        }
    }
}
