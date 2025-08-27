<?php
namespace App\Interfaces\ElectronicDocument;

interface ElectronicDocumentRepositoryInterface {
    public function obtenerTodosLosDatos(); //puede cambiar
    public function obtenerPlantilla(int $id, array $object, string $directiva);
    public function consultaMultiple(array $object);
}
