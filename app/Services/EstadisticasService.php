<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\DetalleVentaProducto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Cifras de ventas y compras que usan el inicio y los reportes.
 *
 * Ventas: solo las pagadas, contadas en la fecha en que se pagaron.
 * Compras: todas las registradas menos las anuladas, por la fecha de la factura.
 */
class EstadisticasService
{
    public const MESES = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    public const MESES_LARGOS = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // ── Ventas ────────────────────────────────────────────────

    /** Total y cantidad de ventas pagadas entre dos fechas. */
    public static function ventasEntre($desde, $hasta): array
    {
        $fila = Venta::pagadasEntre($desde, $hasta)->selectRaw('COALESCE(SUM(total), 0) AS total, COUNT(*) AS cantidad')->first();

        return ['total' => (float) $fila->total, 'cantidad' => (int) $fila->cantidad];
    }

    /** Por cada mes del año: dinero recibido y número de ventas. */
    public static function ventasPorMes(int $anio): array
    {
        $f = Venta::FECHA_PAGO_SQL;
        $datos = Venta::pagadas()
            ->selectRaw("EXTRACT(MONTH FROM {$f})::int AS mes, SUM(total) AS total, COUNT(*) AS cantidad")
            ->whereRaw("EXTRACT(YEAR FROM {$f}) = ?", [$anio])
            ->groupByRaw("EXTRACT(MONTH FROM {$f})")
            ->get()
            ->keyBy('mes');

        return self::llenarMeses($datos);
    }

    /** Productos con más unidades vendidas en el año. */
    public static function productosMasVendidos(int $anio, int $limite = 5): Collection
    {
        $f = Venta::FECHA_PAGO_SQL;

        return DetalleVentaProducto::query()
            ->join('venta', 'venta.id', '=', 'detalle_venta_producto.venta_id')
            ->leftJoin('producto', 'producto.id', '=', 'detalle_venta_producto.producto_id')
            ->where('venta.estado', 'pagada')
            ->whereRaw("EXTRACT(YEAR FROM {$f}) = ?", [$anio])
            ->groupBy('detalle_venta_producto.producto_id', 'producto.nombre')
            ->selectRaw('producto.nombre AS nombre, SUM(detalle_venta_producto.cantidad) AS unidades, SUM(detalle_venta_producto.subtotal) AS total')
            ->orderByDesc('unidades')
            ->limit($limite)
            ->get();
    }

    // ── Compras ───────────────────────────────────────────────

    /** Total y cantidad de compras (no anuladas) entre dos fechas. */
    public static function comprasEntre($desde, $hasta): array
    {
        $fila = Compra::vigentes()
            ->whereBetween('fecha', [Carbon::parse($desde)->toDateString(), Carbon::parse($hasta)->toDateString()])
            ->selectRaw('COALESCE(SUM(total), 0) AS total, COUNT(*) AS cantidad')
            ->first();

        return ['total' => (float) $fila->total, 'cantidad' => (int) $fila->cantidad];
    }

    /** Por cada mes del año: dinero invertido en compras y número de compras. */
    public static function comprasPorMes(int $anio): array
    {
        $datos = Compra::vigentes()
            ->selectRaw('EXTRACT(MONTH FROM fecha)::int AS mes, SUM(total) AS total, COUNT(*) AS cantidad')
            ->whereRaw('EXTRACT(YEAR FROM fecha) = ?', [$anio])
            ->groupByRaw('EXTRACT(MONTH FROM fecha)')
            ->get()
            ->keyBy('mes');

        return self::llenarMeses($datos);
    }

    /** Proveedores a los que más se les compró en el año. */
    public static function proveedoresPrincipales(int $anio, int $limite = 5): Collection
    {
        return Compra::vigentes()
            ->join('proveedor', 'proveedor.id', '=', 'compra.proveedor_id')
            ->whereRaw('EXTRACT(YEAR FROM compra.fecha) = ?', [$anio])
            ->groupBy('proveedor.id', 'proveedor.nombre')
            ->selectRaw('proveedor.nombre AS nombre, SUM(compra.total) AS total, COUNT(*) AS cantidad')
            ->orderByDesc('total')
            ->limit($limite)
            ->get();
    }

    /** Productos de los que más unidades se compraron en el año. */
    public static function productosMasComprados(int $anio, int $limite = 5): Collection
    {
        return DetalleCompra::query()
            ->join('compra', 'compra.id', '=', 'detalle_compra.compra_id')
            ->leftJoin('producto', 'producto.id', '=', 'detalle_compra.producto_id')
            ->where('compra.estado', '!=', 'anulada')
            ->whereRaw('EXTRACT(YEAR FROM compra.fecha) = ?', [$anio])
            ->groupBy('detalle_compra.producto_id', 'producto.nombre')
            ->selectRaw('producto.nombre AS nombre, SUM(detalle_compra.cantidad) AS unidades, SUM(detalle_compra.subtotal) AS total')
            ->orderByDesc('unidades')
            ->limit($limite)
            ->get();
    }

    // ── Apoyo ─────────────────────────────────────────────────

    /** Años que tienen ventas o compras, del más reciente al más antiguo (siempre incluye el actual). */
    public static function aniosDisponibles(): array
    {
        $f = Venta::FECHA_PAGO_SQL;
        $ventas  = Venta::pagadas()->selectRaw("DISTINCT EXTRACT(YEAR FROM {$f})::int AS anio")->pluck('anio')->all();
        $compras = Compra::vigentes()->selectRaw('DISTINCT EXTRACT(YEAR FROM fecha)::int AS anio')->pluck('anio')->all();

        $anios = array_unique(array_merge([now()->year], $ventas, $compras));
        rsort($anios);

        return array_values($anios);
    }

    private static function llenarMeses(Collection $datos): array
    {
        $resultado = [];
        foreach (self::MESES as $i => $nombre) {
            $fila = $datos->get($i + 1);
            $resultado[] = [
                'mes'      => $nombre,
                'mes_largo'=> self::MESES_LARGOS[$i],
                'total'    => (float) ($fila->total ?? 0),
                'cantidad' => (int) ($fila->cantidad ?? 0),
            ];
        }

        return $resultado;
    }
}
