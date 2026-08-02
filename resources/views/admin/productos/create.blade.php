@extends('layouts.app')
@section('title', 'Nuevo Producto')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.productos.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a productos</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Nuevo producto</h1>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.productos.store') }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="descripcion" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('descripcion') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                <input type="number" name="precio" value="{{ old('precio') }}"
                       min="0" step="0.01" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Precio anterior <span class="text-gray-400">(opcional, para mostrar descuento)</span>
                </label>
                <input type="number" name="precio_anterior" value="{{ old('precio_anterior') }}"
                       min="0" step="0.01"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de IVA</label>
            <select name="tipo_iva_id" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <option value="">Selecciona...</option>
                @foreach ($tiposIva as $iva)
                    <option value="{{ $iva->id }}" {{ old('tipo_iva_id') == $iva->id ? 'selected' : '' }}>
                        {{ $iva->descripcion }} ({{ $iva->porcentaje }}%)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="categoria_producto_id" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <option value="">Selecciona una categoría...</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categoria_producto_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Imagen principal <span class="text-gray-400">(opcional, máx. 8MB)</span>
            </label>
            <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewImagenPrincipal"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <p class="text-xs text-gray-400 mt-1">Es la foto de portada que aparece en el catálogo.</p>
            <div id="previewImagenPrincipal" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Galería de fotos <span class="text-gray-400">(opcional, puedes elegir varias)</span>
            </label>
            <input type="file" name="imagenes[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewGaleria"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <p class="text-xs text-gray-400 mt-1">Se muestran en el detalle del producto para que el cliente navegue entre fotos. Puedes elegir varias a la vez o repetir la acción para agregar más.</p>
            <div id="previewGaleria" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <p class="text-sm font-medium text-gray-700 mb-2">
                Variantes de color <span class="text-gray-400 font-normal">(opcional)</span>
            </p>
            <div id="gruposColorNuevos" class="mb-2 space-y-3"></div>
            <button type="button" onclick="agregarGrupoColor()"
                    class="text-sm font-medium text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition inline-flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar grupo de color
            </button>
            <p class="text-xs text-gray-400 mt-1.5">Si tu producto viene en varios colores, crea un grupo por cada color, define su stock y sube sus fotos ahí — aparecerán como círculos seleccionables en la página del producto, cada uno con su propia galería y su propio stock.</p>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Guardar producto
        </button>
    </form>
</div>

<script>
let contadorGrupoColor = 0;
function agregarGrupoColor() {
    contadorGrupoColor++;
    const clave = 'n' + contadorGrupoColor;
    const html = document.getElementById('plantillaGrupoColor').innerHTML.replaceAll('__CLAVE__', clave);
    const envoltorio = document.createElement('div');
    envoltorio.innerHTML = html.trim();
    const nodo = envoltorio.firstElementChild;
    document.getElementById('gruposColorNuevos').appendChild(nodo);
    const input = nodo.querySelector('input[type="file"]');
    if (input && window.attachFilePreview) window.attachFilePreview(input);
}
</script>

<template id="plantillaGrupoColor">
<div class="grupo-color-nuevo border border-dashed border-gray-300 rounded-xl p-3 bg-gray-50/60">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-gray-500">Nuevo grupo de color</span>
        <button type="button" onclick="this.closest('.grupo-color-nuevo').remove()" class="text-gray-400 hover:text-red-600 text-xs">Quitar ✕</button>
    </div>
    <div class="flex items-center gap-2 mb-2">
        <input type="color" name="grupos_color[__CLAVE__][hex]" value="#dc2626" class="h-8 w-8 rounded border border-gray-200 cursor-pointer flex-shrink-0">
        <input type="text" name="grupos_color[__CLAVE__][nombre]" placeholder="Nombre del color (ej. Tornasol)" maxlength="40"
               class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-red-400">
        <input type="number" name="grupos_color[__CLAVE__][stock]" placeholder="Stock" min="0" value="0"
               class="w-24 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-red-400" title="Stock disponible de este color">
    </div>
    <input type="file" name="grupos_color[__CLAVE__][archivos][]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
           data-preview="previewGrupo__CLAVE__"
           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
    <div id="previewGrupo__CLAVE__" class="flex flex-wrap gap-2 mt-2"></div>
</div>
</template>

@endsection
