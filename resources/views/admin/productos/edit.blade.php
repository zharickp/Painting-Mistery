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
                    Precio anterior <span class="text-gray-400">(opcional, para mostrar descuento)</span>
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
                    <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla, o marca "Portada" en una foto de la galería.</p>
                </div>
            @endif
            <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewImagenPrincipal"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <div id="previewImagenPrincipal" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Galería de fotos</label>

            @if ($producto->colores->isNotEmpty() || $fotosCompartidas->isNotEmpty())
                <p class="text-xs text-gray-400 mb-3">Arrastra las fotos para reordenarlas dentro de su grupo, ajusta el stock de cada color, o táchalas para eliminarlas al guardar.</p>
                <div id="gruposFotosExistentes" class="space-y-4 mb-4">
                    @foreach ($producto->colores as $color)
                        <div class="grupo-fotos border border-gray-200 rounded-xl p-3">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <input type="color" name="colores_existentes[{{ $color->id }}][hex]" value="{{ $color->hex ?: '#dc2626' }}"
                                       class="h-7 w-7 rounded border border-gray-200 cursor-pointer flex-shrink-0" title="Color de esta variante">
                                <input type="text" name="colores_existentes[{{ $color->id }}][nombre]" value="{{ $color->nombre }}" maxlength="40"
                                       class="flex-1 min-w-[8rem] rounded-md border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-700 focus:outline-none focus:border-red-400">
                                <label class="flex items-center gap-1.5 text-xs text-gray-500 flex-shrink-0">
                                    Stock
                                    <input type="number" name="colores_existentes[{{ $color->id }}][stock]" value="{{ $color->stock }}" min="0"
                                           class="w-20 rounded-md border border-gray-200 px-2 py-1 text-xs focus:outline-none focus:border-red-400">
                                </label>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach ($color->imagenes as $img)
                                    <div class="foto-card border border-gray-200 rounded-lg p-2 bg-white cursor-move" data-id="{{ $img->id }}">
                                        <div class="relative">
                                            <img src="{{ $img->ruta }}" draggable="false" class="h-20 w-20 object-cover rounded-lg mx-auto pointer-events-none">
                                            <label class="absolute -top-2 -right-2 bg-white rounded-full shadow border border-gray-200 h-6 w-6 flex items-center justify-center cursor-pointer" title="Eliminar">
                                                <input type="checkbox" name="eliminar_imagenes[]" value="{{ $img->id }}" class="accent-red-600">
                                            </label>
                                        </div>
                                        <p class="text-[10px] text-gray-400 text-center mt-1.5 truncate">Foto de "{{ $color->nombre }}"</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if ($fotosCompartidas->isNotEmpty())
                        <div class="grupo-fotos border border-gray-200 rounded-xl p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Fotos compartidas (sin color específico)</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach ($fotosCompartidas as $img)
                                    <div class="foto-card border border-gray-200 rounded-lg p-2 bg-white cursor-move" data-id="{{ $img->id }}">
                                        <div class="relative">
                                            <img src="{{ $img->ruta }}" draggable="false" class="h-20 w-20 object-cover rounded-lg mx-auto pointer-events-none">
                                            <label class="absolute -top-2 -right-2 bg-white rounded-full shadow border border-gray-200 h-6 w-6 flex items-center justify-center cursor-pointer" title="Eliminar">
                                                <input type="checkbox" name="eliminar_imagenes[]" value="{{ $img->id }}" class="accent-red-600">
                                            </label>
                                        </div>
                                        <label class="flex items-center gap-1 mt-1.5 text-[11px] text-gray-500 cursor-pointer">
                                            <input type="radio" name="imagen_portada" value="{{ $img->id }}" class="accent-red-600"
                                                   {{ $producto->imagen === $img->ruta ? 'checked' : '' }}>
                                            Portada
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div id="ordenImagenesContainer"></div>
            @endif

            <div class="border-t border-gray-100 pt-4 mt-1">
                <p class="text-sm font-semibold text-gray-700 mb-2">Agregar fotos nuevas</p>

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fotos sin color específico</label>
                    <input type="file" name="imagenes[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewGaleria"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
                    <div id="previewGaleria" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div id="gruposColorNuevos" class="mb-2 space-y-3"></div>

                <button type="button" onclick="agregarGrupoColor()"
                        class="text-sm font-medium text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Agregar grupo de color
                </button>
                <p class="text-xs text-gray-400 mt-1.5">Si tu producto viene en varios colores, crea un grupo por cada color, define su stock y sube sus fotos ahí — aparecerán como círculos seleccionables en la página del producto, cada uno con su propia galería y su propio stock. Si escribes el nombre de un color que ya existe, las fotos nuevas se agregan a ese mismo color (el stock que pongas aquí solo aplica al crear el color).</p>
            </div>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar producto
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl mt-6">
    <p class="text-sm font-semibold text-gray-700 mb-1">Productos recomendados</p>
    <p class="text-xs text-gray-400 mb-3">Elige qué productos aparecen en "También te puede interesar" en la página de este producto. Si no eliges ninguno, se muestran automáticamente productos de la misma categoría.</p>

    <div class="relative mb-3">
        <input type="text" id="buscarRelacionado" placeholder="Buscar producto por nombre..." autocomplete="off"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        <div id="resultadosRelacionados" class="hidden absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto"></div>
    </div>

    <ul id="listaRelacionados" data-orientacion="vertical" class="space-y-2">
        @foreach ($relacionadosActuales as $rel)
            <li class="rel-item flex items-center gap-3 border border-gray-200 rounded-lg p-2 bg-gray-50 cursor-move" data-id="{{ $rel->id }}">
                <svg class="h-4 w-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                <div class="h-9 w-9 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                    @if ($rel->imagen)<img src="{{ $rel->imagen }}" draggable="false" class="h-full w-full object-cover">@endif
                </div>
                <span class="text-sm text-gray-700 flex-1 truncate">{{ $rel->nombre }}</span>
                <button type="button" onclick="quitarRelacionado(this)" class="text-gray-400 hover:text-red-600 text-sm px-1">✕</button>
            </li>
        @endforeach
    </ul>
    <p id="relacionadosVacio" class="text-xs text-gray-400 {{ $relacionadosActuales->isNotEmpty() ? 'hidden' : '' }}">Aún no has elegido recomendados manuales.</p>

    <div id="relacionadosOrdenContainer"></div>
</div>

<script>
window.productosDisponibles = @json($productosDisponibles->map(fn ($p) => ['id' => $p->id, 'nombre' => $p->nombre, 'imagen' => $p->imagen])->values());

// ── Arrastre genérico (galería agrupada por color + lista de recomendados) ──
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

// ── Recomendados manuales ──
function sincronizarRelacionados() {
    const contenedor = document.getElementById('relacionadosOrdenContainer');
    if (!contenedor) return;
    contenedor.innerHTML = '';
    document.querySelectorAll('#listaRelacionados .rel-item').forEach(li => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'relacionados[]';
        input.value = li.dataset.id;
        contenedor.appendChild(input);
    });
}

function rebindArrastreRelacionados() {
    habilitarArrastre(document.getElementById('listaRelacionados'), '.rel-item', sincronizarRelacionados);
}

function agregarRelacionado(p) {
    const lista = document.getElementById('listaRelacionados');
    const li = document.createElement('li');
    li.className = 'rel-item flex items-center gap-3 border border-gray-200 rounded-lg p-2 bg-gray-50 cursor-move';
    li.dataset.id = p.id;
    li.innerHTML = `
        <svg class="h-4 w-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
        <div class="h-9 w-9 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">${p.imagen ? `<img src="${p.imagen}" draggable="false" class="h-full w-full object-cover">` : ''}</div>
        <span class="text-sm text-gray-700 flex-1 truncate">${p.nombre}</span>
        <button type="button" onclick="quitarRelacionado(this)" class="text-gray-400 hover:text-red-600 text-sm px-1">✕</button>
    `;
    lista.appendChild(li);
    document.getElementById('buscarRelacionado').value = '';
    document.getElementById('resultadosRelacionados').classList.add('hidden');
    document.getElementById('relacionadosVacio')?.classList.add('hidden');
    rebindArrastreRelacionados();
    sincronizarRelacionados();
}

function quitarRelacionado(btn) {
    btn.closest('.rel-item').remove();
    if (!document.querySelectorAll('#listaRelacionados .rel-item').length) {
        document.getElementById('relacionadosVacio')?.classList.remove('hidden');
    }
    sincronizarRelacionados();
}

document.addEventListener('DOMContentLoaded', () => {
    const buscarInput = document.getElementById('buscarRelacionado');
    const resultadosBox = document.getElementById('resultadosRelacionados');

    buscarInput?.addEventListener('input', () => {
        const q = buscarInput.value.trim().toLowerCase();
        resultadosBox.innerHTML = '';
        if (!q) { resultadosBox.classList.add('hidden'); return; }

        const yaElegidos = new Set(Array.from(document.querySelectorAll('#listaRelacionados .rel-item')).map(li => li.dataset.id));
        const coincidencias = window.productosDisponibles
            .filter(p => p.nombre.toLowerCase().includes(q) && !yaElegidos.has(String(p.id)))
            .slice(0, 6);

        if (!coincidencias.length) { resultadosBox.classList.add('hidden'); return; }

        coincidencias.forEach(p => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full text-left px-3 py-2 text-sm hover:bg-red-50 flex items-center gap-2';
            item.innerHTML = `<div class="h-7 w-7 rounded bg-gray-100 overflow-hidden flex-shrink-0">${p.imagen ? `<img src="${p.imagen}" class="h-full w-full object-cover">` : ''}</div><span class="truncate">${p.nombre}</span>`;
            item.onclick = () => agregarRelacionado(p);
            resultadosBox.appendChild(item);
        });
        resultadosBox.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (resultadosBox && !resultadosBox.contains(e.target) && e.target !== buscarInput) {
            resultadosBox.classList.add('hidden');
        }
    });

    document.querySelectorAll('.grupo-fotos').forEach(grupo => habilitarArrastre(grupo, '.foto-card', sincronizarOrden));
    rebindArrastreRelacionados();
    sincronizarOrden();
    sincronizarRelacionados();

    document.getElementById('formProducto')?.addEventListener('submit', () => {
        sincronizarOrden();
        sincronizarRelacionados();
    });
});
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
