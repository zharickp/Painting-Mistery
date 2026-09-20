@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ══════════════════════════════════════════════════════
     CABECERA + BANNER DECORATIVO
══════════════════════════════════════════════════════ --}}
<div class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Título --}}
    <div class="lg:col-span-2">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-red-600 to-red-800 flex items-center justify-center text-white shadow-lg shadow-red-950/20 shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 rest:text-stone-800 tracking-tight">Panel de Control</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 rest:text-stone-600 mt-0.5">Resumen general del sistema</p>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 bg-white dark:bg-slate-800 rest:bg-amber-100 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 rest:border-amber-200 text-xs text-slate-500 dark:text-slate-300 rest:text-stone-700">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <span class="inline-flex items-center gap-1.5 bg-red-50 dark:bg-red-950/40 border border-red-100 dark:border-red-900 text-red-700 dark:text-red-400 text-xs font-bold px-3 py-1.5 rounded-xl">
                <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                {{ auth()->user()->roles->pluck('nombre')->join(' · ') }}
            </span>
        </div>
    </div>

    {{-- Banner decorativo "Ideas que se convierten en arte" --}}
    <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-red-600 via-red-700 to-slate-900 dark:from-red-700 dark:via-red-900 dark:to-black rest:from-amber-600 rest:via-amber-800 rest:to-stone-900 p-6 flex items-center justify-between shadow-lg">
        <div class="absolute -right-6 -bottom-6 w-40 h-40 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-4 -top-4 w-32 h-32 bg-red-400/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative">
            <p class="text-white/70 text-[10px] font-bold uppercase tracking-widest">Painting Mistery</p>
            <p class="text-white font-bold text-lg leading-tight mt-1">Ideas que se</p>
            <p class="text-white font-bold text-lg leading-tight italic">convierten en arte</p>
        </div>
        <div class="relative h-16 w-16 shrink-0 rounded-2xl overflow-hidden ring-2 ring-white/20">
            <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="PM" class="w-full h-full object-cover">
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
@endphp

{{-- 5 TARJETAS PRINCIPALES DEL SISTEMA: PRODUCTOS, CURSOS, VENTAS, INVENTARIO, USUARIOS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">

    {{-- 1. PRODUCTOS --}}
    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 rounded-2xl border border-slate-200/80 dark:border-slate-800 rest:border-amber-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
        <div class="flex items-start justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800/40 flex items-center justify-center text-red-600 dark:text-red-400 group-hover:scale-105 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <a href="{{ route('admin.productos.index') }}" class="text-[11px] font-semibold text-slate-400 hover:text-red-600 flex items-center gap-1 transition">
                Ver más →
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Productos</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($stats['productos'] ?? 0) }}</p>
            <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                <span>{{ $stats['categorias'] ?? 0 }} categorías activas</span>
            </div>
        </div>
    </div>

    {{-- 2. CURSOS --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
        <div class="flex items-start justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-105 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <a href="{{ route('admin.cursos.index') }}" class="text-[11px] font-semibold text-slate-400 hover:text-indigo-600 flex items-center gap-1 transition">
                Ver más →
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cursos</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($stats['cursos'] ?? 0) }}</p>
            <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                <span>Oferta académica activa</span>
            </div>
        </div>
    </div>

    {{-- 3. VENTAS --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
        <div class="flex items-start justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <a href="{{ route('admin.ventas') }}" class="text-[11px] font-semibold text-slate-400 hover:text-emerald-600 flex items-center gap-1 transition">
                Ver más →
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ventas del Mes</p>
            <p class="text-2xl font-black text-slate-800 mt-1">${{ number_format($stats['ventas_mes'] ?? 0, 0, ',', '.') }}</p>
            <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                <span>{{ $stats['ordenes_mes'] ?? 0 }} órdenes en {{ now()->translatedFormat('F') }}</span>
            </div>
        </div>
    </div>

    {{-- 4. INVENTARIO --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
        <div class="flex items-start justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-105 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <a href="{{ route('admin.inventario') }}" class="text-[11px] font-semibold text-slate-400 hover:text-amber-600 flex items-center gap-1 transition">
                Ver más →
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Inventario</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($stats['inventario'] ?? 0) }} <span class="text-xs font-medium text-slate-400">uds</span></p>
            <div class="mt-2.5 flex items-center gap-1.5 text-xs">
                @if(($stats['inventario_bajo'] ?? 0) > 0)
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    <span class="text-rose-600 font-semibold">{{ $stats['inventario_bajo'] }} con stock mínimo</span>
                @else
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-500">Stock en niveles óptimos</span>
                @endif
            </div>
        </div>
    </div>

    {{-- 5. USUARIOS --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
        <div class="flex items-start justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-105 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <a href="{{ route('admin.usuarios.index') }}" class="text-[11px] font-semibold text-slate-400 hover:text-purple-600 flex items-center gap-1 transition">
                Ver más →
            </a>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Usuarios</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($stats['usuarios'] ?? 0) }}</p>
            <div class="mt-2.5 flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-purple-500"></span>
                <span>Clientes y administradores</span>
            </div>
        </div>
    </div>

</div>

{{-- SECCIÓN CENTRAL: GRÁFICOS Y ACCESOS DIRECTOS (SIMILAR A IMAGEN DE REFERENCIA) --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">

    {{-- Gráficos de Ventas (8 columnas) --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Gráfico Línea: Ventas en el tiempo --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Ventas en el tiempo</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Comportamiento mensual de ingresos en el año {{ now()->year }}</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>
                        Ingresos ($)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                        Órdenes
                    </span>
                </div>
            </div>
            <div class="relative w-full h-[220px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Desglose de Estado de Ventas --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Estado de los Pedidos</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Distribución total de ventas según su estado</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-6">
                <div class="sm:col-span-5 relative flex items-center justify-center" style="height: 180px;">
                    <canvas id="statusChart"></canvas>
                    <div class="absolute text-center pointer-events-none">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL</p>
                        <p class="text-2xl font-black text-slate-800" id="totalVentas">{{ $totalV }}</p>
                    </div>
                </div>

                <div class="sm:col-span-7 space-y-3">
                    @foreach([
                        ['Pagadas',    $pagadas,    '#22c55e', 'bg-emerald-500', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        ['Pendientes', $pendientes, '#facc15', 'bg-amber-400',   'bg-amber-50 text-amber-700 border-amber-200'],
                        ['Canceladas', $canceladas, '#f87171', 'bg-rose-500',    'bg-rose-50 text-rose-700 border-rose-200'],
                    ] as [$name, $count, $hex, $bgCircle, $badgeClass])
                    <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50/60 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="h-2.5 w-2.5 rounded-full {{ $bgCircle }}"></span>
                            <span class="text-xs font-semibold text-slate-700">{{ $name }}</span>
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

    {{-- Panel Lateral: Accesos Directos (4 columnas - Inspirado en Imagen 1) --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Tarjeta: Accesos Directos --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <div class="mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Accesos Directos</h3>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Módulos</span>
            </div>

            <div class="space-y-2">
                {{-- Ventas --}}
                <a href="{{ route('admin.ventas') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                            🛒
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition">Ventas</p>
                            <p class="text-[11px] text-slate-400">Consultar pedidos y estados</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                {{-- Inventario --}}
                <a href="{{ route('admin.inventario') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                            📦
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition">Inventario</p>
                            <p class="text-[11px] text-slate-400">Stock, entradas y niveles</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                {{-- Cursos --}}
                <a href="{{ route('admin.cursos.index') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                            📚
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition">Cursos</p>
                            <p class="text-[11px] text-slate-400">Gestión de cursos académicos</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                {{-- Reportes --}}
                <a href="{{ route('admin.reportes') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            📊
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition">Reportes</p>
                            <p class="text-[11px] text-slate-400">Informes de ingresos y ventas</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                {{-- Usuarios --}}
                <a href="{{ route('admin.usuarios.index') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                            👥
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold text-slate-800 group-hover:text-red-600 transition">Usuarios</p>
                            <p class="text-[11px] text-slate-400">Control de usuarios y cuentas</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>

</div>

{{-- TABLA DE ÚLTIMAS VENTAS --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Transacciones Recientes</h2>
            <p class="text-xs text-slate-400 mt-0.5">Últimas órdenes registradas en la tienda</p>
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
                    <th class="px-6 py-3 font-semibold">Fecha y Hora</th>
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
                                <p class="font-bold text-slate-800 text-xs">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $venta->usuario->correo ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-slate-500 text-xs">
                        <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</span>
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
                    <td class="px-6 py-3.5 text-right font-black text-slate-800 text-sm">
                        ${{ number_format($venta->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="h-10 w-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">No hay ventas registradas aún en el sistema.</p>
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
    $statusData  = json_encode([$pagadas, $pendientes, $canceladas]);
@endphp

{{-- ══════════════════════════════════════════════════════
     VISTA ASESOR
══════════════════════════════════════════════════════ --}}
@elseif($esAsesor)

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Productos Activos', $stats['productos'], 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'bg-red-50 text-red-600'],
        ['Cursos Disponibles', $stats['cursos'], 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg-indigo-50 text-indigo-600'],
        ['Ventas del Mes', '$'.number_format($stats['ventas_mes'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-emerald-50 text-emerald-600'],
        ['Inventario Total', number_format($stats['inventario']).' uds', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'bg-amber-50 text-amber-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 flex items-center gap-4 shadow-xs hover:shadow-md transition">
        <div class="rounded-xl p-3 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800">Últimas Ventas</h2>
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
                    <td class="px-6 py-3.5 font-medium text-slate-800">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</td>
                    <td class="px-6 py-3.5 text-slate-500 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3.5 text-center">
                        @php $ec=['pagada'=>'bg-emerald-50 text-emerald-700 border-emerald-200','pendiente'=>'bg-amber-50 text-amber-700 border-amber-200','cancelada'=>'bg-rose-50 text-rose-700 border-rose-200']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ec[$venta->estado]??'bg-slate-100 text-slate-600 border-slate-200' }}">{{ ucfirst($venta->estado) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-black text-slate-800">${{ number_format($venta->total,0,',','.') }}</td>
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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
    @foreach([
        ['Productos Activos', $stats['productos'], 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'bg-red-50 text-red-600'],
        ['Cursos Activos', $stats['cursos'], 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg-indigo-50 text-indigo-600'],
        ['Ventas del Mes', '$'.number_format($stats['ventas_mes'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-emerald-50 text-emerald-600'],
        ['Inventario Total', number_format($stats['inventario']).' uds', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'bg-amber-50 text-amber-600'],
        ['Ventas del Año', '$'.number_format($stats['ventas_anio'],0,',','.'), 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'bg-purple-50 text-purple-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 flex items-center gap-4 shadow-xs hover:shadow-md transition">
        <div class="rounded-xl p-3 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <a href="{{ route('admin.productos.index') }}"
       class="bg-white rounded-2xl border border-slate-200/80 p-5 flex items-center gap-4 hover:shadow-md hover:border-red-200 transition">
        <div class="h-12 w-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-slate-800 text-sm">Gestionar Productos</p>
            <p class="text-xs text-slate-400 mt-0.5">Catálogo general y precios</p>
        </div>
    </a>
    <a href="{{ route('admin.cursos.index') }}"
       class="bg-white rounded-2xl border border-slate-200/80 p-5 flex items-center gap-4 hover:shadow-md hover:border-indigo-200 transition">
        <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div>
            <p class="font-bold text-slate-800 text-sm">Gestionar Cursos</p>
            <p class="text-xs text-slate-400 mt-0.5">Oferta académica y cursos</p>
        </div>
    </a>
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
        ['Mis Pedidos',   $stats['mis_pedidos'], 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'bg-red-50 text-red-600'],
        ['Mis Cursos',    $stats['mis_cursos'], 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'bg-indigo-50 text-indigo-600'],
        ['Total Compras', '$'.number_format($stats['total_gastado'],0,',','.'), 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg-emerald-50 text-emerald-600'],
    ] as [$label, $value, $icon, $color])
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 flex items-center gap-4 shadow-xs hover:shadow-md transition">
        <div class="rounded-xl p-3 {{ explode(' ',$color)[0] }}">
            <svg class="h-6 w-6 {{ explode(' ',$color)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-2xl font-black text-slate-800 mt-0.5">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="text-sm font-bold text-slate-800">Mis últimos pedidos</h2>
        <p class="text-xs text-slate-400 mt-0.5">Historial de tus compras realizadas</p>
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
                    <td class="px-6 py-3.5 font-mono text-slate-500 text-xs font-semibold">#{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-3.5 text-slate-500 text-xs">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3.5 text-center">
                        @php $ec=['pagada'=>'bg-emerald-50 text-emerald-700 border-emerald-200','pendiente'=>'bg-amber-50 text-amber-700 border-amber-200','cancelada'=>'bg-rose-50 text-rose-700 border-rose-200']; @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ec[$venta->estado]??'bg-slate-100 text-slate-600 border-slate-200' }}">{{ ucfirst($venta->estado) }}</span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-black text-slate-800">${{ number_format($venta->total,0,',','.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="h-10 w-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <p class="text-slate-400 text-xs font-medium">Aún no tienes pedidos registrados.</p>
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
        new Chart(salesCanvas, {
            type: 'line',
            data: {
                labels: mesesLabels,
                datasets: [
                    {
                        label: 'Ingresos ($)',
                        data: mesesData,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#dc2626',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Órdenes',
                        data: mesesLabels.map((_, i) => Math.round((mesesData[i] || 0) / 50000) || 0),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.05)',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        yAxisID: 'y2',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0c1322',
                        titleColor: '#ffffff',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => '$' + v.toLocaleString() }
                    },
                    y2: {
                        position: 'right',
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { color: '#6366f1', font: { size: 11 } }
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
