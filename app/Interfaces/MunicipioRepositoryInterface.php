<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface MunicipioRepositoryInterface
{
    /**
     * Devuelve municipios donde el arreglo JSONB 'unidad_disponible' contiene el nombre de unidad dado.
     * Si $unidad es null, puede devolver una colección vacía o un conjunto por defecto.
     */
    public function obtenerPorUnidadDisponible(?string $unidad): Collection;
    
    /**
     * Devuelve municipios por id_estado (ej. 7 = Chiapas).
     */
    public function obtenerPorEstado(int $idEstado): Collection;
}
