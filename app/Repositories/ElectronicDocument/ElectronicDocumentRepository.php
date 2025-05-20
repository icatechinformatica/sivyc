<?php
namespace App\Repositories\ElectronicDocument;
use App\Interfaces\ElectronicDocument\ElectronicDocumentRepositoryInterface;
use App\Models\Documentos\Eplantillas;
use App\Models\Reportes\Rf001Model;
use Illuminate\Support\Facades\Cache;

class ElectronicDocumentRepository implements ElectronicDocumentRepositoryInterface
{
    protected $modelo;
    public function __construct($modelo = null)
    {
        // Si recibe un string, construye la clase (resolver namespace si es nombre simple)
        if (is_string($modelo)) {

            // Si no tiene un namespace completo (ej. "User" o "Reportes\Ejemplo")
            if (!str_contains($modelo, '\\')) {
                // Supone por defecto que está en App\Models
                $modelo = "App\\Models\\{$modelo}";
            } elseif (!str_starts_with($modelo, 'App\\')) {
                // Si tiene subcarpetas pero no el namespace completo
                $modelo = "App\\Models\\{$modelo}";
            }

            if (!class_exists($modelo)) {
                throw new \Exception("El modelo {$modelo} no existe.");
            }

            $modelo = new $modelo();
        }

        // Si no se proporciona nada, usar modelo por defecto
        if (is_null($modelo)) {
            $modelo = new \App\Models\Reportes\Eplantillas(); // o el modelo que desees como fallback
        }

        $this->modelo = $modelo;
    }

    public function obtenerTodosLosDatos()
    {
        return $this->modelo->all();
    }

    public function obtenerPlantilla(int $id, array $seleccion, string $directiva)
    {
        $perPage = $id ? 10 : 5; // ejemplo
        $cacheKey = "plantilla_{$id}_{$perPage}";

        // return Cache::remember($cacheKey, now()->addHours(2), function() use ($id, $seleccion, $directiva) {

        //         // ->orderByDesc('id')
        //         // ->paginate($perPage);
        // });

        return $this->modelo
                ->select($seleccion)
                ->where($directiva, $id)
                ->first();
    }
}
