@extends('layouts.app')
@section('title', 'Inventario')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Inventario</h1>
    <p class="text-sm text-gray-400 mt-1">Control de stock de productos.</p>
</div>
@php $inventarios = \App\Models\Inventario::with('producto')->orderBy('cantidad')->paginate(20); @endphp
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Producto</th>
                    <th class="px-5 py-3 text-center">Cantidad</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($inventarios as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $inv->producto->nombre ?? '—' }}</td>
                    <td class="px-5 py-3 text-center font-semibold {{ $inv->cantidad <= 5 ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $inv->cantidad }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($inv->cantidad == 0)
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Sin stock</span>
                        @elseif($inv->cantidad <= 5)
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Stock bajo</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">OK</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">No hay registros de inventario.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">{{ $inventarios->links() }}</div>
</div>
@endsection
