<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
}
