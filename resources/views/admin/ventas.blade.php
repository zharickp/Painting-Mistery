@extends('layouts.app')
@section('title', 'Ventas')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Ventas</h1>
    <p class="text-sm text-gray-400 mt-1">Historial completo de ventas.</p>
</div>
@php $ventas = \App\Models\Venta::with('usuario')->orderByDesc('fecha')->paginate(15); @endphp
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3"># Orden</th>
                    <th class="px-5 py-3">Cliente</th>
                    <th class="px-5 py-3">Fecha</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ventas as $v)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-400 font-mono">#{{ str_pad($v->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800">
                        {{ $v->usuario->primer_nombre ?? '—' }} {{ $v->usuario->primer_apellido ?? '' }}
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 text-center">
                        @php $ec=['pagada'=>'bg-green-100 text-green-700','pendiente'=>'bg-yellow-100 text-yellow-700','cancelada'=>'bg-red-100 text-red-700']; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ec[$v->estado]??'bg-gray-100 text-gray-600' }}">{{ ucfirst($v->estado) }}</span>
                    </td>
                    <td class="px-5 py-3 text-right font-semibold">${{ number_format($v->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No hay ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">{{ $ventas->links() }}</div>
</div>
@endsection
