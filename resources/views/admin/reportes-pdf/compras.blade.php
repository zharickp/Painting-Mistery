@extends('layouts.documento')

@php
    $pesos  = fn ($v) => '$' . number_format($v, 0, ',', '.');
    $maxMes = max(1, max(array_column($porMes, 'total')), max(array_column($ventasPorMes, 'total')));
    $diferencia = $ventasAnio - $totalAnio;
    $firma = fn ($v) => ($v < 0 ? '-' : '') . $pesos(abs($v));
@endphp

@section('titulo', 'Reporte de compras ' . $anio)
@section('volver', route('admin.reportes.compras', ['anio' => $anio]))
@section('tipo', 'Reporte de compras')
@section('numero', 'Año ' . $anio)
@section('fecha', 'Generado el ' . now()->format('d/m/Y h:i A'))

@section('contenido')
    <div class="kpis">
        <div class="kpi"><p class="etiqueta">Compras del año</p><p class="valor">{{ $pesos($totalAnio) }}</p><p class="detalle">{{ $cantidadAnio }} compra(s)</p></div>
        <div class="kpi"><p class="etiqueta">Promedio por compra</p><p class="valor">{{ $pesos($promedio) }}</p><p class="detalle">en {{ $anio }}</p></div>
        <div class="kpi"><p class="etiqueta">Ventas del año</p><p class="valor">{{ $pesos($ventasAnio) }}</p><p class="detalle">ventas pagadas</p></div>
        <div class="kpi"><p class="etiqueta">Diferencia</p><p class="valor" style="color: {{ $diferencia >= 0 ? '#047857' : '#be123c' }}">{{ $firma($diferencia) }}</p><p class="detalle">ventas − compras</p></div>
    </div>

    <div class="seccion">
        <p class="seccion-titulo">Compras y ventas por mes</p>
        <table>
            <thead>
                <tr><th>Mes</th><th class="centro">Compras</th><th class="der">Valor comprado</th><th class="der">Ventas</th><th class="der">Diferencia</th><th style="width:22%"></th></tr>
            </thead>
            <tbody>
                @foreach ($porMes as $i => $fila)
                    @php $ventas = $ventasPorMes[$i]['total']; @endphp
                    <tr>
                        <td>{{ $fila['mes_largo'] }}</td>
                        <td class="centro num">{{ $fila['cantidad'] }}</td>
                        <td class="der num">{{ $pesos($fila['total']) }}</td>
                        <td class="der num">{{ $pesos($ventas) }}</td>
                        <td class="der num" style="color: {{ $ventas - $fila['total'] >= 0 ? '#047857' : '#be123c' }}">{{ $firma($ventas - $fila['total']) }}</td>
                        <td>
                            <div class="barra" style="margin-bottom:3px"><span style="width: {{ round($ventas / $maxMes * 100) }}%"></span></div>
                            <div class="barra gris"><span style="width: {{ round($fila['total'] / $maxMes * 100) }}%"></span></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="centro num">{{ $cantidadAnio }}</td>
                    <td class="der num">{{ $pesos($totalAnio) }}</td>
                    <td class="der num">{{ $pesos($ventasAnio) }}</td>
                    <td class="der num">{{ $firma($diferencia) }}</td>
                    <td class="suave" style="font-weight:400;font-size:10px">Rojo: ventas · Gris: compras</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="datos" style="margin-top:24px">
        <div>
            <p class="seccion-titulo">Proveedores principales</p>
            @if ($topProveedores->isEmpty())
                <p class="suave">Sin compras en {{ $anio }}.</p>
            @else
                <table>
                    <thead><tr><th>Proveedor</th><th class="centro">Compras</th><th class="der">Valor</th></tr></thead>
                    <tbody>
                        @foreach ($topProveedores as $p)
                            <tr><td>{{ $p->nombre }}</td><td class="centro num">{{ $p->cantidad }}</td><td class="der num">{{ $pesos($p->total) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div>
            <p class="seccion-titulo">Productos más comprados</p>
            @if ($topProductos->isEmpty())
                <p class="suave">Sin compras en {{ $anio }}.</p>
            @else
                <table>
                    <thead><tr><th>Producto</th><th class="centro">Unidades</th><th class="der">Valor</th></tr></thead>
                    <tbody>
                        @foreach ($topProductos as $tp)
                            <tr><td>{{ $tp->nombre ?? 'Producto eliminado' }}</td><td class="centro num">{{ $tp->unidades }}</td><td class="der num">{{ $pesos($tp->total) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="nota">
        No se cuentan las compras anuladas. Las compras se ubican en el mes de la factura y las ventas en el mes en que se aprobó el pago.
        La diferencia no es la ganancia, porque la mercancía comprada puede venderse en otros meses.
        Documento generado por el sistema de Painting Mistery.
    </div>
@endsection
