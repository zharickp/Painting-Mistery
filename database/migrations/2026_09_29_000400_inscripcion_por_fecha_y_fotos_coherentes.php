<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 1) La inscripción queda en cadena: curso → curso_fecha → inscripcion.
 *    El estudiante se inscribe en una fecha del curso; el curso se conoce por
 *    la fecha, así que inscripcion.curso_id sobra y se elimina.
 *
 * 2) Una foto con color debe ser del mismo producto que ese color: llave
 *    foránea compuesta (producto_color_id, producto_id) → producto_color(id, producto_id).
 *    Las fotos sin color (generales) no se ven afectadas.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1) Inscripción por fecha ─────────────────────────────
        if (Schema::hasColumn('inscripcion', 'curso_id')) {
            // Inscripciones sin fecha, o con una fecha de otro curso: se les asigna una fecha de su curso.
            $sinFecha = DB::table('inscripcion as i')
                ->leftJoin('curso_fecha as cf', 'cf.id', '=', 'i.curso_fecha_id')
                ->where(fn ($q) => $q->whereNull('i.curso_fecha_id')->orWhereColumn('cf.curso_id', '<>', 'i.curso_id'))
                ->get(['i.id', 'i.curso_id', 'i.created_at']);

            foreach ($sinFecha as $i) {
                $fechaId = DB::table('curso_fecha')->where('curso_id', $i->curso_id)->orderBy('fecha')->value('id');

                if (! $fechaId) {
                    $fechaId = DB::table('curso_fecha')->insertGetId([
                        'curso_id'   => $i->curso_id,
                        'fecha'      => substr((string) ($i->created_at ?? now()), 0, 10),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('inscripcion')->where('id', $i->id)->update(['curso_fecha_id' => $fechaId]);
            }

            DB::statement('ALTER TABLE inscripcion DROP CONSTRAINT IF EXISTS inscripcion_usuario_id_curso_id_unique');
            DB::statement('DROP INDEX IF EXISTS inscripcion_usuario_id_curso_id_unique');
            DB::statement('ALTER TABLE inscripcion DROP CONSTRAINT IF EXISTS inscripcion_curso_id_foreign');

            Schema::table('inscripcion', function (Blueprint $t) {
                $t->dropColumn('curso_id');
            });

            DB::statement('ALTER TABLE inscripcion ALTER COLUMN curso_fecha_id SET NOT NULL');
        }

        // ── 2) Fotos coherentes con su color ────────────────────
        // Si alguna foto quedó con un color de otro producto, pierde el color (sigue como foto general).
        DB::statement('
            UPDATE producto_imagen pi SET producto_color_id = NULL
            FROM producto_color pc
            WHERE pc.id = pi.producto_color_id AND pc.producto_id <> pi.producto_id
        ');

        DB::statement('ALTER TABLE producto_color DROP CONSTRAINT IF EXISTS producto_color_id_producto_id_unique');
        DB::statement('ALTER TABLE producto_color ADD CONSTRAINT producto_color_id_producto_id_unique UNIQUE (id, producto_id)');

        DB::statement('ALTER TABLE producto_imagen DROP CONSTRAINT IF EXISTS producto_imagen_producto_color_id_foreign');
        DB::statement('ALTER TABLE producto_imagen DROP CONSTRAINT IF EXISTS producto_imagen_color_del_mismo_producto');
        DB::statement('
            ALTER TABLE producto_imagen ADD CONSTRAINT producto_imagen_color_del_mismo_producto
            FOREIGN KEY (producto_color_id, producto_id) REFERENCES producto_color (id, producto_id) ON DELETE CASCADE
        ');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE producto_imagen DROP CONSTRAINT IF EXISTS producto_imagen_color_del_mismo_producto');
        DB::statement('ALTER TABLE producto_imagen ADD CONSTRAINT producto_imagen_producto_color_id_foreign FOREIGN KEY (producto_color_id) REFERENCES producto_color (id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE producto_color DROP CONSTRAINT IF EXISTS producto_color_id_producto_id_unique');

        if (! Schema::hasColumn('inscripcion', 'curso_id')) {
            Schema::table('inscripcion', function (Blueprint $t) {
                $t->foreignId('curso_id')->nullable()->constrained('curso')->cascadeOnDelete();
            });
            DB::statement('UPDATE inscripcion i SET curso_id = cf.curso_id FROM curso_fecha cf WHERE cf.id = i.curso_fecha_id');
            DB::statement('ALTER TABLE inscripcion ALTER COLUMN curso_id SET NOT NULL');
            DB::statement('ALTER TABLE inscripcion ALTER COLUMN curso_fecha_id DROP NOT NULL');
            Schema::table('inscripcion', fn (Blueprint $t) => $t->unique(['usuario_id', 'curso_id']));
        }
    }
};
