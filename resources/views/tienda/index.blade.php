@extends('layouts.guest')

@section('title', $buscar ? "Resultados para \"{$buscar}\" - Painting Mistery" : 'Tienda - Painting Mistery')

@section('content')
<div class="bg-gray-50 min-h-screen">

    @include('partials.nav')

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-sm text-gray-500 flex items-center gap-2 flex-wrap">
            <a href="{{ route('inicio') }}" class="hover:text-red-600 transition">Inicio</a>
            <span>/</span>
            @if ($buscar)
                <a href="{{ route('tienda.index') }}" class="hover:text-red-600 transition">Tienda</a>
                <span>/</span>
                <span class="text-gray-800 font-medium">Resultados para "{{ $buscar }}"</span>
            @else
                <span class="text-gray-800 font-medium">Tienda</span>
            @endif
        </div>
    </div>

    {{-- Fila de categorías --}}
    @if ($categorias->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-4 overflow-x-auto pb-2">
            <a href="{{ route('tienda.index') }}"
               class="flex flex-col items-center gap-2 flex-shrink-0 group {{ !$categoria ? 'opacity-100' : 'opacity-70 hover:opacity-100' }} transition">
                <div class="h-16 w-16 rounded-full flex items-center justify-center border-2 {{ !$categoria ? 'border-red-600' : 'border-gray-200' }} bg-white overflow-hidden">
                    <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-700 whitespace-nowrap">Todas</span>
            </a>
            @foreach ($categorias as $cat)
                <a href="{{ route('tienda.index', ['categoria' => $cat->id]) }}"
                   class="flex flex-col items-center gap-2 flex-shrink-0 group {{ (string) $categoria === (string) $cat->id ? 'opacity-100' : 'opacity-70 hover:opacity-100' }} transition">
                    <div class="h-16 w-16 rounded-full flex items-center justify-center border-2 {{ (string) $categoria === (string) $cat->id ? 'border-red-600' : 'border-gray-200' }} bg-white overflow-hidden">
                        @if ($cat->imagenRepresentativa)
                            <img src="{{ $cat->imagenRepresentativa }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        @endif
                    </div>
                    <span class="text-xs font-medium text-gray-700 whitespace-nowrap">{{ $cat->nombre }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20">
                    <h3 class="font-bold text-gray-800 text-sm mb-3">Buscar productos</h3>
                    <form action="{{ route('tienda.index') }}" method="GET" class="relative mb-6">
                        @if ($categoria)<input type="hidden" name="categoria" value="{{ $categoria }}">@endif
                        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="¿Qué estás buscando?"
                               class="w-full pl-3 pr-9 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>

                    <h3 class="font-bold text-gray-800 text-sm mb-3">Categorías</h3>
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('tienda.index', array_filter(['buscar' => $buscar])) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg transition {{ !$categoria ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                Todas
                            </a>
                        </li>
                        @foreach ($categorias as $cat)
                            <li>
                                <a href="{{ route('tienda.index', array_filter(['categoria' => $cat->id, 'buscar' => $buscar])) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg transition {{ (string) $categoria === (string) $cat->id ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    <span>{{ $cat->nombre }}</span>
                                    <span class="text-xs text-gray-400">{{ $cat->productos_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            {{-- Resultados --}}
            <div class="lg:col-span-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <p class="text-sm text-gray-500">
                        @if ($productos->total() > 0)
                            Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }} de {{ $productos->total() }} resultados
                        @else
                            Sin resultados
                        @endif
                    </p>
                    <form action="{{ route('tienda.index') }}" method="GET" class="flex items-center gap-2">
                        @if ($categoria)<input type="hidden" name="categoria" value="{{ $categoria }}">@endif
                        @if ($buscar)<input type="hidden" name="buscar" value="{{ $buscar }}">@endif
                        <select name="por_pagina" onchange="this.form.submit()"
                                class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-red-400 bg-white">
                            @foreach ([9, 12, 18, 24] as $n)
                                <option value="{{ $n }}" @selected($porPagina == $n)>{{ $n }} / página</option>
                            @endforeach
                        </select>
                        <select name="orden" onchange="this.form.submit()"
                                class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-red-400 bg-white">
                            <option value="relevancia" @selected($orden == 'relevancia' || $orden == 'recientes')>Más recientes</option>
                            <option value="precio_asc" @selected($orden == 'precio_asc')>Precio: menor a mayor</option>
                            <option value="precio_desc" @selected($orden == 'precio_desc')>Precio: mayor a menor</option>
                            <option value="nombre" @selected($orden == 'nombre')>Nombre A-Z</option>
                        </select>
                    </form>
                </div>

                @if ($productos->isEmpty())
                    <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                        <svg class="h-14 w-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-base font-semibold text-gray-600 mb-1">No encontramos productos</h3>
                        <p class="text-gray-400 text-sm">Prueba con otra búsqueda o revisa otra categoría.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach ($productos as $producto)
                            <div class="prod-card bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition group"
                                 data-id="{{ $producto->id }}"
                                 data-nombre="{{ $producto->nombre }}"
                                 data-precio="{{ $producto->precio }}"
                                 data-imagen="{{ $producto->imagen ?? '' }}"
                                 data-cat="{{ $producto->categoria_producto_id ?? '' }}">

                                <a href="{{ route('producto.show', $producto) }}" class="relative h-48 bg-gray-100 overflow-hidden block">
                                    @if ($producto->imagen)
                                        <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}"
                                             class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Badges --}}
                                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                                        @if ($producto->tieneDescuento())
                                            <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $producto->porcentajeDescuento() }}%</span>
                                        @endif
                                        @if ($producto->esNuevo())
                                            <span class="bg-blue-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">NUEVO</span>
                                        @endif
                                        @if ($producto->estaAgotado())
                                            <span class="bg-gray-800 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">AGOTADO</span>
                                        @endif
                                    </div>

                                    <button onclick="event.preventDefault(); event.stopPropagation(); toggleWish(this.closest('.prod-card'))"
                                        class="wish-btn absolute top-2 right-2 bg-white rounded-full p-1.5 shadow-md hover:scale-110 transition"
                                        title="Agregar a lista de deseos">
                                        <svg class="h-4 w-4 text-gray-400 wish-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </button>
                                </a>

                                <div class="p-4">
                                    <a href="{{ route('producto.show', $producto) }}" class="font-semibold text-gray-800 text-sm mb-1 hover:text-red-600 transition block line-clamp-2">{{ $producto->nombre }}</a>
                                    @if ($producto->categoria)
                                        <span class="inline-block bg-red-50 text-red-600 text-xs px-2 py-0.5 rounded-full my-1.5">{{ $producto->categoria->nombre }}</span>
                                    @endif
                                    <div class="flex items-center justify-between mt-2">
                                        <div>
                                            @if ($producto->tieneDescuento())
                                                <p class="text-gray-400 text-xs line-through">${{ number_format($producto->precio_anterior, 0, ',', '.') }}</p>
                                            @endif
                                            <p class="text-red-600 font-bold">${{ number_format($producto->precio, 0, ',', '.') }}</p>
                                        </div>
                                        <button onclick="addToCartDesdeCard(this.closest('.prod-card'))"
                                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $productos->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')
</div>
@endsection
