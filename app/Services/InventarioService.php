<?php

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Venta;

/**
 * Único lugar donde la venta y la compra mueven el stock.
 *
 * Se llama siempre dentro de una transacción. Las filas del inventario se
 * bloquean (lockForUpdate) mientras se revisan y se descuentan, así dos
 * compras al mismo tiempo no pueden llevarse la misma unidad.
 */
class InventarioService
{
    /**
     * Descuenta las cantidades indicadas o no descuenta nada.
     *
     * @param  array<int,int>  $cantidades  [producto_id => cantidad]
     * @param  string  $pedido  cómo se nombra lo pedido en el mensaje de error
     * @throws StockInsuficienteException
     */
    public static function descontar(array $cantidades, string $pedido = 'pediste'): void
    {
        $cantidades = array_filter($cantidades, fn ($c) => $c > 0);
        if ($cantidades === []) {
            return;
        }

        $ids = array_keys($cantidades);
        sort($ids); // mismo orden de bloqueo en todas las compras: evita bloqueos cruzados

        $inventarios = Inventario::whereIn('producto_id', $ids)
            ->orderBy('producto_id')
            ->lockForUpdate()
            ->get()
            ->keyBy('producto_id');

        $nombres = Producto::whereIn('id', $ids)->pluck('nombre', 'id');
        $errores = [];

        foreach ($cantidades as $productoId => $cantidad) {
            $stock  = $inventarios[$productoId]->stock_actual ?? 0;
            $nombre = $nombres[$productoId] ?? 'un producto';

            if ($stock <= 0) {
                $errores[] = "\"{$nombre}\" está agotado.";
            } elseif ($cantidad > $stock) {
                $errores[] = ($stock === 1 ? 'Solo queda 1 unidad' : "Solo quedan {$stock} unidades")
                    . " de \"{$nombre}\" y {$pedido} {$cantidad}.";
            }
        }

        if ($errores) {
            throw new StockInsuficienteException(implode(' ', $errores));
        }

        foreach ($cantidades as $productoId => $cantidad) {
            $inventarios[$productoId]->decrement('stock_actual', $cantidad);
        }
    }

    /**
     * Devuelve unidades al inventario (venta cancelada). Si el producto no
     * tenía fila de inventario, se crea.
     *
     * @param  array<int,int>  $cantidades  [producto_id => cantidad]
     */
    public static function devolver(array $cantidades): void
    {
        self::sumar($cantidades);
    }

    /**
     * Suma unidades al inventario (mercancía que llega de un proveedor).
     *
     * @param  array<int,int>  $cantidades  [producto_id => cantidad]
     */
    public static function sumar(array $cantidades): void
    {
        foreach (array_filter($cantidades, fn ($c) => $c > 0) as $productoId => $cantidad) {
            $inventario = Inventario::where('producto_id', $productoId)->lockForUpdate()->first()
                ?? Inventario::create(['producto_id' => $productoId, 'stock_actual' => 0, 'stock_minimo' => 0]);

            $inventario->increment('stock_actual', $cantidad);
        }
    }

    /** Unidades por producto que lleva una venta: [producto_id => cantidad]. */
    public static function cantidadesDe(Venta $venta): array
    {
        $cantidades = [];
        foreach ($venta->detalleProductos as $d) {
            $cantidades[$d->producto_id] = ($cantidades[$d->producto_id] ?? 0) + (int) $d->cantidad;
        }

        return $cantidades;
    }
}
