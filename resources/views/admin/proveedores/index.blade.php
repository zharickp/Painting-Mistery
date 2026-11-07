@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')
@php $puedeEditar = auth()->user()->tieneRol('Administrador') || auth()->user()->tieneRol('Asesor'); @endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Proveedores</h1>
        <p class="text-sm text-gray-400 mt-1">Empresas o personas a las que se les compra la mercancía.</p>
    </div>
    @if ($puedeEditar)
    <a href="{{ route('admin.proveedores.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo proveedor
    </a>
    @endif
</div>

<form method="GET" action="{{ route('admin.proveedores.index') }}" class="mb-4 flex gap-2 max-w-md">
    <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre, NIT o contacto"
           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
    <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">Buscar</button>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Proveedor</th>
                    <th class="px-5 py-3">Contacto</th>
                    <th class="px-5 py-3 text-center">Compras</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($proveedores as $p)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $p->nombre }}</p>
                        <p class="text-xs text-gray-400">{{ $p->nit ? 'NIT ' . $p->nit : 'Sin NIT' }}</p>
                    </td>
                    <td class="px-5 py-3 text-gray-500">
                        @if ($p->persona_contacto)<p class="text-gray-700">{{ $p->persona_contacto }}</p>@endif
                        <p class="text-xs">{{ collect([$p->telefono, $p->correo])->filter()->join(' · ') ?: '—' }}</p>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <a href="{{ route('admin.compras.index', ['proveedor' => $p->id]) }}"
                           class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-full font-medium">
                            {{ $p->compras_count }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $p->estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $p->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if ($puedeEditar && $p->estado)
                            <a href="{{ route('admin.compras.create', ['proveedor' => $p->id]) }}"
                               class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs rounded-md transition">Registrar compra</a>
                            @endif
                            @if ($puedeEditar)
                            <a href="{{ route('admin.proveedores.edit', $p) }}"
                               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">Editar</a>
                            <form method="POST" action="{{ route('admin.proveedores.toggle', $p) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1 text-xs rounded-md transition {{ $p->estado ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700' : 'bg-green-50 hover:bg-green-100 text-green-700' }}">
                                    {{ $p->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            @else
                            <a href="{{ route('admin.compras.index', ['proveedor' => $p->id]) }}"
                               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">Ver compras</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        {{ $buscar ? 'Ningún proveedor coincide con la búsqueda.' : 'Todavía no hay proveedores registrados.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($proveedores->hasPages())
    <div class="px-5 py-4 border-t border-gray-50">{{ $proveedores->links() }}</div>
    @endif
</div>
@endsection
