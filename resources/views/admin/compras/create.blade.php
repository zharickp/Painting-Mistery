@extends('layouts.app')
@section('title', 'Registrar compra')
@section('content')
@php
    $campo = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400';
    $lineas = old('items', [['producto_id' => '', 'cantidad' => 1, 'costo_unitario' => '']]);
@endphp

<div class="mb-6">
    <a href="{{ route('admin.compras.index') }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a compras</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Registrar compra</h1>
    <p class="text-sm text-gray-400 mt-1">Al guardarla, las cantidades se suman al inventario de cada producto.</p>
</div>

@if ($errors->any())
    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm max-w-4xl">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.compras.store') }}" class="space-y-5 max-w-4xl" id="formCompra">
    @csrf

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
            <select name="proveedor_id" required class="{{ $campo }} bg-white">
                <option value="">Elige un proveedor</option>
                @foreach ($proveedores as $p)
                    <option value="{{ $p->id }}" @selected((string) old('proveedor_id', $proveedorId) === (string) $p->id)>{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">N.º de factura <span class="text-gray-400">(opcional)</span></label>
            <input type="text" name="numero_factura" value="{{ old('numero_factura') }}" maxlength="60" class="{{ $campo }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
            <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required class="{{ $campo }}">
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Productos que llegaron</h2>
            <button type="button" onclick="agregarLinea()" class="text-sm font-semibold text-red-600 hover:text-red-700">+ Agregar producto</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3 w-28">Cantidad</th>
                        <th class="px-4 py-3 w-40">Costo unitario</th>
                        <th class="px-4 py-3 w-36 text-right">Subtotal</th>
                        <th class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody id="lineas" class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-6 text-sm">
            <span class="text-gray-500">Total: <strong id="totalCompra" class="text-gray-900 text-base">$0</strong></span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones <span class="text-gray-400">(opcional)</span></label>
        <textarea name="observaciones" rows="2" maxlength="1000" class="{{ $campo }}">{{ old('observaciones') }}</textarea>
    </div>

    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
        Registrar compra y sumar al inventario
    </button>
</form>

<template id="plantillaLinea">
    <tr>
        <td class="px-4 py-2">
            <select data-campo="producto_id" required class="{{ $campo }} bg-white" onchange="recalcular()">
                <option value="">Elige un producto</option>
                @foreach ($productos as $prod)
                    <option value="{{ $prod['id'] }}">{{ $prod['nombre'] }} — stock actual: {{ $prod['stock'] }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-2"><input type="number" data-campo="cantidad" min="1" step="1" required class="{{ $campo }}" oninput="recalcular()"></td>
        <td class="px-4 py-2"><input type="number" data-campo="costo_unitario" min="0" step="1" required placeholder="$" class="{{ $campo }}" oninput="recalcular()"></td>
        <td class="px-4 py-2 text-right font-semibold text-gray-700" data-subtotal>$0</td>
        <td class="px-4 py-2 text-right">
            <button type="button" onclick="quitarLinea(this)" title="Quitar" class="text-gray-400 hover:text-red-600 text-lg leading-none">&times;</button>
        </td>
    </tr>
</template>

<script>
    const lineasIniciales = @json(array_values($lineas));
    const fmt = n => '$' + Math.round(n).toLocaleString('es-CO');

    function agregarLinea(datos = {}) {
        const fila = document.getElementById('plantillaLinea').content.firstElementChild.cloneNode(true);
        fila.querySelector('[data-campo=producto_id]').value = datos.producto_id ?? '';
        fila.querySelector('[data-campo=cantidad]').value = datos.cantidad ?? 1;
        fila.querySelector('[data-campo=costo_unitario]').value = datos.costo_unitario ?? '';
        document.getElementById('lineas').appendChild(fila);
        renombrar();
        recalcular();
    }

    function quitarLinea(btn) {
        const filas = document.querySelectorAll('#lineas tr');
        if (filas.length === 1) return; // siempre queda al menos una línea
        btn.closest('tr').remove();
        renombrar();
        recalcular();
    }

    // Los nombres items[0][...], items[1][...] se rehacen al agregar o quitar líneas.
    function renombrar() {
        document.querySelectorAll('#lineas tr').forEach((fila, i) => {
            fila.querySelectorAll('[data-campo]').forEach(el => el.name = `items[${i}][${el.dataset.campo}]`);
        });
    }

    function recalcular() {
        let total = 0;
        const elegidos = [];
        document.querySelectorAll('#lineas tr').forEach(fila => {
            const cant = parseInt(fila.querySelector('[data-campo=cantidad]').value, 10) || 0;
            const costo = parseFloat(fila.querySelector('[data-campo=costo_unitario]').value) || 0;
            fila.querySelector('[data-subtotal]').textContent = fmt(cant * costo);
            total += cant * costo;
            const prod = fila.querySelector('[data-campo=producto_id]').value;
            if (prod) elegidos.push(prod);
        });
        // Un producto no se puede elegir en dos líneas.
        document.querySelectorAll('#lineas [data-campo=producto_id]').forEach(sel => {
            [...sel.options].forEach(op => {
                op.disabled = op.value !== '' && op.value !== sel.value && elegidos.includes(op.value);
            });
        });
        document.getElementById('totalCompra').textContent = fmt(total);
    }

    lineasIniciales.forEach(l => agregarLinea(l));
</script>
@endsection
