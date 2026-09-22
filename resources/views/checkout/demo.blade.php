@extends('layouts.guest')
@section('title', 'Pago — modo demostración')

@section('content')
@include('partials.nav')

<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 mb-6">
        <p class="text-sm font-bold text-amber-800">⚠ Modo demostración</p>
        <p class="text-xs text-amber-700 mt-1">Wompi aún no está configurado (falta WOMPI_PUBLIC_KEY en .env). Usa este simulador para probar el flujo. La orden se guarda como si fuese real.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm text-center">
        <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mb-4">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800">Simulación de pago</h1>
        <p class="text-sm text-slate-500 mt-1">Orden {{ $envio->numero_orden }} · <span class="text-red-600 font-bold">${{ number_format($envio->total, 0, ',', '.') }}</span></p>

        <form method="POST" action="{{ route('checkout.demo.confirmar', $envio->numero_orden) }}" class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            @csrf
            <button type="submit" name="resultado" value="aprobado"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl text-sm">
                Simular aprobado
            </button>
            <button type="submit" name="resultado" value="rechazado"
                    class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-3 rounded-xl text-sm">
                Simular rechazado
            </button>
        </form>
    </div>
</div>

@include('partials.footer')
@endsection
