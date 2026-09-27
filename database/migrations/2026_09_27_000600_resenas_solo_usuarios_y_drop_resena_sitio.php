<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reseñas:
 *  - Se elimina `resena_sitio` (testimonios anónimos del inicio). Los testimonios
 *    del inicio ahora salen de `resena`.
 *  - Toda reseña de `resena` queda ligada a un usuario:
 *      1) las reseñas de invitados cuyo correo coincide con un usuario
 *         registrado se asignan a ese usuario (si no tenía ya una reseña
 *         del mismo producto);
 *      2) las reseñas de invitados que no se pudieron asignar se eliminan;
 *      3) se quitan las columnas de invitado y usuario_id vuelve a ser obligatorio.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('resena', 'correo_invitado')) {
            DB::statement('
                UPDATE resena r
                   SET usuario_id = u.id
                  FROM usuario u
                 WHERE r.usuario_id IS NULL
                   AND r.correo_invitado IS NOT NULL
                   AND LOWER(u.correo) = LOWER(r.correo_invitado)
                   AND NOT EXISTS (
                       SELECT 1 FROM resena r2
                        WHERE r2.producto_id = r.producto_id
                          AND r2.usuario_id = u.id
                   )
            ');
        }

        DB::table('resena')->whereNull('usuario_id')->delete();

        Schema::table('resena', function (Blueprint $table) {
            $quitar = array_values(array_filter(
                ['nombre_invitado', 'correo_invitado'],
                fn ($c) => Schema::hasColumn('resena', $c)
            ));
            if ($quitar) {
                $table->dropColumn($quitar);
            }
        });

        DB::statement('ALTER TABLE resena ALTER COLUMN usuario_id SET NOT NULL');

        Schema::dropIfExists('resena_sitio');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE resena ALTER COLUMN usuario_id DROP NOT NULL');

        Schema::table('resena', function (Blueprint $table) {
            if (! Schema::hasColumn('resena', 'nombre_invitado')) {
                $table->string('nombre_invitado')->nullable();
            }
            if (! Schema::hasColumn('resena', 'correo_invitado')) {
                $table->string('correo_invitado')->nullable();
            }
        });

        if (! Schema::hasTable('resena_sitio')) {
            Schema::create('resena_sitio', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 80);
                $table->unsignedTinyInteger('calificacion');
                $table->text('comentario');
                $table->string('estado', 15)->default('pendiente');
                $table->timestamps();
            });
        }
    }
};
