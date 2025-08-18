<?php

namespace App\Interfaces\Services;

interface InstructorServiceInterface
{
    /**
     * Obtiene instructores disponibles para una especialidad y fechas específicas
     */
    public function obtenerInstructoresPorEspecialidad($data);

    /**
     * Obtiene información básica de un instructor
     */
    public function obtenerInformacionBasicaInstructor($id_instructor);

    /**
     * Valida la especialidad del instructor y obtiene el PDF de validación
     */
    public function obtenerValidacionInstructor($grupo);
}
