<?php

// Infrastructure

namespace App\Repositories;

use App\Interfaces\UnidadRepositoryInterface;
use App\Models\Unidad;
use Illuminate\Support\Collection;

class UnidadRepository implements UnidadRepositoryInterface
{
    protected $unidad;

    public function __construct(Unidad $unidad)
    {
        $this->unidad = $unidad;
    }

    public function obtenerPorUbicacion(?string $ubicacion): Collection
    {
        if (!$ubicacion) {
            return collect();
        }

        return $this->unidad->where('ubicacion', $ubicacion)->get();

    }
}
