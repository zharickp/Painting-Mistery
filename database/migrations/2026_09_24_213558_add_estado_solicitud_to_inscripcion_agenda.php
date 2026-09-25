<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `inscripcion.estado` tiene un CHECK de la BD que solo admite 'inscrito' y 'cancelado'
     * (y la tabla no es alterable). El estado detallado de la solicitud vive aquí.
     */
    public function up(): void
    {
        Schema::table('inscripcion_agenda', function (Blueprint $table) {
            if (! Schema::hasColumn('inscripcion_agenda', 'estado_solicitud')) {
                $table->string('estado_solicitud', 15)->default('pendiente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inscripcion_agenda', function (Blueprint $table) {
            $table->dropColumn('estado_solicitud');
        });
    }
};
