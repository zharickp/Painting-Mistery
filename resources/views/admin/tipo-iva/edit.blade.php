@extends('layouts.app')
@section('title', 'Editar Tipo de IVA')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.tipo-iva.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a tipos de IVA</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar tipo de IVA</h1>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-lg">
    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tipo-iva.update', $tipoIva) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <input type="text" name="descripcion"
                   value="{{ old('descripcion', $tipoIva->descripcion) }}"
                   required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Porcentaje (%)</label>
            <input type="number" name="porcentaje"
                   value="{{ old('porcentaje', $tipoIva->porcentaje) }}"
                   min="0" max="100" step="0.01"
                   required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar tipo de IVA
        </button>
    </form>
</div>

@endsection
