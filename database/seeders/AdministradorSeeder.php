<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Crea el primer administrador para poder entrar al panel en una instalación nueva.
 * No hace nada si ya existe algún administrador.
 *
 * Datos tomados del .env (opcionales):
 *   ADMIN_CORREO=admin@paintingmistery.com
 *   ADMIN_PASSWORD=...
 * Si no hay contraseña en el .env, se genera una y se muestra una sola vez en la consola.
 */
class AdministradorSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = Rol::where('nombre', 'Administrador')->firstOrFail();

        if ($rolAdmin->usuarios()->exists()) {
            $this->command?->info('Ya existe un administrador; no se crea otro.');
            return;
        }

        $correo   = env('ADMIN_CORREO', 'admin@paintingmistery.com');
        $password = env('ADMIN_PASSWORD');
        $generada = false;

        if (! $password) {
            $password = Str::password(14);
            $generada = true;
        }

        $admin = Usuario::firstOrCreate(
            ['correo' => $correo],
            [
                'tipo_documento_id'    => TipoDocumento::where('abreviatura', 'CC')->value('id'),
                'numero_documento'     => '0000000000',
                'primer_nombre'        => 'Administrador',
                'primer_apellido'      => 'Painting Mistery',
                'password'             => $password, // el modelo la guarda cifrada
                'estado'               => true,
                'correo_verificado_at' => now(),
            ]
        );

        $admin->roles()->syncWithoutDetaching([$rolAdmin->id]);

        $this->command?->info("Administrador creado: {$correo}");
        if ($generada) {
            $this->command?->warn("Contraseña generada: {$password}  (cámbiala al entrar; no se vuelve a mostrar)");
        }
    }
}
