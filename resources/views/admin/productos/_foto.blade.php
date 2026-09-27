{{-- Tarjeta de una foto en la edición del producto.
     Variables: $producto, $img, $conPortada (solo las fotos generales pueden ser portada). --}}
<div class="foto-card group relative border border-gray-200 rounded-lg p-1.5 bg-white cursor-move transition" data-id="{{ $img->id }}">
    <img src="{{ $img->ruta }}" alt="" draggable="false" class="h-24 w-full object-cover rounded-md pointer-events-none">

    @if ($conPortada)
        <label class="mt-1.5 flex items-center justify-center gap-1 text-[11px] text-gray-500 cursor-pointer">
            <input type="radio" name="imagen_portada" value="{{ $img->id }}" class="accent-red-600"
                   {{ $producto->imagen === $img->ruta ? 'checked' : '' }}>
            Portada
        </label>
    @endif

    <button type="button"
            onclick="eliminarFoto(this, @js(route('admin.productos.imagenes.destroy', [$producto, $img])))"
            class="mt-1.5 w-full text-[11px] font-medium text-red-600 border border-red-100 rounded-md py-1 hover:bg-red-50 transition">
        Eliminar
    </button>
</div>
