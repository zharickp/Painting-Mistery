<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El acceso se controla por rol (middleware role:... en las rutas y tieneRol()
 * en las vistas). Las tablas de permisos nunca se usaron, así que se eliminan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('roles_permisos');
        Schema::dropIfExists('permiso');
    }

    public function down(): void
    {
        Schema::create('permiso', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 40);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('rol')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permiso')->cascadeOnDelete();
            $table->unique(['rol_id', 'permiso_id']);
            $table->timestamps();
        });
    }
};
