<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Rol::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso total al sistema']
        );

        Rol::firstOrCreate(
            ['nombre' => 'Asesor'],
            ['descripcion' => 'Gestiona productos, ventas y atención al cliente']
        );

        Rol::firstOrCreate(
            ['nombre' => 'Cliente'],
            ['descripcion' => 'Cliente registrado']
        );

        Rol::firstOrCreate(
            ['nombre' => 'Gerente'],
            ['descripcion' => 'Visualiza reportes, ventas e inventario del negocio']
        );

        TipoDocumento::firstOrCreate(
            ['abreviatura' => 'CC'],
            ['nombre' => 'Cédula de ciudadanía']
        );

        TipoDocumento::firstOrCreate(
            ['abreviatura' => 'CE'],
            ['nombre' => 'Cédula de extranjería']
        );

        TipoDocumento::firstOrCreate(
            ['abreviatura' => 'PA'],
            ['nombre' => 'Pasaporte']
        );
    }
}
