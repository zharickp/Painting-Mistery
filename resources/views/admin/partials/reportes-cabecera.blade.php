{{-- Cabecera común de los reportes: pestañas Ventas / Compras, año y botón de PDF.
     Variables: $activo ('ventas' | 'compras'), $anio, $anios --}}
@php
    $rutaPagina = $activo === 'ventas' ? 'admin.reportes' : 'admin.reportes.compras';
    $rutaPdf    = $activo === 'ventas' ? 'admin.reportes.ventas.pdf' : 'admin.reportes.compras.pdf';
@endphp
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Reportes</h1>
        <div class="mt-3 inline-flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 text-sm font-semibold">
            <a href="{{ route('admin.reportes', ['anio' => $anio]) }}"
               class="px-4 py-1.5 rounded-lg transition {{ $activo === 'ventas' ? 'bg-white dark:bg-slate-900 text-red-600 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                Ventas
            </a>
            <a href="{{ route('admin.reportes.compras', ['anio' => $anio]) }}"
               class="px-4 py-1.5 rounded-lg transition {{ $activo === 'compras' ? 'bg-white dark:bg-slate-900 text-red-600 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                Compras
            </a>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <form method="GET" action="{{ route($rutaPagina) }}">
            <label class="sr-only" for="anioReporte">Año</label>
            <select id="anioReporte" name="anio" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-semibold text-slate-700 dark:text-slate-200 px-3 py-2">
                @foreach ($anios as $a)
                    <option value="{{ $a }}" @selected($a === $anio)>Año {{ $a }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route($rutaPdf, ['anio' => $anio]) }}" target="_blank"
           class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Descargar PDF
        </a>
    </div>
</div>
