@extends('layouts.guest')
@section('title', 'Mis cursos')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('cliente.partials.menu')

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-800">Mis cursos</h1>
    </div>


    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Mis inscripciones</h2>
    @if($inscripciones->isEmpty())
        <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-10 text-center mb-10">
            <p class="text-slate-500 text-sm">Aún no te has inscrito a ningún curso.</p>
            <a href="{{ route('academia') }}" class="inline-block mt-3 text-sm font-bold text-red-600 hover:text-red-700">Ver cursos →</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
            @foreach($inscripciones as $ins)
            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-slate-800">{{ $ins->curso->nombre ?? 'Curso' }}</h3>
                    <span class="px-2 py-0.5 text-[11px] rounded-full font-semibold shrink-0 {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
                </div>
                <p class="text-xs text-slate-400 mb-3">Reserva {{ $ins->codigoReserva() }} · {{ $ins->created_at->format('d/m/Y') }}</p>
                @if(in_array($ins->estado, ['confirmada', 'completada'], true))
                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-3 text-xs text-slate-700 space-y-1">
                        @if($ins->fechaTexto())<p><strong>Fecha:</strong> {{ $ins->fechaTexto() }}</p>@endif
                        <p><strong>Lugar:</strong> {{ $ins->curso->ubicacion ?: 'Taller Painting Mistery, Melgar – Tolima' }}</p>
                        <p><strong>Duración:</strong> {{ $ins->curso->duracionTexto() }}</p>
                        @if($ins->curso->materiales)<p><strong>Debes traer:</strong> {{ $ins->curso->materiales }}</p>@endif
                        @if($ins->indicaciones)<p><strong>Nota del taller:</strong> {{ $ins->indicaciones }}</p>@endif
                        @if($ins->curso->incluye_certificado ?? true)<p class="text-green-700">Recibirás certificado al finalizar.</p>@endif
                    </div>
                @elseif($ins->estado === 'pendiente' && $ins->venta?->estado === 'pendiente')
                    <div class="text-xs text-amber-800 bg-amber-50 rounded-xl p-3 flex flex-wrap items-center justify-between gap-2">
                        <p>Pendiente de pago{{ $ins->fechaTexto() ? ' · ' . $ins->fechaTexto() : '' }}. Tu cupo está apartado 24 horas.</p>
                        <a href="{{ route('checkout.pago', $ins->venta->numero_orden) }}" class="font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg">Pagar ahora</a>
                    </div>
                @elseif($ins->esReservaAntigua())
                    <div class="text-xs text-amber-800 bg-amber-50 rounded-xl p-3 flex flex-wrap items-center justify-between gap-2">
                        <p>Esta reserva aún no tiene pago. Elige la fecha y paga para quedar inscrito.</p>
                        <a href="{{ route('academia') }}#curso-{{ $ins->curso_id }}" class="font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg">Elegir fecha y pagar</a>
                    </div>
                @else
                    <p class="text-xs text-slate-400">Esta inscripción fue cancelada.</p>
                @endif
                @if($ins->estado !== 'cancelada')
                    <a href="{{ route('mi-cuenta.cursos.comprobante', $ins->id) }}" class="inline-block mt-3 text-xs font-bold text-red-600 hover:text-red-700">Ver comprobante de reserva →</a>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>

@include('partials.footer')
{{-- El carrito y la lista de deseos del menú también funcionan en esta página --}}
@include('partials.carrito-wishlist-modales')
@include('partials.tienda-scripts')
@endsection
