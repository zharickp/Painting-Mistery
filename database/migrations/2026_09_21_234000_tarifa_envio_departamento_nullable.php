<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hace la columna `departamento` nullable para poder guardar la tarifa
 * comodín global (departamento = NULL, ciudad = NULL) que representa
 * "cualquier otro destino".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tarifa_envio')) {
            DB::statement('ALTER TABLE tarifa_envio ALTER COLUMN departamento DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tarifa_envio')) {
            DB::statement('ALTER TABLE tarifa_envio ALTER COLUMN departamento SET NOT NULL');
        }
    }
};
