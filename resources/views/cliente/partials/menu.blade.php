@php
    $tabs = [
        ['mi-cuenta.inicio',  'Resumen',     'mi-cuenta.inicio',   'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['mi-cuenta.pedidos', 'Mis pedidos', 'mi-cuenta.pedido*',  'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['mi-cuenta.cursos',  'Mis cursos',  'mi-cuenta.cursos',   'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
        ['mi-cuenta.perfil',  'Mi perfil',   'mi-cuenta.perfil',   'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
@endphp
<nav class="mb-6 -mx-1 flex gap-1 overflow-x-auto pb-1" aria-label="Mi cuenta">
    @foreach($tabs as [$ruta, $label, $patron, $icono])
        @php $activa = request()->routeIs($patron); @endphp
        <a href="{{ route($ruta) }}"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition {{ $activa ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:border-red-300 hover:text-red-600' }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icono }}"/></svg>
            {{ $label }}
        </a>
    @endforeach
    <a href="{{ route('tienda.index') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:border-red-300 hover:text-red-600 transition ml-auto">
        Ir a la tienda →
    </a>
</nav>
