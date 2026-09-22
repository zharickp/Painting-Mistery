<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla auxiliar 1:1 con `venta`. Guarda el snapshot de los datos
 * de envío y el estado de Wompi. Se usa esta tabla en vez de añadir
 * columnas a `venta` para no depender de permisos ALTER en la BD.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('venta_envio')) return;

        Schema::create('venta_envio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id')->unique();

            // Identificación de la orden
            $table->string('numero_orden', 40)->unique();
            $table->string('wompi_reference', 60)->unique()->nullable();
            $table->string('wompi_transaction_id', 60)->nullable();
            $table->string('wompi_payment_method', 40)->nullable();

            // Estados
            // payment_status: PENDING | APPROVED | DECLINED | ERROR | VOIDED
            // estado_pedido:  pendiente | confirmado | preparando | enviado | entregado | cancelado
            $table->string('payment_status', 20)->default('PENDING');
            $table->string('estado_pedido', 30)->default('pendiente');
            $table->timestamp('fecha_pago')->nullable();

            // Totales replicados aquí para consulta rápida
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('envio',    12, 2)->default(0);
            $table->decimal('total',    12, 2)->default(0);

            // Snapshot de destinatario
            $table->string('nombre_envio', 120);
            $table->string('telefono_envio', 40);
            $table->string('correo_envio', 120);
            $table->string('departamento_envio', 80);
            $table->string('ciudad_envio', 80)->nullable();
            $table->text('direccion_envio');
            $table->text('referencia_envio')->nullable();

            $table->unsignedBigInteger('tarifa_envio_id')->nullable();

            $table->timestamps();

            $table->index('payment_status');
            $table->index('estado_pedido');
            $table->foreign('venta_id')->references('id')->on('venta')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_envio');
    }
};
