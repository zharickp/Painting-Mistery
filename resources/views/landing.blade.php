@extends('layouts.guest')

@section('title', 'Painting Mistery - Accesorios y cursos para tu moto')

@section('content')
<div class="bg-white">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                         alt="Logo" class="h-10 w-10 rounded-full object-cover border-2 border-red-100">
                    <span class="text-lg font-bold text-gray-900">
                        Painting <span class="text-red-600">Mistery</span>
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                        <a href="#nosotros" class="hover:text-red-600 transition">Nosotros</a>
                        <a href="#productos" class="hover:text-red-600 transition">Productos</a>
                        <a href="#cursos" class="hover:text-red-600 transition">Cursos</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}"
                           class="border border-red-600 text-red-600 hover:bg-red-50 px-4 py-2 rounded-md text-sm font-medium transition">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                            Registrarse
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

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

    {{-- FOOTER --}}
    <footer style="background-color: #030712; padding: 32px 24px;">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                     alt="Logo" class="h-9 w-9 rounded-full object-cover">
                <span class="text-white font-semibold text-sm">
                    Painting <span class="text-red-500">Mistery</span>
                </span>
            </div>
            <p class="text-gray-500 text-xs">&copy; {{ date('Y') }} Painting Mistery. Todos los derechos reservados.</p>
        </div>
    </footer>

</div>
@endsection
