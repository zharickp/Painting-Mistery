<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupService
{
    public const CARPETA = 'backups';

    public function directorio(): string
    {
        $ruta = storage_path('app/' . self::CARPETA);

        if (!File::exists($ruta)) {
            File::makeDirectory($ruta, 0755, true);
        }

        return $ruta;
    }

    public function listar(): array
    {
        $dir = $this->directorio();
        $items = [];

        foreach (File::files($dir) as $archivo) {
            $items[] = [
                'nombre'         => $archivo->getFilename(),
                'tamano_bytes'   => $archivo->getSize(),
                'tamano_legible' => $this->formatearTamano($archivo->getSize()),
                'fecha'          => \Carbon\Carbon::createFromTimestamp($archivo->getMTime()),
            ];
        }

        usort($items, fn($a, $b) => $b['fecha']->timestamp <=> $a['fecha']->timestamp);

        return $items;
    }

    /**
     * Genera una copia de la base de datos y devuelve el nombre del archivo.
     * Detecta automáticamente el driver (pgsql o mysql) desde la config.
     * @throws \RuntimeException
     */
    public function generar(): string
    {
        $default = config('database.default');
        $conf    = config("database.connections.{$default}");
        $driver  = $conf['driver'] ?? $default;

        return match ($driver) {
            'pgsql' => $this->dumpPostgres($conf),
            'mysql' => $this->dumpMysql($conf),
            default => throw new \RuntimeException("Driver de BD no soportado para respaldo: {$driver}"),
        };
    }

    private function dumpPostgres(array $conf): string
    {
        $host     = $conf['host']     ?? '127.0.0.1';
        $port     = $conf['port']     ?? '5432';
        $bd       = $conf['database'] ?? '';
        $usuario  = $conf['username'] ?? '';
        $password = $conf['password'] ?? '';

        if (empty($bd)) throw new \RuntimeException('No hay base de datos configurada.');

        $binario = $this->rutaBinario('pg_dump');

        $marca   = now()->format('Y-m-d_His');
        $nombre  = "respaldo_{$marca}.sql";
        $destino = $this->directorio() . DIRECTORY_SEPARATOR . $nombre;

        $comando = [
            $binario,
            '--host=' . $host,
            '--port=' . $port,
            '--username=' . $usuario,
            '--no-owner',
            '--no-privileges',
            '--format=plain',
            '--file=' . $destino,
            $bd,
        ];

        $proceso = new Process($comando, null, ['PGPASSWORD' => (string) $password]);
        $proceso->setTimeout(600);
        $proceso->run();

        if (!$proceso->isSuccessful()) {
            $error = trim($proceso->getErrorOutput());
            if (File::exists($destino)) File::delete($destino);
            Log::error('Fallo pg_dump: ' . $error);
            throw new \RuntimeException('No fue posible generar la copia de seguridad.');
        }

        return $nombre;
    }

    private function dumpMysql(array $conf): string
    {
        $host     = $conf['host']     ?? '127.0.0.1';
        $port     = $conf['port']     ?? '3306';
        $bd       = $conf['database'] ?? '';
        $usuario  = $conf['username'] ?? '';
        $password = $conf['password'] ?? '';

        if (empty($bd)) throw new \RuntimeException('No hay base de datos configurada.');

        $binario = $this->rutaBinario('mysqldump');

        $marca   = now()->format('Y-m-d_His');
        $nombre  = "respaldo_{$marca}.sql";
        $destino = $this->directorio() . DIRECTORY_SEPARATOR . $nombre;

        $comando = [
            $binario,
            '--host=' . $host,
            '--port=' . $port,
            '--user=' . $usuario,
            '--single-transaction',
            '--skip-lock-tables',
            '--default-character-set=utf8mb4',
            $bd,
        ];

        if ($password !== '' && $password !== null) {
            $comando[] = '--password=' . $password;
        }

        $proceso = new Process($comando);
        $proceso->setTimeout(300);

        $handle = fopen($destino, 'w');
        if (!$handle) throw new \RuntimeException('No fue posible crear el archivo de respaldo.');

        try {
            $proceso->run(function ($tipo, $buffer) use ($handle) {
                if ($tipo === Process::OUT) fwrite($handle, $buffer);
            });
        } finally {
            fclose($handle);
        }

        if (!$proceso->isSuccessful()) {
            $error = trim($proceso->getErrorOutput());
            File::delete($destino);
            Log::error('Fallo mysqldump: ' . $error);
            throw new \RuntimeException('No fue posible generar la copia de seguridad.');
        }

        return $nombre;
    }

    public function eliminar(string $nombre): bool
    {
        $ruta = $this->rutaSegura($nombre);
        return File::delete($ruta);
    }

    public function rutaSegura(string $nombre): string
    {
        $limpio = basename($nombre);

        if ($limpio !== $nombre || !preg_match('/^respaldo_[\w\-\.]+\.sql$/', $limpio)) {
            throw new \RuntimeException('Nombre de archivo no válido.');
        }

        $ruta = $this->directorio() . DIRECTORY_SEPARATOR . $limpio;

        if (!File::exists($ruta)) {
            throw new \RuntimeException('El archivo no existe.');
        }

        return $ruta;
    }

    private function rutaBinario(string $nombre): string
    {
        // 1) Ruta explícita en .env: DB_DUMP_PATH (pg_dump o mysqldump)
        $env = env('DB_DUMP_PATH');
        if ($env && file_exists($env)) return $env;

        // 2) Candidatos comunes en Windows / Linux
        $candidatos = $nombre === 'pg_dump' ? [
            'C:\\Program Files\\PostgreSQL\\17\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\15\\bin\\pg_dump.exe',
            'C:\\Program Files\\PostgreSQL\\14\\bin\\pg_dump.exe',
            'C:\\xampp\\pgsql\\bin\\pg_dump.exe',
            'C:\\laragon\\bin\\postgresql\\pgsql-16\\bin\\pg_dump.exe',
            '/usr/bin/pg_dump',
            '/usr/local/bin/pg_dump',
        ] : [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($candidatos as $c) {
            if (file_exists($c)) return $c;
        }

        return $nombre;
    }

    private function formatearTamano(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 1) . ' MB';
        return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
    }
}
