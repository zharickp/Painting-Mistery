@extends('layouts.app')

@section('title', 'Resultado del pago')

@section('content')
<div class="max-w-lg mx-auto text-center py-10">

    @if($estado === 'aprobado')
        <div class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Pago simulado exitosamente</h1>
        <p class="text-sm text-gray-500 mt-2">{{ $mensaje }}</p>
        @if($venta)
            <p class="text-xs text-gray-400 mt-4">
                Referencia interna: <span class="font-mono">VENTA-{{ $venta->id }}</span>
            </p>
        @endif
    @else
        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Pago simulado rechazado</h1>
        <p class="text-sm text-gray-500 mt-2">{{ $mensaje }}</p>
    @endif

    <div class="mt-8 flex items-center justify-center gap-2">
        <a href="{{ route('carrito.index') }}"
           class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
            Volver al carrito
        </a>
        <a href="{{ route('tienda.index') }}"
           class="px-4 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white">
            Seguir comprando
        </a>
    </div>

    <p class="text-xs text-gray-400 mt-8">Modo demostración — Painting Mistery</p>
</div>
@endsection
