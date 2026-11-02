@extends('layouts.app')
@section('title', 'Nuevo Usuario')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.usuarios.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a usuarios</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Nuevo usuario</h1>
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

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primer nombre *</label>
                <input type="text" name="primer_nombre" value="{{ old('primer_nombre') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Segundo nombre <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="segundo_nombre" value="{{ old('segundo_nombre') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primer apellido *</label>
                <input type="text" name="primer_apellido" value="{{ old('primer_apellido') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Segundo apellido <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="segundo_apellido" value="{{ old('segundo_apellido') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de documento *</label>
                <select name="tipo_documento_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <option value="">Selecciona...</option>
                    @foreach($tiposDocumento as $td)
                        <option value="{{ $td->id }}" {{ old('tipo_documento_id') == $td->id ? 'selected' : '' }}>
                            {{ $td->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de documento *</label>
                <input type="text" name="numero_documento" value="{{ old('numero_documento') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo *</label>
                <input type="email" name="correo" value="{{ old('correo') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Teléfono <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="telefono" value="{{ old('telefono') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                <select name="genero"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <option value="">Sin especificar</option>
                    <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>Femenino</option>
                    <option value="O" {{ old('genero') === 'O' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                <select name="rol_id" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <option value="">Selecciona un rol...</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                            {{ $rol->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <button type="button" onclick="togglePass('password', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña *</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                    <button type="button" onclick="togglePass('password_confirmation', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Crear usuario
        </button>
    </form>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const mostrar = input.type === 'password';
    input.type = mostrar ? 'text' : 'password';
    btn.querySelector('.icono-ver').classList.toggle('hidden', mostrar);
    btn.querySelector('.icono-ocultar').classList.toggle('hidden', !mostrar);
    btn.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
    btn.title = btn.getAttribute('aria-label');
}
</script>
@endsection
