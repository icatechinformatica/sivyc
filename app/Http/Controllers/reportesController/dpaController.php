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
class dpaController extends Controller
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

        return view('reportes.dpa.index', compact('data','message','fecha1', 'fecha2'));
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
                    return redirect('reportes/dpa')->with(['data'=>$data,'message'=>$message, 'fecha1'=>$request->fecha1, 'fecha2' => $request->fecha2]);
                break;
                case "XLS":                    
                    $title = "DPA-Reporte de Instructores";
                    $name = $title."_".date('Ymd').".xlsx";
                    $view = 'reportes.dpa.excel_instructores'; 
                    return Excel::download(new ExportExcel($data,null, $title,$view), $name);                    
                break;                              
            }

        }else return redirect('reportes/dpa')->with(['message'=>$message]);        
    }
    
    private function data(Request $request){       
        if($request->fecha1 and $request->fecha2 ){   
            $NoQna = $this->obtenerNumeroQuincena($request->fecha1); 
            $data = DB::table('tbl_cursos as tc')
                ->selectRaw("'2' as nqna, 'ICAT' as subsistema, 'CHIAPAS' as entidad")
                ->selectRaw("MAX(CASE 
                                WHEN ze = 'I' THEN 1
                                WHEN ze = 'II' THEN 2
                                WHEN ze = 'III' THEN 3
                                WHEN ze = 'VI' THEN 4
                                ELSE 0 
                            END) as ze")
                ->selectRaw('MAX(nombre) as nombre')
                ->addSelect('curp')
                ->selectRaw('MAX(rfc) as rfc')
                ->selectRaw("'DOCENTE' as tipo_plaza")
                ->selectRaw("'PROFESOR INSTRUCTOR DE CAPACITACIÓN' as plaza")
                ->selectRaw("'E11001' as codigo_plaza")
                ->selectRaw('SUM(dura) as horas')
                ->addSelect('cct')
                ->where('status_curso', 'AUTORIZADO')
                ->whereBetween('inicio', [$request->fecha1, $request->fecha2])
                ->groupBy('curp', 'cct')
                ->orderByRaw('MAX(nombre)')
                ->get();

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
