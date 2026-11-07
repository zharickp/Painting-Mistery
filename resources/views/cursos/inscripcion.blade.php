@extends('layouts.guest')

@section('title', 'Inscripción: ' . $curso->nombre . '')

@section('content')
<div class="bg-gray-50 min-h-screen">

    @include('partials.nav')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <a href="{{ route('academia') }}#curso-{{ $curso->id }}" class="text-sm text-gray-500 hover:text-red-600 transition">← Volver a los cursos</a>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-4">Inscripción</h1>

        @if($errors->any())
            <div class="mt-6 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div>
        @endif

        {{-- Resumen del curso --}}
        <div class="mt-6 bg-white border border-gray-200 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $curso->nombre }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $curso->duracionTexto() }}@if($curso->materiales) · Debes traer: {{ $curso->materiales }}@endif</p>
            </div>
            <p class="text-2xl font-black text-gray-900 shrink-0">${{ number_format($curso->precio, 0, ',', '.') }}</p>
        </div>

        <form method="POST" action="{{ route('cursos.inscribirse', $curso) }}" class="mt-6 bg-white border border-gray-200 rounded-2xl p-6 space-y-6">
            @csrf

            {{-- 1. Fecha --}}
            <div>
                <p class="font-bold text-gray-900">1. Elige la fecha</p>
                <p class="text-xs text-gray-500 mt-1">{{ $curso->dias() === 1 ? 'El curso dura 1 día.' : 'El curso dura ' . $curso->dias() . ' días seguidos.' }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3">
                    @foreach($fechas as $f)
                    @php $quedan = $libres[$f->id] ?? null; $llena = $quedan !== null && $quedan <= 0; @endphp
                    <label class="{{ $llena ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                        <input type="radio" name="curso_fecha_id" value="{{ $f->id }}" required class="peer sr-only" @disabled($llena) @checked(! $llena && (int) old('curso_fecha_id') === $f->id)>
                        <span class="block rounded-xl border border-gray-200 text-gray-700 hover:border-gray-400 px-3 py-2.5 text-sm leading-snug transition peer-checked:border-red-500 peer-checked:ring-2 peer-checked:ring-red-500/40 peer-checked:bg-red-600/10 peer-focus-visible:ring-2">
                            <span class="block font-bold">{{ ucfirst($f->fecha->copy()->locale('es')->isoFormat('MMMM')) }}</span>
                            {{ $f->etiqueta($curso->dias()) }}
                            @if($quedan !== null)
                                <span class="block text-xs mt-0.5 text-gray-500">{{ $llena ? 'Sin cupos' : ($quedan === 1 ? 'Queda 1 cupo' : 'Quedan ' . $quedan . ' cupos') }}</span>
                            @endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- 2. Pago --}}
            <div>
                <p class="font-bold text-gray-900">2. ¿Cómo vas a pagar?</p>
                <select name="metodo_pago_id" required
                        class="mt-3 w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:outline-none focus:border-red-400">
                    <option value="">Elige un método de pago</option>
                    @foreach($metodosPago as $m)
                        <option value="{{ $m->id }}" @selected((int) old('metodo_pago_id') === $m->id)>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl py-3 text-sm transition mt-4">
                    Inscribirme y pagar ${{ number_format($curso->precio, 0, ',', '.') }}
                </button>
                <p class="text-xs text-gray-500 text-center mt-3">El curso se paga completo. Tu cupo queda apartado 24 horas mientras haces el pago.</p>
            </div>
        </form>
    </div>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
</div>
@endsection
