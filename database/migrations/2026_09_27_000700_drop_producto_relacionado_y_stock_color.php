<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Productos:
 *  - Se elimina `producto_relacionado`: los recomendados de la ficha del
 *    producto ahora se calculan automáticamente por categoría.
 *  - Se elimina `producto_color.stock`: el color solo agrupa fotos. El stock
 *    real es únicamente `inventario.stock_actual` (el que usan el carrito,
 *    el checkout y las ventas). Antes había dos stocks que no coincidían.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('producto_relacionado');

        if (Schema::hasColumn('producto_color', 'stock')) {
            Schema::table('producto_color', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('producto_color', 'stock')) {
            Schema::table('producto_color', function (Blueprint $table) {
                $table->integer('stock')->default(0);
            });
        }

        if (! Schema::hasTable('producto_relacionado')) {
            Schema::create('producto_relacionado', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('producto')->cascadeOnDelete();
                $table->foreignId('relacionado_id')->constrained('producto')->cascadeOnDelete();
                $table->integer('orden')->default(0);
                $table->timestamps();
                $table->unique(['producto_id', 'relacionado_id']);
            });
        }
    }
};
