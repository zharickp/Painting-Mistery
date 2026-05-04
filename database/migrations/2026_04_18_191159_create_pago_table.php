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
        Schema::create('pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('venta')->cascadeOnDelete();
            $table->foreignId('metodo_pago_id')->constrained('metodo_pago');
            $table->string('numero_comprobante')->unique();
            $table->decimal('valor', 10, 2);
            $table->dateTime('fecha_pago')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago');
    }
};
