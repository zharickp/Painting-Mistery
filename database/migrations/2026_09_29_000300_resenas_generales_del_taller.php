<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las reseñas pasan a ser del taller en general, no de un producto:
 * cada cliente deja una sola reseña (que puede editar). Si un cliente tenía
 * varias (una por producto), se conserva la más reciente.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('resena', 'producto_id')) {
            return;
        }

        DB::statement('
            DELETE FROM resena r
            USING resena r2
            WHERE r.usuario_id = r2.usuario_id
              AND (r.updated_at < r2.updated_at OR (r.updated_at = r2.updated_at AND r.id < r2.id))
        ');

        DB::statement('ALTER TABLE resena DROP CONSTRAINT IF EXISTS resena_producto_id_usuario_id_unique');
        DB::statement('DROP INDEX IF EXISTS resena_producto_id_usuario_id_unique');

        Schema::table('resena', function (Blueprint $t) {
            $t->dropConstrainedForeignId('producto_id');
            $t->unique('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('resena', function (Blueprint $t) {
            $t->dropUnique(['usuario_id']);
            $t->foreignId('producto_id')->nullable()->constrained('producto')->cascadeOnDelete();
        });
    }
};
