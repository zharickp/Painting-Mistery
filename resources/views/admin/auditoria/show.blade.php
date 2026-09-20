@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@php
    $etiquetas = [
        'precio_costo'            => 'Precio costo',
        'precio'                  => 'Precio',
        'precio_anterior'         => 'Precio anterior',
        'stock_actual'            => 'Stock actual',
        'stock_minimo'            => 'Stock mínimo',
        'unidad_presentacion'     => 'Unidad de presentación',
        'cantidad_por_presentacion' => 'Cantidad por presentación',
        'nombre'                  => 'Nombre',
        'descripcion'             => 'Descripción',
        'estado'                  => 'Estado',
        'categoria_producto_id'   => 'Categoría',
        'tipo_iva_id'             => 'Tipo de IVA',
        'primer_nombre'           => 'Primer nombre',
        'segundo_nombre'          => 'Segundo nombre',
        'primer_apellido'         => 'Primer apellido',
        'segundo_apellido'        => 'Segundo apellido',
        'correo'                  => 'Correo',
        'telefono'                => 'Teléfono',
        'imagen'                  => 'Imagen',
    ];
    $etiqueta = function ($campo) use ($etiquetas) {
        return $etiquetas[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
    };
    $formato = function ($valor) {
        if ($valor === null || $valor === '') return '—';
        if (is_bool($valor)) return $valor ? 'Sí' : 'No';
        if (is_array($valor)) return json_encode($valor, JSON_UNESCAPED_UNICODE);
        return (string) $valor;
    };
    $cambios = $registro->cambiosNormalizados();
    $ua = $registro->user_agent ?? '';
    $navegador = str_contains($ua, 'Firefox') ? 'Firefox'
        : (str_contains($ua, 'Edg/') ? 'Edge'
        : (str_contains($ua, 'Chrome') ? 'Chrome'
        : (str_contains($ua, 'Safari') ? 'Safari' : 'Desconocido')));
    $so = str_contains($ua, 'Windows') ? 'Windows'
        : (str_contains($ua, 'Mac OS') ? 'macOS'
        : (str_contains($ua, 'Android') ? 'Android'
        : (str_contains($ua, 'Linux') ? 'Linux' : 'Desconocido')));
@endphp

<style>
@media print {
    aside, header, .no-print { display: none !important; }
    main { padding: 0 !important; }
    body { background: white !important; }
}
</style>

@section('content')
<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Detalle de Auditoría</h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ $registro->accionEtiqueta() }} {{ $registro->modulo }}
            @if($registro->registro_etiqueta)
                "{{ $registro->registro_etiqueta }}"
            @endif
        </p>
    </div>
    <div class="flex items-center gap-2 no-print">
        <button onclick="window.print()"
                class="px-4 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar PDF
        </button>
        <a href="{{ route('admin.auditoria.index') }}" class="text-sm text-gray-500 hover:text-red-600">← Volver</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Tarjeta 1: Información general --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Información general</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-gray-500 font-semibold">Acción:</dt>
                <dd class="text-gray-800">{{ $registro->accionEtiqueta() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-semibold">Tipo afectado:</dt>
                <dd class="text-gray-800">
                    {{ $registro->modulo }}@if($registro->registro_id) (ID {{ $registro->registro_id }})@endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 font-semibold">Fecha y hora:</dt>
                <dd class="text-gray-800">{{ $registro->fechaMostrada()?->format('d/m/Y h:i:s A') }}</dd>
            </div>
            <hr class="border-gray-200 my-3">
            <div>
                <dt class="text-gray-500 font-semibold">Quién lo hizo:</dt>
                <dd class="text-gray-800">{{ $registro->usuario_nombre ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-semibold">Correo:</dt>
                <dd class="text-gray-800">{{ $registro->usuario_correo ?: '—' }}</dd>
            </div>
            <hr class="border-gray-200 my-3">
            <div>
                <dt class="text-gray-500 font-semibold">Desde dónde:</dt>
                <dd class="text-gray-800">{{ $registro->ip_address ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-semibold">Dispositivo:</dt>
                <dd class="text-gray-800">{{ $navegador }} · {{ $so }}</dd>
            </div>
        </dl>
    </div>

    {{-- Tarjeta 2: Qué cambió --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800">Qué cambió</h2>
        <p class="text-xs text-gray-500 mb-4">Comparación entre el valor anterior y el nuevo.</p>

        @if(empty($cambios))
            <p class="text-sm text-gray-400 py-6 text-center">Sin cambios registrados.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="py-2 text-left font-semibold">Campo</th>
                            <th class="py-2 text-left font-semibold">Antes</th>
                            <th class="py-2 text-left font-semibold">Después</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($cambios as $campo => $valores)
                            <tr>
                                <td class="py-3 font-medium text-gray-700">{{ $etiqueta($campo) }}</td>
                                <td class="py-3 text-gray-500">{{ $formato($valores['antes'] ?? null) }}</td>
                                <td class="py-3 text-gray-800">{{ $formato($valores['despues'] ?? null) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
