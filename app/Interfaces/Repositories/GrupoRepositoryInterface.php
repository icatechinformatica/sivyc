<?php

namespace App\Interfaces\Repositories;

interface GrupoRepositoryInterface
{
    public function buscarPaginado($busqueda, $registrosPorPagina);
    public function obtenerTodos($registrosPorPagina);
}
