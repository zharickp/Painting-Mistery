@extends('layouts.guest')
@section('title', 'Detalle de la compra')

@section('content')
@include('partials.nav')

@php
    // Ventas antiguas sin número de orden no tienen datos del comprador.
    $compra = $venta->numero_orden ? $venta : null;
    $pago  = $venta->pago;
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    @include('cliente.partials.menu')

    <div class="mb-8 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs text-slate-500 mb-2">
                <a href="{{ route('mi-cuenta.inicio') }}" class="hover:text-red-600">Mi cuenta</a>
                <span class="mx-1">/</span>
                <a href="{{ route('mi-cuenta.pedidos') }}" class="hover:text-red-600">Mis compras</a>
                <span class="mx-1">/</span>
                <span class="text-slate-800 font-semibold">{{ $compra?->numero_orden ?? ('#' . $venta->id) }}</span>
            </p>
            <h1 class="text-3xl font-extrabold text-slate-800">Compra {{ $compra?->numero_orden ?? ('#' . $venta->id) }}</h1>
            <p class="text-sm text-slate-500 mt-1">Realizada el {{ $venta->fecha?->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            @if($compra)
                @include('partials.estado-orden', ['venta' => $venta])
                    <a href="{{ route('mi-cuenta.pedido.orden', $venta->id) }}" target="_blank"
                       class="mt-1 inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:text-red-700 border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver orden de venta
                    </a>
            @endif
        </div>
    </div>

    @if($venta->estado === 'cancelada')
        <div class="mb-6 bg-rose-50 border border-rose-200 rounded-2xl p-5 text-sm text-rose-900">
            <p class="font-bold">Esta compra fue cancelada</p>
            <p class="text-rose-800/80 mt-1">
                {{ $compra?->cancelada_at ? 'El ' . $compra->cancelada_at->format('d/m/Y H:i') . '. ' : '' }}{{ $compra?->motivo_cancelacion }}
                Se conserva en tu historial como referencia.
            </p>
        </div>
    @elseif($venta->estado === 'pendiente')
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-5 flex flex-wrap items-center justify-between gap-3 text-sm text-amber-900">
            <p><span class="font-bold">Pendiente de pago.</span> Si no se paga en 24 horas, se cancela automáticamente.</p>
            <div class="flex items-center gap-2">
                @if($compra)
                <a href="{{ route('checkout.pago', $venta->numero_orden) }}"
                   class="text-xs font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition">Pagar ahora</a>
                @endif
                <form method="POST" action="{{ route('mi-cuenta.pedido.cancelar', $venta->id) }}" onsubmit="return confirm('¿Cancelar esta compra? Seguirá apareciendo en tu historial como cancelada.')">
                    @csrf
                    <button class="text-xs font-bold text-rose-700 hover:text-rose-800 border border-rose-200 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition">Cancelar compra</button>
                </form>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Productos --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detalle</h2>
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
                    @foreach($venta->detalleCursos as $c)
                        <div class="px-6 py-4 flex items-center gap-4">
                            <div class="h-14 w-14 rounded-lg bg-gray-900 text-white flex items-center justify-center shrink-0 text-[10px] font-bold uppercase">Curso</div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ $c->curso?->nombre ?? 'Curso' }}</p>
                                <p class="text-xs text-slate-500">Inscripción al curso</p>
                            </div>
                            <p class="font-bold text-slate-800 shrink-0">${{ number_format($c->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($compra)
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Datos del comprador</h2>
                <div class="text-sm space-y-1">
                    <p class="font-semibold text-slate-800">{{ $compra->nombre_cliente }}</p>
                    @if($compra->numero_documento)
                        <p class="text-slate-500 text-xs">{{ $compra->tipo_documento }} {{ $compra->numero_documento }}</p>
                    @endif
                    <p class="text-slate-600 text-xs pt-1">{{ $compra->telefono_cliente }} · {{ $compra->correo_cliente }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Resumen --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sticky top-6 space-y-4">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Resumen</h2>

                <div class="flex justify-between font-bold text-base">
                    <span>Total <span class="text-xs font-normal text-slate-400">(IVA incluido)</span></span>
                    <span class="text-red-600">${{ number_format($venta->total, 0, ',', '.') }}</span>
                </div>

                @if($pago)
                <div class="pt-4 border-t border-slate-100 text-xs space-y-2 text-slate-600">
                    <div class="flex justify-between">
                        <span>Método de pago:</span>
                        <span class="text-slate-800">{{ $pago->metodoPago?->nombre ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Comprobante:</span>
                        <span class="font-mono text-slate-800 text-[10px] break-all">{{ $pago->numero_comprobante }}</span>
                    </div>
                    @if($pago->estado === 'aprobado' && $pago->fecha_pago)
                        <div class="flex justify-between">
                            <span>Pagada:</span>
                            <span class="text-slate-800">{{ $pago->fecha_pago->format('d/m/Y H:i') }}</span>
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
