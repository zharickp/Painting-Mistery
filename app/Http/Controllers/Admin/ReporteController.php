<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Services\EstadisticasService as E;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reportes de ventas y de compras, cada uno con su versión para descargar en PDF.
 * Se puede elegir el año (?anio=2026); por defecto, el actual.
 */
class ReporteController extends Controller
{
    public function ventas(Request $request): View
    {
        return view('admin.reportes', $this->datosVentas($this->anio($request)));
    }

    public function ventasPdf(Request $request): View
    {
        return view('admin.reportes-pdf.ventas', $this->datosVentas($this->anio($request)));
    }

    public function compras(Request $request): View
    {
        return view('admin.reportes-compras', $this->datosCompras($this->anio($request)));
    }

    public function comprasPdf(Request $request): View
    {
        return view('admin.reportes-pdf.compras', $this->datosCompras($this->anio($request)));
    }

    private function datosVentas(int $anio): array
    {
        $porMes  = E::ventasPorMes($anio);
        $totalAnio    = array_sum(array_column($porMes, 'total'));
        $cantidadAnio = array_sum(array_column($porMes, 'cantidad'));

        return [
            'anio'      => $anio,
            'anios'     => E::aniosDisponibles(),
            'hoy'       => E::ventasEntre(today(), today()->endOfDay()),
            'semana'    => E::ventasEntre(now()->startOfWeek(), now()->endOfWeek()),
            'mes'       => E::ventasEntre(now()->startOfMonth(), now()->endOfMonth()),
            'totalAnio' => $totalAnio,
            'cantidadAnio' => $cantidadAnio,
            'promedio'  => $cantidadAnio > 0 ? $totalAnio / $cantidadAnio : 0,
            'porMes'    => $porMes,
            'estados'   => [
                'pagada'    => Venta::where('estado', 'pagada')->count(),
                'pendiente' => Venta::where('estado', 'pendiente')->count(),
                'cancelada' => Venta::where('estado', 'cancelada')->count(),
            ],
            'topProductos' => E::productosMasVendidos($anio),
        ];
    }

    private function datosCompras(int $anio): array
    {
        $porMes       = E::comprasPorMes($anio);
        $ventasPorMes = E::ventasPorMes($anio);
        $totalAnio    = array_sum(array_column($porMes, 'total'));
        $cantidadAnio = array_sum(array_column($porMes, 'cantidad'));

        return [
            'anio'         => $anio,
            'anios'        => E::aniosDisponibles(),
            'mes'          => E::comprasEntre(now()->startOfMonth(), now()->endOfMonth()),
            'totalAnio'    => $totalAnio,
            'cantidadAnio' => $cantidadAnio,
            'promedio'     => $cantidadAnio > 0 ? $totalAnio / $cantidadAnio : 0,
            'proveedoresActivos' => Proveedor::where('estado', true)->count(),
            'porMes'       => $porMes,
            'ventasPorMes' => $ventasPorMes,
            'ventasAnio'   => array_sum(array_column($ventasPorMes, 'total')),
            'topProveedores' => E::proveedoresPrincipales($anio),
            'topProductos'   => E::productosMasComprados($anio),
        ];
    }

    private function anio(Request $request): int
    {
        $anio = (int) $request->query('anio', now()->year);

        return in_array($anio, E::aniosDisponibles(), true) ? $anio : now()->year;
    }
}
