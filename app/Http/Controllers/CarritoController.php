<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Producto;
use App\Services\CarritoCuentaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    private function carritoActivo(): Carrito
    {
        return Carrito::firstOrCreate(
            ['usuario_id' => auth()->id(), 'estado' => 'activo'],
            ['usuario_id' => auth()->id(), 'estado' => 'activo']
        );
    }

    /**
     * El carrito no tiene página propia: se muestra como ventana en la tienda.
     * Esta ruta lleva a la tienda con el carrito abierto (?carrito=1) y conserva
     * el aviso que venga, por ejemplo "Tu carrito está vacío".
     */
    public function index(): RedirectResponse
    {
        session()->reflash();

        return redirect()->route('tienda.index', ['carrito' => 1]);
    }

    /**
     * Sincroniza el carrito visual (localStorage, usado en toda la tienda
     * pública) con el carrito real en base de datos que consume el checkout.
     * Reemplaza el contenido del carrito activo por lo que llega del cliente,
     * pero SIEMPRE revalida precio, estado y stock contra la BD — nunca
     * confía en los valores que manda el navegador.
     */
    public function sincronizar(Request $request)
    {
        $data = $request->validate([
            'items'            => 'present|array',
            'items.*.id'       => 'required|integer|exists:producto,id',
            'items.*.qty'      => 'required|integer|min:1|max:99',
        ]);

        $carrito = $this->carritoActivo();
        $avisos  = [];
        $idsValidos = [];
        $ajustes = []; // cantidades que el navegador debe corregir: [id => cantidad, 0 = quitar]

        foreach ($data['items'] as $item) {
            $producto = Producto::with('inventario')->find($item['id']);
            if (!$producto || !$producto->estado) {
                $avisos[] = "\"{$producto?->nombre}\" ya no está disponible y se quitó del carrito.";
                $ajustes[$item['id']] = 0;
                continue;
            }

            $stock = $producto->inventario?->stock_actual ?? 0;
            $cantidad = min((int) $item['qty'], $stock);

            if ($cantidad < 1) {
                $avisos[] = "\"{$producto->nombre}\" está agotado y se quitó del carrito.";
                $ajustes[$producto->id] = 0;
                continue;
            }
            if ($cantidad < $item['qty']) {
                $avisos[] = "Solo hay {$stock} unidades de \"{$producto->nombre}\": se ajustó la cantidad.";
                $ajustes[$producto->id] = $cantidad;
            }

            CarritoDetalle::updateOrCreate(
                ['carrito_id' => $carrito->id, 'producto_id' => $producto->id],
                ['cantidad' => $cantidad]
            );
            $idsValidos[] = $producto->id;
        }

        // Elimina del carrito real cualquier producto que ya no venga en la lista actual
        $carrito->detalles()->whereNotIn('producto_id', $idsValidos ?: [0])->delete();

        if ($carrito->detalles()->count() === 0) {
            return response()->json([
                'ok' => false, 'message' => 'No hay productos disponibles para pagar.',
                'avisos' => $avisos, 'ajustes' => (object) $ajustes,
            ], 422);
        }
        // Si hubo que corregir algo, el cliente lo ve en su carrito antes de pagar.
        if ($ajustes) {
            return response()->json(['ok' => false, 'avisos' => $avisos, 'ajustes' => (object) $ajustes], 409);
        }
        return response()->json(['ok' => true, 'avisos' => []]);
    }

    /**
     * Guarda en la cuenta del cliente el carrito y la lista de deseos que
     * tiene en el navegador, para recuperarlos al volver a iniciar sesión.
     */
    public function guardarEnCuenta(Request $request)
    {
        $data = $request->validate([
            'carrito'       => 'present|array|max:100',
            'carrito.*.id'  => 'required|integer',
            'carrito.*.qty' => 'required|integer|min:1|max:99',
            'deseos'        => 'present|array|max:200',
            'deseos.*'      => 'integer',
        ]);

        CarritoCuentaService::guardar($request->user(), $data['carrito'], $data['deseos']);

        return response()->json(['ok' => true]);
    }
}
