@extends('layouts.guest')
@section('title', 'Mis cursos')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('cliente.partials.menu')

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-800">Mis cursos</h1>
        <p class="text-sm text-slate-500 mt-1">Tus inscripciones y los cursos que puedes tomar en el taller.</p>
    </div>

    @if(session('success'))<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif

    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Mis inscripciones</h2>
    @if($inscripciones->isEmpty())
        <div class="bg-white border border-dashed border-slate-200 rounded-2xl p-10 text-center mb-10">
            <p class="text-slate-500 text-sm">Aún no te has inscrito a ningún curso.</p>
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
                        <p><strong>Lugar:</strong> {{ $ins->curso->info?->ubicacion ?: 'Taller Painting Mistery, Melgar – Tolima' }}</p>
                        @if($ins->curso->info?->duracion)<p><strong>Duración:</strong> {{ $ins->curso->info->duracion }}</p>@endif
                        @if($ins->curso->info?->requisitos)<p><strong>Debes traer:</strong> {{ $ins->curso->info->requisitos }}</p>@endif
                        @if($ins->agenda?->notas)<p><strong>Indicaciones:</strong> {{ $ins->agenda->notas }}</p>@endif
                        @if($ins->curso->info?->incluye_certificado ?? true)<p class="text-green-700">Recibirás certificado al finalizar.</p>@endif
                    </div>
                @elseif($ins->estado === 'pendiente')
                    <p class="text-xs text-amber-700 bg-amber-50 rounded-xl p-3">Reservaste{{ $ins->fechaTexto() ? ': ' . $ins->fechaTexto() : '' }}. Pronto nos comunicamos contigo para el abono, la confirmación y el hospedaje.</p>
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

    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Cursos disponibles</h2>
    @if($disponibles->isEmpty())
        <p class="text-sm text-slate-500">Por ahora no hay más cursos disponibles.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($disponibles as $c)
            @php $libres = $c->cuposDisponibles(); @endphp
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <h3 class="font-bold text-slate-800">{{ $c->nombre }}</h3>
                    <span class="text-red-600 font-extrabold shrink-0">${{ number_format($c->costo, 0, ',', '.') }}</span>
                </div>
                <p class="text-sm text-slate-500 mb-3 line-clamp-3">{{ $c->descripcion }}</p>
                <div class="flex flex-wrap gap-2 text-[11px] text-slate-500 mb-4">
                    @if($c->info?->duracion)<span class="bg-slate-100 rounded-full px-2.5 py-1">{{ $c->info->duracion }}</span>@endif
                    <span class="bg-slate-100 rounded-full px-2.5 py-1">{{ $libres === null ? 'Cupos disponibles' : $libres . ' cupos libres' }}</span>
                    @if($c->info?->incluye_certificado ?? true)<span class="bg-green-50 text-green-700 rounded-full px-2.5 py-1">Con certificado</span>@endif
                </div>
                <div class="mt-auto flex items-center gap-2">
                                        @if($libres === null || $libres > 0)
                    <a href="{{ route('academia') }}#curso-{{ $c->id }}" class="ml-auto bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-xl text-sm transition">Elegir fecha</a>
                    @else
                        <span class="ml-auto text-xs font-semibold text-slate-400">Cupos agotados</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@include('partials.footer')
@endsection
