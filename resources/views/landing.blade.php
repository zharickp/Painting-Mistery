@extends('layouts.guest')

@section('title', 'Painting Mistery - Accesorios y cursos para tu moto')

@section('content')
<div class="bg-white">

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
                    <a href="{{ route('inicio') }}" class="hover:text-red-600 transition">Inicio</a>
                    <a href="#productos" class="hover:text-red-600 transition">Tienda</a>
                    <a href="#cursos" class="hover:text-red-600 transition">Academia</a>
                    <a href="#sobre-nosotros" class="hover:text-red-600 transition">Nosotros</a>
                    <a href="#contacto" class="hover:text-red-600 transition">Contacto</a>
                </div>

                {{-- Derecha: barra búsqueda + iconos --}}
                <div class="flex items-center gap-2">

                    {{-- Búsqueda expandible --}}
                    <div class="relative hidden sm:flex items-center" id="searchWrapper">
                        <input id="searchInput" type="text" placeholder="Buscar productos..."
                               class="w-0 overflow-hidden opacity-0 transition-all duration-300 text-sm border border-gray-200 rounded-full px-4 py-1.5 focus:outline-none focus:border-red-400"
                               style="transition: width 0.3s ease, opacity 0.3s ease;">
                        <button onclick="toggleSearch()"
                                class="ml-1 p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Carrito --}}
                    @auth
                    <a href="{{ route('dashboard') }}"
                       class="relative p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Carrito">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </a>

                    {{-- Icono usuario --}}
                    <a href="{{ route('dashboard') }}"
                       class="p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Mi cuenta">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                    @else

                    {{-- Carrito (invitado) --}}
                    <a href="{{ route('login') }}"
                       class="relative p-2 rounded-full text-gray-500 hover:text-red-600 hover:bg-red-50 transition" title="Carrito">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </a>

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
                <a href="#productos" class="block px-2 py-2 hover:text-red-600">Tienda</a>
                <a href="#cursos" class="block px-2 py-2 hover:text-red-600">Academia</a>
                <a href="#sobre-nosotros" class="block px-2 py-2 hover:text-red-600">Nosotros</a>
                <a href="#contacto" class="block px-2 py-2 hover:text-red-600">Contacto</a>
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

    {{-- HERO --}}
    <section>
        {{-- Imagen sola, sin nada encima --}}
        <div style="height: 70vh; overflow: hidden;">
            <img src="/images/hero.jpeg"
                 alt="Painting Mistery"
                 style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
        </div>

        {{-- Texto completamente separado, debajo --}}
        <div style="background-color: #111827; padding: 40px 24px;">
            <div class="max-w-7xl mx-auto">
                <span class="inline-flex items-center gap-2 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full mb-4 uppercase tracking-widest">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Especialistas en pintura automotriz
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-3 text-white">
                    Tu moto, <span class="text-red-500">tu estilo.</span>
                </h1>
                <p class="text-gray-300 text-base md:text-lg max-w-xl">
                    Accesorios, repuestos y personalización para tu moto.
                    Aprende a pintar y reparar con nuestros cursos especializados.
                </p>
            </div>
        </div>
    </section>

    {{-- NOSOTROS --}}
    <section id="nosotros" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">¿Quiénes somos?</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Painting Mistery</h2>
                <p class="max-w-2xl mx-auto text-gray-500 mt-4 text-base leading-relaxed">
                    Combinamos la venta de accesorios y repuestos con
                    formación práctica en pintura y reparación automotriz.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-7 rounded-2xl border border-gray-100 hover:border-red-200 hover:shadow-lg transition group">
                    <div class="bg-red-100 group-hover:bg-red-600 rounded-xl h-14 w-14 flex items-center justify-center mb-5 transition">
                        <svg class="h-7 w-7 text-red-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Accesorios y repuestos</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Encuentra las piezas que tu moto necesita. Calidad garantizada para todo tipo de modelos.
                    </p>
                </div>
                <div class="p-7 rounded-2xl border border-gray-100 hover:border-red-200 hover:shadow-lg transition group">
                    <div class="bg-red-100 group-hover:bg-red-600 rounded-xl h-14 w-14 flex items-center justify-center mb-5 transition">
                        <svg class="h-7 w-7 text-red-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Personalización</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Dale tu toque único con técnicas de pintura automotriz profesional.
                    </p>
                </div>
                <div class="p-7 rounded-2xl border border-gray-100 hover:border-red-200 hover:shadow-lg transition group">
                    <div class="bg-red-100 group-hover:bg-red-600 rounded-xl h-14 w-14 flex items-center justify-center mb-5 transition">
                        <svg class="h-7 w-7 text-red-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800 mb-2">Cursos especializados</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Aprende pintura y reparación automotriz con nosotros.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- PRODUCTOS --}}
    <section id="productos" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Catálogo</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">Nuestros productos</h2>
            </div>
            @if ($productosDestacados->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                    <svg class="h-14 w-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-600 mb-1">Catálogo en preparación</h3>
                    <p class="text-gray-400 text-sm">Muy pronto tendremos nuestros productos disponibles.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                    @foreach ($productosDestacados as $producto)
                        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition group">
                            <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
                                @if ($producto->imagen)
                                    <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}"
                                         class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <svg class="h-14 w-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $producto->nombre }}</h3>
                                @if ($producto->categoria)
                                    <span class="inline-block bg-red-50 text-red-600 text-xs px-2 py-0.5 rounded-full mb-2">
                                        {{ $producto->categoria->nombre ?? '' }}
                                    </span>
                                @endif
                                <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ $producto->descripcion ?? '' }}</p>
                                <p class="text-red-600 font-bold">${{ number_format($producto->precio, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- CURSOS --}}
    <section id="cursos" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Fórmate</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">Cursos disponibles</h2>
            </div>
            @if ($cursosDestacados->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <svg class="h-14 w-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-600 mb-1">Próximos cursos en camino</h3>
                    <p class="text-gray-400 text-sm mb-5">Regístrate y sé el primero en enterarte cuando abramos inscripciones.</p>
                    <a href="{{ route('register') }}"
                       class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-md text-sm font-medium transition">
                        Registrarme
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                    @foreach ($cursosDestacados as $curso)
                        <div class="rounded-2xl border border-gray-100 hover:border-red-200 hover:shadow-lg transition p-6">
                            <div class="bg-red-600 text-white rounded-xl h-12 w-12 flex items-center justify-center mb-4">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $curso->nombre }}</h3>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $curso->descripcion ?? '' }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-red-600 font-bold">${{ number_format($curso->costo, 0, ',', '.') }}</span>
                                @if ($curso->cupos)
                                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ $curso->cupos }} cupos</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- SOBRE NOSOTROS (ampliado) --}}
    <section id="sobre-nosotros" class="py-20 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">Sobre nosotros</span>
                    <h2 class="text-3xl font-extrabold mt-2 mb-4">Pasión por las motos,<br><span class="text-red-500">arte en cada trazo</span></h2>
                    <p class="text-gray-300 text-base leading-relaxed mb-6">
                        Painting Mistery nació de la pasión por las motos y el arte de la pintura automotriz.
                        Somos un taller especializado en personalización y restauración de motocicletas, combinando
                        técnicas tradicionales con las últimas tendencias en diseño.
                    </p>
                    <p class="text-gray-400 text-sm leading-relaxed mb-8">
                        También ofrecemos cursos prácticos donde enseñamos nuestras técnicas a quienes quieren
                        convertir su pasión en una profesión. Más de 5 años de experiencia nos respaldan.
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-3xl font-extrabold text-red-500">5+</p>
                            <p class="text-gray-400 text-xs mt-1">Años de experiencia</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-extrabold text-red-500">200+</p>
                            <p class="text-gray-400 text-xs mt-1">Clientes satisfechos</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-extrabold text-red-500">50+</p>
                            <p class="text-gray-400 text-xs mt-1">Cursos realizados</p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 hover:border-red-500 transition">
                        <div class="bg-red-600 rounded-xl h-12 w-12 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1 text-sm">Repuestos originales</h3>
                        <p class="text-gray-400 text-xs">Piezas de calidad para todas las marcas.</p>
                    </div>
                    <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 hover:border-red-500 transition">
                        <div class="bg-red-600 rounded-xl h-12 w-12 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1 text-sm">Hecho con amor</h3>
                        <p class="text-gray-400 text-xs">Cada trabajo refleja nuestra pasión.</p>
                    </div>
                    <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 hover:border-red-500 transition">
                        <div class="bg-red-600 rounded-xl h-12 w-12 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1 text-sm">Diseño personalizado</h3>
                        <p class="text-gray-400 text-xs">Tu visión hecha realidad en pintura.</p>
                    </div>
                    <div class="bg-gray-800 rounded-2xl p-6 border border-gray-700 hover:border-red-500 transition">
                        <div class="bg-red-600 rounded-xl h-12 w-12 flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-white mb-1 text-sm">Formación profesional</h3>
                        <p class="text-gray-400 text-xs">Aprende de los mejores del sector.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- UBICACIÓN --}}
    <section id="ubicacion" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">¿Dónde estamos?</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Visítanos en Melgar</h2>
                <p class="text-gray-400 text-sm mt-2">Te esperamos en nuestro taller, Melgar – Tolima, Colombia.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                {{-- Info lateral --}}
                <div class="space-y-5 flex flex-col justify-center">
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:shadow-sm transition">
                        <div class="bg-red-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">Dirección</h3>
                            <p class="text-gray-600 text-sm font-medium">Cl. 4 #35-42 casa 13</p>
                            <p class="text-gray-400 text-xs">Sicomoro, Melgar – Tolima, Colombia</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:shadow-sm transition">
                        <div class="bg-green-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">WhatsApp</h3>
                            <a href="https://wa.me/573144557602" target="_blank" class="text-green-600 text-sm hover:underline font-medium">+57 314 455 7602</a>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:shadow-sm transition">
                        <div class="bg-red-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">Email</h3>
                            <a href="mailto:paintingmistery20@gmail.com" class="text-red-600 text-sm hover:underline">paintingmistery20@gmail.com</a>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start p-4 rounded-xl border border-gray-100 hover:border-orange-200 hover:shadow-sm transition">
                        <div class="bg-orange-100 rounded-xl h-11 w-11 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5">Horario</h3>
                            <p class="text-gray-600 text-sm">Lun – Sáb: 8:00 am – 6:00 pm</p>
                            <p class="text-gray-400 text-xs">Domingos: previa cita</p>
                        </div>
                    </div>
                </div>
                {{-- Mapa Melgar, Tolima --}}
                <div class="lg:col-span-2 rounded-2xl overflow-hidden shadow-md border border-gray-100" style="min-height:360px;">
                    <iframe
                        src="https://maps.google.com/maps?q=Cl.+4+%2335-42+casa+13%2C+Sicomoro%2C+Melgar%2C+Tolima%2C+Colombia&hl=es&z=17&output=embed"
                        width="100%" height="100%" style="border:0; min-height:360px;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    {{-- RESEÑAS --}}
    <section class="py-20 bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">Testimonios</span>
                <h2 class="text-3xl font-bold text-white mt-2">Lo que dicen nuestros clientes</h2>
            </div>
            <div class="relative overflow-hidden" id="reviewsWrapper">
                <div class="flex gap-6 transition-transform duration-500 ease-in-out" id="reviewsTrack">
                    @php
                    $reviews = [
                        ['nombre'=>'Carlos M.','texto'=>'Llevé mi moto para personalización y quedé impresionado. Los colores son perfectos y el acabado es de nivel profesional. 100% recomendados.','stars'=>5],
                        ['nombre'=>'Daniela R.','texto'=>'Tomé el curso de pintura automotriz y fue increíble. El instructor explica muy bien y aprendí técnicas que jamás imaginé. Vale cada peso.','stars'=>5],
                        ['nombre'=>'Andrés P.','texto'=>'Compré repuestos y los recibí rápido, todo en perfectas condiciones. El servicio al cliente es excelente, siempre dispuestos a ayudar.','stars'=>5],
                        ['nombre'=>'Laura V.','texto'=>'Mi moto quedó como nueva después del trabajo de pintura. Es un arte lo que hacen, se nota la dedicación y el amor por lo que hacen.','stars'=>5],
                        ['nombre'=>'Miguel S.','texto'=>'Excelente servicio, los precios son justos y la calidad del trabajo no tiene comparación en Melgar. Definitivamente vuelvo.','stars'=>5],
                        ['nombre'=>'Sofía L.','texto'=>'Hice el curso básico de pintura y ahora tengo mi propio negocio. Painting Mistery me cambió la vida. ¡Gracias a todo el equipo!','stars'=>5],
                    ];
                    @endphp
                    @foreach($reviews as $r)
                    <div class="flex-none w-full sm:w-1/2 lg:w-1/3 bg-gray-800 rounded-2xl p-7 border border-gray-700">
                        {{-- Estrellas --}}
                        <div class="flex gap-1 mb-4">
                            @for($i=0;$i<5;$i++)
                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed italic mb-5">"{{ $r['texto'] }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($r['nombre'],0,1)) }}
                            </div>
                            <p class="font-semibold text-white text-sm">{{ $r['nombre'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Controles --}}
                <div class="flex items-center justify-center gap-4 mt-8">
                    <button onclick="moveReviews(-1)" class="h-10 w-10 rounded-full bg-gray-800 hover:bg-red-600 text-white flex items-center justify-center transition border border-gray-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex gap-2" id="reviewDots"></div>
                    <button onclick="moveReviews(1)" class="h-10 w-10 rounded-full bg-gray-800 hover:bg-red-600 text-white flex items-center justify-center transition border border-gray-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTACTO --}}
    <section id="contacto" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header oscuro estilo referencia --}}
            <div class="bg-gray-900 rounded-2xl px-8 py-10 mb-10 text-center">
                <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">Contáctanos</span>
                <h2 class="text-3xl font-bold text-white mt-2">¿En qué podemos ayudarte?</h2>
                <p class="text-gray-400 text-sm mt-2">Escríbenos y te respondemos lo antes posible.</p>
            </div>

            {{-- 4 tarjetas de contacto rápido --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                <a href="https://www.instagram.com/painting_mistery/" target="_blank"
                   class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100 hover:border-pink-200 hover:shadow-md transition group">
                    <div class="h-14 w-14 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">Instagram</p>
                        <p class="text-gray-400 text-xs">@painting_mistery</p>
                    </div>
                </a>
                <a href="https://wa.me/573144557602" target="_blank"
                   class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100 hover:border-green-200 hover:shadow-md transition group">
                    <div class="h-14 w-14 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">WhatsApp</p>
                        <p class="text-gray-400 text-xs">+57 314 455 7602</p>
                    </div>
                </a>
                <a href="mailto:paintingmistery20@gmail.com"
                   class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100 hover:border-red-200 hover:shadow-md transition group">
                    <div class="h-14 w-14 rounded-full bg-red-600 flex items-center justify-center">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">Correo</p>
                        <p class="text-gray-400 text-xs">paintingmistery20<br>@gmail.com</p>
                    </div>
                </a>
                <div class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-100">
                    <div class="h-14 w-14 rounded-full bg-orange-500 flex items-center justify-center">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-gray-800 text-sm">Horario</p>
                        <p class="text-gray-400 text-xs">Lun–Sáb 8am–6pm<br>Dom: previa cita</p>
                    </div>
                </div>
            </div>

            {{-- Formulario con logo + campos --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    {{-- Lado izquierdo: imagen/logo --}}
                    <div class="bg-gray-900 flex flex-col items-center justify-center p-12 gap-4">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                             alt="Logo" class="h-28 w-28 rounded-full object-cover border-4 border-red-600 shadow-2xl">
                        <div class="text-center">
                            <p class="text-white font-extrabold text-xl">Painting <span class="text-red-500">Mistery</span></p>
                            <p class="text-gray-400 text-sm mt-1">Melgar, Tolima – Colombia</p>
                        </div>
                        <div class="flex gap-3 mt-4">
                            <a href="https://www.instagram.com/painting_mistery/" target="_blank" class="h-9 w-9 rounded-full flex items-center justify-center hover:opacity-80 transition" style="background:linear-gradient(135deg,#f09433,#dc2743,#bc1888)">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="https://wa.me/573144557602" target="_blank" class="h-9 w-9 rounded-full bg-green-500 flex items-center justify-center hover:opacity-80 transition">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <a href="https://www.facebook.com/Paintingmistery" target="_blank" class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center hover:opacity-80 transition">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        </div>
                    </div>
                    {{-- Formulario derecho --}}
                    <div class="p-8 bg-white">
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Formulario de contacto</h3>
                        <p class="text-gray-400 text-sm mb-6">Déjanos tu inquietud y te contactamos.</p>
                        <div class="space-y-4" id="contactForm">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Nombre</label>
                                    <input id="cNombre" type="text" placeholder="Tu nombre"
                                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Apellido</label>
                                    <input id="cApellido" type="text" placeholder="Tu apellido"
                                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Correo</label>
                                <input id="cCorreo" type="email" placeholder="tu@correo.com"
                                       class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Número de celular</label>
                                <input id="cTelefono" type="tel" placeholder="+57 300 000 0000"
                                       class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">¿En qué podemos ayudarte?</label>
                                <textarea id="cMensaje" rows="4" placeholder="Cuéntanos..."
                                          class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition resize-none"></textarea>
                            </div>
                            <button onclick="enviarContacto()"
                                    class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg text-sm transition">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Enviar por WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer style="background: linear-gradient(135deg, #0f0f0f 0%, #1a0000 50%, #0f0f0f 100%);" class="pt-14 pb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pb-10 border-b border-red-900/30">

                {{-- Marca --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                             alt="Logo" class="h-12 w-12 rounded-full object-cover border-2 border-red-600 shadow-lg shadow-red-900/50">
                        <div>
                            <span class="text-white font-extrabold text-lg block">Painting <span class="text-red-500">Mistery</span></span>
                            <span class="text-gray-500 text-xs">Melgar, Tolima 🇨🇴</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">
                        Especialistas en pintura automotriz, accesorios y formación profesional para motocicletas.
                    </p>
                    <div class="flex gap-1 items-center">
                        <div class="h-1 w-8 bg-red-600 rounded-full"></div>
                        <div class="h-1 w-4 bg-red-800 rounded-full"></div>
                        <div class="h-1 w-2 bg-red-900 rounded-full"></div>
                    </div>
                </div>

                {{-- Links --}}
                <div>
                    <h4 class="text-red-500 font-bold text-xs uppercase tracking-widest mb-5">Navegación</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('inicio') }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Inicio</a></li>
                        <li><a href="#productos" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Tienda</a></li>
                        <li><a href="#cursos" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Cursos</a></li>
                        <li><a href="#sobre-nosotros" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Sobre nosotros</a></li>
                        <li><a href="#contacto" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Contacto</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-red-400 transition flex items-center gap-2"><span class="text-red-700">›</span> Iniciar sesión</a></li>
                    </ul>
                </div>

                {{-- Redes sociales --}}
                <div>
                    <h4 class="text-red-500 font-bold text-xs uppercase tracking-widest mb-5">Síguenos</h4>

                    {{-- Iconos con colores originales de cada red --}}
                    <div class="flex flex-wrap gap-3 mb-5">

                        {{-- WhatsApp - verde oficial #25D366 --}}
                        <a href="https://wa.me/573144557602" target="_blank" rel="noopener"
                           class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                           style="background-color:#25D366;" title="WhatsApp">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>

                        {{-- Instagram - gradiente oficial --}}
                        <a href="https://www.instagram.com/painting_mistery/" target="_blank" rel="noopener"
                           class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                           style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
                           title="Instagram">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>

                        {{-- Facebook - azul oficial #1877F2 --}}
                        <a href="https://www.facebook.com/Paintingmistery" target="_blank" rel="noopener"
                           class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                           style="background-color:#1877F2;" title="Facebook">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>

                        {{-- TikTok - negro con contraste --}}
                        <a href="https://www.tiktok.com/@paintingmisteryoficial" target="_blank" rel="noopener"
                           class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg border border-gray-600"
                           style="background-color:#010101;" title="TikTok">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.28 6.28 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V9.2a8.16 8.16 0 004.77 1.52V7.27a4.85 4.85 0 01-1-.58z"/>
                            </svg>
                        </a>

                        {{-- Email - rojo de marca --}}
                        <a href="mailto:paintingmistery20@gmail.com"
                           class="group relative h-11 w-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-lg"
                           style="background-color:#dc2626;" title="Email">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-1.5 text-xs text-gray-500">
                        <p class="flex items-center gap-2">
                            <svg class="h-3.5 w-3.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <a href="https://wa.me/573144557602" target="_blank" class="hover:text-green-400 transition">+57 314 455 7602</a>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="h-3.5 w-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:paintingmistery20@gmail.com" class="hover:text-red-400 transition">paintingmistery20@gmail.com</a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-600">
                <p>&copy; {{ date('Y') }} Painting Mistery. Todos los derechos reservados.</p>
                <p>Hecho con ❤️ en Melgar, Tolima — Colombia 🇨🇴</p>
            </div>
        </div>
    </footer>

    {{-- BOTÓN FLOTANTE INSTAGRAM (izquierda) --}}
    <a href="https://www.instagram.com/painting_mistery/" target="_blank" rel="noopener"
       class="fixed bottom-6 left-4 z-50 h-14 w-14 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
       style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
       title="Síguenos en Instagram">
        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
        </svg>
    </a>

    {{-- BOTÓN FLOTANTE WHATSAPP (derecha) --}}
    <a href="https://wa.me/573144557602?text=Hola!%20Vi%20tu%20página%20y%20me%20gustaría%20más%20información%20sobre%20Painting%20Mistery%20🏍️" target="_blank" rel="noopener"
       class="fixed bottom-6 right-4 z-50 h-14 w-14 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 hover:-translate-y-1 transition-all duration-300"
       style="background-color:#25D366;"
       title="Chatea por WhatsApp">
        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    <style>
    /* Pulso en los botones flotantes */
    .fixed a, a.fixed {
        animation: none;
    }
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(37,211,102,.6); }
        70% { box-shadow: 0 0 0 12px rgba(37,211,102,0); }
        100% { box-shadow: 0 0 0 0 rgba(37,211,102,0); }
    }
    a[href*="wa.me"].fixed {
        animation: pulse-ring 2.5s infinite;
    }
    </style>

    <script>
    // ── Carrusel de reseñas ──────────────────────────────────────────
    (function() {
        const track  = document.getElementById('reviewsTrack');
        const dotsEl = document.getElementById('reviewDots');
        if (!track) return;
        const cards    = track.children;
        const visible  = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 640 ? 2 : 1;
        const total    = cards.length;
        const maxSlide = total - visible;
        let current = 0;

        // Dots
        for (let i = 0; i <= maxSlide; i++) {
            const d = document.createElement('button');
            d.className = 'h-2 rounded-full transition-all duration-300 ' + (i === 0 ? 'w-6 bg-red-600' : 'w-2 bg-gray-600');
            d.onclick = () => goTo(i);
            dotsEl.appendChild(d);
        }

        function goTo(idx) {
            current = Math.max(0, Math.min(idx, maxSlide));
            const pct = (100 / visible) * current;
            track.style.transform = `translateX(-${pct}%)`;
            [...dotsEl.children].forEach((d, i) => {
                d.className = 'h-2 rounded-full transition-all duration-300 ' + (i === current ? 'w-6 bg-red-600' : 'w-2 bg-gray-600');
            });
        }

        window.moveReviews = function(dir) { goTo(current + dir); };

        // Autoplay
        setInterval(() => goTo(current >= maxSlide ? 0 : current + 1), 5000);
    })();

    // ── Formulario contacto → WhatsApp ──────────────────────────────
    function enviarContacto() {
        const nombre   = document.getElementById('cNombre').value.trim();
        const apellido = document.getElementById('cApellido').value.trim();
        const correo   = document.getElementById('cCorreo').value.trim();
        const telefono = document.getElementById('cTelefono').value.trim();
        const mensaje  = document.getElementById('cMensaje').value.trim();

        if (!nombre || !mensaje) {
            alert('Por favor completa al menos tu nombre y tu mensaje.');
            return;
        }

        const texto = `¡Hola Painting Mistery! 🏍️\n\n*Nombre:* ${nombre} ${apellido}\n*Correo:* ${correo || 'No indicado'}\n*Teléfono:* ${telefono || 'No indicado'}\n\n*Mensaje:*\n${mensaje}`;
        window.open('https://wa.me/573144557602?text=' + encodeURIComponent(texto), '_blank');
    }
    </script>

</div>
@endsection
