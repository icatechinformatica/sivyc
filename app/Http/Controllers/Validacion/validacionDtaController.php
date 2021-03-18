<?php

namespace App\Http\Controllers\Validacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Models\tbl_curso;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport

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
        $unidad = $request->get('busqueda_unidad');

        $cursos_validar = 
        tbl_curso::select('tbl_cursos.id AS id_tbl_cursos', 'tbl_cursos.unidad','tbl_cursos.plantel','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.dura',DB::raw("case when extract(hour from to_timestamp(tbl_cursos.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from tbl_cursos.inicio) as diai'),DB::raw('extract(month from tbl_cursos.inicio) as mesi'),DB::raw('extract(day from tbl_cursos.termino) as diat'),DB::raw('extract(month from tbl_cursos.termino) as mest'),DB::raw("case when EXTRACT( Month FROM tbl_cursos.termino) between '7' and '9' then '1' when EXTRACT( Month FROM tbl_cursos.termino) between '10' and '12' then '2' when EXTRACT( Month FROM tbl_cursos.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'tbl_cursos.horas','tbl_cursos.dia',DB::raw("concat(tbl_cursos.hini,' ', 'A', ' ',tbl_cursos.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'tbl_cursos.cespecifico','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','tbl_cursos.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.sector','tbl_cursos.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
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
        DB::raw("tbl_cursos.observaciones_formato_t->'OBSERVACION_UNIDAD_DTA'->>'OBSERVACION_UNIDAD' AS observaciones_unidad"),
        DB::raw("to_char(fecha_turnado, 'TMMONTH') AS fechaturnado")
        )
        ->JOIN('tbl_calificaciones as ca','tbl_cursos.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','tbl_cursos.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('tbl_cursos.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('tbl_cursos.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
        ->WHERE('tbl_cursos.status', '=', 'TURNADO_DTA')                
        ->WHERE(DB::raw("extract(year from tbl_cursos.termino)"), '=', '2021')
        ->WHERE('tbl_cursos.turnado', '=', 'DTA')
        ->WHERE('u.ubicacion', '=', $unidad)
        ->groupby('tbl_cursos.unidad','tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.dia','tbl_cursos.dura','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.horas','tbl_cursos.plantel','tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.cespecifico','tbl_cursos.sector','tbl_cursos.mpaqueteria','tbl_cursos.mexoneracion','tbl_cursos.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','tbl_cursos.observaciones'
                 ,'ins.abrinscri','tbl_cursos.arc', 'tbl_cursos.id')
        ->distinct()->get();
        
        $memorandum = DB::table('tbl_cursos')
                      ->select(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM' AS memorandum, memos->'TURNADO_EN_FIRMA'->>'NUMERO' AS num_memo"))
                      ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                      ->where('turnado', '=', 'DTA')
                      ->where('tbl_unidades.ubicacion', '=', $unidad)
                      ->groupby(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM', memos->'TURNADO_EN_FIRMA'->>'NUMERO'"))
                      ->first();
        
        
        /**
         * vamos a consultar para regresar cursos a la unidad
         */
        $regresar_unidad = DB::table('tbl_cursos')
                               ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                               ->where('turnado', '=', 'REVISION_DTA')
                               ->where('status', '=', 'REVISION_DTA')
                               ->get();

        $unidades = DB::table('tbl_unidades')
                    ->select('unidad')
                    ->orderBy('unidad', 'asc')->get();

        $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mesActual = $meses[($fecha->format('n')) - 1];
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mesActual)->first();
        $dateNow = $fechaEntregaActual->fecha_entrega."-".$anioActual;
        $mesInformar = $fechaEntregaActual->mes_informar;

        $convertfEAc = date_create_from_format('d-m-Y', $dateNow);
        $mesEntrega = $meses[($convertfEAc->format('n')) - 1];
        $fechaEntregaFormatoT = $convertfEAc->format('d') . ' DE ' . $mesEntrega . ' DE ' . $convertfEAc->format('Y');

        $diasParaEntrega = $this->getFechaDiff();

        //dd($cursos_validar);
        return view('reportes.vista_validaciondta', compact('cursos_validar', 'unidades', 'memorandum', 'regresar_unidad', 'fechaEntregaFormatoT', 'mesInformar', 'unidad', 'diasParaEntrega')); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRevision(Request $request)
    {
        $unidades_busqueda = $request->get('busqueda_unidad');

        $cursos_validar = 
        tbl_curso::searchbydata($unidades_busqueda)->select('tbl_cursos.id AS id_tbl_cursos', 'tbl_cursos.unidad','tbl_cursos.plantel','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.dura',DB::raw("case when extract(hour from to_timestamp(tbl_cursos.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from tbl_cursos.inicio) as diai'),DB::raw('extract(month from tbl_cursos.inicio) as mesi'),DB::raw('extract(day from tbl_cursos.termino) as diat'),DB::raw('extract(month from tbl_cursos.termino) as mest'),DB::raw("case when EXTRACT( Month FROM tbl_cursos.termino) between '7' and '9' then '1' when EXTRACT( Month FROM tbl_cursos.termino) between '10' and '12' then '2' when EXTRACT( Month FROM tbl_cursos.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'tbl_cursos.horas','tbl_cursos.dia',DB::raw("concat(tbl_cursos.hini,' ', 'A', ' ',tbl_cursos.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'tbl_cursos.cespecifico','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','tbl_cursos.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.sector','tbl_cursos.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
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
        DB::raw("tbl_cursos.observaciones_formato_t->'OBSERVACIONES_REVISION_DTA'->>'OBSERVACION_REVISION_JEFE_DTA' AS observaciones_enlaces")
        )
        ->JOIN('tbl_calificaciones as ca','tbl_cursos.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','tbl_cursos.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('tbl_cursos.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('tbl_cursos.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
        ->WHERE('tbl_cursos.status', '=', 'REVISION_DTA')                
        ->WHERE(DB::raw("extract(year from tbl_cursos.termino)"), '=', '2021')
        ->WHERE('tbl_cursos.turnado', '=', 'REVISION_DTA')
        ->groupby('tbl_cursos.unidad','tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.dia','tbl_cursos.dura','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.horas','tbl_cursos.plantel','tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.cespecifico','tbl_cursos.sector','tbl_cursos.mpaqueteria','tbl_cursos.mexoneracion','tbl_cursos.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','tbl_cursos.observaciones'
                 ,'ins.abrinscri','tbl_cursos.arc', 'tbl_cursos.id')
        ->distinct()->get();
        

        $memorandum = DB::table('tbl_cursos')
                      ->select(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM' AS memorandum, memos->'TURNADO_EN_FIRMA'->>'NUMERO' AS num_memo, tbl_unidades.unidad"))
                      ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                      ->where('turnado', '=', 'REVISION_DTA')
                      ->where('status', '=', 'REVISION_DTA')
                      ->groupby(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM', memos->'TURNADO_EN_FIRMA'->>'NUMERO', tbl_unidades.unidad"))
                      ->get();

        $unidades = DB::table('tbl_unidades')->select('unidad')->orderBy('unidad', 'asc')->get();;

        return view('reportes.vista_supervisiondta', compact('cursos_validar', 'unidades', 'memorandum', 'unidades_busqueda')); 
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
        $fecha_nueva=$fecha_actual->format('d-m-Y');
        //dd($request->num_memo);
        //dd($request->all());
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
                        //  foreach ($_POST['chkcursos'] as $key => $value) { 
                             
                        //     # aqui vas a generar el documento pdf Julio del memorandum de devolución para las unidades
                        //      //dd($value);
                        //  }
                        $nume_memo=$request->num_memo_devolucion;
                        $unidadSeleccionada = $request->get('unidadActual');
                        $total=count($_POST['chkcursos']);                
                        $mes='1';
                        $reg_cursos=DB::table('tbl_cursos')->select(DB::raw("case when EXTRACT( Month FROM termino) = '1' then 'ENERO' when EXTRACT( Month FROM termino) = '2' then 'FEBRERO' when EXTRACT( Month FROM termino) = '3' then 'MARZO' when EXTRACT( Month FROM termino) = '4' then 'ABRIL' when EXTRACT( Month FROM termino) = '5' then 'MAYO' when EXTRACT( Month FROM termino) = '6' then 'JUNIO' when EXTRACT( Month FROM termino) = '7' then 'JULIO' when EXTRACT( Month FROM termino) = '8' then 'AGOSTO' when EXTRACT( Month FROM termino) = '9' then 'SEPTIEMBRE' when EXTRACT( Month FROM termino) = '10' then 'OCTUBRE' when EXTRACT( Month FROM termino) = '11' then 'NOVIEMBRE' else 'DICIEMBRE' end as mes")
                                    ,'unidad','espe','curso','clave', 'status')
                        ->where('memos->TURNADO_EN_FIRMA->NUMERO',$request->num_memo)
                        ->where('turnado',"DTA")
                        ->groupby('unidad','espe','curso','clave','termino', 'status')
                        ->orderby('mes')->get();
                        $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                        'pvinculacion','jcyc','pjcyc')->where('unidad', $unidadSeleccionada)->first();
                        $pdf = PDF::loadView('reportes.memounidad',compact('reg_cursos','reg_unidad','nume_memo','total','fecha_nueva'));
                        return $pdf->download('Memo_Unidad.pdf');
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

        $numero_memo = $request->get('numero_memo_devolucion'); // número de memo
        $cursoschk = $request->get('check_cursos_dta');
        /***
         * vamos a checar el curso de dta
         */
        if (!empty($cursoschk)) {
            # si entramos en esta parte es que hay registros de cursos
            if ($request->hasFile('memorandum_regreso_unidad')) {
                # obtenemos el valor del archivo memo
                $validator = validator::make($request->all(), [
                    'memorandum_regreso_unidad' => 'mimes:pdf|max:2048'
                ]);
                if ($validator->fails()) {
                    # mandar mensaje de error si falla el cargado del archivo
                    return back()->withInput()->withErrors([$validator]);
                } else {
                    # si la validación no falla es hora de subir el archivo
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
                    $url_archivo_memo = $this->uploaded_memo_retorno_unidad_file($archivo_memo_to_dta, $memo, 'memoRegresoUnidad'); #invocamos el método
                }
                
            } else {
                # si está vacio sólo cargamos la url
                $url_archivo_memo = null;
            }
            $fecha_ahora = Carbon::now();
            $date = $fecha_ahora->format('Y-m-d'); // fecha
            /**
             * aquí vamos a vaciar el arreglo en un ciclo que vamos a iterar para obtener los valores y hacer multiples
             * actualizaciones de los registros para enviar la información
             */
            $turnado_unidad = [
                'FECHA' => $date,
                'MEMORANDUM' => $url_archivo_memo,
                'NUMERO' => $numero_memo
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
            // enviar  a la página de inicio del módulo si el proceso fue satisfactorio
            return redirect()->route('validacion.cursos.enviados.dta')
            ->with('success', sprintf('CURSOS TURNADO A LA UNIDAD CORRESPONDIENTE!'));
        } else {
            # no hay cursos (están vacios) se tiene que cargar un mensaje de error
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
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

    protected function uploaded_memo_retorno_unidad_file($file, $memo, $subpath)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim("memorandum_regreso_unidad.".$extensionFile);
        $path = '/'.$subpath.'/'.$memo.'/'.$documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/'.$subpath.'/'.$memo."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function entrega_planeacion(Request $request)
    {
        $valor = $request->get('validarDireccionDta');
        if (isset($valor)) {
            # si la variable está inicializada procedemos a meterlo en el switch
            switch ($valor) {
                case 'generarMemoPlaneacion':
                    /**
                     * GENERAR MEMORANDUM
                     */
                    # generamos el memo de entrega a planeacion.
                    $numMemo = $request->get('num_memo_devolucion');
                    return $this->generarMemorandumPlaneacion($numMemo);
                    break;
                case 'RegresarEnlaceDta':
                    /**
                     * TURNADO_RETORNO_ENLACES
                     */
                    # regresamos el paquete a los enlaces que no está bien
                    $cursoschk = $request->get('chkcursos');
                    if (!empty($cursoschk)) {
                        $fecha_ahora = Carbon::now();
                        $date = $fecha_ahora->format('Y-m-d'); // fecha
                        # generamos el código para enviar de regreso a los enlaces los cursos que no han sido satisfactorios
                        $numMemorandum = $request->get('num_memo_devolucion');
                        $turnado_retorno_unidad = [
                            'FECHA' => $date,
                            'MEMORANDUM' => $numMemorandum
                        ];

                        foreach ($_POST['chkcursos'] as $key => $value) {
                            # recorremos el bucle para vaciar nuestro contenido en la consulta
                            $observaciones_retorno_enlace = [
                                'OBSERVACION_PARA_ENLACES_DTA' =>  $_POST['comentarios'][$key]
                            ];
                            # modificaciones
                            \DB::table('tbl_cursos')->where('id', $value)
                                ->update(['memos' => 
                                    DB::raw("jsonb_set(memos, '{TURNADO_RETORNO_ENLACES}','".json_encode($turnado_retorno_unidad)."'::jsonb)"), 
                                    'status' => 'TURNADO_DTA', 
                                    'turnado' => 'DTA',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_RETORNO_ENLACES}', '".json_encode($observaciones_retorno_enlace)."'::jsonb)")]);
                        }
                        return redirect()->route('validacion.dta.revision.cursos.indice')
                                ->with('success', sprintf('CURSOS ENVIADOS DE REGRESO PARA LOS ENLACES DTA!'));
                    } else {
                        # enviamos un mensaje de que no se pudo generar debido a que no hay registros
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }
                    
                    break;
                default:
                    # code...
                    break;
            }
        }
        
    }

    private function generarMemorandumPlaneacion($num_memo_planeacion)
    {
        if (isset($num_memo_planeacion)) {
            /**
             * mandar información fecha
             */
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $fecha = Carbon::parse(Carbon::now());
            $anioActual = Carbon::now()->year;
            $mes_ = $meses[($fecha->format('n')) - 1];
            /**
             * obtener mes anterior
            */
            $fechaActual = Carbon::now()->format('d-m-Y');
            $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mes_)->first();
            $fEAc = $fechaEntregaActual->fecha_entrega."-".$anioActual;
            $convertfEAc = date_create_from_format('d-m-Y', $fEAc);
            $confEAct = date_format($convertfEAc, 'd-m-Y');
            $fechaSpring = strtotime($confEAct);
            $fechaActual_ = strtotime($fechaActual);

            if ($fechaSpring >= $fechaActual_) {
                # si la condición de fecha de entrega se cumple es mayor a la fecha actual o igual entonces el mes el el actual
                $mesDato = $fechaEntregaActual->mes_informar;
            } else {
                # si la condición no se cumple por lo consiguiente se agrega un mes más 
                $mesDato = $meses[($fecha->format('n')) + 0];
            }
            
            # GENERAMOS EL DOCUMENTO EN PDF
            $value = 'JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO';
            $jefdepto = 'JEFE DE DEPARTAMENTO DE CERTIFICACION Y CONTROL';
            // fecha actual
            $fecha_ahora = Carbon::now();
            $fecha = $fecha_ahora->format('Y-m-d'); // fecha
            // arreglo de meses
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $fechaFormato = Carbon::parse($fecha);
            $mes = $meses[($fechaFormato->format('n')) - 1];
            $fecha_ahora_espaniol = $fechaFormato->format('d') . ' de ' . $mes . ' de ' . $fechaFormato->format('Y');
            // registro de las unidades
            $reg_unidad = DB::table('tbl_unidades')->select('academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                            'pvinculacion','jcyc','pjcyc', 'dgeneral', 'pdgeneral')->groupby('academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                            'pvinculacion','jcyc','pjcyc', 'dgeneral', 'pdgeneral')->first();
            $directorio = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$value}%")->first();
            $jefeDepto = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$jefdepto}%")->first();
            $directorPlaneacion = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('id', 14)->first();
            $pdf = PDF::loadView('layouts.pdfpages.formatot_entrega_planeacion', compact('fecha_ahora_espaniol', 'reg_unidad', 'num_memo_planeacion', 'directorio', 'jefeDepto', 'directorPlaneacion', 'mesDato'));
            // return $pdf->stream('Memorandum_entrega_formato_t_a_planeacion.pdf');
            return $pdf->download('Memorandum_entrega_formato_t_a_planeacion.pdf');
        } else {
            # enviamos mensaje de error o direccionamos para enviarlo con el mensaje de error
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, SE NECESITA EL NÚMERO DE MEMORANDUM']);
        }
        
    }

    protected function getFechaDiff(){

        $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mes = $meses[($fecha->format('n')) - 1];
        /**
         * obtener mes anterior
         */
        $mesAnterior = $meses[($fecha->format('n')) - 2];
        $fechaActual = Carbon::now()->format('d-m-Y');
        /**
         * hacemos una consulta a la tabla para obtener el mes correspondiente
         */
        $fechaEntregaAnterior = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $mesAnterior)->first();
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $mes)->first();
        $fEAn = $fechaEntregaAnterior->fecha_entrega."-".$anioActual;
        $fEAc = $fechaEntregaActual->fecha_entrega."-".$anioActual;
        /**
         * fechaAnteriorEntrega convertirla a fecha
         */
        $convertfEAn = date_create_from_format('d-m-Y', $fEAn);
        $confEAn = date_format($convertfEAn, 'd-m-Y');
        $comconfEAn = strtotime($confEAn);
        $comfechaActual = strtotime($fechaActual);
        $convertfEAc = date_create_from_format('d-m-Y', $fEAc);
        $confEAc = date_format($convertfEAc, 'd-m-Y');
        $comconfEAc = strtotime($confEAc); // fecha actual de entrega
        $dias = (strtotime($confEAc) - strtotime($fechaActual))/86400;
        $dias = abs($dias); $dias = floor($dias);

        return $dias;
    }

    protected function xlsExportReporteFormatotEnlacesUnidad(Request $request){
        $anio_actual = Carbon::now()->year;
        $unidadActual = $request->unidad_;
        // cursos unidades por planeacion
        $formatot_enlace_dta =
        tbl_curso::select('tbl_cursos.unidad','tbl_cursos.plantel','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.dura',DB::raw("case when extract(hour from to_timestamp(tbl_cursos.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from tbl_cursos.inicio) as diai'),DB::raw('extract(month from tbl_cursos.inicio) as mesi'),DB::raw('extract(day from tbl_cursos.termino) as diat'),DB::raw('extract(month from tbl_cursos.termino) as mest'),DB::raw("case when EXTRACT( Month FROM tbl_cursos.termino) between '7' and '9' then '1' when EXTRACT( Month FROM tbl_cursos.termino) between '10' and '12' then '2' when EXTRACT( Month FROM tbl_cursos.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'tbl_cursos.horas','tbl_cursos.dia',DB::raw("concat(tbl_cursos.hini,' ', 'A', ' ',tbl_cursos.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'tbl_cursos.cespecifico','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','tbl_cursos.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.sector','tbl_cursos.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
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
        DB::raw("case when arc='01' then nota else observaciones end as tnota")
        )
        ->JOIN('tbl_calificaciones as ca','tbl_cursos.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','tbl_cursos.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('tbl_cursos.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('tbl_cursos.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
        ->WHERE('tbl_cursos.status', '=', 'TURNADO_DTA')                
        ->WHERE(DB::raw("extract(year from tbl_cursos.termino)"), '=', $anio_actual)
        ->WHERE('tbl_cursos.turnado', '=', 'DTA')
        ->WHERE('u.ubicacion', '=', $unidadActual)
        ->groupby('tbl_cursos.unidad','tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.dia','tbl_cursos.dura','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.horas','tbl_cursos.plantel','tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.cespecifico','tbl_cursos.sector','tbl_cursos.mpaqueteria','tbl_cursos.mexoneracion','tbl_cursos.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','tbl_cursos.observaciones'
                 ,'ins.abrinscri','tbl_cursos.arc', 'tbl_cursos.id')
        ->distinct()->get();


        $head = ['UNIDAD','PLANTEL','ESPECIALIDAD','CURSO','CLAVE','MOD','DURA','TURNO','DIAI','MESI','DIAT','MEST', 'PERI', 'HORAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MAS',
        'EGRESADO', 'EMUJER', 'EHOMBRE', 'DESER', 'COSTO', 'TOTAL', 'ETMUJER', 'ETHOMBRE', 'EPMUJER', 'EPHOMBRE', 'ESPECIFICO', 'MVALIDA', 'ESPACIO FISICO',
        'INSTRUCTOR', 'ESCOLARIDAD', 'DOCUMENTO', 'SEXO', 'MEMO VALIDACION', 'MEMO EXONERACION', 'TRABAJAN', 'NO TRABAJAN', 'DISCAPACITADOS', 'MIGRANTE',
        'INDIGENA', 'ETNIA', 'PROGRAMA', 'MUNICIPIO', 'DEPENDENCIA BENEFICIADA', 'GENERAL', 'SECTOR', 'VALIDACION PAQUETERIA', 'IEDADM1', 'IEDADH1', 'IEDADM2', 
        'IEDADH2', 'IEDADM3', 'IEDADH3', 'IEDADM4', 'IEDADH4', 'IEDADM5', 'IEDADH5', 'IEDADM6', 'IEDADH6', 'IEDADM7', 'IEDADH7',
        'IEDADM8', 'IEDADH8', 'IESCOLM1', 'IESCOLH1', 'IESCOLM2', 'IESCOLH2', 'IESCOLM3', 'IESCOLH3', 'IESCOLM4',
        'IESCOLH4', 'IESCOLM5', 'IESCOLH5', 'IESCOLM6', 'IESCOLH6', 'IESCOLM7', 'IESCOLH7',
        'IESCOLM8', 'IESCOLH8', 'IESCOLM9', 'IESCOLH9', 'AESCOLM1', 'AESCOLH1', 'AESCOLM2', 'AESCOLH2', 'AESCOLM3',
        'AESCOLH3', 'AESCOLM4', 'AESCOLH4', 'AESCOLM5', 'AESCOLH5', 'AESCOLM6', 'AESCOLH6', 'AESCOLM7',
        'AESCOLH7', 'AESCOLM8', 'AESCOLH8', 'AESCOLM9', 'AESCOLH9', 'NAESCOLM1', 'NAESCOLH1', 'NAESCOLM2', 'NAESCOLH2',
        'NAESCOLM3', 'NAESCOLH3', 'NAESCOLM4', 'NAESCOLH4', 'NAESCOLM5', 'NAESCOLH5', 'NAESCOLM6', 'NAESCOLH6', 'NAESCOLM7', 'NAESCOLH7', 'NAESCOLM8', 'NAESCOLH8', 'NAESCOLM9', 'NAESCOLH9', 'OBSERVACIONES'];

        $nombreLayout = "FORMATO_T_PARA_ENLACES_DIRECCION_TECNICA_ACADEMICA.xlsx";
        $titulo = "FORMATO T PARA LOS ENLACES DE DIRECCIÓN TÉCNICA ACADÉMICA";

        if(count($formatot_enlace_dta)>0){  
            return Excel::download(new FormatoTReport($formatot_enlace_dta,$head, $titulo), $nombreLayout);
        }
    }

    /**
     * funcion protegida hecha para exportar el reporte T de formato para Directores de la dirección DTA
     */
    protected function xlsExportReporteFormatoTDirectorDTA(Request $request){
        $anioActual = Carbon::now()->year;

        $reporteDirectorDTA = tbl_curso::select('tbl_cursos.unidad','tbl_cursos.plantel','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.dura',DB::raw("case when extract(hour from to_timestamp(tbl_cursos.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from tbl_cursos.inicio) as diai'),DB::raw('extract(month from tbl_cursos.inicio) as mesi'),DB::raw('extract(day from tbl_cursos.termino) as diat'),DB::raw('extract(month from tbl_cursos.termino) as mest'),DB::raw("case when EXTRACT( Month FROM tbl_cursos.termino) between '7' and '9' then '1' when EXTRACT( Month FROM tbl_cursos.termino) between '10' and '12' then '2' when EXTRACT( Month FROM tbl_cursos.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'tbl_cursos.horas','tbl_cursos.dia',DB::raw("concat(tbl_cursos.hini,' ', 'A', ' ',tbl_cursos.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'tbl_cursos.cespecifico','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','tbl_cursos.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.sector','tbl_cursos.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        DB::raw("sum(Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        db::raw("sum(case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
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
        )
        ->JOIN('tbl_calificaciones as ca','tbl_cursos.id', '=', 'ca.idcurso')
        ->JOIN('instructores as i','tbl_cursos.id_instructor', '=', 'i.id')
        ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        ->JOIN('especialidades as e', function($join)
            {
                $join->on('ei.especialidad_id', '=', 'e.id');                
                $join->on('tbl_cursos.espe', '=', 'e.nombre');
            })
        ->JOIN('alumnos_registro as ar',function($join)
        {
            $join->on('ca.matricula', '=', 'ar.no_control');                
            $join->on('tbl_cursos.id_curso','=','ar.id_curso');
        }) 
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_inscripcion as ins', function($join)
        {
            $join->on('ca.idcurso', '=', 'ins.id_curso');                
            $join->on('ca.matricula','=','ins.matricula');
        })
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
        ->WHERE('tbl_cursos.status', '=', 'REVISION_DTA')                
        ->WHERE(DB::raw("extract(year from tbl_cursos.termino)"), '=', $anioActual)
        ->WHERE('tbl_cursos.turnado', '=', 'REVISION_DTA')
        ->groupby('tbl_cursos.unidad','tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.mod','tbl_cursos.espe','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.dia','tbl_cursos.dura','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.horas','tbl_cursos.plantel','tbl_cursos.programa','tbl_cursos.muni','tbl_cursos.depen','tbl_cursos.cgeneral','tbl_cursos.mvalida','tbl_cursos.efisico','tbl_cursos.cespecifico','tbl_cursos.sector','tbl_cursos.mpaqueteria','tbl_cursos.mexoneracion','tbl_cursos.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','tbl_cursos.observaciones'
                 ,'ins.abrinscri','tbl_cursos.arc', 'tbl_cursos.id')
        ->distinct()->get();

        $cabecera = ['UNIDAD','PLANTEL','ESPECIALIDAD','CURSO','CLAVE','MOD','DURA','TURNO','DIAI','MESI','DIAT','MEST', 'PERI', 'HORAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MAS',
        'EGRESADO', 'EMUJER', 'EHOMBRE', 'DESER', 'COSTO', 'TOTAL', 'ETMUJER', 'ETHOMBRE', 'EPMUJER', 'EPHOMBRE', 'ESPECIFICO', 'MVALIDA', 'ESPACIO FISICO',
        'INSTRUCTOR', 'ESCOLARIDAD', 'DOCUMENTO', 'SEXO', 'MEMO VALIDACION', 'MEMO EXONERACION', 'TRABAJAN', 'NO TRABAJAN', 'DISCAPACITADOS', 'MIGRANTE',
        'INDIGENA', 'ETNIA', 'PROGRAMA', 'MUNICIPIO', 'DEPENDENCIA BENEFICIADA', 'GENERAL', 'SECTOR', 'VALIDACION PAQUETERIA', 'IEDADM1', 'IEDADH1', 'IEDADM2', 
        'IEDADH2', 'IEDADM3', 'IEDADH3', 'IEDADM4', 'IEDADH4', 'IEDADM5', 'IEDADH5', 'IEDADM6', 'IEDADH6', 'IEDADM7', 'IEDADH7',
        'IEDADM8', 'IEDADH8', 'IESCOLM1', 'IESCOLH1', 'IESCOLM2', 'IESCOLH2', 'IESCOLM3', 'IESCOLH3', 'IESCOLM4',
        'IESCOLH4', 'IESCOLM5', 'IESCOLH5', 'IESCOLM6', 'IESCOLH6', 'IESCOLM7', 'IESCOLH7',
        'IESCOLM8', 'IESCOLH8', 'IESCOLM9', 'IESCOLH9', 'AESCOLM1', 'AESCOLH1', 'AESCOLM2', 'AESCOLH2', 'AESCOLM3',
        'AESCOLH3', 'AESCOLM4', 'AESCOLH4', 'AESCOLM5', 'AESCOLH5', 'AESCOLM6', 'AESCOLH6', 'AESCOLM7',
        'AESCOLH7', 'AESCOLM8', 'AESCOLH8', 'AESCOLM9', 'AESCOLH9', 'NAESCOLM1', 'NAESCOLH1', 'NAESCOLM2', 'NAESCOLH2',
        'NAESCOLM3', 'NAESCOLH3', 'NAESCOLM4', 'NAESCOLH4', 'NAESCOLM5', 'NAESCOLH5', 'NAESCOLM6', 'NAESCOLH6', 'NAESCOLM7', 'NAESCOLH7', 'NAESCOLM8', 'NAESCOLH8', 'NAESCOLM9', 'NAESCOLH9', 'OBSERVACIONES'];

        $nombreLayout = "FORMATO_T_PARA_DIRECTOR_DE_DIRECCION_TECNICA_ACADEMICA.xlsx";
        $titulo = "FORMATO T PARA DIRECTOR/A DE DIRECCIÓN TÉCNICA ACADÉMICA";

        if(count($reporteDirectorDTA)>0){  
            return Excel::download(new FormatoTReport($reporteDirectorDTA,$cabecera, $titulo), $nombreLayout);
        }
    }
}