{{-- Avisos flotantes arriba a la derecha.
     Éxito: se oculta solo a los 6 segundos. Error: se queda hasta que se cierre,
     para que no se pierda lo que salió mal. --}}
@php
    $avisos = array_filter([
        'error' => session('error'),
        'exito' => session('success') ?? session('resena_ok'),
    ]);
@endphp
@if ($avisos)
    <div id="pm-avisos" style="position:fixed;top:5rem;right:1rem;z-index:9999;width:calc(100% - 2rem);max-width:24rem;display:flex;flex-direction:column;gap:.5rem;">
        @foreach ($avisos as $tipo => $texto)
            <div class="pm-aviso" data-tipo="{{ $tipo }}" role="{{ $tipo === 'error' ? 'alert' : 'status' }}"
                 style="display:flex;align-items:flex-start;gap:.75rem;padding:.8rem 1rem;border-radius:.75rem;background:#0f172a;color:#f8fafc;border:1px solid #334155;box-shadow:0 10px 25px -5px rgba(0,0,0,.35);font-size:.875rem;line-height:1.35rem;transition:opacity .3s ease,transform .3s ease;">
                @if ($tipo === 'error')
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width:1.15rem;height:1.15rem;flex-shrink:0;margin-top:.1rem;color:#f87171">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="width:1.15rem;height:1.15rem;flex-shrink:0;margin-top:.1rem;color:#4ade80">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span style="flex:1">{{ $texto }}</span>
                <button type="button" aria-label="Cerrar" onclick="pmCerrarAviso(this.parentNode)"
                        style="flex-shrink:0;background:none;border:0;padding:0 .1rem;color:#94a3b8;font-size:1.1rem;line-height:1.2rem;cursor:pointer">&times;</button>
            </div>
        @endforeach
    </div>
    <style>@media print { #pm-avisos { display: none !important; } }</style>
    <script>
        function pmCerrarAviso(a) {
            if (!a || !a.parentNode) return;
            a.style.opacity = '0';
            a.style.transform = 'translateY(-6px)';
            setTimeout(function () { a.remove(); }, 300);
        }
        document.querySelectorAll('.pm-aviso[data-tipo="exito"]').forEach(function (a) {
            setTimeout(function () { pmCerrarAviso(a); }, 6000);
        });
    </script>
@endif
