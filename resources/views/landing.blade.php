@extends('layouts.guest')

@section('title', 'Painting Mistery - Accesorios y cursos para tu moto')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- HERO: banner con esquinas redondeadas, dentro del mismo ancho del contenido --}}
    <section class="bg-white pt-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if ($banners->isEmpty())
        {{-- Sin banners configurados: imagen fija de siempre, sin cambios --}}
        <div class="h-[360px] sm:h-[480px] md:h-[560px] lg:h-[clamp(580px,35vw,700px)] overflow-hidden rounded-3xl">
            <img src="/images/hero.jpeg"
                 alt="Painting Mistery"
                 class="w-full h-full object-cover object-center block">
        </div>

        {{-- Texto completamente separado, debajo --}}
        <div class="rounded-3xl mt-4" style="background-color: #111827; padding: 40px 24px;">
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
        <div id="heroSlider" class="relative overflow-hidden rounded-3xl bg-gray-900 h-[360px] sm:h-[480px] md:h-[560px] lg:h-[clamp(580px,35vw,700px)]">
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
                {{-- Un punto por banner; el activo se ve más largo --}}
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2" id="heroDots">
                    @foreach ($banners as $i => $banner)
                        <button type="button" onclick="heroIrA({{ $i }})" aria-label="Ver banner {{ $i + 1 }}"
                                class="hero-dot h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-6 bg-white' : 'w-2 bg-white/40' }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
    </div>
    </section>

    @php
        $fotoTienda = optional($productosDestacados->first(fn ($p) => $p->imagen))->imagen ?? asset('images/hero.jpeg');
        $fotoCursos = asset('images/hero.jpeg');
        $fotoTaller = optional($banners->first())->imagen ?? asset('images/hero.jpeg');
    @endphp

    {{-- ACCESOS --}}
    <section class="bg-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Bienvenido a Painting <span class="text-red-600">Mistery</span></h2>
            <p class="text-gray-500 mt-2">Taller de pintura y personalización de motos en Melgar, Tolima.</p>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ([
                ['Tienda',            'Accesorios y repuestos para tu moto.',          route('tienda.index'), $fotoTienda],
                ['Cursos',            'Pintura, latonería y polichado en el taller.',  route('academia'),     $fotoCursos],
                ['Contacto',          'Comunícate con nosotros.',                      route('contacto'),     $fotoTaller],
            ] as [$titulo, $texto, $url, $foto])
                <a href="{{ $url }}" class="group relative block h-72 rounded-2xl overflow-hidden bg-gray-900">
                    <img src="{{ $foto }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6">
                        <h2 class="font-display uppercase text-2xl font-bold text-white tracking-wide">{{ $titulo }}</h2>
                        <p class="text-gray-300 text-sm mt-1">{{ $texto }}</p>
                        <span class="inline-block mt-3 text-sm font-semibold text-red-500 group-hover:text-red-400">Entrar →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- PRODUCTOS --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="font-display uppercase text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Productos destacados</h2>
                    <p class="text-gray-500 text-sm mt-1">Lo más reciente que llegó a la tienda.</p>
                </div>
                <a href="{{ route('tienda.index') }}" class="shrink-0 text-sm font-semibold text-red-600 hover:text-red-700">Ver tienda →</a>
            </div>

            @if ($productosDestacados->isEmpty())
                <div class="text-center py-14 bg-white rounded-2xl border border-dashed border-gray-200">
                    <p class="text-gray-500 text-sm">Todavía no hay productos publicados.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="productosGrid">
                    @foreach ($productosDestacados as $producto)
                        @include('partials.producto-card', ['producto' => $producto])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- CURSOS --}}
    <section class="relative py-16 bg-gray-950 overflow-hidden">
        <img src="{{ $fotoCursos }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-15">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/70 to-gray-950"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="font-display uppercase text-3xl sm:text-4xl font-bold text-white tracking-tight">Cursos</h2>
                    <p class="text-gray-400 text-sm mt-1">Clases prácticas, con cupos limitados.</p>
                </div>
                <a href="{{ route('academia') }}" class="shrink-0 text-sm font-semibold text-red-500 hover:text-red-400">Ver cursos y fechas →</a>
            </div>

            @if ($cursosDestacados->isEmpty())
                <p class="text-gray-400 text-sm">Por ahora no hay cursos abiertos.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($cursosDestacados as $curso)
                        @php $proxima = $curso->fechasDisponibles->first(); @endphp
                        <a href="{{ route('academia') }}#curso-{{ $curso->id }}"
                           class="group flex flex-col rounded-2xl bg-white/5 border border-white/10 hover:border-red-600/60 transition p-6">
                            <h3 class="text-lg font-bold text-white">{{ $curso->nombre }}</h3>
                            @if ($curso->descripcion)
                                <p class="text-gray-400 text-sm mt-2 line-clamp-2">{{ $curso->descripcion }}</p>
                            @endif
                            <dl class="mt-4 space-y-1.5 text-sm">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Duración</dt>
                                    <dd class="text-gray-200">{{ $curso->duracionTexto() }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Próxima fecha</dt>
                                    <dd class="text-gray-200 text-right">{{ $proxima ? \App\Models\CursoFecha::etiquetaDe($proxima->fecha) : 'Por definir' }}</dd>
                                </div>
                            </dl>
                            <div class="mt-auto pt-5 flex items-center justify-between">
                                <span class="text-xl font-bold text-white">${{ number_format($curso->costo, 0, ',', '.') }}</span>
                                <span class="text-sm font-semibold text-red-500 group-hover:text-red-400">Ver detalles →</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- RESEÑAS --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="font-display uppercase text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">Reseñas</h2>
                    <p class="text-gray-500 text-sm mt-1">Opiniones de clientes sobre nuestros productos.</p>
                </div>
                <a href="https://search.google.com/local/writereview?placeid=ChIJv7Zab8DfPo4R1AyV2wRvzJM" target="_blank" rel="noopener"
                   class="shrink-0 text-sm font-semibold text-red-600 hover:text-red-700">Opinar en Google →</a>
            </div>

            @if ($resenasClientes->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($resenasClientes as $r)
                        <div class="rounded-2xl border border-gray-200 p-6 flex flex-col">
                            <div class="flex gap-0.5 mb-3">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg class="h-4 w-4 {{ $i < $r->calificacion ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-gray-700 leading-relaxed">{{ $r->comentario }}</p>
                            <div class="mt-auto pt-5 flex items-center gap-3 text-sm">
                                <div class="h-9 w-9 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-xs shrink-0">{{ mb_strtoupper(mb_substr($r->nombreMostrar(), 0, 1)) }}</div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $r->nombreMostrar() }}</p>
                                    @if ($r->producto)
                                        <a href="{{ route('producto.show', $r->producto_id) }}" class="text-gray-500 hover:text-red-600 text-xs">{{ $r->producto->nombre }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Todavía no hay reseñas publicadas.</p>
            @endif

            <p class="text-gray-500 text-sm mt-8">¿Compraste algo con nosotros? Deja tu reseña en la página del producto.</p>
        </div>
    </section>

    {{-- PREGUNTAS FRECUENTES --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-display uppercase text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight text-center mb-10">Preguntas frecuentes</h2>

            <div class="space-y-3">
                @php
                    $faqs = [
                        ['¿Cuánto tarda un trabajo de pintura?', 'Depende del diseño. El tiempo se acuerda contigo al momento de agendar.'],
                        ['¿Aceptan diseños personalizados?', 'Claro. Puedes traernos tu idea o referencia y la adaptamos a tu moto — escríbenos por WhatsApp para cotizar tu diseño.'],
                        ['¿Qué medios de pago reciben?', 'En la tienda puedes elegir tarjeta, PSE, Nequi o Daviplata al finalizar la compra. Para trabajos de taller también recibimos transferencia o efectivo.'],
                        ['¿Los productos tienen garantía?', 'Sí, todos nuestros productos y accesorios cuentan con garantía por defectos de fábrica. Los trabajos de pintura tienen garantía sobre el acabado — consulta condiciones con nosotros.'],
                    ];
                @endphp
                @foreach($faqs as $i => $faq)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
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

    {{-- INVITACIÓN A CONTACTO --}}
    <section class="pb-16 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gray-900 px-8 py-10 sm:px-12 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <span class="text-red-400 font-semibold text-xs uppercase tracking-widest">¿Hablamos?</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">Cotiza tu diseño o resuelve tus dudas</h2>
                    <p class="text-gray-400 text-sm mt-2">Dirección, horario, mapa y formulario en un solo lugar.</p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="{{ route('contacto') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl text-sm transition">Ir a contacto</a>
                    <a href="https://wa.me/573144557602" target="_blank" rel="noopener" class="border border-green-500 text-green-400 hover:bg-green-500 hover:text-white font-bold px-6 py-3 rounded-xl text-sm transition">WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')

    @include('partials.carrito-wishlist-modales')

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
            temporizador = setInterval(() => irA(actual + 1), 15000); // 15 segundos por banner
        }
        function reiniciarAutoplay() {
            clearInterval(temporizador);
            iniciarAutoplay();
        }

        iniciarAutoplay();
    })();

    </script>

    @include('partials.tienda-scripts')

    <script>
    // ── Init ──
    document.addEventListener('DOMContentLoaded', syncUI);

    </script>

</div>
@endsection
