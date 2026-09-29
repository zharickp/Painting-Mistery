<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** En las demás tablas el campo activo/inactivo se llama `estado`; tipo_iva queda igual. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tipo_iva', 'activo') && ! Schema::hasColumn('tipo_iva', 'estado')) {
            Schema::table('tipo_iva', fn (Blueprint $t) => $t->renameColumn('activo', 'estado'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tipo_iva', 'estado') && ! Schema::hasColumn('tipo_iva', 'activo')) {
            Schema::table('tipo_iva', fn (Blueprint $t) => $t->renameColumn('estado', 'activo'));
        }
    }
};
