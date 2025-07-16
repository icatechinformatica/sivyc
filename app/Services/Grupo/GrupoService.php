<?php

namespace App\Services\Grupo;

use App\Interfaces\Repositories\GrupoRepositoryInterface;
use App\Repositories\GrupoRepository;

class GrupoService
{
    protected $grupoRepository;

    public function __construct(?GrupoRepositoryInterface $grupoRepository = null)
    {
        $this->grupoRepository = $grupoRepository ?: new GrupoRepository();
    }

    /**
     * Obtiene la información del grupo y sus alumnos
     */
    public function obtenerGrupoConAlumnos($folio_grupo)
    {
        if (!$folio_grupo) {
            return [null, []];
        }

        $grupo = $this->grupoRepository->obtenerGrupoPorFolio($folio_grupo);
        $alumnos = $this->grupoRepository->obtenerAlumnosPorFolio($folio_grupo);

        if ($grupo && $alumnos) {
            return [$grupo, $alumnos];
        }

        return [null, []];
    }

    /**
     * Verifica si hay alumnos vulnerables en el grupo
     */
    public function tieneAlumnosVulnerables($alumnos)
    {
        return collect($alumnos)->contains(function ($value) {
            return $value->id_gvulnerable != '[]';
        });
    }

    /**
     * Determina si el usuario puede activar/editar el grupo
     */
    public function puedeActivarGrupo($grupo, $data)
    {
        if (!$grupo) {
            return true;
        }

        return ($grupo->turnado_grupo == 'VINCULACION' || $grupo->status_curso == 'EDICION') 
               && isset($data['cct_folio']);
    }

    /**
     * Verifica si hay exoneraciones en edición
     */
    public function tieneExoneracionEnEdicion($folio_grupo)
    {
        return $this->grupoRepository->existeExoneracionEnEdicion($folio_grupo);
    }

    /**
     * Valida si un grupo tiene suficientes alumnos
     */
    public function validarCantidadAlumnos($folio_grupo, $minimo = 1)
    {
        $cantidad = $this->grupoRepository->contarAlumnosGrupo($folio_grupo);
        return $cantidad >= $minimo;
    }
}
