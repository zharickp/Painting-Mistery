<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('curso')->cascadeOnDelete();
            $table->enum('estado', ['inscrito', 'cancelado'])->default('inscrito');
            $table->unique(['usuario_id', 'curso_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion');
    }
};
