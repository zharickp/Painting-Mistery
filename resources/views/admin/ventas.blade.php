@extends('layouts.app')
@section('title', 'Ventas')
@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Ventas</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $ventas->total() }} orden(es) registradas.</p>
    </div>
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Orden, cliente o correo…"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
        <select name="pago" onchange="this.form.submit()" class="rounded-lg border border-gray-200 px-3 py-2 text-sm bg-white">
            <option value="">Todos los pagos</option>
            <option value="APPROVED" @selected($pago === 'APPROVED')>Pago aprobado</option>
            <option value="PENDING" @selected($pago === 'PENDING')>Pendiente</option>
            <option value="DECLINED" @selected($pago === 'DECLINED')>Rechazado</option>
        </select>
        <button class="bg-gray-900 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Buscar</button>
        @if($buscar || $pago)<a href="{{ route('admin.ventas') }}" class="text-xs text-gray-500 hover:text-red-600">Limpiar</a>@endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Orden</th>
                    <th class="px-5 py-3">Cliente</th>
                    <th class="px-5 py-3">Fecha</th>
                    <th class="px-5 py-3 text-center">Pago</th>
                    <th class="px-5 py-3 text-center">Pedido</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-center">Documento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ventas as $v)
                @php $e = $v->envio; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $e?->numero_orden ?? '#' . str_pad($v->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $v->usuario->primer_nombre ?? '—' }} {{ $v->usuario->primer_apellido ?? '' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $v->fecha?->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($e)<span class="px-2 py-0.5 rounded-full text-xs font-semibold border {{ $e->paymentStatusColor() }}">{{ $e->paymentStatusEtiqueta() }}</span>@else — @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($e)<span class="px-2 py-0.5 rounded-full text-xs font-semibold border {{ $e->estadoPedidoColor() }}">{{ $e->estadoPedidoEtiqueta() }}</span>@else — @endif
                    </td>
                    <td class="px-5 py-3 text-right font-semibold">${{ number_format($v->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center">
                        @if($e && $e->payment_status === 'APPROVED')
                            <a href="{{ route('admin.ventas.orden', $v->id) }}" target="_blank" class="text-xs font-semibold text-red-600 hover:text-red-700">Orden de venta</a>
                        @else <span class="text-xs text-gray-300">—</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">No hay ventas que coincidan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ventas->hasPages())<div class="px-5 py-4 border-t border-gray-50">{{ $ventas->links() }}</div>@endif
</div>
@endsection
