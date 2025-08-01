<?php

namespace App\Services\Estatus;

use App\Interfaces\ActualizarEstatusRepositoryInterface;

class ActualizarEstatusService
{
    protected $estatusRepository;

    public function __construct(ActualizarEstatusRepositoryInterface $estatusRepository)
    {
        $this->estatusRepository = $estatusRepository;
    }

    public function actualizarAlumnoEstatus($alumnoId, $nuevoEstatus, $seccion = null)
    {
        return $this->estatusRepository->actualizarEstatus($alumnoId, $nuevoEstatus, $seccion);
    }
}
