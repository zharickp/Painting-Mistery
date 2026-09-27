<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class BackupService
{
    public const CARPETA = 'backups';

    /** Las copias automáticas se reconocen por el nombre del archivo. */
    public const PREFIJO_AUTO = 'respaldo_auto_';

    /** Días entre copias automáticas. */
    public const DIAS_ENTRE_AUTOMATICAS = 15;

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
                'automatica'     => str_starts_with($archivo->getFilename(), self::PREFIJO_AUTO),
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
    public function generar(bool $automatica = false): string
    {
        // Desde el navegador PHP corta a los 30 s por defecto; un volcado de
        // una BD remota puede tardar más.
        @set_time_limit(0);

        $default = config('database.default');
        $conf    = config("database.connections.{$default}");
        $driver  = $conf['driver'] ?? $default;

        return match ($driver) {
            'pgsql' => $this->dumpPostgres($conf, $automatica),
            'mysql' => $this->dumpMysql($conf, $automatica),
            default => throw new \RuntimeException("Driver de BD no soportado para respaldo: {$driver}"),
        };
    }

    private function dumpPostgres(array $conf, bool $automatica = false): string
    {
        $host     = $conf['host']     ?? '127.0.0.1';
        $port     = $conf['port']     ?? '5432';
        $bd       = $conf['database'] ?? '';
        $usuario  = $conf['username'] ?? '';
        $password = $conf['password'] ?? '';

        if (empty($bd)) throw new \RuntimeException('No hay base de datos configurada.');

        $binario = $this->rutaBinario('pg_dump');

        $marca   = now()->format('Y-m-d_His');
        $nombre  = ($automatica ? self::PREFIJO_AUTO : 'respaldo_') . "{$marca}.sql";
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

        $entorno = $this->entornoSistema() + ['PGPASSWORD' => (string) $password];
        if (!empty($conf['sslmode'])) {
            $entorno['PGSSLMODE'] = (string) $conf['sslmode'];
        }

        $proceso = $this->ejecutar($comando, $entorno);

        // pg_dump 17.6+ / 18 genera una llave aleatoria "\restrict" en cada copia. En Windows,
        // si no puede generarla, se reintenta pasándole una llave aleatoria creada aquí.
        if (!$proceso->isSuccessful() && stripos($proceso->getErrorOutput(), 'restrict') !== false) {
            $conLlave = $comando;
            array_splice($conLlave, count($conLlave) - 1, 0, ['--restrict-key=' . bin2hex(random_bytes(16))]);
            $proceso = $this->ejecutar($conLlave, $entorno);
        }

        if (!$proceso->isSuccessful()) {
            $error = trim($proceso->getErrorOutput());
            if (File::exists($destino)) File::delete($destino);
            Log::error('Fallo pg_dump: ' . $error);
            throw new \RuntimeException($this->mensajeError('pg_dump', $binario, $error));
        }

        return $nombre;
    }

    private function ejecutar(array $comando, array $entorno): Process
    {
        $proceso = new Process($comando, null, $entorno);
        $proceso->setTimeout(600);
        $proceso->run();

        return $proceso;
    }

    /**
     * Variables del sistema que los programas externos necesitan en Windows.
     * Cuando PHP corre como servidor web, el proceso hijo puede arrancar sin
     * ellas: sin PATH no encuentra ejecutables y sin SystemRoot no puede
     * generar números aleatorios (pg_dump falla al crear su llave \restrict).
     */
    private function entornoSistema(): array
    {
        // Se usan los nombres tal como los tiene el sistema (en Windows "Path" y
        // "PATH" son la misma variable; repetirla con otro nombre la duplicaría).
        $buscadas = ['SYSTEMROOT', 'WINDIR', 'PATH', 'TEMP', 'TMP', 'USERPROFILE', 'APPDATA', 'LOCALAPPDATA'];
        $entorno  = [];
        foreach (getenv() as $nombre => $valor) {
            if (in_array(strtoupper($nombre), $buscadas, true) && $valor !== '') {
                $entorno[$nombre] = $valor;
            }
        }

        $tieneRaiz = (bool) array_filter(array_keys($entorno), fn ($n) => strtoupper($n) === 'SYSTEMROOT');
        if (DIRECTORY_SEPARATOR === '\\' && ! $tieneRaiz) {
            $entorno['SystemRoot'] = 'C:\\Windows';
        }

        return $entorno;
    }

    private function dumpMysql(array $conf, bool $automatica = false): string
    {
        $host     = $conf['host']     ?? '127.0.0.1';
        $port     = $conf['port']     ?? '3306';
        $bd       = $conf['database'] ?? '';
        $usuario  = $conf['username'] ?? '';
        $password = $conf['password'] ?? '';

        if (empty($bd)) throw new \RuntimeException('No hay base de datos configurada.');

        $binario = $this->rutaBinario('mysqldump');

        $marca   = now()->format('Y-m-d_His');
        $nombre  = ($automatica ? self::PREFIJO_AUTO : 'respaldo_') . "{$marca}.sql";
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
            throw new \RuntimeException($this->mensajeError('mysqldump', $binario, $error));
        }

        return $nombre;
    }

    /** Fecha de la última copia automática, o null si nunca se ha hecho una. */
    public function ultimaAutomatica(): ?\Carbon\Carbon
    {
        foreach ($this->listar() as $copia) {
            if ($copia['automatica']) {
                return $copia['fecha'];
            }
        }

        return null;
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
        // 1) Ruta explícita en .env: DB_DUMP_PATH (pg_dump o mysqldump).
        //    Se lee vía config() para que funcione también con "config:cache".
        $env = config('database.dump_path');
        if ($env && file_exists($env)) return $env;

        // 2) Instalaciones de Windows: se busca CUALQUIER versión instalada
        //    (antes solo se buscaban la 14–17 y el equipo tiene la 18), y se
        //    prefiere la más reciente: pg_dump debe ser igual o más nuevo
        //    que el servidor al que se conecta.
        $patrones = $nombre === 'pg_dump' ? [
            'C:/Program Files/PostgreSQL/*/bin/pg_dump.exe',
            'C:/Program Files (x86)/PostgreSQL/*/bin/pg_dump.exe',
            'C:/laragon/bin/postgresql/*/bin/pg_dump.exe',
            'C:/xampp/pgsql/bin/pg_dump.exe',
        ] : [
            'C:/Program Files/MySQL/MySQL Server */bin/mysqldump.exe',
            'C:/laragon/bin/mysql/*/bin/mysqldump.exe',
            'C:/xampp/mysql/bin/mysqldump.exe',
        ];

        foreach ($patrones as $patron) {
            $encontrados = glob($patron) ?: [];
            if ($encontrados) {
                usort($encontrados, fn($a, $b) => strnatcmp($b, $a)); // versión más alta primero
                return $encontrados[0];
            }
        }

        // 3) Rutas típicas de Linux
        foreach (["/usr/bin/{$nombre}", "/usr/local/bin/{$nombre}"] as $c) {
            if (file_exists($c)) return $c;
        }

        // 4) Último recurso: que esté en el PATH del sistema
        return $nombre;
    }

    /**
     * Mensaje entendible para el administrador (sin datos sensibles).
     */
    private function mensajeError(string $herramienta, string $binario, string $error): string
    {
        $noEncontrado = $binario === $herramienta
            && (stripos($error, 'no se reconoce') !== false
                || stripos($error, 'not recognized') !== false
                || stripos($error, 'not found') !== false);

        if ($noEncontrado) {
            return "No se encontró {$herramienta} en el equipo. Instala el cliente de la base de datos "
                 . "o indica la ruta en el archivo .env con DB_DUMP_PATH.";
        }

        if (stripos($error, 'version mismatch') !== false) {
            return "La versión de {$herramienta} es más antigua que la del servidor de base de datos. "
                 . "Instala una versión igual o más reciente.";
        }

        if (stripos($error, 'password authentication failed') !== false) {
            return 'El usuario o la contraseña de la base de datos no son válidos para generar la copia.';
        }

        if (stripos($error, 'could not connect') !== false || stripos($error, 'connection') !== false) {
            return 'No fue posible conectarse al servidor de base de datos para generar la copia.';
        }

        return 'No fue posible generar la copia de seguridad. Revisa storage/logs/laravel.log para más detalle.';
    }

    private function formatearTamano(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 1) . ' MB';
        return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
    }
}
