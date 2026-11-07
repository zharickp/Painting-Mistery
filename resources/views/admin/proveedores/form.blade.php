@extends('layouts.app')
@section('title', $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor')
@section('content')
@php $campo = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400'; @endphp

<div class="mb-6">
    <a href="{{ route('admin.proveedores.index') }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a proveedores</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">{{ $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor' }}</h1>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $proveedor->exists ? route('admin.proveedores.update', $proveedor) : route('admin.proveedores.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @if ($proveedor->exists) @method('PUT') @endif

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required maxlength="120" class="{{ $campo }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIT <span class="text-gray-400">(opcional)</span></label>
            <input type="text" name="nit" value="{{ old('nit', $proveedor->nit) }}" maxlength="30" class="{{ $campo }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Persona de contacto <span class="text-gray-400">(opcional)</span></label>
            <input type="text" name="persona_contacto" value="{{ old('persona_contacto', $proveedor->persona_contacto) }}" maxlength="120" class="{{ $campo }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-gray-400">(opcional)</span></label>
            <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" maxlength="40" class="{{ $campo }}">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Correo <span class="text-gray-400">(opcional)</span></label>
            <input type="email" name="correo" value="{{ old('correo', $proveedor->correo) }}" maxlength="120" class="{{ $campo }}">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección <span class="text-gray-400">(opcional)</span></label>
            <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}" maxlength="200" class="{{ $campo }}">
        </div>

        <div class="sm:col-span-2">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                {{ $proveedor->exists ? 'Guardar cambios' : 'Guardar proveedor' }}
            </button>
        </div>
    </form>
</div>
@endsection
