<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de reserva {{ $ins->codigoReserva() }} — Painting Mistery</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-10 print:py-0 print:bg-white">
@php
    $curso = $ins->curso;
    $info = $curso->info;
    $usuario = $ins->usuario;
    $fechaTxt = $ins->fechaTexto() ?? 'Por confirmar';
    $mensajeWa = "Hola Painting Mistery! Reservé el {$curso->nombre} ({$fechaTxt}). Mi comprobante es {$ins->codigoReserva()} a nombre de " . trim($usuario->primer_nombre . ' ' . $usuario->primer_apellido) . ". Quiero coordinar el abono y los detalles.";
    $confirmada = in_array($ins->estado, ['confirmada', 'completada'], true);
@endphp

<div class="max-w-3xl mx-auto px-4">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 no-print">
        <a href="{{ route('mi-cuenta.cursos') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-red-600 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a mis cursos
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition">Imprimir / guardar PDF</button>
    </div>

    @if(session('success'))
        <div class="no-print mb-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10 print:shadow-none print:border-0">

        <div class="flex items-start justify-between border-b border-slate-100 pb-6 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-full overflow-hidden ring-2 ring-red-500/30 shrink-0">
                    <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="Painting Mistery" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="font-extrabold text-slate-800 text-lg leading-tight">Painting <span class="text-red-600">Mistery</span></p>
                    <p class="text-xs text-slate-400">Cl. 4 #35-42 casa 13, Sicomoro · Melgar, Tolima</p>
                    <p class="text-xs text-slate-400">WhatsApp +57 314 455 7602</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Comprobante de reserva</p>
                <p class="font-mono font-black text-slate-800 text-lg">{{ $ins->codigoReserva() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $ins->created_at->format('d/m/Y h:i A') }}</p>
                <p class="mt-1.5 inline-block px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wide border {{ $confirmada ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-amber-100 text-amber-800 border-amber-300' }}">
                    {{ $confirmada ? 'Reserva confirmada' : 'Pendiente de abono' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Estudiante</p>
                <p class="font-semibold text-slate-800">{{ $usuario->nombreCompleto() }}</p>
                @if($usuario->numero_documento)<p class="text-slate-500 text-xs">Documento {{ $usuario->numero_documento }}</p>@endif
                <p class="text-slate-500 text-xs">{{ $usuario->correo }}@if($usuario->telefono) · {{ $usuario->telefono }}@endif</p>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Curso</p>
                <p class="font-semibold text-slate-800">{{ $curso->nombre }}</p>
                <p class="text-slate-600">{{ $fechaTxt }}</p>
                <p class="text-slate-500 text-xs">{{ $curso->duracionTexto() }} · {{ $info?->ubicacion ?: 'Taller Painting Mistery, Melgar – Tolima' }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-100 divide-y divide-slate-100 text-sm mb-8">
            <div class="flex justify-between px-4 py-3"><span class="text-slate-500">Valor del curso</span><span class="font-bold text-slate-800">${{ number_format($curso->costo, 0, ',', '.') }}</span></div>
            <div class="flex justify-between px-4 py-3"><span class="text-slate-500">Abono para asegurar el cupo</span><span class="font-semibold text-slate-700">Te lo confirmamos por WhatsApp</span></div>
            <div class="flex justify-between px-4 py-3"><span class="text-slate-500">Saldo</span><span class="font-semibold text-slate-700">Se cancela después del abono, según lo acordado</span></div>
            @if($info?->incluye_certificado ?? true)<div class="flex justify-between px-4 py-3"><span class="text-slate-500">Certificado</span><span class="font-semibold text-emerald-600">Incluido al finalizar</span></div>@endif
            @if($info?->requisitos)<div class="flex justify-between gap-6 px-4 py-3"><span class="text-slate-500 shrink-0">Debes traer</span><span class="text-slate-700 text-right">{{ $info->requisitos }}</span></div>@endif
            @if($ins->agenda?->notas)<div class="flex justify-between gap-6 px-4 py-3"><span class="text-slate-500 shrink-0">Indicaciones</span><span class="text-slate-700 text-right">{{ $ins->agenda->notas }}</span></div>@endif
        </div>

        <div class="mb-8">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3">Qué sigue</p>
            <ol class="space-y-2.5 text-sm text-slate-700">
                <li class="flex gap-3"><span class="h-6 w-6 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>Envíanos este comprobante por WhatsApp al <strong>+57 314 455 7602</strong>.</li>
                <li class="flex gap-3"><span class="h-6 w-6 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center shrink-0">2</span>Por ahí te damos toda la información: valor del abono, medios de pago y, si vienes de otra ciudad, el hospedaje.</li>
                <li class="flex gap-3"><span class="h-6 w-6 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center shrink-0">3</span>Con el abono realizado confirmamos tu cupo. El resto se paga después, como acordemos.</li>
            </ol>
        </div>

        <div class="no-print mb-6">
            <a href="https://wa.me/573144557602?text={{ urlencode($mensajeWa) }}" target="_blank" rel="noopener"
               class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3.5 rounded-xl text-sm transition">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Enviar comprobante por WhatsApp
            </a>
        </div>

        <div class="rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-[11px] text-slate-400 leading-relaxed">
            Documento informativo generado por el sistema de Painting Mistery. No constituye factura ni recibo de pago:
            el cupo queda asegurado cuando se confirme el abono.
        </div>
    </div>
</div>
</body>
</html>
