@extends('layouts.app')
@section('title', 'Editar Categoría')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.categorias.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a categorías</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar categoría</h1>
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

    <form method="POST" action="{{ route('admin.categorias.update', $categoria) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="descripcion" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="estado" id="estado" value="1"
                   {{ old('estado', $categoria->estado) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-red-600 focus:ring-red-400">
            <label for="estado" class="text-sm text-gray-700">Categoría activa</label>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar categoría
        </button>
    </form>
</div>

@endsection
