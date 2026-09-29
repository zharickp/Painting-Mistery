<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Datos mínimos para que el sistema funcione en una instalación nueva:
 *   php artisan migrate --seed
 * Se puede volver a correr sin duplicar nada (php artisan db:seed).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            TipoDocumentoSeeder::class,
            TipoIvaSeeder::class,
            MetodoPagoSeeder::class,
            AdministradorSeeder::class,
        ]);
    }
}
