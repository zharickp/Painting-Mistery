@extends('layouts.app')
@section('title', 'Productos')
@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Productos</h1>
        <p class="text-sm text-gray-400 mt-1">Catálogo de productos disponibles.</p>
    </div>
    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo producto
    </button>
</div>
@php $productos = \App\Models\Producto::with('categoria')->orderByDesc('created_at')->paginate(12); @endphp
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($productos as $p)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
        <div class="h-40 bg-gray-100 flex items-center justify-center">
            @if($p->imagen)
                <img src="{{ $p->imagen }}" alt="{{ $p->nombre }}" class="h-full w-full object-cover">
            @else
                <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @endif
        </div>
        <div class="p-4">
            <p class="font-medium text-gray-800 truncate">{{ $p->nombre }}</p>
            <p class="text-xs text-gray-400 mb-2">{{ $p->categoria->nombre ?? 'Sin categoría' }}</p>
            <p class="text-red-600 font-bold">${{ number_format($p->precio, 0, ',', '.') }}</p>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-gray-400">No hay productos registrados.</div>
    @endforelse
</div>
<div class="mt-4">{{ $productos->links() }}</div>
@endsection
