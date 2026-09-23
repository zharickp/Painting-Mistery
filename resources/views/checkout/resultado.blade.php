@extends('layouts.guest')
@section('title', 'Resultado del pago')

@section('content')
@include('partials.nav')

<div class="max-w-3xl mx-auto px-4 py-12">
    @php $ok = $envio->payment_status === 'APPROVED'; $pending = $envio->payment_status === 'PENDING'; @endphp

    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm text-center">
        @if($ok)
            <div class="mx-auto h-16 w-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">¡Pago aprobado!</h1>
            <p class="text-sm text-slate-500 mt-1">Tu orden fue confirmada correctamente.</p>
        @elseif($pending)
            <div class="mx-auto h-16 w-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">Pago pendiente</h1>
            <p class="text-sm text-slate-500 mt-1">Estamos esperando la confirmación de Wompi. Puedes cerrar esta página; te notificaremos cuando se confirme.</p>
        @else
            <div class="mx-auto h-16 w-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">Pago no completado</h1>
            <p class="text-sm text-slate-500 mt-1">Puedes intentar de nuevo desde tu carrito.</p>
        @endif

        <div class="mt-8 border-t border-slate-100 pt-6 grid grid-cols-2 gap-4 text-left text-sm">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">N° de orden</p>
                <p class="font-mono font-bold text-slate-800">{{ $envio->numero_orden }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Referencia Wompi</p>
                <p class="font-mono text-xs text-slate-700 break-all">{{ $envio->wompi_reference }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Estado del pago</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $envio->paymentStatusColor() }}">
                    {{ $envio->paymentStatusEtiqueta() }}
                </span>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Estado del pedido</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $envio->estadoPedidoColor() }}">
                    {{ $envio->estadoPedidoEtiqueta() }}
                </span>
            </div>
        </div>

        <div class="mt-4 border-t border-slate-100 pt-4 space-y-1.5 text-sm text-left max-w-xs mx-auto">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span>${{ number_format($envio->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Envío</span>
                <span>
                    @if((float) $envio->envio === 0.0) <span class="font-bold text-emerald-600">GRATIS</span>
                    @else ${{ number_format($envio->envio, 0, ',', '.') }} @endif
                </span>
            </div>
            <div class="flex justify-between font-bold text-slate-800 pt-1.5 border-t border-slate-100">
                <span>Total</span>
                <span class="text-red-600">${{ number_format($envio->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('mi-cuenta.pedido', $envio->venta_id) }}"
               class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition">
                Ver detalle del pedido
            </a>
            <a href="{{ route('tienda.index') }}"
               class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition">
                Seguir comprando
            </a>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
