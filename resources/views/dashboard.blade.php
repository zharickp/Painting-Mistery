@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">
            Bienvenido, {{ auth()->user()->primer_nombre }} {{ auth()->user()->primer_apellido }} 👋
        </h1>
        <p class="text-gray-500 mt-1">Panel de gestión de Painting Mistery.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="bg-red-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Productos</p>
                <p class="text-xl font-bold text-gray-800">—</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="bg-red-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Cursos</p>
                <p class="text-xl font-bold text-gray-800">—</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="bg-red-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ventas del mes</p>
                <p class="text-xl font-bold text-gray-800">—</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
            <div class="bg-red-100 rounded-lg p-3">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Stock bajo</p>
                <p class="text-xl font-bold text-gray-800">—</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Tu cuenta</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <div>
                <span class="font-medium text-gray-500">Nombre completo:</span>
                {{ auth()->user()->primer_nombre }}
                {{ auth()->user()->segundo_nombre }}
                {{ auth()->user()->primer_apellido }}
                {{ auth()->user()->segundo_apellido }}
            </div>
            <div>
                <span class="font-medium text-gray-500">Correo:</span>
                {{ auth()->user()->correo }}
            </div>
            <div>
                <span class="font-medium text-gray-500">Teléfono:</span>
                {{ auth()->user()->telefono ?? 'No registrado' }}
            </div>
            <div>
                <span class="font-medium text-gray-500">Rol:</span>
                {{ auth()->user()->roles->pluck('nombre')->join(', ') }}
            </div>
        </div>
    </div>

</div>
@endsection
