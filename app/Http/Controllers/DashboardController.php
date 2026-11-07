<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Curso;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $usuario  = auth()->user();

        if ($usuario->tieneRol('Cliente') && ! $usuario->tieneRol('Administrador', 'Asesor', 'Gerente')) {
            return redirect()->route('mi-cuenta.inicio');
        }
        $esAdmin  = $usuario->tieneRol('Administrador');
        $esAsesor = $usuario->tieneRol('Asesor');
        $esGerente = $usuario->tieneRol('Gerente');

        // Cache corto por rol para evitar recalcular las mismas ~10 agregaciones
        // en cada recarga del dashboard. Se invalida en 60s.
        $rolKey = $esAdmin ? 'admin' : ($esAsesor ? 'asesor' : ($esGerente ? 'gerente' : 'cliente'));
        $stats  = Cache::remember("dashboard_stats_{$rolKey}_{$usuario->id}", 60,
            fn() => $this->buildStats($esAdmin, $esAsesor, $esGerente, $usuario));

        $ventasMensuales = $esAdmin
            ? Cache::remember('dashboard_ventas_mensuales_v2_' . now()->year, 300, fn() => $this->ventasMensuales())
            : collect();

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
                'productos'       => Producto::count(),
                'cursos'          => Curso::count(),
                'categorias'      => CategoriaProducto::where('estado', true)->count(),
                'usuarios'        => Usuario::count(),
                'inventario_bajo' => Inventario::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
                'ventas_hoy'      => Venta::pagadasEntre(today(), today()->endOfDay())->sum('total'),
                'ventas_mes'      => Venta::pagadasEntre(now()->startOfMonth(), now()->endOfMonth())->sum('total'),
                'ventas_anio'     => Venta::pagadasEntre(now()->startOfYear(), now()->endOfYear())->sum('total'),
                'ordenes_mes'     => Venta::pagadasEntre(now()->startOfMonth(), now()->endOfMonth())->count(),
            ];
        }

        if ($esAsesor) {
            return [
                'productos'       => Producto::where('estado', true)->count(),
                'cursos'          => Curso::where('estado', true)->count(),
                'categorias'      => CategoriaProducto::where('estado', true)->count(),
                'inventario_bajo' => Inventario::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
                'ventas_mes'      => Venta::pagadasEntre(now()->startOfMonth(), now()->endOfMonth())->sum('total'),
                'ordenes_mes'     => Venta::pagadasEntre(now()->startOfMonth(), now()->endOfMonth())->count(),
            ];
        }

        if ($esGerente) {
            return [
                'productos'       => Producto::where('estado', true)->count(),
                'cursos'          => Curso::where('estado', true)->count(),
                'inventario_bajo' => Inventario::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
                'ventas_mes'      => Venta::pagadasEntre(now()->startOfMonth(), now()->endOfMonth())->sum('total'),
                'ventas_anio'     => Venta::pagadasEntre(now()->startOfYear(), now()->endOfYear())->sum('total'),
                'ordenes_mes'     => Venta::pagadasEntre(now()->startOfMonth(), now()->endOfMonth())->count(),
            ];
        }

        // Cliente
        return [
            'mis_pedidos'   => $usuario->ventas()->count(),
            'mis_cursos'    => $usuario->inscripciones()->count(),
            'total_gastado' => $usuario->ventas()->pagadas()->sum('total'),
        ];
    }

    private function ventasMensuales(): array
    {
        // Por mes: dinero recibido y cuántas ventas se pagaron (solo ventas pagadas del año actual).
        // Se cuentan en el mes en que se pagaron.
        $f = Venta::FECHA_PAGO_SQL;
        $datos = Venta::pagadas()->selectRaw("EXTRACT(MONTH FROM {$f})::int AS mes, SUM(total) AS total, COUNT(*) AS ordenes")
            ->whereRaw("EXTRACT(YEAR FROM {$f}) = ?", [now()->year])
            ->groupByRaw("EXTRACT(MONTH FROM {$f})")
            ->orderByRaw("EXTRACT(MONTH FROM {$f})")
            ->get()
            ->keyBy('mes');

        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $resultado = [];
        foreach ($meses as $i => $nombre) {
            $fila = $datos->get($i + 1);
            $resultado[] = [
                'mes'     => $nombre,
                'total'   => (float) ($fila->total ?? 0),
                'ordenes' => (int) ($fila->ordenes ?? 0),
            ];
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
