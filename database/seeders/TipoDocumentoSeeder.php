<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'CC'  => 'Cédula de ciudadanía',
            'TI'  => 'Tarjeta de identidad',
            'CE'  => 'Cédula de extranjería',
            'PA'  => 'Pasaporte',
            'NIT' => 'NIT',
        ];

        foreach ($tipos as $abreviatura => $nombre) {
            TipoDocumento::firstOrCreate(['abreviatura' => $abreviatura], ['nombre' => $nombre]);
        }
    }
}
