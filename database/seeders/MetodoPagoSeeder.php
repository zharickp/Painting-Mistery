<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

/**
 * en_linea = true: se ofrece al cliente al comprar en la tienda.
 * "Efectivo" se usa cuando el pago se confirma a mano desde el panel.
 */
class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Tarjeta de crédito', 'descripcion' => 'Visa, Mastercard, American Express',       'en_linea' => true],
            ['nombre' => 'Tarjeta débito',     'descripcion' => 'Tarjeta débito de cualquier banco',        'en_linea' => true],
            ['nombre' => 'PSE',                'descripcion' => 'Débito desde cuenta bancaria',             'en_linea' => true],
            ['nombre' => 'Nequi',              'descripcion' => 'Pago desde la app Nequi',                  'en_linea' => true],
            ['nombre' => 'Daviplata',          'descripcion' => 'Pago desde la app Daviplata',              'en_linea' => true],
            ['nombre' => 'Efectivo',           'descripcion' => 'Pago confirmado manualmente en el taller', 'en_linea' => false],
        ];

        foreach ($metodos as $m) {
            MetodoPago::firstOrCreate(
                ['nombre' => $m['nombre']],
                ['descripcion' => $m['descripcion'], 'en_linea' => $m['en_linea'], 'estado' => true]
            );
        }
    }
}
