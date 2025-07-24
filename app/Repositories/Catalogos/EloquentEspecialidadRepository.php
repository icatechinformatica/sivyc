<?php
namespace App\Repositories\Catalogos;

use App\Models\Especialidad;
use App\Interfaces\CatalogoInterface;

class EloquentProductoRepository implements CatalogoInterface
{
    public function all()         { return Especialidad::all(); }
    public function find(int $id){ return Especialidad::findOrFail($id); }
    public function create(array $data) { return Especialidad::create($data); }
    public function update(int $id, array $data) {
        $producto = Especialidad::findOrFail($id);
        $producto->update($data);
        return $producto;
    }
    public function delete(int $id): void {
        Especialidad::destroy($id);
    }
}