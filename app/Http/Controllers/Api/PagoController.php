<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class PagoController extends Controller
{
    #[OA\Get(
        path: "/pagos",
        summary: "Listar pagos",
        tags: ["Pagos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de pagos")
        ]
    )]
    public function index()
    {
        return response()->json(
            Pago::with(['venta', 'metodoPago'])->get(),
            200
        );
    }

    #[OA\Post(
        path: "/pagos",
        summary: "Registrar pago",
        tags: ["Pagos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["venta_id", "metodo_pago_id", "valor"],
                properties: [
                    new OA\Property(property: "venta_id", type: "integer"),
                    new OA\Property(property: "metodo_pago_id", type: "integer"),
                    new OA\Property(property: "numero_comprobante", type: "string"),
                    new OA\Property(property: "valor", type: "number"),
                    new OA\Property(property: "estado", type: "string", example: "aprobado")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Pago registrado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'venta_id' => 'required|exists:venta,id',
                'metodo_pago_id' => 'required|exists:metodo_pago,id',
                'numero_comprobante' => 'nullable|string',
                'valor' => 'required|numeric|min:0',
                'estado' => 'nullable|string'
            ]);

            $data['fecha_pago'] = now();
            $data['estado'] = $data['estado'] ?? 'aprobado';

            $pago = Pago::create($data);

            $this->actualizarEstadoVenta($data['venta_id']);

            return response()->json($pago, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Get(
        path: "/pagos/{id}",
        summary: "Obtener pago",
        tags: ["Pagos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Pago encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        try {
            return response()->json(
                Pago::with(['venta', 'metodoPago'])->findOrFail($id),
                200
            );
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/pagos/{id}",
        summary: "Actualizar pago",
        tags: ["Pagos"],
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
                    new OA\Property(property: "venta_id", type: "integer"),
                    new OA\Property(property: "metodo_pago_id", type: "integer"),
                    new OA\Property(property: "numero_comprobante", type: "string"),
                    new OA\Property(property: "valor", type: "number"),
                    new OA\Property(property: "estado", type: "string", example: "aprobado")
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
            $pago = Pago::findOrFail($id);

            $data = $request->validate([
                'venta_id' => 'sometimes|exists:venta,id',
                'metodo_pago_id' => 'sometimes|exists:metodo_pago,id',
                'numero_comprobante' => 'nullable|string',
                'valor' => 'sometimes|numeric|min:0',
                'estado' => 'nullable|string'
            ]);

            $pago->update($data);

            $this->actualizarEstadoVenta($pago->venta_id);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $pago->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    #[OA\Delete(
        path: "/pagos/{id}",
        summary: "Eliminar pago",
        tags: ["Pagos"],
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
            $pago = Pago::findOrFail($id);
            $ventaId = $pago->venta_id;

            $pago->delete();

            $this->actualizarEstadoVenta($ventaId);

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }
    }

    private function actualizarEstadoVenta($ventaId)
    {
        $venta = Venta::find($ventaId);

        if (!$venta) return;

        $totalPagado = $venta->pagos()->sum('valor');

        if ($totalPagado >= $venta->total) {
            $venta->estado = 'pagada';
        } else {
            $venta->estado = 'pendiente';
        }

        $venta->save();
    }
}
