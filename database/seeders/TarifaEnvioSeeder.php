<?php

namespace Database\Seeders;

use App\Models\TarifaEnvio;
use Illuminate\Database\Seeder;

/**
 * Tarifas de envío INICIALES (sandbox). NO son tarifas reales de negocio.
 * Ajústalas desde la interfaz de administración cuando definas
 * los precios reales con la transportadora.
 */
class TarifaEnvioSeeder extends Seeder
{
    public function run(): void
    {
        $tarifas = [
            // Local — Melgar (donde queda el taller)
            ['departamento' => 'Tolima',       'ciudad' => 'Melgar',   'precio_base' => 8000,  'umbral_envio_gratis' => 200000],
            ['departamento' => 'Tolima',       'ciudad' => 'Girardot', 'precio_base' => 10000, 'umbral_envio_gratis' => 250000],
            ['departamento' => 'Tolima',       'ciudad' => 'Ibagué',   'precio_base' => 12000, 'umbral_envio_gratis' => 300000],
            ['departamento' => 'Tolima',       'ciudad' => null,       'precio_base' => 15000, 'umbral_envio_gratis' => 300000],

            // Cundinamarca / Bogotá
            ['departamento' => 'Cundinamarca', 'ciudad' => 'Bogotá',   'precio_base' => 14000, 'umbral_envio_gratis' => 300000],
            ['departamento' => 'Cundinamarca', 'ciudad' => null,       'precio_base' => 18000, 'umbral_envio_gratis' => 350000],

            // Otras zonas populares
            ['departamento' => 'Antioquia',    'ciudad' => 'Medellín', 'precio_base' => 20000, 'umbral_envio_gratis' => 400000],
            ['departamento' => 'Valle del Cauca','ciudad' => 'Cali',   'precio_base' => 22000, 'umbral_envio_gratis' => 400000],
            ['departamento' => 'Atlántico',    'ciudad' => 'Barranquilla', 'precio_base' => 25000, 'umbral_envio_gratis' => 450000],

            // Comodín (departamento sin tarifa específica)
            ['departamento' => null,           'ciudad' => null,       'precio_base' => 28000, 'umbral_envio_gratis' => 500000],
        ];

        foreach ($tarifas as $t) {
            TarifaEnvio::firstOrCreate(
                ['departamento' => $t['departamento'], 'ciudad' => $t['ciudad']],
                array_merge($t, ['activo' => true])
            );
        }
    }
}
