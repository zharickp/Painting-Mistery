<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hasta ahora la compra en la tienda guardaba el IVA de cada producto en 0.
 * Se calcula para las ventas ya registradas con el tipo de IVA que tiene hoy
 * cada producto. Los precios incluyen IVA: iva = subtotal × % / (100 + %).
 *
 * Solo toca filas con IVA en 0; el total de la venta no cambia.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE detalle_venta_producto AS d
               SET iva = ROUND(d.subtotal * t.porcentaje / (100 + t.porcentaje), 2)
              FROM producto AS p
              JOIN tipo_iva AS t ON t.id = p.tipo_iva_id
             WHERE p.id = d.producto_id
               AND COALESCE(d.iva, 0) = 0
               AND t.porcentaje > 0
        SQL);
    }

    public function down(): void
    {
        // No se revierte: antes de esta migración el valor guardado era un 0 incorrecto.
    }
};
