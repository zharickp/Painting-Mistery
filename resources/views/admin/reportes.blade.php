@extends('layouts.app')
@section('title', 'Reportes')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Reportes de ventas</h1>
    <p class="text-sm text-gray-400 mt-1">Resumen de ingresos diarios, mensuales y anuales.</p>
</div>

@php
    $hoy     = \App\Models\Venta::whereDate('fecha', today())->sum('total');
    $semana  = \App\Models\Venta::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->sum('total');
    $mes     = \App\Models\Venta::whereYear('fecha', now()->year)->whereMonth('fecha', now()->month)->sum('total');
    $anio    = \App\Models\Venta::whereYear('fecha', now()->year)->sum('total');
    $total   = \App\Models\Venta::sum('total');
@endphp

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['Hoy',       $hoy,    'bg-blue-50',   'text-blue-600'],
        ['Esta semana',$semana,'bg-indigo-50',  'text-indigo-600'],
        ['Este mes',  $mes,    'bg-purple-50',  'text-purple-600'],
        ['Este año',  $anio,   'bg-orange-50',  'text-orange-600'],
        ['Histórico', $total,  'bg-green-50',   'text-green-600'],
    ] as [$label, $val, $bg, $color])
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-1">{{ $label }}</p>
        <p class="text-xl font-bold {{ $color }}">${{ number_format($val, 0, ',', '.') }}</p>
    </div>
    @endforeach
</div>

@php
    $porMes = \App\Models\Venta::selectRaw("TO_CHAR(fecha,'Mon') as mes, EXTRACT(MONTH FROM fecha)::int as num, SUM(total) as total, COUNT(*) as ordenes")
        ->whereRaw("EXTRACT(YEAR FROM fecha) = ?", [now()->year])
        ->groupByRaw("TO_CHAR(fecha,'Mon'), EXTRACT(MONTH FROM fecha)::int")
        ->orderByRaw("EXTRACT(MONTH FROM fecha)::int")
        ->get();
@endphp

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-50">
        <h2 class="font-semibold text-gray-700">Desglose mensual {{ now()->year }}</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Mes</th>
                    <th class="px-5 py-3 text-center">Órdenes</th>
                    <th class="px-5 py-3 text-right">Ingresos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($porMes as $fila)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $fila->mes }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $fila->ordenes }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">${{ number_format($fila->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">Sin datos este año.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
