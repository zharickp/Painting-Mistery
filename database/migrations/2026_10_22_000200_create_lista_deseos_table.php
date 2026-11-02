<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lista de deseos de cada cliente: productos que guardó para comprar después.
 * Antes solo vivía en el navegador; ahora queda en su cuenta y la ve en
 * cualquier dispositivo. Un producto aparece una sola vez por cliente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_deseos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('producto')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_deseos');
    }
};
