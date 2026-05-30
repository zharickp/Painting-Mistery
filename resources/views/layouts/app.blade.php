<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col">
            <div class="px-6 py-5 border-b border-gray-800">
                <span class="text-lg font-bold text-white">
                    Painting <span class="text-red-500">Mistery</span>
                </span>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="block px-3 py-2 rounded-md hover:bg-gray-800 hover:text-white transition">
                   Inicio
                </a>
                <span class="block px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">Productos (próximamente)</span>
                <span class="block px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">Inventario (próximamente)</span>
                <span class="block px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">Cursos (próximamente)</span>
                <span class="block px-3 py-2 rounded-md text-gray-500 cursor-not-allowed">Ventas (próximamente)</span>
            </nav>

            <div class="px-4 py-4 border-t border-gray-800">
                <p class="text-sm text-gray-400 mb-2 truncate px-2">
                    {{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }}
                </p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded-md transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- CONTENIDO --}}
        <div class="flex-1 flex flex-col">
            @if (session('success'))
                <div class="bg-green-100 text-green-800 px-6 py-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-800 px-6 py-3">{{ session('error') }}</div>
            @endif

            <main class="flex-1 p-8">
                @yield('content')
            </main>
        </div>

    </div>
</body>
</html>
