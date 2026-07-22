@php
    $enlaceProductos = request()->routeIs('inicio') ? '#productos' : route('tienda.index');
    $enlaceCursos    = request()->routeIs('inicio') ? '#cursos' : route('inicio') . '#cursos';
    $enlaceNosotros  = request()->routeIs('inicio') ? '#sobre-nosotros' : route('inicio') . '#sobre-nosotros';
    $enlaceContacto  = request()->routeIs('inicio') ? '#contacto' : route('inicio') . '#contacto';
@endphp
{{-- NAVBAR --}}
<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            {{-- Brand --}}
            <a href="{{ route('inicio') }}" class="flex items-center gap-3 group">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                     alt="Logo" class="h-10 w-10 rounded-full object-cover border-2 border-red-100 group-hover:border-red-400 transition">
                <span class="text-lg font-bold text-gray-900 group-hover:text-red-600 transition">
                    Painting <span class="text-red-600">Mistery</span>
                </span>
            </a>

            {{-- Centro: links --}}
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('inicio') }}" class="hover:text-red-600 transition {{ request()->routeIs('inicio') ? 'text-red-600' : '' }}">Inicio</a>
                <a href="{{ $enlaceProductos }}" class="hover:text-red-600 transition {{ request()->routeIs('tienda.index') ? 'text-red-600' : '' }}">Tienda</a>
                <a href="{{ $enlaceCursos }}" class="hover:text-red-600 transition">Academia</a>
                <a href="{{ $enlaceNosotros }}" class="hover:text-red-600 transition">Nosotros</a>
                <a href="{{ $enlaceContacto }}" class="hover:text-red-600 transition">Contacto</a>
            </div>

            {{-- Derecha: barra búsqueda + iconos --}}
            <div class="flex items-center gap-2">

                {{-- Búsqueda: envía directo al catálogo --}}
                <form action="{{ route('tienda.index') }}" method="GET" class="relative hidden sm:flex items-center" id="searchWrapper">
                    <input id="searchInput" name="buscar" type="text" placeholder="Buscar productos..." value="{{ request('buscar') }}"
                           class="w-0 overflow-hidden opacity-0 transition-all duration-300 text-sm border border-gray-200 rounded-full px-4 py-1.5 focus:outline-none focus:border-red-400"
                           style="transition: width 0.3s ease, opacity 0.3s ease;">
                    <button type="button" onclick="toggleSearch()"
                            class="ml-1 p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>

                {{-- Lista de deseos --}}
                <button onclick="abrirWishlist()"
                   class="relative p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Lista de deseos">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span id="navWishBadge" class="hidden absolute -top-0.5 -right-0.5 bg-red-600 text-white text-[9px] font-bold rounded-full h-4 w-4 flex items-center justify-center leading-none">0</span>
                </button>

                {{-- Carrito --}}
                <button onclick="abrirCarrito()"
                   class="relative p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Carrito">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span id="navCartBadge" class="hidden absolute -top-0.5 -right-0.5 bg-red-600 text-white text-[9px] font-bold rounded-full h-4 w-4 flex items-center justify-center leading-none">0</span>
                </button>

                @auth
                {{-- Icono usuario --}}
                <a href="{{ route('dashboard') }}"
                   class="p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Mi cuenta">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </a>
                @else
                {{-- Login icon --}}
                <a href="{{ route('login') }}"
                   class="p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Iniciar sesión">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </a>
                {{-- Register icon --}}
                <a href="{{ route('register') }}"
                   class="p-2 rounded-full bg-red-600 text-white hover:bg-red-700 transition" title="Registrarse">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </a>
                @endauth

                {{-- Mobile menu --}}
                <button class="md:hidden p-2 text-gray-500 hover:text-red-600" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown --}}
        <div id="mobileMenu" class="hidden md:hidden pb-3 space-y-1 text-sm font-medium text-gray-600">
            <a href="{{ route('inicio') }}" class="block px-2 py-2 hover:text-red-600">Inicio</a>
            <a href="{{ $enlaceProductos }}" class="block px-2 py-2 hover:text-red-600">Tienda</a>
            <a href="{{ $enlaceCursos }}" class="block px-2 py-2 hover:text-red-600">Academia</a>
            <a href="{{ $enlaceNosotros }}" class="block px-2 py-2 hover:text-red-600">Nosotros</a>
            <a href="{{ $enlaceContacto }}" class="block px-2 py-2 hover:text-red-600">Contacto</a>
            @guest
            <a href="{{ route('login') }}" class="block px-2 py-2 hover:text-red-600">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="block px-2 py-2 text-red-600 font-semibold">Registrarse</a>
            @endguest
        </div>
    </div>
</nav>

<script>
function toggleSearch() {
    const input = document.getElementById('searchInput');
    const isOpen = input.style.width === '200px';
    input.style.width  = isOpen ? '0' : '200px';
    input.style.opacity = isOpen ? '0' : '1';
    if (!isOpen) input.focus();
}
</script>
