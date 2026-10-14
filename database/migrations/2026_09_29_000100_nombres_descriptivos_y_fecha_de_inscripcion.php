<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 1) Nombres de atributos más claros y en español (solo se renombran; no se pierden datos).
 * 2) La inscripción apunta a la fecha del curso que eligió el cliente (curso_fecha_id)
 *    en vez de guardar dos fechas sueltas (fecha_preferida y fecha_confirmada).
 * 3) La inscripción ya no guarda venta_id: su venta se encuentra por detalle_venta_curso
 *    (venta del mismo usuario que incluye ese curso), igual que los productos.
 * 4) venta.confirmado_por y venta.cancelado_por quedan como llaves foráneas a usuario.
 */
return new class extends Migration
{
    /** tabla => [nombre actual => nombre nuevo] */
    private array $renombres = [
        'usuario' => [
            'verification_code'    => 'codigo_verificacion',
            'code_expires_at'      => 'vencimiento_codigo',
            'correo_verificado_at' => 'fecha_verificacion_correo',
        ],
        'venta' => [
            'pago_confirmado_por' => 'confirmado_por',
            'cancelada_at'        => 'fecha_cancelacion',
            'cancelada_por'       => 'cancelado_por',
        ],
        'auditoria' => [
            'registro_id'       => 'registro_afectado',
            'registro_etiqueta' => 'nombre_registro',
            'ip_address'        => 'direccion_ip',
            'user_agent'        => 'navegador',
        ],
        'curso' => [
            'costo'      => 'precio',
            'requisitos' => 'materiales',
        ],
        'tipo_iva' => [
            'descripcion' => 'nombre',
        ],
        'producto_color' => [
            'hex' => 'codigo_color',
        ],
        'metodo_pago' => [
            'en_linea' => 'disponible_para_cliente',
        ],
        'inscripcion' => [
            'notas' => 'indicaciones',
        ],
        'banner' => [
            'activo'      => 'estado',
            'publicar_en' => 'fecha_publicacion',
        ],
    ];

    public function up(): void
    {
        // La tabla de banners pasa a singular, como las demás.
        if (Schema::hasTable('banners') && ! Schema::hasTable('banner')) {
            Schema::rename('banners', 'banner');
        }

        foreach ($this->renombres as $tabla => $columnas) {
            foreach ($columnas as $actual => $nuevo) {
                if (Schema::hasColumn($tabla, $actual) && ! Schema::hasColumn($tabla, $nuevo)) {
                    Schema::table($tabla, fn (Blueprint $t) => $t->renameColumn($actual, $nuevo));
                }
            }
        }

        // ── Quién confirmó / canceló una venta: llaves foráneas a usuario ──
        foreach (['confirmado_por', 'cancelado_por'] as $col) {
            DB::statement("UPDATE venta SET {$col} = NULL WHERE {$col} IS NOT NULL AND {$col} NOT IN (SELECT id FROM usuario)");
        }
        $this->agregarFkSiFalta('venta', 'confirmado_por', 'venta_confirmado_por_foreign');
        $this->agregarFkSiFalta('venta', 'cancelado_por', 'venta_cancelado_por_foreign');

        // ── Inscripción: fecha del curso elegida ──
        if (! Schema::hasColumn('inscripcion', 'curso_fecha_id')) {
            Schema::table('inscripcion', function (Blueprint $t) {
                $t->foreignId('curso_fecha_id')->nullable()->after('curso_id')
                    ->constrained('curso_fecha')->restrictOnDelete();
            });
        }

        if (Schema::hasColumn('inscripcion', 'fecha_preferida')) {
            $inscripciones = DB::table('inscripcion')
                ->whereNull('curso_fecha_id')
                ->where(fn ($q) => $q->whereNotNull('fecha_confirmada')->orWhereNotNull('fecha_preferida'))
                ->get(['id', 'curso_id', 'fecha_preferida', 'fecha_confirmada']);

            foreach ($inscripciones as $i) {
                $fecha = $i->fecha_confirmada ?? $i->fecha_preferida;

                $fechaId = DB::table('curso_fecha')->where('curso_id', $i->curso_id)->whereDate('fecha', $fecha)->value('id');

                // Si esa fecha ya no estaba publicada, se registra para no perder el dato.
                if (! $fechaId) {
                    $fechaId = DB::table('curso_fecha')->insertGetId([
                        'curso_id'   => $i->curso_id,
                        'fecha'      => $fecha,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('inscripcion')->where('id', $i->id)->update(['curso_fecha_id' => $fechaId]);
            }
        }

        $sobran = array_values(array_filter(
            ['fecha_preferida', 'fecha_confirmada', 'venta_id'],
            fn ($c) => Schema::hasColumn('inscripcion', $c)
        ));
        if ($sobran) {
            if (in_array('venta_id', $sobran, true)) {
                DB::statement('ALTER TABLE inscripcion DROP CONSTRAINT IF EXISTS inscripcion_venta_id_fkey');
                DB::statement('ALTER TABLE inscripcion DROP CONSTRAINT IF EXISTS inscripcion_venta_id_foreign');
            }
            Schema::table('inscripcion', fn (Blueprint $t) => $t->dropColumn($sobran));
        }
    }

    public function down(): void
    {
        Schema::table('inscripcion', function (Blueprint $t) {
            $t->date('fecha_preferida')->nullable();
            $t->date('fecha_confirmada')->nullable();
            $t->foreignId('venta_id')->nullable()->constrained('venta')->nullOnDelete();
        });

        DB::statement('UPDATE inscripcion i SET fecha_preferida = cf.fecha, fecha_confirmada = cf.fecha FROM curso_fecha cf WHERE cf.id = i.curso_fecha_id');

        Schema::table('inscripcion', function (Blueprint $t) {
            $t->dropConstrainedForeignId('curso_fecha_id');
        });

        DB::statement('ALTER TABLE venta DROP CONSTRAINT IF EXISTS venta_confirmado_por_foreign');
        DB::statement('ALTER TABLE venta DROP CONSTRAINT IF EXISTS venta_cancelado_por_foreign');

        foreach (array_reverse($this->renombres, true) as $tabla => $columnas) {
            foreach ($columnas as $actual => $nuevo) {
                if (Schema::hasColumn($tabla, $nuevo) && ! Schema::hasColumn($tabla, $actual)) {
                    Schema::table($tabla, fn (Blueprint $t) => $t->renameColumn($nuevo, $actual));
                }
            }
        }

        if (Schema::hasTable('banner') && ! Schema::hasTable('banners')) {
            Schema::rename('banner', 'banners');
        }
    }

    private function agregarFkSiFalta(string $tabla, string $columna, string $nombre): void
    {
        $existe = DB::selectOne(
            'SELECT 1 FROM pg_constraint WHERE conname = ?',
            [$nombre]
        );

        if (! $existe) {
            DB::statement("ALTER TABLE {$tabla} ADD CONSTRAINT {$nombre} FOREIGN KEY ({$columna}) REFERENCES usuario(id) ON DELETE SET NULL");
        }
    }
};
