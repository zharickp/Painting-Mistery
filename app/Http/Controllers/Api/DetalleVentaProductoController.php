<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVentaProducto;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
}
