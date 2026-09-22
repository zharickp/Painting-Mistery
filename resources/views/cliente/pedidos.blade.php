@extends('layouts.guest')
@section('title', 'Mis pedidos')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs text-slate-500 mb-2">
                <a href="{{ route('mi-cuenta.inicio') }}" class="hover:text-red-600">Mi cuenta</a>
                <span class="mx-1">/</span>
                <span class="text-slate-800 font-semibold">Mis pedidos</span>
            </p>
            <h1 class="text-3xl font-extrabold text-slate-800">Mis pedidos</h1>
            <p class="text-sm text-slate-500 mt-1">Consulta el historial de tus compras.</p>
        </div>
        <a href="{{ route('mi-cuenta.inicio') }}"
           class="text-sm text-slate-600 hover:text-red-600 flex items-center gap-1">
            ← Volver
        </a>
    </div>

    @if($ventas->isEmpty())
        <div class="bg-white border border-slate-200 rounded-2xl p-16 text-center">
            <svg class="h-16 w-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p class="text-lg font-semibold text-slate-700">Aún no tienes pedidos</p>
            <p class="text-sm text-slate-500 mt-1">Explora nuestro catálogo y empieza a comprar.</p>
            <a href="{{ route('tienda.index') }}" class="inline-block mt-6 bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm">
                Ir a la tienda
            </a>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-3 text-left font-semibold">Orden</th>
                            <th class="px-6 py-3 text-left font-semibold">Fecha</th>
                            <th class="px-6 py-3 text-right font-semibold">Total</th>
                            <th class="px-6 py-3 text-center font-semibold">Pago</th>
                            <th class="px-6 py-3 text-center font-semibold">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ventas as $v)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 font-mono font-semibold text-slate-800">
                                    {{ $v->envio?->numero_orden ?? '#' . $v->id }}
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $v->fecha?->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3 text-right font-bold text-slate-800">
                                    ${{ number_format($v->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $v->envio?->paymentStatusColor() ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $v->envio?->paymentStatusEtiqueta() ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $v->envio?->estadoPedidoColor() ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $v->envio?->estadoPedidoEtiqueta() ?? ucfirst($v->estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('mi-cuenta.pedido', $v->id) }}" class="text-red-600 hover:text-red-700 text-xs font-semibold">
                                        Ver detalle →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $ventas->links() }}</div>
    @endif
</div>

@include('partials.footer')
@endsection
