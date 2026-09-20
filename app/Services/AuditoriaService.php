<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Throwable;

class AuditoriaService
{
    public static function registrar(array $datos): ?Auditoria
    {
        try {
            $usuario = Auth::user();

            return Auditoria::create(array_merge([
                'usuario_id'     => $usuario?->id,
                'usuario_nombre' => $usuario ? trim(($usuario->primer_nombre ?? '') . ' ' . ($usuario->primer_apellido ?? '')) : null,
                'usuario_correo' => $usuario->correo ?? null,
                'ip_address'     => Request::ip(),
                'user_agent'     => substr((string) Request::userAgent(), 0, 255),
                'fecha'          => now(),
            ], $datos));
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    public static function login($usuario): void
    {
        self::registrar([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => trim(($usuario->primer_nombre ?? '') . ' ' . ($usuario->primer_apellido ?? '')),
            'usuario_correo' => $usuario->correo,
            'accion'         => 'login',
            'modulo'         => 'Usuario',
            'registro_id'    => $usuario->id,
            'descripcion'    => 'Inició sesión.',
        ]);
    }

    public static function logout($usuario): void
    {
        if (!$usuario) return;

        self::registrar([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => trim(($usuario->primer_nombre ?? '') . ' ' . ($usuario->primer_apellido ?? '')),
            'usuario_correo' => $usuario->correo,
            'accion'         => 'logout',
            'modulo'         => 'Usuario',
            'registro_id'    => $usuario->id,
            'descripcion'    => 'Cerró sesión.',
        ]);
    }
}
