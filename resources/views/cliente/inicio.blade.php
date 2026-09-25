@extends('layouts.guest')
@section('title', 'Mi cuenta')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    @include('cliente.partials.menu')

    {{-- Banner de bienvenida --}}
    <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-red-600 to-red-800 p-6 sm:p-8 mb-6">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-white font-black text-xl shrink-0 ring-1 ring-white/20">
                {{ strtoupper(substr(auth()->user()->primer_nombre, 0, 1)) }}
            </div>
            <div>
                <p class="text-white/70 text-[11px] font-bold uppercase tracking-widest">Bienvenido a tu panel</p>
                <h1 class="text-2xl font-extrabold text-white mt-0.5">{{ auth()->user()->nombreCompleto() }}</h1>
                <p class="text-white/80 text-sm">{{ auth()->user()->correo }}</p>
            </div>
        </div>
    </div>

    {{-- Aviso de pagos pendientes (solo si aplica) --}}
    @if($stats['total_pendiente'] > 0)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-amber-800 text-sm">
                        Tienes ${{ number_format($stats['total_pendiente'], 0, ',', '.') }} en pedidos pendientes de pago
                    </p>
                    <p class="text-xs text-amber-700">Se cancelan automáticamente 24 horas después de creados si no se completa el pago.</p>
                </div>
            </div>
            <a href="{{ route('mi-cuenta.pedidos') }}" class="text-xs font-bold text-amber-800 hover:text-amber-900 underline shrink-0">
                Ver pedidos →
            </a>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="h-10 w-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mb-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Pedidos totales</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Pendientes de pago</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['pendientes'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Compras aprobadas</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['aprobadas'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xs uppercase tracking-wider font-bold text-slate-500">Total invertido</p>
            <p class="text-xl font-black text-slate-800 mt-1">${{ number_format($stats['total_pagado'], 0, ',', '.') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Solo compras pagadas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Últimos pedidos --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">Últimos pedidos</h2>
                <a href="{{ route('mi-cuenta.pedidos') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">
                    Ver todos →
                </a>
            </div>

            @if($ventas->isEmpty())
                <div class="px-6 py-16 text-center">
                    <svg class="h-14 w-14 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-sm text-slate-500">Aún no tienes pedidos.</p>
                    <a href="{{ route('tienda.index') }}" class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2 rounded-xl text-sm">
                        Ir a la tienda
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($ventas as $v)
                        <a href="{{ route('mi-cuenta.pedido', $v->id) }}" class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                            <div>
                                <p class="font-mono font-semibold text-slate-800 text-sm">
                                    {{ $v->envio?->numero_orden ?? '#' . $v->id }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $v->fecha?->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                @include('partials.estado-orden', ['venta' => $v])
                                <p class="font-bold text-slate-800 text-sm w-24 text-right">${{ number_format($v->total, 0, ',', '.') }}</p>
                                <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Accesos rápidos --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Accesos rápidos</h2>
            <div class="space-y-2">
                <a href="{{ route('tienda.index') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-red-600">Ir a la tienda</span>
                    </div>
                    <svg class="h-4 w-4 text-slate-300 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('mi-cuenta.pedidos') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-red-600">Mis pedidos</span>
                    </div>
                    <svg class="h-4 w-4 text-slate-300 group-hover:text-red-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('mi-cuenta.cursos') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition group">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-red-600">Mis cursos</span>
                    <svg class="h-4 w-4 text-slate-300 group-hover:text-red-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('mi-cuenta.perfil') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition group">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-red-600">Mi perfil</span>
                    <svg class="h-4 w-4 text-slate-300 group-hover:text-red-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('academia') }}"
                   class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition group">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-red-600">Explorar los cursos</span>
                    <svg class="h-4 w-4 text-slate-300 group-hover:text-red-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Resumen de actividad --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Última compra</p>
            @if($ultimaOrden)
                <p class="font-mono font-bold text-slate-800 mt-1">{{ $ultimaOrden->envio->numero_orden }}</p>
                <p class="text-xs text-slate-500">{{ $ultimaOrden->fecha?->format('d/m/Y') }} · ${{ number_format($ultimaOrden->total, 0, ',', '.') }}</p>
                <a href="{{ route('mi-cuenta.pedido.orden', $ultimaOrden->id) }}" target="_blank" class="inline-block mt-2 text-xs font-semibold text-red-600 hover:text-red-700">Ver orden de venta →</a>
            @else
                <p class="text-sm text-slate-500 mt-1">Aún no tienes compras pagadas.</p>
            @endif
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Cursos inscritos</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $inscripciones->count() }}</p>
            <a href="{{ route('mi-cuenta.cursos') }}" class="inline-block mt-1 text-xs font-semibold text-red-600 hover:text-red-700">Gestionar cursos →</a>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Próximo curso</p>
            @if($proximoCurso)
                <p class="font-bold text-slate-800 mt-1 leading-snug">{{ $proximoCurso->curso->nombre }}</p>
                <p class="text-xs text-slate-500">{{ $proximoCurso->agenda->fecha_confirmada->format('d/m/Y') }} · {{ $proximoCurso->curso->info?->ubicacion ?: 'Taller Painting Mistery' }}</p>
            @else
                <p class="text-sm text-slate-500 mt-1">Sin fecha confirmada por ahora.</p>
            @endif
        </div>
    </div>

    {{-- Cursos para ti --}}
    @if($cursosDisponibles->isNotEmpty())
    <div class="mt-10">
        <div class="flex items-end justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-800">Cursos para ti</h2>
            <a href="{{ route('academia') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Ver cursos →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($cursosDisponibles as $c)
            <a href="{{ route('academia') }}#curso-{{ $c->id }}" class="bg-white border border-slate-200 hover:border-red-300 hover:shadow-md rounded-2xl p-5 transition block">
                <p class="font-bold text-slate-800 leading-snug">{{ $c->nombre }}</p>
                <p class="text-red-600 font-extrabold mt-2">${{ number_format($c->costo, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-3">Ver detalles →</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recomendados --}}
    @if($recomendados->isNotEmpty())
    <div class="mt-10">
        <div class="flex items-end justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-800">Productos destacados</h2>
            <a href="{{ route('tienda.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-700">Ver tienda →</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($recomendados as $p)
            <a href="{{ route('producto.show', $p) }}" class="group bg-white border border-slate-200 hover:border-red-300 hover:shadow-md rounded-2xl overflow-hidden transition block">
                <div class="h-32 bg-slate-100 overflow-hidden">
                    @if($p->imagen)<img src="{{ $p->imagen }}" alt="{{ $p->nombre }}" loading="lazy" class="h-full w-full object-cover group-hover:scale-105 transition duration-500">@endif
                </div>
                <div class="p-3">
                    <p class="text-sm font-semibold text-slate-800 line-clamp-2">{{ $p->nombre }}</p>
                    <p class="text-red-600 font-bold text-sm mt-1">${{ number_format($p->precio, 0, ',', '.') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Ayuda --}}
    <div class="mt-10 bg-slate-900 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-white font-bold">¿Necesitas ayuda con un pedido, un curso o un servicio?</p>
            <p class="text-slate-400 text-sm">Escríbenos y te respondemos lo antes posible.</p>
        </div>
        <a href="https://wa.me/573144557602" target="_blank" rel="noopener" class="bg-green-500 hover:bg-green-600 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shrink-0">Contactar por WhatsApp</a>
    </div>
</div>

@include('partials.footer')
@endsection
