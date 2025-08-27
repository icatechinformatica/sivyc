<?php
namespace App\Factories;

use App\Repositories\ElectronicDocument\ElectronicDocumentRepository;
use App\Interfaces\ElectronicDocument\ElectronicDocumentRepositoryInterface;

class ElectronicDocumentFactory
{
     /**
     * Crea una instancia del repositorio con el modelo dado
     *
     * @param string|null $modelo
     * @return ElectronicDocumentRepositoryInterface
     */

     public function make(?string $modelo = null) : ElectronicDocumentRepositoryInterface
     {
        return (new ElectronicDocumentRepository($modelo));
     }
}
