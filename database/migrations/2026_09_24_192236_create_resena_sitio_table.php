<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('resena_sitio')) {
            Schema::create('resena_sitio', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 80);
                $table->unsignedTinyInteger('calificacion');
                $table->text('comentario');
                $table->string('estado', 15)->default('pendiente');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resena_sitio');
    }
};
