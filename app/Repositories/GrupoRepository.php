<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Interfaces\Repositories\GrupoRepositoryInterface;
use App\Models\Grupo;

class GrupoRepository implements GrupoRepositoryInterface
{

    protected $grupo;

    public function __construct(Grupo $grupo)
    {
        $this->grupo = $grupo;
    }
    public function obtenerTodos($registrosPorPagina)
    {
        return $this->grupo->orderBy('id', 'desc')->paginate($registrosPorPagina);
    }

    public function buscarPaginado($busqueda, $registrosPorPagina = 15)
    {
        $query = $this->grupo->newQuery();
        
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                // Búsqueda por clave_grupo del grupo
                $q->where('tbl_grupos.clave_grupo', 'LIKE', "%{$busqueda}%")
                
                // Búsqueda por nombre del curso
                ->orWhereHas('curso', function ($cursoQuery) use ($busqueda) {
                    $cursoQuery->where('nombre_curso', 'LIKE', "%{$busqueda}%");
                })
                
                // Búsqueda por unidad
                ->orWhereHas('unidad', function ($unidadQuery) use ($busqueda) {
                    $unidadQuery->where('unidad', 'LIKE', "%{$busqueda}%");
                })
                
                // Búsqueda por instructor (nombre solo, concatenado normal y concatenado inverso)
                ->orWhereHas('instructor', function ($instructorQuery) use ($busqueda) {
                    $instructorQuery->where('nombre', 'LIKE', "%{$busqueda}%")
                        ->orWhere(DB::raw("CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno)"), 'LIKE', "%{$busqueda}%")
                        ->orWhere(DB::raw("CONCAT(apellidoPaterno, ' ', apellidoMaterno, ' ', nombre)"), 'LIKE', "%{$busqueda}%");
                })
                
                // Búsqueda por estatus
                ->orWhereHas('estatus', function ($estatusQuery) use ($busqueda) {
                    $estatusQuery->where('estatus', 'LIKE', "%{$busqueda}%");
                });
            });
        }
        
        return $query->with(['curso', 'unidad', 'instructor', 'estatus'])
                    ->orderBy('id', 'desc')
                    ->paginate($registrosPorPagina);
    }
}
