<?php

namespace App\Interfaces;

interface ActualizarEstatusRepositoryInterface
{
    /**
     * Actualiza el estatus del alumno en la tabla pivote
     * @param int $alumnoId
     * @param int $nuevoEstatus
     * @param string|null $seccion
     * @return bool
     */
    public function actualizarEstatus(int $alumnoId, int $nuevoEstatus, ?string $seccion = null): bool;
    public function obtenerUltimoEstatus(int $alumnoId);
}
