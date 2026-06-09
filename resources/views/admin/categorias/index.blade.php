@extends('layouts.app')
@section('title', 'Categorías')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Categorías de producto</h1>
        <p class="text-sm text-gray-400 mt-1">Administra las categorías del catálogo.</p>
    </div>
    <a href="{{ route('admin.categorias.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nueva categoría
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
                    <th class="px-5 py-3">Nombre</th>
                    <th class="px-5 py-3">Descripción</th>
                    <th class="px-5 py-3 text-center">Productos</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($categorias as $categoria)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $categoria->nombre }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $categoria->descripcion ?? '—' }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                            {{ $categoria->productos_count }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $categoria->estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $categoria->estado ? 'Activa' : 'Inactiva' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.categorias.edit', $categoria) }}"
                               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('admin.categorias.toggle', $categoria) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded-md transition
                                            {{ $categoria->estado
                                                ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700'
                                                : 'bg-green-50 hover:bg-green-100 text-green-700' }}">
                                    {{ $categoria->estado ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        No hay categorías registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">
        {{ $categorias->links() }}
    </div>
</div>

@endsection
