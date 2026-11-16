{{-- Tarjeta de un indicador (inicio y reportes).
     Variables: $etiqueta, $valor, $detalle (opcional), $color (red|emerald|indigo|amber|slate|sky|violet),
     $icono (path SVG), $enlace (opcional) --}}
@php
    $colores = [
        'red'     => 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
        'indigo'  => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
        'amber'   => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        'slate'   => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        'sky'     => 'bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400',
        'violet'  => 'bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400',
    ];
    $etiquetaHtml = isset($enlace) && $enlace ? 'a' : 'div';
@endphp
<{{ $etiquetaHtml }} @if ($etiquetaHtml === 'a') href="{{ $enlace }}" @endif
    class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs {{ $etiquetaHtml === 'a' ? 'hover:shadow-md transition' : '' }}">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 {{ $colores[$color] ?? $colores['slate'] }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icono }}"/>
            </svg>
        </div>
        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider leading-tight">{{ $etiqueta }}</p>
    </div>
    <p class="text-2xl font-black text-slate-800 dark:text-slate-100 mt-3">{{ $valor }}</p>
    @if (!empty($detalle))
        <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $detalle }}</p>
    @endif
</{{ $etiquetaHtml }}>
