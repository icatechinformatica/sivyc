<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Excel\xlsReportes;
use Maatwebsite\Excel\Facades\Excel;

use PDF;
class cursosController extends Controller
{
    function __construct() {
        session_start();
        $this->discapacidad = ["VISUAL" => "1", "AUDITIVA" => "2", "DE COMUNICACIÓN" => "3", "MOTRIZ" => "4", "INTELECTUAL" => "5", "NINGUNA" => "6"];
        $this->escolaridad = ["PRIMARIA INCONCLUSA"=>"1","PRIMARIA TERMINADA"=>"2","SECUNDARIA INCONCLUSA"=>"3","SECUNDARIA TERMINADA"=>"4",
        "NIVEL MEDIO SUPERIOR INCONCLUSO"=>"5","NIVEL MEDIO SUPERIOR TERMINADO"=>"6","NIVEL SUPERIOR INCONCLUSO"=>"7","NIVEL SUPERIOR TERMINADO"=>"8","POSTGRADO"=>"9"];
        $this->periodo = ["7"=>"1","8"=>"1","9"=>"1","10"=>"2","11"=>"2","12"=>"2","1"=>"3","2"=>"3","3"=>"3","4"=>"4","5"=>"4","6"=>"4"];
        $this->mes = ["1"=>"ENERO","2"=>"FEBRERO","3"=>"MARZO","4"=>"ABRIL","5"=>"MAYO","6"=>"JUNIO","7"=>"JULIO","8"=>"AGOSTO","9"=>"SEPTIEMBRE","10"=>"OCTUBRE","11"=>"NOVIEMBRE","12"=>"DICIEMBRE"];
    }

    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');
        $_SESSION['unidades']=NULL;
        //var_dump($rol);exit;
        if($rol){
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');
            if(count($unidades)==0) $unidades =[$unidad];
            $_SESSION['unidades'] = $unidades;
        }
        //var_dump($_SESSION['unidades']);exit;
        return view('reportes.cursos.index');
    }

    public function asistencia(Request $request){ //PRUEBA 2B-20-OPAU-CAE-0022
        $clave = $request->get('clave');

        $file = "LISTA_ASISTENCIA_$clave.PDF";
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),'inicio','termino',DB::raw("CONCAT ('C. ',nombre) as nombre"),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            'u.plantel',DB::raw('EXTRACT(MONTH FROM inicio)  as mes_inicio'),DB::raw('EXTRACT(YEAR FROM inicio)  as anio_inicio'),
            DB::raw("CONCAT ('C. ',trim(substring(u.academico , position('.' in u.academico)+1,char_length(u.academico)))) as academico"),
            'u.pacademico' )
            ->where('clave',$clave);
            if($_SESSION['unidades'])$curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')
            ->first();
            //var_dump($curso);exit;
            if($curso){
                $consec_curso = $curso->id_curso;
                $fecha_termino = $curso->inicio;
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno')
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->groupby('i.matricula','i.alumno')->orderby('i.alumno')->get();
               //var_dump($alumnos); exit;
                if(!$alumnos) return "NO HAY ALUMNOS INSCRITOS";
                $mes = $this->mes;
                $consec = 1;
                if($curso->inicio AND $curso->termino)
                    $Ym = $this->generateDates($curso->inicio, $curso->termino);
                else  return "El Curso no tiene registrado la fecha de inicio y de termino";
                //$Ym = NULL;
//var_dump($Ym);exit;
                $pdf = PDF::loadView('reportes.cursos.pdf-asistencia',compact('curso','alumnos','mes','consec','Ym'));
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream($file);
            } else return "Curso no v&aacute;lido para esta Unidad";
        }
        return "Clave no v&aacute;lida";
    }

    function generateDates(string $since, string $until = null) {
        $Ym = [];
        if (! $until) $until = date('Y-m-d');
        $since = strtotime(date('Y-m', strtotime($since)));
        $until = strtotime(date('Y-m', strtotime($until)));
        do {
            $anio = date('Y', $since);
            $mes = date('m', $since)*1;
            $Ym[]= $anio."-".$mes;
            $since = strtotime("+ 1 month", $since);
        } while($since <= $until);
        return $Ym;
    }

    public function calificaciones(Request $request){
        $clave = $request->get('clave');
        $file = "CALIFICACIONES_$clave.PDF";
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),DB::raw("CONCAT ('C. ',nombre) as nombre"),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),'u.plantel',
            DB::raw("CONCAT ('C. ',trim(substring(u.academico , position('.' in u.academico)+1,char_length(u.academico)))) as academico"),
            'u.pacademico')
            ->where('clave',$clave);
            if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')
            ->first();
           // var_dump($curso);exit;
            if($curso){
                $consec_curso = $curso->id_curso;
                $fecha_termino = $curso->inicio;
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno','i.calificacion' )
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->groupby('i.matricula','i.alumno','i.calificacion')->orderby('i.alumno')->get();
               //var_dump($alumnos); exit;
                if(count($alumnos)==0){ return "NO HAY ALUMNOS INSCRITOS";exit;}
                $consec = 1;

                $pdf = PDF::loadView('reportes.cursos.pdf-calificaciones',compact('curso','alumnos','consec'));
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream($file);
            } else return "Curso no v&aacute;lido para esta Unidad";
        }
        return "Clave no v&aacute;lida";
    }

    public function riacIns(Request $request){
        $clave = $request->get('clave');

        $file = "RIAC_INSCRIPCION_$clave.PDF";
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'u.pdunidad',
            DB::raw("trim(substring(u.dgeneral , position('.' in u.dgeneral)+1,char_length(u.dgeneral))) as dgeneral"),'u.pdgeneral',
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino'),'u.plantel',
            'u.academico','u.pacademico')
            ->where('clave',$clave);
            if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')
            ->first();
           // var_dump($curso);exit;
            if($curso){
                $consec_curso = $curso->id_curso;
                $fecha_inicio = $curso->inicio;
                $alumnos = DB::table('tbl_inscripcion as i')->select(
                    'i.matricula','i.alumno','i.sexo','i.escolaridad','i.discapacidad','i.abrinscri', 'i.id_gvulnerable',
                        DB::raw("EXTRACT(year from (age('".$fecha_inicio."',i.fecha_nacimiento))) as edad")
                )->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')->orderby('i.alumno')->get();
                if(count($alumnos)==0){ return "NO ENCONTR&Oacute; REGISTROS DE ALUMNOS.";exit;}
                $discapacidad = $this->discapacidad;
                $escolaridad = $this->escolaridad;
                $periodo = $this->periodo;
                $consec = 1;

                $pdf = PDF::loadView('reportes.cursos.pdf-riac-ins',compact('curso','alumnos','discapacidad','escolaridad','periodo','consec'));
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream($file);
            } else return "Curso no v&aacute;lido para esta Unidad";
        }
        return "Clave no v&aacute;lida";
    }

    public function riacAcred(Request $request){
        $clave = $request->get('clave');
        $file = "RIAC_ACREDITACION_$clave.PDF";
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'u.pdunidad',
            DB::raw("trim(substring(u.dgeneral , position('.' in u.dgeneral)+1,char_length(u.dgeneral))) as dgeneral"),'u.pdgeneral',
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino'),'u.plantel',
            'u.academico','u.pacademico')
            ->where('clave',$clave);
            if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')
            ->first();

            if($curso){
                $consec_curso = $curso->id_curso;
                $fecha_inicio = $curso->inicio;
                $alumnos = DB::table('tbl_inscripcion as i')
                ->select(
                    'i.matricula','i.alumno','i.sexo','i.escolaridad','i.discapacidad','i.abrinscri', 'i.id_gvulnerable',
                        DB::raw("EXTRACT(year from (age('".$fecha_inicio."',i.fecha_nacimiento))) as edad"),
                        DB::raw("(CASE WHEN i.calificacion!='NP' THEN 'X' END) as acreditado")
                )->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')->orderby('i.alumno')->get();

               //var_dump($alumnos); exit;
                if(count($alumnos)==0){ return "NO ENCONTR&Oacute; REGISTROS DE ALUMNOS.";exit;}
                $discapacidad = $this->discapacidad;
                $escolaridad = $this->escolaridad;
                $periodo = $this->periodo;
                $consec = 1;

                $pdf = PDF::loadView('reportes.cursos.pdf-riac-acred',compact('curso','alumnos','discapacidad','escolaridad','periodo','consec'));
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream($file);
            }else return "Curso no v&aacute;lido para esta Unidad";
        }
        return "Clave no v&aacute;lida";
    }


    public function riacCert(Request $request){

        $clave = $request->get('clave');
        $file = "RIAC_CERTIFICACION_$clave.PDF";
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'u.pdunidad',
            DB::raw("trim(substring(u.dgeneral , position('.' in u.dgeneral)+1,char_length(u.dgeneral))) as dgeneral"),'u.pdgeneral',
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino'),'u.plantel',
            'u.academico','u.pacademico')
            ->where('clave',$clave);
            if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')
            ->first();
            //var_dump($curso);exit;
            //echo $curso->id; exit;
            if($curso){
                $consec_curso = $curso->id_curso;
                $fecha_inicio = $curso->inicio;
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select(
                        'i.matricula','i.alumno','i.sexo','i.escolaridad','i.discapacidad','i.abrinscri', 'i.id_gvulnerable',
                            DB::raw("EXTRACT(year from (age('".$fecha_inicio."',i.fecha_nacimiento))) as edad"),
                            DB::raw("(CASE WHEN i.calificacion!='NP' THEN 'X' END) as acreditado"),'f.folio',DB::raw("to_char(f.fecha_expedicion, 'DD/MM/YYYY') as fecha_expedicion")
                    )->leftjoin('tbl_folios as f','f.id','=','i.id_folio')
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')->orderby('i.alumno')->get();

                if(count($alumnos)==0){ return "NO ENCONTR&Oacute; REGISTROS DE ALUMNOS O NO TIENEN FOLIOS ASIGNADOS";exit;}

                $discapacidad = $this->discapacidad;
                $escolaridad = $this->escolaridad;
                $periodo = $this->periodo;
                $consec = 1;

                $pdf = PDF::loadView('reportes.cursos.pdf-riac-cert',compact('curso','alumnos','discapacidad','escolaridad','periodo','consec'));
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream($file);
            }else return "Curso no v&aacute;lido para esta Unidad";
        }
        return "Clave no v&aacute;lida";
    }

    public function xlsConst(Request $request){

        $clave = $request->get('clave');

        if($clave){
            $curso = DB::table('tbl_cursos as tc')->select('tc.id','tc.curso','tc.dura','tc.cct','tc.unidad','tc.depen', 'tc.mod',
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'tc.id_curso',
            DB::raw('EXTRACT(DAY FROM termino)  as dia_termino'),
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino'),
            DB::raw('EXTRACT(YEAR FROM termino)  as anio_termino'),
            'c.duracion as horas_certificacion','tc.tipo_curso as servicio','tc.nplantel')
            ->where('tc.clave',$clave);
            if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tc.unidad')
            ->leftjoin('cursos as c','c.id','tc.id_curso')
            ->first();

            //var_dump($curso);exit;
            //echo $curso->id; exit;
            if($curso){
                $consec_curso = $curso->id_curso;
                /*if($curso->servicio == "CERTIFICACION") $duracion = $curso->horas_certificacion;
                else */
                //$duracion = $curso->dura;
                if ($curso->servicio=='CERTIFICACION') {
                    $duracion = '';
                }else{
                    $duracion = $curso->dura;
                }
                $data = DB::table('tbl_inscripcion as i')
                    ->select('f.folio','i.alumno','i.curp',
                        DB::raw("E'".addslashes($curso->curso)."' as nombre_curso"),
                        DB::raw("to_char(f.fecha_expedicion, 'DD/MM/YYYY') as fecha"),
                        DB::raw("LPAD('".$curso->dia_termino."',2,'0') as dia"),
                        DB::raw("'".$this->mes[$curso->mes_termino]."' as mes"),
                        DB::raw("'".$curso->anio_termino."' as anio"),
                        DB::raw("'".$duracion."' as horas"),
                        DB::raw("'".$curso->cct."' as cct"),
                        DB::raw("'".$curso->nplantel." ".$curso->unidad."' as planel"),
                        DB::raw("'".$curso->unidad."' as unidad"),
                        DB::raw("'CHIAPAS' as estado"),
                        DB::raw("'C. ".$curso->dunidad."' as dunidad"),
                        DB::raw("REPLACE('".$curso->depen."','.','') as depen"),
                        )
                        ->when($curso->mod == 'CAE', function ($query) {
                            return $query->addSelect(DB::raw("'ICV-00-07-27-K41-0013' as stps"));
                        })
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->where('i.calificacion','<>','NP')

                    ->Join('tbl_folios as f', function($join){
                        $join->on('f.id', '=', 'i.id_folio');
                    })
                    ->orderby('i.alumno')->get();
                //var_dump($data); exit;
                if(count($data)==0){ return "NO TIENEN FOLIOS ASIGNADOS";exit;}

                $head = ['FOLIO','ALUMNO','CURP','CURSO','FECHA','DIA','MES','AÑO','HORAS','CLAVE UNIDAD','UNIDAD DE CAPACITACION','CIUDAD','ESTADO','DIRECTOR','DEPENDENCIA'];
                if ($curso->mod == 'CAE') {$head[] = 'STPS';} //Agregamos una columna mas al excel
                $nombreLayout = $clave.".xlsx";

                if(count($data)>0){
                    return Excel::download(new xlsReportes($data,$head), $nombreLayout);
                }

            }else return "Curso no v&aacute;lido para esta Unidad";
        }
    }

}
