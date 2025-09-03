<?php

// Domain

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface UnidadRepositoryInterface
{
    /**
     * Devuelve las unidades cuyo campo 'ubicacion' coincide con el valor proporcionado.
     * Si $ubicacion es null, debe devolver una colección vacía.
     */
    public function obtenerPorUbicacion(?string $ubicacion): Collection;
}
