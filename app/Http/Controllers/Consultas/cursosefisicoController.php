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

class cursosefisicoController extends Controller
{
    function __construct() {
        session_start();
    }

    public function index(Request $request){
        $id_user = Auth::user()->id;
        $message = $data = $unidad  = $fecha1 = $fecha2 =  $valor = NULL;

        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');
        $unidades = $message = $data = NULL;
        session(['unidades' => null]);
        if(session('message')) $message = session('message');
        // $rol="unidad";
        if($rol){
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->ORDERBY('unidad','asc')->pluck('unidad','unidad');
            if(count($unidades)==0) $unidades =[$unidad];
            session(['unidades' => $unidades]);
        }
       // var_dump(session('unidades'));exit;
        if(!$unidades ){
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
            session(['unidades' => $unidades]);

        }

       $unidad = $request->unidad;
       $fecha1 = $request->fecha1;
       $fecha2 = $request->fecha2;
       $opcion = $request->opcion;
       $valor = $request->valor;


       if($unidad OR $fecha1 OR $fecha2 OR $valor){
           $data = DB::table('tbl_cursos as c')->where('clave','!=','0');
            if($valor){
                $data = $data->where('c.clave','like','%' . $valor.'%')
                ->orWhere('c.munidad', 'like','%'. $valor.'%');
            }
           if($opcion == "TERMINADOS"){
             if($request->fecha1) $data = $data->where('c.termino','>=',$request->fecha1);
             if($request->fecha2) $data = $data->where('c.termino','<=',$request->fecha2);
             $data =  $data->orderby('c.unidad')->orderby('c.termino','DESC');
           }else{
              if($request->fecha1) $data = $data->where('c.inicio','>=',$request->fecha1);
              if($request->fecha2) $data = $data->where('c.inicio','<=',$request->fecha2);
              $data =  $data->orderby('c.unidad')->orderby('c.inicio','DESC');
           }
             if($request->unidad) $data = $data->where('c.unidad',$request->unidad);
             if(session('unidades'))$data = $data->whereIn('c.unidad',session('unidades'));

           $data = $data->get();
       }
        //var_dump($data);exit;
        return view('consultas.cursosefisico', compact('message','unidades','data','unidad', 'fecha1', 'fecha2','opcion', 'valor'));
    }

    public function xls(Request $request){
        $unidad = $request->unidad;
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $opcion = $request->opcion;
        $valor = $request->valor;
        if($unidad OR $fecha1 OR $fecha2 OR $valor){
            //$mes = [1=>"ENERO",2=>"FEBRERO",3=>"MARZO",4=>"ABRIL",5=>"MAYO",6=>"JUNIO",7=>"JULIO",8=>"AGOSTO",9=>"SEPTIEMBRE", 10=>"OCTUBRE", 11=>"NOVIEMBRE",12=>"DICIEMBRE"];

            $data = DB::table('tbl_cursos as c')->where('clave','!=','0')->select('unidad','espe','clave','curso','nombre','efisico','mod','dura',
            'inicio', 'termino', DB::raw("To_char(termino, 'TMMONTH')"), DB::raw("CONCAT(hini,' A ',hfin) as horario"),'dia','horas',DB::raw("hombre+mujer as cupo"),
            'cp','mujer','hombre','costo','tipo_curso','tipo','nota','muni','depen','munidad','mvalida','nmunidad','nmacademico','modinstructor','status','tcapacitacion','status_curso');

            if($valor){
                $data = $data->where('c.clave','like','%' . $valor.'%')
                ->orWhere('c.munidad', 'like','%'. $valor.'%');
            }
            if($opcion == "TERMINADOS"){
                if($request->fecha1) $data = $data->where('c.termino','>=',$request->fecha1);
                if($request->fecha2) $data = $data->where('c.termino','<=',$request->fecha2);
                $data =  $data->orderby('c.unidad')->orderby('c.termino','DESC');
            }else{
                if($request->fecha1) $data = $data->where('c.inicio','>=',$request->fecha1);
                if($request->fecha2) $data = $data->where('c.inicio','<=',$request->fecha2);
                $data =  $data->orderby('c.unidad')->orderby('c.inicio','DESC');
            }
            if($request->unidad) $data = $data->where('c.unidad',$request->unidad);
            if(session('unidades'))$data = $data->whereIn('c.unidad',session('unidades'));

            $data = $data->get();

            if(count($data)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}

            $head = ['UNIDAD','ESPECIALIDAD','CLAVE','CURSO','INSTRUCTOR','ESPACIO','MOD','DURA','INICIO','TERMINO','MES_TERMINO','HORARIO','DIAS',
            'HORAS','CUPO','CP','FEM','MASC','CUOTA','ESQUEMA','TIPO PAGO','OBSERVACIONES','MUNICIPIO',
            'DEPENDENCIA BENEFICIADA','MEMO DE SOLICITUD','MEMO DE AUTORIZACION','MEMO DE SOLICITUD DE REPROGRAMACION',
            'MEMO DE AUTORIZACION DE REPROGRAMACION','PAGO INSTRUCTOR','ESTATUS_FORMATOT','CAPACITACION','ESTATUS_APERTURA'];

            $title = "CURSOS_".$opcion."_".$unidad;
            if($unidad)  $name = "CURSOSEFISICO_".$opcion."_".$unidad."_".date('Ymd').".xlsx";
            else   $name = "CURSOSEFISICO_".$opcion."_".date('Ymd').".xlsx";

            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);

        }else echo "Seleccione un dato para filtrar.";

    }

}
