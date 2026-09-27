<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina el módulo "Tarifas de envío" de la base de datos.
 *
 * El costo del envío ya NO depende de esta tabla: se calcula por el valor
 * del carrito en ShippingService::calcularPorSubtotal (config/envios.php).
 * Se elimina la tabla `tarifa_envio` y la columna `venta_envio.tarifa_envio_id`,
 * que solo apuntaba a ella y ya no se llenaba.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('venta_envio', 'tarifa_envio_id')) {
            Schema::table('venta_envio', function (Blueprint $table) {
                $table->dropColumn('tarifa_envio_id');
            });
        }

        Schema::dropIfExists('tarifa_envio');
    }

    public function down(): void
    {
        if (! Schema::hasTable('tarifa_envio')) {
            Schema::create('tarifa_envio', function (Blueprint $table) {
                $table->id();
                $table->string('departamento', 80)->nullable();
                $table->string('ciudad', 80)->nullable();
                $table->decimal('precio_base', 12, 2);
                $table->decimal('umbral_envio_gratis', 12, 2)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();

                $table->index(['departamento', 'ciudad', 'activo']);
            });
        }

        if (! Schema::hasColumn('venta_envio', 'tarifa_envio_id')) {
            Schema::table('venta_envio', function (Blueprint $table) {
                $table->unsignedBigInteger('tarifa_envio_id')->nullable();
            });
        }
    }
};
