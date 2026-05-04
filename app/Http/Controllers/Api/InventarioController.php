<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class InventarioController extends Controller
{
    #[OA\Get(
        path: "/inventario",
        summary: "Listar inventario",
        tags: ["Inventario"],
        responses: [
            new OA\Response(response: 200, description: "Lista de inventario")
        ]
    )]
    public function index()
    {
        return response()->json(
            Inventario::with('producto')->get()
        );
    }

    #[OA\Post(
        path: "/inventario",
        summary: "Crear registro de inventario",
        tags: ["Inventario"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["producto_id","stock_actual"],
                properties: [
                    new OA\Property(property: "producto_id", type: "integer"),
                    new OA\Property(property: "stock_actual", type: "integer"),
                    new OA\Property(property: "stock_minimo", type: "integer"),
                    new OA\Property(property: "ultima_actualizacion", type: "string", format: "date-time")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Inventario creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:producto,id',
            'stock_actual' => 'required|integer'
        ]);

        $data['ultima_actualizacion'] = now();

        return response()->json(Inventario::create($data), 201);
    }

    #[OA\Get(
        path: "/inventario/{id}",
        summary: "Obtener inventario",
        tags: ["Inventario"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Inventario encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        return response()->json(
            Inventario::with('producto')->findOrFail($id)
        );
    }

    #[OA\Put(
        path: "/inventario/{id}",
        summary: "Actualizar inventario",
        tags: ["Inventario"],
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
        $inventario = Inventario::findOrFail($id);

        $data = $request->all();
        $data['ultima_actualizacion'] = now();

        $inventario->update($data);

        return response()->json($inventario);
    }

    #[OA\Delete(
        path: "/inventario/{id}",
        summary: "Eliminar inventario",
        tags: ["Inventario"],
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
        Inventario::destroy($id);

        return response()->noContent();
    }
}
