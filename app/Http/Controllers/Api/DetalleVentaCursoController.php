<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVentaCurso;
use App\Models\Curso;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
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
            DetalleVentaCurso::with(['venta', 'curso'])->get(),
            200
        );
    }

    #[OA\Post(
        path: "/detalle-venta-curso",
        summary: "Agregar curso a una venta",
        tags: ["Detalle Venta Curso"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["venta_id", "curso_id"],
                properties: [
                    new OA\Property(property: "venta_id", type: "integer"),
                    new OA\Property(property: "curso_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Agregado"),
            new OA\Response(response: 422, description: "Error de validación"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'venta_id' => 'required|exists:venta,id',
                'curso_id' => 'required|exists:curso,id'
            ]);

            $curso = Curso::findOrFail($data['curso_id']);

            $data['precio_unitario'] = $curso->costo;
            $data['subtotal'] = $curso->costo;

            $detalle = DetalleVentaCurso::create($data);

            $this->recalcularVenta($data['venta_id']);

            return response()->json($detalle, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Curso o venta no encontrada'
            ], 404);
        }
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
        try {
            return response()->json(
                DetalleVentaCurso::with(['venta', 'curso'])
                    ->findOrFail($id),
                200
            );
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "curso_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Actualizado"),
            new OA\Response(response: 404, description: "No encontrado"),
            new OA\Response(response: 422, description: "Error de validación")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            $detalle = DetalleVentaCurso::findOrFail($id);

            $data = $request->validate([
                'curso_id' => 'sometimes|exists:curso,id'
            ]);

            if (isset($data['curso_id'])) {
                $curso = Curso::findOrFail($data['curso_id']);

                $data['precio_unitario'] = $curso->costo;
                $data['subtotal'] = $curso->costo;
            }

            $detalle->update($data);

            $this->recalcularVenta($detalle->venta_id);

            return response()->json([
                'message' => 'Actualizado correctamente',
                'data' => $detalle->fresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
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
            new OA\Response(response: 200, description: "Eliminado"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function destroy($id)
    {
        try {
            $detalle = DetalleVentaCurso::findOrFail($id);

            $ventaId = $detalle->venta_id;

            $detalle->delete();

            $this->recalcularVenta($ventaId);

            return response()->json([
                'message' => 'Eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detalle no encontrado'
            ], 404);
        }
    }

    private function recalcularVenta($ventaId)
    {
        $venta = Venta::find($ventaId);

        if (!$venta) return;

        $venta->total = DetalleVentaCurso::where('venta_id', $ventaId)
            ->sum('subtotal');

        $venta->save();
    }
}
