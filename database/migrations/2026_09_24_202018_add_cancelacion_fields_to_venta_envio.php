<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trazabilidad de cancelación y confirmación de pago. Vive en `venta_envio`
     * (tabla propia de pmistery, 1:1 con `venta`) porque `venta` no es alterable.
     */
    public function up(): void
    {
        Schema::table('venta_envio', function (Blueprint $table) {
            if (! Schema::hasColumn('venta_envio', 'cancelada_at')) {
                $table->timestamp('cancelada_at')->nullable();
            }
            if (! Schema::hasColumn('venta_envio', 'cancelada_por')) {
                $table->unsignedBigInteger('cancelada_por')->nullable();
            }
            if (! Schema::hasColumn('venta_envio', 'motivo_cancelacion')) {
                $table->string('motivo_cancelacion', 255)->nullable();
            }
            if (! Schema::hasColumn('venta_envio', 'pago_confirmado_por')) {
                $table->unsignedBigInteger('pago_confirmado_por')->nullable();
            }
        });

        // Órdenes que ya estaban canceladas antes de existir estos campos.
        DB::table('venta_envio')
            ->where('estado_pedido', 'cancelado')
            ->whereNull('cancelada_at')
            ->update([
                'cancelada_at'       => DB::raw('updated_at'),
                'motivo_cancelacion' => DB::raw("coalesce(motivo_cancelacion, 'Cancelada antes del registro de trazabilidad')"),
            ]);
    }

    public function down(): void
    {
        Schema::table('venta_envio', function (Blueprint $table) {
            $table->dropColumn(['cancelada_at', 'cancelada_por', 'motivo_cancelacion', 'pago_confirmado_por']);
        });
    }
};
