<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Venta;
use App\Services\EstadisticasService as E;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Inicio del panel. Cada rol ve lo que le sirve:
 *  - Administrador: ventas, compras, la comparación por mes y los proveedores principales.
 *  - Gerente: ventas y compras del mes y del año, y la comparación por mes.
 *  - Asesor: solo ventas (las de hoy, las del mes, las pendientes y las últimas).
 */
class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $usuario = auth()->user();

        if ($usuario->tieneRol('Cliente') && ! $usuario->tieneRol('Administrador', 'Asesor', 'Gerente')) {
            return redirect()->route('mi-cuenta.inicio');
        }

        $esAdmin   = $usuario->tieneRol('Administrador');
        $esGerente = ! $esAdmin && $usuario->tieneRol('Gerente');
        $esAsesor  = ! $esAdmin && ! $esGerente && $usuario->tieneRol('Asesor');
        $anio      = now()->year;

        $datos = compact('esAdmin', 'esGerente', 'esAsesor', 'anio');

        if ($esAdmin || $esGerente) {
            $datos += [
                'ventasMes'     => E::ventasEntre(now()->startOfMonth(), now()->endOfMonth()),
                'ventasAnio'    => E::ventasEntre(now()->startOfYear(), now()->endOfYear()),
                'comprasMes'    => E::comprasEntre(now()->startOfMonth(), now()->endOfMonth()),
                'comprasAnio'   => E::comprasEntre(now()->startOfYear(), now()->endOfYear()),
                'ventasPorMes'  => E::ventasPorMes($anio),
                'comprasPorMes' => E::comprasPorMes($anio),
                'stockBajo'     => Inventario::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
            ];
        }

        if ($esAdmin) {
            $datos += [
                'ventasHoy'      => E::ventasEntre(today(), today()->endOfDay()),
                'topProveedores' => E::proveedoresPrincipales($anio, 4),
            ];
        }

        if ($esAsesor) {
            $datos += [
                'ventasHoy'       => E::ventasEntre(today(), today()->endOfDay()),
                'ventasMes'       => E::ventasEntre(now()->startOfMonth(), now()->endOfMonth()),
                'pendientes'      => Venta::where('estado', 'pendiente')->count(),
                'ventasRecientes' => Venta::with('usuario')->latest('fecha')->limit(8)->get(),
            ];
        }

        // Usuario sin rol de panel (caso raro): se muestra el resumen de sus propias compras.
        if (! $esAdmin && ! $esGerente && ! $esAsesor) {
            $datos += [
                'stats' => [
                    'mis_pedidos'   => $usuario->ventas()->count(),
                    'mis_cursos'    => $usuario->inscripciones()->count(),
                    'total_gastado' => $usuario->ventas()->pagadas()->sum('total'),
                ],
                'misVentas' => $usuario->ventas()->with('detalleProductos')->latest('fecha')->limit(5)->get(),
            ];
        }

        return view('dashboard', $datos);
    }
}
