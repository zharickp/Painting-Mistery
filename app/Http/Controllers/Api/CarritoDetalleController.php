<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarritoDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class CarritoDetalleController extends Controller
{
    #[OA\Get(
        path: "/carrito-detalle",
        summary: "Listar detalles del carrito",
        tags: ["Carrito Detalle"],
        responses: [
            new OA\Response(response: 200, description: "Lista"),
            new OA\Response(response: 500, description: "Error interno")
        ]
    )]
    public function index()
    {
        try {
            return response()->json(
                CarritoDetalle::with(['carrito','producto'])->get(),
                200
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener detalles del carrito'
            ], 500);
        }
    }

    #[OA\Post(
        path: "/carrito-detalle",
        summary: "Agregar producto al carrito",
        tags: ["Carrito Detalle"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["carrito_id","producto_id","cantidad"],
                properties: [
                    new OA\Property(property: "carrito_id", type: "integer"),
                    new OA\Property(property: "producto_id", type: "integer"),
                    new OA\Property(property: "cantidad", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Agregado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'carrito_id' => 'required|exists:carrito,id',
                'producto_id' => 'required|exists:producto,id',
                'cantidad' => 'required|integer|min:1'
            ]);

            $producto = Producto::findOrFail($data['producto_id']);

            $data['precio_unitario'] = $producto->precio;

            // 🔥 evitar duplicados
            $detalle = CarritoDetalle::where('carrito_id', $data['carrito_id'])
                ->where('producto_id', $data['producto_id'])
                ->first();

            if ($detalle) {
                $detalle->cantidad += $data['cantidad'];
                $detalle->save();

                return response()->json($detalle, 200);
            }

            return response()->json(CarritoDetalle::create($data), 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Get(
        path: "/carrito-detalle/{id}",
        summary: "Obtener detalle",
        tags: ["Carrito Detalle"],
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
            return response()->json(
                CarritoDetalle::with(['carrito','producto'])->findOrFail($id),
                200
            );

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/carrito-detalle/{id}",
        summary: "Actualizar cantidad",
        tags: ["Carrito Detalle"],
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
                    new OA\Property(property: "cantidad", type: "integer", example: 2)
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
            $detalle = CarritoDetalle::findOrFail($id);

            $data = $request->validate([
                'cantidad' => 'required|integer|min:1'
            ]);

            $detalle->update($data);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $detalle->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/carrito-detalle/{id}",
        summary: "Eliminar producto del carrito",
        tags: ["Carrito Detalle"],
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
            $detalle = CarritoDetalle::findOrFail($id);
            $detalle->delete();

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }
}
