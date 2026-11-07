@extends('layouts.app')
@section('title', 'Compras')
@section('content')
@php $puedeEditar = auth()->user()->tieneRol('Administrador') || auth()->user()->tieneRol('Asesor'); @endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Compras a proveedores</h1>
        <p class="text-sm text-gray-400 mt-1">Mercancía que entra al inventario.</p>
    </div>
    @if ($puedeEditar)
    <a href="{{ route('admin.compras.create', array_filter(['proveedor' => $proveedorId])) }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Registrar compra
    </a>
    @endif
</div>

@if ($proveedores->isNotEmpty())
<div class="mb-4">
    <select onchange="location.href=this.value" aria-label="Proveedor"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-400">
        <option value="{{ route('admin.compras.index') }}">Todos los proveedores</option>
        @foreach ($proveedores as $p)
            <option value="{{ route('admin.compras.index', ['proveedor' => $p->id]) }}" @selected((string) $proveedorId === (string) $p->id)>{{ $p->nombre }}</option>
        @endforeach
    </select>
</div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Compra</th>
                    <th class="px-5 py-3">Fecha</th>
                    <th class="px-5 py-3">Proveedor</th>
                    <th class="px-5 py-3 text-center">Productos</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($compras as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">#{{ $c->id }}</p>
                        <p class="text-xs text-gray-400">{{ $c->numero_factura ? 'Factura ' . $c->numero_factura : 'Sin factura' }}</p>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $c->fecha->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $c->proveedor?->nombre }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $c->detalles_count }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">${{ number_format($c->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $c->estaAnulada() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $c->estaAnulada() ? 'Anulada' : 'Registrada' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.compras.show', $c) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">Ver</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">Todavía no hay compras registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($compras->hasPages())
    <div class="px-5 py-4 border-t border-gray-50">{{ $compras->links() }}</div>
    @endif
</div>
@endsection
