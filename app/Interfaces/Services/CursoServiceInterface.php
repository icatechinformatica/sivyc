<?php

namespace App\Interfaces\Services;

interface CursoServiceInterface
{
    /**
     * Obtiene la información del curso por ID
     */
    public function obtenerCursoPorId($id_curso, $status_curso = null);

    /**
     * Obtiene cursos filtrados por tipo, modalidad y unidad
     */
    public function obtenerCursosFiltrados($tipo, $modalidad, $unidad, $status_curso = null, $id_curso_autorizado = null);

    /**
     * Obtiene localidades por clave de municipio
     */
    public function obtenerLocalidadesPorMunicipio($id_municipio);

    /**
     * Obtiene la especialidad de un curso
     */
    public function obtenerEspecialidadCurso($id_curso);
}
