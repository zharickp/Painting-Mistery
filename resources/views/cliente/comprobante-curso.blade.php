@extends('layouts.documento')

@php
    $curso      = $ins->curso;
    $usuario    = $ins->usuario;
    $fechaTxt   = $ins->fechaTexto() ?? 'Por confirmar';
    $confirmada = in_array($ins->estado, ['confirmada', 'completada'], true);
    $pago       = $ins->venta?->pago;
    $mensajeWa  = "Hola Painting Mistery! Reservé el {$curso->nombre} ({$fechaTxt}). Mi comprobante es {$ins->codigoReserva()} a nombre de "
        . trim($usuario->primer_nombre . ' ' . $usuario->primer_apellido) . '. Quiero coordinar mi llegada y los detalles.';
@endphp

@section('titulo', 'Comprobante de reserva ' . $ins->codigoReserva())
@section('volver', route('mi-cuenta.cursos'))
@section('tipo', 'Comprobante de reserva')
@section('numero', $ins->codigoReserva())
@section('fecha', $ins->created_at->format('d/m/Y h:i A'))
@section('estado')
    <span class="estado {{ $confirmada ? 'azul' : 'amarillo' }}">
        {{ $confirmada ? 'Inscripción confirmada' : 'Pendiente de pago' }}
    </span>
@endsection

@push('estilos')
<style>
    .pasos { margin: 0; padding: 0; list-style: none; }
    .pasos li { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 8px; }
    .pasos .n { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; background: #dc2626; color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
    .wa { display: block; margin-top: 18px; text-align: center; background: #22c55e; color: #fff; text-decoration: none; font-weight: 700; padding: 12px; border-radius: 10px; }
    .wa:hover { background: #16a34a; }
</style>
@endpush

@section('contenido')
    @include('partials.aviso')

    <div class="datos">
        <div class="caja">
            <h3>Estudiante</h3>
            <p class="fuerte">{{ $usuario->nombreCompleto() }}</p>
            @if ($usuario->numero_documento)
                <p class="suave">Documento {{ $usuario->numero_documento }}</p>
            @endif
            <p class="suave">{{ $usuario->correo }}@if ($usuario->telefono) · {{ $usuario->telefono }}@endif</p>
        </div>
        <div class="caja">
            <h3>Curso</h3>
            <p class="fuerte">{{ $curso->nombre }}</p>
            <p>{{ $fechaTxt }}</p>
            <p class="suave">{{ $curso->duracionTexto() }} · {{ $curso->ubicacion ?: 'Taller Painting Mistery, Melgar – Tolima' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Detalle</th><th class="der">Información</th></tr>
        </thead>
        <tbody>
            <tr><td>Valor del curso</td><td class="der num"><strong>${{ number_format($curso->precio, 0, ',', '.') }}</strong></td></tr>
            <tr><td>Pago</td><td class="der">{{ $pago ? ($pago->metodoPago?->nombre ?? 'Pago') . ' · ' . $pago->estadoEtiqueta() : 'Por registrar' }}</td></tr>
            @if ($ins->venta?->numero_orden)
                <tr><td>N.º de orden</td><td class="der num">{{ $ins->venta->numero_orden }}</td></tr>
            @endif
            @if ($curso->incluye_certificado ?? true)
                <tr><td>Certificado</td><td class="der">Incluido al finalizar</td></tr>
            @endif
            @if ($curso->materiales)
                <tr><td>Debes traer</td><td class="der">{{ $curso->materiales }}</td></tr>
            @endif
            @if ($ins->indicaciones)
                <tr><td>Indicaciones</td><td class="der">{{ $ins->indicaciones }}</td></tr>
            @endif
        </tbody>
    </table>

    <div class="seccion">
        <p class="seccion-titulo">Qué sigue</p>
        <ol class="pasos">
            <li><span class="n">1</span><span>Si aún no has pagado, paga tu inscripción desde "Mis cursos". El cupo se aparta 24 horas.</span></li>
            <li><span class="n">2</span><span>Con el pago aprobado, tu inscripción queda confirmada.</span></li>
            <li><span class="n">3</span><span>Te escribimos al <strong>+57 314 455 7602</strong> para cuadrar la llegada y, si vienes de otra ciudad, el hospedaje.</span></li>
        </ol>
    </div>

    <a class="wa no-imprimir" href="https://wa.me/573144557602?text={{ urlencode($mensajeWa) }}" target="_blank" rel="noopener">
        Enviar comprobante por WhatsApp
    </a>

    <div class="nota">
        Documento informativo generado por el sistema de Painting Mistery. No constituye factura:
        el cupo queda asegurado cuando el pago de la inscripción está aprobado.
    </div>
@endsection
