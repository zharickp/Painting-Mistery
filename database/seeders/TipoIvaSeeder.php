<?php

namespace Database\Seeders;

use App\Models\TipoIva;
use Illuminate\Database\Seeder;

/** Tarifas de IVA vigentes en Colombia. Se busca por porcentaje para no duplicar las que ya existan. */
class TipoIvaSeeder extends Seeder
{
    public function run(): void
    {
        $tarifas = [
            ['porcentaje' => 0,  'nombre' => 'Exento'],
            ['porcentaje' => 5,  'nombre' => 'Tarifa reducida'],
            ['porcentaje' => 19, 'nombre' => 'Tarifa general'],
        ];

        foreach ($tarifas as $t) {
            TipoIva::firstOrCreate(
                ['porcentaje' => $t['porcentaje']],
                ['nombre' => $t['nombre'], 'estado' => true]
            );
        }
    }
}
