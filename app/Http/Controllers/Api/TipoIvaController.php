<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoIva;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TipoIvaController extends Controller
{
    #[OA\Get(
        path: "/tipo-iva",
        summary: "Listar tipos de IVA",
        tags: ["Tipo IVA"],
        responses: [
            new OA\Response(response: 200, description: "Lista de tipos de IVA")
        ]
    )]
    public function index()
    {
        return response()->json(TipoIva::all());
    }

    #[OA\Post(
        path: "/tipo-iva",
        summary: "Crear tipo de IVA",
        tags: ["Tipo IVA"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["descripcion","porcentaje"],
                properties: [
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "porcentaje", type: "number", example: 19.00)
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
            'descripcion' => 'required',
            'porcentaje' => 'required|numeric'
        ]);

        return response()->json(TipoIva::create($data), 201);
    }

    #[OA\Get(
        path: "/tipo-iva/{id}",
        summary: "Obtener tipo de IVA",
        tags: ["Tipo IVA"],
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
        return response()->json(TipoIva::findOrFail($id));
    }

    #[OA\Put(
        path: "/tipo-iva/{id}",
        summary: "Actualizar tipo de IVA",
        tags: ["Tipo IVA"],
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
        $iva = TipoIva::findOrFail($id);
        $iva->update($request->all());

        return response()->json($iva);
    }

    #[OA\Delete(
        path: "/tipo-iva/{id}",
        summary: "Eliminar tipo de IVA",
        tags: ["Tipo IVA"],
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
        TipoIva::destroy($id);

        return response()->noContent();
    }
}
