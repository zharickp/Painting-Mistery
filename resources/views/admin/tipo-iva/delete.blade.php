@extends('layouts.app')
@section('title', 'Eliminar Tipo de IVA')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.tipo-iva.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a tipos de IVA</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Eliminar tipo de IVA</h1>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-lg">
    <div class="flex items-start gap-4 mb-6">
        <div class="h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h2 class="font-semibold text-gray-800 mb-1">
                ¿Eliminar "{{ $tipoIva->descripcion }}" ({{ $tipoIva->porcentaje }}%)?
            </h2>
            <p class="text-sm text-gray-500">
                Esta acción no se puede deshacer.
                @if($tipoIva->productos_count > 0)
                    <span class="text-red-600 font-medium">
                        Este tipo de IVA tiene {{ $tipoIva->productos_count }}
                        {{ $tipoIva->productos_count === 1 ? 'producto asociado' : 'productos asociados' }}
                        y no puede eliminarse.
                    </span>
                @else
                    El tipo de IVA será eliminado permanentemente.
                @endif
            </p>
        </div>
    </div>

    @if($tipoIva->productos_count > 0)
        <a href="{{ route('admin.tipo-iva.index') }}"
           class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition">
            Volver al listado
        </a>
    @else
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('admin.tipo-iva.destroy', $tipoIva) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Sí, eliminar
                </button>
            </form>
            <a href="{{ route('admin.tipo-iva.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition">
                Cancelar
            </a>
        </div>
    @endif
</div>

@endsection
