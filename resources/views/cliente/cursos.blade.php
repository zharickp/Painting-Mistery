@extends('layouts.app')
@section('title', 'Mis Cursos')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Mis Cursos</h1>
    <p class="text-sm text-gray-400 mt-1">Cursos en los que estás inscrito.</p>
</div>
@php $inscripciones = auth()->user()->inscripciones()->with(['curso.info','agenda'])->latest()->paginate(10); @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($inscripciones as $ins)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="h-10 w-10 bg-red-600 text-white rounded-xl flex items-center justify-center mb-3">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="font-semibold text-gray-800">{{ $ins->curso->nombre ?? 'Curso' }}</h3>
            <span class="px-2 py-0.5 text-[11px] rounded-full font-semibold shrink-0 {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
        </div>
        <p class="text-xs text-gray-400 mb-3">Solicitado el {{ $ins->created_at->format('d/m/Y') }}</p>
        @if($ins->estado === 'confirmada' || $ins->estado === 'completada')
            <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 text-xs text-gray-700 space-y-1">
                @if($ins->agenda?->fecha_confirmada)<p><strong>Fecha:</strong> {{ $ins->agenda->fecha_confirmada->format('d/m/Y') }}</p>@endif
                <p><strong>Lugar:</strong> {{ $ins->curso->info?->ubicacion ?: 'Taller Painting Mistery, Melgar – Tolima' }}</p>
                @if($ins->curso->info?->requisitos)<p><strong>Debes traer:</strong> {{ $ins->curso->info->requisitos }}</p>@endif
                @if($ins->agenda?->notas)<p><strong>Indicaciones:</strong> {{ $ins->agenda->notas }}</p>@endif
                @if($ins->curso->info?->incluye_certificado ?? true)<p class="text-green-700">Recibirás certificado al finalizar.</p>@endif
            </div>
        @elseif($ins->estado === 'pendiente')
            <p class="text-xs text-amber-700 bg-amber-50 rounded-lg p-3">Estamos revisando tu solicitud{{ $ins->agenda?->fecha_preferida ? ' (fecha preferida: ' . $ins->agenda->fecha_preferida->format('d/m/Y') . ')' : '' }}. Te confirmaremos la fecha por aquí.</p>
        @endif
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-gray-400">
        <p class="mb-3">Aún no estás inscrito en ningún curso.</p>
        <a href="{{ route('academia') }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm transition">Ver cursos disponibles</a>
    </div>
    @endforelse
</div>
@endsection
