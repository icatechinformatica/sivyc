<?php
namespace App\Services;

use App\Interfaces\CatalogoInterface;

class EspecialidadService
{
    public function __construct() {

    }
    public function listar()      { return $this->repo->all(); }
    public function obtener($id)  { return $this->repo->find($id); }
    public function crear($data)  { return $this->repo->create($data); }
    public function actualizar($id, $data) { return $this->repo->update($id, $data); }
    public function eliminar($id) { return $this->repo->delete($id); }
}