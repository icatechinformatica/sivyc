<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
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
    function __construct() {
        session_start();        
    }
    
    public function index(Request $request){       
        $id_user = Auth::user()->id;
        $message = $data = $unidad  = $fecha1 = $fecha2= NULL;
        
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = $data = NULL;
        if(session('message')) $message = session('message');
        // $rol="unidad"; 
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad','unidad');
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;           
        }
       // var_dump($_SESSION['unidades']);exit;
        if(!$unidades ){
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
            $_SESSION['unidades'] = $unidades;   
        }
       
       $unidad = $request->unidad;         
       $fecha1 = $request->fecha1;
       $fecha2 = $request->fecha2;
       $opcion = $request->opcion;
       if($unidad OR $fecha1 OR $fecha2){                     
           $data = DB::table('tbl_cursos as c')->where('clave','!=','0');
           if($opcion == "TERMINADOS"){
             if($request->fecha1) $data = $data->where('c.termino','>=',$request->fecha1);
             if($request->fecha2) $data = $data->where('c.termino','<=',$request->fecha2);
           }else{
              if($request->fecha1) $data = $data->where('c.inicio','>=',$request->fecha1);
              if($request->fecha2) $data = $data->where('c.inicio','<=',$request->fecha2);
           }
             if($request->unidad) $data = $data->where('c.unidad',$request->unidad); 
             if($_SESSION['unidades'])$data = $data->whereIn('c.unidad',$_SESSION['unidades']);                       
                
           $data = $data->orderby('c.unidad')->orderby('c.termino')->get();
       }
        //var_dump($data);exit;
        return view('consultas.cursosaperturados', compact('message','unidades','data','unidad', 'fecha1', 'fecha2','opcion'));     
    }  
    
    public function xls(Request $request){        
        $unidad = $request->unidad;            
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $opcion = $request->opcion;
        if($unidad OR $fecha1 OR $fecha2){    
            //$mes = [1=>"ENERO",2=>"FEBRERO",3=>"MARZO",4=>"ABRIL",5=>"MAYO",6=>"JUNIO",7=>"JULIO",8=>"AGOSTO",9=>"SEPTIEMBRE", 10=>"OCTUBRE", 11=>"NOVIEMBRE",12=>"DICIEMBRE"];
                      
            $data = DB::table('tbl_cursos as c')->where('clave','!=','0')->select('unidad','espe','clave','curso','mod','dura',
            'inicio', 'termino', DB::raw("To_char(termino, 'TMMONTH')"), DB::raw("CONCAT(hini,' A ',hfin) as horario"),'dia','horas',DB::raw("hombre+mujer as cupo"),'nombre',
            'cp','mujer','hombre','costo','tipo_curso','tipo','nota','muni','depen','munidad','mvalida','nmunidad','nmacademico','efisico','modinstructor','status','tcapacitacion','status_curso');
            if($opcion == "TERMINADOS"){
                if($request->fecha1) $data = $data->where('c.termino','>=',$request->fecha1);
                if($request->fecha2) $data = $data->where('c.termino','<=',$request->fecha2);
            }else{
                if($request->fecha1) $data = $data->where('c.inicio','>=',$request->fecha1);
                if($request->fecha2) $data = $data->where('c.inicio','<=',$request->fecha2);
            }
            if($request->unidad) $data = $data->where('c.unidad',$request->unidad); 
            if($_SESSION['unidades'])$data = $data->whereIn('c.unidad',$_SESSION['unidades']);                       
                
            $data = $data->orderby('c.unidad')->orderby('c.termino')->get();
                
            if(count($data)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
           
            $head = ['UNIDAD','ESPECIALIDAD','CLAVE','CURSO','MOD','DURA','INICIO','TERMINO','MES_TERMINO','HORARIO','DIAS',	
            'HORAS','CUPO','INSTRUCTOR','CP','FEM','MASC','CUOTA','ESQUEMA','TIPO PAGO','OBSERVACIONES','MUNICIPIO',	
            'DEPENDENCIA BENEFICIADA','MEMO DE SOLICITUD','MEMO DE AUTORIZACION','MEMO DE SOLICITUD DE REPROGRAMACION',
            'MEMO DE AUTORIZACION DE REPROGRAMACION','ESPACIO','PAGO INSTRUCTOR','ESTATUS_FORMATOT','CAPACITACION','ESTATUS_APERTURA'];            
            
            $title = "CURSOS_".$opcion."_".$unidad;
            if($unidad)  $name = "CURSOS_".$opcion."_".$unidad."_".date('Ymd').".xlsx";  
            else   $name = "CURSOS_".$opcion."_".date('Ymd').".xlsx";  
        
            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);
             
        }else echo "Seleccione un rango de fecha";
                
    } 
    
}