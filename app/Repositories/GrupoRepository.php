<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Interfaces\Repositories\GrupoRepositoryInterface;

class GrupoRepository implements GrupoRepositoryInterface
{
    /**
     * Obtiene la información completa del grupo con todos los joins necesarios
     */
    public function obtenerGrupoPorFolio($folio_grupo)
    {
        return DB::table('alumnos_registro as ar')
            ->where('ar.folio_grupo', $folio_grupo)
            ->select(
                // DE LA APERTURA
                DB::raw('COALESCE(tc.folio_grupo, ar.folio_grupo) as folio_grupo'),
                DB::raw("COALESCE(tc.clave, '0') as clave"),
                DB::raw('COALESCE(tc.tdias, null) as tdias'),
                DB::raw('COALESCE(tc.mexoneracion, null) as mexoneracion'),
                DB::raw('COALESCE(tc.dia, null) as dia'),
                DB::raw('COALESCE(tc.cgeneral, null) as cgeneral'),
                DB::raw('COALESCE(tc.fcgen, null) as fcgen'),
                DB::raw('COALESCE(tc.tipo, null) as tipo'),

                // DEL GRUPO
                DB::raw('COALESCE(tc.id_cerss, ar.id_cerss) as id_cerss'),
                DB::raw('COALESCE(tc.inicio, ar.inicio) as inicio'),
                DB::raw('COALESCE(tc.termino, ar.termino) as termino'),
                DB::raw('COALESCE(tc.clave_localidad, ar.clave_localidad) as clave_localidad'),
                DB::raw('COALESCE(tc.unidad, ar.unidad) as unidad'),
                DB::raw('COALESCE(tc.id_gvulnerable, null) as id_gvulnerable'),
                DB::raw('COALESCE(tc.efisico, ar.efisico) as efisico'),
                DB::raw('COALESCE(tc.medio_virtual, ar.medio_virtual) as medio_virtual'),
                DB::raw('COALESCE(tc.link_virtual, ar.link_virtual) as link_virtual'),
                DB::raw('COALESCE(tc.cespecifico, ar.cespecifico) as cespecifico'),
                DB::raw('COALESCE(tc.fcespe, ar.fcespe) as fcespe'),
                DB::raw('COALESCE(tc.depen_representante, ar.depen_repre) as depen_repre'),
                DB::raw('COALESCE(tc.depen_telrepre, ar.depen_telrepre) as depen_telrepre'),
                DB::raw('COALESCE(tc.tcapacitacion, ar.tipo_curso) as tcapacitacion'),
                DB::raw("SUBSTRING( COALESCE(
                    CASE WHEN tc.hini LIKE '%p%' and SUBSTRING(tc.hini, 1, 2)::integer <> 12 THEN (SUBSTRING(tc.hini, 1, 5)::time+'12:00')::text
                         ELSE SUBSTRING(tc.hini, 1, 5)
                    END, SUBSTRING(ar.horario, 1, 5)),1,5) as hini"),
                DB::raw("SUBSTRING(COALESCE(
                    CASE WHEN tc.hfin LIKE '%p%' and SUBSTRING(tc.hfin, 1, 2)::integer <> 12 THEN (SUBSTRING(tc.hfin, 1, 5)::time+'12:00')::text
                         ELSE SUBSTRING(tc.hfin, 1, 5)
                    END,  SUBSTRING(ar.horario, 9, 5)),1,5) as hfin"),

                DB::raw('COALESCE(tc.id_municipio, ar.id_muni) as id_municipio'),
                DB::raw('COALESCE(tc.depen, ar.organismo_publico) as depen'),
                DB::raw('COALESCE(tc.depen_telrepre, ar.depen_telrepre) as depen_telrepre'),
                DB::raw('COALESCE(tc.tipo_curso, ar.servicio) as tipo_curso'),
                DB::raw("COALESCE(tc.id_especialidad, ar.id_especialidad) as id_especialidad"),
                DB::raw("COALESCE(tc.instructor_mespecialidad, '') as instructor_mespecialidad"),
                DB::raw('COALESCE(tc.mod, ar.mod) as mod'),
                DB::raw('COALESCE(tc.status_curso, null) as status_curso'),
                DB::raw('COALESCE(tc.unidad, ar.unidad) as unidad'),
                DB::raw('COALESCE(tc.id_instructor, ar.id_instructor) as id_instructor'),
                DB::raw('COALESCE(tc.plantel, null) as plantel'),
                DB::raw('COALESCE(tc.programa, null) as programa'),
                DB::raw('COALESCE(tr.folio_recibo, COALESCE(tc.folio_pago, ar.folio_pago)) as folio_pago'),
                DB::raw('COALESCE(tr.fecha_expedicion, COALESCE(tc.fecha_pago, ar.fecha_pago)) as fecha_pago'),
                DB::raw("COALESCE(tc.solicita, CONCAT(tu.vinculacion,', ',pvinculacion)) as solicita"),
                DB::raw('COALESCE(tc.tdias, null) as tdias'),
                DB::raw('COALESCE(tc.id_curso, ar.id_curso) as id_curso'),
                DB::raw('COALESCE(tc.curso, c.nombre_curso) as nombre_curso'),
                DB::raw('COALESCE(tc.clave_localidad, ar.clave_localidad) as clave_localidad'),
                
                // DE OTRAS TABLAS
                DB::raw('ar.mpreapertura'),
                DB::raw('ar.turnado as turnado_grupo'),
                DB::raw('ar.observaciones as obs_vincula'),
                DB::raw("CASE WHEN tu.vinculacion=tu.dunidad THEN true ELSE false END as editar_solicita"),
                DB::raw("CASE WHEN tr.folio_recibo is not null THEN true ELSE false END as es_recibo_digital"),
                'exo.status as exo_status',
                'exo.nrevision as exo_nrevision',
                DB::raw('COALESCE(tc.vb_dg, false) as vb_dg')
            )
            ->leftjoin('tbl_cursos as tc', 'tc.folio_grupo', 'ar.folio_grupo')
            ->leftJoin('tbl_recibos as tr', function ($join) {
                $join->on('tr.folio_grupo', '=', 'ar.folio_grupo')
                     ->where('tr.status_folio', 'ENVIADO');
            })
            ->leftJoin('exoneraciones as exo', function ($join) {
                $join->on('exo.folio_grupo', '=', 'ar.folio_grupo')
                     ->where('exo.status', '!=', 'CANCELADO');
            })
            ->leftjoin('cursos as c', 'c.id', 'ar.id_curso')
            ->leftjoin('tbl_unidades as tu', 'ar.unidad', 'tu.unidad')
            ->first();
    }

    /**
     * Obtiene los alumnos de un grupo
     */
    public function obtenerAlumnosPorFolio($folio_grupo)
    {
        return Alumno::busqueda($folio_grupo, 'grupo')->get();
    }

    /**
     * Verifica si existen exoneraciones en edición para un grupo
     */
    public function existeExoneracionEnEdicion($folio_grupo)
    {
        return DB::table('exoneraciones')
            ->where('folio_grupo', $folio_grupo)
            ->where('status', 'EDICION')
            ->exists();
    }

    /**
     * Cuenta el número de alumnos en un grupo
     */
    public function contarAlumnosGrupo($folio_grupo)
    {
        return DB::table('alumnos_registro')
            ->where('folio_grupo', $folio_grupo)
            ->where('eliminado', false)
            ->count();
    }
}
