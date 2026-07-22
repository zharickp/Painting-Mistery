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
        // doctrine/dbal no está instalado, así que la nulabilidad se cambia con SQL crudo (Postgres).
        DB::statement('ALTER TABLE resena ALTER COLUMN usuario_id DROP NOT NULL');

        Schema::table('resena', function (Blueprint $table) {
            $table->string('nombre_invitado')->nullable()->after('usuario_id');
            $table->string('correo_invitado')->nullable()->after('nombre_invitado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resena', function (Blueprint $table) {
            $table->dropColumn(['nombre_invitado', 'correo_invitado']);
        });

        DB::statement('DELETE FROM resena WHERE usuario_id IS NULL');
        DB::statement('ALTER TABLE resena ALTER COLUMN usuario_id SET NOT NULL');
    }
};
