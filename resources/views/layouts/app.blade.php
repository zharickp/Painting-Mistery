<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery') — Panel Administrativo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased overflow-hidden" x-data="{ sidebarOpen: false }">
@php
    $u = auth()->user();
    $esAdmin   = $u?->tieneRol('Administrador');
    $esAsesor  = $u?->tieneRol('Asesor');
    $esGerente = $u?->tieneRol('Gerente');
    $esCliente = $u?->tieneRol('Cliente');
    $verCatalogoConsulta = $esAdmin || $esAsesor || $esGerente;
    $puedeEscribirCatalogo = $esAdmin || $esAsesor;

    // Estados activos para acordeones
    $isProductosActive = request()->routeIs('admin.productos.*') || request()->routeIs('admin.tipo-iva.*') || request()->routeIs('admin.categorias.*');
    $isAdministrativoActive = request()->routeIs('admin.reportes') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.usuarios.*') || request()->routeIs('admin.roles*') || request()->routeIs('admin.auditoria.*') || request()->routeIs('admin.respaldos.*');
@endphp

<div class="h-screen flex overflow-hidden">

    {{-- Backdrop para móviles --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 md:hidden"
         x-cloak></div>

    {{-- ===================== SIDEBAR FIJO ===================== --}}
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed md:static inset-y-0 left-0 z-50 w-64 xl:w-72 bg-[#0c1322] text-slate-300 flex flex-col h-screen border-r border-slate-800/80 shrink-0 transition-transform duration-200 ease-in-out shadow-2xl md:shadow-none">

        {{-- Brand / Logo --}}
        <div class="px-5 py-4 border-b border-slate-800/80 flex items-center justify-between shrink-0 bg-[#0a101d]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="relative">
                    <img src="{{ asset('images/logo-painting-mistery.png') }}"
                         onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s';"
                         alt="Painting Mistery"
                         class="h-10 w-10 rounded-xl object-cover ring-2 ring-red-600/30 group-hover:ring-red-500/60 transition shadow-md">
                    <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-[#0c1322]"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight text-white leading-tight">
                        Painting <span class="text-red-500">Mistery</span>
                    </span>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Panel de Control
                    </span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navegación con scroll independiente --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3.5 py-4 space-y-1 text-sm font-medium">

            {{-- Resumen / Dashboard --}}
            <div class="px-2 pt-1 pb-1">
                <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">General</p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- ================= GESTIÓN COMERCIAL Y OPERATIVA ================= --}}
            @if($verCatalogoConsulta)
            <div class="px-2 pt-4 pb-1">
                <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Operaciones</p>
            </div>

            {{-- 1. PRODUCTOS (Módulo Desplegable con Submódulos) --}}
            <div x-data="{ open: {{ $isProductosActive ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-150 {{ $isProductosActive ? 'bg-slate-800/90 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 {{ $isProductosActive ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        <span>Productos</span>
                    </div>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200 text-slate-400"
                         :class="open ? 'transform rotate-180 text-white' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Submódulos de Productos con sangría y guías --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-4 pr-1 py-1 space-y-1 relative"
                     x-cloak>
                    <div class="absolute left-6 top-1 bottom-1 w-px bg-slate-800"></div>

                    {{-- Submódulo: Productos --}}
                    <a href="{{ route('admin.productos.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.productos.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.productos.*') ? 'bg-red-500 ring-2 ring-red-400/40' : 'bg-slate-600' }}"></span>
                        <span>Catálogo Productos</span>
                    </a>

                    {{-- Submódulo: Tipo IVA --}}
                    <a href="{{ route('admin.tipo-iva.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.tipo-iva.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.tipo-iva.*') ? 'bg-red-500 ring-2 ring-red-400/40' : 'bg-slate-600' }}"></span>
                        <span>Tipo IVA</span>
                    </a>

                    {{-- Submódulo: Categorías --}}
                    <a href="{{ route('admin.categorias.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.categorias.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.categorias.*') ? 'bg-red-500 ring-2 ring-red-400/40' : 'bg-slate-600' }}"></span>
                        <span>Categorías</span>
                    </a>
                </div>
            </div>

            {{-- 2. CURSOS (Módulo Independiente) --}}
            <a href="{{ route('admin.cursos.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.cursos.*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.cursos.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Cursos</span>
            </a>

            {{-- 3. VENTAS (Módulo Independiente) --}}
            <a href="{{ route('admin.ventas') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.ventas') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.ventas') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Ventas</span>
            </a>

            {{-- 4. INVENTARIO (Módulo Independiente) --}}
            <a href="{{ route('admin.inventario') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.inventario') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.inventario') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <span>Inventario</span>
            </a>
            @endif

            {{-- ================= ADMINISTRATIVO (SECCIÓN PRINCIPAL DESPLEGABLE) ================= --}}
            @if($esAdmin || $esGerente)
            <div class="px-2 pt-4 pb-1">
                <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Gestión del Sistema</p>
            </div>

            <div x-data="{ open: {{ $isAdministrativoActive ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-150 {{ $isAdministrativoActive ? 'bg-slate-800/90 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 {{ $isAdministrativoActive ? 'text-red-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Administrativo</span>
                    </div>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200 text-slate-400"
                         :class="open ? 'transform rotate-180 text-white' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Submódulos de Administrativo con sangría y guías visuales --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="pl-4 pr-1 py-1 space-y-1 relative"
                     x-cloak>
                    <div class="absolute left-6 top-1 bottom-1 w-px bg-slate-800"></div>

                    {{-- 1. 📊 Reportes --}}
                    <a href="{{ route('admin.reportes') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.reportes') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="text-sm">📊</span>
                        <span>Reportes</span>
                    </a>

                    {{-- 2. 🖼️ Banners (solo Admin) --}}
                    @if($esAdmin)
                    <a href="{{ route('admin.banners.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.banners.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="text-sm">🖼️</span>
                        <span>Banners</span>
                    </a>
                    @endif

                    {{-- 3. 👥 Usuarios (solo Admin) --}}
                    @if($esAdmin)
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.usuarios.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="text-sm">👥</span>
                        <span>Usuarios</span>
                    </a>

                    {{-- 4. 🔐 Roles y Permisos (solo Admin) --}}
                    <a href="{{ route('admin.roles') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.roles') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="text-sm">🔐</span>
                        <span>Roles y permisos</span>
                    </a>
                    @endif

                    {{-- 5. 📋 Auditoría (Admin + Gerente) --}}
                    <a href="{{ route('admin.auditoria.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.auditoria.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="text-sm">📋</span>
                        <span>Auditoría</span>
                    </a>

                    {{-- 6. 💾 Copias de Seguridad (solo Admin) --}}
                    @if($esAdmin)
                    <a href="{{ route('admin.respaldos.index') }}"
                       class="flex items-center gap-3 pl-6 pr-3 py-2 rounded-lg text-xs transition-all relative {{ request()->routeIs('admin.respaldos.*') ? 'bg-red-600/15 text-red-400 font-semibold border border-red-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <span class="text-sm">💾</span>
                        <span>Copias de seguridad</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- ================= MÓDULO MAYORISTA ================= --}}
            @if($esAdmin || $esGerente)
            <div class="pt-2">
                <a href="{{ route('mayorista.index') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl border border-red-900/50 bg-gradient-to-r from-red-950/40 to-slate-900/40 hover:from-red-900/40 hover:to-slate-800/60 text-slate-300 hover:text-white transition-all group">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-red-400 group-hover:text-red-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-xs font-semibold">Módulo Mayorista</span>
                    </div>
                    <span class="text-[10px] bg-red-600/30 text-red-300 border border-red-500/40 px-1.5 py-0.5 rounded font-bold uppercase">Pro</span>
                </a>
            </div>
            @endif

            {{-- ================= ROL CLIENTE ================= --}}
            @if($esCliente)
            <div class="px-2 pt-4 pb-1">
                <p class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Mi cuenta</p>
            </div>

            <a href="{{ route('cliente.pedidos') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('cliente.pedidos') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Mis Pedidos</span>
            </a>

            <a href="{{ route('cliente.cursos') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('cliente.cursos') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-900/30 font-semibold' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <span>Mis Cursos</span>
            </a>
            @endif

        </nav>

        {{-- Footer de Sidebar --}}
        <div class="p-3 border-t border-slate-800/80 shrink-0 bg-[#0a101d]">
            <a href="{{ route('inicio') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800/60 transition group">
                <div class="flex items-center gap-2.5">
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-red-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Volver a la tienda</span>
                </div>
                <svg class="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </aside>

    {{-- ===================== CONTENEDOR PRINCIPAL ===================== --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">

        {{-- Header Superior Fijo --}}
        <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between shrink-0 z-20 shadow-xs">
            <div class="flex items-center gap-3">
                {{-- Botón menú móvil --}}
                <button @click="sidebarOpen = true"
                        class="md:hidden text-slate-500 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-100 transition focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <span class="hidden sm:inline font-semibold text-slate-600">Painting Mistery</span>
                    <span class="hidden sm:inline text-slate-300">/</span>
                    <h2 class="text-sm font-bold text-slate-800 truncate">@yield('title', 'Dashboard')</h2>
                </div>
            </div>

            {{-- Perfil de Usuario y Acciones --}}
            <div class="flex items-center gap-3">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            type="button"
                            class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 transition border border-transparent hover:border-slate-200 focus:outline-none">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-red-600 to-rose-500 flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-red-500/20">
                            {{ strtoupper(substr($u?->primer_nombre ?? 'U', 0, 1)) }}{{ strtoupper(substr($u?->primer_apellido ?? 'M', 0, 1)) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left leading-tight pr-1">
                            <span class="text-xs font-bold text-slate-800">{{ $u?->nombreCompleto() ?? 'Usuario' }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">
                                {{ $u?->roles->pluck('nombre')->join(', ') ?? 'Invitado' }}
                            </span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-60 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 text-xs z-30 divide-y divide-slate-100"
                         x-cloak>
                        <div class="px-4 py-3">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $u?->nombreCompleto() }}</p>
                            <p class="text-slate-400 truncate mt-0.5">{{ $u?->correo }}</p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded-md bg-red-50 text-red-600 text-[10px] font-bold border border-red-100">
                                {{ $u?->roles->pluck('nombre')->join(' · ') }}
                            </span>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('inicio') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-slate-700 font-medium transition">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Ir a la tienda
                            </a>
                        </div>
                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 font-semibold flex items-center gap-2 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Área de Alertas Flash --}}
        @if (session('success'))
            <div class="bg-emerald-50 border-b border-emerald-200 text-emerald-800 px-6 py-3 text-xs font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-rose-50 border-b border-rose-200 text-rose-800 px-6 py-3 text-xs font-semibold flex items-center gap-2">
                <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ================= CONTENIDO PRINCIPAL CON SCROLL INDEPENDIENTE ================= --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50/70 pb-16">
            @yield('content')
        </main>
    </div>

</div>

{{-- Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>
</html>
