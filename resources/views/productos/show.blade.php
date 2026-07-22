@extends('layouts.guest')

@section('title', $producto->nombre . ' - Painting Mistery')

@section('content')
@php
    $galeria = $producto->galeria();
    $colores = $producto->coloresDisponibles();
    $resumen = $producto->resumenResenas();
    $miResena = auth()->check() ? $producto->resenas->firstWhere('usuario_id', auth()->id()) : null;
    $waTexto = "Hola! Me interesa el producto: *{$producto->nombre}* (\${$producto->precio}). ¿Está disponible? 🏍️";
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
                            @foreach ($galeria as $i => $src)
                                <div class="gal-thumb h-16 w-16 {{ $i === 0 ? 'activa' : '' }}" onclick="galIrAFoto({{ $i }})">
                                    <img src="{{ $src }}" class="h-full w-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <div class="relative flex-1 min-w-0">
                            <div class="relative bg-gray-50 rounded-2xl overflow-hidden" style="aspect-ratio:1/1;">
                                @if (empty($galeria))
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @else
                                    <div id="galImgTrack" class="flex h-full transition-transform duration-500 ease-[cubic-bezier(.22,1,.36,1)]">
                                        @foreach ($galeria as $src)
                                            <div class="w-full h-full flex-shrink-0 cursor-zoom-in" onclick="abrirZoom()">
                                                <img src="{{ $src }}" class="w-full h-full object-contain">
                                            </div>
                                        @endforeach
                                    </div>
                                    @if (count($galeria) > 1)
                                        <button onclick="galMover(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-white/95 hover:bg-white shadow-lg flex items-center justify-center text-gray-700 transition hover:scale-110">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                        </button>
                                        <button onclick="galMover(1)" class="absolute right-2 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-white/95 hover:bg-white shadow-lg flex items-center justify-center text-gray-700 transition hover:scale-110">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                        <div id="galContador" class="absolute top-3 left-3 bg-black/50 text-white text-xs font-semibold px-2.5 py-1 rounded-full">1 / {{ count($galeria) }}</div>
                                    @endif
                                    <button onclick="abrirZoom()" class="absolute bottom-3 right-3 bg-white/95 hover:bg-white rounded-full px-3 py-1.5 text-xs font-medium text-gray-600 flex items-center gap-1.5 shadow-md transition">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zm-3 0v3m0 0v3m0-3h3m-3 0H9"/></svg>
                                        Ampliar
                                    </button>
                                @endif
                            </div>
                            @if (count($galeria) > 1)
                                <div class="flex md:hidden gap-2 mt-3 overflow-x-auto pb-1" id="galThumbsRow">
                                    @foreach ($galeria as $i => $src)
                                        <div class="gal-thumb h-14 w-14 {{ $i === 0 ? 'activa' : '' }}" onclick="galIrAFoto({{ $i }})">
                                            <img src="{{ $src }}" class="h-full w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-6 flex flex-col gap-4">
                    <div>
                        @if ($producto->categoria)
                            <span class="text-xs font-semibold text-red-600 uppercase tracking-widest">{{ $producto->categoria->nombre }}</span>
                        @endif
                        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mt-1 mb-2">{{ $producto->nombre }}</h1>
                        <div class="flex items-center gap-1.5 mb-3">
                            <div class="flex">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= round($resumen['promedio']) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <span class="text-sm font-bold text-gray-700">{{ $resumen['total'] ? number_format($resumen['promedio'], 1) : 'Nuevo' }}</span>
                            @if ($resumen['total'])
                                <span class="text-xs text-gray-400">({{ $resumen['total'] }} reseña{{ $resumen['total'] === 1 ? '' : 's' }})</span>
                            @endif
                        </div>
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
                            <span class="inline-block bg-gray-800 text-white text-xs font-semibold px-2.5 py-1 rounded-full">Sin existencias</span>
                        @else
                            <span class="inline-block bg-green-50 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">Disponible</span>
                        @endif
                    </div>

                    @if (count($colores) > 0)
                        <div>
                            <p class="text-sm text-gray-600 mb-1.5">Color: <span id="colorSeleccionado" class="font-semibold text-gray-800">{{ $colores[0]['nombre'] }}</span></p>
                            <div class="flex items-center gap-2">
                                @foreach ($colores as $c)
                                    <button type="button" onclick="seleccionarColor({{ $c['indice'] }}, '{{ addslashes($c['nombre']) }}', this)"
                                        class="color-swatch h-8 w-8 rounded-full border-2 {{ $loop->first ? 'border-red-500' : 'border-gray-200' }} transition hover:scale-110"
                                        style="background-color: {{ $c['hex'] }};" title="{{ $c['nombre'] }}"></button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">Cantidad:</span>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                            <button onclick="cambiarCantidad(-1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition font-bold text-lg leading-none">−</button>
                            <input id="prodCantidad" type="number" value="1" min="1" max="99" class="w-12 text-center border-0 text-sm font-semibold focus:outline-none">
                            <button onclick="cambiarCantidad(1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition font-bold text-lg leading-none">+</button>
                        </div>
                    </div>

                    <button onclick="agregarAlCarritoDesdePagina()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Agregar al carrito
                    </button>

                    <div class="flex gap-2">
                        <a href="https://wa.me/573144557602?text={{ urlencode($waTexto) }}" target="_blank"
                           class="flex-1 flex items-center justify-center gap-2 border-2 border-green-500 text-green-600 hover:bg-green-500 hover:text-white font-semibold py-2.5 rounded-xl text-sm transition">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        <button onclick="toggleWishDesdePagina()" id="prodWishBtn"
                            class="h-11 w-11 flex-shrink-0 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 transition" title="Lista de deseos">
                            <svg id="prodWishIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                        <div class="text-center">
                            <svg class="h-5 w-5 text-green-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <p class="text-xs text-gray-500">Garantía</p>
                        </div>
                        <div class="text-center">
                            <svg class="h-5 w-5 text-blue-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <p class="text-xs text-gray-500">Pago seguro</p>
                        </div>
                        <div class="text-center">
                            <svg class="h-5 w-5 text-orange-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-xs text-gray-500">Soporte</p>
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

            {{-- Reseñas --}}
            <div class="px-6 pb-6 border-t pt-5">
                <h4 class="font-bold text-gray-800 mb-4">Reseñas de clientes</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="flex gap-5">
                        <div class="text-center flex-shrink-0">
                            <p class="text-4xl font-extrabold text-gray-900">{{ $resumen['total'] ? number_format($resumen['promedio'], 1) : '—' }}</p>
                            <div class="flex justify-center gap-0.5 my-1.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= round($resumen['promedio']) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-xs text-gray-400 whitespace-nowrap">{{ $resumen['total'] ? "{$resumen['total']} reseña" . ($resumen['total'] === 1 ? '' : 's') : 'Sé el primero en opinar' }}</p>
                        </div>
                        <div class="flex-1 space-y-1.5 self-center">
                            @foreach ($resumen['distribucion'] as $d)
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="text-gray-500 w-3">{{ $d['estrella'] }}</span>
                                    <svg class="h-3 w-3 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-yellow-400 h-1.5 rounded-full" style="width:{{ $d['porcentaje'] }}%"></div>
                                    </div>
                                    <span class="text-gray-400 w-4 text-right">{{ $d['cantidad'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Formulario --}}
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p class="text-sm font-semibold text-gray-700 mb-2" id="resFormTitulo">Danos tu opinión</p>
                        @guest
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <input id="resNombreInput" type="text" placeholder="Tu nombre" maxlength="100"
                                       class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition bg-white">
                                <input id="resCorreoInput" type="email" placeholder="Tu correo" maxlength="150"
                                       class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition bg-white">
                            </div>
                        @endguest
                        <div class="flex items-center gap-1 mb-3" id="resRatingInput"></div>
                        <textarea id="resComentarioInput" rows="3" maxlength="1000" placeholder="¿Qué te pareció el producto?"
                                  class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-red-400 transition resize-none mb-2 bg-white"></textarea>
                        <button onclick="enviarResena()" id="resEnviarBtn"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition w-full sm:w-auto">
                            Publicar reseña
                        </button>
                    </div>
                </div>

                <div id="resLista" class="space-y-4">
                    @forelse ($producto->resenas as $r)
                        <div class="flex gap-3">
                            <div class="h-9 w-9 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($r->nombreMostrar(), 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mb-0.5">
                                    <span class="font-semibold text-gray-800 text-xs">{{ $r->nombreMostrar() }}</span>
                                    @auth
                                        @if ($r->usuario_id === auth()->id())
                                            <span class="text-[10px] bg-red-50 text-red-600 font-semibold px-1.5 py-0.5 rounded-full">Tu reseña</span>
                                        @endif
                                    @endauth
                                    <div class="flex">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="h-3 w-3 {{ $i <= $r->calificacion ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="text-[11px] text-gray-400">{{ $r->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-500 text-xs leading-relaxed">{{ $r->comentario }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6" id="resVacio">Todavía no hay reseñas para este producto. ¡Sé el primero en opinar!</p>
                    @endforelse
                </div>
            </div>

            {{-- Recomendados --}}
            @if ($recomendados->isNotEmpty())
            <div class="px-6 pb-8 border-t pt-6">
                <div class="flex items-center justify-between mb-5">
                    <h4 class="font-bold text-gray-800">¡Otras del mismo estilo!</h4>
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
                        <a href="{{ route('producto.show', $rec) }}" class="flex-shrink-0 w-20 flex flex-col items-center gap-2 group">
                            <div class="h-20 w-20 rounded-full bg-gray-100 overflow-hidden border-2 border-transparent group-hover:border-red-400 transition">
                                @if ($rec->imagen)
                                    <img src="{{ $rec->imagen }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <p class="text-xs font-semibold text-gray-700 text-center truncate w-full group-hover:text-red-600 transition">{{ $rec->nombre }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Zoom --}}
    <div id="zoomModal" class="hidden fixed inset-0 z-[60] bg-black/95 select-none">
        <div id="zoomStage" class="absolute inset-0 flex items-center justify-center overflow-hidden" style="touch-action:none;">
            <img id="zoomImg" src="" alt="" class="max-w-none pointer-events-none" draggable="false">
        </div>
        <div class="absolute top-4 right-4 flex items-center gap-2 z-10">
            <button onclick="zoomAjustar(-1)" class="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
            </button>
            <button onclick="zoomAjustar(1)" class="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/></svg>
            </button>
            <button onclick="cerrarZoom()" class="h-10 w-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-4 z-10">
            <button onclick="zoomGaleriaMover(-1)" class="h-9 w-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span id="zoomContador" class="text-white/70 text-xs font-medium min-w-[3rem] text-center"></span>
            <button onclick="zoomGaleriaMover(1)" class="h-9 w-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <p class="absolute bottom-5 right-5 text-white/40 text-[11px] hidden sm:block">Rueda del mouse o pellizca para acercar · Arrastra para mover</p>
    </div>

    @include('partials.footer')
    @include('partials.carrito-wishlist-modales')
    @include('partials.tienda-scripts')

    <style>
    .gal-thumb { position: relative; border-radius: 0.75rem; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: border-color .2s ease, transform .2s ease; flex-shrink: 0; background:#f3f4f6; }
    .gal-thumb:hover { transform: translateY(-1px); }
    .gal-thumb.activa { border-color: #dc2626; }
    .gal-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .res-star-input { cursor: pointer; transition: transform .15s ease; }
    .res-star-input:hover { transform: scale(1.15); }
    #zoomImg { transition: transform .12s ease-out; }
    #recTrack::-webkit-scrollbar { display: none; }
    </style>

    <script>
    window.productoId = @json($producto->id);
    window.productoNombre = @json($producto->nombre);
    window.productoPrecio = @json((float) $producto->precio);
    window.productoImagen = @json($producto->imagen);
    window.galeriaImagenes = @json($galeria);
    window.rutaResenaStore = @json(route('resenas.store', $producto));
    window.usuarioActualId = @json(auth()->id());

    // ── Cantidad y agregar al carrito ──
    function cambiarCantidad(delta) {
        const inp = document.getElementById('prodCantidad');
        inp.value = Math.max(1, parseInt(inp.value || 1) + delta);
    }

    function agregarAlCarritoDesdePagina() {
        const qty = parseInt(document.getElementById('prodCantidad').value) || 1;
        addToCart({ id: window.productoId, nombre: window.productoNombre, precio: window.productoPrecio, imagen: window.productoImagen }, qty);
        showToast(`${window.productoNombre} añadido al carrito 🛒`);
    }

    // ── Wishlist propio de la página ──
    function toggleWishDesdePagina() {
        toggleWishItem(window.productoId, window.productoNombre, window.productoPrecio, window.productoImagen);
    }

    window.alSincronizarUI = function () {
        const icon = document.getElementById('prodWishIcon');
        if (!icon) return;
        const inWish = getWish().some(w => w.id == window.productoId);
        icon.setAttribute('fill', inWish ? '#dc2626' : 'none');
        icon.setAttribute('stroke', inWish ? '#dc2626' : 'currentColor');
    };

    // ── Galería ──
    let galIndex = 0;
    function galAplicarPosicion() {
        const track = document.getElementById('galImgTrack');
        if (!track) return;
        track.style.transform = `translateX(-${galIndex * 100}%)`;
        const contador = document.getElementById('galContador');
        if (contador) contador.textContent = `${galIndex + 1} / ${window.galeriaImagenes.length}`;
        document.querySelectorAll('#galThumbsCol .gal-thumb, #galThumbsRow .gal-thumb').forEach((el, i) => {
            el.classList.toggle('activa', i % window.galeriaImagenes.length === galIndex);
        });
    }
    function galMover(dir) {
        const total = window.galeriaImagenes.length;
        if (total <= 1) return;
        galIndex = (galIndex + dir + total) % total;
        galAplicarPosicion();
    }
    function galIrAFoto(i) { galIndex = i; galAplicarPosicion(); }

    // ── Selector de color: salta a la foto de ese color y marca el swatch activo ──
    function seleccionarColor(indice, nombre, btn) {
        galIrAFoto(indice);
        document.getElementById('colorSeleccionado').textContent = nombre;
        document.querySelectorAll('.color-swatch').forEach(el => el.classList.remove('border-red-500'));
        document.querySelectorAll('.color-swatch').forEach(el => el.classList.add('border-gray-200'));
        btn.classList.remove('border-gray-200');
        btn.classList.add('border-red-500');
    }

    // ── Recomendados: carrusel horizontal ──
    function recMover(dir) {
        const track = document.getElementById('recTrack');
        if (!track) return;
        track.scrollBy({ left: dir * 200, behavior: 'smooth' });
    }

    // ── Acordeón: abrir descripción por defecto ──
    document.addEventListener('DOMContentLoaded', () => abrirAcordeon('descPanel'));

    // ── Reseñas: estrellas de calificación ──
    let resRatingSeleccion = @json(optional($miResena)->calificacion) || 0;

    function pintarEstrellasInput() {
        const input = document.getElementById('resRatingInput');
        if (!input) return;
        input.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const activa = i <= resRatingSeleccion;
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('class', `res-star-input h-7 w-7 ${activa ? 'text-yellow-400' : 'text-gray-200'}`);
            svg.setAttribute('fill', 'currentColor');
            svg.setAttribute('viewBox', '0 0 20 20');
            svg.innerHTML = '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>';
            const idx = i;
            svg.onclick = () => { resRatingSeleccion = idx; pintarEstrellasInput(); };
            input.appendChild(svg);
        }
    }
    pintarEstrellasInput();

    @if ($miResena)
        document.getElementById('resComentarioInput').value = @json($miResena->comentario);
        document.getElementById('resEnviarBtn').textContent = 'Actualizar reseña';
        document.getElementById('resFormTitulo').textContent = 'Tu opinión';
    @endif

    async function enviarResena() {
        const comentario = document.getElementById('resComentarioInput').value.trim();
        if (resRatingSeleccion < 1) { showToast('Selecciona una calificación en estrellas'); return; }
        if (comentario.length < 5) { showToast('Escribe una reseña un poco más larga'); return; }

        const payload = { calificacion: resRatingSeleccion, comentario };

        if (!window.usuarioActualId) {
            const nombre = document.getElementById('resNombreInput').value.trim();
            const correo = document.getElementById('resCorreoInput').value.trim();
            if (!nombre) { showToast('Escribe tu nombre'); return; }
            if (!correo) { showToast('Escribe tu correo'); return; }
            payload.nombre = nombre;
            payload.correo = correo;
        }

        const btn = document.getElementById('resEnviarBtn');
        const textoOriginal = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Enviando...';

        try {
            const resp = await fetch(window.rutaResenaStore, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!resp.ok) throw new Error('No se pudo enviar la reseña');
            const data = await resp.json();

            showToast('¡Gracias por tu reseña! 🌟');
            setTimeout(() => window.location.reload(), 900);
        } catch (e) {
            showToast('Ocurrió un error, intenta de nuevo');
            btn.disabled = false;
            btn.textContent = textoOriginal;
        }
    }

    // ── Zoom con pan + pinch + rueda ──
    const zoomState = { scale: 1, x: 0, y: 0, index: 0 };
    const zoomPointers = new Map();
    let zoomPinchStartDist = 0, zoomPinchStartScale = 1, zoomDragStart = null;

    function abrirZoom() {
        if (!window.galeriaImagenes.length) return;
        zoomState.index = galIndex;
        resetZoomTransform();
        renderZoomImg();
        document.getElementById('zoomModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function cerrarZoom() {
        document.getElementById('zoomModal').classList.add('hidden');
        document.body.style.overflow = '';
        galIndex = zoomState.index;
        galAplicarPosicion();
    }
    function renderZoomImg() {
        document.getElementById('zoomImg').src = window.galeriaImagenes[zoomState.index];
        document.getElementById('zoomContador').textContent = `${zoomState.index + 1} / ${window.galeriaImagenes.length}`;
        aplicarTransformZoom();
    }
    function resetZoomTransform() { zoomState.scale = 1; zoomState.x = 0; zoomState.y = 0; aplicarTransformZoom(); }
    function aplicarTransformZoom() {
        const img = document.getElementById('zoomImg');
        img.style.transform = `translate(${zoomState.x}px, ${zoomState.y}px) scale(${zoomState.scale})`;
        img.style.cursor = zoomState.scale > 1 ? 'grab' : 'default';
    }
    function zoomAjustar(dir) {
        zoomState.scale = Math.min(4, Math.max(1, zoomState.scale + dir * 0.5));
        if (zoomState.scale === 1) { zoomState.x = 0; zoomState.y = 0; }
        aplicarTransformZoom();
    }
    function zoomGaleriaMover(dir) {
        const total = window.galeriaImagenes.length;
        if (total <= 1) return;
        zoomState.index = (zoomState.index + dir + total) % total;
        resetZoomTransform();
        renderZoomImg();
    }
    (function initZoomGestos() {
        const stage = document.getElementById('zoomStage');
        if (!stage) return;
        stage.addEventListener('wheel', (e) => { e.preventDefault(); zoomAjustar(e.deltaY < 0 ? 1 : -1); }, { passive: false });
        stage.addEventListener('dblclick', () => {
            zoomState.scale = zoomState.scale > 1 ? 1 : 2.2;
            if (zoomState.scale === 1) { zoomState.x = 0; zoomState.y = 0; }
            aplicarTransformZoom();
        });
        stage.addEventListener('pointerdown', (e) => {
            zoomPointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (zoomPointers.size === 1) {
                zoomDragStart = { x: e.clientX - zoomState.x, y: e.clientY - zoomState.y, startX: e.clientX, moved: false };
            } else if (zoomPointers.size === 2) {
                const pts = [...zoomPointers.values()];
                zoomPinchStartDist = Math.hypot(pts[0].x - pts[1].x, pts[0].y - pts[1].y);
                zoomPinchStartScale = zoomState.scale;
            }
        });
        stage.addEventListener('pointermove', (e) => {
            if (!zoomPointers.has(e.pointerId)) return;
            zoomPointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (zoomPointers.size === 2) {
                const pts = [...zoomPointers.values()];
                const dist = Math.hypot(pts[0].x - pts[1].x, pts[0].y - pts[1].y);
                zoomState.scale = Math.min(4, Math.max(1, zoomPinchStartScale * (dist / zoomPinchStartDist)));
                aplicarTransformZoom();
            } else if (zoomPointers.size === 1 && zoomDragStart) {
                if (zoomState.scale > 1) {
                    zoomState.x = e.clientX - zoomDragStart.x;
                    zoomState.y = e.clientY - zoomDragStart.y;
                    aplicarTransformZoom();
                }
                if (Math.abs(e.clientX - zoomDragStart.startX) > 5) zoomDragStart.moved = true;
            }
        });
        function terminarPointer(e) {
            if (zoomPointers.size === 1 && zoomDragStart && zoomState.scale === 1 && zoomDragStart.moved) {
                const delta = e.clientX - zoomDragStart.startX;
                if (delta > 60) zoomGaleriaMover(-1);
                else if (delta < -60) zoomGaleriaMover(1);
            }
            zoomPointers.delete(e.pointerId);
            zoomDragStart = null;
        }
        stage.addEventListener('pointerup', terminarPointer);
        stage.addEventListener('pointercancel', terminarPointer);
        stage.addEventListener('pointerleave', terminarPointer);
    })();

    document.addEventListener('keydown', (e) => {
        const zoomAbierto = !document.getElementById('zoomModal').classList.contains('hidden');
        if (zoomAbierto) {
            if (e.key === 'Escape') cerrarZoom();
            if (e.key === 'ArrowLeft') zoomGaleriaMover(-1);
            if (e.key === 'ArrowRight') zoomGaleriaMover(1);
        }
    });
    </script>
</div>
@endsection
