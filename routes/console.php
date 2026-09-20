<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Copia de seguridad automática cada 15 días (día 1 y día 16 de cada mes a las 03:00).
Schedule::command('respaldos:generar')
    ->cron('0 3 1,16 * *')
    ->onOneServer();
