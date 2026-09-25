<?php

namespace App\Observers;

use App\Services\AuditoriaService;

class AuditableObserver
{
    public function created($model): void
    {
        $ocultos = method_exists($model, 'auditoriaOcultos') ? $model->auditoriaOcultos() : [];
        $nuevos  = collect($model->getAttributes())
            ->except($ocultos)
            ->except(['created_at', 'updated_at'])
            ->all();

        AuditoriaService::registrar([
            'accion'             => 'creado',
            'modulo'             => $model->auditoriaTipo(),
            'registro_id'        => $model->getKey(),
            'registro_etiqueta'  => method_exists($model, 'auditoriaEtiqueta') ? $model->auditoriaEtiqueta() : null,
            'descripcion'        => 'Creó ' . $model->auditoriaTipo() . ($model->auditoriaEtiqueta() ? ' "' . $model->auditoriaEtiqueta() . '"' : ''),
            'valores_anteriores' => [],
            'valores_nuevos'     => $nuevos,
        ]);
    }

    public function updated($model): void
    {
        $ocultos = method_exists($model, 'auditoriaOcultos') ? $model->auditoriaOcultos() : [];
        $dirty   = collect($model->getDirty())
            ->except($ocultos)
            ->except(['updated_at']);

        if ($dirty->isEmpty()) return;

        $antes = [];
        $desp  = [];
        foreach ($dirty as $campo => $nuevo) {
            $antes[$campo] = $model->getOriginal($campo);
            $desp[$campo]  = $nuevo;
        }

        $accion = 'actualizado';
        $verbo  = 'Actualizó';
        $sufijo = '';

        // Solo cambió el interruptor activo/inactivo (estado booleano): evento propio.
        if ($dirty->keys()->all() === ['estado'] && is_bool($model->getAttribute('estado'))) {
            $activo = (bool) $model->getAttribute('estado');
            $accion = $activo ? 'activado' : 'desactivado';
            $verbo  = $activo ? 'Activó' : 'Desactivó';
            $sufijo = $activo ? ' (Inactivo → Activo)' : ' (Activo → Inactivo)';
        }

        $etiqueta = method_exists($model, 'auditoriaEtiqueta') ? $model->auditoriaEtiqueta() : null;

        AuditoriaService::registrar([
            'accion'             => $accion,
            'modulo'             => $model->auditoriaTipo(),
            'registro_id'        => $model->getKey(),
            'registro_etiqueta'  => $etiqueta,
            'descripcion'        => $verbo . ' ' . $model->auditoriaTipo() . ($etiqueta ? ' "' . $etiqueta . '"' : '') . $sufijo,
            'valores_anteriores' => $antes,
            'valores_nuevos'     => $desp,
        ]);
    }

    public function deleted($model): void
    {
        $ocultos = method_exists($model, 'auditoriaOcultos') ? $model->auditoriaOcultos() : [];
        $antes   = collect($model->getOriginal())
            ->except($ocultos)
            ->except(['created_at', 'updated_at'])
            ->all();

        AuditoriaService::registrar([
            'accion'             => 'eliminado',
            'modulo'             => $model->auditoriaTipo(),
            'registro_id'        => $model->getKey(),
            'registro_etiqueta'  => method_exists($model, 'auditoriaEtiqueta') ? $model->auditoriaEtiqueta() : null,
            'descripcion'        => 'Eliminó ' . $model->auditoriaTipo() . ($model->auditoriaEtiqueta() ? ' "' . $model->auditoriaEtiqueta() . '"' : ''),
            'valores_anteriores' => $antes,
            'valores_nuevos'     => [],
        ]);
    }
}
