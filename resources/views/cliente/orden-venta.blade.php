@extends('layouts.documento')

@php
    $pago      = $venta->pago;
    $ivaTotal  = (float) $venta->detalleProductos->sum('iva');
    $claseEstado = match ($venta->estado) {
        'pagada'    => 'verde',
        'cancelada' => 'rojo',
        default     => 'amarillo',
    };
@endphp

@section('titulo', 'Orden de venta ' . $venta->numero_orden)
@section('volver', $volver)
@section('tipo', 'Orden de venta')
@section('numero', $venta->numero_orden)
@section('fecha', $venta->fecha?->format('d/m/Y h:i A'))
@section('estado')
    <span class="estado {{ $claseEstado }}">{{ $venta->estado === 'pagada' ? 'Pagada' : $venta->estadoEtiqueta() }}</span>
@endsection
@if ($venta->estado === 'cancelada')
    @section('sello', 'CANCELADA')
@endif

@section('contenido')
    <div class="datos">
        <div class="caja">
            <h3>Cliente</h3>
            <p class="fuerte">{{ $venta->nombre_cliente }}</p>
            @if ($venta->numero_documento)
                <p class="suave">{{ $venta->tipo_documento }} {{ $venta->numero_documento }}</p>
            @endif
            <p class="suave">{{ $venta->correo_cliente }}@if ($venta->telefono_cliente) · {{ $venta->telefono_cliente }}@endif</p>
        </div>
        <div class="caja">
            <h3>Pago</h3>
            <p><strong>Método:</strong> {{ $pago?->metodoPago?->nombre ?? '—' }}</p>
            <p><strong>Estado:</strong> {{ $pago?->estadoEtiqueta() ?? 'Sin registro' }}@if ($pago?->estado === 'aprobado' && $pago->fecha_pago) · {{ $pago->fecha_pago->format('d/m/Y h:i A') }}@endif</p>
            @if ($pago)
                <p class="suave">Comprobante: {{ $pago->numero_comprobante }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="centro">Cant.</th>
                <th class="der">Precio unitario</th>
                <th class="der">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalleProductos as $d)
                <tr>
                    <td>{{ $d->producto_nombre ?? $d->producto?->nombre ?? 'Producto' }}</td>
                    <td class="centro num">{{ $d->cantidad }}</td>
                    <td class="der num">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
                    <td class="der num"><strong>${{ number_format($d->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
            @foreach ($venta->detalleCursos as $c)
                <tr>
                    <td>Curso: {{ $c->curso?->nombre ?? 'Curso' }}</td>
                    <td class="centro num">1</td>
                    <td class="der num">${{ number_format($c->precio_unitario, 0, ',', '.') }}</td>
                    <td class="der num"><strong>${{ number_format($c->subtotal, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        @if ($ivaTotal > 0)
            <div class="fila"><span>Subtotal sin IVA</span><span class="num">${{ number_format($venta->total - $ivaTotal, 0, ',', '.') }}</span></div>
            <div class="fila"><span>IVA incluido</span><span class="num">${{ number_format($ivaTotal, 0, ',', '.') }}</span></div>
        @endif
        <div class="fila total"><span>{{ $venta->estado === 'pagada' ? 'Total pagado' : 'Total' }}</span><span class="num">${{ number_format($venta->total, 0, ',', '.') }}</span></div>
    </div>

    @if ($venta->estado === 'cancelada')
        <div class="nota" style="border-left-color:#dc2626;color:#991b1b;background:#fef2f2">
            Venta cancelada el {{ $venta->fecha_cancelacion?->format('d/m/Y h:i A') ?? '—' }}@if ($venta->motivo_cancelacion). Motivo: {{ $venta->motivo_cancelacion }}@endif.
        </div>
    @elseif ($venta->estado === 'pendiente')
        <div class="nota" style="border-left-color:#d97706;color:#92400e;background:#fffbeb">
            El pago de esta orden está pendiente. Este documento no acredita el pago.
        </div>
    @endif

    <div class="nota">
        Documento interno generado por el sistema de Painting Mistery como soporte de la venta.
        No constituye factura de venta ni factura electrónica.
    </div>
    <p class="pie">Gracias por tu compra en Painting Mistery.</p>
@endsection
