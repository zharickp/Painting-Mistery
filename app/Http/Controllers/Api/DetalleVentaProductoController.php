<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVentaProducto;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class DetalleVentaProductoController extends Controller
{
    #[OA\Get(
        path: "/detalle-venta-producto",
        summary: "Listar detalles de productos vendidos",
        tags: ["Detalle Venta Producto"],
        responses: [
            new OA\Response(response: 200, description: "Lista")
        ]
    )]
    public function index()
    {
        return response()->json(
            DetalleVentaProducto::with(['venta', 'producto'])->get(),
            200
        );
    }

    #[OA\Post(
        path: "/detalle-venta-producto",
        summary: "Agregar producto a una venta",
        tags: ["Detalle Venta Producto"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["venta_id", "producto_id", "cantidad"],
                properties: [
                    new OA\Property(property: "venta_id", type: "integer"),
                    new OA\Property(property: "producto_id", type: "integer"),
                    new OA\Property(property: "cantidad", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Agregado"),
            new OA\Response(response: 422, description: "Error de validación"),
            new OA\Response(response: 400, description: "Error de negocio")
        ]
    )]
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'venta_id' => 'required|exists:venta,id',
                'producto_id' => 'required|exists:producto,id',
                'cantidad' => 'required|integer|min:1'
            ]);

            $producto = Producto::with('tipoIva')
                ->findOrFail($data['producto_id']);

            if (!$producto->tipoIva) {
                return response()->json([
                    'message' => 'El producto no tiene IVA asignado'
                ], 400);
            }

            $precio = $producto->precio;
            $subtotal = $precio * $data['cantidad'];
            $iva = ($subtotal * $producto->tipoIva->porcentaje) / 100;

            $data['precio_unitario'] = $precio;
            $data['subtotal'] = $subtotal;
            $data['iva'] = $iva;

            $detalle = DetalleVentaProducto::create($data);

            $this->recalcularVenta($data['venta_id']);

            return response()->json($detalle, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Producto o venta no encontrada'
            ], 404);
        }
    }

    #[OA\Get(
        path: "/detalle-venta-producto/{id}",
        summary: "Obtener detalle",
        tags: ["Detalle Venta Producto"],
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
            return response()->json(
                DetalleVentaProducto::with(['venta', 'producto'])
                    ->findOrFail($id),
                200
            );

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }

    #[OA\Put(
        path: "/detalle-venta-producto/{id}",
        summary: "Actualizar detalle",
        tags: ["Detalle Venta Producto"],
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
                    new OA\Property(property: "cantidad", type: "integer")
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

            $detalle = DetalleVentaProducto::findOrFail($id);

            $data = $request->validate([
                'cantidad' => 'sometimes|integer|min:1'
            ]);

            if (isset($data['cantidad'])) {

                $producto = Producto::with('tipoIva')
                    ->findOrFail($detalle->producto_id);

                if (!$producto->tipoIva) {
                    return response()->json([
                        'message' => 'El producto no tiene IVA asignado'
                    ], 400);
                }

                $precio = $producto->precio;
                $subtotal = $precio * $data['cantidad'];
                $iva = ($subtotal * $producto->tipoIva->porcentaje) / 100;

                $data['precio_unitario'] = $precio;
                $data['subtotal'] = $subtotal;
                $data['iva'] = $iva;
            }

            $detalle->update($data);

            $this->recalcularVenta($detalle->venta_id);

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
        path: "/detalle-venta-producto/{id}",
        summary: "Eliminar detalle",
        tags: ["Detalle Venta Producto"],
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

            $detalle = DetalleVentaProducto::findOrFail($id);

            $ventaId = $detalle->venta_id;

            $detalle->delete();

            $this->recalcularVenta($ventaId);

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }

    private function recalcularVenta($ventaId)
    {
        $venta = Venta::find($ventaId);

        if (!$venta) return;

        $venta->total = DetalleVentaProducto::where('venta_id', $ventaId)
            ->sum(DB::raw('subtotal + iva'));

        $venta->save();
    }
}
