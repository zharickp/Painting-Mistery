<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Las reseñas ya no pasan por aprobación: se publican apenas el cliente las envía
 * y el taller solo puede desactivarlas (ocultarlas) o volver a activarlas.
 * resena.estado pasa de texto (pendiente / aprobada / rechazada) a BOOLEAN,
 * igual que el estado de las demás tablas. Las rechazadas quedan desactivadas;
 * las pendientes y aprobadas, activas.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('resena', 'estado')) {
            return;
        }

        DB::statement('ALTER TABLE resena DROP CONSTRAINT IF EXISTS resena_estado_check');
        DB::statement('ALTER TABLE resena ALTER COLUMN estado DROP DEFAULT');
        DB::statement("ALTER TABLE resena ALTER COLUMN estado TYPE BOOLEAN USING (estado IS DISTINCT FROM 'rechazada')");
        DB::statement('ALTER TABLE resena ALTER COLUMN estado SET DEFAULT TRUE');
        DB::statement('ALTER TABLE resena ALTER COLUMN estado SET NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('resena', 'estado')) {
            return;
        }

        DB::statement('ALTER TABLE resena ALTER COLUMN estado DROP DEFAULT');
        DB::statement("ALTER TABLE resena ALTER COLUMN estado TYPE VARCHAR(15) USING (CASE WHEN estado THEN 'aprobada' ELSE 'rechazada' END)");
        DB::statement("ALTER TABLE resena ALTER COLUMN estado SET DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE resena ADD CONSTRAINT resena_estado_check CHECK (estado IN ('pendiente', 'aprobada', 'rechazada'))");
    }
};
