@extends('layouts.app')
@section('title', 'Mis Cursos')
@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Mis Cursos</h1>
    <p class="text-sm text-gray-400 mt-1">Cursos en los que estás inscrito.</p>
</div>
@php $inscripciones = auth()->user()->inscripciones()->with('curso')->paginate(10); @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($inscripciones as $ins)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="h-10 w-10 bg-red-600 text-white rounded-xl flex items-center justify-center mb-3">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 mb-1">{{ $ins->curso->nombre ?? 'Curso' }}</h3>
        <p class="text-xs text-gray-400">Inscrito el {{ \Carbon\Carbon::parse($ins->created_at)->format('d/m/Y') }}</p>
    </div>
    @empty
    <div class="col-span-full text-center py-16 text-gray-400">
        <p class="mb-3">Aún no estás inscrito en ningún curso.</p>
        <a href="{{ route('inicio') }}#cursos" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm transition">Ver cursos disponibles</a>
    </div>
    @endforelse
</div>
@endsection
