<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class VentaController extends Controller
{
    #[OA\Get(
        path: "/ventas",
        summary: "Listar ventas",
        tags: ["Ventas"],
        responses: [
            new OA\Response(response: 200, description: "Lista de ventas")
        ]
    )]
    public function index()
    {
        return response()->json(
            Venta::with(['usuario','detalleProductos','detalleCursos','pagos'])->get(),
            200
        );
    }

    #[OA\Post(
        path: "/ventas",
        summary: "Crear venta",
        tags: ["Ventas"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["usuario_id"],
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer"),
                    new OA\Property(property: "total", type: "number"),
                    new OA\Property(property: "estado", type: "string", example: "pendiente")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Venta creada"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'usuario_id' => 'required|exists:usuario,id',
                'total' => 'nullable|numeric|min:0',
                'estado' => 'nullable|in:pendiente,pagada,cancelada'
            ]);

            $data['total'] = $data['total'] ?? 0;
            $data['estado'] = $data['estado'] ?? 'pendiente';
            $data['fecha'] = now();

            $venta = Venta::create($data);

            return response()->json($venta, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear venta'
            ], 500);
        }
    }

    #[OA\Get(
        path: "/ventas/{id}",
        summary: "Obtener venta",
        tags: ["Ventas"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Venta encontrada"),
            new OA\Response(response: 404, description: "No encontrada")
        ]
    )]
    public function show($id)
    {
        try {
            $venta = Venta::with([
                'usuario',
                'detalleProductos.producto',
                'detalleCursos.curso',
                'pagos'
            ])->findOrFail($id);

            return response()->json($venta, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/ventas/{id}",
        summary: "Actualizar venta",
        tags: ["Ventas"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "estado", type: "string", example: "pagada"),
                    new OA\Property(property: "total", type: "number", example: 50000)
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
            $venta = Venta::findOrFail($id);

            $data = $request->validate([
                'estado' => 'sometimes|in:pendiente,pagada,cancelada',
                'total' => 'sometimes|numeric|min:0'
            ]);

            if (empty($data)) {
                return response()->json([
                    'message' => 'No hay datos para actualizar'
                ], 400);
            }

            $venta->update($data);

            return response()->json([
                'message' => 'Actualizada correctamente',
                'data' => $venta->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar venta'
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/ventas/{id}",
        summary: "Eliminar venta",
        tags: ["Ventas"],
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
            $venta = Venta::findOrFail($id);
            $venta->delete();

            return response()->json([
                'message' => 'Eliminada correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar venta'
            ], 500);
        }
    }
}
