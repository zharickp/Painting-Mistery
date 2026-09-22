<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tarifas de envío configurables desde admin.
 * Se usan en ShippingService para calcular el costo del envío
 * según departamento + ciudad + valor del pedido.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tarifa_envio')) return;

        Schema::create('tarifa_envio', function (Blueprint $table) {
            $table->id();
            $table->string('departamento', 80)->nullable(); // null + ciudad null = comodín global
            $table->string('ciudad', 80)->nullable();       // null = aplica a todo el departamento
            $table->decimal('precio_base', 12, 2);
            $table->decimal('umbral_envio_gratis', 12, 2)->nullable(); // subtotal a partir del cual el envío es gratis
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['departamento', 'ciudad', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifa_envio');
    }
};
