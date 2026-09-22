<?php

namespace Database\Seeders;

use App\Models\TarifaEnvio;
use Illuminate\Database\Seeder;

/**
 * Tarifas de envío INICIALES. Reflejan el mínimo aproximado que cobran
 * la mayoría de transportadoras nacionales (Servientrega, Interrapidísimo,
 * Coordinadora, etc.) para envíos cercanos ($20.000-$30.000) y escalan
 * según la distancia al taller (Melgar, Tolima). Ajústalas desde
 * /admin/tarifas-envio cuando definas acuerdos concretos con tu
 * transportadora — estas son un punto de partida realista, no una
 * cotización oficial.
 */
class TarifaEnvioSeeder extends Seeder
{
    public function run(): void
    {
        $tarifas = [
            // Local — Melgar (donde queda el taller) y zona cercana en Tolima
            ['departamento' => 'Tolima',       'ciudad' => 'Melgar',   'precio_base' => 20000, 'umbral_envio_gratis' => 250000],
            ['departamento' => 'Tolima',       'ciudad' => 'Girardot', 'precio_base' => 25000, 'umbral_envio_gratis' => 280000],
            ['departamento' => 'Tolima',       'ciudad' => 'Ibagué',   'precio_base' => 28000, 'umbral_envio_gratis' => 320000],
            ['departamento' => 'Tolima',       'ciudad' => null,       'precio_base' => 30000, 'umbral_envio_gratis' => 350000],

            // Cundinamarca / Bogotá
            ['departamento' => 'Cundinamarca', 'ciudad' => 'Bogotá',   'precio_base' => 32000, 'umbral_envio_gratis' => 380000],
            ['departamento' => 'Cundinamarca', 'ciudad' => null,       'precio_base' => 35000, 'umbral_envio_gratis' => 420000],

            // Otras zonas populares (más lejos del taller)
            ['departamento' => 'Antioquia',    'ciudad' => 'Medellín', 'precio_base' => 38000, 'umbral_envio_gratis' => 450000],
            ['departamento' => 'Valle del Cauca','ciudad' => 'Cali',   'precio_base' => 40000, 'umbral_envio_gratis' => 480000],
            ['departamento' => 'Atlántico',    'ciudad' => 'Barranquilla', 'precio_base' => 45000, 'umbral_envio_gratis' => 500000],

            // Comodín (cualquier otro destino nacional sin tarifa específica)
            ['departamento' => null,           'ciudad' => null,       'precio_base' => 50000, 'umbral_envio_gratis' => 600000],
        ];

        foreach ($tarifas as $t) {
            TarifaEnvio::firstOrCreate(
                ['departamento' => $t['departamento'], 'ciudad' => $t['ciudad']],
                array_merge($t, ['activo' => true])
            );
        }
    }
}
