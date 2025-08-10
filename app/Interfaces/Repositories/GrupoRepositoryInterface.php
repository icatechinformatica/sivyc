<?php

namespace App\Interfaces\Repositories;

interface GrupoRepositoryInterface
{
    public function buscarPaginado($busqueda, $registrosPorPagina);
    public function obtenerTodos($registrosPorPagina);
    public function actualizarOrCrear(array $datos);
    public function obtenerPorId($id);

    public function actualizarEstatus($grupoId, $seccion, $nombreEstatus);
    // public function obtenerEstatusActual($grupoId);
    // public function agregarEstatus($grupoId, $nombreEstatus);
}
