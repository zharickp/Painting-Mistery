<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade a la tabla `venta` los campos que hacen falta para operar
 * como una orden de e-commerce real con integración Wompi:
 * - número de orden legible (PM-ORD-000001)
 * - snapshot de dirección de envío (para trazabilidad)
 * - subtotal / envío separados del total
 * - estado logístico + estado de pago
 * - referencia y transacción Wompi
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            if (!Schema::hasColumn('venta', 'numero_orden')) {
                $table->string('numero_orden', 40)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('venta', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->nullable()->after('total');
            }
            if (!Schema::hasColumn('venta', 'envio')) {
                $table->decimal('envio', 12, 2)->nullable()->after('subtotal');
            }
            if (!Schema::hasColumn('venta', 'estado_pedido')) {
                // pendiente, confirmado, preparando, enviado, entregado, cancelado
                $table->string('estado_pedido', 30)->default('pendiente')->after('envio');
            }
            if (!Schema::hasColumn('venta', 'payment_status')) {
                // PENDING, APPROVED, DECLINED, ERROR, VOIDED (mismos que Wompi)
                $table->string('payment_status', 20)->default('PENDING')->after('estado_pedido');
            }
            if (!Schema::hasColumn('venta', 'wompi_reference')) {
                $table->string('wompi_reference', 60)->nullable()->unique()->after('payment_status');
            }
            if (!Schema::hasColumn('venta', 'wompi_transaction_id')) {
                $table->string('wompi_transaction_id', 60)->nullable()->after('wompi_reference');
            }
            if (!Schema::hasColumn('venta', 'wompi_payment_method')) {
                $table->string('wompi_payment_method', 40)->nullable()->after('wompi_transaction_id');
            }
            if (!Schema::hasColumn('venta', 'fecha_pago')) {
                $table->timestamp('fecha_pago')->nullable()->after('wompi_payment_method');
            }

            // Snapshot de datos de envío (se congelan cuando se crea la orden)
            if (!Schema::hasColumn('venta', 'nombre_envio')) {
                $table->string('nombre_envio', 120)->nullable();
            }
            if (!Schema::hasColumn('venta', 'telefono_envio')) {
                $table->string('telefono_envio', 40)->nullable();
            }
            if (!Schema::hasColumn('venta', 'correo_envio')) {
                $table->string('correo_envio', 120)->nullable();
            }
            if (!Schema::hasColumn('venta', 'departamento_envio')) {
                $table->string('departamento_envio', 80)->nullable();
            }
            if (!Schema::hasColumn('venta', 'ciudad_envio')) {
                $table->string('ciudad_envio', 80)->nullable();
            }
            if (!Schema::hasColumn('venta', 'direccion_envio')) {
                $table->text('direccion_envio')->nullable();
            }
            if (!Schema::hasColumn('venta', 'referencia_envio')) {
                $table->text('referencia_envio')->nullable();
            }
        });

        // Índices para consultas frecuentes en el dashboard cliente/admin
        Schema::table('venta', function (Blueprint $table) {
            try { $table->index('payment_status'); } catch (\Throwable $e) {}
            try { $table->index('estado_pedido'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $cols = [
                'numero_orden','subtotal','envio','estado_pedido','payment_status',
                'wompi_reference','wompi_transaction_id','wompi_payment_method','fecha_pago',
                'nombre_envio','telefono_envio','correo_envio','departamento_envio',
                'ciudad_envio','direccion_envio','referencia_envio',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('venta', $c)) $table->dropColumn($c);
            }
        });
    }
};
