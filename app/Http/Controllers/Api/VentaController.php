<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
}
