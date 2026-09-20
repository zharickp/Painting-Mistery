<?php

namespace App\Traits;

use App\Observers\AuditableObserver;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::observe(AuditableObserver::class);
    }

    /**
     * Etiqueta legible del tipo (por defecto, el basename del modelo).
     * Puede sobrescribirse en cada modelo con protected string $auditoriaTipo.
     */
    public function auditoriaTipo(): string
    {
        if (property_exists($this, 'auditoriaTipo') && !empty($this->auditoriaTipo)) {
            return $this->auditoriaTipo;
        }

        return class_basename($this);
    }

    /**
     * Etiqueta breve del registro (nombre, título, correo…) para la vista.
     * Puede sobrescribirse por modelo con protected string $auditoriaEtiqueta = 'nombre'.
     */
    public function auditoriaEtiqueta(): ?string
    {
        $candidatos = property_exists($this, 'auditoriaCandidatos') && is_array($this->auditoriaCandidatos)
            ? $this->auditoriaCandidatos
            : ['nombre', 'titulo', 'title', 'correo', 'email', 'descripcion'];

        foreach ($candidatos as $campo) {
            if (!empty($this->{$campo})) {
                return (string) $this->{$campo};
            }
        }

        return null;
    }

    /**
     * Campos que nunca se guardan en auditoría, ni siquiera en la lista de cambios.
     */
    public function auditoriaOcultos(): array
    {
        $base = ['password', 'password_confirmation', 'remember_token', 'verification_code', 'code_expires_at'];

        if (property_exists($this, 'auditoriaOcultos') && is_array($this->auditoriaOcultos)) {
            return array_values(array_unique(array_merge($base, $this->auditoriaOcultos)));
        }

        return $base;
    }
}
