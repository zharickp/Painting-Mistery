@php
    $resumen = $producto->resumenResenas();
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

        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); toggleWish(this.closest('.prod-card'), this)"
            class="wish-btn absolute top-2 right-2 bg-white rounded-full p-1.5 shadow-md hover:scale-110 transition-transform"
            title="Agregar a lista de deseos">
            <svg class="h-4 w-4 text-gray-400 wish-icon transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </a>

    <div class="p-4">
        <a href="{{ route('producto.show', $producto) }}" class="font-semibold text-gray-800 text-sm mb-1 hover:text-red-600 transition-colors block line-clamp-2">{{ $producto->nombre }}</a>
        @if ($producto->categoria)
            <span class="inline-block bg-red-50 text-red-600 text-xs px-2 py-0.5 rounded-full my-1.5">{{ $producto->categoria->nombre }}</span>
        @endif
        @if ($resumen['total'] > 0)
            <div class="flex items-center gap-0.5 mb-1.5">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="h-3 w-3 {{ $i <= round($resumen['promedio']) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
                <span class="text-[11px] text-gray-400 ml-1">({{ $resumen['total'] }})</span>
            </div>
        @endif
        <div class="flex items-center justify-between mt-1.5">
            <div>
                @if ($producto->tieneDescuento())
                    <p class="text-gray-400 text-xs line-through">${{ number_format($producto->precio_anterior, 0, ',', '.') }}</p>
                @endif
                <p class="text-red-600 font-bold">${{ number_format($producto->precio, 0, ',', '.') }}</p>
            </div>
            <button type="button" onclick="addToCartDesdeCard(this.closest('.prod-card'), this)"
                class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-transform active:scale-90">
                Agregar
            </button>
        </div>
    </div>
</div>
