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
                       value="{{ old('precio', (float) $producto->precio) }}"
                       min="0" step="0.01" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Precio antes del descuento <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="number" name="precio_anterior"
                       value="{{ old('precio_anterior', $producto->precio_anterior !== null ? (float) $producto->precio_anterior : '') }}"
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
                        {{ $iva->nombre }}@unless (str_contains($iva->nombre, '%')) ({{ (float) $iva->porcentaje }}%)@endunless
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

        {{-- ── FOTOS ─────────────────────────────────────────────── --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-sm font-semibold text-gray-800">Fotos</p>
            <p class="text-xs text-gray-400 mb-3">Arrastra para cambiar el orden.</p>

            <div id="avisoFotos" class="hidden mb-3 text-xs rounded-lg px-3 py-2"></div>

            <div class="grupo-fotos">
                <div class="grilla-fotos grid grid-cols-4 sm:grid-cols-6 gap-2 mb-2">
                    @foreach ($fotosCompartidas as $img)
                        @include('admin.productos._foto', ['img' => $img, 'conPortada' => true])
                    @endforeach
                </div>
                <p class="vacio-fotos text-xs text-gray-400 mb-2 {{ $fotosCompartidas->isEmpty() ? '' : 'hidden' }}">Aún no tiene fotos.</p>
            </div>
            @include('admin.productos._subir-fotos', ['nombre' => 'imagenes[]', 'id' => 'fotosGenerales', 'texto' => 'Agregar fotos'])

            {{-- Colores --}}
            <div class="mt-6 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Colores <span class="font-normal text-gray-400">(opcional)</span></p>
                </div>
                <button type="button" onclick="agregarGrupoColor()"
                        class="shrink-0 text-sm font-medium text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Agregar color
                </button>
            </div>

            <div id="coloresExistentes" class="mt-3 space-y-3">
                @foreach ($producto->colores as $color)
                    <div class="grupo-fotos tarjeta-color rounded-xl border border-gray-200 p-4" data-color-id="{{ $color->id }}">
                        <div class="flex items-center gap-2 mb-3">
                            <input type="color" name="colores_existentes[{{ $color->id }}][hex]" value="{{ $color->codigo_color ?: '#dc2626' }}"
                                   class="h-9 w-9 rounded-lg border border-gray-200 cursor-pointer flex-shrink-0" title="Tono del círculo en la tienda">
                            <input type="text" name="colores_existentes[{{ $color->id }}][nombre]" value="{{ $color->nombre }}" maxlength="40"
                                   class="flex-1 min-w-[8rem] rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 focus:outline-none focus:border-red-400">
                            <button type="button" title="Eliminar color" aria-label="Eliminar color"
                                    onclick="eliminarColor(this, @js(route('admin.productos.colores.destroy', [$producto, $color])), @js($color->nombre), {{ $color->imagenes->count() }})"
                                    class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        <div class="grilla-fotos grid grid-cols-4 sm:grid-cols-6 gap-2 mb-2">
                            @foreach ($color->imagenes as $img)
                                @include('admin.productos._foto', ['img' => $img, 'conPortada' => false])
                            @endforeach
                        </div>
                        <p class="vacio-fotos text-xs text-gray-400 mb-2 {{ $color->imagenes->isEmpty() ? '' : 'hidden' }}">Este color no tiene fotos.</p>

                        @include('admin.productos._subir-fotos', ['nombre' => 'fotos_color[' . $color->id . '][]', 'id' => 'fotosColor' . $color->id, 'texto' => 'Agregar fotos de ' . $color->nombre])
                    </div>
                @endforeach
            </div>
            <p id="sinColores" class="hidden"></p>

            <div id="ordenImagenesContainer"></div>
            <div id="gruposColorNuevos" class="mt-3 space-y-3"></div>

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

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.grupo-fotos').forEach(grupo => habilitarArrastre(grupo, '.foto-card', sincronizarOrden));
    sincronizarOrden();

    document.getElementById('formProducto')?.addEventListener('submit', () => {
        sincronizarOrden();
    });
});
</script>

@include('admin.productos._color-nuevo')

@endsection
