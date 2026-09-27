<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La agenda de la inscripción (fecha elegida, fecha confirmada, notas) pasa a
 * `inscripcion` y se elimina la tabla auxiliar 1:1 `inscripcion_agenda`.
 *
 * Además, `inscripcion.estado` guarda directamente el estado real de la
 * solicitud: pendiente | confirmada | completada | cancelada.
 * Antes solo admitía 'inscrito' | 'cancelado' (CHECK) y el estado detallado
 * vivía en inscripcion_agenda.estado_solicitud.
 *
 * REQUISITO: `inscripcion` debe pertenecer al usuario de la aplicación
 * (ver database/sql/permisos_pmistery.sql).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscripcion', function (Blueprint $table) {
            if (! Schema::hasColumn('inscripcion', 'fecha_preferida')) {
                $table->date('fecha_preferida')->nullable();
            }
            if (! Schema::hasColumn('inscripcion', 'fecha_confirmada')) {
                $table->date('fecha_confirmada')->nullable();
            }
            if (! Schema::hasColumn('inscripcion', 'notas')) {
                $table->text('notas')->nullable();
            }
        });

        // Se quita la restricción vieja ('inscrito' | 'cancelado') antes de cambiar valores.
        DB::statement('ALTER TABLE inscripcion DROP CONSTRAINT IF EXISTS inscripcion_estado_check');

        $hayAgenda = Schema::hasTable('inscripcion_agenda');
        $hayEstadoSolicitud = $hayAgenda && Schema::hasColumn('inscripcion_agenda', 'estado_solicitud');

        if ($hayAgenda) {
            DB::statement('
                UPDATE inscripcion i
                   SET fecha_preferida  = a.fecha_preferida,
                       fecha_confirmada = a.fecha_confirmada,
                       notas            = a.notas
                  FROM inscripcion_agenda a
                 WHERE a.inscripcion_id = i.id
            ');
        }

        $estadoDetallado = $hayEstadoSolicitud
            ? "(SELECT a.estado_solicitud FROM inscripcion_agenda a WHERE a.inscripcion_id = i.id)"
            : 'NULL';

        DB::statement("
            UPDATE inscripcion i
               SET estado = CASE
                   WHEN i.estado IN ('cancelado', 'cancelada') THEN 'cancelada'
                   WHEN i.estado IN ('pendiente', 'confirmada', 'completada') THEN i.estado
                   ELSE COALESCE({$estadoDetallado}, 'pendiente')
               END
        ");

        DB::statement("ALTER TABLE inscripcion ALTER COLUMN estado SET DEFAULT 'pendiente'");
        DB::statement("
            ALTER TABLE inscripcion ADD CONSTRAINT inscripcion_estado_check
            CHECK (estado IN ('pendiente', 'confirmada', 'completada', 'cancelada'))
        ");

        if ($hayAgenda) {
            Schema::drop('inscripcion_agenda');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('inscripcion_agenda')) {
            Schema::create('inscripcion_agenda', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->unique()->constrained('inscripcion')->cascadeOnDelete();
                $table->date('fecha_preferida')->nullable();
                $table->date('fecha_confirmada')->nullable();
                $table->text('notas')->nullable();
                $table->string('estado_solicitud', 15)->default('pendiente');
                $table->timestamps();
            });
        }

        DB::statement("
            INSERT INTO inscripcion_agenda (inscripcion_id, fecha_preferida, fecha_confirmada, notas, estado_solicitud, created_at, updated_at)
            SELECT id, fecha_preferida, fecha_confirmada, notas, estado, now(), now() FROM inscripcion
        ");

        DB::statement('ALTER TABLE inscripcion DROP CONSTRAINT IF EXISTS inscripcion_estado_check');
        DB::statement("UPDATE inscripcion SET estado = CASE WHEN estado = 'cancelada' THEN 'cancelado' ELSE 'inscrito' END");
        DB::statement("ALTER TABLE inscripcion ALTER COLUMN estado SET DEFAULT 'inscrito'");
        DB::statement("ALTER TABLE inscripcion ADD CONSTRAINT inscripcion_estado_check CHECK (estado IN ('inscrito', 'cancelado'))");

        Schema::table('inscripcion', function (Blueprint $table) {
            $table->dropColumn(['fecha_preferida', 'fecha_confirmada', 'notas']);
        });
    }
};
