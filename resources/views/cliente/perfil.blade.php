@extends('layouts.guest')
@section('title', 'Mi perfil')

@section('content')
@include('partials.nav')

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('cliente.partials.menu')

    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-800">Mi perfil</h1>
        <p class="text-sm text-slate-500 mt-1">Actualiza tus datos de contacto.</p>
    </div>

    @if(session('success'))<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('mi-cuenta.perfil.update') }}" class="bg-white border border-slate-200 rounded-2xl p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([['primer_nombre','Primer nombre',true],['segundo_nombre','Segundo nombre',false],['primer_apellido','Primer apellido',true],['segundo_apellido','Segundo apellido',false]] as [$campo,$label,$req])
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ $label }}{{ $req ? ' *' : '' }}</label>
                <input type="text" name="{{ $campo }}" value="{{ old($campo, $usuario->$campo) }}" {{ $req ? 'required' : '' }} maxlength="60"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-red-400">
            </div>
            @endforeach
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Teléfono</label>
            <input type="tel" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" maxlength="20"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:border-red-400">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Correo</p>
                <p class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2.5 text-slate-600">{{ $usuario->correo }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Documento</p>
                <p class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2.5 text-slate-600">{{ $usuario->numero_documento ?: '—' }}</p>
            </div>
        </div>
        <p class="text-[11px] text-slate-400">El correo y el documento no se pueden cambiar desde aquí. Si necesitas modificarlos, escríbenos por WhatsApp.</p>
        <button class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition">Guardar cambios</button>
    </form>
</div>

@include('partials.footer')
@endsection
