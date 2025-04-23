<?php
namespace App\Interfaces\ElectronicDocument;

interface ElectronicDocumentRepositoryInterface {
    public function getallData(); //puede cambiar
    public function obtenerPlantilla(int $id);
}
