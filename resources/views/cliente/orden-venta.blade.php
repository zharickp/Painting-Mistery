<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de venta {{ $venta->numero_orden }} — Painting Mistery</title>
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
@php $compra = $venta; @endphp

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

    <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10 print:shadow-none print:border-0">

        @if($venta->estado === 'cancelada')
            <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                <span class="-rotate-12 text-7xl sm:text-8xl font-black tracking-widest text-rose-500/15 border-8 border-rose-500/15 rounded-2xl px-6">CANCELADA</span>
            </div>
        @endif

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
                <p class="font-mono font-black text-slate-800 text-lg">{{ $compra->numero_orden }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $venta->fecha?->format('d/m/Y h:i A') }}</p>
                <p class="mt-1.5 inline-block px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wide border {{ $venta->estadoColor() }}">Estado: {{ $venta->estado === 'pagada' ? 'Pagada / Confirmada' : $venta->estadoEtiqueta() }}</p>
            </div>
        </div>

        {{-- Datos del cliente --}}
        <div class="mb-8 text-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Cliente</p>
                <p class="font-semibold text-slate-800">{{ $compra->nombre_cliente }}</p>
                @if($compra->numero_documento)
                    <p class="text-slate-500 text-xs">{{ $compra->tipo_documento }} {{ $compra->numero_documento }}</p>
                @endif
                <p class="text-slate-500 text-xs">{{ $compra->correo_cliente }} · {{ $compra->telefono_cliente }}</p>
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
                @php $ivaTotal = (float) $venta->detalleProductos->sum('iva'); @endphp
                @if($ivaTotal > 0)
                    <div class="flex justify-between text-slate-400 text-xs">
                        <span>IVA (incluido)</span>
                        <span>${{ number_format($ivaTotal, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-slate-800 text-base">
                    <span>{{ $venta->estado === 'pagada' ? 'Total pagado' : 'Total' }}</span>
                    <span class="text-red-600">${{ number_format($venta->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Pago y observaciones --}}
        <div class="border-t border-slate-100 pt-6 text-xs text-slate-500 space-y-1">
            @php $pago = $venta->pago; @endphp
            <p>Método de pago: <span class="font-semibold text-slate-700">{{ $pago?->metodoPago?->nombre ?? '—' }}</span></p>
            <p>Estado del pago: <span class="font-semibold text-slate-700">{{ $pago?->estadoEtiqueta() ?? 'Sin registro' }}</span>@if($pago?->estado === 'aprobado' && $pago->fecha_pago) · {{ $pago->fecha_pago->format('d/m/Y h:i A') }}@endif</p>
            @if($venta->estado === 'cancelada')
                <p class="text-rose-700">Cancelada el {{ $compra->cancelada_at?->format('d/m/Y h:i A') ?? '—' }}@if($compra->motivo_cancelacion) · Motivo: {{ $compra->motivo_cancelacion }}@endif</p>
            @elseif($venta->estado === 'pendiente')
                <p class="text-amber-700">Pago pendiente. Este documento no acredita el pago.</p>
            @endif
            @if($pago)<p>Comprobante: <span class="font-mono">{{ $pago->numero_comprobante }}</span></p>@endif
        </div>

        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-[11px] text-slate-400 leading-relaxed">
            Documento interno generado por el sistema de Painting Mistery como soporte de la venta.
            No constituye factura de venta ni factura electrónica.
        </div>
        <p class="pt-4 text-xs text-slate-400 text-center">Gracias por tu compra en Painting Mistery.</p>
    </div>
</div>

</body>
</html>
