<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Limpieza de columnas y tablas que repetían información o no se usaban:
 *
 * - users, password_reset_tokens: tablas por defecto de Laravel; el sistema usa `usuario`
 *   y la recuperación de contraseña guarda su código en `usuario`.
 * - inventario.ultima_actualizacion: igual a updated_at.
 * - curso.duracion: repetía `dias` (a veces contradiciéndolo). El texto se arma con `dias`.
 * - banners.subtitulo / boton_texto / boton_enlace: nunca se mostraban. `titulo` se queda
 *   como nombre interno y texto alternativo de la imagen.
 * - venta.acepto_terminos: siempre era verdadero (la compra exige aceptar los términos).
 * - carrito_detalle.precio_unitario: el carrito mostraba el precio del momento en que se
 *   agregó, pero se cobraba el precio actual. Ahora siempre se usa el precio del producto.
 * - auditoria.created_at / updated_at: la fecha del movimiento ya está en `fecha`
 *   y un registro de auditoría nunca se modifica.
 */
return new class extends Migration
{
    private array $columnas = [
        'inventario'      => ['ultima_actualizacion'],
        'curso'           => ['duracion'],
        'banners'         => ['subtitulo', 'boton_texto', 'boton_enlace'],
        'venta'           => ['acepto_terminos'],
        'carrito_detalle' => ['precio_unitario'],
        'auditoria'       => ['created_at', 'updated_at'],
    ];

    public function up(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        foreach ($this->columnas as $tabla => $columnas) {
            $existen = array_values(array_filter($columnas, fn ($c) => Schema::hasColumn($tabla, $c)));
            if ($existen) {
                Schema::table($tabla, fn (Blueprint $t) => $t->dropColumn($existen));
            }
        }
    }

    public function down(): void
    {
        Schema::table('auditoria', fn (Blueprint $t) => $t->timestamps());
        Schema::table('carrito_detalle', fn (Blueprint $t) => $t->decimal('precio_unitario', 10, 2)->default(0));
        Schema::table('venta', fn (Blueprint $t) => $t->boolean('acepto_terminos')->default(true));
        Schema::table('banners', function (Blueprint $t) {
            $t->string('subtitulo')->nullable();
            $t->string('boton_texto')->nullable();
            $t->string('boton_enlace')->nullable();
        });
        Schema::table('curso', fn (Blueprint $t) => $t->string('duracion')->nullable());
        Schema::table('inventario', fn (Blueprint $t) => $t->timestamp('ultima_actualizacion')->nullable());

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
};
