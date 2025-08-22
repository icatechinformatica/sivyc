<?php

namespace App\Services\Unidades;

use App\Interfaces\UnidadRepositoryInterface;

class UnidadesService
{
    private $unidadesRepositorio;

    public function __construct(UnidadRepositoryInterface $unidadesRepositorio)
    {
        $this->unidadesRepositorio = $unidadesRepositorio;
    }
    
    /**
     * Christopher — 2025-08-21
     * El vinculador (usuario que captura) tiene asignada una unidad.
     * Dicha unidad se utiliza para identificar los municipios a los que el grupo asistirá físicamente.
     * En el modelo Municipio, el campo unidad_disponible es un arreglo con los nombres de las unidades que pueden asistir.
     */
    public function obtenerUnidadesPorUsuario()
    {
        $unidadUsuario = auth()->user()->unidad;
        $unidades_del_usuario = $unidadUsuario?->unidad; // Operador nullsafe para evitar error si el usuario no tiene unidad
        return $this->unidadesRepositorio->obtenerPorUbicacion($unidades_del_usuario);
    }
}
