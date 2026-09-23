@extends('layouts.guest')
@section('title', 'Confirmar pago')

@section('content')
@include('partials.nav')

<div class="max-w-2xl mx-auto px-4 py-12">

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

        {{-- Cabecera con marca --}}
        <div class="bg-gradient-to-br from-red-600 to-red-800 px-8 py-8 text-center relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative">
                <div class="mx-auto h-14 w-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center mb-4 ring-1 ring-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <p class="text-white/70 text-[11px] font-bold uppercase tracking-[0.2em]">Pasarela de pago segura</p>
                <h1 class="text-2xl font-extrabold text-white mt-1">Confirmar pago</h1>
            </div>
        </div>

        <div class="px-8 py-8">
            {{-- Resumen de la orden --}}
            <div class="flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl px-5 py-4 mb-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Número de orden</p>
                    <p class="font-mono font-bold text-slate-800 mt-0.5">{{ $envio->numero_orden }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total a pagar</p>
                    <p class="text-xl font-black text-red-600 mt-0.5">${{ number_format($envio->total, 0, ',', '.') }}</p>
                </div>
            </div>

            <p class="text-sm text-slate-500 text-center mb-6">
                Selecciona el resultado para continuar con la simulación de tu compra.
            </p>

            <form method="POST" action="{{ route('checkout.demo.confirmar', $envio->numero_orden) }}"
                  class="flex flex-col sm:flex-row gap-3">
                @csrf
                <button type="submit" name="resultado" value="aprobado"
                        class="flex-1 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl text-sm transition shadow-md shadow-emerald-900/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Pago aprobado
                </button>
                <button type="submit" name="resultado" value="rechazado"
                        class="flex-1 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-bold py-3.5 rounded-xl text-sm transition border border-slate-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Pago rechazado
                </button>
            </form>
        </div>

        <div class="border-t border-slate-100 px-8 py-4 flex items-center justify-center gap-2 bg-slate-50/50">
            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <p class="text-[11px] text-slate-400">Modo demostración — no se realiza ningún cobro real</p>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection
