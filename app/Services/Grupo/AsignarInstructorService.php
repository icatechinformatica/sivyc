<?php

namespace App\Services\Grupo;

use App\Interfaces\Repositories\InstructorRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AsignarInstructorService
{
    protected $instructorRepository;

    public function __construct(InstructorRepositoryInterface $instructorRepository)
    {
        $this->instructorRepository = $instructorRepository;
    }

    public function buscarInstructores(?string $busqueda = '', int $limite = 20): Collection
    {
        // Validar que el límite esté en un rango razonable
        $limite = max(1, min($limite, 100));

        // Limpiar el término de búsqueda y manejar null
        $busqueda = trim($busqueda ?? '');

        try {
            // Llamar al repositorio para obtener los datos
            $instructores = $this->instructorRepository->buscarInstructores($busqueda, $limite);

            // Filtrar solo instructores activos por seguridad
            $instructoresActivos = $instructores->filter(function ($instructor) {
                return isset($instructor['activo']) && $instructor['activo'] === true;
            });

            return $instructoresActivos;

        } catch (\Exception $e) {
            // En caso de error, devolver colección vacía y loggear el error
            Log::error('Error al buscar instructores: ' . $e->getMessage());
            return collect([]);
        }
    }
}
