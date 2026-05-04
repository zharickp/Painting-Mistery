<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
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
            Pago::with(['venta','metodoPago'])->get()
        );
    }

    #[OA\Post(
        path: "/pagos",
        summary: "Registrar pago",
        tags: ["Pagos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["venta_id","metodo_pago_id","valor"],
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
            new OA\Response(response: 201, description: "Pago registrado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'venta_id' => 'required|exists:venta,id',
            'metodo_pago_id' => 'required|exists:metodo_pago,id',
            'valor' => 'required|numeric|min:0'
        ]);

        $data['fecha_pago'] = now();

        $pago = Pago::create($data);

        // 🔥 lógica de negocio REAL
        $venta = Venta::findOrFail($data['venta_id']);

        // suma de pagos realizados
        $totalPagado = $venta->pagos()->sum('valor');

        if ($totalPagado >= $venta->total) {
            $venta->estado = 'pagada';
        }

        $venta->save();

        return response()->json($pago, 201);
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
        return response()->json(
            Pago::with(['venta','metodoPago'])->findOrFail($id)
        );
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
        responses: [
            new OA\Response(response: 200, description: "Actualizado")
        ]
    )]
    public function update(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);
        $pago->update($request->all());

        return response()->json($pago);
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
            new OA\Response(response: 204, description: "Eliminado")
        ]
    )]
    public function destroy($id)
    {
        Pago::destroy($id);

        return response()->noContent();
    }
}
