<?php

namespace App\Services\Catalogo;

use App\Interfaces\Repositories\CatalogoRepositoryInterface;
use App\Repositories\CatalogoRepository;

class CatalogoService
{
    protected $catalogoRepository;

    public function __construct(?CatalogoRepositoryInterface $catalogoRepository = null)
    {
        $this->catalogoRepository = $catalogoRepository ?: new CatalogoRepository();
    }

    /**
     * Obtiene los CERSS disponibles para una unidad
     */
    public function obtenerCerss($unidad, $id_unidad)
    {
        return $this->catalogoRepository->obtenerCerss($unidad ? $id_unidad : null);
    }

    /**
     * Obtiene municipios según el CCT del usuario
     */
    public function obtenerMunicipios($cct, $unidad)
    {
        $es_cct_especial = str_starts_with($cct ?? 0, '07000');
        return $this->catalogoRepository->obtenerMunicipios($es_cct_especial, $unidad);
    }

    /**
     * Obtiene organismos públicos activos
     */
    public function obtenerDependencias()
    {
        return $this->catalogoRepository->obtenerOrganismosPublicos();
    }

    /**
     * Obtiene grupos vulnerables
     */
    public function obtenerGruposVulnerables()
    {
        return $this->catalogoRepository->obtenerGruposVulnerables();
    }
}
