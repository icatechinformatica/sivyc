<?php

namespace App\Http\Controllers\Validacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class validacionDtaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $unidades = $request->get('busqueda_unidad');

        $cursos_validar = DB::table('tbl_cursos as c')
        ->select('c.id AS id_tbl_cursos', 'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MUJER' then 1 else 0 end) as iesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as naesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as naesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),
        DB::raw("case when arc='01' then nota else observaciones end as tnota"),
        DB::raw("c.observaciones_formato_t->'OBSERVACION_UNIDAD_DTA'->>'OBSERVACION_UNIDAD' AS observaciones_unidad")
        )
        ->JOIN('tbl_calificaciones as ca','c.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','c.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('c.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('c.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->WHERE('u.ubicacion', '=', $unidades)
        ->WHERE('c.status', '=', 'TURNADO_DTA')                
        ->WHERE(DB::raw("extract(year from c.termino)"), '=', '2021')
        ->WHERE('c.turnado', '=', 'DTA')
        ->groupby('c.unidad','c.nombre','c.clave','c.mod','c.espe','c.curso','c.inicio','c.termino','c.dia','c.dura','c.hini','c.hfin','c.horas','c.plantel','c.programa','c.muni','c.depen','c.cgeneral','c.mvalida','c.efisico','c.cespecifico','c.sector','c.mpaqueteria','c.mexoneracion','c.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','c.observaciones'
                 ,'ins.abrinscri','c.arc', 'c.id')
        ->distinct()->get();

        $memorandum = DB::table('tbl_cursos')
                      ->select(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM' AS memorandum, memos->'TURNADO_EN_FIRMA'->>'NUMERO' AS num_memo"))
                      ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                      ->where('turnado', '=', 'DTA')
                      ->where('tbl_unidades.ubicacion', '=', $unidades)
                      ->groupby(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM', memos->'TURNADO_EN_FIRMA'->>'NUMERO'"))
                      ->first();
        /**
         * vamos a consultar para regresar cursos a la unidad
         */
        $regresar_unidad = DB::table('tbl_cursos')
                               ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                               ->where('turnado', '=', 'REVISION_DTA')
                               ->where('status', '=', 'REVISION_DTA')
                               ->where('tbl_unidades.ubicacion', '=', $unidades)
                               ->get();

        $unidades = DB::table('tbl_unidades')->select('unidad', 'ubicacion')->get();

        //dd($cursos_validar);
        return view('reportes.vista_validaciondta', compact('cursos_validar', 'unidades', 'memorandum', 'regresar_unidad')); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRevision(Request $request)
    {
        $unidades = $request->get('busqueda_unidad');

        $cursos_validar = DB::table('tbl_cursos as c')
        ->select('c.id AS id_tbl_cursos', 'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MUJER' then 1 else 0 end) as iesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
        db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as naesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
        db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as naesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
        db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
        db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),
        DB::raw("case when arc='01' then nota else observaciones end as tnota"),
        DB::raw("c.observaciones_formato_t->'OBSERVACIONES_REVISION_DTA'->>'OBSERVACION_REVISION_JEFE_DTA' AS observaciones_enlaces")
        )
        ->JOIN('tbl_calificaciones as ca','c.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','c.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('c.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('c.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->WHERE('u.ubicacion', '=', $unidades)
        ->WHERE('c.status', '=', 'REVISION_DTA')                
        ->WHERE(DB::raw("extract(year from c.termino)"), '=', '2021')
        ->WHERE('c.turnado', '=', 'REVISION_DTA')
        ->groupby('c.unidad','c.nombre','c.clave','c.mod','c.espe','c.curso','c.inicio','c.termino','c.dia','c.dura','c.hini','c.hfin','c.horas','c.plantel','c.programa','c.muni','c.depen','c.cgeneral','c.mvalida','c.efisico','c.cespecifico','c.sector','c.mpaqueteria','c.mexoneracion','c.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','c.observaciones'
                 ,'ins.abrinscri','c.arc', 'c.id')
        ->distinct()->get();
        

        $memorandum = DB::table('tbl_cursos')
                      ->select(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM' AS memorandum, memos->'TURNADO_EN_FIRMA'->>'NUMERO' AS num_memo"))
                      ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                      ->where('turnado', '=', 'REVISION_DTA')
                      ->where('status', '=', 'REVISION_DTA')
                      ->where('tbl_unidades.ubicacion', '=', $unidades)
                      ->groupby(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM', memos->'TURNADO_EN_FIRMA'->>'NUMERO'"))
                      ->first();
        /**
         * vamos a consultar para regresar cursos a la unidad
         */
        $regresar_unidad = DB::table('tbl_cursos')
                               ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                               ->where('turnado', '=', 'REVISION_DTA')
                               ->where('status', '=', 'REVISION_DTA')
                               ->where('tbl_unidades.ubicacion', '=', $unidades)
                               ->get();

        $unidades = DB::table('tbl_unidades')->select('unidad', 'ubicacion')->get();

        return view('reportes.vista_supervisiondta', compact('cursos_validar', 'unidades', 'memorandum', 'regresar_unidad')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // variables y creación de la fecha de retorno
        $fecha_actual = Carbon::now();
        $date = $fecha_actual->format('Y-m-d'); // fecha
        //dd($_POST['comentarios']);
        $validacion = $request->get('validarEnDta');
        if (isset($validacion)) {
            # hacemos un switch
            switch ($validacion) {

                case 'EnviarJefaDta':
                    # en esta parte se envía a la jefa de DTA para validación y envío a Planeación
                    // TURNADO_VALIDACION_DIRECCION_DTA[FECHA: "XXXX-XX-XX"]
                    $turnado_revision_dta = [
                        'FECHA' => $date
                    ];

                    if (!empty($_POST['chkcursos'])) {
                        # entramos al loop
                        foreach ($_POST['chkcursos'] as $key => $value) {
                            $observaciones_revision_dta = [
                                'OBSERVACION_REVISION_JEFE_DTA' =>  $_POST['comentarios_enlaces'][$key]
                            ];
                            # modificaciones
                            \DB::table('tbl_cursos')
                                    ->where('id', $value)
                                    ->update(['memos' => 
                                    DB::raw("jsonb_set(memos, '{TURNADO_REVISION_DTA}','".json_encode($turnado_revision_dta)."'::jsonb)"), 
                                    'status' => 'REVISION_DTA', 
                                    'turnado' => 'REVISION_DTA',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_REVISION_DTA}', '".json_encode($observaciones_revision_dta)."'::jsonb)")]);
                        }
                        return redirect()->route('validacion.cursos.enviados.dta')
                                ->with('success', sprintf('CURSOS ENVIADOS A PLANEACIÓN PARA REVISIÓN!'));
                    } else {
                        # regresamos y mandamos un mensaje de error
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }
                    break;
                case 'GenerarMemorandum':
                     # entramos a un loop y antes checamos que se haya seleccionado cursos para realizar esta operacion
                     if (!empty($_POST['chkcursos'])) {
                         # si no están vacios enviamos a un loop
                         foreach ($_POST['chkcursos'] as $key => $value) {
                             # aqui vas a generar el documento pdf Julio del memorandum de devolución para las unidades
                             dd($value);
                         }
                     } else {
                         # hay cursos vacios, regresamos y mandamos un mensaje de error
                         return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                     }
                     
                    break;
                
                default:
                    # break
                    break;
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storetodta(Request $request)
    {
        // variables y creación de la fecha de retorno
        $fecha_actual = Carbon::now();
        $date = $fecha_actual->format('Y-m-d'); // fecha

        $validacion = $request->get('validarDireccionDta');
        if (isset($validacion)) {
            switch ($validacion) {
                case 'EnviarPlaneacion':
                    # enviar a planeación
                    # en esta parte del código tenemos que envíar a planeación
                    // TURNADO_PLANEACION[“NUMERO”:”XXXXXX”,FECHA:”XXXX-XX-XX”]
                    $turnado_planeacion = [
                        'FECHA' => $date
                    ];
                    if (!empty($request->get('chkcursos'))) {
                        # checamos que la variable no se encuentre vacia
                        foreach ($_POST['chkcursos'] as $key => $value) {
                            $observaciones_revision_a_planeacion = [
                                'OBSERVACION_REVISION_A_PLANEACION' =>  $_POST['comentarios'][$key]
                            ];
                            # entremos en el loop
                            \DB::table('tbl_cursos')
                                    ->where('id', $value)
                                    ->update(['memos' => DB::raw("jsonb_set(memos, '{TURNADO_PLANEACION}','".json_encode($turnado_planeacion)."'::jsonb)"), 
                                    'status' => 'TURNADO_PLANEACION', 
                                    'turnado' => 'PLANEACION',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_REVISION_PLANEACION}', '".json_encode($observaciones_revision_a_planeacion)."'::jsonb)")]);
                        }
                        return redirect()->route('validacion.dta.revision.cursos.indice')
                                ->with('success', sprintf('CURSOS ENVIADOS A PLANEACIÓN PARA REVISIÓN!'));
                    } else {
                        # hay cursos vacios, regresamos y mandamos un mensaje de error
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }
                    break;
                case 'RegresarEnlaceDta':
                    # regresar a la unidad
                    $regresar_enlace_dta = [
                        'FECHA' => $date
                    ];
                    if (!empty($request->get('chkcursos'))) {
                        # si no está vacio la variable iniciamos un loop
                        foreach ($_POST['chkcursos'] as $key => $value) {
                            # entramos en el bucle para actualizar los registros datos y enviarlos nuevamente a los enlaces
                            $observaciones_revision_dta_enlaces = [
                                'OBSERVACION_RETORNO_ENLACES' =>  $_POST['comentarios'][$key]
                            ];
                            # entremos en el loop
                            \DB::table('tbl_cursos')
                                ->where('id', $value)
                                ->update(['memos' => DB::raw("jsonb_set(memos, '{TURNADO_ENLACE_DTA}','".json_encode($regresar_enlace_dta)."'::jsonb)"), 
                                'status' => 'TURNADO_DTA', 
                                'turnado' => 'DTA',
                                'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_REVISION_ENLACES_DTA}', '".json_encode($observaciones_revision_dta_enlaces)."'::jsonb)")]);
                        }
                        return redirect()->route('validacion.dta.revision.cursos.indice')
                                ->with('success', sprintf('CURSOS ENVIADOS A PLANEACIÓN PARA REVISIÓN!'));
                    } else {
                        # hay cursos vacios, regresamos y mandamos un mensaje de error
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }
                    break;
                
                default:
                    # por defecto
                    break;
            }
        }
    }

    public function storedtafile(Request $request)
    {
        // $json = json_encode($request->all());
        //     return $json;
        // exit;

        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('check_cursos_dta');
        /**
         * vamos al cargar el archivo que se sube
         */
        if ($request->hasFile('memorandum_regreso_unidad')) {
            // obtenemos el valor del archivo memo

            $validator = Validator::make($request->all(), [
                'memorandum_regreso_unidad' => 'mimes:pdf|max:2048'
            ]);

            if ($validator->fails()) {
                # mandar un mensaje de error
                return json_encode($validator);
            } else {
                $memo = str_replace('/', '_', $numero_memo);
                /**
                 * aquí vamos a verificar que el archivo no se encuentre guardado
                 * previamente en el sistema de archivos del sistema de ser así se 
                 * remplazará el archivo porel que se subirá a continuación
                 */
                // construcción del archivo
                $archivo_memo = 'uploadFiles/memoRegresoUnidad/'.$memo.'/memorandum_regreso_unidad.pdf';
                if (Storage::exists($archivo_memo)) {
                    #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                    Storage::delete($archivo_memo);
                }

                $archivo_memo_to_dta = $request->file('memorandum_regreso_unidad'); # obtenemos el archivo
                $url_archivo_memo = $this->uploaded_memo_retorno_unidad_file($archivo_memo_to_dta, $memo); #invocamos el método
            }
        } else {
            $url_archivo_memo = null;
        }

        if (!empty($cursoschk)) {
            # vamos a checar sólo a los checkbox checados como propiedad
            if (!empty($cursoschk)) {
                $fecha_ahora = Carbon::now();
                $date = $fecha_ahora->format('Y-m-d'); // fecha
                $numero_memo = $request->get('numero_memo'); // número de memo

                $turnado_unidad = [
                    'FECHA' => $date,
                    'MEMORANDUM' => $url_archivo_memo
                ];
                /**
                 * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                 */
                # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                $data = explode(",", $cursoschk);
                $comentario = explode(",", $_POST['comentarios_enlaces']);
                foreach(array_combine($data, $comentario) as $key => $comentarios){
                    $comentarios_regreso_unidad = [
                        'OBSERVACION_RETORNO' =>  $comentarios
                    ];
                    \DB::table('tbl_cursos')
                        ->where('id', $key)
                        ->update(['memos' => DB::raw("jsonb_set(memos, '{TURNADO_UNIDAD}','".json_encode($turnado_unidad)."'::jsonb)"), 
                        'status' => 'RETORNO_UNIDAD', 
                        'turnado' => 'UNIDAD',
                        'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_RETORNO_UNIDAD}', '".json_encode($comentarios_regreso_unidad)."'::jsonb)")]);
                }



            }

            $json = json_encode('DONE');
            return $json;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function uploaded_memo_retorno_unidad_file($file, $memo)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim("memorandum_regreso_unidad.".$extensionFile);
        $path = '/memoRegresoUnidad/'.$memo.'/'.$documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/memoRegresoUnidad/'.$memo."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
