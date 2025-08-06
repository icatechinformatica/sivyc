<?php

namespace App\Services\Grupo;

use App\Interfaces\Repositories\GrupoRepositoryInterface;
use App\Repositories\GrupoRepository;

class GrupoService
{

    public function __construct(private GrupoRepositoryInterface $grupoRepository)
    {
        $this->grupoRepository = $grupoRepository;
    }

    public function obtenerGrupos($registrosPorPagina = 15, $busqueda = null)
    {
        if ($busqueda) {
            return $this->grupoRepository->buscarPaginado($busqueda, $registrosPorPagina);
        }

        return $this->grupoRepository->obtenerTodos($registrosPorPagina);
    }
}
