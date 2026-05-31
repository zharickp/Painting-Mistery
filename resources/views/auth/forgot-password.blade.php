@extends('layouts.guest')

@section('title', 'Olvidé mi contraseña')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <div class="bg-white/20 rounded-full p-3">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Recuperar contraseña</h2>
        </div>

        <div class="p-6 sm:p-8">

            @if (session('success'))
                <div class="mb-4 bg-green-50 text-green-700 p-3 rounded-md border-l-4 border-green-500 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <p class="text-gray-500 text-sm mb-6 text-center">
                Ingresa tu correo registrado y te enviaremos un código para restablecer tu contraseña.
            </p>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-3 rounded-md border-l-4 border-red-500 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.sendCode') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </div>
                        <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="usuario@ejemplo.com">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition text-sm">
                    Enviar código
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    ← Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
