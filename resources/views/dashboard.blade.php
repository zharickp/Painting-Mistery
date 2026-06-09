@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ══════════════════════════════════════════════════════
     CABECERA ESTILO DUSTY
══════════════════════════════════════════════════════ --}}
<div class="mb-6">
    <p class="text-xs text-gray-400 mb-1">
        Painting Mistery
        <span class="mx-1 text-gray-300">/</span>
        Panel
        <span class="mx-1 text-gray-300">/</span>
        <span class="text-gray-600">Dashboard</span>
    </p>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->translatedFormat('d \d\e F, Y') }}
            &nbsp;·&nbsp;
            <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">
                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                {{ auth()->user()->roles->pluck('nombre')->join(' · ') }}
            </span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     PANEL ADMINISTRADOR
══════════════════════════════════════════════════════ --}}
@if($esAdmin)

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
    @php
    $cards = [
        [
            'label'   => 'Total Ventas hoy',
            'value'   => '$'.number_format($stats['ventas_hoy'],0,',','.'),
            'trend'   => '+0%',
            'up'      => true,
            'note'    => 'respecto a ayer',
            'color'   => '#22c55e',
            'bg'      => 'bg-green-50',
            'icon'    => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label'   => 'Total Órdenes',
            'value'   => $stats['ordenes_mes'],
            'trend'   => 'este mes',
            'up'      => true,
            'note'    => 'órdenes registradas',
            'color'   => '#6366f1',
            'bg'      => 'bg-indigo-50',
            'icon'    => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        ],
        [
            'label'   => 'Nuevos Clientes',
            'value'   => $stats['usuarios'],
            'trend'   => 'total',
            'up'      => true,
            'note'    => 'usuarios en sistema',
            'color'   => '#f59e0b',
            'bg'      => 'bg-yellow-50',
            'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        ],
        [
            'label'   => 'Ingresos Totales',
            'value'   => '$'.number_format($stats['ventas_mes'],0,',','.'),
            'trend'   => 'este mes',
            'up'      => true,
            'note'    => 'ingresos del mes',
            'color'   => '#dc2626',
            'bg'      => 'bg-red-50',
            'icon'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
        [
            'label'   => 'Ingresos Anuales',
            'value'   => '$'.number_format($stats['ventas_anio'],0,',','.'),
            'trend'   => now()->year,
            'up'      => true,
            'note'    => 'acumulado del año',
            'color'   => '#8b5cf6',
            'bg'      => 'bg-purple-50',
            'icon'    => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
        ],
    ];
    @endphp

    @foreach($cards as $c)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-start justify-between mb-3">
            <div class="rounded-xl p-2.5 {{ $c['bg'] }}">
                <svg class="h-5 w-5" style="color:{{ $c['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
                </svg>
            </div>
            <svg class="h-8 w-16 opacity-40" viewBox="0 0 64 32" fill="none">
                <polyline points="0,28 12,20 24,24 36,12 48,16 64,4"
                          stroke="{{ $c['color'] }}" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <p class="text-xs text-gray-400 mb-0.5 truncate">{{ $c['label'] }}</p>
        <p class="text-xl font-extrabold text-gray-800 truncate">{{ $c['value'] }}</p>
        <p class="text-xs mt-1 truncate" style="color:{{ $c['color'] }}">
            {{ $c['trend'] }}
            <span class="text-gray-400 font-normal hidden sm:inline">· {{ $c['note'] }}</span>
        </p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="mb-4">
            <h2 class="font-bold text-gray-800">Categorías más vendidas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Distribución de ventas por estado</p>
        </div>
        <div class="relative flex items-center justify-center mb-4" style="height:180px;">
            <canvas id="statusChart"></canvas>
            <div class="absolute text-center pointer-events-none">
                <p class="text-xs text-gray-400">TOTAL</p>
                <p class="text-2xl font-extrabold text-gray-800" id="totalVentas">0</p>
            </div>
        </div>
        <div class="space-y-2.5 mt-2">
            @php
                $pagadas    = \App\Models\Venta::where('estado','pagada')->count();
                $pendientes = \App\Models\Venta::where('estado','pendiente')->count();
                $canceladas = \App\Models\Venta::where('estado','cancelada')->count();
                $totalV     = $pagadas + $pendientes + $canceladas;
            @endphp
            @foreach([
                ['Pagadas',    $pagadas,    '#22c55e', 'bg-green-500'],
                ['Pendientes', $pendientes, '#facc15', 'bg-yellow-400'],
                ['Canceladas', $canceladas, '#f87171', 'bg-red-400'],
            ] as [$name, $count, $hex, $bg])
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full {{ $bg }}"></span>
                    <span class="text-sm text-gray-600">{{ $name }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $totalV > 0 ? round($count/$totalV*100) : 0 }}%; background:{{ $hex }}"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-800 w-5 text-right">{{ $count }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="font-bold text-gray-800">Ventas en el tiempo</h2>
                <p class="text-xs text-gray-400 mt-0.5">Ingresos {{ now()->year }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span class="h-2 w-5 rounded-full bg-red-500 inline-block"></span>Ingresos</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-5 rounded-full bg-indigo-400 inline-block"></span>Órdenes</span>
            </div>
        </div>
        <canvas id="salesChart" height="170"></canvas>
    </div>
</div>

{{-- Cards rápidos --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-xs text-gray-400">Productos en catálogo</p>
            <p class="text-2xl font-extrabold text-gray-800">{{ $stats['productos'] }}</p>
        </div>
        <a href="{{ route('admin.productos.index') }}"
           class="text-xs bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition font-medium whitespace-nowrap">
            Gestionar →
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-xs text-gray-400">Categorías activas</p>
            <p class="text-2xl font-extrabold text-gray-800">{{ $stats['categorias'] }}</p>
        </div>
        <a href="{{ route('admin.categorias.index') }}"
           class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded-lg hover:bg-orange-600 transition font-medium whitespace-nowrap">
            Gestionar →
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-xs text-gray-400">Cursos disponibles</p>
            <p class="text-2xl font-extrabold text-gray-800">{{ $stats['cursos'] }}</p>
        </div>
        <a href="{{ route('admin.cursos.index') }}"
           class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition font-medium whitespace-nowrap">
            Gestionar →
        </a>
    </div>
</div>

{{-- Últimas ventas --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-gray-800">Últimas ventas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Transacciones más recientes</p>
        </div>
        <a href="{{ route('admin.ventas') }}" class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
            Ver todas
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs text-gray-400 uppercase tracking-wide">
                    <th class="px-6 py-3 font-semibold">Cliente</th>
                    <th class="px-6 py-3 font-semibold">Fecha</th>
                    <th class="px-6 py-3 font-semibold">Estado</th>
                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ventasRecientes as $venta)
                <tr class="hover:bg-red-50/30 transition">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($venta->usuario->primer_nombre ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</p>
                                <p class="text-xs text-gray-400">{{ $venta->usuario->correo ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-gray-500 text-xs">
                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}<br>
                        <span class="text-gray-300">{{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</span>
                    </td>
                    <td class="px-6 py-3.5">
                        @php $ec=['pagada'=>'bg-green-100 text-green-700 border-green-200','pendiente'=>'bg-yellow-100 text-yellow-700 border-yellow-200','cancelada'=>'bg-red-100 text-red-700 border-red-200']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $ec[$venta->estado] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-extrabold text-gray-800">
                        ${{ number_format($venta->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center">
                        <svg class="h-10 w-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-400 text-sm">No hay ventas registradas aún.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Módulo Mayorista --}}
<div class="rounded-xl overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #1f0000 100%);">
    <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 bg-red-600/20 text-red-400 text-xs font-bold px-2.5 py-1 rounded-full mb-2">
                <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                Módulo especial
            </span>
            <h3 class="text-xl font-bold text-white">Módulo Mayorista</h3>
            <p class="text-gray-400 text-sm mt-1">Gestiona pedidos al por mayor, precios especiales y clientes mayoristas.</p>
        </div>
        <a href="{{ route('mayorista.index') }}"
           class="flex-shrink-0 bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-red-900/30">
            Ir al módulo →
        </a>
    </div>
</div>

@php
    $mesesLabels = json_encode(array_column($ventasMensuales, 'mes'));
    $mesesData   = json_encode(array_column($ventasMensuales, 'total'));
    $statusData  = json_encode([$pagadas, $pendientes, $canceladas]);
@endphp

{{-- ══════════════════════════════════════════════════════
     PANEL ASESOR
══════════════════════════════════════════════════════ --}}
@elseif($esAsesor)

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Productos activos', $stats['productos'], 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'bg-red-50 text-red-600'],
        ['Cursos activos',    $stats['cursos'],    'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg-indigo-50 text-indigo-600'],
        ['Ventas del mes',    '$'.number_format($stats['ventas_mes'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-green-50 text-green-600'],
        ['Órdenes mes',       $stats['ordenes_mes'], 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'bg-orange-50 text-orange-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition">
        <div class="rounded-xl p-3 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ $label }}</p>
            <p class="text-2xl font-extrabold text-gray-800">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <h2 class="font-bold text-gray-800">Últimas ventas</h2>
        <a href="{{ route('admin.ventas') }}" class="text-xs text-red-600 hover:underline font-medium">Ver todas →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs text-gray-400 uppercase tracking-wide">
                    <th class="px-6 py-3 font-semibold">Cliente</th>
                    <th class="px-6 py-3 font-semibold">Fecha</th>
                    <th class="px-6 py-3 font-semibold">Estado</th>
                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ventasRecientes as $venta)
                <tr class="hover:bg-red-50/20 transition">
                    <td class="px-6 py-3.5 font-medium text-gray-800">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</td>
                    <td class="px-6 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3.5">
                        @php $ec=['pagada'=>'bg-green-100 text-green-700','pendiente'=>'bg-yellow-100 text-yellow-700','cancelada'=>'bg-red-100 text-red-700']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ec[$venta->estado]??'bg-gray-100 text-gray-600' }}">{{ ucfirst($venta->estado) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-extrabold text-gray-800">${{ number_format($venta->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No hay ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     PANEL GERENTE
══════════════════════════════════════════════════════ --}}
@elseif($esGerente)

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Productos activos', $stats['productos'],  'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'bg-red-50 text-red-600'],
        ['Cursos activos',    $stats['cursos'],     'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg-indigo-50 text-indigo-600'],
        ['Ventas del mes',    '$'.number_format($stats['ventas_mes'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-green-50 text-green-600'],
        ['Ventas del año',    '$'.number_format($stats['ventas_anio'],0,',','.'), 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'bg-purple-50 text-purple-600'],
        ['Órdenes del mes',   $stats['ordenes_mes'], 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'bg-orange-50 text-orange-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition">
        <div class="rounded-xl p-3 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400">{{ $label }}</p>
            <p class="text-2xl font-extrabold text-gray-800">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-2 gap-4 mb-6">
    <a href="{{ route('admin.productos.index') }}"
       class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:border-red-200 transition">
        <div class="h-12 w-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-gray-800">Ver productos</p>
            <p class="text-xs text-gray-400 mt-0.5">Catálogo completo</p>
        </div>
    </a>
    <a href="{{ route('admin.cursos.index') }}"
       class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md hover:border-indigo-200 transition">
        <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-gray-800">Ver cursos</p>
            <p class="text-xs text-gray-400 mt-0.5">Oferta académica</p>
        </div>
    </a>
</div>

{{-- ══════════════════════════════════════════════════════
     PANEL CLIENTE
══════════════════════════════════════════════════════ --}}
@else

<div class="bg-gradient-to-r from-red-600 to-red-800 rounded-2xl p-6 mb-6 text-white flex items-center gap-6">
    <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-extrabold flex-shrink-0">
        {{ strtoupper(substr(auth()->user()->primer_nombre,0,1)) }}
    </div>
    <div>
        <p class="text-red-200 text-xs font-semibold uppercase tracking-widest mb-1">Bienvenido de vuelta</p>
        <h2 class="text-xl font-extrabold">{{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }}</h2>
        <p class="text-red-200 text-sm mt-1">{{ auth()->user()->correo }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Mis pedidos',   $stats['mis_pedidos'],                              'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'bg-red-50 text-red-600'],
        ['Mis cursos',    $stats['mis_cursos'],                               'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'bg-indigo-50 text-indigo-600'],
        ['Total gastado', '$'.number_format($stats['total_gastado'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-green-50 text-green-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 flex items-center gap-4 hover:shadow-md transition">
        <div class="h-12 w-12 rounded-xl {{ explode(' ',$color)[0] }} flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400 mb-0.5">{{ $label }}</p>
            <p class="text-2xl font-extrabold text-gray-800">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50">
        <h2 class="font-bold text-gray-800">Mis últimos pedidos</h2>
        <p class="text-xs text-gray-400 mt-0.5">Historial de tus compras</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-xs text-gray-400 uppercase tracking-wide">
                    <th class="px-6 py-3 font-semibold"># Orden</th>
                    <th class="px-6 py-3 font-semibold">Fecha</th>
                    <th class="px-6 py-3 font-semibold">Estado</th>
                    <th class="px-6 py-3 font-semibold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($misVentas as $venta)
                <tr class="hover:bg-red-50/20 transition">
                    <td class="px-6 py-3.5 font-mono text-gray-500 text-xs">#{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-3.5 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3.5">
                        @php $ec=['pagada'=>'bg-green-100 text-green-700','pendiente'=>'bg-yellow-100 text-yellow-700','cancelada'=>'bg-red-100 text-red-700']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $ec[$venta->estado]??'bg-gray-100 text-gray-600' }}">{{ ucfirst($venta->estado) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-extrabold text-gray-800">${{ number_format($venta->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="h-12 w-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-gray-400 text-sm font-medium">Aún no tienes pedidos</p>
                        <a href="{{ route('inicio') }}" class="mt-3 inline-block text-red-600 text-xs hover:underline">Ver productos →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif

{{-- Chart.js solo Admin --}}
@if($esAdmin)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const mesesLabels = {!! $mesesLabels !!};
const mesesData   = {!! $mesesData !!};
const statusData  = {!! $statusData !!};

document.getElementById('totalVentas').textContent = statusData.reduce((a,b)=>a+b, 0);

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pagadas','Pendientes','Canceladas'],
        datasets: [{
            data: statusData,
            backgroundColor: ['#22c55e','#facc15','#f87171'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: {
            label: ctx => ` ${ctx.label}: ${ctx.parsed}`
        }}},
        cutout: '72%',
    }
});

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: mesesLabels,
        datasets: [
            {
                label: 'Ingresos',
                data: mesesData,
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220,38,38,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.45,
                pointBackgroundColor: '#dc2626',
                pointRadius: 4,
                pointHoverRadius: 6,
            },
            {
                label: 'Órdenes',
                data: mesesLabels.map((_, i) => Math.round((mesesData[i] || 0) / 50000) || 0),
                borderColor: '#818cf8',
                backgroundColor: 'rgba(129,140,248,0.06)',
                borderWidth: 2,
                fill: true,
                tension: 0.45,
                pointBackgroundColor: '#818cf8',
                pointRadius: 4,
                pointHoverRadius: 6,
                yAxisID: 'y2',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#f9fafb',
                bodyColor: '#d1d5db',
                padding: 10,
                cornerRadius: 8,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f3f4f6' },
                ticks: { color: '#9ca3af', callback: v => '$' + v.toLocaleString() }
            },
            y2: {
                position: 'right',
                beginAtZero: true,
                grid: { display: false },
                ticks: { color: '#818cf8' }
            },
            x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
        }
    }
});
</script>
@endif

@endsection
