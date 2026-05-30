@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-lg w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Header con logo -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
                 alt="Painting Mistery"
                 class="h-16 w-16 rounded-full object-cover border-2 border-white shadow">
            <h2 class="text-xl font-bold text-white">Crear cuenta</h2>
        </div>

        <div class="p-6 sm:p-8">

            <!-- Errores -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-md border-l-4 border-red-600">
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="primer_nombre" class="block text-sm font-medium text-gray-700 mb-1">Primer nombre</label>
                        <input id="primer_nombre" name="primer_nombre" type="text" value="{{ old('primer_nombre') }}" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="primer_apellido" class="block text-sm font-medium text-gray-700 mb-1">Primer apellido</label>
                        <input id="primer_apellido" name="primer_apellido" type="text" value="{{ old('primer_apellido') }}" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="segundo_nombre" class="block text-sm font-medium text-gray-700 mb-1">Segundo nombre <span class="text-gray-400">(opcional)</span></label>
                        <input id="segundo_nombre" name="segundo_nombre" type="text" value="{{ old('segundo_nombre') }}"
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="segundo_apellido" class="block text-sm font-medium text-gray-700 mb-1">Segundo apellido <span class="text-gray-400">(opcional)</span></label>
                        <input id="segundo_apellido" name="segundo_apellido" type="text" value="{{ old('segundo_apellido') }}"
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="genero" class="block text-sm font-medium text-gray-700 mb-1">Género <span class="text-gray-400">(opcional)</span></label>
                    <select id="genero" name="genero"
                            class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Prefiero no decir</option>
                        <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                        <option value="O" {{ old('genero') == 'O' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tipo_documento_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de documento</label>
                        <select id="tipo_documento_id" name="tipo_documento_id" required
                                class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Selecciona...</option>
                            @foreach ($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_documento_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-1">Número de documento</label>
                        <input id="numero_documento" name="numero_documento" type="text" value="{{ old('numero_documento') }}" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required
                           class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-gray-400">(opcional)</span></label>
                    <input id="telefono" name="telefono" type="text" value="{{ old('telefono') }}"
                           class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input id="password" name="password" type="password" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    Crear cuenta
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">¿Ya tienes una cuenta?</p>
                <a href="{{ route('login') }}"
                   class="mt-2 inline-block border border-red-600 text-red-600 hover:bg-red-50 font-medium rounded-md px-5 py-2 transition text-sm">
                    Inicia sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
