<?php

namespace App\Repositories;

use App\Models\Grupo;
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

            // Obtener o crear el estatus por nombre
            $estatus = \App\Models\Estatus::firstOrCreate(['estatus' => $nombreEstatus]);

            // NO desvinculamos estatus anteriores para mantener el historial
            // Verificar si ya existe este estatus para evitar duplicados
            $existeRelacion = $grupo->estatus()->where('id_estatus', $estatus->id)->exists();
            
            if (!$existeRelacion) {
                // Agregar el nuevo estatus manteniendo el historial
                $grupo->estatus()->attach($estatus->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                // Si ya existe esta relación, solo actualizar el timestamp
                $grupo->estatus()->updateExistingPivot($estatus->id, [
                    'updated_at' => now()
                ]);
            }

            return $grupo;
        } catch (\Exception $e) {
            Log::error('Error al actualizar el estatus del grupo: ' . $e->getMessage());
            throw new \Exception('Error al actualizar el estatus del grupo: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el estatus actual de un grupo (el más reciente del historial)
     */
    public function obtenerEstatusActual($grupoId)
    {
        try {
            $grupo = $this->grupo->find($grupoId);
            if (!$grupo) {
                throw new \Exception('Grupo no encontrado');
            }

            // Retornar el último estatus basado en el timestamp más reciente de la tabla pivote
            return $grupo->estatus()
                        ->orderBy('tbl_grupo_estatus.updated_at', 'desc')
                        ->orderBy('tbl_grupo_estatus.created_at', 'desc')
                        ->first();
        } catch (\Exception $e) {
            Log::error('Error al obtener el estatus actual del grupo: ' . $e->getMessage());
            throw new \Exception('Error al obtener el estatus actual del grupo');
        }
    }

    /**
     * Agregar un estatus sin eliminar los anteriores (para historial)
     */
    public function agregarEstatus($grupoId, $nombreEstatus)
    {
        try {
            $grupo = $this->grupo->find($grupoId);
            if (!$grupo) {
                throw new \Exception('Grupo no encontrado');
            }

            $estatus = \App\Models\Estatus::firstOrCreate(['estatus' => $nombreEstatus]);

            // Solo agregar si no existe ya
            if (!$grupo->estatus()->where('id_estatus', $estatus->id)->exists()) {
                $grupo->estatus()->attach($estatus->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return $grupo;
        } catch (\Exception $e) {
            Log::error('Error al agregar estatus al grupo: ' . $e->getMessage());
            throw new \Exception('Error al agregar estatus al grupo');
        }
    }

    /**
     * Obtener todo el historial de estatus de un grupo
     */
    public function obtenerHistorialEstatus($grupoId)
    {
        try {
            $grupo = $this->grupo->find($grupoId);
            if (!$grupo) {
                throw new \Exception('Grupo no encontrado');
            }

            // Retornar todos los estatus ordenados cronológicamente
            return $grupo->estatus()
                        ->withPivot('created_at', 'updated_at')
                        ->orderBy('tbl_grupo_estatus.created_at', 'asc')
                        ->get();
        } catch (\Exception $e) {
            Log::error('Error al obtener el historial de estatus del grupo: ' . $e->getMessage());
            throw new \Exception('Error al obtener el historial de estatus del grupo');
        }
    }
}
