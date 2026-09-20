<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery') — Panel</title>

    {{-- Favicon Painting Mistery --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo-painting-mistery.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-painting-mistery.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-painting-mistery.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ══════════════════════════════════════════════════════
           Scrollbar delgado y elegante (WebKit + Firefox)
           ══════════════════════════════════════════════════════ */
        .pm-scroll { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
        .pm-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .pm-scroll::-webkit-scrollbar-track { background: transparent; }
        .pm-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 8px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        .pm-scroll::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

        .pm-scroll-dark { scrollbar-width: thin; scrollbar-color: #374151 transparent; }
        .pm-scroll-dark::-webkit-scrollbar { width: 6px; }
        .pm-scroll-dark::-webkit-scrollbar-track { background: transparent; }
        .pm-scroll-dark::-webkit-scrollbar-thumb {
            background-color: #374151;
            border-radius: 8px;
        }
        .pm-scroll-dark::-webkit-scrollbar-thumb:hover { background-color: #4b5563; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100">
@php
    $u = auth()->user();
    $esAdmin   = $u?->tieneRol('Administrador');
    $esAsesor  = $u?->tieneRol('Asesor');
    $esGerente = $u?->tieneRol('Gerente');
    $esCliente = $u?->tieneRol('Cliente');

    // Grupos desplegables (auto-expand según la ruta activa)
    $productosOpen = request()->routeIs('admin.productos.*')
        || request()->routeIs('admin.tipo-iva.*')
        || request()->routeIs('admin.categorias.*');

    $adminOpen = request()->routeIs('admin.reportes')
        || request()->routeIs('admin.banners.*')
        || request()->routeIs('admin.usuarios.*')
        || request()->routeIs('admin.roles*')
        || request()->routeIs('admin.auditoria.*')
        || request()->routeIs('admin.respaldos.*');

    $verOperaciones = $esAdmin || $esAsesor || $esGerente;
    $puedeEscribir  = $esAdmin || $esAsesor;
@endphp

<div class="flex h-screen overflow-hidden"
     x-data="{ sidebarMobile: false, notiOpen: false, userOpen: false, notiCount: 0 }"
     @keydown.escape.window="sidebarMobile = false; notiOpen = false; userOpen = false">

    {{-- ═════════════════════════════════════════════════════════════════
         SIDEBAR
    ═════════════════════════════════════════════════════════════════ --}}
    <aside class="fixed md:sticky md:top-0 inset-y-0 left-0 z-40 w-64 shrink-0
                  bg-slate-900 text-slate-300 flex flex-col h-screen
                  transform transition-transform duration-200
                  {{ '' }}"
           :class="sidebarMobile ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        {{-- Logo integrado al sidebar (sin fondo blanco) --}}
        <div class="px-5 py-4 flex items-center gap-3 border-b border-slate-800/60">
            <a href="{{ route('inicio') }}" class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-full overflow-hidden ring-2 ring-red-500/50 shrink-0 bg-slate-950">
                    <img src="{{ asset('images/logo-painting-mistery.png') }}"
                         onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s';"
                         alt="Painting Mistery" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-white leading-tight truncate">
                        Painting <span class="text-red-500">Mistery</span>
                    </p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 mt-0.5">Panel administrativo</p>
                </div>
            </a>
        </div>

        {{-- Navegación (con scroll propio) --}}
        <nav class="flex-1 overflow-y-auto pm-scroll-dark px-3 py-4 space-y-0.5 text-sm">

            {{-- ─── PRINCIPAL ─── --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('dashboard') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            @if($verOperaciones)
            {{-- ═════ OPERACIONES ═════ --}}
            <div class="pt-5 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em]">Operaciones</p>
            </div>

            {{-- PRODUCTOS (grupo desplegable) --}}
            <div x-data="{ open: {{ $productosOpen ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-left
                               {{ $productosOpen ? 'text-white bg-slate-800/60' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <span class="flex-1 font-medium">Productos</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-0.5 ml-3 pl-3 border-l border-slate-800 space-y-0.5">
                    <a href="{{ route('admin.productos.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.productos.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.productos.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Productos
                    </a>
                    @if($esAdmin)
                    <a href="{{ route('admin.tipo-iva.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.tipo-iva.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.tipo-iva.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Tipo de IVA
                    </a>
                    <a href="{{ route('admin.categorias.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.categorias.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.categorias.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Categorías
                    </a>
                    @endif
                </div>
            </div>

            {{-- CURSOS --}}
            <a href="{{ route('admin.cursos.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('admin.cursos.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium">Cursos</span>
            </a>

            {{-- VENTAS --}}
            <a href="{{ route('admin.ventas') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('admin.ventas') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="font-medium">Ventas</span>
            </a>

            {{-- INVENTARIO --}}
            <a href="{{ route('admin.inventario') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('admin.inventario') ? 'bg-red-600 text-white shadow-md shadow-red-900/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <span class="font-medium">Inventario</span>
            </a>
            @endif

            {{-- ═════ GESTIÓN DEL SISTEMA ═════ --}}
            @if($esAdmin || $esGerente)
            <div class="pt-5 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em]">Gestión del sistema</p>
            </div>

            {{-- ADMINISTRATIVO (grupo desplegable) --}}
            <div x-data="{ open: {{ $adminOpen ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition text-left
                               {{ $adminOpen ? 'text-white bg-slate-800/60' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="flex-1 font-medium">Administrativo</span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mt-0.5 ml-3 pl-3 border-l border-slate-800 space-y-0.5">
                    <a href="{{ route('admin.reportes') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.reportes') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.reportes') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Reportes
                    </a>
                    @if($esAdmin)
                    <a href="{{ route('admin.banners.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.banners.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.banners.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Banners
                    </a>
                    <a href="{{ route('admin.usuarios.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.usuarios.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.usuarios.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Usuarios
                    </a>
                    <a href="{{ route('admin.roles') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.roles*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.roles*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Roles y permisos
                    </a>
                    @endif
                    <a href="{{ route('admin.auditoria.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.auditoria.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.auditoria.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Auditoría
                    </a>
                    @if($esAdmin)
                    <a href="{{ route('admin.respaldos.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-[13px] transition
                              {{ request()->routeIs('admin.respaldos.*') ? 'text-white bg-red-600/90' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs('admin.respaldos.*') ? 'bg-white' : 'bg-slate-600' }}"></span>
                        Copias de seguridad
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- ═════ CLIENTE ═════ --}}
            @if($esCliente)
            <div class="pt-5 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em]">Mi cuenta</p>
            </div>
            <a href="{{ route('cliente.pedidos') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('cliente.pedidos') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="font-medium">Mis Pedidos</span>
            </a>
            <a href="{{ route('cliente.cursos') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('cliente.cursos') ? 'bg-red-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <span class="font-medium">Mis Cursos</span>
            </a>
            @endif
        </nav>

        {{-- Footer sidebar --}}
        <div class="px-4 py-3 border-t border-slate-800/60">
            <a href="{{ route('inicio') }}"
               class="flex items-center gap-2 text-xs text-slate-500 hover:text-white transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Ir a la tienda
            </a>
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
        <header class="shrink-0 bg-white border-b border-slate-200 px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

            {{-- Izquierda: botón móvil + título --}}
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarMobile = !sidebarMobile"
                        class="md:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 rounded-md">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base font-bold text-slate-800 truncate">
                    @yield('title', 'Dashboard')
                </h1>
            </div>

            {{-- Derecha: notificaciones + perfil --}}
            <div class="flex items-center gap-2">

                {{-- Notificaciones --}}
                <div class="relative">
                    <button @click="notiOpen = !notiOpen; userOpen = false"
                            class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition"
                            aria-label="Notificaciones">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="notiCount > 0"
                              class="absolute top-1 right-1 h-4 min-w-[16px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold flex items-center justify-center"
                              x-text="notiCount"></span>
                    </button>

                    {{-- Panel dropdown notificaciones --}}
                    <div x-show="notiOpen" x-transition x-cloak @click.outside="notiOpen = false"
                         class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-800">Notificaciones</p>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider">Al día</span>
                        </div>
                        <div class="px-4 py-8 text-center">
                            <svg class="h-10 w-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-xs text-slate-500">No tienes notificaciones nuevas.</p>
                        </div>
                    </div>
                </div>

                {{-- Perfil --}}
                <div class="relative">
                    <button @click="userOpen = !userOpen; notiOpen = false"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr($u->primer_nombre, 0, 1)) }}{{ strtoupper(substr($u->primer_apellido, 0, 1)) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left leading-tight">
                            <span class="text-sm font-semibold text-slate-800 truncate max-w-[160px]">
                                {{ $u->nombreCompleto() }}
                            </span>
                            <span class="text-[11px] text-slate-500 truncate max-w-[160px]">
                                {{ $u->roles->pluck('nombre')->join(', ') ?: 'Usuario' }}
                            </span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown usuario --}}
                    <div x-show="userOpen" x-transition x-cloak @click.outside="userOpen = false"
                         class="absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $u->nombreCompleto() }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $u->correo }}</p>
                            <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-bold uppercase tracking-wider text-red-700 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full">
                                {{ $u->roles->pluck('nombre')->join(', ') ?: 'Usuario' }}
                            </span>
                        </div>
                        <a href="{{ route('inicio') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Ir a la tienda
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition text-left">
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

        {{-- CONTENT (con scroll propio) --}}
        <main class="flex-1 overflow-y-auto pm-scroll">
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-6 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <div class="p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
    </div>

</div>

{{-- Alpine.js con plugin collapse para animar submenús (collapse debe ir antes que alpine para registrarse) --}}
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
</body>
</html>
