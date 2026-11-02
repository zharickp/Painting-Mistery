@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

{{-- ══════════════════════════════════════════════════════
     CABECERA
══════════════════════════════════════════════════════ --}}
<div class="mb-6">

    {{-- Título --}}
    <div>
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-red-600 to-red-800 flex items-center justify-center text-white shadow-lg shadow-red-950/20 shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 dark:text-slate-100 tracking-tight">Inicio</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 dark:text-slate-400 mt-0.5">Hola, {{ auth()->user()->primer_nombre }}</p>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-400 dark:text-slate-300">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <span class="inline-flex items-center gap-1.5 bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900 text-red-700 dark:text-red-400 text-xs font-bold px-3 py-1.5 rounded-xl">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                {{ auth()->user()->roles->pluck('nombre')->join(' · ') }}
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     VISTA ADMINISTRADOR
══════════════════════════════════════════════════════ --}}
@if($esAdmin)

@php
    $pagadas    = \App\Models\Venta::where('estado','pagada')->count();
    $pendientes = \App\Models\Venta::where('estado','pendiente')->count();
    $canceladas = \App\Models\Venta::where('estado','cancelada')->count();
    $totalV     = $pagadas + $pendientes + $canceladas;

    // Sparkline SVG helper (misma línea de datos, distintos colores por tarjeta)
    $sparkline = function($values, $stroke, $fill) {
        if (empty($values)) $values = [0,0,0,0,0,0,0];
        $max = max($values); if ($max <= 0) $max = 1;
        $w = 100; $h = 34; $n = count($values);
        $step = $n > 1 ? $w / ($n - 1) : $w;
        $pts = [];
        foreach ($values as $i => $v) {
            $x = round($i * $step, 1);
            $y = round($h - (($v / $max) * ($h - 6)) - 3, 1);
            $pts[] = "$x,$y";
        }
        $line = 'M' . implode(' L', $pts);
        $area = $line . " L{$w},{$h} L0,{$h} Z";
        return "<svg viewBox=\"0 0 {$w} {$h}\" class=\"w-full h-full\" preserveAspectRatio=\"none\">"
            . "<path d=\"{$area}\" fill=\"{$fill}\"/>"
            . "<path d=\"{$line}\" fill=\"none\" stroke=\"{$stroke}\" stroke-width=\"1.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>"
            . "</svg>";
    };
    $spark = collect($ventasMensuales)->pluck('total')->take(7)->all();
    if (count($spark) === 0) $spark = [0,0,0,0,0,0,0];

    // Ventas de ayer para delta
    $ventasAyer = \App\Models\Venta::pagadas()->whereDate('fecha', now()->subDay())->sum('total');
    $ventasHoy  = $stats['ventas_hoy'] ?? 0;
    $delta      = $ventasAyer > 0 ? round((($ventasHoy - $ventasAyer) / $ventasAyer) * 100) : 0;
@endphp

{{-- 5 TARJETAS KPI CON SPARKLINES --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">

    {{-- 1. VENTAS HOY --}}
    <a href="{{ route('admin.ventas') }}"
       class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition group block">
        <div class="flex items-center justify-between mb-4">
            <div class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ventas de hoy</p>
        <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">${{ number_format($ventasHoy, 0, ',', '.') }}</p>
        <p class="mt-2 text-[11px] flex items-center gap-1">
            @if($delta > 0)
                <span class="text-emerald-600 font-bold">+{{ $delta }}%</span>
            @elseif($delta < 0)
                <span class="text-rose-600 font-bold">{{ $delta }}%</span>
            @else
                <span class="text-slate-400 font-bold">+0%</span>
            @endif
            <span class="text-slate-400 dark:text-slate-500">respecto a ayer</span>
        </p>
    </a>

    {{-- 2. ÓRDENES DEL MES --}}
    <a href="{{ route('admin.ventas') }}"
       class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition group block">
        <div class="flex items-center justify-between mb-4">
            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Órdenes pagadas</p>
        <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">{{ number_format($stats['ordenes_mes'] ?? 0) }}</p>
        <p class="mt-2 text-[11px] text-slate-400 dark:text-slate-500">
            <span class="font-bold text-slate-600 dark:text-slate-300">este mes</span>
        </p>
    </a>

    {{-- 3. NUEVOS CLIENTES / USUARIOS --}}
    <a href="{{ route('admin.usuarios.index') }}"
       class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition group block">
        <div class="flex items-center justify-between mb-4">
            <div class="h-10 w-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Usuarios registrados</p>
        <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">{{ number_format($stats['usuarios'] ?? 0) }}</p>
    </a>

    {{-- 4. INGRESOS DEL MES --}}
    <a href="{{ route('admin.reportes') }}"
       class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition group block">
        <div class="flex items-center justify-between mb-4">
            <div class="h-10 w-10 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ingresos del mes</p>
        <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">${{ number_format($stats['ventas_mes'] ?? 0, 0, ',', '.') }}</p>
        <p class="mt-2 text-[11px] text-slate-400 dark:text-slate-500">
            <span class="font-bold text-slate-600 dark:text-slate-300">ventas pagadas</span>
        </p>
    </a>

    {{-- 5. INGRESOS ANUALES --}}
    <a href="{{ route('admin.reportes') }}"
       class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs hover:shadow-md transition group block">
        <div class="flex items-center justify-between mb-4">
            <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="h-8 w-20 opacity-90">{!! $sparkline(collect($ventasMensuales)->pluck('total')->all(), '#a855f7', 'rgba(168,85,247,0.15)') !!}</div>
        </div>
        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ingresos del año</p>
        <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-1">${{ number_format($stats['ventas_anio'] ?? 0, 0, ',', '.') }}</p>
        <p class="mt-2 text-[11px] text-slate-400 dark:text-slate-500">
            <span class="font-bold text-slate-600 dark:text-slate-300">{{ now()->year }}</span>
        </p>
    </a>

</div>

{{-- GRÁFICOS: ventas por mes y estado de las ventas, lado a lado --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Gráfico de barras: dinero recibido por mes --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Ventas por mes</h2>
                <p class="text-xs text-slate-400 mt-0.5">Dinero recibido en {{ now()->year }} (solo ventas pagadas)</p>
            </div>
            <div class="relative w-full h-[220px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Desglose de Estado de Ventas --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-100">Estado de las ventas</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Todas las ventas según su estado</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-6">
                <div class="sm:col-span-5 relative flex items-center justify-center" style="height: 180px;">
                    <canvas id="statusChart"></canvas>
                    <div class="absolute text-center pointer-events-none">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-slate-100" id="totalVentas">{{ $totalV }}</p>
                    </div>
                </div>

                <div class="sm:col-span-7 space-y-3">
                    @foreach([
                        ['Pagadas',    $pagadas,    '#22c55e', 'bg-emerald-500', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        ['Pendientes', $pendientes, '#facc15', 'bg-amber-400',   'bg-amber-50 text-amber-700 border-amber-200'],
                        ['Canceladas', $canceladas, '#f87171', 'bg-rose-500',    'bg-rose-50 text-rose-700 border-rose-200'],
                    ] as [$name, $count, $hex, $bgCircle, $badgeClass])
                    <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50/60 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="h-2.5 w-2.5 rounded-full {{ $bgCircle }}"></span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden hidden sm:block">
                                <div class="h-full rounded-full" style="width:{{ $totalV > 0 ? round($count/$totalV*100) : 0 }}%; background:{{ $hex }}"></div>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg text-xs font-bold border {{ $badgeClass }}">
                                {{ $count }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

</div>

{{-- TABLA DE ÚLTIMAS VENTAS --}}
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Últimas ventas</h2>
            <p class="text-xs text-slate-400 mt-0.5">Las órdenes más recientes de la tienda</p>
        </div>
        <a href="{{ route('admin.ventas') }}" class="text-xs text-red-600 hover:text-red-700 font-bold flex items-center gap-1 transition">
            Ver todas las ventas
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-[11px] text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-3 font-semibold">Cliente</th>
                    <th class="px-6 py-3 font-semibold">Fecha y hora</th>
                    <th class="px-6 py-3 font-semibold text-center">Estado</th>
                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($ventasRecientes as $venta)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-slate-800 to-slate-700 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs ring-1 ring-slate-200">
                                {{ strtoupper(substr($venta->usuario->primer_nombre ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-100 text-xs">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $venta->usuario->correo ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 text-xs">
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</span>
                        <span class="text-slate-400 ml-1.5 text-[11px]">{{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-center">
                        @php
                            $ec = [
                                'pagada'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'cancelada' => 'bg-rose-50 text-rose-700 border-rose-200'
                            ];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $ec[$venta->estado] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-black text-slate-800 dark:text-slate-100 text-sm">
                        ${{ number_format($venta->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="h-10 w-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Todavía no hay ventas.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@php
    $mesesLabels = json_encode(array_column($ventasMensuales, 'mes'));
    $mesesData   = json_encode(array_column($ventasMensuales, 'total'));
    $mesesOrdenes = json_encode(array_column($ventasMensuales, 'ordenes'));
    $statusData  = json_encode([$pagadas, $pendientes, $canceladas]);
@endphp

{{-- ══════════════════════════════════════════════════════
     VISTA ASESOR
══════════════════════════════════════════════════════ --}}
@elseif($esAsesor)

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Productos', $stats['productos'], 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'bg-red-50 text-red-600'],
        ['Cursos disponibles', $stats['cursos'], 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg-indigo-50 text-indigo-600'],
        ['Ventas del mes', '$'.number_format($stats['ventas_mes'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-emerald-50 text-emerald-600'],
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
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Últimas ventas</h2>
        <a href="{{ route('admin.ventas') }}" class="text-xs text-red-600 hover:text-red-700 font-bold">Ver todas →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-[11px] text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-3 font-semibold">Cliente</th>
                    <th class="px-6 py-3 font-semibold">Fecha</th>
                    <th class="px-6 py-3 font-semibold text-center">Estado</th>
                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($ventasRecientes as $venta)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-6 py-3.5 font-medium text-slate-800 dark:text-slate-100">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</td>
                    <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3.5 text-center">
                        @php $ec=['pagada'=>'bg-emerald-50 text-emerald-700 border-emerald-200','pendiente'=>'bg-amber-50 text-amber-700 border-amber-200','cancelada'=>'bg-rose-50 text-rose-700 border-rose-200']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ec[$venta->estado]??'bg-slate-100 text-slate-600 border-slate-200' }}">{{ ucfirst($venta->estado) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-black text-slate-800 dark:text-slate-100">${{ number_format($venta->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs">No hay ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     VISTA GERENTE
══════════════════════════════════════════════════════ --}}
@elseif($esGerente)

{{-- Indicadores: 4 tarjetas para que la cuadrícula quede pareja (2 o 4 por fila) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Productos', number_format($stats['productos']), 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'bg-red-50 text-red-600'],
        ['Cursos', number_format($stats['cursos']), 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg-indigo-50 text-indigo-600'],
        ['Ventas del mes', '$'.number_format($stats['ventas_mes'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-emerald-50 text-emerald-600'],
        ['Ventas del año', '$'.number_format($stats['ventas_anio'],0,',','.'), 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'bg-purple-50 text-purple-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 flex items-center gap-4 shadow-xs min-w-0">
        <div class="rounded-xl p-3 shrink-0 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider truncate">{{ $label }}</p>
            <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-0.5 truncate">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Accesos a los módulos que consulta el Gerente --}}
<h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-3">Módulos</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Productos',  'Catálogo, precios e IVA',          route('admin.productos.index'),      'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',  'bg-red-50 text-red-600',         'hover:border-red-200'],
        ['Cursos',     'Cursos, fechas e inscritos',       route('admin.cursos.index'),         'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',     'bg-indigo-50 text-indigo-600',   'hover:border-indigo-200'],
        ['Ventas',     'Historial de ventas y pagos',      route('admin.ventas'),               'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',     'bg-emerald-50 text-emerald-600', 'hover:border-emerald-200'],
        ['Inventario', $stats['inventario_bajo'] > 0
                          ? $stats['inventario_bajo'] . ' producto(s) con stock bajo'
                          : 'Existencias por producto',   route('admin.inventario'),           'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'bg-amber-50 text-amber-600',     'hover:border-amber-200'],
        ['Reportes',   'Ingresos por mes y más vendidos',  route('admin.reportes'),             'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',   'bg-sky-50 text-sky-600',         'hover:border-sky-200'],
        ['Auditoría',  'Registro de acciones en el sistema', route('admin.auditoria.index'),    'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',  'bg-slate-100 text-slate-600',    'hover:border-slate-300'],
    ] as [$titulo, $detalle, $url, $icon, $color, $hover])
    <a href="{{ $url }}"
       class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 flex items-center gap-4 hover:shadow-md {{ $hover }} transition min-w-0">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center shrink-0 {{ $color }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $titulo }}</p>
            <p class="text-xs mt-0.5 truncate {{ $titulo === 'Inventario' && $stats['inventario_bajo'] > 0 ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">{{ $detalle }}</p>
        </div>
    </a>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════
     VISTA CLIENTE
══════════════════════════════════════════════════════ --}}
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
                        @php $ec=['pagada'=>'bg-emerald-50 text-emerald-700 border-emerald-200','pendiente'=>'bg-amber-50 text-amber-700 border-amber-200','cancelada'=>'bg-rose-50 text-rose-700 border-rose-200']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ec[$venta->estado]??'bg-slate-100 text-slate-600 border-slate-200' }}">{{ ucfirst($venta->estado) }}</span>
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

{{-- Script Chart.js solo para Admin --}}
@if($esAdmin)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mesesLabels = {!! $mesesLabels !!};
    const mesesData   = {!! $mesesData !!};
    const mesesOrdenes = {!! $mesesOrdenes !!};
    const statusData  = {!! $statusData !!};

    const totalEl = document.getElementById('totalVentas');
    if (totalEl) {
        totalEl.textContent = statusData.reduce((a, b) => a + b, 0);
    }

    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Pagadas', 'Pendientes', 'Canceladas'],
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed}`
                        }
                    }
                },
                cutout: '72%',
            }
        });
    }

    const salesCanvas = document.getElementById('salesChart');
    if (salesCanvas) {
        const pesos = v => '$' + Math.round(v).toLocaleString('es-CO');
        // Eje en millones o miles para que se lea fácil: $5 M, $500 mil
        const corto = v => v >= 1000000 ? '$' + (v / 1000000).toLocaleString('es-CO', { maximumFractionDigits: 1 }) + ' M'
                         : v >= 1000    ? '$' + Math.round(v / 1000) + ' mil'
                         : '$' + v;
        new Chart(salesCanvas, {
            type: 'bar',
            data: {
                labels: mesesLabels,
                datasets: [{
                    label: 'Dinero recibido',
                    data: mesesData,
                    backgroundColor: mesesLabels.map((_, i) => i === new Date().getMonth() ? '#dc2626' : '#fca5a5'),
                    hoverBackgroundColor: '#b91c1c',
                    borderRadius: 6,
                    maxBarThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0c1322',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: ctx => {
                                const n = mesesOrdenes[ctx.dataIndex] || 0;
                                return [pesos(ctx.parsed.y), n + (n === 1 ? ' venta pagada' : ' ventas pagadas')];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => corto(v), maxTicksLimit: 5 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endif

@endsection
