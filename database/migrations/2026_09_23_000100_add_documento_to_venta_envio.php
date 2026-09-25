<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot del documento de identidad de quien compra, para la
 * orden de venta — igual que el resto de datos de envío, se congela
 * en el momento de la compra.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta_envio', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_envio', 'tipo_documento')) {
                $table->string('tipo_documento', 10)->nullable()->after('correo_envio');
            }
            if (!Schema::hasColumn('venta_envio', 'numero_documento')) {
                $table->string('numero_documento', 20)->nullable()->after('tipo_documento');
            }
            if (!Schema::hasColumn('venta_envio', 'acepto_terminos')) {
                $table->boolean('acepto_terminos')->default(false)->after('numero_documento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_envio', function (Blueprint $table) {
            foreach (['tipo_documento', 'numero_documento', 'acepto_terminos'] as $c) {
                if (Schema::hasColumn('venta_envio', $c)) $table->dropColumn($c);
            }
        });
    }
};
