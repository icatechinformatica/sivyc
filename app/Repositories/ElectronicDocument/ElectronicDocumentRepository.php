<?php
namespace App\Repositories\ElectronicDocument;
use App\Interfaces\ElectronicDocument\ElectronicDocumentRepositoryInterface;
use App\Models\Documentos\Eplantillas;
use App\Models\Reportes\Rf001Model;
use Illuminate\Support\Facades\Cache;

class ElectronicDocumentRepository implements ElectronicDocumentRepositoryInterface
{
    public function obtenerTodosLosDatos()
    {
        return Eplantillas::all();
    }

    public function obtenerPlantilla(int $id)
    {
        $perPage = $id ? 10 : 5; // ejemplo
        $cacheKey = "plantilla_{$id}_{$perPage}";

        // return (new Rf001Model())->where('id_unidad', $id)->orderByDesc('id')->paginate($perPage);
        // return Eplantillas::findOrFail($id);

        return Cache::remember($cacheKey, now()->addHours(1), function() use ($id, $perPage) {
        return (new Rf001Model())
            ->select([ 'id', 'memorandum', 'estado', 'movimientos', 'id_unidad', 'envia', 'dirigido' ])
            ->where('id_unidad', $id)
            ->orderByDesc('id')
            ->paginate($perPage);
        });
    }
}
