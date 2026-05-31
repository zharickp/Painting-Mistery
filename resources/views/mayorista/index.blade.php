@extends('layouts.app')
@section('title', 'Módulo Mayorista')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Módulo Mayorista</h1>
    <p class="text-sm text-gray-400 mt-1">Gestión de clientes mayoristas, precios especiales y pedidos al por mayor.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-center">
        <div class="h-14 w-14 bg-red-50 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-7 w-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Clientes Mayoristas</h3>
        <p class="text-sm text-gray-400">Gestión de clientes con precios especiales</p>
        <button class="mt-4 w-full py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">Ver clientes</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-center">
        <div class="h-14 w-14 bg-indigo-50 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-7 w-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Precios Mayoreo</h3>
        <p class="text-sm text-gray-400">Configura precios especiales por volumen</p>
        <button class="mt-4 w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">Configurar</button>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-center">
        <div class="h-14 w-14 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">Pedidos al por Mayor</h3>
        <p class="text-sm text-gray-400">Historial y gestión de pedidos mayoristas</p>
        <button class="mt-4 w-full py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">Ver pedidos</button>
    </div>
</div>

<div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-6">
    <p class="text-red-400 text-xs font-semibold uppercase tracking-widest mb-2">En desarrollo</p>
    <h2 class="text-white text-lg font-bold mb-2">Módulo Mayorista — Próximamente completo</h2>
    <p class="text-gray-400 text-sm">Este módulo incluirá gestión completa de clientes mayoristas, tablas de precios por volumen, pedidos especiales y reportes diferenciados.</p>
</div>
@endsection
