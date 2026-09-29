<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

/** Roles del sistema. Los nombres se usan en el código (middleware role:...), no cambiarlos. */
class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Administrador' => 'Acceso total al sistema',
            'Gerente'       => 'Consulta ventas, inventario, reportes y auditoría',
            'Asesor'        => 'Gestiona productos, cursos, ventas, inventario y reseñas',
            'Cliente'       => 'Compra productos y se inscribe a cursos',
        ];

        foreach ($roles as $nombre => $descripcion) {
            Rol::firstOrCreate(['nombre' => $nombre], ['descripcion' => $descripcion]);
        }
    }
}
