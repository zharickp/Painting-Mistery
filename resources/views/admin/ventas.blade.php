@extends('layouts.app')
@section('title', 'Ventas')
@section('content')

@php
    $tabs = [
        [null,        'Todas',      $conteos->sum()],
        ['pendiente', 'Pendientes', $conteos['pendiente'] ?? 0],
        ['pagada',    'Pagadas',    $conteos['pagada'] ?? 0],
        ['cancelada', 'Canceladas', $conteos['cancelada'] ?? 0],
    ];
    $params = fn (array $o = []) => array_filter(array_merge(['buscar' => $buscar, 'fecha' => $fecha, 'estado' => $estado], $o), fn ($v) => $v !== null && $v !== '');
@endphp

<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-800">Historial de ventas</h1>
    <p class="text-sm text-gray-400 mt-1">Todas las órdenes se conservan: las canceladas no se eliminan.</p>
</div>

@if(session('success'))<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif

<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
    <div class="flex flex-wrap gap-2">
        @foreach($tabs as [$valor, $label, $n])
            @php $on = ($estado ?: null) === $valor; @endphp
            <a href="{{ route('admin.ventas', $params(['estado' => $valor])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border transition {{ $on ? 'bg-gray-900 border-gray-900 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-400' }}">
                {{ $label }}
                <span class="text-[11px] rounded-full px-2 py-0.5 {{ $on ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }}">{{ $n }}</span>
            </a>
        @endforeach
    </div>
    <form method="GET" class="flex flex-wrap items-center gap-2">
        @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Orden (#0015), cliente o correo…"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400 w-56">
        <input type="date" name="fecha" value="{{ $fecha }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
        <button class="bg-gray-900 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Buscar</button>
        @if($buscar || $fecha)<a href="{{ route('admin.ventas', $params(['buscar' => null, 'fecha' => null])) }}" class="text-xs text-gray-500 hover:text-red-600">Limpiar</a>@endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3"># Orden</th>
                    <th class="px-5 py-3">Cliente</th>
                    <th class="px-5 py-3">Fecha</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ventas as $v)
                <tr class="hover:bg-gray-50 {{ $v->estado === 'cancelada' ? 'bg-rose-50/30' : '' }}">
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $v->envio?->numero_orden ?? '#' . str_pad($v->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $v->usuario->primer_nombre ?? '—' }} {{ $v->usuario->primer_apellido ?? '' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $v->fecha?->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 text-center">@include('partials.estado-orden', ['venta' => $v])</td>
                    <td class="px-5 py-3 text-right font-semibold {{ $v->estado === 'cancelada' ? 'text-gray-400 line-through' : '' }}">${{ number_format($v->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center">
                        <a href="{{ route('admin.ventas.show', $v->id) }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Ver detalle</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">No hay órdenes que coincidan con el filtro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ventas->hasPages())<div class="px-5 py-4 border-t border-gray-50">{{ $ventas->links() }}</div>@endif
</div>
@endsection
