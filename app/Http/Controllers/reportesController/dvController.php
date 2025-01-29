<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel;
use App\Utilities\MyUtility;
use PDF;
use DateTime;
class dvController extends Controller
{
    function __construct() {        

    }

    public function index(Request $request){ 
        $id_user = Auth::user()->id;        
        $data = $message = [];                       
        if(session('message')) $message = session('message');
        if(session('data')) $data = session('data');  

        if(session('fecha1')) $fecha1 = session('fecha1');  
        else $fecha1 = $request->fecha1;

        if(session('fecha2')) $fecha2 = session('fecha2');
        else $fecha2 = $request->fecha2;

        return view('reportes.dv.index', compact('data','message','fecha1', 'fecha2'));
    }

    public function generar(Request $request){    //dd($request->request->parameters);        
        $data = $message = [];
        $data = $this->data($request);// dd($data);
        if (is_array($data) && array_key_exists("ERROR", $data)){
            $message = $data;
            $data = [];
        }        
        if($data){
            switch($request->opcion){
                case "FILTRAR":
                    return redirect('reportes/dv')->with(['data'=>$data,'message'=>$message, 'fecha1'=>$request->fecha1, 'fecha2' => $request->fecha2]);
                break;
                case "XLS":                    
                    $title = "DPA-Reporte de Operación con Convenios Generales Vigentes";
                    $name = $title."_".date('Ymd').".xlsx";
                    $view = 'reportes.dv.excel_operacion_convenios'; 
                    return Excel::download(new ExportExcel($data,null, $title,$view), $name);                    
                break;                              
            }

        }else return redirect('reportes/dv')->with(['message'=>$message]);        
    }
    
    private function data(Request $request){       
        if($request->fecha1 and $request->fecha2 ){
            $start_date = new DateTime($request->fecha1);
            $end_date = new DateTime($request->fecha2);
            $meses = [];            
            while ($start_date <= $end_date) {                
                $mes = $start_date->format('Y-m');
                if (!in_array($mes, $meses)) {
                    $meses[] = $mes;
                }
                $start_date->modify('first day of next month');
            }
            
            //dd($meses);
            $data = DB::table('convenios as c')
            ->leftJoin('tbl_cursos as tc', 'c.no_convenio', '=', 'tc.cgeneral')
            ->leftJoin('tbl_inscripcion as ti', 'ti.id_curso', '=', 'tc.id')
            ->select(
                'c.no_convenio',
                'c.institucion',
                'c.tipo_sector',
                'c.fecha_firma',
                'c.fecha_vigencia',
                'c.poblacion',
                'c.municipio',
                DB::raw('COUNT(DISTINCT tc.id) as total_cursos'),
                DB::raw("SUM(CASE WHEN ti.calificacion != 'NP' AND ti.status = 'INSCRITO' AND ti.sexo = 'M' THEN 1 ELSE 0 END) as total_egresamos_mujeres"),
                
            )
            ->whereBetween('c.fecha_firma', ['2024-01-01', '2024-12-01'])
            ->where('tc.status_curso', 'AUTORIZADO')
            ->groupBy(
                'c.no_convenio',
                'c.institucion',
                'c.tipo_sector',
                'c.fecha_firma',
                'c.fecha_vigencia',
                'c.poblacion',
                'c.municipio'
            )
            ->get();
dd($data);
            return $data;                            
        }else $message["ERROR"] = "SE REQUIERE QUE SELECCIONE LA FECHA INICIAL Y FECHA FINAL PARA GENERAR EL REPORTE.";             
        //dd($message);
        if($message) return $message;
    }

    function obtenerNumeroQuincena($fecha) {        
        $date = new DateTime($fecha);
        $inicioAnio = new DateTime($date->format('Y') . '-01-01');        
        $diasTranscurridos = $inicioAnio->diff($date)->days;        
        $numeroQuincena = intdiv($diasTranscurridos, 15) + 1;                
        return min($numeroQuincena, 24);
    }
}
