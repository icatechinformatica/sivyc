<?php

namespace App\Interfaces\Services;

interface CatalogoServiceInterface
{
    /**
     * Obtiene los CERSS disponibles para una unidad
     */
    public function obtenerCerss($unidad, $id_unidad);

    /**
     * Obtiene municipios según el CCT del usuario
     */
    public function obtenerMunicipios($cct, $unidad);

    /**
     * Obtiene organismos públicos activos
     */
    public function obtenerDependencias();

    /**
     * Obtiene grupos vulnerables
     */
    public function obtenerGruposVulnerables();
}
