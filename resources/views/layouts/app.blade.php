<!DOCTYPE html>
<html lang="es" class="{{ '' }}"
      x-data="{ theme: localStorage.getItem('pm-theme') || 'light' }"
      x-init="document.documentElement.classList.remove('dark','rest'); if (theme === 'dark') document.documentElement.classList.add('dark'); if (theme === 'rest') document.documentElement.classList.add('rest');"
      :class="{ 'dark': theme === 'dark', 'rest': theme === 'rest' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery') — Panel</title>

    {{-- Favicon Painting Mistery --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="256x256" href="{{ asset('images/logo-painting-mistery.png') }}">

    {{-- Aplica tema antes del CSS para evitar flash --}}
    <script>
        (function() {
            var t = localStorage.getItem('pm-theme');
            if (t === 'dark') document.documentElement.classList.add('dark');
            if (t === 'rest') document.documentElement.classList.add('rest');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-slate-950 rest:bg-amber-50 transition-colors">
@php
    $u = auth()->user();
    $esAdmin   = $u?->tieneRol('Administrador');
    $esAsesor  = $u?->tieneRol('Asesor');
    $esGerente = $u?->tieneRol('Gerente');
    $esCliente = $u?->tieneRol('Cliente');
    // El módulo "Mis Cursos" solo aparece si el cliente ya tiene alguna
    // inscripción — no tiene sentido mostrarlo vacío a quien solo compra productos.
    $tieneCursos = $esCliente && $u->inscripciones()->exists();

    $productosOpen = request()->routeIs('admin.productos.*')
        || request()->routeIs('admin.tipo-iva.*')
        || request()->routeIs('admin.categorias.*');

    $adminOpen = request()->routeIs('admin.reportes')
        || request()->routeIs('admin.banners.*')
        || request()->routeIs('admin.usuarios.*')
        || request()->routeIs('admin.roles*')
        || request()->routeIs('admin.auditoria.*')
        || request()->routeIs('admin.respaldos.*')
        || request()->routeIs('admin.tarifas-envio.*');

    $verOperaciones = $esAdmin || $esAsesor || $esGerente;
@endphp

<div class="flex h-screen overflow-hidden"
     x-data="{
        sidebarMobile: false,
        notiOpen: false,
        userOpen: false,
        themeMenuOpen: false,
        notiCount: 0,
        setTheme(t) {
            this.$root.__x?.$data && (this.$root.__x.$data.theme = t);
            document.documentElement.classList.remove('dark','rest');
            if (t === 'dark') document.documentElement.classList.add('dark');
            if (t === 'rest') document.documentElement.classList.add('rest');
            localStorage.setItem('pm-theme', t);
            this.themeMenuOpen = false;
        }
     }"
     @keydown.escape.window="sidebarMobile = false; notiOpen = false; userOpen = false; themeMenuOpen = false">

    {{-- ═════════════════════════════════════════════════════════════════
         SIDEBAR
    ═════════════════════════════════════════════════════════════════ --}}
    <aside class="fixed md:sticky md:top-0 inset-y-0 left-0 z-40 w-64 shrink-0
                  bg-slate-950 dark:bg-black rest:bg-stone-900 text-slate-300 flex flex-col h-screen
                  transform transition-transform duration-200
                  border-r border-slate-800/60"
           :class="sidebarMobile ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        {{-- Logo + marca (integrado al sidebar) --}}
        <div class="px-5 py-4 border-b border-slate-800/60">
            <a href="{{ route('inicio') }}" class="flex items-center gap-3 min-w-0 group">
                <div class="h-10 w-10 rounded-full overflow-hidden bg-slate-900 ring-2 ring-red-500/40 shrink-0">
                    <img src="{{ asset('images/logo-painting-mistery.png') }}"
                         alt="Painting Mistery" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0 leading-none">
                    <p class="text-[15px] font-extrabold text-white truncate">
                        Painting <span class="text-red-500">Mistery</span>
                    </p>
                    <p class="text-[9px] font-semibold text-slate-500 tracking-[0.22em] uppercase mt-1.5 truncate">
                        Panel Administrativo
                    </p>
                </div>
            </a>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-3 space-y-1 text-sm">

            {{-- INICIO --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('dashboard')
                        ? 'bg-red-600/95 text-white shadow-lg shadow-red-900/30'
                        : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium">Inicio</span>
            </a>

            @if($verOperaciones)
            {{-- OPERACIONES --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em]">Operaciones</p>
            </div>

            {{-- PRODUCTOS (grupo desplegable) --}}
            <div x-data="{ open: {{ $productosOpen ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-left
                               {{ $productosOpen ? 'text-white bg-slate-800/60' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <span class="flex-1 font-medium">Productos</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 ml-4 pl-3 border-l border-slate-800 space-y-1">
                    <a href="{{ route('admin.productos.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.productos.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                        Productos
                    </a>
                    @if($esAdmin)
                    <a href="{{ route('admin.tipo-iva.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.tipo-iva.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        Tipo de IVA
                    </a>
                    <a href="{{ route('admin.categorias.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.categorias.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Categorías
                    </a>
                    @endif
                </div>
            </div>

            {{-- CURSOS --}}
            <a href="{{ route('admin.cursos.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.cursos.*') ? 'bg-red-600/95 text-white shadow-lg shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium">Cursos</span>
            </a>

            {{-- RESEÑAS DEL SITIO --}}
            @if(auth()->user()->tieneRol('Administrador', 'Asesor', 'Gerente'))
            <a href="{{ route('admin.resenas-sitio.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.resenas-sitio.*') ? 'bg-red-600/95 text-white shadow-lg shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <span class="font-medium">Reseñas</span>
            </a>
            @endif

            {{-- VENTAS --}}
            <a href="{{ route('admin.ventas') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.ventas') ? 'bg-red-600/95 text-white shadow-lg shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="font-medium">Ventas</span>
            </a>

            {{-- INVENTARIO --}}
            <a href="{{ route('admin.inventario') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.inventario') ? 'bg-red-600/95 text-white shadow-lg shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <span class="font-medium">Inventario</span>
            </a>
            @endif

            @if($esAdmin || $esGerente)
            {{-- GESTIÓN --}}
            <div class="pt-4 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em]">Gestión del sistema</p>
            </div>

            {{-- ADMINISTRATIVO (grupo desplegable) --}}
            <div x-data="{ open: {{ $adminOpen ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-left
                               {{ $adminOpen ? 'text-white bg-slate-800/60' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="flex-1 font-medium">Administrativo</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-1 ml-4 pl-3 border-l border-slate-800 space-y-1">
                    <a href="{{ route('admin.reportes') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.reportes') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Reportes
                    </a>
                    @if($esAdmin)
                    <a href="{{ route('admin.banners.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.banners.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16M4 6a2 2 0 00-2 2v8a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2M4 6a2 2 0 012-2h12a2 2 0 012 2"/>
                        </svg>
                        Banners
                    </a>
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.usuarios.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Usuarios
                    </a>
                    <a href="{{ route('admin.roles') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.roles*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Roles y permisos
                    </a>
                    @endif
                    <a href="{{ route('admin.auditoria.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.auditoria.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Auditoría
                    </a>
                    @if($esAdmin)
                    <a href="{{ route('admin.tarifas-envio.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.tarifas-envio.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                        </svg>
                        Tarifas de envío
                    </a>
                    <a href="{{ route('admin.respaldos.index') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13px] transition
                              {{ request()->routeIs('admin.respaldos.*') ? 'text-white bg-red-600/85' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                        Copias de seguridad
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if($esCliente)
            <div class="pt-4 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em]">Mi cuenta</p>
            </div>
            <a href="{{ route('mi-cuenta.inicio') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('mi-cuenta.inicio') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium">Inicio</span>
            </a>
            <a href="{{ route('mi-cuenta.pedidos') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('mi-cuenta.pedidos') || request()->routeIs('mi-cuenta.pedido') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="font-medium">Mis Pedidos</span>
            </a>
            @if($tieneCursos)
            <a href="{{ route('cliente.cursos') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('cliente.cursos') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <span class="font-medium">Mis Cursos</span>
            </a>
            @endif
            @endif
        </nav>

        {{-- Toggle de tema (Claro / Oscuro / Descanso) --}}
        <div class="px-3 py-3 border-t border-slate-800/60 relative" x-data="{ localOpen: false }" @click.outside="localOpen = false">
            <button @click="localOpen = !localOpen"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800/70 hover:text-white transition">
                <template x-if="theme === 'light'">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </template>
                <template x-if="theme === 'dark'">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </template>
                <template x-if="theme === 'rest'">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4m0 0l4-4m-4 4l4 4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>
                </template>
                <span class="flex-1 text-left text-sm font-medium"
                      x-text="theme === 'dark' ? 'Modo oscuro' : (theme === 'rest' ? 'Modo descanso' : 'Modo claro')"></span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                </svg>
            </button>

            <div x-show="localOpen" x-transition x-cloak
                 class="absolute left-3 right-3 bottom-16 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl overflow-hidden z-50">
                <button @click="setTheme('light'); localOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800 transition"
                        :class="theme === 'light' ? 'text-white bg-slate-800/60' : ''">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Modo claro
                </button>
                <button @click="setTheme('dark'); localOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800 transition"
                        :class="theme === 'dark' ? 'text-white bg-slate-800/60' : ''">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    Modo oscuro
                </button>
                <button @click="setTheme('rest'); localOpen = false"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800 transition"
                        :class="theme === 'rest' ? 'text-white bg-slate-800/60' : ''">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    Modo descanso
                </button>
            </div>
        </div>
    </aside>

    {{-- Backdrop móvil --}}
    <div x-show="sidebarMobile" x-transition.opacity @click="sidebarMobile = false"
         class="fixed inset-0 bg-slate-900/60 z-30 md:hidden" x-cloak></div>

    {{-- ═════════════════════════════════════════════════════════════════
         MAIN
    ═════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">

        {{-- HEADER --}}
        <header class="shrink-0 bg-white dark:bg-slate-900 rest:bg-amber-100/80 border-b border-slate-200 dark:border-slate-800 rest:border-amber-200
                       px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button @click="sidebarMobile = !sidebarMobile"
                        class="md:hidden p-2 -ml-2 text-slate-500 dark:text-slate-400 rounded-md">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Buscador --}}
                <div class="relative flex-1 max-w-md hidden sm:block">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" placeholder="Buscar en el sistema..."
                           class="w-full pl-9 pr-3 py-2 text-sm rounded-xl bg-slate-100 dark:bg-slate-800 rest:bg-amber-50 border border-transparent focus:border-red-400 focus:bg-white dark:focus:bg-slate-900 focus:outline-none text-slate-700 dark:text-slate-200 rest:text-stone-800 placeholder-slate-400">
                </div>
            </div>

            <div class="flex items-center gap-2">

                {{-- Notificaciones --}}
                <div class="relative">
                    <button @click="notiOpen = !notiOpen; userOpen = false"
                            class="relative p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                            aria-label="Notificaciones">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900"></span>
                    </button>

                    <div x-show="notiOpen" x-transition x-cloak @click.outside="notiOpen = false"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Notificaciones</p>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider">Al día</span>
                        </div>
                        <div class="px-4 py-8 text-center">
                            <svg class="h-10 w-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400">No tienes notificaciones nuevas.</p>
                        </div>
                    </div>
                </div>

                {{-- Perfil --}}
                <div class="relative">
                    <button @click="userOpen = !userOpen; notiOpen = false"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr($u->primer_nombre, 0, 1)) }}{{ strtoupper(substr($u->primer_apellido, 0, 1)) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left leading-tight">
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate max-w-[160px]">
                                {{ $u->nombreCompleto() }}
                            </span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[160px]">
                                {{ $u->roles->pluck('nombre')->join(', ') ?: 'Usuario' }}
                            </span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="userOpen" x-transition x-cloak @click.outside="userOpen = false"
                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $u->nombreCompleto() }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $u->correo }}</p>
                            <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-bold uppercase tracking-wider text-red-700 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full">
                                {{ $u->roles->pluck('nombre')->join(', ') ?: 'Usuario' }}
                            </span>
                        </div>
                        <a href="{{ route('inicio') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Ir a la tienda
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-slate-800">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto pm-scroll bg-slate-50 dark:bg-slate-950 rest:bg-amber-50 text-slate-800 dark:text-slate-100 rest:text-stone-800">
            @if (session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-500 text-emerald-800 dark:text-emerald-200 px-6 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-800 dark:text-red-200 px-6 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
</body>
</html>
