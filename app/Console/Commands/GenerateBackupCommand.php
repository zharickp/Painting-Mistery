<?php

namespace App\Console\Commands;

use App\Services\AuditoriaService;
use App\Services\BackupService;
use Illuminate\Console\Command;

class GenerateBackupCommand extends Command
{
    protected $signature = 'respaldos:generar
                            {--si-corresponde : Solo genera la copia si ya pasaron los días definidos desde la última automática}';

    protected $description = 'Genera una copia de seguridad automática de la base de datos.';

    public function handle(BackupService $backup): int
    {
        if ($this->option('si-corresponde')) {
            $ultima = $backup->ultimaAutomatica();

            if ($ultima && $ultima->gt(now()->subDays(BackupService::DIAS_ENTRE_AUTOMATICAS))) {
                $this->line('Todavía no corresponde una copia automática. Última: ' . $ultima->format('d/m/Y H:i'));
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
            'registro_etiqueta' => $nombre,
            'usuario_nombre'    => 'Sistema',
            'descripcion'       => "Copia de seguridad automática {$nombre}",
        ]);

        $this->info("Copia generada: {$nombre}");

        return self::SUCCESS;
    }
}
