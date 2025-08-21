<?php

namespace App\Interfaces\Services;

interface GrupoServiceInterface
{
    /**
     * Obtiene la información del grupo y sus alumnos
     */
    public function obtenerGrupoConAlumnos($folio_grupo);

    /**
     * Verifica si hay alumnos vulnerables en el grupo
     */
    public function tieneAlumnosVulnerables($alumnos);

    /**
     * Determina si el usuario puede activar/editar el grupo
     */
    public function puedeActivarGrupo($grupo, $data);

    /**
     * Verifica si hay exoneraciones en edición
     */
    public function tieneExoneracionEnEdicion($folio_grupo);

    /**
     * Valida si un grupo tiene suficientes alumnos
     */
    public function validarCantidadAlumnos($folio_grupo, $minimo = 1);
}
