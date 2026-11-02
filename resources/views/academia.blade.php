@extends('layouts.guest')

@section('title', 'Cursos - Painting Mistery')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    {{-- PORTADA: clara y compacta, texto a la izquierda y foto de un curso a la derecha --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="text-red-600 font-semibold text-xs uppercase tracking-[0.3em]">Cursos</span>
                <h1 class="text-4xl font-extrabold text-gray-900 mt-3 leading-tight">
                    Aprende a <span class="text-red-600">pintar</span> y restaurar motos
                </h1>
                <p class="text-gray-600 mt-4 text-base sm:text-lg leading-relaxed">
                    Clases prácticas con cupos limitados. Eliges la fecha, pagas en línea y tu cupo queda asegurado.
                </p>
            </div>
            <div class="rounded-3xl overflow-hidden h-64">
                <img src="{{ asset('images/cursos.jpeg') }}" alt="Estudiantes de un curso de Painting Mistery" class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    @if($errors->any())
        <div class="max-w-4xl mx-auto mt-6 px-4"><div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div></div>
    @endif

    {{-- CÓMO FUNCIONA --}}
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Cómo inscribirte</h2>
            </div>
            <div class="relative grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-6">
                <div class="hidden md:block absolute top-7 left-[12%] right-[12%] h-px bg-gray-200"></div>
                @foreach([
                    ['Elige el curso', 'Revisa qué incluye cada curso y su precio.', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['Escoge la fecha', 'Selecciona una de las fechas disponibles.', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['Paga en línea', 'Pagas el valor completo con tarjeta, PSE, Nequi o Daviplata.', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                    ['Asiste a tu clase', 'Te esperamos en el taller en la fecha que escogiste.', 'M5 13l4 4L19 7'],
                ] as $i => $paso)
                <div class="relative text-center">
                    <div class="relative z-10 mx-auto h-14 w-14 rounded-2xl bg-red-600 text-white flex items-center justify-center">
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
    <section id="cursos" class="scroll-mt-24 py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($cursos->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-gray-500">Pronto publicaremos nuevos cursos.</div>
            @else
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 text-center mb-10">Cursos disponibles</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($cursos as $curso)
                @php
                    $ins = $misInscripciones->get($curso->id);
                    $activa = $ins && in_array($ins->estado, ['pendiente','confirmada','completada'], true) && ! $ins->esReservaAntigua();
                    $libres = $curso->cuposDisponibles();
                    $lineas = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $curso->descripcion))));
                    $intro = $lineas[0] ?? 'Curso práctico dictado en el taller.';
                    $puntos = array_slice($lineas, 1);
                    $suave = 'text-gray-600';
                @endphp
                <article id="curso-{{ $curso->id }}" class="scroll-mt-24 relative rounded-3xl overflow-hidden flex flex-col
                    bg-white border border-gray-200 shadow-sm">

                    <div class="relative p-7 sm:p-9 flex-1 flex flex-col">
                        <h2 class="text-2xl font-extrabold leading-tight text-gray-900">{{ $curso->nombre }}</h2>
                        <p class="mt-4 leading-relaxed text-sm {{ $suave }}">{{ $intro }}</p>

                        @if($puntos)
                        <ul class="mt-5 space-y-2">
                            @foreach($puntos as $p)
                            <li class="flex gap-2.5 text-sm text-gray-700">
                                <svg class="h-5 w-5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ $p }}
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <div class="mt-6 flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-widest text-gray-400">Precio</p>
                                <p class="text-3xl font-black text-gray-900">${{ number_format($curso->precio, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-[11px] font-semibold">
                                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">{{ $curso->duracionTexto() }}</span>
                                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">{{ $libres === null ? 'Cupos limitados' : $libres . ' cupos libres' }}</span>
                                @if($curso->incluye_certificado ?? true)<span class="px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-500">Con certificado</span>@endif
                            </div>
                        </div>

                        @if($curso->materiales)
                            <p class="mt-4 text-xs text-gray-500"><strong class="text-gray-700">Debes traer:</strong> {{ $curso->materiales }}</p>
                        @endif

                        {{-- Reserva --}}
                        <div class="mt-auto pt-6 border-t border-gray-100">
                        @if($activa)
                            <div class="rounded-xl px-4 py-3 flex items-center justify-between bg-gray-50 border border-gray-100">
                                <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
                                @if($ins->estado === 'pendiente' && $ins->venta?->estado === 'pendiente')
                                    <a href="{{ route('checkout.pago', $ins->venta->numero_orden) }}" class="text-sm font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition">Pagar ahora</a>
                                @else
                                    <a href="{{ route('mi-cuenta.cursos') }}" class="text-sm font-semibold text-red-600">Ver mi inscripción →</a>
                                @endif
                            </div>
                        @elseif($libres !== null && $libres <= 0 && ! $ins?->esReservaAntigua())
                            <div class="rounded-xl text-sm text-center py-3 font-medium bg-gray-100 text-gray-500">Cupos agotados por ahora</div>
                        @elseif($curso->fechas->isEmpty())
                            <p class="text-sm mb-3 {{ $suave }}">Todavía no hay fechas publicadas. Escríbenos y te avisamos cuando las haya.</p>
                            <a href="https://wa.me/573144557602?text={{ urlencode('Hola! Quiero información sobre las próximas fechas del ' . $curso->nombre) }}" target="_blank" rel="noopener"
                               class="block text-center bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl py-3 text-sm transition">Escribir por WhatsApp</a>
                        @else
                            {{-- La fecha y el pago se eligen en una página aparte, para que la tarjeta no sea tan larga --}}
                            <p class="text-sm mb-3 {{ $suave }}">Próximas fechas: {{ $curso->fechas->take(3)->map(fn ($f) => $f->etiqueta($curso->dias()))->implode(' · ') }}</p>
                            @if(auth()->check() && ! auth()->user()->tieneRol('Cliente'))
                                <p class="text-xs text-center text-gray-400">Las inscripciones se hacen desde una cuenta de cliente.</p>
                            @else
                                <a href="{{ route('cursos.inscripcion', $curso) }}" class="block text-center bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-3 text-sm transition">Inscribirme</a>
                                @guest
                                <p class="text-xs text-center text-gray-500 mt-3">
                                    ¿No tienes cuenta?
                                    <a href="{{ route('register', ['volver' => '/cursos/' . $curso->id . '/inscribirse']) }}" class="text-red-600 font-semibold hover:underline">Regístrate</a>
                                </p>
                                @endguest
                            @endif
                        @endif
                        </div>

                    </div>
                </article>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
    <script>document.addEventListener('DOMContentLoaded', syncUI);</script>
</div>
@endsection
