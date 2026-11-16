@extends('layouts.app')

@section('title', 'Inicio')

@php
    $pesos = fn ($v) => '$' . number_format($v, 0, ',', '.');
    $iconos = [
        'dinero'     => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'barras'     => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'tendencia'  => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
        'caja'       => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
        'alerta'     => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'reloj'      => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
@endphp

@section('content')

{{-- Cabecera --}}
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div class="flex items-center gap-3">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-red-600 to-red-800 flex items-center justify-center text-white shadow-lg shadow-red-950/20 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Inicio</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Hola, {{ auth()->user()->primer_nombre }}</p>
        </div>
    </div>
    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ ucfirst(now()->translatedFormat('l, d \d\e F \d\e Y')) }}</p>
</div>

@if ($esAdmin || $esGerente)

    @php
        $mesTexto = ucfirst(now()->translatedFormat('F'));
        $diferenciaAnio = $ventasAnio['total'] - $comprasAnio['total'];
    @endphp

    @if ($esAdmin)
        {{-- Ventas --}}
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Ventas</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de hoy', 'valor' => $pesos($ventasHoy['total']), 'detalle' => $ventasHoy['cantidad'] . ' venta(s) pagada(s)', 'color' => 'emerald', 'icono' => $iconos['dinero'], 'enlace' => route('admin.ventas')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de ' . $mesTexto, 'valor' => $pesos($ventasMes['total']), 'detalle' => $ventasMes['cantidad'] . ' venta(s) pagada(s)', 'color' => 'red', 'icono' => $iconos['barras'], 'enlace' => route('admin.reportes')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de ' . $anio, 'valor' => $pesos($ventasAnio['total']), 'detalle' => $ventasAnio['cantidad'] . ' venta(s) pagada(s)', 'color' => 'violet', 'icono' => $iconos['tendencia'], 'enlace' => route('admin.reportes')])
        </div>

        {{-- Compras --}}
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Compras a proveedores</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @include('partials.tarjeta-dato', ['etiqueta' => 'Compras de ' . $mesTexto, 'valor' => $pesos($comprasMes['total']), 'detalle' => $comprasMes['cantidad'] . ' compra(s)', 'color' => 'slate', 'icono' => $iconos['caja'], 'enlace' => route('admin.compras.index')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Compras de ' . $anio, 'valor' => $pesos($comprasAnio['total']), 'detalle' => $comprasAnio['cantidad'] . ' compra(s)', 'color' => 'indigo', 'icono' => $iconos['tendencia'], 'enlace' => route('admin.reportes.compras')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Productos con stock bajo', 'valor' => $stockBajo, 'detalle' => $stockBajo > 0 ? 'Conviene revisar el inventario' : 'Todo el inventario está bien', 'color' => $stockBajo > 0 ? 'amber' : 'emerald', 'icono' => $iconos['alerta'], 'enlace' => route('admin.inventario')])
        </div>
    @else
        {{-- Gerente: ventas y compras del mes y del año --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de ' . $mesTexto, 'valor' => $pesos($ventasMes['total']), 'detalle' => $ventasMes['cantidad'] . ' venta(s) pagada(s)', 'color' => 'red', 'icono' => $iconos['barras'], 'enlace' => route('admin.reportes')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de ' . $anio, 'valor' => $pesos($ventasAnio['total']), 'detalle' => $ventasAnio['cantidad'] . ' venta(s) pagada(s)', 'color' => 'violet', 'icono' => $iconos['tendencia'], 'enlace' => route('admin.reportes')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Compras de ' . $mesTexto, 'valor' => $pesos($comprasMes['total']), 'detalle' => $comprasMes['cantidad'] . ' compra(s)', 'color' => 'slate', 'icono' => $iconos['caja'], 'enlace' => route('admin.compras.index')])
            @include('partials.tarjeta-dato', ['etiqueta' => 'Compras de ' . $anio, 'valor' => $pesos($comprasAnio['total']), 'detalle' => $comprasAnio['cantidad'] . ' compra(s)', 'color' => 'indigo', 'icono' => $iconos['tendencia'], 'enlace' => route('admin.reportes.compras')])
        </div>
        @if ($stockBajo > 0)
            <a href="{{ route('admin.inventario') }}" class="mb-6 flex items-center gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 px-4 py-3 text-sm text-amber-800 dark:text-amber-300 hover:bg-amber-100 transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconos['alerta'] }}"/></svg>
                <span><strong>{{ $stockBajo }}</strong> producto(s) con stock bajo. Ver inventario →</span>
            </a>
        @endif
    @endif

    {{-- Comparación por mes --}}
    <div class="grid grid-cols-1 {{ $esAdmin ? 'lg:grid-cols-3' : '' }} gap-6 mb-6">
        <div class="{{ $esAdmin ? 'lg:col-span-2' : '' }} bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Ventas y compras por mes</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Año {{ $anio }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Diferencia del año</p>
                    <p class="text-lg font-black {{ $diferenciaAnio >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $diferenciaAnio < 0 ? '-' : '' }}{{ $pesos(abs($diferenciaAnio)) }}</p>
                </div>
            </div>
            <div class="relative w-full h-[280px]"><canvas id="graficaInicio"></canvas></div>
        </div>

        @if ($esAdmin)
            @php $maxProv = max(1, (float) $topProveedores->max('total')); @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs flex flex-col">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Proveedores principales</h2>
                <p class="text-xs text-slate-400 mt-0.5 mb-5">Por valor comprado en {{ $anio }}</p>
                <div class="space-y-4 flex-1">
                    @forelse ($topProveedores as $p)
                        <div>
                            <div class="flex items-center justify-between gap-3 text-sm mb-1.5">
                                <span class="font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $p->nombre }}</span>
                                <span class="font-bold text-slate-800 dark:text-slate-100 shrink-0">{{ $pesos($p->total) }}</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-slate-600 dark:bg-slate-400 rounded-full" style="width: {{ round($p->total / $maxProv * 100) }}%"></div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $p->cantidad }} compra(s)</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Aún no hay compras en {{ $anio }}.</p>
                    @endforelse
                </div>
                <a href="{{ route('admin.reportes.compras') }}" class="mt-5 text-xs font-bold text-red-600 hover:text-red-700">Ver reporte de compras →</a>
            </div>
        @endif
    </div>

    @include('admin.partials.grafica-script')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        new Chart(document.getElementById('graficaInicio'), {
            type: 'bar',
            data: {
                labels: @json(array_column($ventasPorMes, 'mes')),
                datasets: [
                    { label: 'Ventas',  data: @json(array_column($ventasPorMes, 'total')),  backgroundColor: '#dc2626', borderRadius: 5, maxBarThickness: 22 },
                    { label: 'Compras', data: @json(array_column($comprasPorMes, 'total')), backgroundColor: '#475569', borderRadius: 5, maxBarThickness: 22 },
                ],
            },
            options: window.pmGrafica.opcionesBarras(true),
        });
    });
    </script>

@elseif ($esAsesor)

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de hoy', 'valor' => $pesos($ventasHoy['total']), 'detalle' => $ventasHoy['cantidad'] . ' venta(s) pagada(s)', 'color' => 'emerald', 'icono' => $iconos['dinero']])
        @include('partials.tarjeta-dato', ['etiqueta' => 'Ventas de ' . ucfirst(now()->translatedFormat('F')), 'valor' => $pesos($ventasMes['total']), 'detalle' => $ventasMes['cantidad'] . ' venta(s) pagada(s)', 'color' => 'red', 'icono' => $iconos['barras']])
        @include('partials.tarjeta-dato', ['etiqueta' => 'Pendientes de pago', 'valor' => $pendientes, 'detalle' => 'órdenes por confirmar', 'color' => 'amber', 'icono' => $iconos['reloj'], 'enlace' => route('admin.ventas', ['estado' => 'pendiente'])])
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Últimas ventas</h2>
            <a href="{{ route('admin.ventas') }}" class="text-xs text-red-600 hover:text-red-700 font-bold">Ver todas →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40 text-left text-[11px] text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3 font-semibold">Orden</th>
                        <th class="px-6 py-3 font-semibold">Cliente</th>
                        <th class="px-6 py-3 font-semibold">Fecha</th>
                        <th class="px-6 py-3 font-semibold text-center">Estado</th>
                        <th class="px-6 py-3 font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($ventasRecientes as $venta)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="px-6 py-3.5"><a href="{{ route('admin.ventas.show', $venta->id) }}" class="font-mono text-xs text-slate-600 dark:text-slate-300 hover:text-red-600">{{ $venta->numero_orden }}</a></td>
                            <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-100">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</td>
                            <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-center">@include('partials.estado-orden', ['venta' => $venta])</td>
                            <td class="px-6 py-3.5 text-right font-black text-slate-800 dark:text-slate-100">{{ $pesos($venta->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">No hay ventas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else


<div class="bg-gradient-to-r from-red-600 to-rose-700 rounded-2xl p-6 mb-6 text-white flex items-center gap-6 shadow-lg shadow-red-900/20">
    <div class="h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl font-black shrink-0 border border-white/30">
        {{ strtoupper(substr(auth()->user()->primer_nombre,0,1)) }}
    </div>
    <div>
        <p class="text-red-200 text-xs font-bold uppercase tracking-wider mb-1">Bienvenido a tu panel</p>
        <h2 class="text-xl font-black">{{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }}</h2>
        <p class="text-red-100 text-xs mt-0.5">{{ auth()->user()->correo }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Mis compras',   $stats['mis_pedidos'], 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'bg-red-50 text-red-600'],
        ['Mis cursos',    $stats['mis_cursos'], 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'bg-indigo-50 text-indigo-600'],
        ['Total comprado', '$'.number_format($stats['total_gastado'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-emerald-50 text-emerald-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 flex items-center gap-4 shadow-xs hover:shadow-md transition">
        <div class="rounded-xl p-3 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Mis últimas compras</h2>
        <p class="text-xs text-slate-400 mt-0.5">Tus compras más recientes</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-[11px] text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-3 font-semibold"># Orden</th>
                    <th class="px-6 py-3 font-semibold">Fecha</th>
                    <th class="px-6 py-3 font-semibold text-center">Estado</th>
                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($misVentas as $venta)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-6 py-3.5 font-mono text-slate-500 dark:text-slate-400 text-xs font-semibold">#{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3.5 text-center">
                        @include('partials.estado-orden', ['venta' => $venta])
                    </td>
                    <td class="px-6 py-3.5 text-right font-black text-slate-800 dark:text-slate-100">${{ number_format($venta->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="h-10 w-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Aún no tienes compras.</p>
                        <a href="{{ route('inicio') }}" class="mt-2 inline-block text-red-600 font-bold text-xs hover:underline">Ver catálogo en la tienda →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


@endif

@endsection
