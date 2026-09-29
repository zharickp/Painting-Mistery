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
            ['porcentaje' => 0,  'descripcion' => 'Exento'],
            ['porcentaje' => 5,  'descripcion' => 'Tarifa reducida'],
            ['porcentaje' => 19, 'descripcion' => 'Tarifa general'],
        ];

        foreach ($tarifas as $t) {
            TipoIva::firstOrCreate(
                ['porcentaje' => $t['porcentaje']],
                ['descripcion' => $t['descripcion'], 'estado' => true]
            );
        }
    }
}
