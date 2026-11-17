<?php

namespace App\Console\Commands;

use App\Models\Inscripcion;
use Illuminate\Console\Command;

/**
 * Marca como "Completado" a quienes estaban inscritos en una fecha de curso
 * que ya terminó. Así no hay que hacerlo a mano desde el panel.
 */
class CompletarCursosCommand extends Command
{
    protected $signature = 'cursos:completar-finalizados';

    protected $description = 'Marca como completadas las inscripciones de las fechas de curso que ya terminaron';

    public function handle(): int
    {
        $total = Inscripcion::completarFinalizadas();
        $this->info("Inscripciones completadas: {$total}");

        return self::SUCCESS;
    }
}
