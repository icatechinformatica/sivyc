<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Interfaces\Repositories\InstructorRepositoryInterface;

class InstructorRepository implements InstructorRepositoryInterface
{
    /**
     * Obtiene instructores internos que ya tienen cursos en el período
     */
    public function obtenerInstructoresInternos($fecha_inicio)
    {
        return DB::table('instructores as i')
            ->select('i.id')
            ->join('tbl_cursos as c', 'c.id_instructor', 'i.id')
            ->where('i.tipo_instructor', 'INTERNO')
            ->where('curso_extra', false)
            ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($fecha_inicio)))
            ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($fecha_inicio)))
            ->havingRaw('count(*) >= 2')
            ->groupby('i.id');
    }

    /**
     * Obtiene instructores por especialidad
     */
    public function obtenerInstructoresPorEspecialidad($id_especialidad)
    {
        return DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
            ->select(
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                'instructores.id',
                DB::raw('count(id_curso) as total')
            )
            ->rightJoin('instructores', 't.id_instructor', '=', 'instructores.id')
            ->leftJoin('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->leftJoin('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->leftJoin('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            ->where('especialidad_instructores.especialidad_id', $id_especialidad)
            ->groupBy('t.id_instructor', 'instructores.id')
            ->orderBy('instructor')
            ->limit(1)
            ->get();
    }

    /**
     * Obtiene información básica de un instructor
     */
    public function obtenerInformacionBasica($id_instructor)
    {
        return DB::table('instructores')
            ->select(
                'id',
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                'tipo_honorario'
            )
            ->where('id', $id_instructor)
            ->first();
    }

    /**
     * Obtiene validación de especialidad del instructor
     */
    public function obtenerValidacionEspecialidad($id_especialidad, $id_instructor, $memo_especialidad)
    {
        return DB::table('especialidad_instructores')
            ->where('especialidad_id', $id_especialidad)
            ->where('id_instructor', $id_instructor)
            ->whereExists(function ($query) use ($memo_especialidad) {
                $query->select(DB::raw("elem->>'arch_val'"))
                    ->from(DB::raw("jsonb_array_elements(hvalidacion) AS elem"))
                    ->where(DB::raw("elem->>'memo_val'"), '=', $memo_especialidad);
            })
            ->value(DB::raw("(SELECT elem->>'arch_val' FROM jsonb_array_elements(hvalidacion) AS elem WHERE elem->>'memo_val' = '$memo_especialidad') as pdfvalida"));
    }
}
