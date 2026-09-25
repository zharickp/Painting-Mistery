<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Fechas de inicio publicadas por el taller para cada curso (aprox. dos cursos al mes). */
    public function up(): void
    {
        if (! Schema::hasTable('curso_fecha')) {
            Schema::create('curso_fecha', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_id')->constrained('curso')->cascadeOnDelete();
                $table->date('fecha');
                $table->timestamps();
                $table->unique(['curso_id', 'fecha']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_fecha');
    }
};
