<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Copia de seguridad automática diaria a las 3:00 a. m. (hora de Colombia).
// Desde esa hora se revisa cada hora si ya se hizo la de hoy: si el servidor
// estuvo apagado a las 3, la copia se hace en cuanto vuelva a estar encendido.
// Se conservan las últimas 30 automáticas.
Schedule::command('respaldos:generar --si-corresponde')
    ->hourly()
    ->between('03:00', '23:59')
    ->withoutOverlapping()
    ->onOneServer();

// Cancela órdenes con pago pendiente que nunca se confirmó (24h de espera).
Schedule::command('pedidos:expirar-pendientes')
    ->hourly()
    ->onOneServer();
