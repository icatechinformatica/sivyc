<?php

namespace App\Services\Alumno;

use App\Models\Alumnopre;
use illuminate\Support\Facades\DB;
use App\Repositories\AlumnosRepository;

class AlumnoConsultaService
{

    public function __construct(private AlumnosRepository $alumnoRepository)
    {
        $this->alumnoRepository = $alumnoRepository;
    }

    public function obtenerAlumnos($registrosPorPagina = 15, $busqueda = null)
    {
        if ($busqueda) {
            return $this->alumnoRepository->buscarPaginado($busqueda, $registrosPorPagina);
        }
        return $this->alumnoRepository->obtenerTodos($registrosPorPagina);
    }

    public function obtenerAlumnoPorCURP(string $curp)
    {
        return $this->alumnoRepository->buscarPorCURP($curp);
    }
}
