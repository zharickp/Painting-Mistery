<?php

namespace App\Console\Commands;

use App\Services\AuditoriaService;
use App\Services\BackupService;
use Illuminate\Console\Command;

class GenerateBackupCommand extends Command
{
    protected $signature = 'respaldos:generar
                            {--si-corresponde : Solo genera la copia si todavía no se hizo la automática de hoy}';

    protected $description = 'Genera una copia de seguridad automática de la base de datos.';

    public function handle(BackupService $backup): int
    {
        if ($this->option('si-corresponde')) {
            if ($backup->automaticaDeHoyHecha()) {
                $this->line('La copia automática de hoy ya está hecha.');
                return self::SUCCESS;
            }
        }

        try {
            $nombre = $backup->generar(automatica: true);
        } catch (\Throwable $e) {
            $this->error('No fue posible generar la copia: ' . $e->getMessage());
            report($e);
            return self::FAILURE;
        }

        AuditoriaService::registrar([
            'accion'            => 'creado',
            'modulo'            => 'Copia de seguridad',
            'nombre_registro' => $nombre,
            'usuario_nombre'    => 'Sistema',
            'descripcion'       => "Copia de seguridad automática {$nombre}",
        ]);

        $borradas = $backup->limpiarAutomaticas();

        $this->info("Copia generada: {$nombre}" . ($borradas ? " (se borraron {$borradas} automáticas antiguas)" : ''));

        return self::SUCCESS;
    }
}
