<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MetodoPagoController extends Controller
{
    #[OA\Get(
        path: "/metodos-pago",
        summary: "Listar métodos de pago",
        tags: ["Métodos de Pago"],
        responses: [
            new OA\Response(response: 200, description: "Lista")
        ]
    )]
    public function index()
    {
        return response()->json(MetodoPago::all());
    }

    #[OA\Post(
        path: "/metodos-pago",
        summary: "Crear método de pago",
        tags: ["Métodos de Pago"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nombre"],
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "estado", type: "boolean")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Creado")
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|unique:metodo_pago,nombre'
        ]);

        return response()->json(MetodoPago::create($data), 201);
    }

    #[OA\Get(
        path: "/metodos-pago/{id}",
        summary: "Obtener método de pago",
        tags: ["Métodos de Pago"],
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
        return response()->json(MetodoPago::findOrFail($id));
    }

    #[OA\Put(
        path: "/metodos-pago/{id}",
        summary: "Actualizar método de pago",
        tags: ["Métodos de Pago"],
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
        $metodo = MetodoPago::findOrFail($id);
        $metodo->update($request->all());

        return response()->json($metodo);
    }

    #[OA\Delete(
        path: "/metodos-pago/{id}",
        summary: "Eliminar método de pago",
        tags: ["Métodos de Pago"],
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
        MetodoPago::destroy($id);

        return response()->noContent();
    }
}
