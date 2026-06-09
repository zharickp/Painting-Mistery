@extends('layouts.app')
@section('title', 'Productos')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Productos</h1>
        <p class="text-sm text-gray-400 mt-1">Catálogo de productos.</p>
    </div>
    <a href="{{ route('admin.productos.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo producto
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

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse ($productos as $producto)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
        <div class="h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if ($producto->imagen)
                <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}"
                     class="h-full w-full object-cover">
            @else
                <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @endif
        </div>
        <div class="p-4">
            <p class="font-semibold text-gray-800 truncate">{{ $producto->nombre }}</p>
            <p class="text-xs text-gray-400 mb-1">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>
            <p class="text-red-600 font-bold mb-3">${{ number_format($producto->precio, 0, ',', '.') }}</p>

            <div class="flex items-center justify-between mb-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $producto->estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                </span>
                <span class="text-xs text-gray-400">
                    IVA {{ $producto->tipoIva->porcentaje ?? 0 }}%
                </span>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.productos.edit', $producto) }}"
                   class="flex-1 text-center px-2 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">
                    Editar
                </a>
                <form method="POST" action="{{ route('admin.productos.toggle', $producto) }}"
                      class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full px-2 py-1.5 text-xs rounded-md transition
                                {{ $producto->estado
                                    ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700'
                                    : 'bg-green-50 hover:bg-green-100 text-green-700' }}">
                        {{ $producto->estado ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-gray-400">
        No hay productos registrados.
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $productos->links() }}</div>

@endsection
