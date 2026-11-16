{{-- Tarjeta de una foto en la edición del producto. Se arrastra para cambiar el orden.
     Variables: $producto, $img, $conPortada (solo las fotos generales pueden ser portada). --}}
<div class="foto-card group relative aspect-square rounded-lg overflow-hidden border border-gray-200 bg-white cursor-move" data-id="{{ $img->id }}">
    <img src="{{ $img->ruta }}" alt="" draggable="false" class="h-full w-full object-cover pointer-events-none">

    @if ($conPortada)
        <label class="absolute bottom-1 left-1 cursor-pointer" title="Usar como portada">
            <input type="radio" name="imagen_portada" value="{{ $img->id }}" class="peer sr-only"
                   {{ $producto->imagen === $img->ruta ? 'checked' : '' }}>
            <span class="flex items-center gap-1 rounded-md bg-white/90 px-1.5 py-0.5 text-[10px] font-semibold text-gray-500 shadow-sm peer-checked:bg-red-600 peer-checked:text-white">
                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Portada
            </span>
        </label>
    @endif

    <button type="button" title="Eliminar foto" aria-label="Eliminar foto"
            onclick="eliminarFoto(this, @js(route('admin.productos.imagenes.destroy', [$producto, $img])))"
            class="absolute top-1 right-1 rounded-md bg-white/90 p-1 text-gray-500 shadow-sm hover:bg-red-600 hover:text-white transition">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </button>
</div>
