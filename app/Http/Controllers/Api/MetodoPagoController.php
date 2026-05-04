<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class MetodoPagoController extends Controller
{
    #[OA\Get(
        path: "/metodos-pago",
        summary: "Listar métodos de pago",
        tags: ["Métodos de Pago"],
        responses: [
            new OA\Response(response: 200, description: "Lista")
        ]
    )]
    public function index()
    {
        return response()->json(MetodoPago::all(), 200);
    }

    #[OA\Post(
        path: "/metodos-pago",
        summary: "Crear método de pago",
        tags: ["Métodos de Pago"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "estado", type: "boolean", example: true)
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
                'nombre' => 'required|unique:metodo_pago,nombre',
                'descripcion' => 'nullable|string',
                'estado' => 'nullable|boolean'
            ]);

            $metodo = MetodoPago::create($data);

            return response()->json($metodo, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Get(
        path: "/metodos-pago/{id}",
        summary: "Obtener método de pago por ID",
        tags: ["Métodos de Pago"],
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
            $metodo = MetodoPago::findOrFail($id);

            return response()->json($metodo, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Método de pago no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/metodos-pago/{id}",
        summary: "Actualizar método de pago",
        tags: ["Métodos de Pago"],
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
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
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
            $metodo = MetodoPago::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'sometimes|unique:metodo_pago,nombre,' . $id,
                'descripcion' => 'nullable|string',
                'estado' => 'nullable|boolean'
            ]);

            $metodo->update($data);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $metodo->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Método de pago no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/metodos-pago/{id}",
        summary: "Eliminar método de pago",
        tags: ["Métodos de Pago"],
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
            $metodo = MetodoPago::findOrFail($id);
            $metodo->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Método de pago no encontrado'
            ], 404);
        }
    }
}
