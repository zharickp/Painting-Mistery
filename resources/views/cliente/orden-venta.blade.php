<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de venta {{ $venta->envio->numero_orden }} — Painting Mistery</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-10 print:py-0 print:bg-white">
@php $envio = $venta->envio; @endphp

<div class="max-w-3xl mx-auto px-4">

    <div class="flex items-center justify-between mb-4 no-print">
        <a href="{{ $volver }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-red-600 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar / Imprimir
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10 print:shadow-none print:border-0">

        {{-- Cabecera --}}
        <div class="flex items-start justify-between border-b border-slate-100 pb-6 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-full overflow-hidden ring-2 ring-red-500/30 shrink-0">
                    <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="Painting Mistery" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="font-extrabold text-slate-800 text-lg leading-tight">Painting <span class="text-red-600">Mistery</span></p>
                    <p class="text-xs text-slate-400">Cl. 4 #35-42 casa 13, Sicomoro · Melgar, Tolima — Colombia</p>
                    <p class="text-xs text-slate-400">WhatsApp +57 314 455 7602 · paintingmistery20@gmail.com</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Orden de venta</p>
                <p class="font-mono font-black text-slate-800 text-lg">{{ $envio->numero_orden }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $venta->fecha?->format('d/m/Y h:i A') }}</p>
                <p class="mt-1.5 inline-block px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $envio->estadoPedidoColor() }}">{{ $envio->estadoPedidoEtiqueta() }}</p>
            </div>
        </div>

        {{-- Datos del cliente --}}
        <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Cliente</p>
                <p class="font-semibold text-slate-800">{{ $envio->nombre_envio }}</p>
                @if($envio->numero_documento)
                    <p class="text-slate-500 text-xs">{{ $envio->tipo_documento }} {{ $envio->numero_documento }}</p>
                @endif
                <p class="text-slate-500 text-xs">{{ $envio->correo_envio }} · {{ $envio->telefono_envio }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Enviado a</p>
                <p class="text-slate-700">{{ $envio->direccion_envio }}</p>
                <p class="text-slate-500 text-xs">{{ $envio->ciudad_envio }}, {{ $envio->departamento_envio }}</p>
                @if($envio->referencia_envio)<p class="text-slate-400 text-xs mt-0.5">Ref.: {{ $envio->referencia_envio }}</p>@endif
            </div>
        </div>

        {{-- Productos --}}
        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-200">
                    <th class="py-2 text-left font-bold">Producto</th>
                    <th class="py-2 text-center font-bold">Cant.</th>
                    <th class="py-2 text-right font-bold">Precio unit.</th>
                    <th class="py-2 text-right font-bold">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($venta->detalleProductos as $d)
                    <tr>
                        <td class="py-3 text-slate-800 font-medium">{{ $d->producto_nombre ?? $d->producto?->nombre ?? 'Producto' }}</td>
                        <td class="py-3 text-center text-slate-600">{{ $d->cantidad }}</td>
                        <td class="py-3 text-right text-slate-600">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
                        <td class="py-3 text-right font-semibold text-slate-800">${{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totales --}}
        <div class="flex justify-end mb-8">
            <div class="w-56 space-y-1.5 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($envio->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Envío</span>
                    <span>
                        @if((float) $envio->envio === 0.0) GRATIS
                        @else ${{ number_format($envio->envio, 0, ',', '.') }} @endif
                    </span>
                </div>
                @php $ivaTotal = (float) $venta->detalleProductos->sum('iva'); @endphp
                @if($ivaTotal > 0)
                    <div class="flex justify-between text-slate-400 text-xs">
                        <span>IVA (incluido)</span>
                        <span>${{ number_format($ivaTotal, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-slate-800 text-base pt-2 border-t border-slate-200">
                    <span>Total</span>
                    <span class="text-red-600">${{ number_format($venta->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Pago y observaciones --}}
        <div class="border-t border-slate-100 pt-6 text-xs text-slate-500 space-y-1">
            <p>Método de pago: <span class="font-semibold text-slate-700">{{ $envio->wompi_payment_method ?: 'Pago en línea' }}</span></p>
            <p>Estado del pago: <span class="font-semibold text-slate-700">{{ $envio->paymentStatusEtiqueta() }}</span>@if($envio->fecha_pago) · {{ $envio->fecha_pago->format('d/m/Y h:i A') }}@endif</p>
            <p>Referencia de pago: <span class="font-mono">{{ $envio->wompi_reference }}</span></p>
            <p class="text-slate-400">Observaciones: {{ $envio->referencia_envio ?: 'Sin observaciones.' }}</p>
        </div>

        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-[11px] text-slate-400 leading-relaxed">
            Documento interno generado por el sistema de Painting Mistery para el seguimiento de la orden.
            No constituye factura de venta ni factura electrónica.
        </div>
        <p class="pt-4 text-xs text-slate-400 text-center">Gracias por tu compra en Painting Mistery.</p>
    </div>
</div>

</body>
</html>
