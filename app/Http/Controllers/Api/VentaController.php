<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
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
            Venta::with(['usuario','detalleProductos','detalleCursos','pagos'])->get()
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
            new OA\Response(response: 201, description: "Venta creada")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:usuario,id'
        ]);

        // 🔥 se puede calcular después con detalles
        $data['total'] = 0;

        return response()->json(Venta::create($data), 201);
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
        return response()->json(
            Venta::with(['usuario','detalleProductos.producto','detalleCursos.curso','pagos'])
                ->findOrFail($id)
        );
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
        responses: [
            new OA\Response(response: 200, description: "Actualizada")
        ]
    )]
    public function update(Request $request, $id)
    {
        $venta = Venta::findOrFail($id);
        $venta->update($request->all());

        return response()->json($venta);
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
            new OA\Response(response: 204, description: "Eliminada")
        ]
    )]
    public function destroy($id)
    {
        Venta::destroy($id);

        return response()->noContent();
    }
}
