@extends('layouts.app')
@section('title', 'Tipos de IVA')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Tipos de IVA</h1>
        <p class="text-sm text-gray-400 mt-1">Gestiona los tipos de IVA aplicables a los productos.</p>
    </div>
    <a href="{{ route('admin.tipo-iva.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo tipo de IVA
    </a>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Descripción</th>
                    <th class="px-5 py-3 text-center">Porcentaje</th>
                    <th class="px-5 py-3 text-center">Productos</th>
                    <th class="px-5 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($tiposIva as $tipo)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $tipo->descripcion }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full font-medium">
                            {{ $tipo->porcentaje }}%
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                            {{ $tipo->productos_count }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <a href="{{ route('admin.tipo-iva.edit', $tipo) }}"
                           class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">
                            Editar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-gray-400">
                        No hay tipos de IVA registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">
        {{ $tiposIva->links() }}
    </div>
</div>

@endsection
