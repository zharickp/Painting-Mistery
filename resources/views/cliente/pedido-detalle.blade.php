@extends('layouts.guest')
@section('title', 'Detalle del pedido')

@section('content')
@include('partials.nav')

@php $envio = $venta->envio; @endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs text-slate-500 mb-2">
                <a href="{{ route('mi-cuenta.inicio') }}" class="hover:text-red-600">Mi cuenta</a>
                <span class="mx-1">/</span>
                <a href="{{ route('mi-cuenta.pedidos') }}" class="hover:text-red-600">Mis pedidos</a>
                <span class="mx-1">/</span>
                <span class="text-slate-800 font-semibold">{{ $envio?->numero_orden ?? ('#' . $venta->id) }}</span>
            </p>
            <h1 class="text-3xl font-extrabold text-slate-800">Pedido {{ $envio?->numero_orden ?? ('#' . $venta->id) }}</h1>
            <p class="text-sm text-slate-500 mt-1">Realizado el {{ $venta->fecha?->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            @if($envio)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $envio->paymentStatusColor() }}">
                    Pago: {{ $envio->paymentStatusEtiqueta() }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $envio->estadoPedidoColor() }}">
                    Pedido: {{ $envio->estadoPedidoEtiqueta() }}
                </span>
            @endif
        </div>
    </div>

    {{-- Línea de progreso --}}
    @if($envio)
    @php
        $pasos = ['pendiente','confirmado','preparando','enviado','entregado'];
        $actual = array_search($envio->estado_pedido, $pasos);
        $actual = $actual === false ? 0 : $actual;
        $cancelado = $envio->estado_pedido === 'cancelado';
    @endphp
    @if(!$cancelado)
    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            @foreach(['Recibido','Confirmado','Preparando','Enviado','Entregado'] as $i => $paso)
                <div class="flex-1 flex flex-col items-center relative">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $i <= $actual ? 'bg-red-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                        {{ $i + 1 }}
                    </div>
                    <p class="text-[11px] text-center mt-2 {{ $i <= $actual ? 'text-slate-800 font-semibold' : 'text-slate-400' }}">{{ $paso }}</p>
                    @if($i < 4)
                        <div class="absolute top-4 left-1/2 w-full h-0.5 {{ $i < $actual ? 'bg-red-600' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Productos --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Productos</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($venta->detalleProductos as $d)
                        <div class="px-6 py-4 flex items-center gap-4">
                            <div class="h-14 w-14 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                @if($d->producto?->imagen)
                                    <img src="{{ $d->producto->imagen }}" alt="" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 truncate">
                                    {{ $d->producto_nombre ?? $d->producto?->nombre ?? 'Producto' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $d->cantidad }} × ${{ number_format($d->precio_unitario, 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="font-bold text-slate-800 shrink-0">
                                ${{ number_format($d->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($envio)
            {{-- Envío --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Datos de envío y facturación</h2>
                <div class="text-sm space-y-1">
                    <p class="font-semibold text-slate-800">{{ $envio->nombre_envio }}</p>
                    @if($envio->numero_documento)
                        <p class="text-slate-500 text-xs">{{ $envio->tipo_documento }} {{ $envio->numero_documento }}</p>
                    @endif
                    <p class="text-slate-600">{{ $envio->direccion_envio }}</p>
                    <p class="text-slate-600">{{ $envio->ciudad_envio }}, {{ $envio->departamento_envio }}</p>
                    @if($envio->referencia_envio)
                        <p class="text-xs text-slate-500 italic">{{ $envio->referencia_envio }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-slate-600 pt-2 border-t border-slate-100 mt-2 text-xs">
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $envio->telefono_envio }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $envio->correo_envio }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Resumen --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sticky top-6 space-y-4">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Resumen</h2>

                <div class="text-sm space-y-2 border-b border-slate-100 pb-4">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($envio?->subtotal ?? $venta->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Envío</span>
                        <span>
                            @if(($envio?->envio ?? 0) == 0) GRATIS
                            @else ${{ number_format($envio->envio, 0, ',', '.') }} @endif
                        </span>
                    </div>
                </div>
                <div class="flex justify-between font-bold text-base">
                    <span>Total</span>
                    <span class="text-red-600">${{ number_format($venta->total, 0, ',', '.') }}</span>
                </div>

                @if($envio)
                <div class="pt-4 border-t border-slate-100 text-xs space-y-2 text-slate-600">
                    <div class="flex justify-between">
                        <span>Referencia:</span>
                        <span class="font-mono text-slate-800">{{ $envio->wompi_reference }}</span>
                    </div>
                    @if($envio->wompi_transaction_id)
                        <div class="flex justify-between">
                            <span>Transacción:</span>
                            <span class="font-mono text-slate-800 text-[10px] break-all">{{ $envio->wompi_transaction_id }}</span>
                        </div>
                    @endif
                    @if($envio->fecha_pago)
                        <div class="flex justify-between">
                            <span>Pagado:</span>
                            <span class="text-slate-800">{{ $envio->fecha_pago->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
