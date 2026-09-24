<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `curso` e `inscripcion` son propiedad del rol `postgres`, no de `pmistery`,
     * por lo que ALTER TABLE falla por permisos (mismo caso ya resuelto con `venta_envio`).
     * Se usan tablas auxiliares 1:1, propiedad de `pmistery`, en su lugar.
     */
    public function up(): void
    {
        if (! Schema::hasTable('curso_info')) {
            Schema::create('curso_info', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_id')->unique()->constrained('curso')->cascadeOnDelete();
                $table->string('ubicacion')->nullable();
                $table->string('duracion')->nullable();
                $table->text('requisitos')->nullable();
                $table->boolean('incluye_certificado')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('inscripcion_agenda')) {
            Schema::create('inscripcion_agenda', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->unique()->constrained('inscripcion')->cascadeOnDelete();
                $table->date('fecha_preferida')->nullable();
                $table->date('fecha_confirmada')->nullable();
                $table->text('notas')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripcion_agenda');
        Schema::dropIfExists('curso_info');
    }
};
