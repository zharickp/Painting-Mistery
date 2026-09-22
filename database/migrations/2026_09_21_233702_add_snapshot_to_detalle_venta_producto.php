<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fotografía histórica del producto en el momento de la compra:
 * si el producto cambia de nombre o precio, la orden vieja se conserva intacta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_venta_producto', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_venta_producto', 'producto_nombre')) {
                $table->string('producto_nombre', 200)->nullable()->after('producto_id');
            }
            if (!Schema::hasColumn('detalle_venta_producto', 'producto_imagen')) {
                $table->string('producto_imagen', 300)->nullable()->after('producto_nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_venta_producto', function (Blueprint $table) {
            foreach (['producto_nombre','producto_imagen'] as $c) {
                if (Schema::hasColumn('detalle_venta_producto', $c)) $table->dropColumn($c);
            }
        });
    }
};
