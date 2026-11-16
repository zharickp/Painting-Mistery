{{-- Zona para subir fotos: se hace clic o se arrastran las fotos encima.
     Variables: $nombre (name del input), $id (único), $texto (opcional), $marcarPortada (opcional: la 1.ª se marca como portada) --}}
<div class="zona-fotos" data-portada="{{ !empty($marcarPortada) ? '1' : '0' }}">
    <label for="{{ $id }}"
           class="zona-fotos-area flex flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/60 px-4 py-5 text-center cursor-pointer hover:border-red-400 hover:bg-red-50/40 transition">
        <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="text-sm text-gray-600"><span class="font-semibold text-red-600">{{ $texto ?? 'Elegir fotos' }}</span> o arrástralas aquí</span>
        <span class="zona-fotos-estado text-xs text-gray-400">JPG, PNG o WEBP · máx. 8 MB cada una</span>
    </label>
    <input type="file" id="{{ $id }}" name="{{ $nombre }}" multiple accept="image/jpg,image/jpeg,image/png,image/webp" class="sr-only">
    <div class="zona-fotos-vista grid grid-cols-4 sm:grid-cols-6 gap-2 mt-2 empty:hidden"></div>
</div>

@once
<script>
// Zonas de fotos: vista previa, arrastrar y soltar, y "Portada" en la primera cuando aplica.
window.pmZonaFotos = function (zona) {
    if (!zona || zona.dataset.lista) return;
    zona.dataset.lista = '1';
    const input  = zona.querySelector('input[type=file]');
    const area   = zona.querySelector('.zona-fotos-area');
    const vista  = zona.querySelector('.zona-fotos-vista');
    const estado = zona.querySelector('.zona-fotos-estado');
    const textoInicial = estado.textContent;

    function pintar() {
        vista.innerHTML = '';
        const archivos = Array.from(input.files || []).filter(f => f.type.startsWith('image/'));
        estado.textContent = archivos.length ? archivos.length + ' foto(s) lista(s) para guardar' : textoInicial;
        estado.classList.toggle('text-green-600', archivos.length > 0);
        archivos.forEach((archivo, i) => {
            const caja = document.createElement('div');
            caja.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200';
            caja.innerHTML = '<img class="h-full w-full object-cover" alt="">'
                + (zona.dataset.portada === '1' && i === 0 ? '<span class="absolute bottom-1 left-1 rounded bg-red-600 px-1.5 py-0.5 text-[10px] font-semibold text-white">Portada</span>' : '');
            caja.querySelector('img').src = URL.createObjectURL(archivo);
            vista.appendChild(caja);
        });
    }

    input.addEventListener('change', pintar);
    ['dragenter', 'dragover'].forEach(ev => area.addEventListener(ev, e => { e.preventDefault(); area.classList.add('border-red-400', 'bg-red-50/40'); }));
    ['dragleave', 'drop'].forEach(ev => area.addEventListener(ev, e => { e.preventDefault(); area.classList.remove('border-red-400', 'bg-red-50/40'); }));
    area.addEventListener('drop', e => {
        if (!e.dataTransfer?.files?.length) return;
        input.files = e.dataTransfer.files;
        pintar();
    });
};
document.addEventListener('DOMContentLoaded', () => document.querySelectorAll('.zona-fotos').forEach(window.pmZonaFotos));
</script>
@endonce
