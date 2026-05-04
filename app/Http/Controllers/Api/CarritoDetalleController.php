<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarritoDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CarritoDetalleController extends Controller
{
    #[OA\Get(
        path: "/carrito-detalle",
        summary: "Listar detalles del carrito",
        tags: ["Carrito Detalle"],
        responses: [
            new OA\Response(response: 200, description: "Lista")
        ]
    )]
    public function index()
    {
        return response()->json(
            CarritoDetalle::with(['carrito','producto'])->get()
        );
    }

    #[OA\Post(
        path: "/carrito-detalle",
        summary: "Agregar producto al carrito",
        tags: ["Carrito Detalle"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["carrito_id","producto_id","cantidad"],
                properties: [
                    new OA\Property(property: "carrito_id", type: "integer"),
                    new OA\Property(property: "producto_id", type: "integer"),
                    new OA\Property(property: "cantidad", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Agregado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'carrito_id' => 'required|exists:carrito,id',
            'producto_id' => 'required|exists:producto,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Producto::findOrFail($data['producto_id']);

        // 🔥 lógica clave: tomar precio del producto
        $data['precio_unitario'] = $producto->precio;

        return response()->json(CarritoDetalle::create($data), 201);
    }

    #[OA\Get(
        path: "/carrito-detalle/{id}",
        summary: "Obtener detalle",
        tags: ["Carrito Detalle"],
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
        return response()->json(
            CarritoDetalle::with(['carrito','producto'])->findOrFail($id)
        );
    }

    #[OA\Put(
        path: "/carrito-detalle/{id}",
        summary: "Actualizar cantidad",
        tags: ["Carrito Detalle"],
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
        $detalle = CarritoDetalle::findOrFail($id);

        $data = $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $detalle->update($data);

        return response()->json($detalle);
    }

    #[OA\Delete(
        path: "/carrito-detalle/{id}",
        summary: "Eliminar producto del carrito",
        tags: ["Carrito Detalle"],
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
        CarritoDetalle::destroy($id);

        return response()->noContent();
    }
}
