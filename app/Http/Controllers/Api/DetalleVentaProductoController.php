<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVentaProducto;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
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
            DetalleVentaProducto::with(['venta','producto'])->get()
        );
    }

    #[OA\Post(
        path: "/detalle-venta-producto",
        summary: "Agregar producto a una venta",
        tags: ["Detalle Venta Producto"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["venta_id","producto_id","cantidad"],
                properties: [
                    new OA\Property(property: "venta_id", type: "integer"),
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
            'venta_id' => 'required|exists:venta,id',
            'producto_id' => 'required|exists:producto,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Producto::with('tipoIva')->findOrFail($data['producto_id']);

        // 🔥 lógica financiera
        $precio = $producto->precio;
        $subtotal = $precio * $data['cantidad'];
        $iva = ($subtotal * $producto->tipoIva->porcentaje) / 100;

        $data['precio_unitario'] = $precio;
        $data['subtotal'] = $subtotal;
        $data['iva'] = $iva;

        $detalle = DetalleVentaProducto::create($data);

        // 🔥 actualizar total de la venta
        $venta = Venta::findOrFail($data['venta_id']);
        $venta->total += ($subtotal + $iva);
        $venta->save();

        return response()->json($detalle, 201);
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
        return response()->json(
            DetalleVentaProducto::with(['venta','producto'])->findOrFail($id)
        );
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
        responses: [
            new OA\Response(response: 200, description: "Actualizado")
        ]
    )]
    public function update(Request $request, $id)
    {
        $detalle = DetalleVentaProducto::findOrFail($id);
        $detalle->update($request->all());

        return response()->json($detalle);
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
            new OA\Response(response: 204, description: "Eliminado")
        ]
    )]
    public function destroy($id)
    {
        DetalleVentaProducto::destroy($id);

        return response()->noContent();
    }
}
