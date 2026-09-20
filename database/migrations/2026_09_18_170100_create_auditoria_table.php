<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La tabla `auditoria` ya existe en la base de datos con las columnas base.
 * Esta migración solo añade `registro_etiqueta` si aún no está presente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('auditoria') && !Schema::hasColumn('auditoria', 'registro_etiqueta')) {
            Schema::table('auditoria', function (Blueprint $table) {
                $table->string('registro_etiqueta')->nullable()->after('registro_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('auditoria') && Schema::hasColumn('auditoria', 'registro_etiqueta')) {
            Schema::table('auditoria', function (Blueprint $table) {
                $table->dropColumn('registro_etiqueta');
            });
        }
    }
};
