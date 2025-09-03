<?php

namespace App\Repositories;

use App\Interfaces\MunicipioRepositoryInterface;
use App\Models\Municipio;
use Illuminate\Support\Collection;

class MunicipioRepository implements MunicipioRepositoryInterface
{
    protected $municipio;

    public function __construct(Municipio $municipio)
    {
        $this->municipio = $municipio;
    }

    public function obtenerPorUnidadDisponible(?string $unidad): Collection
    {
        if (!$unidad) {
            return collect();
        }
        
        return $this->municipio->newQuery()->whereRaw('(unidad_disponible::jsonb) @> ?', [json_encode([$unidad])])->get();
    }

    public function obtenerPorEstado(int $idEstado): Collection
    {
        return $this->municipio->where('id_estado', $idEstado)->get();
    }
}
