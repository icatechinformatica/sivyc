<?php
namespace App\Repositories\ElectronicDocument;
use App\Interfaces\ElectronicDocument\ElectronicDocumentRepositoryInterface;
use App\Models\Documentos\Eplantillas;

class ElectronicDocumentRepository implements ElectronicDocumentRepositoryInterface
{
    public function obtenerTodosLosDatos()
    {
        return Eplantillas::all();
    }

    public function obtenerPlantilla(int $id)
    {
        return Eplantillas::findOrFail($id);
    }
}
