@extends('layouts.app')

@section('title', 'Tarifas de envío')

@section('content')

<div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 text-white flex items-center justify-center shadow-lg shadow-red-950/20 shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Tarifas de envío</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Costos usados por el checkout para calcular el envío según destino.</p>
        </div>
    </div>
    <a href="{{ route('admin.tarifas-envio.create') }}"
       class="px-4 py-2 text-sm rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold flex items-center gap-2 shadow-md shadow-red-900/20 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva tarifa
    </a>
</div>

<div class="mb-6 bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900 rounded-2xl p-4 flex items-start gap-3">
    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="text-sm text-blue-800 dark:text-blue-200">
        <p class="font-semibold">Cómo se elige la tarifa</p>
        <p class="text-xs text-blue-700 dark:text-blue-300 mt-1 leading-relaxed">
            Al calcular el envío, el sistema busca primero una tarifa con el <strong>departamento + ciudad</strong> exactos. Si no la encuentra, usa la del <strong>departamento sin ciudad</strong> (comodín del depto). Si tampoco existe, aplica la tarifa <strong>global</strong> (departamento y ciudad vacíos). Si el subtotal supera el umbral configurado, el envío es <strong>gratis</strong>.
        </p>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/40">
                <tr class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-5 py-3 text-left font-semibold">Departamento</th>
                    <th class="px-5 py-3 text-left font-semibold">Ciudad</th>
                    <th class="px-5 py-3 text-right font-semibold">Precio base</th>
                    <th class="px-5 py-3 text-right font-semibold">Envío gratis desde</th>
                    <th class="px-5 py-3 text-center font-semibold">Estado</th>
                    <th class="px-5 py-3 text-center font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($tarifas as $t)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3 font-semibold text-slate-800 dark:text-slate-100">
                            {{ $t->departamento ?: '— Global —' }}
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-300">
                            {{ $t->ciudad ?: 'Todas del departamento' }}
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-slate-800 dark:text-slate-100">
                            ${{ number_format($t->precio_base, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-right text-slate-600 dark:text-slate-300">
                            {{ $t->umbral_envio_gratis
                                ? '$' . number_format($t->umbral_envio_gratis, 0, ',', '.')
                                : '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                {{ $t->activo ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                              : 'bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $t->activo ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $t->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.tarifas-envio.edit', $t) }}"
                                   class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition" title="Editar">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.tarifas-envio.toggle', $t) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition
                                                {{ $t->activo
                                                    ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100'
                                                    : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100' }}">
                                        {{ $t->activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            No hay tarifas configuradas todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $tarifas->links() }}</div>
@endsection
