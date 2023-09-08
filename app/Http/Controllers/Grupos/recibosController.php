<?php
namespace App\Http\Controllers\Grupos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\Unidad;
use PDF;

class recibosController extends Controller
{   
    function __construct() {  
        session_start(); 
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {   
            $this->user = Auth::user();
            $this->ubicacion = Unidad::where('id',$this->user->unidad)->value('ubicacion');
           if($this->user->roles[0]->slug =="admin")
                $this->unidades = Unidad::pluck('unidad');
            else
                $this->unidades = Unidad::where('ubicacion',$this->ubicacion)->pluck('unidad');
            
            return $next($request);
        });       
    }

    public function index(Request $request){   
        //dd( $this->user);
        if(session('folio_grupo'))$request->folio_grupo = session('folio_grupo');  

        [$data , $message] = $this->data($request);
        //dd($data);
        if(session('message')) $message = session('message');
        
        return  view('grupos.recibos.index', compact('message','data', 'request')); 
    } 
    
    public function asignar(Request $request) {         
        [$data , $message] = $this->data($request);
        if($data){
            $result = DB::table('tbl_recibos')->updateOrInsert(
                ['folio_recibo' => $data->folio_recibo ],
                [ 'importe' => $data->costo, 'importe_letra' =>'IMPORTE EN LETRAS','status' => 'ASIGNANDO',
                   'fecha_status' => date('Y-m-d h:m:s'), 'id_curso' => $data->id, 'folio_grupo' => $data->folio_grupo,
                   'clave' => $data->clave, 'fecha_expedicion' => $request->fecha_expedicion, 'recibio' => $request->recibio,
                   'unidad' => $data->unidad,
                   'recibio'=>$request->recibio,
                   'recibide'=>$request->recibide,
                   'fecha_expedicion' => $request->fecha
                ]
            );
            if($request) $message["ALERT"] = "NÚMERO DE RECIBO ASIGNADO CORRECTAMENTE!!";
            else $message["ERROR"] = "NO SE REALIZÓ LA ASIGNACIÓN, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    public function modificar(Request $request) {         
        [$data , $message] = $this->data($request);
        if($data){
            $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                [  'recibio'=>$request->recibio,
                   'recibide'=>$request->recibide,
                   'fecha_expedicion' => $request->fecha
                ]
            );
            if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";
            else $message["ERROR"] = "LA OPERACIÓN NO SE EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    private function data(Request $request){        
        $data = $message = [];
        if($request->folio_grupo){

            $data = DB::table('tbl_cursos as tc')  
                ->select('tc.id','tc.folio_grupo','tc.unidad','tu.ubicacion', 'tc.clave','tc.curso','tc.nombre','tc.tipo_curso',
                    'tc.status_curso','tc.inicio', 'tc.termino', 'tc.hini', 'tc.hfin','tc.costo','tc.hombre','tc.mujer','tr.recibide',
                    'tr.fecha_expedicion','tr.recibio','tu.direccion','tu.delegado_administrativo',
                    DB::raw('UPPER(tu.municipio) as municipio'),
                    DB::raw("
                        CASE WHEN tc.tipo = 'EXO' THEN 'EXONERACIÓN'
                        WHEN tc.tipo = 'PINS' THEN 'ORDINARIO'
                        WHEN tc.tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA' 
                        END as tpago"),                       
                    DB::raw("LEFT(tu.ubicacion,2) as uc"), 'tc.id','tc.clave',
                    DB::raw("(CASE
                        WHEN  tr.status is not null THEN tr.folio_recibo 
                        WHEN max.status is null THEN max.folio_recibo
                        ELSE max.folio_recibo+1
                        END) as folio_recibo"),
                    DB::raw("(
                        CASE
                        WHEN tr.status is null THEN 'DISPONIBLE'
                        ELSE  tr.status
                        END) as status_recibo"),
                    DB::raw("(CASE
                        WHEN  tr.folio_recibo = max.folio_recibo THEN true
                        ELSE false
                        END) as deshacer")
                )
                ->where('tc.folio_grupo',$request->folio_grupo)
                ->wherein('tc.unidad',$this->unidades)
                ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')
                ->leftjoin('tbl_recibos as tr', function ($join) {                    
                    $join->on('tr.folio_grupo','=','tc.folio_grupo');                        
                })
                ->join('tbl_recibos as max', function ($join) {
                        $join->on('max.unidad', '=', 'tu.ubicacion')                    
                        ->where('max.folio_recibo', '=', DB::raw("(SELECT max(folio_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion)")); 
                })
                ->first();     
                if(!$data->recibide) $data->recibide = DB::table('alumnos_registro')->where('folio_grupo',$request->folio_grupo)->value('realizo');                
                if(!$data->fecha_expedicion) $data->fecha_expedicion = date('Y-m-d');
                if(!$data->recibio) $data->recibio = $data->delegado_administrativo;
                $_SESSION['data'] = $data;
                if(!$data) $message["ERROR"]= "EL FOLIO INGRESADO NO CORRESPONDE A LA UNIDAD $this->ubicacion.";                
        }elseif($request->BUSCAR) $message["ERROR"] = "INGRESE EL FOLIO DE GRUPO PARA EJECUTAR LA BUSQUEDA";

        return [$data, $message];
    }

    public function pdfRecibo(Request $request){
        if($_SESSION['data']){
            $data = $_SESSION['data'];            
            $letras = $this->letras($data->costo);
            //dd($letra);
            $direccion = $data->direccion;
            $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
            $pdf = PDF::loadView('grupos.recibos.pdfRecibo',compact('data','distintivo','direccion','letras'));
            $pdf->setpaper('halfletter','portrait');
            //$pdf->setpaper(['width' => 5.5, 'height' => 8.5], 'portrait');
            return $pdf->stream('Recibo.pdf');
        }else return "ACCIÓN INVÁlIDA";exit;
    }
    
    private function letras($cantidad){
        $unidades = ["", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $decenas = ["", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
        $especiales = ["diez", "once", "doce", "trece", "catorce", "quince"];
        $centenas = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];
    
        $entero = floor($cantidad);
        $decimal = round(($cantidad - $entero) * 100);    
        $pesos = ($entero == 1) ? "peso" : "pesos";
        $centavos = ($decimal == 1) ? "centavo" : "centavos";    
        $parteEntera = "";
        $parteDecimal = "";
    
        if ($entero >= 1 && $entero <= 999999999) {
            $millones = floor($entero / 1000000);
            $millar = floor(($entero % 1000000) / 1000);
            $centena = floor(($entero-1000) / 100);
            $unidad = $entero % 1000;    
            if ($millones > 0) $parteEntera .= $this->letras($millones) . " millón ";
            if ($millar > 0) {
                if ($millar == 1) $parteEntera .= "mil ";
                else $parteEntera .= $this->letras($millar) . " mil ";            
            }
            if ($centena > 0) $parteEntera .= $centenas[$centena] . " ";
            if ($unidad > 0) {
                if ($unidad == 1) $parteEntera .= "un ";                
                if ($unidad >= 2 && $unidad <= 9) $parteEntera .= $unidades[$unidad] . " ";                
            }    
            $parteEntera .= " ";
        } else $parteEntera = "No soportado";
        
        if ($decimal > 0) {
            if ($decimal >= 10 && $decimal <= 15) {
                $parteDecimal .= $especiales[$decimal - 10];
            } else {
                $d = floor($decimal / 10);
                $u = $decimal % 10;    
                if ($d > 0) $parteDecimal .= $decenas[$d] . " y ";                
                if ($u > 0) $parteDecimal .= $unidades[$u];                
            }    
            $parteDecimal = " $decimal/100 MN ";
        } else $parteDecimal = " 00/100 MN ";
        return strtoupper(trim($parteEntera) . " $pesos" . $parteDecimal );
    }
    
}