<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Curso;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usuario  = auth()->user();
        $esAdmin  = $usuario->tieneRol('Administrador');
        $esAsesor = $usuario->tieneRol('Asesor');
        $esGerente = $usuario->tieneRol('Gerente');

        $stats           = $this->buildStats($esAdmin, $esAsesor, $esGerente, $usuario);
        $ventasMensuales = $esAdmin ? $this->ventasMensuales() : collect();
        $ventasRecientes = ($esAdmin || $esAsesor) ? $this->ventasRecientes() : collect();
        $misVentas       = $usuario->ventas()->with('detalleProductos')->latest('fecha')->limit(5)->get();

        return view('dashboard', compact(
            'stats', 'ventasMensuales', 'ventasRecientes', 'misVentas',
            'esAdmin', 'esAsesor', 'esGerente'
        ));
    }

    private function buildStats(bool $esAdmin, bool $esAsesor, bool $esGerente, $usuario): array
    {
        if ($esAdmin) {
            return [
                'productos'   => Producto::count(),
                'cursos'      => Curso::count(),
                'categorias'  => CategoriaProducto::where('estado', true)->count(),
                'usuarios'    => Usuario::count(),
                'ventas_hoy'  => Venta::whereDate('fecha', today())->sum('total'),
                'ventas_mes'  => Venta::whereYear('fecha', now()->year)
                                      ->whereMonth('fecha', now()->month)->sum('total'),
                'ventas_anio' => Venta::whereYear('fecha', now()->year)->sum('total'),
                'ordenes_mes' => Venta::whereYear('fecha', now()->year)
                                      ->whereMonth('fecha', now()->month)->count(),
            ];
        }

        if ($esAsesor) {
            return [
                'productos'   => Producto::where('estado', true)->count(),
                'cursos'      => Curso::where('estado', true)->count(),
                'categorias'  => CategoriaProducto::where('estado', true)->count(),
                'ventas_mes'  => Venta::whereYear('fecha', now()->year)
                                      ->whereMonth('fecha', now()->month)->sum('total'),
                'ordenes_mes' => Venta::whereYear('fecha', now()->year)
                                      ->whereMonth('fecha', now()->month)->count(),
            ];
        }

        if ($esGerente) {
            return [
                'productos'   => Producto::where('estado', true)->count(),
                'cursos'      => Curso::where('estado', true)->count(),
                'ventas_mes'  => Venta::whereYear('fecha', now()->year)
                                      ->whereMonth('fecha', now()->month)->sum('total'),
                'ventas_anio' => Venta::whereYear('fecha', now()->year)->sum('total'),
                'ordenes_mes' => Venta::whereYear('fecha', now()->year)
                                      ->whereMonth('fecha', now()->month)->count(),
            ];
        }

        // Cliente
        return [
            'mis_pedidos'   => $usuario->ventas()->count(),
            'mis_cursos'    => $usuario->inscripciones()->count(),
            'total_gastado' => $usuario->ventas()->sum('total'),
        ];
    }

    private function ventasMensuales(): array
    {
        $datos = Venta::selectRaw("EXTRACT(MONTH FROM fecha)::int AS mes, SUM(total) AS total")
            ->whereRaw("EXTRACT(YEAR FROM fecha) = ?", [now()->year])
            ->groupByRaw("EXTRACT(MONTH FROM fecha)")
            ->orderByRaw("EXTRACT(MONTH FROM fecha)")
            ->pluck('total', 'mes')
            ->toArray();

        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $resultado = [];
        foreach ($meses as $i => $nombre) {
            $resultado[] = ['mes' => $nombre, 'total' => (float)($datos[$i + 1] ?? 0)];
        }
        return $resultado;
    }

    private function ventasRecientes(): \Illuminate\Database\Eloquent\Collection
    {
        return Venta::with('usuario')
            ->latest('fecha')
            ->limit(6)
            ->get();
    }
}
