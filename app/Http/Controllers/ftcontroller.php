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

class ftcontroller extends Controller
{
    public function index(Request $request)
    {
        // $file = 'http://localhost:8000/storage/uploadFiles/memoValidacion/1268326/1268326.pdf';
        // $comprobanteCalidadMigratoria = explode("/",$file, 5);
        // dd($comprobanteCalidadMigratoria[4]);
        $anio=$request->get("anio");
        $id_user = Auth::user()->id;
        //var_dump($id_user);exit;
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
                $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                $_SESSION['unidad'] = $unidad;
            }

            $anios = $anio;
            $var_cursos = DB::table('tbl_cursos as c')
                ->select('c.id AS id_tbl_cursos', 'c.status AS estadocurso' ,'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
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
                         ,'ins.abrinscri','c.arc', 'c.id')
                ->distinct()->get();

        } else {
            # si se encuentra vacio
            $var_cursos = null;
        }
            //var_dump($_SESSION['unidades']);exit;

        return view('reportes.vista_formatot',compact('var_cursos', 'meses', 'enFirma', 'retornoUnidad'));    
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
        if($rol=='unidad')
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

        } else {
            # si se encuentra vacio
            $var_cursos = null;
        }
            //var_dump($_SESSION['unidades']);exit;

        return view('reportes.vista_formatot',compact('var_cursos', 'meses', 'enFirma', 'retornoUnidad'));
                
    }

    public function paso2(Request $request)
    {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('check_cursos_dta');
        /**
         * vamos al cargar el archivo que se sube
         */
        if ($request->hasFile('cargar_archivo_formato_t')) {
            // obtenemos el valor del archivo memo

            $validator = Validator::make($request->all(), [
                'cargar_archivo_formato_t' => 'mimes:pdf|max:2048'
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
                $archivo_memo = 'uploadFiles/memoValidacion/'.$memo.'/'.$memo.'.pdf';
                if (Storage::exists($archivo_memo)) {
                    #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                    Storage::delete($archivo_memo);
                }

                $archivo_memo_to_dta = $request->file('cargar_archivo_formato_t'); # obtenemos el archivo
                $url_archivo_memo = $this->uploaded_memo_validacion_file($archivo_memo_to_dta, $memo); #invocamos el método
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

                $memos_DTA = [
                    'FECHA' => $date,
                    'MEMORANDUM' => $url_archivo_memo
                ];
                /**
                 * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                 */
                # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                $data = explode(",", $cursoschk);
                $comentario_unidad = explode(",", $_POST['comentarios_unidad']); // obtenemos los comentarios
                foreach(array_combine($data, $comentario_unidad) as $key => $comentariosUnidad){
                    $comentarios_envio_dta = [
                        'OBSERVACION_UNIDAD' =>  $comentariosUnidad
                    ];
                    \DB::table('tbl_cursos')
                        ->where('id', $key)
                        ->update(['memos' => DB::raw("jsonb_set(memos, '{TURNADO_DTA}','".json_encode($memos_DTA)."'::jsonb)"), 
                        'status' => 'TURNADO_DTA', 
                        'turnado' => 'DTA',
                        'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_UNIDAD_DTA}', '".json_encode($comentarios_envio_dta)."'::jsonb)")]);
                }

                /**
                 * GENERAMOS UNA REDIRECCIÓN HACIA EL INDEX
                 */
                // return redirect()->route('formatot.send.dta')
                //         ->with('success', sprintf('GENERACIÓN DE DOCUMENTO MEMORANDUM PARA ESPERA DE FIRMA!'));

            }
        }

        $json = json_encode(1);
        return $json;
        /**
         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”],
         */

        // $id_user = Auth::user()->id;
        // $rol = DB::table('role_user')
        // ->select('roles.slug')
        // ->leftjoin('roles', 'roles.id', '=', 'role_user.role_id')            
        // ->where([['role_user.user_id', '=', $id_user], ['roles.slug', '=', 'unidad']])
        // ->first();
        // if (!empty($rol->slug)) {
        //     #si no está vacio
        //     if($rol->slug=='unidad')
        //     { 
        //         $unidad_ = Auth::user()->unidad;
        //         $unidad = DB::table('tbl_unidades')->where('id',$unidad_)->value('unidad');
        //     }
        //     $now = Carbon::now();
        //     $anios = $now->year;
        //     $varcursospaso2 = DB::table('tbl_cursos as c')
        //         ->select('c.id AS id_tbl_cursos', 'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
        //         DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
        //         'c.horas','c.dia',DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),DB::raw('count(distinct(ca.id)) as tinscritos'),DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
        //         DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
        //         'ins.costo',DB::raw("SUM(ins.costo) as ctotal"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
        //         DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
        //         DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
        //         DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
        //         'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
        //         DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < '15' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='FEMENINO' then 1 else 0 end) as iem2"),
        //         DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '15' and '19' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='FEMENINO' then 1 else 0 end) as iem3"),
        //         DB::raw("sum(Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='FEMENINO' then 1 else 0 end) as iem4"),
        //         db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '25' and '34' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
        //         DB::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '35' and '44' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
        //         db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '45' and '54' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
        //         db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
        //         db::raw("sum(case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento)))>= '65' and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MUJER' then 1 else 0 end) as iesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as naesm2"),db::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as naesh3"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),db::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),db::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
        //         db::raw("sum(case when ap.ultimo_grado_estudios='POSTRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),db::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),
        //         DB::raw("case when arc='01' then nota else observaciones end as tnota")
        //         )
        //         ->JOIN('tbl_calificaciones as ca','c.id', '=', 'ca.idcurso')
        //         ->JOIN('instructores as i','c.id_instructor', '=', 'i.id')
        //         ->JOIN('instructor_perfil as ip','i.id', '=', 'ip.numero_control')
        //         ->JOIN('especialidad_instructores as ei','ip.id', '=', 'ei.perfilprof_id')                
        //         ->JOIN('especialidades as e', function($join)
        //             {
        //                 $join->on('ei.especialidad_id', '=', 'e.id');                
        //                 $join->on('c.espe', '=', 'e.nombre');
        //             })
        //         ->JOIN('alumnos_registro as ar',function($join)
        //         {
        //             $join->on('ca.matricula', '=', 'ar.no_control');                
        //             $join->on('c.id_curso','=','ar.id_curso');
        //         }) 
        //         ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        //         ->JOIN('tbl_inscripcion as ins', function($join)
        //         {
        //             $join->on('ca.idcurso', '=', 'ins.id_curso');                
        //             $join->on('ca.matricula','=','ins.matricula');
        //         })
        //         ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        //         ->WHERE('u.ubicacion', '=', $unidad)
        //         ->WHERE('c.status', '=', 'NO REPORTADO')                
        //         ->WHERE(DB::raw("extract(year from c.termino)"), '=', $anios)
        //         ->WHERE(DB::raw("date_part('year', date (c.memos->'TURNADO_DTA'->>'FECHA') )"), '=', $anios)
        //         ->WHERE('c.turnado', '=', 'ENFIRMA')
        //         ->groupby('c.unidad','c.nombre','c.clave','c.mod','c.espe','c.curso','c.inicio','c.termino','c.dia','c.dura','c.hini','c.hfin','c.horas','c.plantel','c.programa','c.muni','c.depen','c.cgeneral','c.mvalida','c.efisico','c.cespecifico','c.sector','c.mpaqueteria','c.mexoneracion','c.nota','i.sexo','ei.memorandum_validacion','ip.grado_profesional','ip.estatus','ins.costo','c.observaciones'
        //                  ,'ins.abrinscri','c.arc', 'c.id')
        //         ->distinct()->get();

        //         return view('reportes.vista_formatot_paso2',compact('varcursospaso2')); 
        // }
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
                    //aqui generamos las consultas pertinentes
                    $fecha_ahora = Carbon::now();
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $fecha_nueva=$fecha_ahora->format('d-m-Y');
                    $numero_memo = $request->get('numero_memo'); // número de memo

                    $memos = [
                        'TURNADO_EN_FIRMA' => [
                            'NUMERO' => $numero_memo,
                            'FECHA' => $date
                        ]
                    ];
                    # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                    foreach($_POST['chkcursos_list'] as $key => $value){
                        \DB::table('tbl_cursos')
                            ->where('id', $value)
                            ->update(['memos' => $memos, 'status' => 'EN_FIRMA', 'turnado' => 'UNIDAD']);
                    }
                    $total=count($_POST['chkcursos_list']);                
                    $id_user = Auth::user()->id;
                    $rol = DB::table('role_user')->select('roles.slug')->leftjoin('roles', 'roles.id', '=', 'role_user.role_id') 
                    ->where([['role_user.user_id', '=', $id_user], ['roles.slug', '=', 'unidad']])->get();
                    if($rol[0]->slug=='unidad')
                    { 
                    $unidad = Auth::user()->unidad;
                    $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                    $_SESSION['unidad'] = $unidad;
                    }
                    $mes=date("m");
                    $reg_cursos=DB::table('tbl_cursos')->select(db::raw("sum(case when extract(month from termino) = ".$mes." then 1 else 0 end) as tota"),'unidad','curso','mod','inicio','termino',db::raw("sum(hombre + mujer) as cupo"),'nombre','clave','ciclo',
                                'memos->TURNADO_EN_FIRMA->FECHA as fecha')
                    ->where('num_memo_devolucion',$request->memo)
                    ->groupby('unidad','curso','mod','inicio','termino','nombre','clave','ciclo','memos->TURNADO_EN_FIRMA->FECHA')->get();
                    $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                    'pvinculacion','jcyc','pjcyc')->where('unidad',$_SESSION['unidad'])->first();
                    $pdf = PDF::loadView('reportes.memodta',compact('reg_cursos','reg_unidad','numero_memo','total','fecha_nueva'));
                    return $pdf->stream('Memo_DTA.pdf');
                    /**
                     * GENERAMOS UNA REDIRECCIÓN HACIA EL INDEX
                     */
                    return redirect()->route('vista_formatot')
                            ->with('success', sprintf('GENERACIÓN DE DOCUMENTO MEMORANDUM PARA ESPERA DE FIRMA!'));
                } catch (QueryException  $th) {
                    //throw $th;
                    return back()->withErrors([$ex->getMessage()]);
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
    public function memodta()
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($memo.".".$extensionFile);
        $path = '/memoValidacion/'.$memo.'/'.$documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/memoValidacion/'.$memo."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
    
}