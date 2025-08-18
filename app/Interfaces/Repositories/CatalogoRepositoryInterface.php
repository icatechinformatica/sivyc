<?php

namespace App\Interfaces\Repositories;

interface CatalogoRepositoryInterface
{
    /**
     * Obtiene CERSS por unidad
     */
    public function obtenerCerss($id_unidad = null);

    /**
     * Obtiene municipios según criterios
     */
    public function obtenerMunicipios($es_cct_especial = false, $unidad = null);

    /**
     * Obtiene organismos públicos activos
     */
    public function obtenerOrganismosPublicos();

    /**
     * Obtiene grupos vulnerables
     */
    public function obtenerGruposVulnerables();

    /**
     * Obtiene localidades por municipio
     */
    public function obtenerLocalidadesPorMunicipio($id_municipio);
}
