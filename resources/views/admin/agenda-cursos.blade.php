@extends('layouts.app')
@section('title', 'Agenda de cursos')
@section('content')

@php
    $anterior = $mes->copy()->subMonth()->format('Y-m');
    $siguiente = $mes->copy()->addMonth()->format('Y-m');
    $titulo = ucfirst($mes->copy()->locale('es')->isoFormat('MMMM YYYY'));
    $puedeGestionar = auth()->user()->tieneRol('Administrador', 'Asesor');
@endphp

<div class="mb-5 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Agenda de cursos</h1>
        <p class="text-sm text-gray-400 mt-1">Qué día hay curso y quién está inscrito. Las fechas se publican en Cursos → Editar.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.agenda-cursos.index', ['mes' => $anterior]) }}" class="h-9 w-9 rounded-lg border border-gray-200 bg-white hover:border-gray-400 flex items-center justify-center" aria-label="Mes anterior">‹</a>
        <span class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold min-w-[9rem] text-center">{{ $titulo }}</span>
        <a href="{{ route('admin.agenda-cursos.index', ['mes' => $siguiente]) }}" class="h-9 w-9 rounded-lg border border-gray-200 bg-white hover:border-gray-400 flex items-center justify-center" aria-label="Mes siguiente">›</a>
        @if(! $mes->isSameMonth(now()))<a href="{{ route('admin.agenda-cursos.index') }}" class="text-xs text-gray-500 hover:text-red-600 ml-1">Hoy</a>@endif
    </div>
</div>

{{-- Calendario --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-x-auto mb-8">
    <div class="min-w-[720px]">
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wide text-gray-400">
            @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $dn)<div class="px-2 py-2 text-center">{{ $dn }}</div>@endforeach
        </div>
        @foreach($semanas as $semana)
        <div class="grid grid-cols-7 border-b border-gray-50 last:border-0">
            @foreach($semana as $celda)
                @php $f = $celda['fecha']; $fuera = ! $f->isSameMonth($mes); @endphp
                <div class="min-h-[92px] p-1.5 border-r border-gray-50 last:border-0 {{ $fuera ? 'bg-gray-50/60' : '' }}">
                    <p class="text-xs font-semibold mb-1 {{ $f->isToday() ? 'inline-flex h-5 w-5 rounded-full bg-red-600 text-white items-center justify-center' : ($fuera ? 'text-gray-300' : 'text-gray-500') }}">{{ $f->day }}</p>
                    @foreach($celda['chips'] as $chip)
                        @php $s = $chip['sesion']; $largo = $s->dias > 1; @endphp
                        <a href="#sesion-{{ $s->id }}" class="block mb-1 rounded px-1.5 py-1 text-[10px] leading-tight font-semibold truncate {{ $largo ? 'bg-gray-900 text-white' : 'bg-red-100 text-red-800' }}"
                           title="{{ $s->curso->nombre }}">
                            {{ $largo ? 'Completo' : 'Polichado' }}@if($largo) · día {{ $chip['dia'] }}/{{ $s->dias }}@endif
                            @if($chip['dia'] === 1 && $s->inscritos->count()) · {{ $s->inscritos->count() }} insc.@endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

{{-- Sesiones del mes --}}
<h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3">Sesiones de {{ strtolower($titulo) }}</h2>
<div class="space-y-3">
    @forelse($sesiones as $s)
    <div id="sesion-{{ $s->id }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 scroll-mt-24">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="font-bold text-gray-800">{{ $s->curso->nombre }}</p>
                <p class="text-sm text-gray-500">{{ $s->etiqueta($s->dias) }} · {{ $s->dias }} {{ $s->dias === 1 ? 'día' : 'días' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">{{ $s->inscritos->count() }} inscrito(s)</span>
                <a href="{{ route('admin.cursos.inscripciones', $s->curso_id) }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Gestionar inscripciones →</a>
            </div>
        </div>
        @if($s->inscritos->isNotEmpty())
        <ul class="mt-3 divide-y divide-gray-50 text-sm">
            @foreach($s->inscritos as $i)
            <li class="py-2 flex flex-wrap items-center justify-between gap-2">
                <span class="text-gray-700">{{ $i->usuario->primer_nombre ?? '' }} {{ $i->usuario->primer_apellido ?? '' }}
                    <span class="text-gray-400 text-xs">· {{ $i->usuario->telefono ?? $i->usuario->correo ?? '' }} · {{ $i->codigoReserva() }}</span></span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $i->estadoColor() }}">{{ $i->estadoEtiqueta() }}</span>
            </li>
            @endforeach
        </ul>
        @else
            <p class="mt-3 text-xs text-gray-400">Sin inscritos todavía.</p>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-400">
        No hay sesiones programadas este mes.
        @if($puedeGestionar)<a href="{{ route('admin.cursos.index') }}" class="text-red-600 font-semibold">Publicar fechas →</a>@endif
    </div>
    @endforelse
</div>
@endsection
