@extends('layouts.guest')
@section('title', 'Mi cuenta')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800">Hola, {{ auth()->user()->primer_nombre }}</h1>
        <p class="text-sm text-slate-500 mt-1">Bienvenido a tu cuenta de Painting Mistery.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['Total de pedidos', $stats['total'], 'red',     'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
            ['Pendientes',      $stats['pendientes'], 'amber', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Aprobados',       $stats['aprobadas'],  'emerald', 'M5 13l4 4L19 7'],
            ['Entregados',      $stats['entregadas'], 'blue',   'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ] as [$label, $val, $color, $path])
            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="h-10 w-10 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center mb-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                    </svg>
                </div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">{{ $label }}</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ $val }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Últimos pedidos</h2>
            <a href="{{ route('mi-cuenta.pedidos') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">
                Ver todos →
            </a>
        </div>

        @if($ventas->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm text-slate-500">Aún no tienes pedidos.</p>
                <a href="{{ route('tienda.index') }}" class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2 rounded-xl text-sm">
                    Ir a la tienda
                </a>
            </div>
        @else
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
                            <td class="px-6 py-3 text-slate-600">{{ $v->fecha?->format('d/m/Y') }}</td>
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
        @endif
    </div>
</div>

@include('partials.footer')
@endsection
