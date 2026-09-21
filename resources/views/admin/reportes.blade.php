@extends('layouts.app')
@section('title', 'Reportes')

@php
    use App\Models\Venta;
    use App\Models\DetalleVentaProducto;

    $hoy    = Venta::whereDate('fecha', today())->sum('total');
    $semana = Venta::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->sum('total');
    $mes    = Venta::whereYear('fecha', now()->year)->whereMonth('fecha', now()->month)->sum('total');
    $anio   = Venta::whereYear('fecha', now()->year)->sum('total');
    $total  = Venta::sum('total');

    $ordenesHoy    = Venta::whereDate('fecha', today())->count();
    $ordenesMes    = Venta::whereYear('fecha', now()->year)->whereMonth('fecha', now()->month)->count();
    $ticketPromedio = $ordenesMes > 0 ? round($mes / $ordenesMes, 0) : 0;

    // Ventas por mes del año actual (para gráfica de barras)
    $porMes = Venta::selectRaw("EXTRACT(MONTH FROM fecha)::int as num, SUM(total) as total, COUNT(*) as ordenes")
        ->whereRaw("EXTRACT(YEAR FROM fecha) = ?", [now()->year])
        ->groupByRaw("EXTRACT(MONTH FROM fecha)::int")
        ->orderByRaw("EXTRACT(MONTH FROM fecha)::int")
        ->get()
        ->keyBy('num');

    $mesesAll = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $labels = [];
    $ingresosData = [];
    $ordenesData = [];
    foreach ($mesesAll as $i => $nombre) {
        $labels[] = $nombre;
        $ingresosData[] = (float)($porMes[$i+1]->total ?? 0);
        $ordenesData[]  = (int)($porMes[$i+1]->ordenes ?? 0);
    }

    // Distribución de estados de venta (para dona)
    $pagadas    = Venta::where('estado','pagada')->count();
    $pendientes = Venta::where('estado','pendiente')->count();
    $canceladas = Venta::where('estado','cancelada')->count();
    $totalV     = $pagadas + $pendientes + $canceladas;

    // Top productos vendidos
    $topProductos = DetalleVentaProducto::selectRaw('producto_id, SUM(cantidad) as unidades, SUM(subtotal) as ingresos')
        ->with('producto:id,nombre')
        ->groupBy('producto_id')
        ->orderByDesc('unidades')
        ->limit(5)
        ->get();
    $maxUnidades = $topProductos->max('unidades') ?: 1;
@endphp

@section('content')

{{-- Cabecera --}}
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 text-white flex items-center justify-center shadow-lg shadow-red-950/20 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 rest:text-stone-800">Reportes de ventas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Ingresos, órdenes y desempeño de {{ now()->year }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 bg-white dark:bg-slate-900 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300">
        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="font-semibold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['Hoy',        $hoy,   'red',     'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17'],
        ['Esta semana',$semana,'indigo',  'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['Este mes',   $mes,   'purple',  'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2'],
        ['Este año',   $anio,  'amber',   'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ['Histórico',  $total, 'emerald', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
    ] as [$label, $val, $color, $path])
    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition">
        <div class="flex items-start justify-between mb-3">
            <div class="h-10 w-10 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-900/30 text-{{ $color }}-600 dark:text-{{ $color }}-400 flex items-center justify-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                </svg>
            </div>
        </div>
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</p>
        <p class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1">${{ number_format($val, 0, ',', '.') }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Gráfica principal: ventas mensuales (2 cols) --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rest:bg-amber-100/70 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Ingresos mensuales</h2>
                <p class="text-xs text-slate-400 mt-0.5">Comportamiento del año {{ now()->year }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>
                    Ingresos
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                    Órdenes
                </span>
            </div>
        </div>
        <div class="relative w-full h-[280px]">
            <canvas id="reporteChart"></canvas>
        </div>
    </div>

    {{-- Estado de pedidos (dona) --}}
    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <div class="mb-4">
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Estado de pedidos</h2>
            <p class="text-xs text-slate-400 mt-0.5">Distribución del histórico</p>
        </div>
        <div class="relative flex items-center justify-center" style="height: 180px;">
            <canvas id="doughnut"></canvas>
            <div class="absolute text-center pointer-events-none">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalV }}</p>
            </div>
        </div>
        <div class="mt-4 space-y-2">
            @foreach([
                ['Pagadas',    $pagadas,    'bg-emerald-500', 'text-emerald-700 dark:text-emerald-400', 'bg-emerald-50 dark:bg-emerald-900/30'],
                ['Pendientes', $pendientes, 'bg-amber-400',   'text-amber-700 dark:text-amber-400',     'bg-amber-50 dark:bg-amber-900/30'],
                ['Canceladas', $canceladas, 'bg-rose-500',    'text-rose-700 dark:text-rose-400',       'bg-rose-50 dark:bg-rose-900/30'],
            ] as [$name, $count, $dot, $text, $bg])
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $name }}</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-md font-bold {{ $text }} {{ $bg }}">
                        {{ $count }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Top productos --}}
    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Top productos vendidos</h2>
            <p class="text-xs text-slate-400 mt-0.5">Los 5 más vendidos del histórico</p>
        </div>
        @if($topProductos->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-slate-400">Sin datos de ventas todavía.</div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($topProductos as $i => $tp)
                    <div class="px-6 py-3.5">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="h-6 w-6 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-300 text-xs font-bold flex items-center justify-center shrink-0">
                                    {{ $i + 1 }}
                                </span>
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                    {{ $tp->producto->nombre ?? 'Producto #' . $tp->producto_id }}
                                </span>
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">
                                {{ $tp->unidades }} <span class="font-normal text-slate-400">uds</span>
                            </span>
                        </div>
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-red-500 to-red-700 rounded-full" style="width: {{ round(($tp->unidades / $maxUnidades) * 100) }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">${{ number_format($tp->ingresos, 0, ',', '.') }} en ingresos</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Desglose mensual --}}
    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Desglose mensual</h2>
                <p class="text-xs text-slate-400 mt-0.5">Detalle por mes de {{ now()->year }}</p>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Ticket promedio: <span class="font-bold text-slate-800 dark:text-slate-100">${{ number_format($ticketPromedio, 0, ',', '.') }}</span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/40">
                    <tr class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left font-semibold">Mes</th>
                        <th class="px-5 py-3 text-center font-semibold">Órdenes</th>
                        <th class="px-5 py-3 text-right font-semibold">Ingresos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($mesesAll as $i => $nombre)
                        @php $fila = $porMes[$i+1] ?? null; @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                            <td class="px-5 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $nombre }}</td>
                            <td class="px-5 py-3 text-center text-slate-600 dark:text-slate-300">{{ $fila?->ordenes ?? 0 }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800 dark:text-slate-100">
                                ${{ number_format($fila?->total ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(148,163,184,0.15)' : 'rgba(148,163,184,0.2)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // Gráfica de ingresos y órdenes por mes
    new Chart(document.getElementById('reporteChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Ingresos ($)',
                    data: @json($ingresosData),
                    backgroundColor: 'rgba(220,38,38,0.85)',
                    borderRadius: 8,
                    yAxisID: 'y',
                },
                {
                    label: 'Órdenes',
                    data: @json($ordenesData),
                    type: 'line',
                    borderColor: '#6366f1',
                    backgroundColor: '#6366f1',
                    tension: 0.35,
                    yAxisID: 'y1',
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: textColor } },
                y: {
                    beginAtZero: true, position: 'left',
                    grid: { color: gridColor },
                    ticks: { color: textColor, callback: (v) => '$' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v) }
                },
                y1: {
                    beginAtZero: true, position: 'right',
                    grid: { display: false },
                    ticks: { color: textColor, stepSize: 1 }
                }
            }
        }
    });

    // Dona de estado de pedidos
    new Chart(document.getElementById('doughnut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Pagadas', 'Pendientes', 'Canceladas'],
            datasets: [{
                data: [{{ $pagadas }}, {{ $pendientes }}, {{ $canceladas }}],
                backgroundColor: ['#22c55e', '#facc15', '#f87171'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection
