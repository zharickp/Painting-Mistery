<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Datos repetidos que se eliminan:
 *
 * 1) producto.imagen guardaba la ruta de la portada, que ya existía (o debía
 *    existir) en producto_imagen. Ahora la portada es la foto de
 *    producto_imagen con es_portada = true (máximo una por producto).
 *
 * 2) curso.fecha_inicio y curso.fecha_fin duplicaban lo que ya guarda
 *    curso_fecha (un curso tiene varias fechas). Se usa solo curso_fecha.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1) Portada ─────────────────────────────────────────────
        if (! Schema::hasColumn('producto_imagen', 'es_portada')) {
            Schema::table('producto_imagen', function (Blueprint $table) {
                $table->boolean('es_portada')->default(false);
            });
        }

        if (Schema::hasColumn('producto', 'imagen')) {
            $productos = DB::table('producto')->whereNotNull('imagen')->where('imagen', '<>', '')->get(['id', 'imagen']);

            foreach ($productos as $p) {
                $foto = DB::table('producto_imagen')
                    ->where('producto_id', $p->id)
                    ->where('ruta', $p->imagen)
                    ->orderBy('id')
                    ->first();

                if ($foto) {
                    DB::table('producto_imagen')->where('id', $foto->id)->update(['es_portada' => true]);
                } else {
                    // La portada era un archivo que no estaba en la galería: se agrega como primera foto.
                    $orden = (int) DB::table('producto_imagen')->where('producto_id', $p->id)->min('orden');
                    DB::table('producto_imagen')->insert([
                        'producto_id'       => $p->id,
                        'ruta'              => $p->imagen,
                        'orden'             => $orden - 1,
                        'producto_color_id' => null,
                        'es_portada'        => true,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }

            Schema::table('producto', function (Blueprint $table) {
                $table->dropColumn('imagen');
            });
        }

        // Solo una portada por producto.
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS producto_imagen_una_portada ON producto_imagen (producto_id) WHERE es_portada');

        // ── 2) Fechas del curso ────────────────────────────────────
        $sobran = array_values(array_filter(['fecha_inicio', 'fecha_fin'], fn ($c) => Schema::hasColumn('curso', $c)));
        if ($sobran) {
            Schema::table('curso', function (Blueprint $table) use ($sobran) {
                $table->dropColumn($sobran);
            });
        }
    }

    public function down(): void
    {
        Schema::table('curso', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
        });

        Schema::table('producto', function (Blueprint $table) {
            $table->string('imagen')->nullable();
        });

        DB::statement('UPDATE producto p SET imagen = i.ruta FROM producto_imagen i WHERE i.producto_id = p.id AND i.es_portada');
        DB::statement('DROP INDEX IF EXISTS producto_imagen_una_portada');

        Schema::table('producto_imagen', function (Blueprint $table) {
            $table->dropColumn('es_portada');
        });
    }
};
