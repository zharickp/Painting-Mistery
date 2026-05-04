<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVentaCurso;
use App\Models\Curso;
use App\Models\Venta;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DetalleVentaCursoController extends Controller
{
    #[OA\Get(
        path: "/detalle-venta-curso",
        summary: "Listar cursos vendidos",
        tags: ["Detalle Venta Curso"],
        responses: [
            new OA\Response(response: 200, description: "Lista")
        ]
    )]
    public function index()
    {
        return response()->json(
            DetalleVentaCurso::with(['venta','curso'])->get()
        );
    }

    #[OA\Post(
        path: "/detalle-venta-curso",
        summary: "Agregar curso a una venta",
        tags: ["Detalle Venta Curso"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["venta_id","curso_id"],
                properties: [
                    new OA\Property(property: "venta_id", type: "integer"),
                    new OA\Property(property: "curso_id", type: "integer")
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
            'curso_id' => 'required|exists:curso,id'
        ]);

        $curso = Curso::findOrFail($data['curso_id']);

        // 🔥 lógica financiera (más simple que producto)
        $precio = $curso->costo;

        $data['precio_unitario'] = $precio;
        $data['subtotal'] = $precio;

        $detalle = DetalleVentaCurso::create($data);

        // 🔥 actualizar total de la venta
        $venta = Venta::findOrFail($data['venta_id']);
        $venta->total += $precio;
        $venta->save();

        return response()->json($detalle, 201);
    }

    #[OA\Get(
        path: "/detalle-venta-curso/{id}",
        summary: "Obtener detalle",
        tags: ["Detalle Venta Curso"],
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
            DetalleVentaCurso::with(['venta','curso'])->findOrFail($id)
        );
    }

    #[OA\Put(
        path: "/detalle-venta-curso/{id}",
        summary: "Actualizar detalle",
        tags: ["Detalle Venta Curso"],
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
        $detalle = DetalleVentaCurso::findOrFail($id);
        $detalle->update($request->all());

        return response()->json($detalle);
    }

    #[OA\Delete(
        path: "/detalle-venta-curso/{id}",
        summary: "Eliminar detalle",
        tags: ["Detalle Venta Curso"],
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
        DetalleVentaCurso::destroy($id);

        return response()->noContent();
    }
}
