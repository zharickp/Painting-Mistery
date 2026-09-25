@extends('layouts.guest')

@section('title', $buscar ? "Resultados para \"{$buscar}\" - Painting Mistery" : 'Tienda - Painting Mistery')

@section('content')
@php
    $base = array_filter([
        'buscar'     => $buscar,
        'categoria'  => $categoria,
        'precio_min' => $precioMin,
        'precio_max' => $precioMax,
        'en_stock'   => $soloStock ? 1 : null,
        'oferta'     => $soloOferta ? 1 : null,
        'orden'      => in_array($orden, ['precio_asc', 'precio_desc', 'nombre'], true) ? $orden : null,
        'por_pagina' => $porPagina !== 12 ? $porPagina : null,
    ], fn ($v) => $v !== null && $v !== '');
    $url = fn (array $o = []) => route('tienda.index', array_filter(array_merge($base, $o), fn ($v) => $v !== null && $v !== ''));

    $rangos = [
        ['Hasta $50.000',         null,   50000],
        ['$50.000 – $150.000',    50000,  150000],
        ['$150.000 – $300.000',   150000, 300000],
        ['Más de $300.000',       300000, null],
    ];
    $rangoActivo = fn ($min, $max) => (float) ($precioMin ?: 0) === (float) ($min ?: 0) && (float) ($precioMax ?: 0) === (float) ($max ?: 0) && ($precioMin || $precioMax);
    $hayFiltros = $buscar || $categoria || $precioMin || $precioMax || $soloStock || $soloOferta;
    $umbral = (int) config('envios.umbral_envio_gratis', 400000);
@endphp
<div class="bg-gray-50 min-h-screen">

    @include('partials.nav')

    {{-- Franja de beneficios --}}
    <div class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex flex-wrap items-center justify-center gap-x-8 gap-y-1 text-xs">
            <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m10 0H3m10 0h2m4 0h1a1 1 0 001-1v-3.65a1 1 0 00-.22-.624l-3.48-4.35A1 1 0 0017.52 6H14v10m-4 0a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Envío gratis desde ${{ number_format($umbral, 0, ',', '.') }}</span>
            <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>Garantía por defectos de fábrica</span>
            <span class="flex items-center gap-1.5"><svg class="h-4 w-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>Asesoría por WhatsApp</span>
        </div>
    </div>

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

    {{-- Categorías --}}
    @if ($categorias->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="flex gap-3 overflow-x-auto pb-2">
            <a href="{{ $url(['categoria' => null]) }}"
               class="flex items-center gap-2 flex-shrink-0 px-4 py-2 rounded-full border text-sm font-medium transition {{ !$categoria ? 'bg-red-600 border-red-600 text-white shadow-md shadow-red-600/20' : 'bg-white border-gray-200 text-gray-600 hover:border-red-300 hover:text-red-600' }}">
                Todas
            </a>
            @foreach ($categorias as $cat)
                @php $activa = (string) $categoria === (string) $cat->id; @endphp
                <a href="{{ $url(['categoria' => $cat->id]) }}"
                   class="flex items-center gap-2 flex-shrink-0 pl-1.5 pr-4 py-1.5 rounded-full border text-sm font-medium transition {{ $activa ? 'bg-red-600 border-red-600 text-white shadow-md shadow-red-600/20' : 'bg-white border-gray-200 text-gray-600 hover:border-red-300 hover:text-red-600' }}">
                    <span class="h-8 w-8 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center shrink-0">
                        @if ($cat->imagenRepresentativa)
                            <img src="{{ $cat->imagenRepresentativa }}" alt="" class="h-full w-full object-cover">
                        @else
                            <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                        @endif
                    </span>
                    {{ $cat->nombre }}
                    <span class="text-[11px] {{ $activa ? 'text-red-100' : 'text-gray-400' }}">{{ $cat->productos_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-16">

        {{-- Botón filtros (móvil) --}}
        <button type="button" onclick="document.getElementById('filtrosPanel').classList.toggle('hidden')"
                class="lg:hidden w-full mb-4 flex items-center justify-center gap-2 bg-white border border-gray-200 rounded-xl py-2.5 text-sm font-semibold text-gray-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12m-8 8h4"/></svg>
            Filtros{{ $hayFiltros ? ' (activos)' : '' }}
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- Filtros --}}
            <aside id="filtrosPanel" class="hidden lg:block lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 lg:sticky lg:top-20 space-y-6">

                    <div class="flex items-center justify-between">
                        <h2 class="font-extrabold text-gray-900">Filtrar</h2>
                        @if ($hayFiltros)
                            <a href="{{ route('tienda.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Limpiar todo</a>
                        @endif
                    </div>

                    {{-- Búsqueda --}}
                    <form action="{{ route('tienda.index') }}" method="GET" class="relative">
                        @foreach (array_diff_key($base, ['buscar' => 1]) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar productos..."
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:border-red-400 focus:bg-white transition">
                        <svg class="h-4 w-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </form>

                    {{-- Precio: rangos rápidos --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Precio</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($rangos as [$label, $min, $max])
                                @php $on = $rangoActivo($min, $max); @endphp
                                <a href="{{ $url(['precio_min' => $on ? null : $min, 'precio_max' => $on ? null : $max]) }}"
                                   class="px-3 py-1.5 rounded-full border text-xs font-medium transition {{ $on ? 'bg-gray-900 border-gray-900 text-white' : 'border-gray-200 text-gray-600 hover:border-gray-900' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                        <form action="{{ route('tienda.index') }}" method="GET" class="mt-3 flex items-center gap-2">
                            @foreach (array_diff_key($base, ['precio_min' => 1, 'precio_max' => 1]) as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                            <input type="number" name="precio_min" value="{{ $precioMin }}" placeholder="Mín" min="0"
                                   class="w-full px-2.5 py-2 rounded-lg border border-gray-200 text-xs focus:outline-none focus:border-red-400">
                            <span class="text-gray-300">–</span>
                            <input type="number" name="precio_max" value="{{ $precioMax }}" placeholder="Máx" min="0"
                                   class="w-full px-2.5 py-2 rounded-lg border border-gray-200 text-xs focus:outline-none focus:border-red-400">
                            <button class="h-8 w-8 shrink-0 rounded-lg bg-gray-900 hover:bg-red-600 text-white flex items-center justify-center transition" title="Aplicar rango">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Interruptores --}}
                    <div class="space-y-3">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Disponibilidad</h3>
                        @foreach ([['En stock', 'en_stock', $soloStock], ['En oferta', 'oferta', $soloOferta]] as [$lbl, $key, $on])
                            <a href="{{ $url([$key => $on ? null : 1]) }}" class="flex items-center justify-between group">
                                <span class="text-sm text-gray-700 group-hover:text-red-600 transition">{{ $lbl }}</span>
                                <span class="relative h-6 w-11 rounded-full transition {{ $on ? 'bg-red-600' : 'bg-gray-200' }}">
                                    <span class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition-all {{ $on ? 'left-[22px]' : 'left-0.5' }}"></span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- Resultados --}}
            <div class="lg:col-span-3">

                {{-- Barra superior --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-gray-500">
                        @if ($productos->total() > 0)
                            <span class="font-bold text-gray-900">{{ $productos->total() }}</span> producto(s)
                        @else
                            Sin resultados
                        @endif
                    </p>
                    <div class="flex items-center gap-2">
                        <select onchange="location.href=this.value" aria-label="Productos por página"
                                class="text-sm border border-gray-200 rounded-full px-3 py-1.5 focus:outline-none focus:border-red-400 bg-white">
                            @foreach ([9, 12, 18, 24] as $n)
                                <option value="{{ $url(['por_pagina' => $n === 12 ? null : $n]) }}" @selected($porPagina == $n)>{{ $n }} por página</option>
                            @endforeach
                        </select>
                        <select onchange="location.href=this.value" aria-label="Ordenar"
                                class="text-sm border border-gray-200 rounded-full px-3 py-1.5 focus:outline-none focus:border-red-400 bg-white">
                            <option value="{{ $url(['orden' => null]) }}" @selected(! in_array($orden, ['precio_asc', 'precio_desc', 'nombre'], true))>Más recientes</option>
                            <option value="{{ $url(['orden' => 'precio_asc']) }}" @selected($orden == 'precio_asc')>Precio: menor a mayor</option>
                            <option value="{{ $url(['orden' => 'precio_desc']) }}" @selected($orden == 'precio_desc')>Precio: mayor a menor</option>
                            <option value="{{ $url(['orden' => 'nombre']) }}" @selected($orden == 'nombre')>Nombre A-Z</option>
                        </select>
                    </div>
                </div>

                {{-- Filtros activos --}}
                @if ($hayFiltros)
                    <div class="flex flex-wrap items-center gap-2 mb-5">
                        @php
                            $chips = [];
                            if ($buscar) $chips[] = ['"' . $buscar . '"', ['buscar' => null]];
                            if ($categoria) $chips[] = [$categorias->firstWhere('id', $categoria)?->nombre ?? 'Categoría', ['categoria' => null]];
                            if ($precioMin || $precioMax) $chips[] = ['$' . number_format((float) ($precioMin ?: 0), 0, ',', '.') . ' – ' . ($precioMax ? '$' . number_format((float) $precioMax, 0, ',', '.') : 'sin límite'), ['precio_min' => null, 'precio_max' => null]];
                            if ($soloStock) $chips[] = ['En stock', ['en_stock' => null]];
                            if ($soloOferta) $chips[] = ['En oferta', ['oferta' => null]];
                        @endphp
                        @foreach ($chips as [$txt, $quitar])
                            <a href="{{ $url($quitar) }}" class="inline-flex items-center gap-1.5 bg-gray-900 text-white text-xs font-medium px-3 py-1.5 rounded-full hover:bg-red-600 transition">
                                {{ $txt }} <span aria-hidden="true">✕</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($productos->isEmpty())
                    <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                        <svg class="h-14 w-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-base font-semibold text-gray-600 mb-1">No encontramos productos</h3>
                        <p class="text-gray-400 text-sm mb-4">Prueba con otra búsqueda o quita algún filtro.</p>
                        @if ($hayFiltros)<a href="{{ route('tienda.index') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition">Ver todos los productos</a>@endif
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
