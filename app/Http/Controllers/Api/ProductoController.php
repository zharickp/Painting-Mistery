<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProductoController extends Controller
{
    #[OA\Get(
        path: "/productos",
        summary: "Listar productos",
        tags: ["Productos"],
        responses: [
            new OA\Response(response: 200, description: "Lista de productos")
        ]
    )]
    public function index()
    {
        return response()->json(Producto::all());
    }

    #[OA\Post(
        path: "/productos",
        summary: "Crear producto",
        tags: ["Productos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["categoria_producto_id","tipo_iva_id","nombre","precio"],
                properties: [
                    new OA\Property(property: "categoria_producto_id", type: "integer"),
                    new OA\Property(property: "tipo_iva_id", type: "integer"),
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "precio", type: "number"),
                    new OA\Property(property: "imagen", type: "string"),
                    new OA\Property(property: "estado", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Producto creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_producto_id' => 'required|exists:categoria_producto,id',
            'tipo_iva_id' => 'required|exists:tipo_iva,id',
            'nombre' => 'required',
            'precio' => 'required|numeric'
        ]);

        return response()->json(Producto::create($data), 201);
    }

    #[OA\Get(
        path: "/productos/{id}",
        summary: "Obtener producto con categoría e IVA",
        tags: ["Productos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Producto encontrado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        return response()->json(
            Producto::with(['categoria','tipoIva'])->findOrFail($id)
        );
    }

    #[OA\Put(
        path: "/productos/{id}",
        summary: "Actualizar producto",
        tags: ["Productos"],
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
        $producto = Producto::findOrFail($id);
        $producto->update($request->all());

        return response()->json($producto);
    }

    #[OA\Delete(
        path: "/productos/{id}",
        summary: "Eliminar producto",
        tags: ["Productos"],
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
        Producto::destroy($id);

        return response()->noContent();
    }
}
