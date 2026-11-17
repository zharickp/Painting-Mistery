@extends('layouts.app')
@section('title', 'Inscripciones · ' . $curso->nombre)
@section('content')

@php
    $colorSituacion = [
        'Próxima'    => 'bg-blue-50 text-blue-700',
        'En curso'   => 'bg-amber-50 text-amber-700',
        'Finalizada' => 'bg-gray-100 text-gray-500',
    ];
@endphp

<div class="mb-6">
    <a href="{{ route('admin.cursos.index') }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a cursos</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Inscripciones · {{ $curso->nombre }}</h1>
    <p class="text-sm text-gray-400 mt-0.5">
        {{ $curso->duracionTexto() }}@if($curso->cupos) · {{ $curso->cupos }} cupos por fecha @endif
    </p>
</div>

@foreach ([['Próximas fechas', $proximas, 'Este curso no tiene fechas próximas. Se publican al editar el curso.'], ['Fechas anteriores', $anteriores, null]] as [$titulo, $lista, $vacio])
    @continue($lista->isEmpty() && ! $vacio)
    <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3 {{ $loop->first ? '' : 'mt-8' }}">{{ $titulo }}</h2>

    @if($lista->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-10 text-center text-sm text-gray-400">{{ $vacio }}</div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">
        @foreach($lista as $f)
            @php
                $situacion = $f->situacion();
                $pct = $curso->cupos ? min(100, round($f->ocupados / $curso->cupos * 100)) : 0;
            @endphp
            <a href="{{ route('admin.cursos.inscripciones.fecha', [$curso, $f]) }}"
               class="flex flex-wrap items-center gap-4 px-5 py-4 hover:bg-gray-50/70 transition">
                <div class="flex-1 min-w-[12rem]">
                    <p class="font-semibold text-gray-800">{{ $f->etiqueta($curso->dias()) }}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $colorSituacion[$situacion] }}">{{ $situacion }}</span>
                </div>

                <div class="w-40">
                    <p class="text-xs text-gray-500 mb-1">
                        <strong class="text-gray-800">{{ $f->ocupados }}</strong>{{ $curso->cupos ? ' de ' . $curso->cupos : '' }} inscrito(s)
                    </p>
                    @if($curso->cupos)
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 60 ? 'bg-amber-400' : 'bg-green-400') }}" style="width: {{ $pct }}%"></div>
                        </div>
                    @endif
                </div>

                <div class="w-28 text-xs">
                    @if($f->pendientes > 0)
                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white font-semibold whitespace-nowrap">{{ $f->pendientes }} por pagar</span>
                    @endif
                </div>

                <span class="text-sm font-semibold text-red-600 whitespace-nowrap">Ver inscritos →</span>
            </a>
        @endforeach
    </div>
    @endif
@endforeach

@endsection
