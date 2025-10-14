<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use App\Models\cat\catUnidades;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Excel\xls;
use Maatwebsite\Excel\Facades\Excel;

class cursosaperturadosController extends Controller
{
    use catUnidades;
    function __construct(){        
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->data = $this->unidades_user('unidad');
            $this->unidades = $this->data['unidades'];      
            unset($this->unidades["ECE-CONOCER"]);      
            return $next($request);
        });
    }

    public function index(Request $request){
        $message = $data = $unidad  = $fecha1 = $fecha2 =  $valor = NULL;
        $unidades =$this->unidades;
        if(session('message')) $message = session('message');
        $unidad = $request->unidad;
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $opcion = $request->opcion;
        $valor = $request->valor;

        $data = $this->data($request);   
        $values = $request->all();
        return view('consultas.cursosaperturados', compact('message','unidades','data', 'values'));
        //return view('consultas.cursosaperturados', compact('message','unidades','data','unidad', 'fecha1', 'fecha2','opcion', 'valor'));
    }

    public function xls(Request $request){
        $data = $this->data($request, true);     
        $opcion = $request->opcion;

        if(count($data)>0){
            #CONDICION DEL ENCABEZADO DEL EXCEL
            if($opcion == 'EXONERADOS'){
                $head = ['UNIDAD','ESPECIALIDAD','CLAVE','CURSO','MOD','DURA','INICIO','TERMINO','MES_TERMINO','HORARIO','DIAS',
                'HORAS','CUPO','INSTRUCTOR','CP','FEM','MASC','CUOTA','ESQUEMA','TIPO PAGO','OBSERVACIONES_ARC01','OBSERVACIONES_ARC02','MUNICIPIO',
                'DEPENDENCIA BENEFICIADA','MEMO DE SOLICITUD','FECHA SOLICITUD','MEMO DE AUTORIZACION','FECHA AUTORIZACION','MEMO DE SOLICITUD DE REPROGRAMACION',
                'MEMO DE AUTORIZACION DE REPROGRAMACION','ESPACIO','PAGO INSTRUCTOR','ESTATUS_FORMATOT','CAPACITACION','ESTATUS_APERTURA','PLATAFORMA',
                'NO MEMO', 'FECHA MEMO', 'REDU/EXO', 'OBSERVACIONES_EXO', 'NO CONVENIO', 'NO OFICIO', 'FECHA OFICIO'];
            }else{
                $head = ['UNIDAD','ESPECIALIDAD','CLAVE','CURSO','MOD','DURA','INICIO','TERMINO','MES_TERMINO','HORARIO','DIAS',
                'HORAS','CUPO','INSTRUCTOR','CP','FEM','MASC','CUOTA','ESQUEMA','TIPO PAGO','OBSERVACIONES_ARC01','OBSERVACIONES_ARC02','MUNICIPIO',
                'DEPENDENCIA BENEFICIADA','MEMO DE SOLICITUD','FECHA SOLICITUD','MEMO DE AUTORIZACION','FECHA AUTORIZACION','MEMO DE SOLICITUD DE REPROGRAMACION',
                'MEMO DE AUTORIZACION DE REPROGRAMACION','ESPACIO','PAGO INSTRUCTOR','ESTATUS_FORMATOT','CAPACITACION','ESTATUS_APERTURA','PLATAFORMA'];
            }
            
            $title = "CURSOS_".$opcion;
            $name = "CURSOS_".$opcion."_".date('Ymd').".xlsx";
            
            return Excel::download(new xls($data,$head, $title), $name);
        }else { return "NO REGISTROS QUE MOSTRAR";exit;}
        

    }

    public function data(Request $request, $xls = false){
        $unidad = $request->unidad; 
        $fecha1 = $request->fecha1;
        if(!$request->fecha2) $fecha2 = $request->fecha1;
        else $fecha2 = $request->fecha2;
        $opcion = $request->opcion;
        $valor = $request->valor;
        $data = null;
        if($unidad OR $fecha1 OR $fecha2 OR $valor){    
            $data = DB::table('tbl_cursos as c')->where('clave','!=','0');        
            if($opcion == "EXONERADOS"){                 
                 $data->join('exoneraciones as exo', 'exo.folio_grupo', '=', 'c.folio_grupo');
                 if($xls){                 
                    $data->select('unidad','espe','clave','curso','mod','dura',
                    'inicio', 'termino', DB::raw("To_char(termino, 'TMMONTH')"), DB::raw("CONCAT(hini,' A ',hfin) as horario"),'dia','horas',DB::raw("hombre+mujer as cupo"),'nombre',
                    'cp','mujer','hombre','costo','tipo_curso','tipo','nota','exo.observaciones','muni','depen','munidad','fecha_arc01','mvalida','fecha_apertura','nmunidad',
                    'nmacademico','efisico','modinstructor','c.status','tcapacitacion','status_curso','medio_virtual',
                    'exo.no_memorandum', 'exo.fecha_memorandum', DB::raw("
                    CASE
                    WHEN exo.tipo_exoneracion = 'EXO' THEN 'EXONERACIÓN'
                    WHEN exo.tipo_exoneracion = 'EPAR' THEN 'REDUCCIÓN'
                    END as tpago"), 'exo.observaciones','exo.no_convenio','exo.noficio', 'exo.foficio');
                 }
            }else{                
                if($xls){
                    $data->select('unidad','espe','clave','curso','mod','dura',
                    'inicio', 'termino', DB::raw("To_char(termino, 'TMMONTH')"), DB::raw("CONCAT(hini,' A ',hfin) as horario"),'dia','horas',DB::raw("hombre+mujer as cupo"),'nombre',
                    'cp','mujer','hombre','costo','tipo_curso','tipo','nota','observaciones','muni','depen','munidad','fecha_arc01','mvalida','fecha_apertura','nmunidad',
                    'nmacademico','efisico','modinstructor','status','tcapacitacion','status_curso','medio_virtual');
                }
            }
            ##FILTRADOS
            if($valor){
                $data = $data->where(function ($q) use ($valor) {
                    $q->where('c.clave', 'like', "%$valor%")
                    ->orWhere('c.munidad', 'like', "%$valor%");
                });
            }else{         
                switch($opcion){
                    case "AUTORIZADOS":
                        $data->whereBetween('c.fecha_apertura', [$fecha1, $fecha2]);                        
                        $data =  $data->where('c.status_curso','AUTORIZADO')->orderby('c.unidad')->orderby('c.inicio','DESC');
                    break;
                    case "INICIADOS":
                        $data->whereBetween('c.inicio', [$fecha1, $fecha2]);                         
                        $data =  $data->orderby('c.unidad')->orderby('c.inicio','DESC');
                    break;
                    case "TERMINADOS":
                        $data->whereBetween('c.termino', [$fecha1, $fecha2]);                         
                        $data =  $data->orderby('c.unidad')->orderby('c.termino','DESC');
                    break;
                    case "EXONERADOS":
                        $data->whereBetween('c.termino', [$fecha1, $fecha2]);                         
                        $data = $data->where('exo.status','=', 'AUTORIZADO');
                        $data =  $data->orderby('c.unidad')->orderby('c.termino','DESC');
                    break;
                }               
            } 
            if(count($unidad)>0) $data = $data->whereIn('c.unidad',$unidad);
            else $data = $data->whereIn('c.unidad',$this->unidades);            
            $data = $data->get(); 
        }
        return $data;
    }

}
