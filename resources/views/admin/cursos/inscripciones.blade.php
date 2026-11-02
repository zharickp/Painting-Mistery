@extends('layouts.app')
@section('title', 'Inscripciones · ' . $curso->nombre)
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.cursos.index') }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a cursos</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Inscripciones · {{ $curso->nombre }}</h1>
    <p class="text-sm text-gray-400 mt-0.5">
        {{ $inscripciones->total() }} inscripción(es)
        @if($curso->cupos) · {{ $curso->cuposDisponibles() }} de {{ $curso->cupos }} cupos libres @endif
    </p>
</div>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div>
@endif

<div class="space-y-4">
    @forelse($inscripciones as $ins)
    @php $puedeEditar = auth()->user()->tieneRol('Administrador', 'Asesor'); @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
            <div>
                <p class="font-semibold text-gray-800">{{ $ins->usuario->primer_nombre ?? '' }} {{ $ins->usuario->primer_apellido ?? '' }}</p>
                <p class="text-xs text-gray-400">{{ $ins->usuario->correo ?? '' }} · solicitado {{ $ins->created_at->format('d/m/Y') }} · {{ $ins->codigoReserva() }}</p>
                @if($ins->fechaTexto())
                    <p class="text-xs text-gray-500 mt-1">Fecha del curso: <strong>{{ $ins->fechaTexto() }}</strong></p>
                @endif
                @if($ins->venta)
                    <p class="text-xs text-gray-500 mt-1">Pago:
                        <a href="{{ route('admin.ventas.show', $ins->venta->id) }}" class="font-semibold text-gray-700 hover:text-red-600">{{ $ins->venta->numero_orden }}</a>
                        · {{ $ins->venta->estadoEtiqueta() }}
                    </p>
                @endif
            </div>
            <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $ins->estadoColor() }}">{{ $ins->estadoEtiqueta() }}</span>
        </div>

        @if($ins->estado === 'pendiente' && $ins->venta?->estado === 'pendiente')
            <p class="mb-4 text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                Esperando el pago. Queda inscrito cuando se pague; si el pago ya se verificó, puedes confirmarlo desde
                <a href="{{ route('admin.ventas.show', $ins->venta->id) }}" class="font-semibold underline">la venta</a>.
            </p>
        @elseif($ins->esReservaAntigua())
            <p class="mb-4 text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
                Reserva de antes de los pagos en línea (no tiene venta). El cliente puede volver a inscribirse y pagar desde la página de cursos.
            </p>
        @endif

        @if($puedeEditar && $ins->estadosPermitidos() !== [])
        <form method="POST" action="{{ route('admin.inscripciones.update', $ins) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                @php
                    $nombresEstado = ['pendiente' => 'Pendiente de pago', 'confirmada' => 'Inscrito', 'completada' => 'Completado', 'cancelada' => 'Cancelar inscripción'];
                @endphp
                <select name="estado" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @foreach($ins->estadosPermitidos() as $e)
                        <option value="{{ $e }}" @selected($ins->estado === $e)>{{ $nombresEstado[$e] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Fecha del curso</label>
                <select name="curso_fecha_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @if(! $ins->curso_fecha_id)<option value="">Sin fecha</option>@endif
                    @foreach($curso->fechas as $f)
                        <option value="{{ $f->id }}" @selected($ins->curso_fecha_id === $f->id)>{{ $f->fecha->format('d/m/Y') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Indicaciones para el estudiante</label>
                <input type="text" name="indicaciones" value="{{ $ins->indicaciones }}" maxlength="1000" placeholder="Hora, qué traer, punto de encuentro..."
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-4 flex flex-wrap items-center gap-3">
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Guardar</button>
                @if($ins->venta && $ins->venta->estado !== 'cancelada')
                    <span class="text-[11px] text-gray-400">Si la cancelas, su venta también queda cancelada.@if($ins->venta->estado === 'pagada') La devolución del dinero se hace por fuera del sistema.@endif</span>
                @endif
            </div>
        </form>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center text-gray-400">Aún no hay solicitudes para este curso.</div>
    @endforelse
</div>

@if($inscripciones->hasPages())
<div class="mt-4">{{ $inscripciones->links() }}</div>
@endif

@endsection
