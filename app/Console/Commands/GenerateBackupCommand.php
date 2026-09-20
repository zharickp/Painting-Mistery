<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class GenerateBackupCommand extends Command
{
    protected $signature = 'respaldos:generar';

    protected $description = 'Genera una copia de seguridad de la base de datos (automática cada 15 días).';

    public function handle(BackupService $backup): int
    {
        try {
            $nombre = $backup->generar();
            $this->info("Copia generada: {$nombre}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('No fue posible generar la copia: ' . $e->getMessage());
            report($e);
            return self::FAILURE;
        }
    }
}
