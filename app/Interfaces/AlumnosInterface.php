<?php 

namespace App\Interfaces;

interface AlumnosInterface
{
    public function obtenerTodos($perPage = 15);
    public function buscarPaginado($busqueda = null, $perPage = 15);
    public function buscarPorCURP($curp);
    public function crear(array $data);
    public function actualizar($id, array $data);
    public function eliminar($id);
}