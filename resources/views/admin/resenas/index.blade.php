@extends('layouts.app')
@section('title', 'Reseñas')
@section('content')

@php
    $tabs = [
        ['pendiente', 'Pendientes', $conteos['pendiente'] ?? 0],
        ['aprobada',  'Aprobadas',  $conteos['aprobada'] ?? 0],
        ['rechazada', 'Rechazadas', $conteos['rechazada'] ?? 0],
        ['todas',     'Todas',      $conteos->sum()],
    ];
    $params = fn (array $o = []) => array_filter(array_merge(['estado' => $estado, 'buscar' => $buscar, 'calificacion' => $calificacion], $o), fn ($v) => $v !== null && $v !== '');
    $colorEstado = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'aprobada'  => 'bg-green-100 text-green-700',
        'rechazada' => 'bg-gray-100 text-gray-500',
    ];
@endphp

<div class="mb-5 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-slate-100">Reseñas de productos</h1>
        <p class="text-sm text-gray-400 mt-1">Una reseña se muestra en la tienda cuando se aprueba. Si el cliente la edita, vuelve a quedar pendiente.</p>
    </div>
    <div class="text-right text-sm">
        <p class="text-xs text-gray-400">Promedio publicado</p>
        <p class="font-bold text-gray-800 dark:text-slate-100"><span class="text-yellow-500">★</span> {{ ($conteos['aprobada'] ?? 0) ? number_format($promedio, 1, ',', '.') : '—' }}</p>
    </div>
</div>

<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
    <div class="flex flex-wrap gap-2">
        @foreach($tabs as [$valor, $label, $n])
            @php $on = $estado === $valor; @endphp
            <a href="{{ route('admin.resenas.index', $params(['estado' => $valor])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border transition {{ $on ? 'bg-gray-900 border-gray-900 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-400' }}">
                {{ $label }}
                <span class="text-[11px] rounded-full px-2 py-0.5 {{ $on ? 'bg-white/20' : ($valor === 'pendiente' && $n > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">{{ $n }}</span>
            </a>
        @endforeach
    </div>
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <input type="hidden" name="estado" value="{{ $estado }}">
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Producto, cliente o comentario"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400 w-60">
        <select name="calificacion" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
            <option value="">Todas las estrellas</option>
            @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected($calificacion === $i)>{{ $i }} {{ $i === 1 ? 'estrella' : 'estrellas' }}</option>
            @endfor
        </select>
        <button class="bg-gray-900 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Buscar</button>
        @if($buscar !== '' || $calificacion)
            <a href="{{ route('admin.resenas.index', ['estado' => $estado]) }}" class="text-xs text-gray-500 hover:text-red-600">Limpiar</a>
        @endif
    </form>
</div>

<div class="space-y-3">
    @forelse($resenas as $r)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 shadow-sm p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mb-1">
                <span class="text-yellow-500 text-sm tracking-tight">{{ str_repeat('★', $r->calificacion) }}<span class="text-gray-300 dark:text-slate-600">{{ str_repeat('★', 5 - $r->calificacion) }}</span></span>
                <p class="font-semibold text-gray-800 dark:text-slate-100">{{ $r->nombreMostrar() }}</p>
                <span class="px-2 py-0.5 text-[11px] rounded-full font-semibold {{ $colorEstado[$r->estado] ?? 'bg-gray-100 text-gray-500' }}">{{ $r->estadoEtiqueta() }}</span>
                <span class="text-xs text-gray-400">{{ $r->updated_at?->format('d/m/Y H:i') }}</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">
                Producto:
                @if($r->producto)
                    <a href="{{ route('producto.show', $r->producto_id) }}" target="_blank" class="font-medium text-gray-700 dark:text-slate-300 hover:text-red-600">{{ $r->producto->nombre }}</a>
                @else
                    <span class="italic">producto eliminado</span>
                @endif
            </p>
            <p class="text-sm text-gray-600 dark:text-slate-300 whitespace-pre-line break-words">{{ $r->comentario }}</p>
        </div>
        @if($puedeEditar)
        <div class="flex flex-wrap gap-2 shrink-0">
            @if($r->estado !== 'aprobada')
                <form method="POST" action="{{ route('admin.resenas.aprobar', $r) }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 text-xs font-semibold transition">Aprobar</button>
                </form>
            @endif
            @if($r->estado !== 'rechazada')
                <form method="POST" action="{{ route('admin.resenas.rechazar', $r) }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold transition">Rechazar</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.resenas.destroy', $r) }}"
                  onsubmit="return confirm('¿Eliminar esta reseña? No se puede recuperar.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold transition">Eliminar</button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-dashed border-gray-200 dark:border-slate-700 p-12 text-center text-gray-400">
        @if($buscar !== '' || $calificacion)
            No hay reseñas con esos filtros.
        @elseif($estado === 'pendiente')
            No hay reseñas pendientes de revisar.
        @else
            No hay reseñas en esta lista.
        @endif
    </div>
    @endforelse
</div>

@if($resenas->hasPages())<div class="mt-4">{{ $resenas->links() }}</div>@endif

@endsection
