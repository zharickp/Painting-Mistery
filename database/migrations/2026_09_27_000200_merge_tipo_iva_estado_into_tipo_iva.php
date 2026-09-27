<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El estado activo/inactivo del tipo de IVA pasa a ser una columna de `tipo_iva`
 * y se elimina la tabla auxiliar `tipo_iva_estado`.
 *
 * REQUISITO: `tipo_iva` debe pertenecer al usuario de la aplicación
 * (ver database/sql/permisos_pmistery.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tipo_iva', 'activo')) {
            Schema::table('tipo_iva', function (Blueprint $table) {
                $table->boolean('activo')->default(true);
            });
        }

        if (Schema::hasTable('tipo_iva_estado')) {
            DB::statement('
                UPDATE tipo_iva t
                   SET activo = e.activo
                  FROM tipo_iva_estado e
                 WHERE e.tipo_iva_id = t.id
            ');

            Schema::drop('tipo_iva_estado');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('tipo_iva_estado')) {
            Schema::create('tipo_iva_estado', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tipo_iva_id')->unique()->constrained('tipo_iva')->cascadeOnDelete();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('tipo_iva', 'activo')) {
            DB::statement('
                INSERT INTO tipo_iva_estado (tipo_iva_id, activo, created_at, updated_at)
                SELECT id, activo, now(), now() FROM tipo_iva WHERE activo = false
            ');

            Schema::table('tipo_iva', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }
    }
};
