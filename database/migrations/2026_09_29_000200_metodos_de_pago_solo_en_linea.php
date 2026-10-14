<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El sistema solo recibe pagos por internet, así que ya no hace falta
 * distinguir métodos "para el cliente" de métodos del taller (efectivo).
 * Los métodos que no eran en línea quedan inactivos (no se borran porque
 * pagos anteriores pueden estar registrados con ellos).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['disponible_para_cliente', 'en_linea'] as $columna) {
            if (Schema::hasColumn('metodo_pago', $columna)) {
                DB::table('metodo_pago')->where($columna, false)->update(['estado' => false, 'updated_at' => now()]);
                Schema::table('metodo_pago', fn (Blueprint $t) => $t->dropColumn($columna));
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('metodo_pago', 'disponible_para_cliente')) {
            Schema::table('metodo_pago', fn (Blueprint $t) => $t->boolean('disponible_para_cliente')->default(true));
            DB::table('metodo_pago')->where('nombre', 'Efectivo')->update(['disponible_para_cliente' => false]);
        }
    }
};
