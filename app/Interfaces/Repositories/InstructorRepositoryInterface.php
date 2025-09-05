<?php

namespace App\Interfaces\Repositories;

interface InstructorRepositoryInterface
{
    /**
     * Obtiene instructores internos que ya tienen cursos en el período
     */
    public function obtenerInstructoresInternos($fecha_inicio);

    /**
     * Obtiene instructores por especialidad
     */
    public function obtenerInstructoresPorEspecialidad($id_especialidad);

    /**
     * Obtiene información básica de un instructor
     */
    public function obtenerInformacionBasica($id_instructor);

    /**
     * Obtiene validación de especialidad del instructor
     */
    public function obtenerValidacionEspecialidad($id_especialidad, $id_instructor, $memo_especialidad);

    /**
     * Buscar instructores por nombre o especialidad
     */
    public function buscarInstructores(string $busqueda, int $limite = 20);
}
