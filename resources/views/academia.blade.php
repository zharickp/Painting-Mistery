@extends('layouts.guest')

@section('title', 'Academia - Painting Mistery')

@section('content')
<div class="bg-white">

    @include('partials.nav')

    <section class="relative bg-gray-950 overflow-hidden">
        <img src="{{ asset('images/hero.jpeg') }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 to-transparent"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <span class="text-red-400 font-semibold text-xs uppercase tracking-[0.3em]">Academia</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mt-3">Fórmate en <span class="text-red-500">pintura y reparación</span> de motos</h1>
            <p class="text-gray-300 max-w-2xl mx-auto mt-4">Cursos prácticos de pintura y reparación de partes en nuestro taller de Melgar, Tolima.</p>
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

    {{-- Cómo funciona --}}
    <section class="py-14 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([['1','Solicita tu cupo','Elige el curso y, si quieres, una fecha preferida.'],['2','Confirmamos la fecha','Te asignamos el día según disponibilidad real.'],['3','Asiste al taller','Te indicamos ubicación y qué debes traer.'],['4','Certifícate','Recibe tu certificado al terminar.']] as $s)
                <div class="rounded-2xl border border-gray-100 p-5 text-center">
                    <div class="h-9 w-9 mx-auto rounded-full bg-red-600 text-white font-bold flex items-center justify-center mb-3">{{ $s[0] }}</div>
                    <p class="font-bold text-gray-800 text-sm">{{ $s[1] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $s[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="pb-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($cursos->isEmpty())
                <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-gray-500">Próximamente abriremos nuevos cursos.</div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($cursos as $curso)
                @php
                    $ins = $misInscripciones->get($curso->id);
                    $activa = $ins && in_array($ins->estado, ['pendiente','confirmada','completada'], true);
                    $libres = $curso->cuposDisponibles();
                    $info = $curso->info;
                @endphp
                <article id="curso-{{ $curso->id }}" class="scroll-mt-24 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition overflow-hidden flex flex-col">
                    <div class="bg-gray-900 px-7 py-6 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $curso->nombre }}</h2>
                            @if($info?->duracion)<p class="text-gray-400 text-xs mt-1">{{ $info->duracion }}</p>@endif
                        </div>
                        <span class="text-red-400 font-extrabold text-xl shrink-0">${{ number_format($curso->costo, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-7 flex-1 flex flex-col">
                        <p class="text-gray-600 text-sm leading-relaxed mb-5">{{ $curso->descripcion ?: 'Curso práctico dictado por profesionales del taller.' }}</p>
                        <dl class="space-y-2 text-sm mb-6">
                            <div class="flex gap-2"><dt class="text-gray-400 w-24 shrink-0">Ubicación</dt><dd class="text-gray-700">{{ $info?->ubicacion ?: 'Taller Painting Mistery, Melgar – Tolima' }}</dd></div>
                            @if($info?->requisitos)<div class="flex gap-2"><dt class="text-gray-400 w-24 shrink-0">Debes traer</dt><dd class="text-gray-700">{{ $info->requisitos }}</dd></div>@endif
                            <div class="flex gap-2"><dt class="text-gray-400 w-24 shrink-0">Cupos</dt><dd class="text-gray-700">{{ $libres === null ? 'Disponibles' : $libres . ' de ' . $curso->cupos . ' libres' }}</dd></div>
                            @if($info?->incluye_certificado ?? true)<div class="flex gap-2"><dt class="text-gray-400 w-24 shrink-0">Certificado</dt><dd class="text-green-600 font-medium">Incluido al finalizar</dd></div>@endif
                        </dl>

                        <div class="mt-auto">
                        @if($activa)
                            <div class="rounded-xl bg-gray-50 border border-gray-100 px-4 py-3 flex items-center justify-between">
                                <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
                                @auth @if(auth()->user()->tieneRol('Cliente'))<a href="{{ route('cliente.cursos') }}" class="text-sm text-red-600 font-semibold">Ver detalles →</a>@endif @endauth
                            </div>
                        @elseif($libres !== null && $libres <= 0)
                            <div class="rounded-xl bg-gray-100 text-gray-500 text-sm text-center py-3 font-medium">Cupos agotados</div>
                        @else
                            @auth
                                @if(auth()->user()->tieneRol('Cliente'))
                                <form method="POST" action="{{ route('cursos.inscribirse', $curso) }}" class="flex flex-col sm:flex-row gap-3">
                                    @csrf
                                    <input type="date" name="fecha_preferida" min="{{ now()->toDateString() }}" title="Fecha preferida (opcional)"
                                           class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:border-red-400">
                                    <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-2.5 text-sm transition">Solicitar cupo</button>
                                </form>
                                @else
                                <p class="text-xs text-gray-400 text-center">Las inscripciones se realizan desde una cuenta de cliente.</p>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block text-center bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-2.5 text-sm transition">Inicia sesión para inscribirte</a>
                            @endauth
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
