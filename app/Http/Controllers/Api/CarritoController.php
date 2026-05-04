<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CarritoController extends Controller
{
    #[OA\Get(
        path: "/carrito",
        summary: "Listar carritos",
        tags: ["Carrito"],
        responses: [
            new OA\Response(response: 200, description: "Lista de carritos")
        ]
    )]
    public function index()
    {
        return response()->json(
            Carrito::with(['usuario','detalles'])->get()
        );
    }

    #[OA\Post(
        path: "/carrito",
        summary: "Crear carrito",
        tags: ["Carrito"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["usuario_id"],
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer"),
                    new OA\Property(property: "estado", type: "string", example: "activo")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Carrito creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario_id' => 'required|exists:usuario,id'
        ]);

        return response()->json(Carrito::create($data), 201);
    }

    #[OA\Get(
        path: "/carrito/{id}",
        summary: "Obtener carrito con detalle",
        tags: ["Carrito"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Carrito encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        return response()->json(
            Carrito::with(['usuario','detalles.producto'])->findOrFail($id)
        );
    }

    #[OA\Put(
        path: "/carrito/{id}",
        summary: "Actualizar carrito",
        tags: ["Carrito"],
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
        $carrito = Carrito::findOrFail($id);
        $carrito->update($request->all());

        return response()->json($carrito);
    }

    #[OA\Delete(
        path: "/carrito/{id}",
        summary: "Eliminar carrito",
        tags: ["Carrito"],
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
        Carrito::destroy($id);

        return response()->noContent();
    }
}
