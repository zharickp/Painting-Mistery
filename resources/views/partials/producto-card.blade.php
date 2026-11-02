@php $puedeComprar = ! auth()->check() || auth()->user()->puedeComprar(); @endphp
@php
    $segunda = $producto->segundaImagen();
@endphp
<div class="prod-card group bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
     data-id="{{ $producto->id }}"
     data-nombre="{{ $producto->nombre }}"
     data-precio="{{ $producto->precio }}"
     data-imagen="{{ $producto->imagen ?? '' }}"
     data-cat="{{ $producto->categoria_producto_id ?? '' }}">

    <a href="{{ route('producto.show', $producto) }}" class="relative h-48 bg-gray-100 overflow-hidden block">
        @if ($producto->imagen)
            <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}" loading="lazy"
                 class="absolute inset-0 h-full w-full object-cover transition duration-500 {{ $segunda ? 'group-hover:opacity-0' : 'group-hover:scale-105' }}">
            @if ($segunda)
                <img src="{{ $segunda }}" alt="{{ $producto->nombre }}" loading="lazy"
                     class="absolute inset-0 h-full w-full object-cover opacity-0 scale-105 transition duration-500 group-hover:opacity-100">
            @endif
        @else
            <div class="h-full w-full flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

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

        @if($puedeComprar)
        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); toggleWish(this.closest('.prod-card'), this)"
            class="wish-btn absolute top-2 right-2 bg-white rounded-full p-1.5 shadow-md hover:scale-110 transition-transform"
            title="Agregar a lista de deseos">
            <svg class="h-4 w-4 text-gray-400 wish-icon transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        @endif
    </a>

    <div class="p-3 sm:p-4">
        <a href="{{ route('producto.show', $producto) }}" class="font-semibold text-gray-800 text-sm mb-1 hover:text-red-600 transition-colors block line-clamp-2">{{ $producto->nombre }}</a>
        @if ($producto->categoria)
            <span class="inline-block bg-red-50 text-red-600 text-xs px-2 py-0.5 rounded-full my-1.5">{{ $producto->categoria->nombre }}</span>
        @endif
        <div class="flex items-center justify-between gap-2 mt-1.5">
            <div>
                @if ($producto->tieneDescuento())
                    <p class="text-gray-400 text-xs line-through">${{ number_format($producto->precio_anterior, 0, ',', '.') }}</p>
                @endif
                <p class="text-red-600 font-bold">${{ number_format($producto->precio, 0, ',', '.') }}</p>
            </div>
            @if ($producto->estaAgotado())
                <span class="shrink-0 text-[11px] font-semibold text-gray-400 bg-gray-100 rounded-full px-2 sm:px-3 py-1.5">Agotado</span>
            @elseif ($puedeComprar)
                <button type="button" onclick="addToCartDesdeCard(this.closest('.prod-card'), this)"
                    title="Agregar al carrito" aria-label="Agregar al carrito"
                    class="h-10 w-10 sm:h-11 sm:w-11 shrink-0 rounded-full bg-red-600 hover:bg-gray-900 text-white flex items-center justify-center shadow-md shadow-red-600/30 hover:shadow-gray-900/30 transition-all duration-300 hover:scale-110 active:scale-90">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v4m-2-2h4" stroke-width="2.2"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
