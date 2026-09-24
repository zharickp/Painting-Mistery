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
                    <ul class="space-y-1 text-sm mb-6">
                        <li>
                            <a href="{{ route('tienda.index', array_filter(['buscar' => $buscar, 'precio_min' => $precioMin, 'precio_max' => $precioMax, 'en_stock' => $soloStock ? 1 : null])) }}"
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg transition {{ !$categoria ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                Todas
                            </a>
                        </li>
                        @foreach ($categorias as $cat)
                            <li>
                                <a href="{{ route('tienda.index', array_filter(['categoria' => $cat->id, 'buscar' => $buscar, 'precio_min' => $precioMin, 'precio_max' => $precioMax, 'en_stock' => $soloStock ? 1 : null])) }}"
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg transition {{ (string) $categoria === (string) $cat->id ? 'bg-red-50 text-red-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    <span>{{ $cat->nombre }}</span>
                                    <span class="text-xs text-gray-400">{{ $cat->productos_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Filtro por precio --}}
                    <h3 class="font-bold text-gray-800 text-sm mb-3">Precio</h3>
                    <form action="{{ route('tienda.index') }}" method="GET" class="mb-6">
                        @if ($categoria)<input type="hidden" name="categoria" value="{{ $categoria }}">@endif
                        @if ($buscar)<input type="hidden" name="buscar" value="{{ $buscar }}">@endif
                        <div class="flex items-center gap-2 mb-3">
                            <input type="number" name="precio_min" value="{{ $precioMin }}" placeholder="Mín" min="0"
                                   class="w-full px-2.5 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400">
                            <span class="text-gray-300">—</span>
                            <input type="number" name="precio_max" value="{{ $precioMax }}" placeholder="Máx" min="0"
                                   class="w-full px-2.5 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-600 mb-3 cursor-pointer">
                            <input type="checkbox" name="en_stock" value="1" @checked($soloStock)
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Solo con stock disponible
                        </label>
                        <button type="submit"
                                class="w-full bg-gray-900 hover:bg-black text-white text-sm font-semibold py-2 rounded-lg transition">
                            Aplicar filtros
                        </button>
                    </form>

                    @if ($buscar || $categoria || $precioMin || $precioMax || $soloStock)
                        <a href="{{ route('tienda.index') }}"
                           class="flex items-center justify-center gap-1.5 text-xs text-gray-500 hover:text-red-600 transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar todos los filtros
                        </a>
                    @endif
                </div>
            </aside>

            {{-- Resultados --}}
            <div class="lg:col-span-3">
                {{-- Chips de filtros activos --}}
                @if ($buscar || $categoria || $precioMin || $precioMax || $soloStock)
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @if ($buscar)
                            <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 text-xs font-medium px-3 py-1 rounded-full">
                                "{{ $buscar }}"
                                <a href="{{ route('tienda.index', array_filter(['categoria' => $categoria, 'precio_min' => $precioMin, 'precio_max' => $precioMax, 'en_stock' => $soloStock ? 1 : null])) }}" class="hover:text-red-900">✕</a>
                            </span>
                        @endif
                        @if ($categoria)
                            <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 text-xs font-medium px-3 py-1 rounded-full">
                                {{ $categorias->firstWhere('id', $categoria)?->nombre ?? 'Categoría' }}
                                <a href="{{ route('tienda.index', array_filter(['buscar' => $buscar, 'precio_min' => $precioMin, 'precio_max' => $precioMax, 'en_stock' => $soloStock ? 1 : null])) }}" class="hover:text-red-900">✕</a>
                            </span>
                        @endif
                        @if ($precioMin || $precioMax)
                            <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 text-xs font-medium px-3 py-1 rounded-full">
                                ${{ number_format((float) ($precioMin ?: 0), 0, ',', '.') }} – {{ $precioMax ? '$' . number_format((float) $precioMax, 0, ',', '.') : 'sin límite' }}
                                <a href="{{ route('tienda.index', array_filter(['buscar' => $buscar, 'categoria' => $categoria, 'en_stock' => $soloStock ? 1 : null])) }}" class="hover:text-red-900">✕</a>
                            </span>
                        @endif
                        @if ($soloStock)
                            <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 text-xs font-medium px-3 py-1 rounded-full">
                                Con stock
                                <a href="{{ route('tienda.index', array_filter(['buscar' => $buscar, 'categoria' => $categoria, 'precio_min' => $precioMin, 'precio_max' => $precioMax])) }}" class="hover:text-red-900">✕</a>
                            </span>
                        @endif
                    </div>
                @endif

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
                        @if ($precioMin)<input type="hidden" name="precio_min" value="{{ $precioMin }}">@endif
                        @if ($precioMax)<input type="hidden" name="precio_max" value="{{ $precioMax }}">@endif
                        @if ($soloStock)<input type="hidden" name="en_stock" value="1">@endif
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
                            @include('partials.producto-card', ['producto' => $producto])
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
