<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las reseñas de productos se publican solo después de aprobarlas en el panel.
 * Las que ya existían estaban publicadas, así que quedan como aprobadas.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('resena', 'estado')) {
            Schema::table('resena', function (Blueprint $table) {
                $table->string('estado', 15)->default('pendiente');
            });

            DB::table('resena')->update(['estado' => 'aprobada']);

            DB::statement("ALTER TABLE resena ADD CONSTRAINT resena_estado_check CHECK (estado IN ('pendiente', 'aprobada', 'rechazada'))");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('resena', 'estado')) {
            DB::statement('ALTER TABLE resena DROP CONSTRAINT IF EXISTS resena_estado_check');
            Schema::table('resena', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }
    }
};
