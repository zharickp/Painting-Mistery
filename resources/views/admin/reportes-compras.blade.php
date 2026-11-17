@extends('layouts.app')

@section('title', 'Reportes de compras')

@php
    $pesos = fn ($v) => '$' . number_format($v, 0, ',', '.');
    $iconos = [
        'caja'       => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
        'tendencia'  => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
        'recibo'     => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
        'camion'     => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
    ];
    $diferencia   = $ventasAnio - $totalAnio;
    $maxProveedor = max(1, (float) $topProveedores->max('total'));
    $maxUnidades  = max(1, (int) $topProductos->max('unidades'));
@endphp

@section('content')

@include('admin.partials.reportes-cabecera', ['activo' => 'compras'])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @include('partials.tarjeta-dato', ['etiqueta' => 'Compras de este mes', 'valor' => $pesos($mes['total']), 'detalle' => $mes['cantidad'] . ' compra(s)', 'color' => 'slate', 'icono' => $iconos['caja']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Compras del año ' . $anio, 'valor' => $pesos($totalAnio), 'detalle' => $cantidadAnio . ' compra(s)', 'color' => 'indigo', 'icono' => $iconos['tendencia']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Promedio por compra', 'valor' => $pesos($promedio), 'detalle' => 'en ' . $anio, 'color' => 'amber', 'icono' => $iconos['recibo']])
    @include('partials.tarjeta-dato', ['etiqueta' => 'Proveedores activos', 'valor' => $proveedoresActivos, 'detalle' => 'disponibles para comprar', 'color' => 'sky', 'icono' => $iconos['camion'], 'enlace' => route('admin.proveedores.index')])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Compras y ventas por mes</h2>
        <p class="text-xs text-slate-400 mt-0.5 mb-5">Año {{ $anio }}</p>
        <div class="relative w-full h-[280px]"><canvas id="graficaCompras"></canvas></div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Resumen del año</h2>
        <p class="text-xs text-slate-400 mt-0.5 mb-5">{{ $anio }}</p>
        <dl class="space-y-4 text-sm">
            <div class="flex items-center justify-between">
                <dt class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>Ventas</dt>
                <dd class="font-bold text-slate-800 dark:text-slate-100">{{ $pesos($ventasAnio) }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="flex items-center gap-2 text-slate-600 dark:text-slate-300"><span class="h-2.5 w-2.5 rounded-full bg-slate-600"></span>Compras</dt>
                <dd class="font-bold text-slate-800 dark:text-slate-100">{{ $pesos($totalAnio) }}</dd>
            </div>
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <dt class="font-semibold text-slate-700 dark:text-slate-200">Diferencia</dt>
                <dd class="text-lg font-black {{ $diferencia >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $diferencia < 0 ? '-' : '' }}{{ $pesos(abs($diferencia)) }}</dd>
            </div>
        </dl>
        <p class="mt-4 text-[11px] text-slate-400 leading-relaxed">Ventas menos compras. No es la ganancia.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Proveedores principales</h2>
            <p class="text-xs text-slate-400 mt-0.5">Por valor comprado en {{ $anio }}</p>
        </div>
        @forelse ($topProveedores as $i => $p)
            <div class="px-6 py-3.5 border-b border-slate-100 dark:border-slate-800 last:border-0">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $i + 1 }}. {{ $p->nombre }}</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">{{ $pesos($p->total) }} · {{ $p->cantidad }} compra(s)</span>
                </div>
                <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-600 dark:bg-slate-400 rounded-full" style="width: {{ round($p->total / $maxProveedor * 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="px-6 py-12 text-center text-sm text-slate-400">No hay compras registradas en {{ $anio }}.</p>
        @endforelse
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Productos más comprados</h2>
            <p class="text-xs text-slate-400 mt-0.5">Por unidades compradas en {{ $anio }}</p>
        </div>
        @forelse ($topProductos as $i => $tp)
            <div class="px-6 py-3.5 border-b border-slate-100 dark:border-slate-800 last:border-0">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $i + 1 }}. {{ $tp->nombre ?? 'Producto eliminado' }}</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">{{ $tp->unidades }} uds · {{ $pesos($tp->total) }}</span>
                </div>
                <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-slate-600 dark:bg-slate-400 rounded-full" style="width: {{ round($tp->unidades / $maxUnidades * 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="px-6 py-12 text-center text-sm text-slate-400">No hay compras registradas en {{ $anio }}.</p>
        @endforelse
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Detalle por mes</h2>
        <p class="text-xs text-slate-400 mt-0.5">Año {{ $anio }}</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/40">
                <tr class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-5 py-2.5 text-left font-semibold">Mes</th>
                    <th class="px-5 py-2.5 text-center font-semibold">Compras</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Valor comprado</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Ventas</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Diferencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($porMes as $i => $fila)
                    @php $dif = $ventasPorMes[$i]['total'] - $fila['total']; @endphp
                    <tr>
                        <td class="px-5 py-2.5 font-semibold text-slate-800 dark:text-slate-100">{{ $fila['mes_largo'] }}</td>
                        <td class="px-5 py-2.5 text-center text-slate-600 dark:text-slate-300">{{ $fila['cantidad'] }}</td>
                        <td class="px-5 py-2.5 text-right font-bold text-slate-800 dark:text-slate-100">{{ $pesos($fila['total']) }}</td>
                        <td class="px-5 py-2.5 text-right text-slate-600 dark:text-slate-300">{{ $pesos($ventasPorMes[$i]['total']) }}</td>
                        <td class="px-5 py-2.5 text-right font-semibold {{ $dif >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $dif < 0 ? '-' : '' }}{{ $pesos(abs($dif)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('admin.partials.grafica-script')
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('graficaCompras'), {
        type: 'bar',
        data: {
            labels: @json(array_column($porMes, 'mes')),
            datasets: [
                { label: 'Ventas',  data: @json(array_column($ventasPorMes, 'total')), backgroundColor: '#dc2626', borderRadius: 5, maxBarThickness: 22 },
                { label: 'Compras', data: @json(array_column($porMes, 'total')),       backgroundColor: '#475569', borderRadius: 5, maxBarThickness: 22 },
            ],
        },
        options: window.pmGrafica.opcionesBarras(true),
    });
});
</script>
@endsection
