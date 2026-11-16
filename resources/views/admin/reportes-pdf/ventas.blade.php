@extends('layouts.documento')

@php
    $pesos  = fn ($v) => '$' . number_format($v, 0, ',', '.');
    $maxMes = max(1, max(array_column($porMes, 'total')));
@endphp

@section('titulo', 'Reporte de ventas ' . $anio)
@section('volver', route('admin.reportes', ['anio' => $anio]))
@section('tipo', 'Reporte de ventas')
@section('numero', 'Año ' . $anio)
@section('fecha', 'Generado el ' . now()->format('d/m/Y h:i A'))

@section('contenido')
    <div class="kpis">
        <div class="kpi"><p class="etiqueta">Ventas del año</p><p class="valor">{{ $pesos($totalAnio) }}</p><p class="detalle">{{ $cantidadAnio }} venta(s) pagada(s)</p></div>
        <div class="kpi"><p class="etiqueta">Promedio por venta</p><p class="valor">{{ $pesos($promedio) }}</p><p class="detalle">en {{ $anio }}</p></div>
        <div class="kpi"><p class="etiqueta">Este mes</p><p class="valor">{{ $pesos($mes['total']) }}</p><p class="detalle">{{ $mes['cantidad'] }} venta(s)</p></div>
        <div class="kpi"><p class="etiqueta">Órdenes registradas</p><p class="valor">{{ array_sum($estados) }}</p><p class="detalle">{{ $estados['pagada'] }} pagadas · {{ $estados['pendiente'] }} pendientes · {{ $estados['cancelada'] }} canceladas</p></div>
    </div>

    <div class="seccion">
        <p class="seccion-titulo">Ventas por mes</p>
        <table>
            <thead>
                <tr><th>Mes</th><th class="centro">Ventas</th><th style="width:38%"></th><th class="der">Ingresos</th></tr>
            </thead>
            <tbody>
                @foreach ($porMes as $fila)
                    <tr>
                        <td>{{ $fila['mes_largo'] }}</td>
                        <td class="centro num">{{ $fila['cantidad'] }}</td>
                        <td><div class="barra"><span style="width: {{ round($fila['total'] / $maxMes * 100) }}%"></span></div></td>
                        <td class="der num">{{ $pesos($fila['total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><td>Total</td><td class="centro num">{{ $cantidadAnio }}</td><td></td><td class="der num">{{ $pesos($totalAnio) }}</td></tr>
            </tfoot>
        </table>
    </div>

    <div class="seccion">
        <p class="seccion-titulo">Productos más vendidos</p>
        @if ($topProductos->isEmpty())
            <p class="suave">No hay ventas de productos en {{ $anio }}.</p>
        @else
            <table>
                <thead><tr><th>#</th><th>Producto</th><th class="centro">Unidades</th><th class="der">Ingresos</th></tr></thead>
                <tbody>
                    @foreach ($topProductos as $i => $tp)
                        <tr>
                            <td class="num">{{ $i + 1 }}</td>
                            <td>{{ $tp->nombre ?? 'Producto eliminado' }}</td>
                            <td class="centro num">{{ $tp->unidades }}</td>
                            <td class="der num">{{ $pesos($tp->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="nota">
        Solo se cuentan las ventas pagadas, en el mes en que se aprobó el pago. Incluye productos y cursos.
        Documento generado por el sistema de Painting Mistery.
    </div>
@endsection
