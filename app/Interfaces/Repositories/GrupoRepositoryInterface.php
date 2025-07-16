<?php

namespace App\Interfaces\Repositories;

interface GrupoRepositoryInterface
{
    /**
     * Obtiene la información completa del grupo con todos los joins necesarios
     */
    public function obtenerGrupoPorFolio($folio_grupo);

    /**
     * Obtiene los alumnos de un grupo
     */
    public function obtenerAlumnosPorFolio($folio_grupo);

    /**
     * Verifica si existen exoneraciones en edición para un grupo
     */
    public function existeExoneracionEnEdicion($folio_grupo);

    /**
     * Cuenta el número de alumnos en un grupo
     */
    public function contarAlumnosGrupo($folio_grupo);
}
