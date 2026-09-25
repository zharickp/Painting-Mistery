{{-- Insignia única del estado de una orden. Uso: @include('partials.estado-orden', ['venta' => $venta]) --}}
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $venta->estadoColor() }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $venta->estadoPunto() }}"></span>
    {{ $venta->estadoEtiqueta() }}
</span>
