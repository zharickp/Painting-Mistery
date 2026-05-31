<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('verification_code', 6)->nullable()->after('estado');
            $table->timestamp('code_expires_at')->nullable()->after('verification_code');
            $table->timestamp('correo_verificado_at')->nullable()->after('code_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'code_expires_at', 'correo_verificado_at']);
        });
    }
};
