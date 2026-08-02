<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('producto_imagen', function (Blueprint $table) {
            $table->foreignId('producto_color_id')->nullable()->after('color_hex')
                ->constrained('producto_color')->cascadeOnDelete();
        });

        // Backfill: cada (producto_id, color_nombre) existente se convierte en
        // una fila real de producto_color, y las fotos quedan vinculadas a ella.
        $grupos = DB::table('producto_imagen')
            ->whereNotNull('color_nombre')
            ->select('producto_id', 'color_nombre', 'color_hex')
            ->distinct()
            ->get();

        foreach ($grupos as $grupo) {
            $colorId = DB::table('producto_color')->insertGetId([
                'producto_id' => $grupo->producto_id,
                'nombre'      => $grupo->color_nombre,
                'hex'         => $grupo->color_hex,
                'stock'       => 0,
                'orden'       => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::table('producto_imagen')
                ->where('producto_id', $grupo->producto_id)
                ->where('color_nombre', $grupo->color_nombre)
                ->update(['producto_color_id' => $colorId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto_imagen', function (Blueprint $table) {
            $table->dropForeign(['producto_color_id']);
            $table->dropColumn('producto_color_id');
        });
    }
};
