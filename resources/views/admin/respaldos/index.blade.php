@extends('layouts.app')

@section('title', 'Copias de Seguridad')

@section('content')

{{-- Cabecera --}}
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 text-white flex items-center justify-center shadow-lg shadow-red-950/20 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 rest:text-stone-800">Copias de Seguridad</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Se generan automáticamente cada 15 días. También puedes generar una manualmente.
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('dashboard') }}"
           class="px-4 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold flex items-center gap-2 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
        <form method="POST" action="{{ route('admin.respaldos.store') }}"
              onsubmit="return confirm('¿Generar ahora una nueva copia de seguridad?');">
            @csrf
            <button type="submit"
                    class="px-4 py-2 text-sm rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold flex items-center gap-2 shadow-md shadow-red-900/20 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Generar ahora
            </button>
        </form>
    </div>
</div>

{{-- KPIs de resumen --}}
@php
    $totalCopias = count($copias);
    $tamTotal    = array_sum(array_column($copias, 'tamano_bytes'));
    $ultima      = $copias[0] ?? null;
    $formatearTam = function($bytes) {
        if ($bytes < 1024) return $bytes.' B';
        if ($bytes < 1024*1024) return round($bytes/1024,1).' KB';
        if ($bytes < 1024*1024*1024) return round($bytes/(1024*1024),1).' MB';
        return round($bytes/(1024*1024*1024),2).' GB';
    };
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total de copias</p>
                <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalCopias }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Espacio ocupado</p>
                <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $formatearTam($tamTotal) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Última copia</p>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                    {{ $ultima ? $ultima['fecha']->format('d/m/Y H:i') : 'Sin copias' }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Listado --}}
<div class="bg-white dark:bg-slate-900 rest:bg-amber-100/70 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Historial de copias</h2>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Más reciente primero</span>
    </div>

    @if(empty($copias))
        <div class="px-6 py-16 text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Todavía no hay copias de seguridad</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Genera la primera con el botón "Generar ahora".</p>
        </div>
    @else
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($copias as $copia)
                <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                    <div class="h-11 w-11 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate font-mono">
                            {{ $copia['nombre'] }}
                        </p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $copia['fecha']->format('d/m/Y H:i') }}
                            </span>
                            <span class="text-slate-300 dark:text-slate-600">·</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
                                </svg>
                                {{ $copia['tamano_legible'] }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.respaldos.download', $copia['nombre']) }}"
                           class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition" title="Descargar">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.respaldos.destroy', $copia['nombre']) }}"
                              onsubmit="return confirm('¿Está seguro de eliminar esta copia de seguridad?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition" title="Eliminar">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Info sobre el proceso --}}
<div class="mt-6 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900 rounded-2xl p-4 flex items-start gap-3">
    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="text-sm text-blue-800 dark:text-blue-200">
        <p class="font-semibold">Sobre las copias automáticas</p>
        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1 leading-relaxed">
            El sistema genera copias los días 1 y 16 de cada mes a las 3:00 a.m. (aproximadamente cada 15 días). Los archivos se guardan en un directorio privado del servidor y no son accesibles públicamente. La restauración de una copia es un procedimiento técnico manual — no se ejecuta desde esta interfaz por seguridad.
        </p>
    </div>
</div>
@endsection
