@extends('layouts.guest')

@section('title', 'Painting Mistery - Accesorios y cursos para tu moto')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- HERO --}}
    <section>
    @if ($banners->isEmpty())
        {{-- Sin banners configurados: imagen fija de siempre, sin cambios --}}
        <div class="h-[320px] sm:h-[420px] md:h-[500px] overflow-hidden">
            <img src="/images/hero.jpeg"
                 alt="Painting Mistery"
                 class="w-full h-full object-cover object-center block">
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
    @else
        {{-- Slider administrable desde /admin/banners.
             Sin overlay de texto: los banners se ven completos y limpios.
             La altura es responsiva por breakpoint para que la imagen respire. --}}
        <div id="heroSlider" class="relative overflow-hidden bg-gray-900 h-[320px] sm:h-[420px] md:h-[500px]">
            @foreach ($banners as $i => $banner)
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}">
                    <img src="{{ $banner->imagen }}" alt="Painting Mistery"
                         class="w-full h-full object-cover object-center">
                    {{-- Vignette sutil solo alrededor para dar profundidad, sin oscurecer el centro --}}
                    <div class="absolute inset-0 pointer-events-none"
                         style="background: radial-gradient(ellipse at center, transparent 55%, rgba(0,0,0,0.4) 100%);"></div>
                </div>
            @endforeach

            @if ($banners->count() > 1)
                <button onclick="heroMover(-1)" aria-label="Anterior"
                    class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-20 h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-white/90 hover:bg-white shadow-lg hover:shadow-xl flex items-center justify-center text-gray-800 transition-all duration-200 hover:scale-110">
                    <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="heroMover(1)" aria-label="Siguiente"
                    class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-20 h-12 w-12 sm:h-14 sm:w-14 rounded-full bg-white/90 hover:bg-white shadow-lg hover:shadow-xl flex items-center justify-center text-gray-800 transition-all duration-200 hover:scale-110">
                    <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
                {{-- Dots ocultos por solicitud: mantenidos en el DOM para que el JS del slider funcione --}}
                <div class="hidden" id="heroDots">
                    @foreach ($banners as $i => $banner)
                        <button onclick="heroIrA({{ $i }})" class="hero-dot"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         FRANJA DE ESTADÍSTICAS — compacta, estilo taller (no "tarjetas SaaS")
    ══════════════════════════════════════════════════════ --}}
    <div class="relative bg-[#121212] border-y border-red-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
            <div class="flex flex-wrap items-center justify-center sm:justify-between gap-y-6 gap-x-4">

                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-white font-black text-xl leading-none">
                            <span class="count-up" data-target="500">0</span>+
                        </p>
                        <p class="text-gray-500 text-[11px] mt-1">Clientes satisfechos</p>
                    </div>
                </div>

                <span class="hidden sm:block h-8 w-px bg-white/10"></span>

                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <div>
                        <p class="text-white font-black text-xl leading-none">
                            <span class="count-up" data-target="800">0</span>+
                        </p>
                        <p class="text-gray-500 text-[11px] mt-1">Trabajos realizados</p>
                    </div>
                </div>

                <span class="hidden sm:block h-8 w-px bg-white/10"></span>

                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-white font-black text-xl leading-none">
                            <span class="count-up" data-target="5">0</span>+ años
                        </p>
                        <p class="text-gray-500 text-[11px] mt-1">De experiencia</p>
                    </div>
                </div>

                <span class="hidden sm:block h-8 w-px bg-white/10"></span>

                <a href="https://wa.me/573144557602" target="_blank" class="flex items-center gap-3 group">
                    <svg class="h-6 w-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-white font-black text-xl leading-none group-hover:text-red-400 transition">Melgar</p>
                        <p class="text-gray-500 text-[11px] mt-1">Tolima, Colombia →</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Contador animado con Intersection Observer (solo se dispara cuando entra a la vista)
        (function() {
            const els = document.querySelectorAll('.count-up');
            if (!els.length || !('IntersectionObserver' in window)) return;

            const anim = (el) => {
                const target = parseInt(el.dataset.target, 10) || 0;
                const dur = 1400;
                const start = performance.now();
                const tick = (now) => {
                    const p = Math.min(1, (now - start) / dur);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.floor(target * eased).toLocaleString('es-CO');
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            };

            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) { anim(e.target); obs.unobserve(e.target); }
                });
            }, { threshold: 0.4 });

            els.forEach(el => obs.observe(el));
        })();
    </script>
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
            <div class="text-center mt-10">
                <a href="{{ route('nosotros') }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-red-600 text-white font-semibold px-6 py-3 rounded-xl text-sm transition">Conoce nuestra historia →</a>
            </div>
        </div>
    </section>

    {{-- PRODUCTOS --}}
    <section id="productos" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">Catálogo</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">Productos destacados</h2>
                </div>
                <button onclick="abrirWishlist()"
                    class="flex items-center gap-2 text-sm text-red-600 border border-red-200 rounded-full px-4 py-1.5 hover:bg-red-50 transition self-start sm:self-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Mi lista de deseos
                </button>
            </div>

            {{-- Filtros por categoría --}}
            @if (!$productosDestacados->isEmpty())
            <div class="flex flex-wrap gap-2 mb-8" id="catFiltros">
                <button onclick="filtrarCat('todos')"
                    class="cat-btn px-4 py-1.5 rounded-full text-sm font-medium border transition active-cat"
                    data-cat="todos">Todos</button>
                @foreach($productosDestacados->unique('categoria_producto_id') as $p)
                    @if($p->categoria)
                    <button onclick="filtrarCat('{{ $p->categoria_producto_id }}')"
                        class="cat-btn px-4 py-1.5 rounded-full text-sm font-medium border transition"
                        data-cat="{{ $p->categoria_producto_id }}">{{ $p->categoria->nombre }}</button>
                    @endif
                @endforeach
            </div>
            @endif

            @if ($productosDestacados->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                    <svg class="h-14 w-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <h3 class="text-base font-semibold text-gray-600 mb-1">Catálogo en preparación</h3>
                    <p class="text-gray-400 text-sm">Muy pronto tendremos nuestros productos disponibles.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-7" id="productosGrid">
                    @foreach ($productosDestacados as $producto)
                        @include('partials.producto-card', ['producto' => $producto])
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
                        <a href="{{ route('academia') }}#curso-{{ $curso->id }}" class="block rounded-2xl border border-gray-100 hover:border-red-200 hover:shadow-lg hover:-translate-y-0.5 transition p-6">
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
                            <p class="text-red-600 text-xs font-semibold mt-4">Ver detalles e inscribirme →</p>
                        </a>
                    @endforeach
                </div>
            @endif
            <div class="text-center mt-10">
                <a href="{{ route('academia') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-xl text-sm transition">Ver academia e inscribirme →</a>
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
    <section id="resenas" class="py-20 bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">Testimonios</span>
                <h2 class="text-3xl font-bold text-white mt-2">Lo que dicen nuestros clientes</h2>
            </div>

            @if($resenasSitio->isNotEmpty())
            <div class="relative overflow-hidden" id="reviewsWrapper">
                <div class="flex gap-6 transition-transform duration-500 ease-in-out" id="reviewsTrack">
                    @foreach($resenasSitio as $r)
                    <div class="flex-none w-full sm:w-1/2 lg:w-1/3 bg-gray-800 rounded-2xl p-7 border border-gray-700">
                        <div class="flex gap-1 mb-4">
                            @for($i=0;$i<5;$i++)
                            <svg class="h-4 w-4 {{ $i < $r->calificacion ? 'text-yellow-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed italic mb-5">"{{ $r->comentario }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-sm">{{ mb_strtoupper(mb_substr($r->nombre,0,1)) }}</div>
                            <p class="font-semibold text-white text-sm">{{ $r->nombre }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
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
            @else
            <p class="text-center text-gray-400 text-sm mb-2">Aún no hay reseñas publicadas aquí. ¡Sé la primera persona en dejar la suya!</p>
            @endif

            {{-- Dejar reseña + Google --}}
            <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 sm:p-8">
                    <h3 class="font-bold text-white text-lg">Cuéntanos tu experiencia</h3>
                    <p class="text-gray-400 text-sm mb-5">Tu opinión se publica después de una breve revisión.</p>

                    @if(session('resena_ok'))
                        <div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-300 text-sm px-4 py-3 rounded-xl">{{ session('resena_ok') }}</div>
                    @endif
                    @if($errors->has('nombre') || $errors->has('calificacion') || $errors->has('comentario'))
                        <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-300 text-sm px-4 py-3 rounded-xl">Revisa los datos: escribe tu nombre, elige estrellas y un comentario de al menos 10 caracteres.</div>
                    @endif

                    <form method="POST" action="{{ route('resenas-sitio.store') }}" class="space-y-4">
                        @csrf
                        <input type="text" name="nombre" value="{{ old('nombre', auth()->user()->primer_nombre ?? '') }}" maxlength="80" required placeholder="Tu nombre"
                               class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white placeholder-gray-500 px-3 py-2.5 text-sm focus:outline-none focus:border-red-500">
                        <div class="flex items-center gap-3">
                            <span class="text-gray-400 text-sm">Tu calificación:</span>
                            <select name="calificacion" class="rounded-lg bg-gray-900 border border-gray-700 text-yellow-400 px-3 py-2 text-sm">
                                @for($i=5;$i>=1;$i--)
                                    <option value="{{ $i }}" @selected((int) old('calificacion', 5) === $i)>{{ str_repeat('★', $i) }} ({{ $i }})</option>
                                @endfor
                            </select>
                        </div>
                        <textarea name="comentario" rows="3" minlength="10" maxlength="600" required placeholder="¿Qué te pareció el servicio?"
                                  class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white placeholder-gray-500 px-3 py-2.5 text-sm focus:outline-none focus:border-red-500 resize-none">{{ old('comentario') }}</textarea>
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl text-sm transition">Enviar reseña</button>
                    </form>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 sm:p-8 flex flex-col justify-center gap-5">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-full bg-white flex items-center justify-center shrink-0">
                            <svg class="h-7 w-7" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-white">También en Google</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <div class="flex gap-0.5">
                                    @for($i=0;$i<5;$i++)<svg class="h-3.5 w-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                                </div>
                                <span class="text-gray-400 text-xs">4.3 · 62 opiniones en Google</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">Si prefieres, deja tu reseña directamente en Google y ayuda a que más personas nos encuentren.</p>
                    <a href="https://search.google.com/local/writereview?placeid=ChIJv7Zab8DfPo4R1AyV2wRvzJM" target="_blank" rel="noopener"
                       class="bg-white hover:bg-gray-100 text-gray-900 font-bold px-6 py-3 rounded-xl text-sm transition text-center">Dejar reseña en Google</a>
                </div>
            </div>
        </div>
    </section>

    {{-- PREGUNTAS FRECUENTES --}}
    <section class="py-20 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-red-600 font-semibold text-xs uppercase tracking-widest">¿Dudas?</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-2">Preguntas frecuentes</h2>
            </div>

            <div class="space-y-3">
                @php
                    $faqs = [
                        ['¿Cuánto tarda un pedido?', 'Los pedidos de productos en stock se despachan en 1 a 2 días hábiles. Los trabajos de pintura y personalización dependen del diseño y se acuerdan contigo al momento de agendar.'],
                        ['¿Realizan envíos?', 'Sí, realizamos envíos a nivel nacional. El costo se calcula automáticamente al finalizar la compra según el valor de tu carrito.'],
                        ['¿Aceptan diseños personalizados?', 'Claro. Puedes traernos tu idea o referencia y la adaptamos a tu moto — escríbenos por WhatsApp para cotizar tu diseño.'],
                        ['¿Qué medios de pago reciben?', 'En la tienda online el pago se procesa de forma segura durante el checkout. Para trabajos de taller también aceptamos pagos por transferencia o en efectivo.'],
                        ['¿Los productos tienen garantía?', 'Sí, todos nuestros productos y accesorios cuentan con garantía por defectos de fábrica. Los trabajos de pintura tienen garantía sobre el acabado — consulta condiciones con nosotros.'],
                    ];
                @endphp
                @foreach($faqs as $i => $faq)
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <button type="button" onclick="toggleAcordeon('faqHome{{ $i }}')"
                                class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-800 text-sm">{{ $faq[0] }}</span>
                            <svg id="faqHome{{ $i }}Icon" class="h-4 w-4 text-gray-400 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="faqHome{{ $i }}" class="overflow-hidden transition-all duration-300" style="max-height: 0px;">
                            <p class="px-5 pb-4 text-sm text-gray-500 leading-relaxed">{{ $faq[1] }}</p>
                        </div>
                    </div>
                @endforeach
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
                        <img src="{{ asset('images/logo-painting-mistery.png') }}"
                             onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s';"
                             alt="Painting Mistery" class="h-28 w-28 rounded-full object-cover border-4 border-red-600 shadow-2xl">
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

    @include('partials.footer')

    @include('partials.carrito-wishlist-modales')

    <style>
    .cat-btn { background:#fff; color:#6b7280; border-color:#e5e7eb; }
    .cat-btn.active-cat { background:#dc2626; color:#fff; border-color:#dc2626; }
    </style>

    <script>
    // ── Slider del hero (banners administrables) ──────────────────────
    (function() {
        const slider = document.getElementById('heroSlider');
        if (!slider) return;

        const slides = slider.querySelectorAll('.hero-slide');
        const dots   = slider.querySelectorAll('.hero-dot');
        const total  = slides.length;
        let actual = 0;
        let temporizador = null;

        function pintar() {
            slides.forEach((s, i) => {
                s.classList.toggle('opacity-100', i === actual);
                s.classList.toggle('z-10', i === actual);
                s.classList.toggle('opacity-0', i !== actual);
                s.classList.toggle('z-0', i !== actual);
                s.classList.toggle('pointer-events-none', i !== actual);
            });
            dots.forEach((d, i) => {
                d.classList.toggle('w-6', i === actual);
                d.classList.toggle('bg-white', i === actual);
                d.classList.toggle('w-2', i !== actual);
                d.classList.toggle('bg-white/40', i !== actual);
            });
        }

        function irA(idx) {
            actual = (idx + total) % total;
            pintar();
        }

        window.heroIrA = function(idx) { irA(idx); reiniciarAutoplay(); };
        window.heroMover = function(dir) { irA(actual + dir); reiniciarAutoplay(); };

        function iniciarAutoplay() {
            if (total <= 1) return;
            temporizador = setInterval(() => irA(actual + 1), 6000);
        }
        function reiniciarAutoplay() {
            clearInterval(temporizador);
            iniciarAutoplay();
        }

        slider.addEventListener('mouseenter', () => clearInterval(temporizador));
        slider.addEventListener('mouseleave', iniciarAutoplay);

        iniciarAutoplay();
    })();

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

    </script>

    @include('partials.tienda-scripts')

    <script>
    // ── Filtro categorías ──
    function filtrarCat(cat) {
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.toggle('active-cat', btn.dataset.cat === cat);
        });
        document.querySelectorAll('.prod-card').forEach(card => {
            if (cat === 'todos' || card.dataset.cat === cat) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // ── Init ──
    document.addEventListener('DOMContentLoaded', syncUI);

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
