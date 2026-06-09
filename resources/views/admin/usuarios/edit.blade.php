@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.usuarios.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a usuarios</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar usuario</h1>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info no editable --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Información del sistema</p>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-400">Correo:</span>
                <span class="font-medium text-gray-700 ml-1">{{ $usuario->correo }}</span>
            </div>
            <div>
                <span class="text-gray-400">Documento:</span>
                <span class="font-medium text-gray-700 ml-1">{{ $usuario->tipoDocumento->nombre ?? '' }} {{ $usuario->numero_documento }}</span>
            </div>
            <div>
                <span class="text-gray-400">Roles:</span>
                @foreach($usuario->roles as $r)
                    <span class="ml-1 px-2 py-0.5 bg-red-50 text-red-600 text-xs rounded-full">{{ $r->nombre }}</span>
                @endforeach
            </div>
            <div>
                <span class="text-gray-400">Verificado:</span>
                <span class="ml-1 text-xs {{ $usuario->correo_verificado_at ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ $usuario->correo_verificado_at ? '✅ Sí' : '⏳ No' }}
                </span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primer nombre *</label>
                <input type="text" name="primer_nombre"
                       value="{{ old('primer_nombre', $usuario->primer_nombre) }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Segundo nombre <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="segundo_nombre"
                       value="{{ old('segundo_nombre', $usuario->segundo_nombre) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primer apellido *</label>
                <input type="text" name="primer_apellido"
                       value="{{ old('primer_apellido', $usuario->primer_apellido) }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Segundo apellido <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="segundo_apellido"
                       value="{{ old('segundo_apellido', $usuario->segundo_apellido) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Teléfono <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="telefono"
                       value="{{ old('telefono', $usuario->telefono) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                <select name="genero"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <option value="">Sin especificar</option>
                    <option value="M" {{ old('genero', $usuario->genero) === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero', $usuario->genero) === 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="O" {{ old('genero', $usuario->genero) === 'O' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar usuario
        </button>
    </form>
</div>

@endsection
