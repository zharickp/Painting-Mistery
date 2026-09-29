@extends('layouts.app')
@section('title', 'Editar Producto')
@section('content')

@php
    $fotosCompartidas = $producto->imagenes->whereNull('producto_color_id')->values();
@endphp

<div class="mb-6">
    <a href="{{ route('admin.productos.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a productos</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar producto</h1>
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

    <form method="POST" action="{{ route('admin.productos.update', $producto) }}"
          enctype="multipart/form-data" class="space-y-5" id="formProducto">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre"
                   value="{{ old('nombre', $producto->nombre) }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="descripcion" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                <input type="number" name="precio"
                       value="{{ old('precio', $producto->precio) }}"
                       min="0" step="0.01" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Precio antes del descuento <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="number" name="precio_anterior"
                       value="{{ old('precio_anterior', $producto->precio_anterior) }}"
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
                    <option value="{{ $iva->id }}"
                        {{ old('tipo_iva_id', $producto->tipo_iva_id) == $iva->id ? 'selected' : '' }}>
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
                    <option value="{{ $categoria->id }}"
                        {{ old('categoria_producto_id', $producto->categoria_producto_id) == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Imagen principal <span class="text-gray-400">(opcional, máx. 8MB)</span>
            </label>
            @if ($producto->imagen)
                <div class="mb-3">
                    <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}"
                         class="h-32 w-32 object-cover rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-400 mt-1">Portada actual. Puedes subir una foto nueva como portada o marcar "Portada" en una de las fotos generales.</p>
                </div>
            @endif
            <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewImagenPrincipal"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <div id="previewImagenPrincipal" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        {{-- ── FOTOS ─────────────────────────────────────────────── --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-sm font-semibold text-gray-800">Fotos del producto</p>
            <p class="text-xs text-gray-400 mt-0.5 mb-4">
                El botón <span class="font-semibold text-red-600">Eliminar</span> borra la foto de inmediato.
                Las fotos nuevas, el orden (arrastrando) y la portada se guardan con <span class="font-semibold">Actualizar producto</span>.
            </p>

            <div id="avisoFotos" class="hidden mb-3 text-xs rounded-lg px-3 py-2"></div>

            {{-- Fotos generales (sin color) --}}
            <div class="grupo-fotos border border-gray-200 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Fotos generales</p>
                        <p class="text-[11px] text-gray-400">Se muestran cuando el cliente no ha elegido un color.</p>
                    </div>
                </div>

                <div class="grilla-fotos grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach ($fotosCompartidas as $img)
                        @include('admin.productos._foto', ['img' => $img, 'conPortada' => true])
                    @endforeach
                </div>
                <p class="vacio-fotos text-xs text-gray-400 {{ $fotosCompartidas->isEmpty() ? '' : 'hidden' }}">Sin fotos generales.</p>

                <label class="mt-3 block">
                    <span class="text-xs font-medium text-gray-600">Agregar fotos generales</span>
                    <input type="file" name="imagenes[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewGaleria"
                           class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
                </label>
                <div id="previewGaleria" class="flex flex-wrap gap-2 mt-2"></div>
            </div>

            {{-- Colores existentes --}}
            <p class="text-sm font-semibold text-gray-700">Colores</p>
            <p class="text-[11px] text-gray-400 mb-2">Cada color aparece en la tienda como un círculo con sus propias fotos. El stock se maneja en Inventario.</p>

            <div id="coloresExistentes" class="space-y-4">
                @foreach ($producto->colores as $color)
                    <div class="grupo-fotos tarjeta-color border border-gray-200 rounded-xl p-4" data-color-id="{{ $color->id }}">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <input type="color" name="colores_existentes[{{ $color->id }}][hex]" value="{{ $color->hex ?: '#dc2626' }}"
                                   class="h-8 w-8 rounded border border-gray-200 cursor-pointer flex-shrink-0" title="Tono del círculo en la tienda">
                            <input type="text" name="colores_existentes[{{ $color->id }}][nombre]" value="{{ $color->nombre }}" maxlength="40"
                                   class="flex-1 min-w-[8rem] rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700 focus:outline-none focus:border-red-400">
                            <button type="button"
                                    onclick="eliminarColor(this, @js(route('admin.productos.colores.destroy', [$producto, $color])), @js($color->nombre), {{ $color->imagenes->count() }})"
                                    class="text-xs font-medium text-red-600 border border-red-200 rounded-lg px-2.5 py-1.5 hover:bg-red-50 transition">
                                Eliminar color
                            </button>
                        </div>

                        <div class="grilla-fotos grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @foreach ($color->imagenes as $img)
                                @include('admin.productos._foto', ['img' => $img, 'conPortada' => false])
                            @endforeach
                        </div>
                        <p class="vacio-fotos text-xs text-gray-400 {{ $color->imagenes->isEmpty() ? '' : 'hidden' }}">Este color no tiene fotos.</p>

                        <label class="mt-3 block">
                            <span class="text-xs font-medium text-gray-600">Agregar fotos a "{{ $color->nombre }}"</span>
                            <input type="file" name="fotos_color[{{ $color->id }}][]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
                                   data-preview="previewColor{{ $color->id }}"
                                   class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
                        </label>
                        <div id="previewColor{{ $color->id }}" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>
                @endforeach
            </div>
            <p id="sinColores" class="text-xs text-gray-400 {{ $producto->colores->isEmpty() ? '' : 'hidden' }}">Este producto no tiene colores. Si viene en varios, agrégalos abajo.</p>

            <div id="ordenImagenesContainer"></div>

            {{-- Colores nuevos --}}
            <div id="gruposColorNuevos" class="mt-4 space-y-3"></div>
            <button type="button" onclick="agregarGrupoColor()"
                    class="mt-3 text-sm font-medium text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition inline-flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar color
            </button>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar producto
        </button>
    </form>
</div>

<script>
// ── Arrastre genérico (galería agrupada por color) ──
function habilitarArrastre(contenedor, selectorItem, alSoltar) {
    if (!contenedor) return;
    let arrastrando = null;
    contenedor.querySelectorAll(selectorItem).forEach(item => {
        item.setAttribute('draggable', 'true');
        item.ondragstart = () => { arrastrando = item; item.classList.add('opacity-40'); };
        item.ondragend = () => { item.classList.remove('opacity-40'); arrastrando = null; if (alSoltar) alSoltar(); };
        item.ondragover = (e) => {
            e.preventDefault();
            if (!arrastrando || arrastrando === item || item.parentElement !== arrastrando.parentElement) return;
            const rect = item.getBoundingClientRect();
            const vertical = contenedor.dataset.orientacion === 'vertical';
            const after = vertical ? (e.clientY - rect.top) > rect.height / 2 : (e.clientX - rect.left) > rect.width / 2;
            item.parentElement.insertBefore(arrastrando, after ? item.nextSibling : item);
        };
    });
}

function sincronizarOrden() {
    const contenedor = document.getElementById('ordenImagenesContainer');
    if (!contenedor) return;
    contenedor.innerHTML = '';
    document.querySelectorAll('.foto-card[data-id]').forEach(card => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'orden_imagenes[]';
        input.value = card.dataset.id;
        contenedor.appendChild(input);
    });
}

// ── Eliminación inmediata de fotos y colores ──
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function mostrarAviso(texto, tipo = 'ok') {
    const aviso = document.getElementById('avisoFotos');
    aviso.textContent = texto;
    aviso.className = 'mb-3 text-xs rounded-lg px-3 py-2 ' + (tipo === 'ok'
        ? 'bg-green-50 text-green-700 border border-green-200'
        : 'bg-red-50 text-red-700 border border-red-200');
    clearTimeout(window.__avisoFotosTimer);
    window.__avisoFotosTimer = setTimeout(() => aviso.classList.add('hidden'), 5000);
}

async function pedirEliminacion(url) {
    const resp = await fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    if (!resp.ok) throw new Error('HTTP ' + resp.status);
    return resp.json();
}

function actualizarVacios(grupo) {
    if (!grupo) return;
    const vacio = grupo.querySelector('.vacio-fotos');
    if (vacio) vacio.classList.toggle('hidden', grupo.querySelectorAll('.foto-card').length > 0);
}

function quitarTarjetaColor(tarjeta) {
    tarjeta?.remove();
    if (!document.querySelector('.tarjeta-color')) {
        document.getElementById('sinColores')?.classList.remove('hidden');
    }
}

async function eliminarFoto(boton, url) {
    if (!confirm('¿Eliminar esta foto? Se borra de inmediato.')) return;
    const card = boton.closest('.foto-card');
    const grupo = boton.closest('.grupo-fotos');
    boton.disabled = true;
    card.classList.add('opacity-40');
    try {
        const data = await pedirEliminacion(url);
        card.remove();
        actualizarVacios(grupo);
        if (data.color_eliminado) {
            quitarTarjetaColor(grupo);
            mostrarAviso('Foto eliminada. El color quedó sin fotos, así que también se eliminó.');
        } else {
            mostrarAviso('Foto eliminada.');
        }
        sincronizarOrden();
    } catch (e) {
        card.classList.remove('opacity-40');
        boton.disabled = false;
        mostrarAviso('No se pudo eliminar la foto. Recarga la página e inténtalo de nuevo.', 'error');
    }
}

async function eliminarColor(boton, url, nombre, cantidad) {
    const detalle = cantidad > 0 ? ` y sus ${cantidad} foto(s)` : '';
    if (!confirm(`¿Eliminar el color "${nombre}"${detalle}? Se borra de inmediato.`)) return;
    const tarjeta = boton.closest('.tarjeta-color');
    boton.disabled = true;
    tarjeta.classList.add('opacity-40');
    try {
        await pedirEliminacion(url);
        quitarTarjetaColor(tarjeta);
        mostrarAviso(`Color "${nombre}" eliminado.`);
        sincronizarOrden();
    } catch (e) {
        tarjeta.classList.remove('opacity-40');
        boton.disabled = false;
        mostrarAviso('No se pudo eliminar el color. Recarga la página e inténtalo de nuevo.', 'error');
    }
}

// ── Grupos de color nuevos (subir varias fotos de un color en un solo paso) ──
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

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.grupo-fotos').forEach(grupo => habilitarArrastre(grupo, '.foto-card', sincronizarOrden));
    sincronizarOrden();

    document.getElementById('formProducto')?.addEventListener('submit', () => {
        sincronizarOrden();
    });
});
</script>

<template id="plantillaGrupoColor">
<div class="grupo-color-nuevo border border-dashed border-red-200 rounded-xl p-4 bg-red-50/30">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-gray-600">Color nuevo <span class="font-normal text-gray-400">(se crea al guardar)</span></span>
        <button type="button" onclick="this.closest('.grupo-color-nuevo').remove()" class="text-xs font-medium text-gray-500 hover:text-red-600">Quitar</button>
    </div>
    <div class="flex items-center gap-2 mb-3">
        <input type="color" name="grupos_color[__CLAVE__][hex]" value="#dc2626" class="h-8 w-8 rounded border border-gray-200 cursor-pointer flex-shrink-0" title="Tono del círculo en la tienda">
        <input type="text" name="grupos_color[__CLAVE__][nombre]" placeholder="Nombre del color (ej. Tornasol)" maxlength="40"
               class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:border-red-400">
    </div>
    <span class="text-xs font-medium text-gray-600">Fotos de este color</span>
    <input type="file" name="grupos_color[__CLAVE__][archivos][]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
           data-preview="previewGrupo__CLAVE__"
           class="mt-1 w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
    <div id="previewGrupo__CLAVE__" class="flex flex-wrap gap-2 mt-2"></div>
    <p class="text-[11px] text-gray-400 mt-2">Si no subes fotos, el color no se crea.</p>
</div>
</template>

@endsection
