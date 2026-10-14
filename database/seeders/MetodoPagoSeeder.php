<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

/** Métodos de pago que el cliente puede elegir al pagar en la página. */
class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Tarjeta de crédito', 'descripcion' => 'Visa, Mastercard, American Express'],
            ['nombre' => 'Tarjeta débito',     'descripcion' => 'Tarjeta débito de cualquier banco'],
            ['nombre' => 'PSE',                'descripcion' => 'Débito desde cuenta bancaria'],
            ['nombre' => 'Nequi',              'descripcion' => 'Pago desde la app Nequi'],
            ['nombre' => 'Daviplata',          'descripcion' => 'Pago desde la app Daviplata'],
        ];

        foreach ($metodos as $m) {
            MetodoPago::firstOrCreate(
                ['nombre' => $m['nombre']],
                ['descripcion' => $m['descripcion'], 'estado' => true]
            );
        }
    }
}
