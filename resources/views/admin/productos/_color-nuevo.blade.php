{{-- Plantilla de un color nuevo (crear y editar producto) y la función que la agrega. --}}
<template id="plantillaGrupoColor">
<div class="grupo-color-nuevo rounded-xl border border-gray-200 p-4">
    <div class="flex items-center gap-2 mb-3">
        <input type="color" name="grupos_color[__CLAVE__][hex]" value="#dc2626" title="Tono del círculo en la tienda"
               class="h-9 w-9 rounded-lg border border-gray-200 cursor-pointer flex-shrink-0">
        <input type="text" name="grupos_color[__CLAVE__][nombre]" placeholder="Nombre del color (ej. Rojo)" maxlength="40"
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
        <button type="button" onclick="this.closest('.grupo-color-nuevo').remove()" title="Quitar color" aria-label="Quitar color"
                class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @include('admin.productos._subir-fotos', ['nombre' => 'grupos_color[__CLAVE__][archivos][]', 'id' => 'fotosColor__CLAVE__', 'texto' => 'Fotos de este color'])
</div>
</template>

<script>
let contadorGrupoColor = 0;
function agregarGrupoColor() {
    contadorGrupoColor++;
    const html = document.getElementById('plantillaGrupoColor').innerHTML.replaceAll('__CLAVE__', 'n' + contadorGrupoColor);
    const envoltorio = document.createElement('div');
    envoltorio.innerHTML = html.trim();
    const nodo = envoltorio.firstElementChild;
    document.getElementById('gruposColorNuevos').appendChild(nodo);
    window.pmZonaFotos(nodo.querySelector('.zona-fotos'));
    nodo.querySelector('input[type=text]').focus();
}
</script>
