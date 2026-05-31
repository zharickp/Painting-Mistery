@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Gestión de Usuarios</h1>
    <p class="text-sm text-gray-400 mt-1">Listado completo de usuarios registrados.</p>
</div>

@php $usuarios = \App\Models\Usuario::with('roles','tipoDocumento')->orderByDesc('created_at')->paginate(15); @endphp

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr class="text-xs text-gray-400 uppercase tracking-wide">
                    <th class="px-5 py-3">Nombre</th>
                    <th class="px-5 py-3">Documento</th>
                    <th class="px-5 py-3">Correo</th>
                    <th class="px-5 py-3">Rol</th>
                    <th class="px-5 py-3">Estado</th>
                    <th class="px-5 py-3">Verificado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($usuarios as $u)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-medium text-gray-800">
                        {{ $u->primer_nombre }} {{ $u->primer_apellido }}
                    </td>
                    <td class="px-5 py-3 text-gray-500">
                        {{ $u->tipoDocumento->nombre ?? '' }} {{ $u->numero_documento }}
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $u->correo }}</td>
                    <td class="px-5 py-3">
                        @foreach($u->roles as $r)
                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs rounded-full">{{ $r->nombre }}</span>
                        @endforeach
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $u->estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $u->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-xs {{ $u->correo_verificado_at ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $u->correo_verificado_at ? '✅ Sí' : '⏳ No' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
