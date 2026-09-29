@extends('layouts.app')
@section('title', 'Orden ' . ($venta->numero_orden ?? $venta->id))
@section('content')

@php
    // Datos del comprador y del pago (ventas antiguas sin número de orden no los tienen).
    $e = $venta->numero_orden ? $venta : null;
    $pago = $venta->pago;
    $puedeGestionar = auth()->user()->tieneRol('Administrador', 'Asesor');
@endphp

<div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
    <div>
        <a href="{{ route('admin.ventas') }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver al historial</a>
        <div class="flex flex-wrap items-center gap-3 mt-2">
            <h1 class="text-xl font-bold text-gray-800 font-mono">{{ $e?->numero_orden ?? '#' . $venta->id }}</h1>
            @include('partials.estado-orden', ['venta' => $venta])
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($e)
            <a href="{{ route('admin.ventas.orden', $venta->id) }}" target="_blank" class="px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:border-gray-400 transition">Orden de venta</a>
        @endif
        @if($puedeGestionar && $venta->estado === 'pendiente')
            <form method="POST" action="{{ route('admin.ventas.confirmar-pago', $venta->id) }}" onsubmit="return confirm('¿Confirmar el pago de esta venta? Se registra como pago en efectivo y se descuenta el inventario.')">
                @csrf
                <button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold transition">Confirmar pago</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif
@if($errors->any())<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div>@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50 text-sm font-bold text-gray-800">Detalle</div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr class="text-xs text-gray-400 uppercase text-left">
                    <th class="px-5 py-2.5">Descripción</th><th class="px-3 py-2.5 text-center">Cant.</th><th class="px-3 py-2.5 text-right">Precio</th><th class="px-5 py-2.5 text-right">Subtotal</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($venta->detalleProductos as $d)
                    <tr>
                        <td class="px-5 py-3 text-gray-800">{{ $d->producto_nombre ?? $d->producto?->nombre ?? 'Producto' }}</td>
                        <td class="px-3 py-3 text-center text-gray-600">{{ $d->cantidad }}</td>
                        <td class="px-3 py-3 text-right text-gray-600">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-semibold">${{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @foreach($venta->detalleCursos as $c)
                    <tr>
                        <td class="px-5 py-3 text-gray-800">Curso: {{ $c->curso?->nombre ?? 'Curso' }}</td>
                        <td class="px-3 py-3 text-center text-gray-600">1</td>
                        <td class="px-3 py-3 text-right text-gray-600">${{ number_format($c->precio_unitario, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-semibold">${{ number_format($c->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @php $iva = (float) $venta->detalleProductos->sum('iva'); @endphp
            <div class="px-5 py-4 border-t border-gray-50 text-sm space-y-1 ml-auto max-w-xs">
                @if($iva > 0)<div class="flex justify-between text-gray-400 text-xs"><span>IVA (incluido)</span><span>${{ number_format($iva, 0, ',', '.') }}</span></div>@endif
                <div class="flex justify-between font-bold text-gray-900 text-base"><span>Total</span><span>${{ number_format($venta->total, 0, ',', '.') }}</span></div>
            </div>
        </div>

        @if($puedeGestionar && $venta->estado !== 'cancelada')
        <div class="bg-white rounded-xl border border-rose-100 shadow-sm p-5">
            <h2 class="text-sm font-bold text-rose-700 mb-1">Cancelar venta</h2>
            <p class="text-xs text-gray-500 mb-3">La venta no se elimina: queda como cancelada con quién, cuándo y por qué.
                @if($venta->estado === 'pagada') Como ya estaba pagada, el inventario se devolverá. @endif</p>
            <form method="POST" action="{{ route('admin.ventas.cancelar', $venta->id) }}" class="flex flex-col sm:flex-row gap-3"
                  onsubmit="return confirm('¿Cancelar esta venta? Queda registrado en la auditoría.')">
                @csrf
                <input type="text" name="motivo" required minlength="3" maxlength="255" placeholder="Motivo de la cancelación"
                       class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-rose-400">
                <button class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold transition">Cancelar venta</button>
            </form>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-sm space-y-2">
            <h2 class="text-sm font-bold text-gray-800 mb-1">Información</h2>
            <p><span class="text-gray-400">Cliente:</span> <span class="font-medium text-gray-800">{{ $venta->usuario->primer_nombre ?? '—' }} {{ $venta->usuario->primer_apellido ?? '' }}</span></p>
            <p class="text-gray-500 text-xs">{{ $venta->usuario->correo ?? '' }}</p>
            <p><span class="text-gray-400">Creada:</span> {{ $venta->fecha?->format('d/m/Y H:i') }}</p>
            <p><span class="text-gray-400">Última actualización:</span> {{ $venta->updated_at?->format('d/m/Y H:i') }}</p>
            @if($pago?->estado === 'aprobado' && $pago->fecha_pago)
                <p><span class="text-gray-400">Pagada:</span> {{ $pago->fecha_pago->format('d/m/Y H:i') }}
                    @if($venta->pagoConfirmadoPor) <span class="text-gray-400">(confirmó</span> {{ $venta->pagoConfirmadoPor->primer_nombre }} {{ $venta->pagoConfirmadoPor->primer_apellido }}<span class="text-gray-400">)</span>@endif</p>
            @endif
            @if($venta->estado === 'cancelada')
                <div class="rounded-lg bg-rose-50 border border-rose-100 p-3 text-xs text-rose-800 space-y-1">
                    <p class="font-bold">Venta cancelada</p>
                    <p>Fecha: {{ $e?->cancelada_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    <p>Responsable: {{ $e?->canceladaPor ? $e->canceladaPor->primer_nombre . ' ' . $e->canceladaPor->primer_apellido : 'Sistema / cliente' }}</p>
                    <p>Motivo: {{ $e?->motivo_cancelacion ?: '—' }}</p>
                </div>
            @endif
        </div>

        @if($e)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-sm space-y-1.5">
            <h2 class="text-sm font-bold text-gray-800 mb-1">Comprador</h2>
            <p class="text-gray-700">{{ $e->nombre_cliente }}</p>
            <p class="text-gray-500 text-xs">{{ $e->tipo_documento }} {{ $e->numero_documento }}</p>
            <p class="text-gray-500 text-xs">{{ $e->telefono_cliente }} · {{ $e->correo_cliente }}</p>
        </div>
        @endif

        @if($pago)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-sm space-y-1.5">
            <h2 class="text-sm font-bold text-gray-800 mb-1">Pago</h2>
            <p><span class="text-gray-400">Método:</span> {{ $pago->metodoPago?->nombre ?? '—' }}</p>
            <p><span class="text-gray-400">Estado:</span> {{ $pago->estadoEtiqueta() }}</p>
            <p><span class="text-gray-400">Comprobante:</span> <span class="font-mono text-xs">{{ $pago->numero_comprobante }}</span></p>
        </div>
        @endif
    </div>
</div>
@endsection
