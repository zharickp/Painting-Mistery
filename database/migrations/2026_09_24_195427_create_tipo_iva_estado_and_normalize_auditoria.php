<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `tipo_iva` pertenece a otro rol de la BD (no se puede ALTER), por eso el estado
     * activo/inactivo vive en una tabla auxiliar 1:1. Sin fila = activo.
     */
    public function up(): void
    {
        if (! Schema::hasTable('tipo_iva_estado')) {
            Schema::create('tipo_iva_estado', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tipo_iva_id')->unique()->constrained('tipo_iva')->cascadeOnDelete();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Acciones antiguas guardadas con la etiqueta en vez de la clave.
        foreach (['Creado' => 'creado', 'Actualizado' => 'actualizado', 'Eliminado' => 'eliminado', 'Inicio de sesión' => 'login'] as $malo => $bueno) {
            DB::table('auditoria')->where('accion', $malo)->update(['accion' => $bueno]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_iva_estado');
    }
};
