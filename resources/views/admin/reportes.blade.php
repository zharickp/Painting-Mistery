@extends('layouts.app')

@section('title', 'Reportes de ventas')

@php
    $pesos = fn ($v) => '$' . number_format($v, 0, ',', '.');
    $iconos = [
        'carrito'    => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17',
        'calendario' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'barras'     => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'tendencia'  => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
        'recibo'     => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
    ];
    $totalEstados = array_sum($estados);
    $maxUnidades  = max(1, (int) $topProductos->max('unidades'));
@endphp

@section('content')

@include('admin.partials.reportes-cabecera', ['activo' => 'ventas'])

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    @include('partials.tarjeta-dato', ['etiqueta' => 'Hoy', 'valor' => $pesos($hoy['total']), 'detalle' => $hoy['cantidad'] . ' venta(s)', 'color' => 'red', 'icono' => $iconos['carrito']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Esta semana', 'valor' => $pesos($semana['total']), 'detalle' => $semana['cantidad'] . ' venta(s)', 'color' => 'indigo', 'icono' => $iconos['calendario']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Este mes', 'valor' => $pesos($mes['total']), 'detalle' => $mes['cantidad'] . ' venta(s)', 'color' => 'violet', 'icono' => $iconos['barras']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Año ' . $anio, 'valor' => $pesos($totalAnio), 'detalle' => $cantidadAnio . ' venta(s)', 'color' => 'emerald', 'icono' => $iconos['tendencia']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Promedio por venta', 'valor' => $pesos($promedio), 'detalle' => 'en ' . $anio, 'color' => 'amber', 'icono' => $iconos['recibo']])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Ingresos por mes</h2>
        <p class="text-xs text-slate-400 mt-0.5 mb-5">Ventas pagadas en {{ $anio }}</p>
        <div class="relative w-full h-[280px]"><canvas id="graficaVentas"></canvas></div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Estado de las ventas</h2>
        <p class="text-xs text-slate-400 mt-0.5 mb-4">Todas las órdenes registradas</p>
        <div class="relative flex items-center justify-center" style="height: 180px;">
            <canvas id="graficaEstados"></canvas>
            <div class="absolute text-center pointer-events-none">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalEstados }}</p>
            </div>
        </div>
        <div class="mt-4 space-y-2 text-xs">
            @foreach ([['Pagadas', 'pagada', 'bg-emerald-500'], ['Pendientes', 'pendiente', 'bg-amber-400'], ['Canceladas', 'cancelada', 'bg-rose-500']] as [$nombre, $clave, $punto])
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 font-semibold text-slate-700 dark:text-slate-200">
                        <span class="h-2 w-2 rounded-full {{ $punto }}"></span>{{ $nombre }}
                    </span>
                    <span class="font-bold text-slate-700 dark:text-slate-200">{{ $estados[$clave] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Productos más vendidos</h2>
            <p class="text-xs text-slate-400 mt-0.5">Por unidades vendidas en {{ $anio }}</p>
        </div>
        @forelse ($topProductos as $i => $tp)
            <div class="px-6 py-3.5 border-b border-slate-100 dark:border-slate-800 last:border-0">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $i + 1 }}. {{ $tp->nombre ?? 'Producto eliminado' }}</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">{{ $tp->unidades }} uds · {{ $pesos($tp->total) }}</span>
                </div>
                <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-red-600 rounded-full" style="width: {{ round($tp->unidades / $maxUnidades * 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="px-6 py-12 text-center text-sm text-slate-400">No hay ventas de productos en {{ $anio }}.</p>
        @endforelse
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Detalle por mes</h2>
            <p class="text-xs text-slate-400 mt-0.5">Año {{ $anio }}</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/40">
                <tr class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-5 py-2.5 text-left font-semibold">Mes</th>
                    <th class="px-5 py-2.5 text-center font-semibold">Ventas</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Ingresos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($porMes as $fila)
                    <tr>
                        <td class="px-5 py-2.5 font-semibold text-slate-800 dark:text-slate-100">{{ $fila['mes_largo'] }}</td>
                        <td class="px-5 py-2.5 text-center text-slate-600 dark:text-slate-300">{{ $fila['cantidad'] }}</td>
                        <td class="px-5 py-2.5 text-right font-bold text-slate-800 dark:text-slate-100">{{ $pesos($fila['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('admin.partials.grafica-script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const g = window.pmGrafica;
    new Chart(document.getElementById('graficaVentas'), {
        type: 'bar',
        data: {
            labels: @json(array_column($porMes, 'mes')),
            datasets: [{
                label: 'Ingresos',
                data: @json(array_column($porMes, 'total')),
                backgroundColor: '#dc2626',
                borderRadius: 6,
                maxBarThickness: 34,
            }],
        },
        options: g.opcionesBarras(),
    });

    new Chart(document.getElementById('graficaEstados'), {
        type: 'doughnut',
        data: {
            labels: ['Pagadas', 'Pendientes', 'Canceladas'],
            datasets: [{ data: @json(array_values($estados)), backgroundColor: ['#10b981', '#fbbf24', '#f43f5e'], borderWidth: 0 }],
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false } } },
    });
});
</script>
@endsection
