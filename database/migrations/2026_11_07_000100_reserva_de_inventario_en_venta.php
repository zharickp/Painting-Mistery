<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Control real del inventario en las ventas.
 *
 * - venta.inventario_descontado: indica si las unidades de esa venta ya se
 *   restaron del inventario. Desde ahora se restan al crear la orden (quedan
 *   apartadas mientras el cliente paga) y se devuelven si se cancela o vence.
 *   Las ventas ya pagadas descontaron su stock al pagarse, por eso quedan en true.
 * - inventario.stock_actual nunca puede quedar negativo (restricción CHECK).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('venta', 'inventario_descontado')) {
            Schema::table('venta', function (Blueprint $table) {
                $table->boolean('inventario_descontado')->default(false);
            });
            DB::table('venta')->where('estado', 'pagada')->update(['inventario_descontado' => true]);
        }

        DB::table('inventario')->where('stock_actual', '<', 0)->update(['stock_actual' => 0]);
        DB::statement('ALTER TABLE inventario DROP CONSTRAINT IF EXISTS inventario_stock_no_negativo');
        DB::statement('ALTER TABLE inventario ADD CONSTRAINT inventario_stock_no_negativo CHECK (stock_actual >= 0)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE inventario DROP CONSTRAINT IF EXISTS inventario_stock_no_negativo');

        if (Schema::hasColumn('venta', 'inventario_descontado')) {
            Schema::table('venta', function (Blueprint $table) {
                $table->dropColumn('inventario_descontado');
            });
        }
    }
};
