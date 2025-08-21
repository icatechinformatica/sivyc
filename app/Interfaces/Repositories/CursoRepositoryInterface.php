<?php

namespace App\Interfaces\Repositories;

interface CursoRepositoryInterface
{
    /**
     * Obtiene un curso por ID
     */
    public function obtenerPorId($id_curso, $incluir_inactivos = false);

    /**
     * Obtiene cursos filtrados por criterios
     */
    public function obtenerCursosFiltrados($tipo, $modalidad, $unidad, $incluir_inactivos = false);

    /**
     * Obtiene la especialidad de un curso
     */
    public function obtenerEspecialidadCurso($id_curso);
}
