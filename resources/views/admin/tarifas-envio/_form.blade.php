<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.tarifas-envio.index') }}"
       class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">{{ $titulo }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Define destino y precio. Deja los campos vacíos para crear comodines.</p>
    </div>
</div>

<form method="POST" action="{{ $action }}"
      class="max-w-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-6 shadow-xs space-y-5">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Departamento</label>
            <input type="text" name="departamento" value="{{ old('departamento', $tarifa?->departamento) }}"
                   placeholder="Ej: Tolima (deja vacío para tarifa global)"
                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:border-red-500 focus:outline-none">
            @error('departamento')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Ciudad</label>
            <input type="text" name="ciudad" value="{{ old('ciudad', $tarifa?->ciudad) }}"
                   placeholder="Ej: Melgar (vacío = todo el departamento)"
                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:border-red-500 focus:outline-none">
            @error('ciudad')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Precio base (COP) *</label>
            <input type="number" step="1" min="0" name="precio_base" required
                   value="{{ old('precio_base', $tarifa?->precio_base) }}"
                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:border-red-500 focus:outline-none">
            @error('precio_base')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1">Envío gratis desde (COP)</label>
            <input type="number" step="1" min="0" name="umbral_envio_gratis"
                   value="{{ old('umbral_envio_gratis', $tarifa?->umbral_envio_gratis) }}"
                   placeholder="Opcional"
                   class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:border-red-500 focus:outline-none">
            <p class="text-[11px] text-slate-400 mt-1">Si el subtotal supera este valor, el envío será gratis.</p>
        </div>
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" name="activo" value="1"
               {{ old('activo', $tarifa?->activo ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-red-600 focus:ring-red-500">
        Tarifa activa
    </label>

    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.tarifas-envio.index') }}"
           class="px-4 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold transition">
            Cancelar
        </a>
        <button type="submit"
                class="px-5 py-2 text-sm rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow-md shadow-red-900/20 transition">
            Guardar
        </button>
    </div>
</form>
