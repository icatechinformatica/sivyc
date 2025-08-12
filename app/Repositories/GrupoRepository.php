<?php

namespace App\Repositories;

use App\Models\Grupo;
use App\Models\Estatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\Repositories\GrupoRepositoryInterface;

class GrupoRepository implements GrupoRepositoryInterface
{

    protected $grupo;

    public function __construct(Grupo $grupo)
    {
        $this->grupo = $grupo;
    }
    public function obtenerTodos($registrosPorPagina)
    {
        return $this->grupo
            ->with(['curso', 'unidad', 'instructor', 'estatus'])
            ->orderBy('id', 'desc')
            ->paginate($registrosPorPagina);
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

    public function actualizarOrCrear(array $datos)
    {
        try {
            $resultado = Grupo::updateOrCreate(
                ['id' => $datos['id'] ?? null],
                $datos
            );
            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al actualizar o crear grupo: ' . $e->getMessage());
            throw new \Exception('Error al guardar los datos del grupo');
        }
    }

    public function obtenerPorId($id)
    {
        return $this->grupo->with(['curso', 'unidad', 'instructor', 'estatus'])->find($id);
    }

    public function actualizarEstatus($grupoId, $nombreEstatus)
    {
        try {
            $grupo = $this->grupo->find($grupoId);
            if (!$grupo) {
                throw new \Exception('Grupo no encontrado');
            }

            // * Comprobar que existe el estatus 
            $estatus = Estatus::where('estatus', $nombreEstatus)->firstOrFail();

            // ! Agregar nuevo estatus NO ACTUALIZAR
            $grupo->estatus()->attach($estatus->id, [
                'id_usuario' => auth()->id(),
                'fecha_cambio' => now(),
                'es_ultimo_estatus' => false
            ]);

            return $grupo;
        } catch (\Exception $e) {
            Log::error('Error al actualizar el estatus del grupo: ' . $e->getMessage());
            throw new \Exception('Error al actualizar el estatus del grupo: ' . $e->getMessage());
        }
    }
}
