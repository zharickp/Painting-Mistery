@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Gestión de Usuarios</h1>
        <p class="text-sm text-gray-400 mt-1">Listado completo de usuarios registrados.</p>
    </div>
    <a href="{{ route('admin.usuarios.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo usuario
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr class="text-xs text-gray-400 uppercase tracking-wide">
                    <th class="px-5 py-3">Nombre</th>
                    <th class="px-5 py-3">Documento</th>
                    <th class="px-5 py-3">Correo</th>
                    <th class="px-5 py-3">Teléfono</th>
                    <th class="px-5 py-3 text-center">Rol</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-center">Verificado</th>
                    <th class="px-5 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($usuarios as $u)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs shrink-0">
                                {{ strtoupper(substr($u->primer_nombre, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $u->primer_nombre }} {{ $u->primer_apellido }}</p>
                                @if($u->segundo_nombre || $u->segundo_apellido)
                                    <p class="text-xs text-gray-400">{{ $u->segundo_nombre }} {{ $u->segundo_apellido }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $u->tipoDocumento->nombre ?? '' }}<br>
                        <span class="font-medium text-gray-700">{{ $u->numero_documento }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $u->correo }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $u->telefono ?? '—' }}</td>
                    <td class="px-5 py-3 text-center">
                        @foreach($u->roles as $r)
                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs rounded-full">{{ $r->nombre }}</span>
                        @endforeach
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $u->estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $u->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs {{ $u->correo_verificado_at ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $u->correo_verificado_at ? '✅ Sí' : '⏳ No' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.usuarios.edit', $u) }}"
                               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">
                                Editar
                            </a>
                            @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.toggle', $u) }}">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1 text-xs rounded-md transition
                                                {{ $u->estado
                                                    ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700'
                                                    : 'bg-green-50 hover:bg-green-100 text-green-700' }}">
                                        {{ $u->estado ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-300">Tu cuenta</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        No hay usuarios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">
        {{ $usuarios->links() }}
    </div>
</div>

@endsection
