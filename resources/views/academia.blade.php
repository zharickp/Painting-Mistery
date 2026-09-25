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
                @foreach(['Dos cursos por mes', 'De lunes a viernes', 'Cupos limitados', 'Certificado al terminar'] as $chip)
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
                                    <p class="text-xs font-semibold {{ $suave }}">Elige tu fecha <span class="font-normal opacity-70">({{ $curso->dias() === 1 ? 'dura 1 día' : 'dura ' . $curso->dias() . ' días' }})</span></p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1">
                                        @foreach($curso->fechas as $f)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="curso_fecha_id" value="{{ $f->id }}" required class="peer sr-only">
                                            <span class="block rounded-xl border px-3 py-2.5 text-xs leading-snug transition peer-checked:border-red-500 peer-checked:ring-2 peer-checked:ring-red-500/40 peer-checked:bg-red-600/10 peer-focus-visible:ring-2 {{ $destacado ? 'border-white/15 text-gray-200 hover:border-white/40' : 'border-gray-200 text-gray-700 hover:border-gray-400' }}">
                                                <span class="block font-bold">{{ ucfirst($f->fecha->copy()->locale('es')->isoFormat('MMMM')) }}</span>
                                                {{ $f->etiqueta($curso->dias()) }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-3 text-sm transition shadow-lg shadow-red-600/20">Reservar y ver mi comprobante</button>
                                    <p class="text-[11px] text-gray-400">Recibirás un comprobante para coordinar el abono por WhatsApp.</p>
                                </form>
                                @else
                                <p class="text-xs text-center text-gray-400">Las reservas se hacen desde una cuenta de cliente.</p>
                                @endif
                            @else
                                <p class="text-sm mb-3 {{ $suave }}">Próximas fechas: {{ $curso->fechas->take(3)->map(fn ($f) => $f->etiqueta($curso->dias()))->implode(' · ') }}</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('register') }}" class="text-center bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-3 text-sm transition">Crear cuenta</a>
                                    <a href="{{ route('login') }}" class="text-center border font-bold rounded-xl py-3 text-sm transition {{ $destacado ? 'border-white/20 text-white hover:bg-white/10' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">Ya tengo cuenta</a>
                                </div>
                            @endauth
                        @endif
                        </div>

                        <a href="https://wa.me/573144557602?text={{ urlencode('Hola! Quisiera más información sobre el ' . $curso->nombre) }}" target="_blank" rel="noopener"
                           class="mt-4 flex items-center justify-center gap-2 rounded-xl border border-green-500/60 text-green-500 hover:bg-green-500 hover:text-white font-semibold py-2.5 text-sm transition">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Preguntar cualquier duda por WhatsApp
                        </a>
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
