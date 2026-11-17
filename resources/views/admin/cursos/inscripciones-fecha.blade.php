@extends('layouts.app')
@section('title', 'Inscritos · ' . $curso->nombre)
@section('content')

@php
    $puedeEditar = auth()->user()->tieneRol('Administrador', 'Asesor');
    $activos = $inscripciones->whereIn('estado', \App\Models\Inscripcion::ESTADOS_ACTIVOS)->count();
    $situacion = $fecha->situacion();
    $nombresEstado = ['pendiente' => 'Pendiente de pago', 'confirmada' => 'Inscrito', 'cancelada' => 'Cancelar inscripción'];
@endphp

<div class="mb-6">
    <a href="{{ route('admin.cursos.inscripciones', $curso) }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a las fechas</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">{{ $curso->nombre }}</h1>
    <p class="text-sm text-gray-500 mt-0.5">
        {{ $fecha->etiqueta($curso->dias()) }} ·
        <strong class="text-gray-700">{{ $activos }}</strong>{{ $curso->cupos ? ' de ' . $curso->cupos : '' }} inscrito(s) ·
        {{ $situacion }}
    </p>
</div>

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div>
@endif

@if($fecha->yaEmpezo())
    <p class="mb-4 text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
        Fecha {{ $fecha->yaTermino() ? 'finalizada' : 'en curso' }}: las inscripciones ya no se modifican.
    </p>
@endif

<div class="space-y-3">
    @forelse($inscripciones as $ins)
        @php $permitidos = $puedeEditar ? $ins->estadosPermitidos() : []; @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="font-semibold text-gray-800">{{ $ins->usuario?->nombreCompleto() ?? 'Usuario eliminado' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $ins->usuario->correo ?? '' }}@if($ins->usuario?->telefono) · {{ $ins->usuario->telefono }}@endif
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $ins->codigoReserva() }} · inscrito el {{ $ins->created_at->format('d/m/Y') }}
                        @if($ins->venta)
                            · <a href="{{ route('admin.ventas.show', $ins->venta->id) }}" class="font-semibold text-gray-600 hover:text-red-600">{{ $ins->venta->numero_orden }}</a>
                            ({{ $ins->venta->estadoEtiqueta() }})
                        @endif
                    </p>
                </div>
                <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
            </div>

            @if($ins->estado === 'pendiente' && $ins->venta?->estado === 'pendiente')
                <p class="mt-3 text-xs text-amber-800 bg-amber-50 rounded-lg px-3 py-2">
                    Pago pendiente · <a href="{{ route('admin.ventas.show', $ins->venta->id) }}" class="font-semibold underline">ver venta</a>
                </p>
            @endif

            @if($ins->indicaciones && $permitidos === [])
                <p class="mt-3 text-xs text-gray-600"><strong>Nota:</strong> {{ $ins->indicaciones }}</p>
            @endif

            @if($permitidos !== [])
                <form method="POST" action="{{ route('admin.inscripciones.update', $ins) }}"
                      class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                        <select name="estado" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            @foreach($permitidos as $e)
                                <option value="{{ $e }}" @selected($ins->estado === $e)>{{ $nombresEstado[$e] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Pasar a otra fecha</label>
                        <select name="curso_fecha_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            @foreach($fechasDestino as $f)
                                <option value="{{ $f->id }}" @selected($ins->curso_fecha_id === $f->id)>{{ $f->fecha->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nota para el estudiante</label>
                        <input type="text" name="indicaciones" value="{{ $ins->indicaciones }}" maxlength="1000" placeholder="Opcional"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Guardar</button>
                    </div>
                    @if($ins->venta && $ins->venta->estado !== 'cancelada')
                        <p class="sm:col-span-2 lg:col-span-4 text-[11px] text-gray-400">Cancelarla también cancela su venta.</p>
                    @endif
                </form>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center text-gray-400">Nadie se ha inscrito en esta fecha.</div>
    @endforelse
</div>

@endsection
