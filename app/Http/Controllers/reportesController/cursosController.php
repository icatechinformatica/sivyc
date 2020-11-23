<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\Tbl_curso;

use PDF;
class cursosController extends Controller
{   
    function __construct() {
        $this->discapacidad = ["AUDITIVA"=>"1","DEL HABLA"=>"2","INTELECTUAL"=>"3", "MOTRIZ"=>"4", "VISUAL"=>"5","NINGUNA"=>"6"];
        $this->escolaridad = ["PRIMARIA INCONCLUSA"=>"1","PRIMARIA TERMINADA"=>"2","SECUNDARIA INCONCLUSA"=>"3","SECUNDARIA TERMINADA"=>"4",
        "NIVEL MEDIO SUPERIOR INCONCLUSO"=>"5","NIVEL MEDIO SUPERIOR TERMINADO"=>"6","NIVEL SUPERIOR INCONCLUSO"=>"7","NIVEL SUPERIOR TERMINADO"=>"8","POSTGRADO"=>"9"];
        $this->periodo = ["7"=>"1","8"=>"1","9"=>"1","10"=>"2","11"=>"2","12"=>"2","1"=>"3","2"=>"3","3"=>"3","4"=>"4","5"=>"4","6"=>"4"];
    }
    public function index(Request $request){    
        return view('reportes.cursos.index');
    }
    
    public function riacIns(Request $request){ //PRUEBA 2B-20-OPAU-CAE-0022
        $clave = $request->get('clave');
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'u.pdunidad',
            DB::raw("trim(substring(u.dgeneral , position('.' in u.dgeneral)+1,char_length(u.dgeneral))) as dgeneral"),'u.pdgeneral',
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino') )
            ->where('clave',$clave)
            ->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')            
            ->first(); 
           // var_dump($curso);exit;
            if($curso){
                $consec_curso = $curso->id_curso; 
                $fecha_termino = $curso->termino;
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno','a_pre.sexo','a_pre.ultimo_grado_estudios','a_pre.discapacidad',
                        'i.abrinscri',DB::raw("to_char(i.created_at, 'YYYY-MM-DD') as fecha_creacion"),
                        DB::raw("EXTRACT(year from (age('".$fecha_termino."',a_pre.fecha_nacimiento))) as edad"),'a_pre.fecha_nacimiento' )
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')                                      
                    ->Join('alumnos_registro as a_reg', function($join)use($consec_curso){
                        //$join->on('a_r.id_curso', '=', $consec_curso);                
                        $join->on('a_reg.no_control', '=', 'i.matricula');                    
                    }) 
                    ->Join('alumnos_pre as a_pre', function($join)use($consec_curso){
                        $join->on('a_pre.id', '=', 'a_reg.id_pre');
                    });                
                $alumnos = $alumnos->groupby('i.matricula','i.alumno','i.created_at',
                    'a_reg.id_pre','a_pre.fecha_nacimiento','a_pre.sexo','a_pre.ultimo_grado_estudios',
                    'a_pre.discapacidad','i.abrinscri')->orderby('i.alumno')->get();
               //var_dump($alumnos); exit;       
                if(!$alumnos) return "NO HAY ALUMNOS INSCRITOS";
                $discapacidad = $this->discapacidad;  
                $escolaridad = $this->escolaridad;
                $periodo = $this->periodo;
                $consec = 1;               
                
                $pdf = PDF::loadView('reportes.cursos.pdf-riac-ins',compact('curso','alumnos','discapacidad','escolaridad','periodo','consec'));        
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream("RIAC-ACREDITACION");
            }
        }
        return "Clave no v&aacute;lida";
    } 
    
    public function riacAcred(Request $request){
        $clave = $request->get('clave');
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'u.pdunidad',
            DB::raw("trim(substring(u.dgeneral , position('.' in u.dgeneral)+1,char_length(u.dgeneral))) as dgeneral"),'u.pdgeneral',
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino'))
            ->where('clave',$clave)
            ->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')            
            ->first(); 
            
            if($curso){
                $consec_curso = $curso->id_curso; 
                $fecha_termino = $curso->termino;
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno','cal.acreditado','a_pre.sexo','a_pre.ultimo_grado_estudios','a_pre.discapacidad',
                        'i.abrinscri',DB::raw("to_char(i.created_at, 'YYYY-MM-DD') as fecha_creacion"),
                        DB::raw("EXTRACT(year from (age('".$fecha_termino."',a_pre.fecha_nacimiento))) as edad"),'a_pre.fecha_nacimiento' )
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->Join('tbl_calificaciones as cal', function($join){
                        $join->on('cal.idcurso', '=', 'i.id_curso');                
                        $join->on('cal.matricula', '=', 'i.matricula');                
                    })                    
                    ->Join('alumnos_registro as a_reg', function($join)use($consec_curso){
                        //$join->on('a_r.id_curso', '=', $consec_curso);                
                        $join->on('a_reg.no_control', '=', 'i.matricula');                    
                    }) 
                    ->Join('alumnos_pre as a_pre', function($join)use($consec_curso){
                        $join->on('a_pre.id', '=', 'a_reg.id_pre');
                    });                
                $alumnos = $alumnos->groupby('i.matricula','i.alumno','i.created_at','cal.acreditado',
                    'a_reg.id_pre','a_pre.fecha_nacimiento','a_pre.sexo','a_pre.ultimo_grado_estudios',
                    'a_pre.discapacidad','i.abrinscri')->orderby('i.alumno')->get();                
               //var_dump($alumnos); exit;
                if(!$alumnos) return "NO TIENEN CALIFICACIONES ASIGNADAS";       
                $discapacidad = $this->discapacidad;  
                $escolaridad = $this->escolaridad;
                $periodo = $this->periodo;
                $consec = 1;               
                
                $pdf = PDF::loadView('reportes.cursos.pdf-riac-acred',compact('curso','alumnos','discapacidad','escolaridad','periodo','consec'));        
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream("RIAC-ACREDITACION");
            }
        }
        return "Clave no v&aacute;lida";
    } 
    
    public function riacCert(Request $request){
        $clave = $request->get('clave');
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            DB::raw("trim(substring(u.dunidad , position('.' in u.dunidad)+1,char_length(u.dunidad))) as dunidad"),'u.pdunidad',
            DB::raw("trim(substring(u.dgeneral , position('.' in u.dgeneral)+1,char_length(u.dgeneral))) as dgeneral"),'u.pdgeneral',
            DB::raw('EXTRACT(MONTH FROM termino)  as mes_termino') )
            ->where('clave',$clave)
            ->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')            
            ->first(); 
            //var_dump($curso);exit;
            //echo $curso->id; exit;
            if($curso){
                $consec_curso = $curso->id_curso; 
                $fecha_termino = $curso->termino;
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno','cal.acreditado','f.folio',DB::raw("to_char(f.fecha_expedicion, 'DD/MM/YYYY') as fecha_expedicion"),
                        'a_pre.sexo','a_pre.ultimo_grado_estudios','a_pre.discapacidad','i.abrinscri',DB::raw("to_char(i.created_at, 'YYYY-MM-DD') as fecha_creacion"),
                        DB::raw("EXTRACT(year from (age('".$fecha_termino."',a_pre.fecha_nacimiento))) as edad"),'a_pre.fecha_nacimiento' )
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->Join('tbl_calificaciones as cal', function($join){
                        $join->on('cal.idcurso', '=', 'i.id_curso');                
                        $join->on('cal.matricula', '=', 'i.matricula');                
                    })
                    ->Join('tbl_folios as f', function($join){
                        $join->on('f.id_curso', '=', 'i.id_curso');                
                        $join->on('f.matricula', '=', 'i.matricula');                
                    })
                    ->Join('alumnos_registro as a_reg', function($join)use($consec_curso){
                        //$join->on('a_r.id_curso', '=', $consec_curso);                
                        $join->on('a_reg.no_control', '=', 'i.matricula');                    
                    }) 
                    ->Join('alumnos_pre as a_pre', function($join)use($consec_curso){
                        $join->on('a_pre.id', '=', 'a_reg.id_pre');
                    });                
                $alumnos = $alumnos->groupby('i.matricula','i.alumno','i.created_at','cal.acreditado','f.folio',
                    'f.fecha_expedicion','a_reg.id_pre','a_pre.fecha_nacimiento','a_pre.sexo','a_pre.ultimo_grado_estudios',
                    'a_pre.discapacidad','i.abrinscri')->orderby('i.alumno')->get();
               //var_dump($alumnos); exit;
                if(!$alumnos) return "NO TIENEN FOLIOS ASIGNADOS";       
                $discapacidad = $this->discapacidad;  
                $escolaridad = $this->escolaridad;
                $periodo = $this->periodo;
                $consec = 1;               
                
                $pdf = PDF::loadView('reportes.cursos.pdf-riac-cert',compact('curso','alumnos','discapacidad','escolaridad','periodo','consec'));        
                $pdf->setPaper('Letter', 'landscape');
                return $pdf->stream("RIAC-CERTIFICACION");
            }
        }
        return "Clave no v&aacute;lida";
    } 
}