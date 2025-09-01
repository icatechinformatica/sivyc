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
            ->with(['curso', 'unidad', 'instructor', 'estatus', 'exoneracion'])
            ->orderBy('id', 'desc')
            ->paginate($registrosPorPagina);
    }
    public function obtenerTodosPorUnidad($registrosPorPagina)
    {
        return $this->grupo
            ->where('id_unidad', auth()->user()->unidad->id)
            ->with(['curso', 'unidad', 'instructor', 'estatus', 'exoneracion'])
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

                    // ! Búsqueda por instructor (nombre solo, concatenado normal y concatenado inverso) IMPLEMENTAR CUANDO SE TENGA LA TABLA COMPLETA
                    // ->orWhereHas('instructor', function ($instructorQuery) use ($busqueda) {
                    //     $instructorQuery->where('nombre', 'LIKE', "%{$busqueda}%")
                    //         ->orWhere(DB::raw("CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno)"), 'LIKE', "%{$busqueda}%")
                    //         ->orWhere(DB::raw("CONCAT(apellidoPaterno, ' ', apellidoMaterno, ' ', nombre)"), 'LIKE', "%{$busqueda}%");
                    // })

                    // Búsqueda por estatus (sólo estatus actual)
                    ->orWhereHas('estatus', function ($estatusQuery) use ($busqueda) {
                        $estatusQuery
                            ->where('estatus', 'LIKE', "%{$busqueda}%")
                            ->where('tbl_grupo_estatus.es_ultimo_estatus', true);
                    });
            });
        }

        return $query->with(['curso', 'unidad', 'instructor', 'estatus', 'exoneracion'])
            ->orderBy('id', 'desc')
            ->paginate($registrosPorPagina);
    }

    public function buscarPaginadoPorUnidad($busqueda, $registrosPorPagina = 15)
    {
        $query = $this->grupo->newQuery()
            ->where('id_unidad', auth()->user()->unidad->id);

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

                    // Búsqueda por estatus (sólo estatus actual)
                    ->orWhereHas('estatus', function ($estatusQuery) use ($busqueda) {
                        $estatusQuery
                            ->where('estatus', 'LIKE', "%{$busqueda}%")
                            ->where('tbl_grupo_estatus.es_ultimo_estatus', true);
                    });
            });
        }

        return $query->with(['curso', 'unidad', 'instructor', 'estatus', 'exoneracion'])
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
        return $this->grupo->with(['curso', 'unidad', 'instructor', 'estatus', 'exoneracion'])->find($id);
    }

    public function actualizarEstatus($grupoId, $nombreEstatus)
    {
        try {
            return DB::transaction(function () use ($grupoId, $nombreEstatus) {
                $grupo = $this->grupo->find($grupoId);
                if (!$grupo) {
                    throw new \Exception('Grupo no encontrado');
                }

                // Validar que el estatus exista
                $estatus = Estatus::where('estatus', $nombreEstatus)->firstOrFail();

                // Obtener el estatus actual marcado como último (si existe)
                $estatusActual = $grupo->estatus()
                    ->wherePivot('es_ultimo_estatus', true)
                    ->orderByDesc('tbl_grupo_estatus.id')
                    ->first();

                // Si no hay cambio de estatus, no hacer nada
                if ($estatusActual && (int) $estatusActual->id === (int) $estatus->id) {
                    return $grupo;
                }

                // Desmarcar cualquier último estatus previo
                DB::table('tbl_grupo_estatus')
                    ->where('id_grupo', $grupo->id)
                    ->where('es_ultimo_estatus', true)
                    ->update(['es_ultimo_estatus' => false]);

                // Adjuntar el nuevo estatus como último
                $grupo->estatus()->attach($estatus->id, [
                    'id_usuario' => auth()->id(),
                    'fecha_cambio' => now(),
                    'es_ultimo_estatus' => true,
                ]);

                return $grupo;
            });
        } catch (\Exception $e) {
            Log::error('Error al actualizar el estatus del grupo: ' . $e->getMessage());
            throw new \Exception('Error al actualizar el estatus del grupo: ' . $e->getMessage());
        }
    }
}
