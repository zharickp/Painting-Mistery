<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class ProductoController extends Controller
{
    #[OA\Get(
        path: "/productos",
        summary: "Listar productos",
        tags: ["Productos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de productos"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(
                Producto::with(['categoria', 'tipoIva'])->get(),
                200
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener productos'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/productos",
        summary: "Crear producto",
        tags: ["Productos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["categoria_producto_id","tipo_iva_id","nombre","precio"],
                properties: [
                    new OA\Property(property: "categoria_producto_id", type: "integer"),
                    new OA\Property(property: "tipo_iva_id", type: "integer"),
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "precio", type: "number"),
                    new OA\Property(property: "imagen", type: "string"),
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
                'categoria_producto_id' => 'required|exists:categoria_producto,id',
                'tipo_iva_id' => 'required|exists:tipo_iva,id',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'imagen' => 'nullable|string',
                'estado' => 'nullable|boolean'
            ]);

            $producto = Producto::create($data);

            return response()->json($producto, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Get(
        path: "/productos/{id}",
        summary: "Obtener producto",
        tags: ["Productos"],
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
            $producto = Producto::with(['categoria','tipoIva'])->findOrFail($id);

            return response()->json($producto, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/productos/{id}",
        summary: "Actualizar producto",
        tags: ["Productos"],
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
                    new OA\Property(property: "precio", type: "number"),
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
            $producto = Producto::findOrFail($id);

            $data = $request->only([
                'nombre',
                'descripcion',
                'precio',
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
                'precio' => 'sometimes|numeric|min:0',
                'estado' => 'nullable|boolean'
            ]);

            $producto->update($validated);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $producto->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/productos/{id}",
        summary: "Eliminar producto",
        tags: ["Productos"],
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
            $producto = Producto::findOrFail($id);
            $producto->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }
}
