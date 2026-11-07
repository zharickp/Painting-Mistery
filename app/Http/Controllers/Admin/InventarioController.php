<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventarioController extends Controller
{
    /**
     * Ajustes que no son compra ni venta. La mercancía nueva entra por Compras,
     * por eso aquí no hay "entrada": solo el conteo físico (que puede subir o
     * bajar el stock) y las salidas.
     */
    public const TIPOS_AJUSTE = [
        'conteo'     => 'Conteo físico',
        'danado'     => 'Producto dañado',
        'perdida'    => 'Pérdida',
        'devolucion' => 'Devolución al proveedor',
    ];

    public function index(): View
    {
        $inventarios = Inventario::with('producto.categoria')
            ->orderBy('stock_actual')
            ->paginate(15);

        return view('admin.inventario', [
            'inventarios' => $inventarios,
            'tiposAjuste' => self::TIPOS_AJUSTE,
        ]);
    }

    public function actualizar(Request $request, Inventario $inventario): RedirectResponse
    {
        $data = $request->validate([
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'tipo'         => ['nullable', Rule::in(array_keys(self::TIPOS_AJUSTE))],
            'cantidad'     => ['required_with:tipo', 'nullable', 'integer', 'min:0', 'max:1000000'],
            'motivo'       => ['required_with:tipo', 'nullable', 'string', 'min:3', 'max:255'],
        ], [
            'stock_minimo.min'       => 'El stock mínimo no puede ser negativo.',
            'cantidad.required_with' => 'Escribe la cantidad del ajuste.',
            'motivo.required_with'   => 'Escribe el motivo del ajuste.',
            'motivo.min'             => 'El motivo debe tener al menos 3 caracteres.',
        ]);

        $nombre = $inventario->producto->nombre;

        // Solo cambia el stock mínimo: es un umbral de alerta, no mueve unidades.
        if (empty($data['tipo'])) {
            $inventario->update(['stock_minimo' => $data['stock_minimo']]);

            return redirect()->route('admin.inventario')
                ->with('success', "Stock mínimo de \"{$nombre}\" actualizado.");
        }

        $error = DB::transaction(function () use ($inventario, $data, $nombre) {
            $fila     = Inventario::lockForUpdate()->findOrFail($inventario->id);
            $anterior = $fila->stock_actual;
            $cantidad = (int) $data['cantidad'];

            if ($data['tipo'] === 'conteo') {
                $nuevo = $cantidad;
            } else {
                if ($cantidad < 1) {
                    return 'La cantidad que sale debe ser al menos 1.';
                }
                if ($cantidad > $anterior) {
                    return "No pueden salir {$cantidad} unidades: solo hay {$anterior}.";
                }
                $nuevo = $anterior - $cantidad;
            }

            $minimoAnterior = $fila->stock_minimo;
            // Sin el registro automático: este ajuste se audita abajo con su tipo y motivo.
            Inventario::withoutEvents(fn () => $fila->update([
                'stock_actual' => $nuevo,
                'stock_minimo' => $data['stock_minimo'],
            ]));

            $tipo = self::TIPOS_AJUSTE[$data['tipo']];
            AuditoriaService::registrar([
                'accion'             => 'actualizado',
                'modulo'             => 'Inventario',
                'registro_afectado'  => $fila->id,
                'nombre_registro'    => $nombre,
                'descripcion'        => "Ajuste de inventario ({$tipo}) en \"{$nombre}\": {$anterior} → {$nuevo}. Motivo: {$data['motivo']}",
                'valores_anteriores' => ['stock_actual' => $anterior, 'stock_minimo' => $minimoAnterior],
                'valores_nuevos'     => ['stock_actual' => $nuevo, 'stock_minimo' => (int) $data['stock_minimo'], 'tipo' => $tipo, 'motivo' => $data['motivo']],
            ]);

            return null;
        });

        if ($error) {
            return back()->withInput()->with('error', $error);
        }

        return redirect()->route('admin.inventario')
            ->with('success', "Ajuste registrado en \"{$nombre}\".");
    }
}
