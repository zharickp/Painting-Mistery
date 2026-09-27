<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Copia de seguridad automática: cada hora se revisa si ya pasaron 15 días desde
// la última. Así, si el equipo estuvo apagado en la fecha, la copia se hace en
// cuanto vuelva a estar encendido.
Schedule::command('respaldos:generar --si-corresponde')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

// Cancela órdenes con pago pendiente que nunca se confirmó (24h de espera).
Schedule::command('pedidos:expirar-pendientes')
    ->hourly()
    ->onOneServer();
