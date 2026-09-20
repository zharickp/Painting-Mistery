<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditoriaService;
use App\Services\BackupService;
use Illuminate\Http\Request;

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
                'tipo'              => 'Copia de seguridad',
                'registro_etiqueta' => $nombre,
                'descripcion'       => "Generó copia de seguridad {$nombre}",
            ]);

            return redirect()
                ->route('admin.respaldos.index')
                ->with('success', 'Copia de seguridad generada correctamente.');
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

    public function destroy(string $nombre)
    {
        try {
            $this->backup->eliminar($nombre);

            AuditoriaService::registrar([
                'accion'            => 'eliminado',
                'tipo'              => 'Copia de seguridad',
                'registro_etiqueta' => $nombre,
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
