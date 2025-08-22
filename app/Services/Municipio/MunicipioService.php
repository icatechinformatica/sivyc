<?php

namespace App\Services\Municipio;

use App\Interfaces\MunicipioRepositoryInterface;

class MunicipioService
{
    protected $municipioRepo;

    public function __construct(MunicipioRepositoryInterface $municipioRepo)
    {
        $this->municipioRepo = $municipioRepo;
    }

    /** 
     * Christopher 21-Agosto-2025
     * El municipio tiene un campo asignado llamado “unidad_disponible” que es un arreglo con el nombre de la o las unidades que pueden asistir(?). 
     */

    public function municipiosPorUnidadDisponible($grupo)
    {
        $grupo_unidad = $grupo->unidad?->unidad;
        return $this->municipioRepo->obtenerPorUnidadDisponible($grupo_unidad);
    }
}
