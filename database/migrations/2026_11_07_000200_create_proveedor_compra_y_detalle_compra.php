<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Proveedores y compras (entradas de inventario).
 *
 *   proveedor 1 ──< compra 1 ──< detalle_compra >── 1 producto
 *
 * - proveedor: a quién se le compra la mercancía.
 * - compra: cada pedido que llega de un proveedor (con su factura).
 * - detalle_compra: qué productos llegaron, cuántos y a qué costo.
 *
 * Al registrar una compra el stock de cada producto sube. Una compra no se
 * edita ni se borra: si se registró mal se anula, y al anularla se restan
 * esas unidades del inventario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('nit', 30)->nullable()->unique();
            $table->string('persona_contacto', 120)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('correo', 120)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedor')->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuario')->nullOnDelete();
            $table->string('numero_factura', 60)->nullable();
            $table->date('fecha');
            $table->decimal('total', 12, 2)->default(0);
            $table->string('estado', 15)->default('registrada');
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_anulacion')->nullable();
            $table->foreignId('anulada_por')->nullable()->constrained('usuario')->nullOnDelete();
            $table->string('motivo_anulacion', 255)->nullable();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE compra ADD CONSTRAINT compra_estado_check CHECK (estado IN ('registrada', 'anulada'))");

        Schema::create('detalle_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compra')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('producto')->restrictOnDelete();
            $table->integer('cantidad');
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
        DB::statement('ALTER TABLE detalle_compra ADD CONSTRAINT detalle_compra_cantidad_check CHECK (cantidad > 0)');
        DB::statement('ALTER TABLE detalle_compra ADD CONSTRAINT detalle_compra_costo_check CHECK (costo_unitario >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compra');
        Schema::dropIfExists('compra');
        Schema::dropIfExists('proveedor');
    }
};
