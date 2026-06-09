@extends('layouts.app')
@section('title', 'Roles y Permisos')
@section('content')

{{-- Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Roles y Permisos</h1>
        <p class="text-sm text-gray-400 mt-1">Administra los roles asignados a cada usuario del sistema.</p>
    </div>
    {{-- Resumen por rol --}}
    <div class="flex gap-3 flex-wrap">
        @foreach($roles as $rol)
        @php
            $colors = [
                'Administrador' => 'bg-red-50 text-red-700 border-red-200',
                'Asesor'        => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'Gerente'       => 'bg-purple-50 text-purple-700 border-purple-200',
                'Cliente'       => 'bg-green-50 text-green-700 border-green-200',
            ];
            $c = $colors[$rol->nombre] ?? 'bg-gray-50 text-gray-700 border-gray-200';
        @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold {{ $c }}">
            {{ $rol->nombre }}
            <span class="bg-white/60 px-1.5 py-0.5 rounded-md">{{ $rol->usuarios()->count() }}</span>
        </span>
        @endforeach
    </div>
</div>

{{-- Flash message --}}
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
    <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Buscador --}}
<form method="GET" action="{{ route('admin.roles') }}" class="mb-5">
    <div class="relative max-w-sm">
        <input type="text" name="buscar" value="{{ $buscar ?? '' }}" placeholder="Buscar por nombre, correo o documento..."
               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
</form>

{{-- Tabla usuarios --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Usuario</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Documento</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Correo</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rol actual</th>
                @if(auth()->user()->tieneRol('Administrador'))
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Cambiar rol</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($usuarios as $u)
            <tr class="hover:bg-gray-50/50 transition">
                {{-- Avatar + nombre --}}
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-red-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($u->primer_nombre,0,1)) }}{{ strtoupper(substr($u->primer_apellido,0,1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $u->primer_nombre }} {{ $u->segundo_nombre }} {{ $u->primer_apellido }} {{ $u->segundo_apellido }}</p>
                            @if($u->genero)
                            <p class="text-xs text-gray-400">{{ $u->genero === 'M' ? 'Masculino' : ($u->genero === 'F' ? 'Femenino' : 'Otro') }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                {{-- Documento --}}
                <td class="px-5 py-4 hidden md:table-cell">
                    <p class="text-gray-600 text-sm">{{ $u->numero_documento }}</p>
                    <p class="text-xs text-gray-400">{{ $u->tipoDocumento->nombre ?? '—' }}</p>
                </td>
                {{-- Correo --}}
                <td class="px-5 py-4 hidden sm:table-cell">
                    <p class="text-gray-600 text-sm">{{ $u->correo }}</p>
                    @if($u->telefono)
                    <p class="text-xs text-gray-400">{{ $u->telefono }}</p>
                    @endif
                </td>
                {{-- Rol badge --}}
                <td class="px-5 py-4">
                    @forelse($u->roles as $rol)
                    @php
                        $badge = [
                            'Administrador' => 'bg-red-100 text-red-700',
                            'Asesor'        => 'bg-indigo-100 text-indigo-700',
                            'Gerente'       => 'bg-purple-100 text-purple-700',
                            'Cliente'       => 'bg-green-100 text-green-700',
                        ][$rol->nombre] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $rol->nombre }}</span>
                    @empty
                    <span class="text-gray-400 text-xs">Sin rol</span>
                    @endforelse
                </td>
                {{-- Selector de rol (solo admins) --}}
                @if(auth()->user()->tieneRol('Administrador'))
                <td class="px-5 py-4">
                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.roles.update', $u) }}" class="flex items-center gap-2">
                        @csrf
                        <select name="rol_id"
                                onchange="this.form.submit()"
                                class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:border-red-400 bg-white text-gray-700 cursor-pointer hover:border-gray-300 transition">
                            @foreach($roles as $rol)
                            <option value="{{ $rol->id }}"
                                {{ $u->roles->contains('id', $rol->id) ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                    @else
                    <span class="text-xs text-gray-400 italic">Tu cuenta</span>
                    @endif
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">
                    <svg class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    No se encontraron usuarios.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginación --}}
    @if($usuarios->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
        {{ $usuarios->links() }}
    </div>
    @endif
</div>

@endsection
