@extends('layouts.app')
@section('title', 'Buscar')
@section('content')

@php
    // Cada grupo: título, filas [texto principal, detalle, enlace, etiqueta opcional, color de etiqueta]
    $grupos = [];
    if ($resultados) {
        $grupos['Productos'] = $resultados['productos']->map(fn ($p) => [
            $p->nombre,
            '$' . number_format($p->precio, 0, ',', '.') . ' · ' . $p->stockActual() . ' en inventario',
            $puedeEditar ? route('admin.productos.edit', $p) : route('producto.show', $p),
            $p->estado ? null : 'Inactivo', 'bg-gray-100 text-gray-500 border-gray-200',
        ]);
        $grupos['Cursos'] = $resultados['cursos']->map(fn ($c) => [
            $c->nombre,
            '$' . number_format($c->precio, 0, ',', '.') . ' · ' . $c->duracionTexto(),
            $puedeEditar ? route('admin.cursos.edit', $c) : route('admin.cursos.inscripciones', $c),
            $c->estado ? null : 'Inactivo', 'bg-gray-100 text-gray-500 border-gray-200',
        ]);
        $grupos['Ventas'] = $resultados['ventas']->map(fn ($v) => [
            $v->numero_orden,
            trim(($v->nombre_cliente ?: 'Cliente') . ' · ' . ($v->fecha?->format('d/m/Y') ?? '') . ' · $' . number_format($v->total, 0, ',', '.')),
            route('admin.ventas.show', $v->id),
            $v->estadoEtiqueta(), $v->estadoColor(),
        ]);
        if ($resultados['usuarios'] !== null) {
            $grupos['Usuarios'] = $resultados['usuarios']->map(fn ($u) => [
                $u->nombreCompleto(),
                $u->correo . ' · ' . $u->roles->pluck('nombre')->join(', '),
                route('admin.usuarios.edit', $u),
                $u->estado ? null : 'Inactivo', 'bg-gray-100 text-gray-500 border-gray-200',
            ]);
        }
    }
    $total = collect($grupos)->sum(fn ($g) => $g->count());
@endphp

<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-800 dark:text-slate-100">Buscar</h1>
    @if($resultados)
        <p class="text-sm text-gray-400 mt-1">{{ $total }} {{ $total === 1 ? 'resultado' : 'resultados' }} para "{{ $q }}"</p>
    @endif
</div>

@if(! $resultados)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-dashed border-gray-200 dark:border-slate-700 p-12 text-center text-gray-400">
        Escribe al menos 2 letras en la barra de búsqueda de arriba.
    </div>
@elseif($total === 0)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-dashed border-gray-200 dark:border-slate-700 p-12 text-center text-gray-400">
        No se encontró nada con "{{ $q }}".
    </div>
@else
    <div class="space-y-5">
        @foreach($grupos as $titulo => $filas)
            @continue($filas->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 dark:text-slate-100">{{ $titulo }}</h2>
                    <span class="text-xs text-gray-400">{{ $filas->count() }}</span>
                </div>
                @foreach($filas->take($porGrupo) as [$principal, $detalle, $url, $etiqueta, $color])
                    <a href="{{ $url }}" class="flex items-center justify-between gap-4 px-5 py-3 border-b border-gray-50 dark:border-slate-800 last:border-0 hover:bg-gray-50 transition">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 dark:text-slate-100 truncate">{{ $principal }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 truncate">{{ $detalle }}</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            @if($etiqueta)
                                <span class="px-2 py-0.5 text-[11px] rounded-full font-semibold border {{ $color }}">{{ $etiqueta }}</span>
                            @endif
                            <span class="text-gray-300">›</span>
                        </div>
                    </a>
                @endforeach
                @if($filas->count() > $porGrupo)
                    <p class="px-5 py-2 text-xs text-gray-400">Y {{ $filas->count() - $porGrupo }} más. Escribe algo más específico para afinar.</p>
                @endif
            </div>
        @endforeach
    </div>
@endif

@endsection
