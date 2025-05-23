<?php
namespace App\Repositories\ElectronicDocument;
use App\Interfaces\ElectronicDocument\ElectronicDocumentRepositoryInterface;
use App\Models\Documentos\Eplantillas;
use App\Models\Reportes\Rf001Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ElectronicDocumentRepository implements ElectronicDocumentRepositoryInterface
{
    protected $modelo = null;
    protected $tabla = null;
    protected $qry;
    public function __construct($fuente = null)
    {
         // Caso 1: No se pasa nada → usar modelo por defecto
        if (is_null($fuente)) {
            $this->modelo = new \App\Models\Reportes\Eplantillas();
            $this->qry = $this->modelo->newQuery();
            return;
        }

        // caso 1 es una instancia de modelo eloquent
        if ($fuente instanceof Model) {
            $this->modelo = $fuente;
            $this->qry = $this->modelo->newQuery();
            return;
        }

        // caso 3 es string: puede ser nombre de la clase o una tabla
        if (is_string($fuente)) {
            $nombreClase = $fuente;

            // 1. ¿Es clase válida directamente y extiende de Model?
            if (class_exists($nombreClase) && is_subclass_of($nombreClase, Model::class)) {
                $this->modelo = new $nombreClase();
                $this->qry = $this->modelo->newQuery();
                return;
            }

            // 2. Intentar con namespace por defecto si no funcionó antes
            $posibleClase = "App\\Models\\{$nombreClase}";
            if (class_exists($posibleClase) && is_subclass_of($posibleClase, Model::class)) {
                $this->modelo = new $posibleClase();
                $this->qry = $this->modelo->newQuery();
                return;
            }
            // Si no es clase válida, se trata como nombre de tabla
            $this->tabla = $fuente;
            $this->qry = DB::table($this->tabla);
            return;
        }
        // Fuente no válida
        throw new \Exception("Fuente inválida para construir consulta dinámica.");
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

    public function consultaMultiple(array $params)
    {
        $query = $this->qry;

        // JOIN (solo si la fuente es tabla y no un modelo, para evitar ambigüedades Eloquent)
        if (!empty($params['joins']) && is_array($params['joins']) && isset($this->tabla)) {
            foreach ($params['joins'] as $join) {
                $query->join(
                    $join['table'],
                    $join['first'],
                    $join['operator'] ?? '=',
                    $join['second'],
                    $join['type'] ?? 'inner'
                );
            }
        }

        if (!empty($params['select'])) {
            $query->select($params['select']);
        }

        // WHERE normales
        if (!empty($params['where']) && is_array($params['where'])) {
            foreach ($params['where'] as $cond) {
                $query->where(
                    $cond['column'],
                    $cond['operator'] ?? '=',
                    $cond['value']
                );
            }
        }

        // WHERE IN
        if (!empty($params['whereIn']) && is_array($params['whereIn'])) {
            foreach ($params['whereIn'] as $cond) {
                $query->whereIn($cond['column'], $cond['values']);
            }
        }
        // RETURN tipo
        if (!empty($params['count'])) {
            return $query->count();
        }

        return $params['first'] ?? false ? $query->first() : $query->get();

    }
}
