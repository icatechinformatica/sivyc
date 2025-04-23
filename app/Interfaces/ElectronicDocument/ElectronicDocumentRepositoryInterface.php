<?php
namespace App\Interfaces\ElectronicDocument;

interface ElectronicDocumentRepositoryInterface {
    public function obtenerTodosLosDatos(); //puede cambiar
    public function obtenerPlantilla(int $id);
}
