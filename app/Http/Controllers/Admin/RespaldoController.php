<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditoriaService;
use App\Models\Usuario;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespaldoController extends Controller
{
    public function __construct(private BackupService $backup)
    {
    }

    public function index()
    {
        return view('admin.respaldos.index', [
            'copias' => $this->backup->listar(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $nombre = $this->backup->generar();

            AuditoriaService::registrar([
                'accion'            => 'creado',
                'modulo'            => 'Copia de seguridad',
                'nombre_registro' => $nombre,
                'descripcion'       => "Generó copia de seguridad {$nombre}",
            ]);

            return redirect()
                ->route('admin.respaldos.index')
                ->with('success', 'Copia de seguridad generada correctamente.');
        } catch (\Symfony\Component\Process\Exception\ExceptionInterface $e) {
            // Ej: tiempo agotado. Su mensaje incluye la línea de comando, no se muestra.
            report($e);
            return redirect()
                ->route('admin.respaldos.index')
                ->with('error', 'La copia de seguridad tardó demasiado o fue interrumpida. Inténtalo de nuevo.');
        } catch (\RuntimeException $e) {
            // Mensajes controlados por BackupService: seguros de mostrar.
            report($e);
            return redirect()
                ->route('admin.respaldos.index')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return redirect()
                ->route('admin.respaldos.index')
                ->with('error', 'No fue posible generar la copia de seguridad.');
        }
    }

    public function download(string $nombre)
    {
        try {
            $ruta = $this->backup->rutaSegura($nombre);
            return response()->download($ruta);
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.respaldos.index')
                ->with('error', 'No fue posible descargar el archivo.');
        }
    }

    /**
     * Aplica la copia al sistema. Como también se restauran las sesiones,
     * al terminar se cierra la sesión y se pide entrar de nuevo.
     */
    public function restaurar(Request $request, string $nombre)
    {
        $usuario = Auth::user();

        try {
            $previa = $this->backup->restaurar($nombre);
        } catch (\Symfony\Component\Process\Exception\ExceptionInterface $e) {
            report($e);
            return redirect()->route('admin.respaldos.index')
                ->with('error', 'La restauración tardó demasiado o fue interrumpida. No se cambió nada.');
        } catch (\RuntimeException $e) {
            report($e);
            return redirect()->route('admin.respaldos.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('admin.respaldos.index')
                ->with('error', 'No fue posible restaurar la copia. No se cambió nada.');
        }

        // Queda registrado en la auditoría de la base restaurada (si el usuario existe en ella).
        $existe = Usuario::whereKey($usuario->id)->exists();
        AuditoriaService::registrar([
            'usuario_id'      => $existe ? $usuario->id : null,
            'accion'          => 'actualizado',
            'modulo'          => 'Copia de seguridad',
            'nombre_registro' => $nombre,
            'descripcion'     => "Restauró la copia de seguridad {$nombre}. El estado anterior quedó en {$previa}",
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'La copia se restauró. Inicia sesión de nuevo.');
    }

    public function destroy(string $nombre)
    {
        try {
            $this->backup->eliminar($nombre);

            AuditoriaService::registrar([
                'accion'            => 'eliminado',
                'modulo'            => 'Copia de seguridad',
                'nombre_registro' => $nombre,
                'descripcion'       => "Eliminó copia de seguridad {$nombre}",
            ]);

            return redirect()
                ->route('admin.respaldos.index')
                ->with('success', 'Copia eliminada correctamente.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.respaldos.index')
                ->with('error', 'No fue posible eliminar la copia.');
        }
    }
}
