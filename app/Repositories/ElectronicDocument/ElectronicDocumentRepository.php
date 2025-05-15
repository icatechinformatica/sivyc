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
            # Si el string no tiene namespace completo, se lo añade automáticamente
            if (!str_contains($modelo, '\\')) {
                $modelo = "App\\Models\\Reportes\\{$modelo}";
            }

            if (!class_exists($modelo)) {
                throw new \Exception("El modelo $modelo no existe.");
            }

            $modelo = new $modelo();
        }

        // si no recibe algo, usar un modelo por defecto
        if (is_null($modelo)) {
            $modelo = new Eplantillas();
        }

        $this->modelo = $modelo;
    }
    public function obtenerTodosLosDatos()
    {
        return $this->modelo->all();
    }

    public function obtenerPlantilla(int $id)
    {
        $perPage = $id ? 10 : 5; // ejemplo
        $cacheKey = "plantilla_{$id}_{$perPage}";

        // return (new Rf001Model())->where('id_unidad', $id)->orderByDesc('id')->paginate($perPage);
        // return Eplantillas::findOrFail($id);

        return Cache::remember($cacheKey, now()->addHours(2), function() use ($id, $perPage) {
        return $this->modelo
            ->select([ 'id',
        'memorandum',
        'estado',
        'movimientos',
        'id_unidad',
        'envia',
        'dirigido',
        'archivos',
        'unidad',
        'periodo_inicio',
        'periodo_fin',
        'realiza',
        'movimiento',
        'tipo',
        'confirmed', 'created_at' ])
            ->where('id_unidad', $id)
            ->orderByDesc('id')
            ->paginate($perPage);
        });
    }
}
