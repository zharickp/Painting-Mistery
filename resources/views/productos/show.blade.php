@extends('layouts.guest')

@section('title', $producto->nombre)

@section('content')
@php
    $colores = $producto->coloresDisponibles();
    $tieneColores = count($colores) > 0;
    $galeriaCompleta = $producto->imagenesConColor();

    if ($tieneColores) {
        // Vista por defecto: portada general (no pertenece a ningún color) + fotos compartidas.
        $galeriaPorDefecto = collect();
        if ($producto->imagen) {
            $galeriaPorDefecto->push($producto->imagen);
        }
        foreach ($producto->galeriaSinColor() as $ruta) {
            $galeriaPorDefecto->push($ruta);
        }
        $galeria = $galeriaPorDefecto->unique()->values()->all();
    } else {
        $galeria = $producto->galeria();
    }
    $galeriaTotalCount = count($galeria);

@endphp
<div class="bg-gray-50 min-h-screen">

    @include('partials.nav')

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-sm text-gray-500 flex items-center gap-2 flex-wrap">
            <a href="{{ route('inicio') }}" class="hover:text-red-600 transition">Inicio</a>
            <span>/</span>
            <a href="{{ route('tienda.index') }}" class="hover:text-red-600 transition">Tienda</a>
            @if ($producto->categoria)
                <span>/</span>
                <a href="{{ route('tienda.index', ['categoria' => $producto->categoria_producto_id]) }}" class="hover:text-red-600 transition">{{ $producto->categoria->nombre }}</a>
            @endif
            <span>/</span>
            <span class="text-gray-800 font-medium truncate">{{ $producto->nombre }}</span>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

                {{-- Galería --}}
                <div class="p-4 sm:p-6 md:border-r border-gray-100">
                    <div class="flex gap-3">
                        <div class="hidden md:flex flex-col gap-2 w-16 flex-shrink-0 max-h-[420px] overflow-y-auto" id="galThumbsCol">
                            @if ($tieneColores)
                                <div class="gal-thumb gal-thumb-general h-16 w-16 activa" onclick="mostrarPortada()" title="Ver galería general">
                                    @if ($producto->imagen)
                                        <img src="{{ $producto->imagen }}" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-300">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @foreach ($galeria as $i => $src)
                                @continue($tieneColores && $i === 0 && $producto->imagen)
                                <div class="gal-thumb h-16 w-16 {{ $i === 0 ? 'activa' : '' }}" data-idx="{{ $i }}" onclick="galIrAFoto({{ $i }})">
                                    <img src="{{ $src }}" class="h-full w-full object-cover" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                        <div class="relative flex-1 min-w-0">
                            <div id="galStage" class="relative bg-gray-50 rounded-2xl overflow-hidden select-none" style="aspect-ratio:1/1; touch-action: pan-y;">
                                @if (empty($galeria))
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @else
                                    <div id="galImgTrack" class="absolute inset-0">
                                        @foreach ($galeria as $i => $src)
                                            <div class="gal-slide absolute inset-0 flex items-center justify-center transition-opacity duration-500 ease-out {{ $i === 0 ? 'activa' : '' }}">
                                                <img src="{{ $src }}" class="gal-img w-full h-full object-contain" @if($i > 0) loading="lazy" @endif draggable="false">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button onclick="galMover(-1)" class="gal-nav-arrow absolute left-2 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-white/95 hover:bg-white shadow-lg flex items-center justify-center text-gray-700 transition hover:scale-110 z-20 {{ $galeriaTotalCount <= 1 ? 'hidden' : '' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button onclick="galMover(1)" class="gal-nav-arrow absolute right-2 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-white/95 hover:bg-white shadow-lg flex items-center justify-center text-gray-700 transition hover:scale-110 z-20 {{ $galeriaTotalCount <= 1 ? 'hidden' : '' }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                    <div id="galContador" class="absolute top-3 left-3 bg-black/50 text-white text-xs font-semibold px-2.5 py-1 rounded-full z-20 {{ $galeriaTotalCount <= 1 ? 'hidden' : '' }}">1 / {{ count($galeria) }}</div>
                                    <div id="zoomHint" class="absolute bottom-3 right-3 bg-white/95 rounded-full p-2 shadow-md text-gray-500 pointer-events-none transition-opacity duration-200 z-20">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zm-3 0v3m0 0v3m0-3h3m-3 0H9"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex md:hidden gap-2 mt-3 overflow-x-auto pb-1" id="galThumbsRow">
                                @if ($tieneColores)
                                    <div class="gal-thumb gal-thumb-general h-14 w-14 activa flex-shrink-0" onclick="mostrarPortada()" title="Ver galería general">
                                        @if ($producto->imagen)
                                            <img src="{{ $producto->imagen }}" class="h-full w-full object-cover" loading="lazy">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-300">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @foreach ($galeria as $i => $src)
                                    @continue($tieneColores && $i === 0 && $producto->imagen)
                                    <div class="gal-thumb h-14 w-14 {{ $i === 0 ? 'activa' : '' }} flex-shrink-0" data-idx="{{ $i }}" onclick="galIrAFoto({{ $i }})">
                                        <img src="{{ $src }}" class="h-full w-full object-cover" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-6 md:p-8 flex flex-col animate-fade-in-up">
                    <div>
                        @if ($producto->categoria)
                            <span class="text-xs font-semibold text-red-600 uppercase tracking-widest">{{ $producto->categoria->nombre }}</span>
                        @endif
                        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mt-1 mb-2">{{ $producto->nombre }}</h1>
                        @if ($producto->tieneDescuento())
                            <p class="text-gray-400 text-sm line-through">${{ number_format($producto->precio_anterior, 0, ',', '.') }}</p>
                        @endif
                        <div class="flex items-center gap-3 mb-1">
                            <p class="text-3xl font-extrabold text-red-600">${{ number_format($producto->precio, 0, ',', '.') }}</p>
                            @if ($producto->tieneDescuento())
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">-{{ $producto->porcentajeDescuento() }}%</span>
                            @endif
                        </div>
                        @if ($producto->estaAgotado())
                            <span id="badgeDisponibilidad" class="inline-block bg-gray-800 text-white text-xs font-semibold px-2.5 py-1 rounded-full transition-colors duration-200">Sin existencias</span>
                        @else
                            <span id="badgeDisponibilidad" class="inline-block bg-green-50 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full transition-colors duration-200">Disponible</span>
                        @endif
                    </div>

                    @if ($tieneColores)
                        <div class="mt-8">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.15em] mb-4">Color</p>
                            <div class="flex flex-wrap gap-x-7 gap-y-5" id="coloresGrid">
                                @foreach ($colores as $c)
                                    <div class="flex flex-col items-center" style="width: 72px;">
                                        <button type="button" onclick="seleccionarColor({{ $c['id'] }}, this)"
                                            data-color-id="{{ $c['id'] }}"
                                            class="color-swatch-btn"
                                            style="background-color: {{ $c['hex'] }};"
                                            aria-label="{{ $c['nombre'] }}">
                                        </button>
                                        <span class="mt-3 block text-center text-[12px] font-medium text-gray-700 leading-tight truncate max-w-full">{{ $c['nombre'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(! auth()->check() || auth()->user()->puedeComprar())
                    <div class="mt-8 flex items-center gap-4">
                        <span class="text-sm font-semibold text-gray-700">Cantidad</span>
                        <div class="flex items-center h-10 border border-gray-200 rounded-xl overflow-hidden">
                            <button onclick="cambiarCantidad(-1)" class="w-9 h-full flex items-center justify-center text-gray-600 hover:bg-gray-50 transition font-bold text-lg leading-none">−</button>
                            <input id="prodCantidad" type="number" value="1" min="1" max="{{ max(1, $producto->stockActual()) }}" onchange="cambiarCantidad(0)" class="w-12 h-full text-center border-0 text-sm font-semibold focus:outline-none">
                            <button onclick="cambiarCantidad(1)" class="w-9 h-full flex items-center justify-center text-gray-600 hover:bg-gray-50 transition font-bold text-lg leading-none">+</button>
                        </div>
                        <span id="prodDisponibles" class="text-sm text-gray-500"></span>
                    </div>

                    <button onclick="agregarAlCarritoDesdePagina(this)" id="btnAgregarCarrito"
                        class="mt-7 w-full bg-red-600 hover:bg-red-700 hover:shadow-lg hover:shadow-red-600/20 text-white font-bold py-3 rounded-xl text-sm transition-all active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-600 disabled:hover:shadow-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Agregar al carrito
                    </button>

                    <div class="mt-[18px] flex gap-2">
                        <button onclick="toggleWishDesdePagina()" id="prodWishBtn"
                            class="flex-1 h-11 flex items-center justify-center gap-2 rounded-xl border border-gray-200 text-gray-600 hover:text-red-600 hover:border-red-200 font-semibold text-sm transition">
                            <svg id="prodWishIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <span id="prodWishTexto">Agregar a la lista de deseos</span>
                        </button>
                    </div>
                    @else
                    {{-- El personal (Administrador, Asesor y Gerente) puede ver el producto, pero no comprarlo --}}
                    <p class="mt-8 rounded-xl bg-gray-50 border border-gray-200 text-gray-500 text-sm px-4 py-3">
                        Las compras se hacen desde una cuenta de cliente.
                    </p>
                    @endif

                    <div class="mt-6 pt-6 border-t border-gray-100 space-y-4">
                        {{-- Acordeón: Garantía y devoluciones --}}
                        <div class="rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <button type="button" onclick="toggleAcordeon('garantiaPanel')"
                                class="w-full flex items-center gap-4 px-5 py-5 text-left">
                                <div class="h-11 w-11 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800">Garantía y devoluciones</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Qué hacer si el producto llega con un problema.</p>
                                </div>
                                <svg id="garantiaPanelIcon" class="h-4 w-4 text-gray-400 transition-transform duration-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="garantiaPanel" class="overflow-hidden transition-all duration-300" style="max-height: 0px;">
                                <div class="px-5 pb-5 space-y-4 border-t border-gray-100 pt-4">
                                    <div class="flex gap-3">
                                        <div class="h-8 w-8 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 mb-0.5">Garantía</p>
                                            <p class="text-xs text-gray-400 leading-relaxed">Todos los productos tienen garantía por defectos de fabricación. Si el tuyo presenta un problema, escríbenos y lo revisamos.</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0118 7.5M18.5 15A7 7 0 016 16.5"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 mb-0.5">Cambios y devoluciones</p>
                                            <p class="text-xs text-gray-400 leading-relaxed">Si el producto tiene un defecto de fabricación o hubo un error en la compra, escríbenos y buscamos una solución.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Confianza: fila compacta de íconos --}}
                        <div class="grid grid-cols-3 gap-2 pt-1">
                            <div class="text-center">
                                <svg class="h-5 w-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <p class="text-[11px] text-gray-500">Compra segura</p>
                            </div>
                            <div class="text-center">
                                <svg class="h-5 w-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 18v-6a9 9 0 0118 0v6M3 18a2 2 0 002 2h1a1 1 0 001-1v-4a1 1 0 00-1-1H3m18 6a2 2 0 01-2 2h-1a1 1 0 01-1-1v-4a1 1 0 011-1h3"/></svg>
                                <p class="text-[11px] text-gray-500">Soporte cercano</p>
                            </div>
                            <div class="text-center">
                                <svg class="h-5 w-5 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[11px] text-gray-500">Productos originales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            <div class="px-6 border-t mt-2">
                <button onclick="toggleAcordeon('descPanel')" class="w-full flex items-center justify-between py-4 text-left">
                    <span class="font-bold text-gray-800">Descripción del producto</span>
                    <svg id="descPanelIcon" class="h-4 w-4 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="descPanel" class="overflow-hidden transition-all duration-300">
                    <p class="text-gray-500 text-sm leading-relaxed pb-5">{{ $producto->descripcion ?: 'Sin descripción disponible.' }}</p>
                </div>
            </div>

            {{-- Recomendados --}}
            @if ($recomendados->isNotEmpty())
            <div class="px-6 pb-8 border-t pt-6">
                <div class="flex items-center justify-between mb-5">
                    <h4 class="font-bold text-gray-800">También te puede interesar</h4>
                    <div class="flex items-center gap-1.5">
                        <button onclick="recMover(-1)" class="h-7 w-7 rounded-full border border-gray-200 text-gray-500 hover:border-red-300 hover:text-red-600 flex items-center justify-center transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button onclick="recMover(1)" class="h-7 w-7 rounded-full border border-gray-200 text-gray-500 hover:border-red-300 hover:text-red-600 flex items-center justify-center transition">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                <div id="recTrack" class="flex gap-5 overflow-x-auto pb-2 -mx-1 px-1 scroll-smooth" style="scrollbar-width:none;">
                    @foreach ($recomendados as $rec)
                        <div class="flex-shrink-0 w-56">
                            @include('partials.producto-card', ['producto' => $rec])
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')

    <style>
    .gal-thumb { position: relative; border-radius: 0.85rem; overflow: hidden; cursor: pointer; border: 2px solid transparent; box-shadow: 0 1px 3px rgba(0,0,0,.06); transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease; flex-shrink: 0; background:#f3f4f6; }
    .gal-thumb:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,.1); }
    .gal-thumb.activa { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.12); }
    .gal-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .gal-slide { opacity: 0; pointer-events: none; z-index: 0; }
    .gal-slide.activa { opacity: 1; pointer-events: auto; z-index: 10; }
    .gal-img { transition: transform .25s ease-out; cursor: zoom-in; will-change: transform; }
    .res-star-input { cursor: pointer; transition: transform .15s ease; }
    .res-star-input:hover { transform: scale(1.15); }
    #recTrack::-webkit-scrollbar { display: none; }

    /* Selector de colores: paleta compacta, con nombre y stock siempre visibles debajo */
    .color-swatch-btn {
        position: relative;
        height: 26px; width: 26px;
        border-radius: 9999px;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #e5e7eb;
        cursor: pointer;
        flex-shrink: 0;
        padding: 0;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .color-swatch-btn:hover { transform: scale(1.1); box-shadow: 0 0 0 1px #fca5a5; }
    .color-swatch-btn.activa { box-shadow: 0 0 0 1.5px #dc2626, 0 2px 6px rgba(220,38,38,.2); }
    .color-swatch-btn.activa:hover { box-shadow: 0 0 0 1.5px #dc2626, 0 2px 6px rgba(220,38,38,.2); }
    .color-swatch-btn.agotado { opacity: .4; }
    .color-swatch-btn.seleccionando { animation: cardPop .3s ease; }
    @keyframes cardPop { 0% { transform: scale(1); } 40% { transform: scale(1.18); } 100% { transform: scale(1); } }

    /* Miniatura fija que representa la galería general del producto */
    .gal-thumb-general { position: relative; }
    .animate-fade-in-up { animation: fadeInUp .5s ease-out; }
    @keyframes fadeInUp { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
    window.productoId = @json($producto->id);
    window.productoNombre = @json($producto->nombre);
    window.productoPrecio = @json((float) $producto->precio);
    window.productoImagen = @json($producto->imagen);
    window.galeriaImagenes = @json($galeria);
    window.galeriaCompleta = @json($galeriaCompleta);
    window.coloresProducto = @json($colores);
    window.tieneColores = @json($tieneColores);
    window.galeriaSinColorRutas = @json($producto->galeriaSinColor());
    window.productoPortada = @json($producto->imagen);
    window.stockGeneral = @json($producto->stockActual()); // el stock es del producto (Inventario), no del color
    let colorActivoId = null;

    // ── Cantidad y agregar al carrito ──
    // La cantidad nunca pasa de las unidades disponibles (ni escribiéndola a mano).
    function cambiarCantidad(delta) {
        const inp = document.getElementById('prodCantidad');
        const stock = Math.max(0, window.stockGeneral || 0);
        let valor = (parseInt(inp.value, 10) || 1) + delta;
        if (stock > 0 && valor > stock) {
            valor = stock;
            if (delta > 0) showToast(stock === 1 ? 'Solo hay 1 disponible' : `Solo hay ${stock} disponibles`);
        }
        inp.value = Math.max(1, valor);
    }

    function agregarAlCarritoDesdePagina(btn) {
        cambiarCantidad(0);
        const qty = parseInt(document.getElementById('prodCantidad').value) || 1;
        const ok = addToCart({ id: window.productoId, nombre: window.productoNombre, precio: window.productoPrecio, imagen: window.productoImagen, stock: window.stockGeneral }, qty);
        if (ok) showToast(`${window.productoNombre} agregado al carrito`);
        pulso(btn);
    }

    // ── Wishlist propio de la página ──
    function toggleWishDesdePagina() {
        toggleWishItem(window.productoId, window.productoNombre, window.productoPrecio, window.productoImagen);
        pulso(document.getElementById('prodWishIcon'));
    }

    window.alSincronizarUI = function () {
        const icon = document.getElementById('prodWishIcon');
        if (!icon) return;
        const inWish = getWish().some(w => w.id == window.productoId);
        icon.setAttribute('fill', inWish ? '#dc2626' : 'none');
        icon.setAttribute('stroke', inWish ? '#dc2626' : 'currentColor');
        const texto = document.getElementById('prodWishTexto');
        if (texto) texto.textContent = inWish ? 'En tu lista de deseos' : 'Agregar a la lista de deseos';
    };

    // ── Galería (transición fade) ──
    let galIndex = 0;

    function imagenActiva() {
        return document.querySelector('#galImgTrack .gal-slide.activa .gal-img');
    }

    function resetZoomTodasLasImagenes() {
        // Al cambiar de foto (flechas, miniaturas o color) el zoom siempre se reinicia:
        // la nueva imagen se muestra en tamaño normal. Se apaga el zoom aunque el cursor
        // siga sobre la galería (p. ej. tras pulsar una flecha) y se guarda la posición
        // actual como referencia: solo un movimiento real del cursor (no el micro-jitter
        // que produce un clic) vuelve a activar el zoom — ver mousemove más abajo.
        document.querySelectorAll('#galImgTrack .gal-img').forEach(img => {
            img.style.transition = 'none';
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center';
        });
        inlineZoomState.scale = 1; inlineZoomState.x = 0; inlineZoomState.y = 0;
        zoomHoverActivo = false;
        zoomBaseXPercent = lastMouseXPercent;
        zoomBaseYPercent = lastMouseYPercent;
        const hint = document.getElementById('zoomHint');
        if (hint) hint.style.opacity = '1';
    }

    function galAplicarPosicion() {
        document.querySelectorAll('#galImgTrack .gal-slide').forEach((el, i) => {
            el.classList.toggle('activa', i === galIndex);
        });
        const contador = document.getElementById('galContador');
        if (contador) contador.textContent = `${galIndex + 1} / ${window.galeriaImagenes.length}`;
        document.querySelectorAll('#galThumbsCol .gal-thumb:not(.gal-thumb-general), #galThumbsRow .gal-thumb:not(.gal-thumb-general)').forEach(el => {
            el.classList.toggle('activa', Number(el.dataset.idx) === galIndex);
        });
        document.querySelectorAll('.gal-thumb-general').forEach(el => el.classList.toggle('activa', colorActivoId === null));
        resetZoomTodasLasImagenes();
    }
    function galMover(dir) {
        const total = window.galeriaImagenes.length;
        if (total <= 1) return;
        galIndex = (galIndex + dir + total) % total;
        galAplicarPosicion();
    }
    function galIrAFoto(i) { galIndex = i; galAplicarPosicion(); }

    // ── Miniatura fija "general": primer elemento de la galería lateral, siempre visible
    // cuando el producto tiene colores, para volver a la vista general con un clic. ──
    function thumbGeneralHtml(claseAlto) {
        if (!window.tieneColores) return '';
        const activa = colorActivoId === null ? 'activa' : '';
        const contenido = window.productoPortada
            ? `<img src="${window.productoPortada}" class="h-full w-full object-cover" loading="lazy">`
            : `<div class="h-full w-full flex items-center justify-center bg-gray-100 text-gray-300">
                   <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
               </div>`;
        return `
            <div class="gal-thumb gal-thumb-general ${claseAlto} ${activa} flex-shrink-0" onclick="mostrarPortada()" title="Ver galería general">
                ${contenido}
            </div>
        `;
    }

    // ── Reconstruye el track + miniaturas a partir de un listado explícito de rutas ──
    function renderGaleria(rutas) {
        window.galeriaImagenes = rutas;
        galIndex = 0;

        const track = document.getElementById('galImgTrack');
        if (track) {
            track.innerHTML = window.galeriaImagenes.map((src, i) => `
                <div class="gal-slide absolute inset-0 flex items-center justify-center transition-opacity duration-500 ease-out ${i === 0 ? 'activa' : ''}">
                    <img src="${src}" class="gal-img w-full h-full object-contain" ${i > 0 ? 'loading="lazy"' : ''} draggable="false">
                </div>
            `).join('');
        }

        // En modo general, la primera foto (portada) ya está representada por la
        // miniatura fija, así que no se repite dentro del listado de miniaturas.
        const saltarPrimera = colorActivoId === null && window.productoPortada && rutas[0] === window.productoPortada;
        const miniaturas = (claseAlto) => rutas.map((src, i) => {
            if (saltarPrimera && i === 0) return '';
            return `
                <div class="gal-thumb ${claseAlto} flex-shrink-0 ${i === 0 ? 'activa' : ''}" data-idx="${i}" onclick="galIrAFoto(${i})">
                    <img src="${src}" class="h-full w-full object-cover" loading="lazy">
                </div>
            `;
        }).join('');

        const colThumbs = document.getElementById('galThumbsCol');
        if (colThumbs) colThumbs.innerHTML = thumbGeneralHtml('h-16 w-16') + miniaturas('h-16 w-16');
        const rowThumbs = document.getElementById('galThumbsRow');
        if (rowThumbs) rowThumbs.innerHTML = thumbGeneralHtml('h-14 w-14') + miniaturas('h-14 w-14');

        const haySuficientes = window.galeriaImagenes.length > 1;
        document.querySelectorAll('.gal-nav-arrow').forEach(el => el.classList.toggle('hidden', !haySuficientes));
        document.getElementById('galContador')?.classList.toggle('hidden', !haySuficientes);

        galAplicarPosicion();
    }

    // ── Stock / disponibilidad: refleja el color activo (o el agregado del producto) ──
    function actualizarBadgeDisponibilidad(stock) {
        const badge = document.getElementById('badgeDisponibilidad');
        if (!badge) return;
        const disponible = stock > 0;
        badge.textContent = disponible ? 'Disponible' : 'Sin existencias';
        badge.classList.toggle('bg-green-50', disponible);
        badge.classList.toggle('text-green-700', disponible);
        badge.classList.toggle('bg-gray-800', !disponible);
        badge.classList.toggle('text-white', !disponible);
    }

    function actualizarControlesStock(stock) {
        actualizarBadgeDisponibilidad(stock);
        const btn = document.getElementById('btnAgregarCarrito');
        const cantidad = document.getElementById('prodCantidad');
        if (btn) btn.disabled = stock <= 0;
        if (cantidad) {
            cantidad.max = Math.max(1, stock);
            if (parseInt(cantidad.value || 1) > cantidad.max) cantidad.value = cantidad.max;
        }
        const disp = document.getElementById('prodDisponibles');
        if (disp) disp.textContent = stock <= 0 ? '' : (stock === 1 ? 'Queda 1 disponible' : `${stock} disponibles`);
    }

    // ── Portada general: vista por defecto (no pertenece a ningún color) ──
    function mostrarPortada() {
        colorActivoId = null;
        const rutas = [];
        if (window.productoPortada) rutas.push(window.productoPortada);
        window.galeriaSinColorRutas.forEach(r => { if (!rutas.includes(r)) rutas.push(r); });
        renderGaleria(rutas);

        document.querySelectorAll('.color-swatch-btn[data-color-id]').forEach(el => el.classList.remove('activa'));
        actualizarControlesStock(window.stockGeneral);
    }

    // ── Selector de color: cambia toda la galería (imagen principal primero + miniaturas).
    // El color solo agrupa fotos; el stock es el del producto.
    // Volver a pulsar el mismo color reinicia su galería mostrando de nuevo la imagen principal. ──
    function seleccionarColor(colorId, btn) {
        colorActivoId = colorId;

        const color = window.coloresProducto.find(c => c.id === colorId);
        const rutas = window.galeriaCompleta.filter(img => img.color_id === colorId).map(img => img.ruta);
        renderGaleria(rutas);

        document.querySelectorAll('.color-swatch-btn[data-color-id]').forEach(el => el.classList.remove('activa'));
        btn.classList.add('activa');
        btn.classList.remove('seleccionando');
        void btn.offsetWidth; // reinicia la animación aunque se repita el mismo color
        btn.classList.add('seleccionando');

        actualizarControlesStock(window.stockGeneral);
    }

    // ── Zoom in-line: ocurre sobre la misma foto, sin modal ni panel aparte ──
    const zoomHoverCapaz = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const ZOOM_FACTOR_HOVER = 2.2;
    const ZOOM_FACTOR_TAP = 2.4;
    let zoomHoverActivo = false;
    let lastMouseXPercent = 50, lastMouseYPercent = 50;
    let zoomBaseXPercent = 50, zoomBaseYPercent = 50;
    const ZOOM_REACTIVAR_UMBRAL = 4; // % del ancho/alto del stage que debe moverse el cursor para reactivar el zoom tras un cambio de foto
    const inlineZoomState = { scale: 1, x: 0, y: 0 };

    function inicializarZoomHover() {
        if (!zoomHoverCapaz) return;
        const stage = document.getElementById('galStage');
        if (!stage) return;

        stage.addEventListener('mouseenter', () => {
            const img = imagenActiva();
            if (!img) return;
            zoomHoverActivo = true;
            img.style.transition = 'transform .25s ease-out';
            img.style.transformOrigin = `${lastMouseXPercent}% ${lastMouseYPercent}%`;
            img.style.transform = `scale(${ZOOM_FACTOR_HOVER})`;
            const hint = document.getElementById('zoomHint');
            if (hint) hint.style.opacity = '0';
        });
        stage.addEventListener('mousemove', (e) => {
            const rect = stage.getBoundingClientRect();
            lastMouseXPercent = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
            lastMouseYPercent = Math.max(0, Math.min(100, ((e.clientY - rect.top) / rect.height) * 100));
            const img = imagenActiva();
            if (!img) return;

            if (!zoomHoverActivo) {
                // Tras un cambio de foto el zoom queda apagado aunque el cursor siga sobre
                // la galería (p. ej. al pulsar una flecha). Solo lo reactivamos si el cursor
                // se mueve de verdad — así el clic en sí (que produce un mousemove mínimo)
                // no vuelve a "pegar" el zoom sobre la nueva imagen.
                const distancia = Math.hypot(lastMouseXPercent - zoomBaseXPercent, lastMouseYPercent - zoomBaseYPercent);
                if (distancia < ZOOM_REACTIVAR_UMBRAL) return;
                zoomHoverActivo = true;
                const hint = document.getElementById('zoomHint');
                if (hint) hint.style.opacity = '0';
            }

            img.style.transition = 'none';
            img.style.transformOrigin = `${lastMouseXPercent}% ${lastMouseYPercent}%`;
            img.style.transform = `scale(${ZOOM_FACTOR_HOVER})`;
        });
        stage.addEventListener('mouseleave', () => {
            zoomHoverActivo = false;
            const img = imagenActiva();
            if (!img) return;
            img.style.transition = 'transform .25s ease-out';
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center';
            const hint = document.getElementById('zoomHint');
            if (hint) hint.style.opacity = '1';
        });
    }

    // ── Zoom táctil: pinch-to-zoom, arrastrar con zoom activo, doble toque y swipe para cambiar de foto ──
    function aplicarTransformTactil() {
        const img = imagenActiva();
        if (!img) return;
        img.style.transition = 'none';
        img.style.transformOrigin = 'center';
        img.style.transform = `translate(${inlineZoomState.x}px, ${inlineZoomState.y}px) scale(${inlineZoomState.scale})`;
    }

    (function initZoomTactil() {
        const stage = document.getElementById('galStage');
        if (!stage) return;

        const pointers = new Map();
        let pinchStartDist = 0, pinchStartScale = 1, dragStart = null, lastTap = 0;

        stage.addEventListener('pointerdown', (e) => {
            if (e.pointerType !== 'touch') return;
            pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (pointers.size === 1) {
                dragStart = { x: e.clientX - inlineZoomState.x, y: e.clientY - inlineZoomState.y, startX: e.clientX, moved: false };
            } else if (pointers.size === 2) {
                const pts = [...pointers.values()];
                pinchStartDist = Math.hypot(pts[0].x - pts[1].x, pts[0].y - pts[1].y);
                pinchStartScale = inlineZoomState.scale;
            }
        });
        stage.addEventListener('pointermove', (e) => {
            if (e.pointerType !== 'touch' || !pointers.has(e.pointerId)) return;
            pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (pointers.size === 2) {
                const pts = [...pointers.values()];
                const dist = Math.hypot(pts[0].x - pts[1].x, pts[0].y - pts[1].y);
                inlineZoomState.scale = Math.min(4, Math.max(1, pinchStartScale * (dist / pinchStartDist)));
                aplicarTransformTactil();
            } else if (pointers.size === 1 && dragStart) {
                if (inlineZoomState.scale > 1) {
                    inlineZoomState.x = e.clientX - dragStart.x;
                    inlineZoomState.y = e.clientY - dragStart.y;
                    aplicarTransformTactil();
                }
                if (Math.abs(e.clientX - dragStart.startX) > 5) dragStart.moved = true;
            }
        });
        function terminarPointer(e) {
            if (e.pointerType !== 'touch') return;
            const fueTapSimple = pointers.size === 1 && dragStart && !dragStart.moved;
            if (pointers.size === 1 && dragStart && inlineZoomState.scale === 1 && dragStart.moved) {
                const delta = e.clientX - dragStart.startX;
                if (delta > 60) galMover(-1);
                else if (delta < -60) galMover(1);
            }
            if (fueTapSimple) {
                const ahora = Date.now();
                if (ahora - lastTap < 300) {
                    inlineZoomState.scale = inlineZoomState.scale > 1 ? 1 : ZOOM_FACTOR_TAP;
                    inlineZoomState.x = 0; inlineZoomState.y = 0;
                    const img = imagenActiva();
                    if (img) img.style.transition = 'transform .25s ease-out';
                    aplicarTransformTactil();
                }
                lastTap = ahora;
            }
            pointers.delete(e.pointerId);
            dragStart = null;
        }
        stage.addEventListener('pointerup', terminarPointer);
        stage.addEventListener('pointercancel', terminarPointer);
    })();

    document.addEventListener('DOMContentLoaded', inicializarZoomHover);
    document.addEventListener('DOMContentLoaded', () => {
        actualizarControlesStock(window.stockGeneral);
    });

    // ── Recomendados: carrusel horizontal ──
    function recMover(dir) {
        const track = document.getElementById('recTrack');
        if (!track) return;
        track.scrollBy({ left: dir * 200, behavior: 'smooth' });
    }

    // ── Acordeón: abrir descripción por defecto ──
    document.addEventListener('DOMContentLoaded', () => abrirAcordeon('descPanel'));

    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
        if (e.key === 'ArrowLeft') galMover(-1);
        if (e.key === 'ArrowRight') galMover(1);
    });
    </script>
</div>
@endsection
