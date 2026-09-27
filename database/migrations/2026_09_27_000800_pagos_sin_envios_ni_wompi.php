<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El sistema deja de manejar envíos y Wompi. Los pagos se registran en las
 * tablas del diseño original: metodo_pago (catálogo) y pago (un registro por
 * intento de pago).
 *
 * Pasos:
 *  1) metodo_pago: columna en_linea y métodos base.
 *  2) Los pagos aprobados que ya existían (guardados en venta con datos de
 *     Wompi) se copian a la tabla pago, para no perder el historial.
 *  3) Se eliminan de venta las columnas de envío y de Wompi.
 *  4) Los datos del comprador dejan de llamarse "_envio".
 */
return new class extends Migration
{
    private array $metodosBase = [
        ['nombre' => 'Tarjeta de crédito', 'descripcion' => 'Visa, Mastercard, American Express', 'en_linea' => true,  'estado' => true],
        ['nombre' => 'Tarjeta débito',     'descripcion' => 'Tarjeta débito de cualquier banco',  'en_linea' => true,  'estado' => true],
        ['nombre' => 'PSE',                'descripcion' => 'Débito desde cuenta bancaria',       'en_linea' => true,  'estado' => true],
        ['nombre' => 'Nequi',              'descripcion' => 'Pago desde la app Nequi',            'en_linea' => true,  'estado' => true],
        ['nombre' => 'Daviplata',          'descripcion' => 'Pago desde la app Daviplata',        'en_linea' => true,  'estado' => true],
        ['nombre' => 'Efectivo',           'descripcion' => 'Pago confirmado manualmente en el taller', 'en_linea' => false, 'estado' => true],
        ['nombre' => 'Otro',               'descripcion' => 'Pagos registrados con la pasarela anterior', 'en_linea' => false, 'estado' => false],
    ];

    /** Columnas de envío y de Wompi que salen de venta. */
    private array $columnasFuera = [
        'envio', 'subtotal', 'departamento_envio', 'ciudad_envio', 'direccion_envio', 'referencia_envio',
        'estado_pedido', 'payment_status', 'wompi_reference', 'wompi_transaction_id', 'wompi_payment_method',
        'fecha_pago',
    ];

    public function up(): void
    {
        // 1) Catálogo de métodos de pago
        if (! Schema::hasColumn('metodo_pago', 'en_linea')) {
            Schema::table('metodo_pago', function (Blueprint $table) {
                $table->boolean('en_linea')->default(true);
            });
        }
        foreach ($this->metodosBase as $m) {
            DB::table('metodo_pago')->updateOrInsert(
                ['nombre' => $m['nombre']],
                $m + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2) Historial: pagos aprobados que estaban guardados en venta
        if (Schema::hasColumn('venta', 'payment_status')) {
            $id = fn (string $nombre) => DB::table('metodo_pago')->where('nombre', $nombre)->value('id');
            $mapa = [
                'CARD'                => $id('Tarjeta de crédito'),
                'PSE'                 => $id('PSE'),
                'NEQUI'               => $id('Nequi'),
                'DAVIPLATA'           => $id('Daviplata'),
                'Confirmación manual' => $id('Efectivo'),
            ];
            $otro = $id('Otro');

            $aprobadas = DB::table('venta')
                ->where('payment_status', 'APPROVED')
                ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('pago')->whereColumn('pago.venta_id', 'venta.id'))
                ->get();

            foreach ($aprobadas as $v) {
                DB::table('pago')->insert([
                    'venta_id'           => $v->id,
                    'metodo_pago_id'     => $mapa[$v->wompi_payment_method] ?? $otro,
                    'numero_comprobante' => $v->wompi_transaction_id ?: ('ANT-' . str_pad((string) $v->id, 6, '0', STR_PAD_LEFT)),
                    'valor'              => $v->total,
                    'fecha_pago'         => $v->fecha_pago ?? $v->updated_at,
                    'estado'             => 'aprobado',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }

        // 3) Fuera columnas de envío y Wompi (los índices únicos se van con la columna)
        $quitar = array_values(array_filter($this->columnasFuera, fn ($c) => Schema::hasColumn('venta', $c)));
        if ($quitar) {
            Schema::table('venta', function (Blueprint $table) use ($quitar) {
                $table->dropColumn($quitar);
            });
        }

        // 4) Datos del comprador
        foreach (['nombre', 'telefono', 'correo'] as $campo) {
            if (Schema::hasColumn('venta', "{$campo}_envio") && ! Schema::hasColumn('venta', "{$campo}_cliente")) {
                Schema::table('venta', function (Blueprint $table) use ($campo) {
                    $table->renameColumn("{$campo}_envio", "{$campo}_cliente");
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['nombre', 'telefono', 'correo'] as $campo) {
            if (Schema::hasColumn('venta', "{$campo}_cliente")) {
                Schema::table('venta', function (Blueprint $table) use ($campo) {
                    $table->renameColumn("{$campo}_cliente", "{$campo}_envio");
                });
            }
        }

        Schema::table('venta', function (Blueprint $table) {
            $table->decimal('envio', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->string('departamento_envio', 80)->nullable();
            $table->string('ciudad_envio', 80)->nullable();
            $table->text('direccion_envio')->nullable();
            $table->text('referencia_envio')->nullable();
            $table->string('estado_pedido', 30)->default('pendiente');
            $table->string('payment_status', 20)->default('PENDING');
            $table->string('wompi_reference', 60)->nullable()->unique();
            $table->string('wompi_transaction_id', 60)->nullable();
            $table->string('wompi_payment_method', 40)->nullable();
            $table->timestamp('fecha_pago')->nullable();
        });

        // Los datos de envío borrados no se pueden recuperar; los pagos quedan en la tabla pago.
        if (Schema::hasColumn('metodo_pago', 'en_linea')) {
            Schema::table('metodo_pago', function (Blueprint $table) {
                $table->dropColumn('en_linea');
            });
        }
    }
};
