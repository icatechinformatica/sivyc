<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Interfaces\Repositories\CursoRepositoryInterface;

class CursoRepository implements CursoRepositoryInterface
{
    /**
     * Obtiene un curso por ID
     */
    public function obtenerPorId($id_curso, $incluir_inactivos = false)
    {
        $query = DB::table('cursos')->where('id', $id_curso);
        
        if (!$incluir_inactivos) {
            $query = $query->where('cursos.estado', true);
        }
        
        return $query->first();
    }

    /**
     * Obtiene cursos filtrados por criterios
     */
    public function obtenerCursosFiltrados($tipo, $modalidad, $unidad, $incluir_inactivos = false)
    {
        $query = DB::table('cursos')
            ->where('tipo_curso', 'like', "%$tipo%")
            ->where('modalidad', 'like', "%$modalidad%")
            ->whereJsonContains('unidades_disponible', [$unidad])
            ->orderby('cursos.nombre_curso');

        if (!$incluir_inactivos) {
            $query = $query->where('cursos.estado', true);
        }

        return $query->pluck('nombre_curso', 'cursos.id');
    }

    /**
     * Obtiene la especialidad de un curso
     */
    public function obtenerEspecialidadCurso($id_curso)
    {
        return DB::table('cursos')
            ->where('id', $id_curso)
            ->value('id_especialidad');
    }
}
