<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery') — Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
<div class="min-h-screen flex">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside id="sidebar"
           class="w-64 bg-gray-900 text-gray-300 flex flex-col fixed inset-y-0 left-0 z-30 transform -translate-x-full md:translate-x-0 transition-transform duration-200">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-gray-800 flex items-center gap-3">
            <a href="{{ route('inicio') }}" class="flex items-center gap-2">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                     alt="Logo" class="h-9 w-9 rounded-full object-cover">
                <span class="text-base font-bold text-white">
                    Painting <span class="text-red-500">Mistery</span>
                </span>
            </a>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">

            {{-- INICIO --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Inicio
            </a>

            {{-- ADMIN --}}
            @if(auth()->user()->tieneRol('Administrador'))
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Administración</p>
            </div>

            <a href="{{ route('admin.usuarios') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.usuarios') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Usuarios
            </a>

            <a href="{{ route('admin.roles') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.roles') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Roles y Permisos
            </a>

            <a href="{{ route('admin.reportes') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.reportes') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Reportes
            </a>

            <a href="{{ route('mayorista.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('mayorista.*') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Módulo Mayorista
            </a>
            @endif

            {{-- CATÁLOGO (admin + asesor) --}}
            @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catálogo</p>
            </div>

            <a href="{{ route('admin.productos') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.productos') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                Productos
            </a>

            <a href="{{ route('admin.inventario') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.inventario') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                Inventario
            </a>

            <a href="{{ route('admin.cursos') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.cursos') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Cursos
            </a>

            <a href="{{ route('admin.ventas') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.ventas') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Ventas
            </a>
            @endif

            {{-- CLIENTE --}}
            @if(auth()->user()->tieneRol('Cliente'))
            <div class="pt-4 pb-1 px-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Mi cuenta</p>
            </div>

            <a href="{{ route('cliente.pedidos') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('cliente.pedidos') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Mis Pedidos
            </a>

            <a href="{{ route('cliente.cursos') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('cliente.cursos') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }} transition">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                Mis Cursos
            </a>
            @endif

        </nav>

        {{-- Usuario / Logout --}}
        <div class="px-4 py-4 border-t border-gray-800">
            <div class="flex items-center gap-3 mb-3 px-1">
                <div class="h-8 w-8 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->primer_nombre, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm text-white font-medium truncate">
                        {{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->correo }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded-md transition flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ===================== MAIN ===================== --}}
    <div class="flex-1 flex flex-col md:ml-64 min-h-screen">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between sticky top-0 z-20">
            {{-- Mobile menu button --}}
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')"
                    class="md:hidden text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h2 class="text-sm font-semibold text-gray-700 hidden md:block">@yield('title', 'Dashboard')</h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('inicio') }}" class="text-xs text-gray-400 hover:text-red-600 transition hidden sm:block">
                    Ver tienda →
                </a>
                <span class="text-xs font-medium px-2 py-1 bg-red-50 text-red-600 rounded-full">
                    {{ auth()->user()->roles->pluck('nombre')->join(', ') }}
                </span>
            </div>
        </header>

        {{-- Alertas --}}
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-3 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-3 text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</div>
</body>
</html>
