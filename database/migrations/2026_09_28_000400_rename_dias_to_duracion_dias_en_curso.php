<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** `dias` no decía qué medía: pasa a llamarse duracion_dias (cuántos días dura el curso). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('curso', 'dias') && ! Schema::hasColumn('curso', 'duracion_dias')) {
            Schema::table('curso', fn (Blueprint $t) => $t->renameColumn('dias', 'duracion_dias'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('curso', 'duracion_dias')) {
            Schema::table('curso', fn (Blueprint $t) => $t->renameColumn('duracion_dias', 'dias'));
        }
    }
};
