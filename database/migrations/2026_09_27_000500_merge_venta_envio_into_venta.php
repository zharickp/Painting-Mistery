<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los datos de la orden (número, pago Wompi, estado del pedido, dirección de
 * envío y trazabilidad de cancelación) pasan a ser columnas de `venta` y se
 * elimina la tabla auxiliar 1:1 `venta_envio`.
 *
 * Varias de estas columnas ya las define la migración
 * 2026_09_21_233701_add_orden_fields_to_venta_table; por eso cada columna se
 * agrega solo si no existe.
 *
 * REQUISITO: `venta` debe pertenecer al usuario de la aplicación
 * (ver database/sql/permisos_pmistery.sql).
 */
return new class extends Migration
{
    /** Columnas que se copian de venta_envio a venta (mismo nombre en ambas). */
    private array $columnas = [
        'numero_orden', 'wompi_reference', 'wompi_transaction_id', 'wompi_payment_method',
        'payment_status', 'estado_pedido', 'fecha_pago', 'subtotal', 'envio',
        'nombre_envio', 'telefono_envio', 'correo_envio', 'tipo_documento', 'numero_documento',
        'acepto_terminos', 'departamento_envio', 'ciudad_envio', 'direccion_envio', 'referencia_envio',
        'cancelada_at', 'cancelada_por', 'motivo_cancelacion', 'pago_confirmado_por',
    ];

    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $agregar = function (string $col, callable $def) use ($table) {
                if (! Schema::hasColumn('venta', $col)) {
                    $def($table);
                }
            };

            $agregar('numero_orden',         fn ($t) => $t->string('numero_orden', 40)->nullable()->unique());
            $agregar('wompi_reference',      fn ($t) => $t->string('wompi_reference', 60)->nullable()->unique());
            $agregar('wompi_transaction_id', fn ($t) => $t->string('wompi_transaction_id', 60)->nullable());
            $agregar('wompi_payment_method', fn ($t) => $t->string('wompi_payment_method', 40)->nullable());
            $agregar('payment_status',       fn ($t) => $t->string('payment_status', 20)->default('PENDING')->index());
            $agregar('estado_pedido',        fn ($t) => $t->string('estado_pedido', 30)->default('pendiente')->index());
            $agregar('fecha_pago',           fn ($t) => $t->timestamp('fecha_pago')->nullable());
            $agregar('subtotal',             fn ($t) => $t->decimal('subtotal', 12, 2)->nullable());
            $agregar('envio',                fn ($t) => $t->decimal('envio', 12, 2)->nullable());
            $agregar('nombre_envio',         fn ($t) => $t->string('nombre_envio', 120)->nullable());
            $agregar('telefono_envio',       fn ($t) => $t->string('telefono_envio', 40)->nullable());
            $agregar('correo_envio',         fn ($t) => $t->string('correo_envio', 120)->nullable());
            $agregar('tipo_documento',       fn ($t) => $t->string('tipo_documento', 10)->nullable());
            $agregar('numero_documento',     fn ($t) => $t->string('numero_documento', 20)->nullable());
            $agregar('acepto_terminos',      fn ($t) => $t->boolean('acepto_terminos')->default(false));
            $agregar('departamento_envio',   fn ($t) => $t->string('departamento_envio', 80)->nullable());
            $agregar('ciudad_envio',         fn ($t) => $t->string('ciudad_envio', 80)->nullable());
            $agregar('direccion_envio',      fn ($t) => $t->text('direccion_envio')->nullable());
            $agregar('referencia_envio',     fn ($t) => $t->text('referencia_envio')->nullable());
            $agregar('cancelada_at',         fn ($t) => $t->timestamp('cancelada_at')->nullable());
            $agregar('cancelada_por',        fn ($t) => $t->unsignedBigInteger('cancelada_por')->nullable());
            $agregar('motivo_cancelacion',   fn ($t) => $t->string('motivo_cancelacion', 255)->nullable());
            $agregar('pago_confirmado_por',  fn ($t) => $t->unsignedBigInteger('pago_confirmado_por')->nullable());
        });

        if (! Schema::hasTable('venta_envio')) {
            return;
        }

        // Solo se copian las columnas que realmente existen en venta_envio.
        $copiar = array_values(array_filter($this->columnas, fn ($c) => Schema::hasColumn('venta_envio', $c)));
        $set = implode(",\n", array_map(fn ($c) => "{$c} = e.{$c}", $copiar));

        DB::statement("
            UPDATE venta v
               SET {$set}
              FROM venta_envio e
             WHERE e.venta_id = v.id
        ");

        // Verificación: no se borra venta_envio si alguna orden no quedó copiada.
        $faltantes = DB::table('venta_envio as e')
            ->join('venta as v', 'v.id', '=', 'e.venta_id')
            ->whereRaw('v.numero_orden IS DISTINCT FROM e.numero_orden')
            ->count();

        if ($faltantes > 0) {
            throw new RuntimeException("{$faltantes} orden(es) no se copiaron de venta_envio a venta. No se eliminó venta_envio.");
        }

        Schema::drop('venta_envio');
    }

    public function down(): void
    {
        if (! Schema::hasTable('venta_envio')) {
            Schema::create('venta_envio', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venta_id')->unique();
                $table->string('numero_orden', 40)->unique();
                $table->string('wompi_reference', 60)->unique()->nullable();
                $table->string('wompi_transaction_id', 60)->nullable();
                $table->string('wompi_payment_method', 40)->nullable();
                $table->string('payment_status', 20)->default('PENDING');
                $table->string('estado_pedido', 30)->default('pendiente');
                $table->timestamp('fecha_pago')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('envio', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->string('nombre_envio', 120);
                $table->string('telefono_envio', 40);
                $table->string('correo_envio', 120);
                $table->string('tipo_documento', 10)->nullable();
                $table->string('numero_documento', 20)->nullable();
                $table->boolean('acepto_terminos')->default(false);
                $table->string('departamento_envio', 80);
                $table->string('ciudad_envio', 80)->nullable();
                $table->text('direccion_envio');
                $table->text('referencia_envio')->nullable();
                $table->timestamp('cancelada_at')->nullable();
                $table->unsignedBigInteger('cancelada_por')->nullable();
                $table->string('motivo_cancelacion', 255)->nullable();
                $table->unsignedBigInteger('pago_confirmado_por')->nullable();
                $table->timestamps();
                $table->index('payment_status');
                $table->index('estado_pedido');
                $table->foreign('venta_id')->references('id')->on('venta')->cascadeOnDelete();
            });
        }

        $cols = implode(', ', $this->columnas);
        DB::statement("
            INSERT INTO venta_envio (venta_id, total, {$cols}, created_at, updated_at)
            SELECT id, total, {$cols}, created_at, updated_at
              FROM venta
             WHERE numero_orden IS NOT NULL
        ");

        // Solo se quitan las columnas que agregó esta migración; las demás
        // pertenecen a 2026_09_21_233701_add_orden_fields_to_venta_table.
        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_documento', 'numero_documento', 'acepto_terminos',
                'cancelada_at', 'cancelada_por', 'motivo_cancelacion', 'pago_confirmado_por',
            ]);
        });
    }
};
