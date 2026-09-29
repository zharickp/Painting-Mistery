@extends('layouts.guest')
@section('title', 'Resultado del pago')

@section('content')
@include('partials.nav')

<div class="max-w-3xl mx-auto px-4 py-12">
    @php $ok = $venta->estado === 'pagada'; $pending = $venta->estado === 'pendiente'; @endphp

    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm text-center">
        @if($ok)
            <div class="mx-auto h-16 w-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">¡Pago aprobado!</h1>
            @if($venta->detalleCursos->isNotEmpty())
                <p class="text-sm text-slate-500 mt-1">Tu inscripción quedó confirmada. Te escribiremos para coordinar la llegada y el hospedaje.</p>
            @else
                <p class="text-sm text-slate-500 mt-1">Tu compra quedó registrada. La encuentras en Mis compras.</p>
            @endif
        @elseif($pending)
            <div class="mx-auto h-16 w-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">Pago pendiente</h1>
            <p class="text-sm text-slate-500 mt-1">La compra está creada pero todavía no se ha pagado.</p>
            <a href="{{ route('checkout.pago', $venta->numero_orden) }}" class="inline-block mt-3 text-sm font-semibold text-red-600 hover:text-red-700">Ir a pagar →</a>
        @else
            <div class="mx-auto h-16 w-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">Pago no completado</h1>
            <p class="text-sm text-slate-500 mt-1">El pago fue rechazado y la compra se canceló. Puedes volver a intentarlo desde la tienda.</p>
        @endif

        <div class="mt-8 border-t border-slate-100 pt-6 grid grid-cols-2 gap-4 text-left text-sm">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">N.º de orden</p>
                <p class="font-mono font-bold text-slate-800">{{ $venta->numero_orden }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Método de pago</p>
                <p class="text-slate-800">{{ $venta->pago?->metodoPago?->nombre ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Comprobante</p>
                <p class="font-mono text-xs text-slate-700 break-all">{{ $venta->pago?->numero_comprobante ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Estado</p>
                @include('partials.estado-orden', ['venta' => $venta])
            </div>
        </div>

        <div class="mt-4 border-t border-slate-100 pt-4 text-sm text-left max-w-xs mx-auto">
            <div class="flex justify-between font-bold text-slate-800">
                <span>Total <span class="text-xs font-normal text-slate-400">(IVA incluido)</span></span>
                <span class="text-red-600">${{ number_format($venta->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('mi-cuenta.pedido', $venta->id) }}"
               class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition">
                Ver detalle de la compra
            </a>
            @if($venta->detalleCursos->isNotEmpty())
            <a href="{{ route('mi-cuenta.cursos') }}"
               class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition">
                Ver mis cursos
            </a>
            @else
            <a href="{{ route('tienda.index') }}"
               class="border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition">
                Seguir comprando
            </a>
            @endif
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
