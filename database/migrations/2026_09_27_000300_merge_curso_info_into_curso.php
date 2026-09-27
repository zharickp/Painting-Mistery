<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los datos de agendamiento del curso (ubicación, duración, requisitos,
 * certificado y número de días) pasan a ser columnas de `curso` y se
 * elimina la tabla auxiliar 1:1 `curso_info`.
 *
 * `curso_fecha` NO se une: un curso tiene varias fechas (relación 1 a muchos).
 *
 * REQUISITO: `curso` debe pertenecer al usuario de la aplicación
 * (ver database/sql/permisos_pmistery.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curso', function (Blueprint $table) {
            if (! Schema::hasColumn('curso', 'ubicacion')) {
                $table->string('ubicacion')->nullable();
            }
            if (! Schema::hasColumn('curso', 'duracion')) {
                $table->string('duracion')->nullable();
            }
            if (! Schema::hasColumn('curso', 'requisitos')) {
                $table->text('requisitos')->nullable();
            }
            if (! Schema::hasColumn('curso', 'incluye_certificado')) {
                $table->boolean('incluye_certificado')->default(true);
            }
            if (! Schema::hasColumn('curso', 'dias')) {
                $table->unsignedTinyInteger('dias')->default(1);
            }
        });

        if (Schema::hasTable('curso_info')) {
            $tieneDias = Schema::hasColumn('curso_info', 'dias');

            DB::statement('
                UPDATE curso c
                   SET ubicacion           = i.ubicacion,
                       duracion            = i.duracion,
                       requisitos          = i.requisitos,
                       incluye_certificado = i.incluye_certificado'
                       . ($tieneDias ? ', dias = COALESCE(i.dias, 1)' : '') . '
                  FROM curso_info i
                 WHERE i.curso_id = c.id
            ');

            Schema::drop('curso_info');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('curso_info')) {
            Schema::create('curso_info', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_id')->unique()->constrained('curso')->cascadeOnDelete();
                $table->string('ubicacion')->nullable();
                $table->string('duracion')->nullable();
                $table->text('requisitos')->nullable();
                $table->boolean('incluye_certificado')->default(true);
                $table->unsignedTinyInteger('dias')->default(1);
                $table->timestamps();
            });
        }

        DB::statement('
            INSERT INTO curso_info (curso_id, ubicacion, duracion, requisitos, incluye_certificado, dias, created_at, updated_at)
            SELECT id, ubicacion, duracion, requisitos, incluye_certificado, dias, now(), now() FROM curso
        ');

        Schema::table('curso', function (Blueprint $table) {
            $table->dropColumn(['ubicacion', 'duracion', 'requisitos', 'incluye_certificado', 'dias']);
        });
    }
};
