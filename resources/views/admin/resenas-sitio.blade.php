@extends('layouts.app')
@section('title', 'Reseñas del sitio')
@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Reseñas del sitio</h1>
    <p class="text-sm text-gray-400 mt-0.5">Solo las aprobadas se muestran en el inicio.</p>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>
@endif

@php $puedeEditar = auth()->user()->tieneRol('Administrador', 'Asesor'); @endphp

<div class="space-y-3">
    @forelse($resenas as $r)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <p class="font-semibold text-gray-800">{{ $r->nombre }}</p>
                <span class="text-yellow-500 text-sm">{{ str_repeat('★', $r->calificacion) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $r->calificacion) }}</span></span>
                <span class="px-2 py-0.5 text-[11px] rounded-full font-semibold
                    {{ $r->estado === 'aprobada' ? 'bg-green-100 text-green-700' : ($r->estado === 'pendiente' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">{{ ucfirst($r->estado) }}</span>
            </div>
            <p class="text-sm text-gray-600">{{ $r->comentario }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $r->created_at->format('d/m/Y H:i') }}</p>
        </div>
        @if($puedeEditar)
        <form method="POST" action="{{ route('admin.resenas-sitio.update', $r) }}" class="flex gap-2 shrink-0">
            @csrf @method('PUT')
            @if($r->estado !== 'aprobada')
                <button name="estado" value="aprobada" class="px-3 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 text-xs font-semibold transition">Aprobar</button>
            @endif
            @if($r->estado !== 'rechazada')
                <button name="estado" value="rechazada" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold transition">Rechazar</button>
            @endif
        </form>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center text-gray-400">Aún no hay reseñas.</div>
    @endforelse
</div>

@if($resenas->hasPages())<div class="mt-4">{{ $resenas->links() }}</div>@endif

@endsection
