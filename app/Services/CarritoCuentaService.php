<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\ListaDeseos;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

/**
 * Carrito y lista de deseos guardados en la cuenta del cliente.
 * La tienda los maneja en el navegador mientras el cliente navega; con la
 * sesión iniciada, cada cambio se guarda aquí, y al entrar desde otro
 * dispositivo (o después de cerrar sesión) se recuperan desde la cuenta.
 */
class CarritoCuentaService
{
    /** Lo que la página necesita para pintar el carrito y los deseos del cliente. */
    public static function estado(Usuario $usuario): array
    {
        $carrito = Carrito::where('usuario_id', $usuario->id)->where('estado', 'activo')->latest('id')->first();

        $items = $carrito
            ? $carrito->detalles()->with('producto.imagenes')->orderBy('id')->get()
                ->filter(fn ($d) => $d->producto?->estado)
                ->map(fn ($d) => self::datosProducto($d->producto) + ['qty' => (int) $d->cantidad])
                ->values()->all()
            : [];

        $deseos = ListaDeseos::where('usuario_id', $usuario->id)->with('producto.imagenes')->orderBy('id')->get()
            ->filter(fn ($d) => $d->producto?->estado)
            ->map(fn ($d) => self::datosProducto($d->producto))
            ->values()->all();

        return ['usuario' => $usuario->id, 'carrito' => $items, 'deseos' => $deseos];
    }

    /**
     * Reemplaza el carrito activo y la lista de deseos por lo que manda el navegador.
     * Solo se guardan productos activos; el precio y el stock se revalidan al pagar.
     *
     * @param  array<int, array{id:int, qty:int}>  $carrito
     * @param  array<int, int>  $deseos  ids de producto
     */
    public static function guardar(Usuario $usuario, array $carrito, array $deseos): void
    {
        $ids = collect($carrito)->pluck('id')->merge($deseos)->unique()->all();
        $activos = Producto::whereIn('id', $ids ?: [0])->where('estado', true)->pluck('id')->all();

        DB::transaction(function () use ($usuario, $carrito, $deseos, $activos) {
            $actual = Carrito::firstOrCreate(['usuario_id' => $usuario->id, 'estado' => 'activo']);

            $cantidades = [];
            foreach ($carrito as $item) {
                if (in_array((int) $item['id'], $activos, true)) {
                    $cantidades[(int) $item['id']] = min(99, max(1, (int) $item['qty']));
                }
            }

            $actual->detalles()->whereNotIn('producto_id', array_keys($cantidades) ?: [0])->delete();
            foreach ($cantidades as $productoId => $cantidad) {
                CarritoDetalle::updateOrCreate(
                    ['carrito_id' => $actual->id, 'producto_id' => $productoId],
                    ['cantidad' => $cantidad]
                );
            }

            $deseosValidos = array_values(array_intersect(array_map('intval', $deseos), $activos));
            ListaDeseos::where('usuario_id', $usuario->id)->whereNotIn('producto_id', $deseosValidos ?: [0])->delete();
            foreach ($deseosValidos as $productoId) {
                ListaDeseos::firstOrCreate(['usuario_id' => $usuario->id, 'producto_id' => $productoId]);
            }
        });
    }

    private static function datosProducto(Producto $producto): array
    {
        return [
            'id'     => $producto->id,
            'nombre' => $producto->nombre,
            'precio' => (float) $producto->precio,
            'imagen' => $producto->imagen ?? '',
        ];
    }
}
