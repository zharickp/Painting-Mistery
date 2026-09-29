<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La inscripción a un curso se paga como una compra: se crea una venta con su
 * detalle_venta_curso y su pago. venta_id dice con qué venta se pagó la inscripción
 * (vacío en las inscripciones hechas antes de este cambio).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inscripcion', 'venta_id')) {
            Schema::table('inscripcion', function (Blueprint $table) {
                $table->foreignId('venta_id')->nullable()->constrained('venta')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inscripcion', 'venta_id')) {
            Schema::table('inscripcion', function (Blueprint $table) {
                $table->dropConstrainedForeignId('venta_id');
            });
        }
    }
};
