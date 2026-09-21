@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@php
    $etiquetas = [
        'precio_costo'              => 'Precio costo',
        'precio'                    => 'Precio',
        'precio_anterior'           => 'Precio anterior',
        'stock_actual'              => 'Stock actual',
        'stock_minimo'              => 'Stock mínimo',
        'unidad_presentacion'       => 'Unidad de presentación',
        'cantidad_por_presentacion' => 'Cantidad por presentación',
        'nombre'                    => 'Nombre',
        'descripcion'               => 'Descripción',
        'estado'                    => 'Estado',
        'categoria_producto_id'     => 'Categoría',
        'tipo_iva_id'               => 'Tipo de IVA',
        'primer_nombre'             => 'Primer nombre',
        'segundo_nombre'            => 'Segundo nombre',
        'primer_apellido'           => 'Primer apellido',
        'segundo_apellido'          => 'Segundo apellido',
        'correo'                    => 'Correo',
        'telefono'                  => 'Teléfono',
        'imagen'                    => 'Imagen',
    ];
    $etiqueta = fn ($campo) => $etiquetas[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
    $formato  = function ($valor) {
        if ($valor === null || $valor === '') return '—';
        if (is_bool($valor)) return $valor ? 'Sí' : 'No';
        if (is_array($valor)) return json_encode($valor, JSON_UNESCAPED_UNICODE);
        return (string) $valor;
    };
    $cambios = $registro->cambiosNormalizados();
    $ua = $registro->user_agent ?? '';
    $navegador = str_contains($ua, 'Firefox') ? 'Firefox'
        : (str_contains($ua, 'Edg/') ? 'Edge'
        : (str_contains($ua, 'Chrome') ? 'Chrome'
        : (str_contains($ua, 'Safari') ? 'Safari' : 'Desconocido')));
    $so = str_contains($ua, 'Windows') ? 'Windows'
        : (str_contains($ua, 'Mac OS') ? 'macOS'
        : (str_contains($ua, 'Android') ? 'Android'
        : (str_contains($ua, 'Linux') ? 'Linux' : 'Desconocido')));
@endphp

<style>
@media print {
    aside, header, .no-print { display: none !important; }
    main { padding: 0 !important; }
    body { background: white !important; }
}
</style>

@section('content')

{{-- Cabecera --}}
<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 text-white flex items-center justify-center shadow-lg shadow-red-950/20 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 rest:text-stone-800">Detalle de Auditoría</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $registro->accionColor() }}">
                    {{ $registro->accionEtiqueta() }}
                </span>
                <span>{{ $registro->modulo }}</span>
                @if($registro->registro_etiqueta)
                    <span class="text-slate-400">·</span>
                    <span class="italic">"{{ $registro->registro_etiqueta }}"</span>
                @endif
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2 no-print">
        <button onclick="window.print()"
                class="px-4 py-2 text-sm rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold flex items-center gap-2 shadow-md shadow-red-900/20 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar PDF
        </button>
        <a href="{{ route('admin.auditoria.index') }}"
           class="px-4 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold flex items-center gap-2 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ─── Tarjeta 1: Información general (2 cols) ─── --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rest:bg-amber-100/70 border border-slate-200/80 dark:border-slate-800 rest:border-amber-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Información general</h2>
        </div>

        <div class="px-6 py-5 space-y-4 text-sm">
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 3.076a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Acción</p>
                    <p class="text-slate-800 dark:text-slate-100 font-semibold mt-0.5">{{ $registro->accionEtiqueta() }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tipo afectado</p>
                    <p class="text-slate-800 dark:text-slate-100 font-semibold mt-0.5">
                        {{ $registro->modulo }}
                        @if($registro->registro_id)
                            <span class="text-slate-400 dark:text-slate-500 font-normal">· ID {{ $registro->registro_id }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Fecha y hora</p>
                    <p class="text-slate-800 dark:text-slate-100 font-semibold mt-0.5">
                        {{ $registro->fechaMostrada()?->format('d/m/Y') }}
                        <span class="text-slate-400 dark:text-slate-500 font-normal">·</span>
                        {{ $registro->fechaMostrada()?->format('h:i:s A') }}
                    </p>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-800">

            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Quién lo hizo</p>
                    <p class="text-slate-800 dark:text-slate-100 font-semibold mt-0.5">{{ $registro->usuario_nombre ?: '—' }}</p>
                    @if($registro->usuario_correo)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $registro->usuario_correo }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Origen</p>
                    <p class="text-slate-800 dark:text-slate-100 font-mono text-xs mt-0.5">{{ $registro->ip_address ?: '—' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $navegador }} · {{ $so }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Tarjeta 2: Qué cambió (3 cols) ─── --}}
    <div class="lg:col-span-3 bg-white dark:bg-slate-900 rest:bg-amber-100/70 border border-slate-200/80 dark:border-slate-800 rest:border-amber-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Qué cambió</h2>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Comparación entre el valor anterior y el nuevo</p>
                </div>
            </div>
            @if(!empty($cambios))
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-full">
                    {{ count($cambios) }} {{ count($cambios) === 1 ? 'cambio' : 'cambios' }}
                </span>
            @endif
        </div>

        @if(empty($cambios))
            <div class="px-6 py-16 text-center">
                <div class="mx-auto h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                    <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Sin cambios registrados</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto">
                    Esta acción no modificó datos del registro. Habitual en inicios de sesión, cierres de sesión y consultas.
                </p>
            </div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($cambios as $campo => $valores)
                    <div class="px-6 py-4">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                            {{ $etiqueta($campo) }}
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr] gap-3 items-center">
                            {{-- Antes --}}
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 px-3 py-2.5">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-0.5">Antes</p>
                                <p class="text-sm text-slate-700 dark:text-slate-200 break-words">
                                    {{ $formato($valores['antes'] ?? null) }}
                                </p>
                            </div>

                            {{-- Flecha --}}
                            <div class="hidden sm:flex items-center justify-center text-red-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </div>
                            <div class="sm:hidden flex justify-center text-red-500 text-xs font-bold">↓</div>

                            {{-- Después --}}
                            <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50/50 dark:bg-red-900/20 px-3 py-2.5">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-red-500 dark:text-red-400 mb-0.5">Después</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 break-words">
                                    {{ $formato($valores['despues'] ?? null) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
