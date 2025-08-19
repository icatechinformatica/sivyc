<?php

namespace App\Services\Instructor;

use App\Interfaces\Repositories\InstructorRepositoryInterface;
use App\Repositories\InstructorRepository;

class InstructorService
{
    protected $instructorRepository;

    public function __construct(?InstructorRepositoryInterface $instructorRepository = null)
    {
        $this->instructorRepository = $instructorRepository ?: new InstructorRepository();
    }

    /**
     * Obtiene instructores disponibles para una especialidad y fechas específicas
     */
    public function obtenerInstructoresPorEspecialidad($data)
    {
        return $this->instructorRepository->obtenerInstructoresPorEspecialidad($data->id_especialidad);
    }

    /**
     * Obtiene información básica de un instructor
     */
    public function obtenerInformacionBasicaInstructor($id_instructor)
    {
        return $this->instructorRepository->obtenerInformacionBasica($id_instructor);
    }

    /**
     * Valida la especialidad del instructor y obtiene el PDF de validación
     */
    public function obtenerValidacionInstructor($grupo)
    {
        if (!$grupo->id_especialidad) {
            return null;
        }

        return $this->instructorRepository->obtenerValidacionEspecialidad(
            $grupo->id_especialidad,
            $grupo->id_instructor,
            $grupo->instructor_mespecialidad
        );
    }
}
