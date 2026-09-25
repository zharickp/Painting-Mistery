@extends('layouts.guest')

@section('title', 'Cursos - Painting Mistery')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- HERO --}}
    <section class="relative bg-gray-950 overflow-hidden">
        <img src="{{ asset('images/hero.jpeg') }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-25">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/60 to-gray-950/30"></div>
        <div class="absolute -left-20 top-10 h-72 w-72 rounded-full bg-red-600/20 blur-3xl"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-center">
            <span class="inline-flex items-center gap-2 text-red-400 font-semibold text-xs uppercase tracking-[0.3em]">
                <span class="h-px w-8 bg-red-500"></span>Cursos en el taller<span class="h-px w-8 bg-red-500"></span>
            </span>
            <h1 class="text-4xl sm:text-6xl font-extrabold text-white mt-5 leading-[1.05]">
                Aprende el <span class="text-red-500">arte</span> de<br class="hidden sm:block"> darle alma a una moto
            </h1>
            <p class="text-gray-300 max-w-2xl mx-auto mt-6 text-base sm:text-lg leading-relaxed">
                Pintura, latonería y polichado en el mismo taller donde personalizamos motos y armamos accesorios.
                Aquí la teoría dura poco: se pinta, se pule y te vas con el oficio en las manos.
            </p>
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                @foreach(['Dos cursos por mes', 'Cupos limitados', 'Certificado al terminar'] as $chip)
                    <span class="px-4 py-2 rounded-full border border-white/15 bg-white/5 backdrop-blur text-white text-xs font-semibold">{{ $chip }}</span>
                @endforeach
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="max-w-4xl mx-auto mt-6 px-4"><div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div></div>
    @endif
    @if(session('error'))
        <div class="max-w-4xl mx-auto mt-6 px-4"><div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div></div>
    @endif
    @if($errors->any())
        <div class="max-w-4xl mx-auto mt-6 px-4"><div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div></div>
    @endif

    {{-- CÓMO FUNCIONA --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Así reservas tu lugar</h2>
                <p class="text-gray-500 mt-2 text-sm">Cuatro pasos, sin vueltas.</p>
            </div>
            <div class="relative grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-6">
                <div class="hidden md:block absolute top-7 left-[12%] right-[12%] h-px bg-gradient-to-r from-red-200 via-red-500 to-red-200"></div>
                @foreach([
                    ['Elige tu curso',   'Polichado, o el programa completo con pintura y latonería.', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['Escoge una fecha', 'Salen dos cursos al mes. Reservas en una de las fechas abiertas.', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['Hablamos contigo', 'Te escribimos para cuadrar hospedaje, llegada y lo que haga falta.', 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                    ['Manos a la obra',  'Llegas al taller, aprendes haciendo y sales con tu certificado.', 'M5 13l4 4L19 7'],
                ] as $i => $paso)
                <div class="relative text-center">
                    <div class="relative z-10 mx-auto h-14 w-14 rounded-2xl {{ $i % 2 ? 'bg-gray-900 rotate-3 shadow-gray-900/20' : 'bg-red-600 shadow-red-600/30' }} text-white flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $paso[2] }}"/></svg>
                    </div>
                    <p class="font-bold text-gray-900 mt-4">{{ $paso[0] }}</p>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ $paso[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CURSOS --}}
    <section class="pb-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($cursos->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-gray-500">Muy pronto abriremos nuevos cursos.</div>
            @else
            @php $maxCosto = $cursos->max('costo'); @endphp
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($cursos as $curso)
                @php
                    $ins = $misInscripciones->get($curso->id);
                    $activa = $ins && in_array($ins->estado, ['pendiente','confirmada','completada'], true);
                    $libres = $curso->cuposDisponibles();
                    $info = $curso->info;
                    $destacado = $cursos->count() > 1 && (float) $curso->costo === (float) $maxCosto;
                    $lineas = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $curso->descripcion))));
                    $intro = $lineas[0] ?? 'Curso práctico dictado en el taller.';
                    $puntos = array_slice($lineas, 1);
                    $suave = $destacado ? 'text-gray-300' : 'text-gray-600';
                @endphp
                <article id="curso-{{ $curso->id }}" class="scroll-mt-24 relative rounded-3xl overflow-hidden flex flex-col
                    {{ $destacado ? 'bg-gray-950 text-white shadow-2xl shadow-red-900/20 ring-1 ring-red-500/40' : 'bg-white border border-gray-200 shadow-sm hover:shadow-xl transition' }}">

                    @if($destacado)
                        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-red-600/25 blur-3xl"></div>
                        <span class="absolute top-5 right-5 text-[10px] font-black uppercase tracking-widest bg-red-600 text-white rounded-full px-3 py-1">El recorrido completo</span>
                    @else
                        <div class="absolute left-0 top-0 h-full w-1.5 bg-red-600"></div>
                    @endif

                    <div class="relative p-7 sm:p-9 flex-1 flex flex-col">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] {{ $destacado ? 'text-red-400' : 'text-red-600' }}">
                            {{ $destacado ? 'Programa integral' : 'Curso especializado' }}
                        </p>
                        <h2 class="text-2xl font-extrabold mt-2 leading-tight {{ $destacado ? 'text-white pr-24' : 'text-gray-900' }}">{{ $curso->nombre }}</h2>
                        <p class="mt-4 leading-relaxed text-sm {{ $suave }}">{{ $intro }}</p>

                        @if($puntos)
                        <ul class="mt-5 space-y-2">
                            @foreach($puntos as $p)
                            <li class="flex gap-2.5 text-sm {{ $destacado ? 'text-gray-200' : 'text-gray-700' }}">
                                <svg class="h-5 w-5 shrink-0 {{ $destacado ? 'text-red-400' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ $p }}
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <div class="mt-6 flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-widest text-gray-400">Inversión</p>
                                <p class="text-3xl font-black {{ $destacado ? 'text-white' : 'text-gray-900' }}">${{ number_format($curso->costo, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-[11px] font-semibold">
                                @if($info?->duracion)<span class="px-2.5 py-1 rounded-full {{ $destacado ? 'bg-white/10 text-gray-200' : 'bg-gray-100 text-gray-600' }}">{{ $info->duracion }}</span>@endif
                                <span class="px-2.5 py-1 rounded-full {{ $destacado ? 'bg-white/10 text-gray-200' : 'bg-gray-100 text-gray-600' }}">{{ $libres === null ? 'Cupos limitados' : $libres . ' cupos libres' }}</span>
                                @if($info?->incluye_certificado ?? true)<span class="px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-500">Con certificado</span>@endif
                            </div>
                        </div>

                        @if($info?->requisitos)
                            <p class="mt-4 text-xs {{ $destacado ? 'text-gray-400' : 'text-gray-500' }}"><strong class="{{ $destacado ? 'text-gray-200' : 'text-gray-700' }}">Debes traer:</strong> {{ $info->requisitos }}</p>
                        @endif

                        {{-- Reserva --}}
                        <div class="mt-auto pt-6 border-t {{ $destacado ? 'border-white/10' : 'border-gray-100' }}">
                        @if($activa)
                            <div class="rounded-xl px-4 py-3 flex items-center justify-between {{ $destacado ? 'bg-white/5' : 'bg-gray-50 border border-gray-100' }}">
                                <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
                                @auth @if(auth()->user()->tieneRol('Cliente'))<a href="{{ route('mi-cuenta.cursos') }}" class="text-sm font-semibold {{ $destacado ? 'text-red-400' : 'text-red-600' }}">Ver mi inscripción →</a>@endif @endauth
                            </div>
                        @elseif($libres !== null && $libres <= 0)
                            <div class="rounded-xl text-sm text-center py-3 font-medium {{ $destacado ? 'bg-white/5 text-gray-400' : 'bg-gray-100 text-gray-500' }}">Cupos agotados por ahora</div>
                        @elseif($curso->fechas->isEmpty())
                            <p class="text-sm mb-3 {{ $suave }}">Estamos definiendo las próximas fechas. Escríbenos y te avisamos primero.</p>
                            <a href="https://wa.me/573144557602?text={{ urlencode('Hola! Quiero información sobre las próximas fechas del ' . $curso->nombre) }}" target="_blank" rel="noopener"
                               class="block text-center bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl py-3 text-sm transition">Preguntar por WhatsApp</a>
                        @else
                            @auth
                                @if(auth()->user()->tieneRol('Cliente'))
                                <form method="POST" action="{{ route('cursos.inscribirse', $curso) }}" class="space-y-3">
                                    @csrf
                                    <label class="block text-xs font-semibold {{ $suave }}">Fechas disponibles</label>
                                    <select name="curso_fecha_id" required
                                            class="w-full rounded-xl border px-3 py-3 text-sm focus:outline-none focus:border-red-400 {{ $destacado ? 'bg-gray-900 border-white/15 text-white' : 'bg-white border-gray-200 text-gray-800' }}">
                                        <option value="" disabled selected>Elige cuándo quieres empezar…</option>
                                        @foreach($curso->fechas as $f)
                                            <option value="{{ $f->id }}">{{ $f->etiqueta() }}</option>
                                        @endforeach
                                    </select>
                                    <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-3 text-sm transition shadow-lg shadow-red-600/20">Reservar mi cupo</button>
                                    <p class="text-[11px] text-gray-400">Después te contactamos para coordinar hospedaje y los detalles.</p>
                                </form>
                                @else
                                <p class="text-xs text-center text-gray-400">Las reservas se hacen desde una cuenta de cliente.</p>
                                @endif
                            @else
                                <p class="text-sm mb-3 {{ $suave }}">Próximas fechas: {{ $curso->fechas->take(3)->map->etiqueta()->implode(' · ') }}</p>
                                <a href="{{ route('login') }}" class="block text-center bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-3 text-sm transition">Inicia sesión para reservar</a>
                            @endauth
                        @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            @endif

            {{-- Hospedaje --}}
            <div class="mt-12 rounded-3xl bg-gradient-to-r from-gray-900 to-gray-800 text-white p-8 sm:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="max-w-xl">
                    <h3 class="text-xl font-extrabold">¿Vienes de otra ciudad?</h3>
                    <p class="text-gray-300 text-sm mt-2 leading-relaxed">
                        Una vez reservas tu fecha, nos comunicamos contigo para cuadrar el hospedaje y todos los detalles del viaje.
                        Tú solo preocúpate de llegar con ganas de aprender.
                    </p>
                </div>
                <a href="https://wa.me/573144557602" target="_blank" rel="noopener" class="shrink-0 bg-white text-gray-900 font-bold px-6 py-3 rounded-xl text-sm hover:bg-red-50 transition">Resolver una duda</a>
            </div>
        </div>
    </section>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
    <script>document.addEventListener('DOMContentLoaded', syncUI);</script>
</div>
@endsection
