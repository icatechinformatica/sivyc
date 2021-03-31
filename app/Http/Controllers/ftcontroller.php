<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport
use App\Models\tbl_curso;

class ftcontroller extends Controller
{
    public function index(Request $request)
    {
        // obtener el año actual --
        $anio_actual = Carbon::now()->year;
        $anio=$request->get("anio");
        $id_user = Auth::user()->id;
        // dd($id_user);exit;
        $rol = DB::table('role_user')
        ->select('roles.slug')
        ->leftjoin('roles', 'roles.id', '=', 'role_user.role_id')            
        ->where([['role_user.user_id', '=', $id_user], ['roles.slug', '=', 'unidad']])
        ->get();
        $_SESSION['unidades']=NULL;
        $meses = array(1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'Julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'diciembre');
        $enFirma = DB::table('tbl_cursos')->where('status', '=', 'EN_FIRMA')->get();
        $retornoUnidad = DB::table('tbl_cursos')->where('status', 'RETORNO_UNIDAD')->get();
        //var_dump($rol);exit;
        if (!empty($rol[0]->slug)) {
            # si no está vacio
            if($rol[0]->slug=='unidad')
            { 
                $unidad = Auth::user()->unidad;
                //dd($unidad);
                $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                $_SESSION['unidad'] = $unidad;
                // dd($_SESSION['unidad']);
            }

            $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, migrante, indigena, etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, migrante, indigena, etnia) as ar");

            $var_cursos = DB::table('tbl_cursos as c')
                ->select('c.id AS id_tbl_cursos', 'c.status AS estadocurso' ,'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
                DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
                'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
                DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
                DB::raw("SUM(DISTINCT(ins.costo)) as costo"),DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
                DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
                DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
                DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
                'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',

                DB::raw("sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),
                DB::raw("sum( CASE  WHEN  EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO'  THEN 1  ELSE 0 END ) as iem2"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),
                DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END ) as iem3"),
                DB::raw("sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),
                DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END ) as iem4"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
                db::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),

                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),

                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),

                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),

                DB::raw("case when c.arc='01' then nota else observaciones end as tnota"),
                DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD'->>'OBSERVACION_RETORNO' AS observaciones_enlaces"),
                DB::raw("count( ar.id_pre) AS totalinscripciones"),
                DB::raw("count( CASE  WHEN  ap.sexo ='MASCULINO' THEN ar.id_pre END ) AS masculinocheck"),
                DB::raw("count( CASE  WHEN ap.sexo ='FEMENINO' THEN ar.id_pre END ) AS femeninocheck"),
                DB::raw("COALESCE(sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO' 
                THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END )) + COALESCE(sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34 
                AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end)) as sumatoria_total_ins_edad")
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
                ->JOIN($temptblinner ,function($join)
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
                ->WHERE('u.ubicacion', '=', $_SESSION['unidad'])
                ->WHEREIN('c.status', ['NO REPORTADO', 'EN_FIRMA', 'RETORNO_UNIDAD'])
                ->WHERE('c.clave', '!=', 'NULL')
                // ->WHERE(DB::raw("extract(year from c.termino)"), '=', $anio_actual)
                ->groupby('c.id', 'ip.grado_profesional', 'ip.estatus', 'i.sexo', 'ei.memorandum_validacion', 'e.id')
                ->distinct()->get();

        } else {
            # si se encuentra vacio
            $var_cursos = null;
        }

        $meses_ = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mesActual = $meses_[($fecha->format('n')) - 1];
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mesActual)->first();
        $dateNow = $fechaEntregaActual->fecha_entrega."-".$anioActual;
        $mesInformar = $fechaEntregaActual->mes_informar;

        $convertfEAc = date_create_from_format('d-m-Y', $dateNow);
        $mesEntrega = $meses_[($convertfEAc->format('n')) - 1];
        $fechaEntregaFormatoT = $convertfEAc->format('d') . ' DE ' . $mesEntrega . ' DE ' . $convertfEAc->format('Y');
        $diasParaEntrega = $this->chkDateToDeliver();

        return view('reportes.vista_formatot',compact('var_cursos', 'meses', 'enFirma', 'retornoUnidad', 'fechaEntregaFormatoT', 'mesInformar', 'diasParaEntrega', 'unidad'));    
    }

    public function cursos(Request $request)
    {
        $año=$request->get("año");
        if ($año)
        { 
            return $this->busqueda($año);
        }
    }
    public function busqueda($año)
    {
        $id_user = Auth::user()->id;
        //var_dump($id_user);exit;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
        ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', '=', 'unidad')
        ->value('roles.slug');        
        $_SESSION['unidades']=NULL;
        $meses = array(1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'Julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'diciembre');
        $enFirma = DB::table('tbl_cursos')->where('status', '=', 'EN_FIRMA')->get();
        $retornoUnidad = DB::table('tbl_cursos')->where('status', 'RETORNO_UNIDAD')->get();
        //var_dump($rol);exit;
        if (!empty($rol[0]->slug)) {
            if($rol[0]->slug=='unidad')
            { 
                $unidad = Auth::user()->unidad;
                $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                $_SESSION['unidad'] = $unidad;             
            }
            //var_dump($_SESSION['unidades']);exit;
            $años = $año;
            $var_cursos = DB::table('tbl_cursos as c')->select('c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
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
                DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD'->>'OBSERVACION_RETORNO' AS observaciones_enlaces")
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
                ->WHERE('u.ubicacion', '=', $_SESSION['unidad'])
                ->WHEREIN('c.status', ['NO REPORTADO', 'EN_FIRMA', 'RETORNO_UNIDAD'])
                ->WHERE(DB::raw("extract(year from c.termino)"), '=', $anios)
                ->groupby('c.unidad','c.nombre','c.clave','c.mod','c.espe','c.curso','c.inicio','c.termino','c.dia','c.dura','c.hini','c.hfin','c.horas','c.plantel','c.programa','c.muni','c.depen','c.cgeneral','c.mvalida','c.efisico','c.cespecifico','c.sector','c.mpaqueteria','c.mexoneracion','c.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','c.observaciones'
                         ,'ins.abrinscri','c.arc')
                ->distinct()->get();
            // $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso FROM alumnos_registro GROUP BY id_pre, no_control, id_curso) AS ar");


        } else {
            # si se encuentra vacio
            $var_cursos = null;
        }
        
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
        $diasParaEntrega = $this->chkDateToDeliver();

        return view('reportes.vista_formatot',compact('var_cursos', 'meses', 'enFirma', 'retornoUnidad', 'fechaEntregaFormatoT', 'mesInformar', 'diasParaEntrega'));
                
    }

    /**
     * enviar a validación enlaces DTA - envía los cursos para su validación de las unidades a los enlaces DTA
     */
    public function paso2(Request $request)
    {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('check_cursos_dta');
        
        if (!empty($numero_memo)) {
            # si el número de memo no está vacio hay que iniciar todo el proceso
            if (!empty($cursoschk)) {
                /**
                 * vamos al cargar el archivo que se sube
                */
                if ($request->hasFile('cargar_archivo_formato_t')) {
                    // obtenemos el valor del archivo memo
    
                    $validator = Validator::make($request->all(), [
                        'cargar_archivo_formato_t' => 'mimes:pdf|max:10240'
                    ]);
    
                    if ($validator->fails()) {
                         # mandar mensaje de error si falla el cargado del archivo
                         return back()->withInput()->withErrors([$validator]);
                    } else {
                        $memo = str_replace('/', '_', $numero_memo);
                        /**
                         * aquí vamos a verificar que el archivo no se encuentre guardado
                         * previamente en el sistema de archivos del sistema de ser así se 
                         * remplazará el archivo porel que se subirá a continuación
                         */
                        // construcción del archivo
                        $archivo_memo = 'uploadFiles/memoValidacion/'.$memo.'/'.$memo.'.pdf';
                        if (Storage::exists($archivo_memo)) {
                            #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                            Storage::delete($archivo_memo);
                        }
    
                        $archivo_memo_to_dta = $request->file('cargar_archivo_formato_t'); # obtenemos el archivo
                        $url_archivo_memo = $this->uploaded_memo_validacion_file($archivo_memo_to_dta, $memo, 'memoValidacion'); #invocamos el método
                    }
                } else {
                    $url_archivo_memo = null;
                }
                # vamos a checar sólo a los checkbox checados como propiedad
                if (!empty($cursoschk)) {
                    // se agregar un arreglo con los meses del año calendario
                    $mesesCalendarizado = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
                    $fecha_ahora = Carbon::now();
                    $fechaActual = Carbon::parse($fecha_ahora);
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $numero_memo = $request->get('numero_memo'); // número de memo
                    $fecha_nueva=$fecha_ahora->format('d-m-Y');
    
                    $anioActual = $fecha_ahora->year; // año actual
    
                    $currentMonth = $mesesCalendarizado[($fechaActual->format('n')) - 1];
                    $fechaEntregaAct = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $currentMonth)->first();
                    $fEAct = $fechaEntregaAct->fecha_entrega."-".$anioActual;
                    /**
                     * convertirlo en un formato fecha
                     */
                    $convertfEAct = date_create_from_format('d-m-Y', $fEAct);
                    $confEAct = date_format($convertfEAct, 'd-m-Y');
                    /**
                     * fecha actual
                     */
                    $fecha_actual = strtotime($fecha_nueva);
                    $fechaEntregaSpring = strtotime($confEAct);
                    /**
                     * se compara la fecha actual en la que se envía el paquete con la fecha establecida
                     * en la entrega del calendario del formato t
                     */
                    // se generar el arreglo que enviara al paquete DTA
                    $memos_DTA = [
                        'NUMERO' => $numero_memo,
                        'FECHA' => $date,
                        'MEMORANDUM' => $url_archivo_memo
                    ];
    
                    if ($fechaEntregaSpring >= $fecha_actual) {
    
                        $actualMonth = $mesesCalendarizado[($fechaActual->format('n')) - 1];
                        $actualSpring = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $actualMonth)->first();
                        $fechActualSpring = $actualSpring->fecha_entrega."-".$anioActual;
                        $fechActSpring = date_create_from_format('d-m-Y', $fechActualSpring);
                        $formatFechaActual = date_format($fechActSpring, 'Y-m-d');
                        # la fecha de entrega debe siempre ser mayor o igual sobre la fecha actual que se envía el paquete.
                        
                        /**
                         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                         */
                        # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                        $data = explode(",", $cursoschk);
                        /**
                         * forzamos un nuevo registro con datos a un arreglo
                         */
                        $pila = [];
                        foreach ($data as $key ) {
                            array_push($pila, $key);
                        }
                        //$comentario_unidad = explode(",", $_POST['comentarios_unidad_to_dta']); // obtenemos los comentarios
                        // dd($_POST['comentarios_unidad_to_dta']);
                        foreach(array_combine($pila, $_POST['comentarios_unidad_to_dta']) as $key => $comentariosUnidad){
                            $comentarios_envio_dta = [
                                'OBSERVACION_UNIDAD' =>  $comentariosUnidad
                            ];
                            \DB::table('tbl_cursos')
                                ->where('id', $key)
                                ->update([
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_UNIDAD_DTA}', '".json_encode($comentarios_envio_dta)."'::jsonb)"),
                                    'memos' => DB::raw("jsonb_set(memos, '{TURNADO_DTA}','".json_encode($memos_DTA)."'::jsonb)"), 
                                    'status' => 'TURNADO_DTA', 
                                    'turnado' => 'DTA',
                                    'fecha_turnado' => $formatFechaActual
                                ]);
                        }
                        
                    } else {
                        # si la condición no se cumple se tiene que tomar el envío con fecha del siguiente spring
                        #obtenemos el mes después
                        $nextMonth = $mesesCalendarizado[($fechaActual->format('n')) + 0];
                        $fechaNextSpring = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $nextMonth)->first();
                        $fechNextSpring = $fechaNextSpring->fecha_entrega."-".$anioActual;
                        $nextSpring = date_create_from_format('d-m-Y', $fechNextSpring);
                        $formatFechaSiguiente = date_format($nextSpring, 'Y-m-d');
    
                        /**
                         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                         */
                        # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                        $data = explode(",", $cursoschk);
                        /**
                         * forzamos un nuevo registro con datos a un arreglo
                         */
                        $pila = [];
                        foreach ($data as $key ) {
                            array_push($pila, $key);
                        }
                        // $comentario_unidad = explode(",", $_POST['comentarios_unidad_to_dta']); // obtenemos los comentarios
                        foreach(array_combine($pila, $_POST['comentarios_unidad_to_dta']) as $key => $comentariosUnidad){
                            $comentarios_envio_dta = [
                                'OBSERVACION_UNIDAD' =>  $comentariosUnidad
                            ];
                            \DB::table('tbl_cursos')
                                ->where('id', $key)
                                ->update([
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_UNIDAD_DTA}', '".json_encode($comentarios_envio_dta)."'::jsonb)"),
                                    'memos' => DB::raw("jsonb_set(memos, '{TURNADO_DTA}','".json_encode($memos_DTA)."'::jsonb)"), 
                                    'status' => 'TURNADO_DTA', 
                                    'turnado' => 'DTA',
                                    'fecha_turnado' => $formatFechaSiguiente,
                                ]);
                        }
                    }
    
                    /**
                     * GENERAMOS UNA REDIRECCIÓN HACIA EL INDEX
                     */
                    return redirect()->route('vista_formatot')
                           ->with('success', sprintf('CURSOS TURNADO A LA UNIDAD PARA VALIDACIÓN A LA DIRECCIÓN TÉCNICA ACÁDEMICA!'));
    
                }
            } else {
                # enviamos un mensaje que no se puede porque no hay registros
                return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
            }
        } else {
            # si el número de memo está vacio por ende se regresa y se le comenta al usuario que se necesita adjuntar ese dato
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE ASIGNO EL NÚMERO DE MEMORANDUM!']);
        }
 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        if (isset($_POST['generarMemoAFirma'])) 
        {
            
            # vamos a checar sólo a los checkbox checados como propiedad
            if (!empty($_POST['chkcursos_list'])) {

                try {
                    /**
                     * unidades
                     */
                    
                    //aqui generamos las consultas pertinentes
                    $fecha_ahora = Carbon::now();
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $numero_memo = $request->get('numero_memo'); // número de memo
                    $fecha_nueva=$fecha_ahora->format('d-m-Y');

                    $memos = [
                        'TURNADO_EN_FIRMA' => [
                            'NUMERO' => $numero_memo,
                            'FECHA' => $date
                        ]
                    ];
                    # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                    foreach($_POST['chkcursos_list'] as $key => $value){
                        $comentarios_envio_firma = [
                            'OBSERVACION_FIRMA' =>  $_POST['comentarios_unidad'][$key]
                        ];
                        \DB::table('tbl_cursos')
                            ->where('id', $value)
                            ->update([
                                'memos' => $memos, 
                                'status' => 'EN_FIRMA', 
                                'turnado' => 'UNIDAD',
                                'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_PARA_FIRMA}', '".json_encode($comentarios_envio_firma)."'::jsonb)")
                            ]);
                    }
                    $total=count($_POST['chkcursos_list']);          
                    $id_user = Auth::user()->id;
                    $rol = DB::table('role_user')->select('roles.slug')->leftjoin('roles', 'roles.id', '=', 'role_user.role_id') 
                    ->where([['role_user.user_id', '=', $id_user], ['roles.slug', '=', 'unidad']])->get();
                    if($rol[0]->slug=='unidad'){ 
                        $unidad = Auth::user()->unidad;
                        $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                        $_SESSION['unidad'] = $unidad;
                    }
                    $mes=date("m");
                    $elaboro = Auth::user()->name;
                    $reg_cursos=DB::table('tbl_cursos')->select(db::raw("sum(case when extract(month from termino) = ".$mes." then 1 else 0 end) as tota"),'unidad','curso','mod','inicio','termino',db::raw("sum(hombre + mujer) as cupo"),'nombre','clave','ciclo',
                                'memos->TURNADO_EN_FIRMA->FECHA as fecha', DB::raw("case when arc='01' then nota else observaciones end as tnota"))
                    ->where(DB::raw("memos->'TURNADO_EN_FIRMA'->>'NUMERO'"), $numero_memo)
                    ->where('status', 'EN_FIRMA')
                    ->groupby('unidad','curso','mod','inicio','termino','nombre','clave','ciclo','memos->TURNADO_EN_FIRMA->FECHA', DB::raw("observaciones_formato_t->'OBSERVACION_PARA_FIRMA'->>'OBSERVACION_FIRMA'"), 'arc', 'nota', 'observaciones')->get();
                    $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                    'pvinculacion','jcyc','pjcyc', 'direccion', 'ubicacion', 'codigo_postal')->where('unidad',$_SESSION['unidad'])->whereNotIn('direccion', ['N/A', 'null'])->first();
                    $pdf = PDF::loadView('reportes.memodta',compact('reg_cursos','reg_unidad','numero_memo','total','fecha_nueva', 'elaboro'));
                    return $pdf->stream('Memo_unidad_para_DTA.pdf');
                    /**
                     * GENERAMOS UNA REDIRECCIÓN HACIA EL INDEX
                     */
                    return redirect()->route('vista_formatot')
                            ->with('success', sprintf('GENERACIÓN DE DOCUMENTO MEMORANDUM PARA ESPERA DE FIRMA!'));
                } catch (QueryException  $th) {
                    //throw $th;
                    return back()->withErrors([$th->getMessage()]);
                }

            } else {
                return back()->withInput()->withErrors(['ERROR AL MOMENTO DE GUARDAR LOS REGISTROS, SE DEBE DE ESTAR SELECCIONADOS LOS CHECKBOX CORRESPONDIENTES']);
            }
        }

            // TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
            // “TURNADO_DTA”:”2021-01-28”
            /**
             *  {TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”], 
             * TURNADO_PLANEACION[“NUMERO”:”XXXXXX”,FECHA:”XXXX-XX-XX”], 
             * TUNADO_UNIDAD:[“NUMERO”:”XXXXXX”,FECHA:”XXXX-XX-XX”]}
             */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendToPlaneacion(Request $request)
    {
        // checamos que se envía el número de memorandum y no esté vacio
        if (!empty($request->get('numeroMemo'))) {
            // checamos si hay cursos si no regresamos con un mensaje de error
            if (!empty($_POST['checkedCursos'])) {
                try {
                    //iniciamos el código verdadero
                    $fecha_ahora = Carbon::now();
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $fecha_nueva=$fecha_ahora->format('d-m-Y');
                    $numero_memo = $request->get('numeroMemo'); // número de memo
                    /**
                    * vamos al cargar el archivo que se sube
                    */
                    if ($request->hasFile('cargar_memorandum_to_planeacion')) {
                        # optener el valor del archivo
                        $validator = Validator::make($request->all(), [
                            'cargar_memorandum_to_planeacion' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # enviamos mensaje de error si la validacion falla
                            return back()->withErrors([$validator]);
                        } else {
                            # de lo contrario proseguimos con nuestra información
                            $memo = str_replace('/', '_', $numero_memo);
                            /**
                            * aquí vamos a verificar que el archivo no se encuentre guardado
                            * previamente en el sistema de archivos del sistema de ser así se 
                            * remplazará el archivo porel que se subirá a continuación
                            */
                            // construcción del archivo
                            $archivo_memo = 'uploadFiles/memoTurnadoPlaneacion/'.$memo.'/'.$memo.'.pdf';
                            if (Storage::exists($archivo_memo)) {
                                #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                                Storage::delete($archivo_memo);
                            }

                            $archivo_memo_send_to_planeacion = $request->file('cargar_memorandum_to_planeacion'); # obtenemos el archivo
                            $url_memo_turnado_planeacion = $this->uploaded_memo_validacion_file($archivo_memo_send_to_planeacion, $memo, 'memoTurnadoPlaneacion'); #invocamos el método
                        }
                        
                    } else {
                        $url_memo_turnado_planeacion = null;
                    }
                    
                    // empezamos a turnar a planeacion
                    $memo_turnado_planeacion = [
                        'PLANEACION' => [
                            'NUMERO' => $numero_memo,
                            'FECHA' => $date,
                            'MEMORANDUM' => $url_memo_turnado_planeacion
                        ]
                    ];

                    $data = explode(",", $_POST['checkedCursos']);
                    // GENERARMOS UN ARREGLO O PILA
                    $pilasendtoplaneacion = [];
                    foreach ($data as $key ) {
                        array_push($pilasendtoplaneacion, $key);
                    }
                    // $comentarioDireccionDTA = explode(",", $_POST['comentarios_direccionDta']);
                    foreach (array_combine($pilasendtoplaneacion, $_POST['comentarios_direccionDta']) as $key => $value) {
                        $comentarios_envio_planeacion = [
                            'OBSERVACION_ENVIO_PLANEACION' => $value
                        ];
                        # entramos en el ciclo para guardar cada registro
                        \DB::table('tbl_cursos')
                            ->where('id', $key)
                            ->update([
                                'memos' => DB::raw("jsonb_set(memos, '{TURNADO_PLANEACION}','".json_encode($memo_turnado_planeacion)."'::jsonb)"), 
                                'status' => 'TURNADO_PLANEACION', 
                                'turnado' => 'PLANEACION',
                                'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_DIRECCIONDTA_TO_PLANEACION}','".json_encode($comentarios_envio_planeacion)."'::jsonb)"),
                            ]);
                    }
                    // enviar  a la página de inicio del módulo si el proceso fue satisfactorio
                    return redirect()->route('validacion.dta.revision.cursos.indice')
                            ->with('success', sprintf('CURSOS TURNADOS A PLANEACIÓN SATISFACTORIAMENTE!'));

                } catch (QueryException $th) {
                    //excepción de consulta
                    return back()->withErrors([$th->getMessage()]);
                }
            } else {
                return back()->withInput()->withErrors(['ERROR AL MOMENTO DE GUARDAR LOS REGISTROS, SE DEBE DE ESTAR SELECCIONADOS LOS CURSOS CORRESPONDIENTES']);
            }
        } else {
            # mensaje de error por no tener el número de memorandum
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, SE NECESITA EL NÚMERO DE MEMORANDUM']);
        }
    }

    protected function uploaded_memo_validacion_file($file, $memo, $subpath)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($memo.".".$extensionFile);
        $path = '/'.$subpath.'/'.$memo.'/'.$documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/'.$subpath.'/'.$memo."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
    
    protected function chkDateToDeliver()
    {
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

    protected function xlxExportReporteTbyUnidad(Request $request){
        $anio_actual = Carbon::now()->year;
        $unidad_ = $request->unidadesFormatoT;
        // cursos unidades por planeacion
        $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, migrante, indigena, etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, migrante, indigena, etnia) as ar");

        $formatot_planeacion_unidad = DB::table('tbl_cursos as c')
        ->select('c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
        'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        DB::raw("SUM(DISTINCT(ins.costo)) as costo"),DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
        DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',

        DB::raw("sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),
        DB::raw("sum( CASE  WHEN  EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO'  THEN 1  ELSE 0 END ) as iem2"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),
        DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END ) as iem3"),
        DB::raw("sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),
        DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END ) as iem4"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        db::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),

        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm5"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),

        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),

        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm2"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh3"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),
        DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),

        DB::raw("case when c.arc='01' then nota else observaciones end as tnota")
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
        ->JOIN($temptblinner ,function($join)
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
        ->WHERE('u.ubicacion', '=', $unidad_)
        ->WHEREIN('c.status', ['NO REPORTADO', 'EN_FIRMA', 'RETORNO_UNIDAD'])
        ->WHERE(DB::raw("extract(year from c.termino)"), '=', $anio_actual)
        ->WHERE('c.clave', '!=', 'NULL')
        ->groupby('c.id', 'ip.grado_profesional', 'ip.estatus', 'i.sexo', 'ei.memorandum_validacion')
        ->distinct()->get();



        // $formatot_planeacion_unidad =
        // DB::table('tbl_cursos as c')
        // ->select('c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        // DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
        // 'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        // DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        // 'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        // DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
        // DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        // DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        // 'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        // DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        // DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MUJER' then 1 else 0 end) as iesm5"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as naesm2"),DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as naesh3"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
        // DB::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),
        // DB::raw("case when arc='01' then nota else observaciones end as tnota"),
        // DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD'->>'OBSERVACION_RETORNO' AS observaciones_enlaces")
        // )
        // ->JOIN('tbl_calificaciones as ca','c.id', '=', 'ca.idcurso')
        // ->JOIN('instructores as i','c.id_instructor', '=', 'i.id')
        // ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        // ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        // ->JOIN('especialidades as e', function($join)
        // {
        //     $join->on('ei.especialidad_id', '=', 'e.id');                
        //     $join->on('c.espe', '=', 'e.nombre');
        // })
        // ->JOIN('alumnos_registro as ar',function($join)
        // {
        //     $join->on('ca.matricula', '=', 'ar.no_control');                
        //     $join->on('c.id_curso','=','ar.id_curso');
        // }) 
        // ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        // ->JOIN('tbl_inscripcion as ins', function($join)
        // {
        //     $join->on('ca.idcurso', '=', 'ins.id_curso');                
        //     $join->on('ca.matricula','=','ins.matricula');
        // })
        // ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        // ->WHERE('u.ubicacion', '=', $unidad_)
        // ->WHEREIN('c.status', ['NO REPORTADO', 'EN_FIRMA', 'RETORNO_UNIDAD'])
        // ->WHERE(DB::raw("extract(year from c.termino)"), '=', $anio_actual)
        // ->groupby('c.unidad','c.nombre','c.clave','c.mod','c.espe','c.curso','c.inicio','c.termino','c.dia','c.dura','c.hini','c.hfin','c.horas','c.plantel','c.programa','c.muni','c.depen','c.cgeneral','c.mvalida','c.efisico','c.cespecifico','c.sector','c.mpaqueteria','c.mexoneracion','c.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','c.observaciones'
        //             ,'ins.abrinscri','c.arc', 'c.id')
        // ->distinct()->get();


        $head = ['UNIDAD DE CAPACITACION','TIPO DE PLANTEL (UNIDAD, AULA MOVIL, ACCION MOVIL O CAPACITACION EXTERNA)','ESPECIALIDAD','CURSO','CLAVE DEL GRUPO','MODALIDAD','DURACION TOTAL EN HORAS','TURNO','DIA INICIO','MES INICIO','DIA TERMINO','MES TERMINO', 'PERIODO', 'HRS. DIARIAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MASC',
        'EGRESADOS', 'EGRESADOS FEMENINO', 'EGRESADO MASCULINO', 'DESERCION', 'COSTO TOTAL DEL CURSO POR PERSONA', 'INGRESO TOTAL', 'EXONERACION TOTAL MUJERES', 'EXONERACION TOTAL HOMBRES', 'EXONERACION PARCIAL MUJERES', 'EXONERACION PARCIAL HOMBRES', 'NUMERO DE CONVENIO ESPECIFICO', 'MEMO DE VALIDACION DEL CURSO', 'ESPACIO FISICO',
        'NOMBRE DEL INSTRUCTOR', 'ESCOLARIDAD DEL INSTRUCTOR', 'DOCUMENTO ADQUIRIDO', 'SEXO', 'MEMO DE VALIDACION', 'MEMO DE AUTORIZACION DE EXONERACION', 'EMPLEADOS', 'DESEMPLEADOS', 'DISCAPACITADOS', 'MIGRANTES',
        'INDIGENA', 'ETNIA', 'PROGRAMA ESTRATEGICO', 'MUNICIPIO', 'DEPENDENCIA BENEFICIADA', 'CONVENIO GENERAL', 'CONVENIO CON EL SECTOR PUBLICO O PRIVADO', 'MEMO DE VALIDACION DE PAQUETERIA', 
        'INSCRITOS EDAD-1 MUJERES', 'INSCRITOS EDAD-1 HOMBRES', 
        'INSCRITOS EDAD-2 MUJERES', 'INSCRITOS EDAD-2 HOMBRES', 
        'INSCRITOS EDAD-3 MUJERES', 'INSCRITOS EDAD-3 HOMBRES', 
        'INSCRITOS EDAD-4 MUJERES', 'INSCRITOS EDAD-4 HOMBRES', 
        'INSCRITOS EDAD-5 MUJERES', 'INSCRITOS EDAD-5 HOMBRES', 
        'INSCRITOS EDAD-6 MUJERES', 'INSCRITOS EDAD-6 HOMBRES', 
        'INSCRITOS EDAD-7 MUJERES', 'INSCRITOS EDAD-7 HOMBRES',
        'INSCRITOS EDAD-8 MUJERES', 'INSCRITOS EDAD-8 HOMBRES', 
        'INSCRITOS ESC-1 MUJERES', 'INSCRITOS ESC-1 HOMBRES', 
        'INSCRITOS ESC-2 MUJERES', 'INSCRITOS ESC-2 HOMBRES', 
        'INSCRITOS ESC-3 MUJERES', 'INSCRITOS ESC-3 HOMBRES', 
        'INSCRITOS ESC-4 MUJERES', 'INSCRITOS ESC-4 HOMBRES',
        'INSCRITOS ESC-5 MUJERES', 'INSCRITOS ESC-5 HOMBRES',
        'INSCRITOS ESC-6 MUJERES', 'INSCRITOS ESC-6 HOMBRES',
        'INSCRITOS ESC-7 MUJERES', 'INSCRITOS ESC-7 HOMBRES',
        'INSCRITOS ESC-8 MUJERES', 'INSCRITOS ESC-8 HOMBRES',
        'INSCRITOS ESC-9 MUJERES', 'INSCRITOS ESC-9 HOMBRES', 
        'ACREDITADOS ESC-1 MUJERES', 'ACREDITADOS ESC-1 HOMBRES', 
        'ACREDITADOS ESC-2 MUJERES', 'ACREDITADOS ESC-2 HOMBRES',
        'ACREDITADOS ESC-3 MUJERES', 'ACREDITADOS ESC-3 HOMBRES', 
        'ACREDITADOS ESC-4 MUJERES', 'ACREDITADOS ESC-4 HOMBRES', 
        'ACREDITADOS ESC-5 MUJERES', 'ACREDITADOS ESC-5 HOMBRES', 
        'ACREDITADOS ESC-6 MUJERES', 'ACREDITADOS ESC-6 HOMBRES', 
        'ACREDITADOS ESC-7 MUJERES', 'ACREDITADOS ESC-7 HOMBRES', 
        'ACREDITADOS ESC-8 MUJERES', 'ACREDITADOS ESC-8 HOMBRES', 
        'ACREDITADOS ESC-9 MUJERES', 'ACREDITADOS ESC-9 HOMBRES', 
        'DESERTORES ESC-1 MUJERES', 'DESERTORES ESC-1 HOMBRES', 
        'DESERTORES ESC-2 MUJERES', 'DESERTORES ESC-2 HOMBRES',
        'DESERTORES ESC-3 MUJERES', 'DESERTORES ESC-3 HOMBRES', 
        'DESERTORES ESC-4 MUJERES', 'DESERTORES ESC-4 HOMBRES', 
        'DESERTORES ESC-5 MUJERES', 'DESERTORES ESC-5 HOMBRES', 
        'DESERTORES ESC-6 MUJERES', 'DESERTORES ESC-6 HOMBRES', 
        'DESERTORES ESC-7 MUJERES', 'DESERTORES ESC-7 HOMBRES', 
        'DESERTORES ESC-8 MUJERES', 'DESERTORES ESC-8 HOMBRES', 
        'DESERTORES ESC-9 MUJERES', 'DESERTORES ESC-9 HOMBRES', 
        'OBSERVACIONES'];

        $nombreLayout = "FORMATO_T_PARA_LA_UNIDAD_".$unidad_.".xlsx";
        $titulo = "FORMATO T DE LA UNIDAD ".$unidad_;


        if(count($formatot_planeacion_unidad)>0){  
            return Excel::download(new FormatoTReport($formatot_planeacion_unidad,$head, $titulo), $nombreLayout);
        }
    }

}