@extends('layouts.app')
@section('title', 'Inventario')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Inventario</h1>
    <p class="text-sm text-gray-400 mt-1">Stock actual de productos. Las alertas en rojo indican stock por debajo del mínimo.</p>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Producto</th>
                    <th class="px-5 py-3">Categoría</th>
                    <th class="px-5 py-3 text-center">Stock actual</th>
                    <th class="px-5 py-3 text-center">Stock mínimo</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-center">Última actualización</th>
                    @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
                    <th class="px-5 py-3 text-center">Ajustar</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($inventarios as $inv)
                @php $alerta = $inv->stock_actual <= $inv->stock_minimo; @endphp
                <tr class="hover:bg-gray-50 transition {{ $alerta ? 'bg-red-50/30' : '' }}">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($inv->producto->imagen)
                                <img src="{{ $inv->producto->imagen }}"
                                     alt="{{ $inv->producto->nombre }}"
                                     class="h-10 w-10 rounded-lg object-cover border border-gray-100">
                            @else
                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $inv->producto->nombre }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $inv->producto->categoria->nombre ?? '—' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            {{ $alerta ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $inv->stock_actual }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center text-gray-500 text-xs">
                        {{ $inv->stock_minimo }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($alerta)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-medium">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Stock bajo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Normal
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center text-gray-400 text-xs">
                        {{ \Carbon\Carbon::parse($inv->ultima_actualizacion)->format('d/m/Y H:i') }}
                    </td>
                    @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
                    <td class="px-5 py-3 text-center">
                        <button onclick="abrirModal({{ $inv->id }}, {{ $inv->stock_actual }}, {{ $inv->stock_minimo }}, '{{ addslashes($inv->producto->nombre) }}')"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">
                            Ajustar
                        </button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        No hay productos en el inventario.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">
        {{ $inventarios->links() }}
    </div>
</div>

{{-- Modal ajuste de stock --}}
@if(auth()->user()->tieneRol('Administrador', 'Asesor'))
<div id="modalInventario" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-800">Ajustar stock</h3>
                <p id="modalProductoNombre" class="text-xs text-gray-400 mt-0.5"></p>
            </div>
            <button onclick="cerrarModal()" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="formInventario" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock actual</label>
                <input type="number" name="stock_actual" id="inputStockActual"
                       min="0" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock mínimo</label>
                <input type="number" name="stock_minimo" id="inputStockMinimo"
                       min="0" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <p class="text-xs text-gray-400 mt-1">El sistema alertará cuando el stock baje de este número.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModal()"
                        class="flex-1 border border-gray-200 rounded-lg py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-lg py-2 text-sm font-semibold transition">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(id, stockActual, stockMinimo, nombre) {
    document.getElementById('modalProductoNombre').textContent = nombre;
    document.getElementById('inputStockActual').value = stockActual;
    document.getElementById('inputStockMinimo').value = stockMinimo;
    document.getElementById('formInventario').action = '/admin/inventario/' + id + '/actualizar';
    document.getElementById('modalInventario').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalInventario').classList.add('hidden');
}

document.getElementById('modalInventario').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endif

@endsection
