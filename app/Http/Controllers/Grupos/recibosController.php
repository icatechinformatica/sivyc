<?php
namespace App\Http\Controllers\Grupos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Unidad;
use Carbon\Carbon;
use PDF;

class recibosController extends Controller
{   
    function __construct() {  
        session_start(); 
        $this->middleware('auth');
        $this->path_pdf = "/UNIDAD/recibos_pago/";        
        $this->path_files = env("APP_URL").'/storage/uploadFiles';        
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
        //dd( $this->unidades);
        $movimientos = [];
        if(session('folio_grupo'))$request->folio_grupo = session('folio_grupo');  

        [$data , $message] = $this->data($request);
        //dd($data);
        if(session('message')) $message = session('message');

        if($data){            
            if($data->deshacer)$movimientos = [ 'SUBIR' => 'SUBIR ARCHIVO PDF', 'DESHACER'=>'DESHACER ASIGNACION'];
            elseif (!in_array($data->status_recibo, ['ENVIADO','DISPONIBLE'])) $movimientos = [ 'SUBIR' => 'SUBIR ARCHIVO PDF'];

            if($data->status_recibo=="ENVIADO") $movimientos = [ 'RECHAZADAR' => 'SOLICITUD DE RETORNO', 'REEMPLAZAR'=>'SOLICITUD DE REEMPLAZO DE RECIBO', 'CANCELAR' => 'SOLICITUD DE CANCELACIÓN'];
        }     
        $path_files = $this->path_files;
        return  view('grupos.recibos.index', compact('message','data', 'request','movimientos','path_files')); 
    } 
    
    public function buscar(Request $request){ 
        $data = $message = [];
        $data = DB::table('tbl_recibos as tr')  
            ->select('tc.curso','tc.nombre','tc.hombre','tc.mujer', 'tc.costo','tc.inicio','tc.termino','tc.hini','tc.hfin',
                DB::raw("CASE 
                    WHEN tr.status='CANCELADO' THEN 'CANCELADO' 
                    WHEN tr.status='ENVIADO' THEN 'ENVIADO' 
                    ELSE 'ASIGNADO'  END as status_recibo"),
                'tr.*', 'tu.ubicacion')          
            ->wherein('tc.unidad',$this->unidades);
            if($request->folio_grupo){
                $data = $data->where(DB::raw('CONCAT(folio_recibo,tc.folio_grupo,tc.clave)'),'LIKE','%'.$request->folio_grupo.'%');
            }
            
            $data = $data->leftjoin('tbl_cursos as tc', function ($join) {                    
                $join->on('tc.folio_grupo','=','tr.folio_grupo'); 
            })
            ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')
            ->orderby('tr.fecha_expedicion')
            ->paginate(15); 

            $data->appends($request->except('page'));            
            $path_files = $this->path_files;
        return  view('grupos.recibos.buscar', compact('message','data','request','path_files')); 
    }

    public function asignar(Request $request) {         
        [$data , $message] = $this->data($request);
        if($data){
            $letras = $this->letras($data->costo);
            $result = DB::table('tbl_recibos')->updateOrInsert(
                ['num_recibo' => $data->num_recibo, 'unidad' => $data->ubicacion],
                [ 'importe' => $data->costo, 'importe_letra' => $letras,'status' => 'ASIGNADO',
                   'fecha_status' => date('Y-m-d h:m:s'), 'id_curso' => $data->id, 'folio_grupo' => $data->folio_grupo,
                   'clave' => $data->clave, 'fecha_expedicion' => $request->fecha_expedicion, 'recibio' => $request->recibio,
                   'recibio'=>$request->recibio,
                   'recibide'=>$request->recibide,
                   'fecha_expedicion' => $request->fecha,
                   'iduser_updated' => $this->user->id,
                   'updated_at'=> date('Y-m-d'),
                   'folio_recibo' => $data->uc.'-'.$data->num_recibo
                ]
            );
            if($request) $message["ALERT"] = "NÚMERO DE RECIBO ASIGNADO CORRECTAMENTE!!";
            else $message["ERROR"] = "NO SE REALIZÓ LA ASIGNACIÓN, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    public function deshacer(Request $request) {         
        [$data , $message] = $this->data($request);
        if($data->deshacer){
            $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->whereNotIn('status', ['ENVIADO','DISPONIBLE'])->update(                
                [ 
                    'importe' => 0, 'importe_letra' =>null,'status' => null,
                    'fecha_status' => null, 'id_curso' => null, 'folio_grupo' => null,
                    'clave' => null, 'fecha_expedicion' => null, 'recibio' => null,                   
                    'recibio'=> null,
                    'recibide'=>null,
                    'fecha_expedicion' => null,
                    'file_pdf' => null,
                    'iduser_updated' => $this->user->id,
                    'updated_at'=> date('Y-m-d')               
                ]
            );
            if($request){
                $file_delete = "uploadFiles".$data->file_pdf;
                if(Storage::exists($file_delete)){
                     Storage::delete($file_delete);
                     $message["ALERT"] = "LA ASIGNACIÓN Y EL ARCHIVO HAN SIDO ELIMINADOS!!";  
                }else $message["ALERT"] = "LA ASIGNACIÓN HA SIDO ELIMINADA!!";  
            } 
            else $message["ERROR"] = "EL MOVIMIENTO DE DESHACER NO SE EJECUTO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    public function modificar(Request $request) {         
        [$data , $message] = $this->data_validate($request);
        if($data){
            $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                [  'recibio'=>$request->recibio,
                   'recibide'=>$request->recibide,
                   'fecha_expedicion' => $request->fecha,
                   'iduser_updated' => $this->user->id,
                   'updated_at'=> date('Y-m-d')
                ]
            );
            if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";
            else $message["ERROR"] = "LA OPERACIÓN NO SE EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    public function subir(Request $request) {   
        [$data , $message] = $this->data_validate($request); //dd($request->hasFile('file_recibo')); dd($data);
        if($data->folio_grupo AND !in_array($data->status, ['ENVIADO','DISPONIBLE'])){
            if ($request->hasFile('file_recibo')) {               
                $name_file = $data->id."_".date('ymdHis')."_". $this->user->id;                                
                $file = $request->file('file_recibo');                
                $file_result = $this->upload_file($file,$name_file,$data->file_pdf);                
                $url_file = $file_result["url_file"];                 
                if($file_result['up']){                       
                    if($data){                        
                        $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                            [  'status' => 'CARGADO',
                            'fecha_status' => date('Y-m-d h:m:s'),
                            'iduser_updated' => $this->user->id,
                            'updated_at'=> date('Y-m-d h:m:s'),
                            'file_pdf' => $url_file
                            ]
                        );
                        if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";
                        else $message["ERROR"] = "LA OPERACIÓN NO SE EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
                    }

                }
            
            }
        }
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    public function enviar(Request $request) {         
        [$data , $message] = $this->data_validate($request);
        if($data){            
            $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->where('status','CARGADO')->update(                
                [ 'status' => 'ENVIADO','fecha_status' => date('Y-m-d h:m:s'),                    
                   'iduser_updated' => $this->user->id, 'updated_at'=> date('Y-m-d')
                ]
            );
            if($request) $message["ALERT"] = "EL RECIBO DE PAGO HA SIDO ENVIADO CORRECTAMENTE!!";
            else $message["ERROR"] = "EL RECIBO DE PAGO NO SE ENVIADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
        }        
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    private function data_validate(Request $request){        
        $data = $message = [];
        if($request->folio_grupo){
            $data = DB::table('tbl_recibos as tc')->where('folio_grupo',$request->folio_grupo)->whereNotIn('status',['CANCELADO'])->first();     
                if(!$data) $message["ERROR"]= "EL FOLIO INGRESADO NO CORRESPONDE A LA UNIDAD $this->ubicacion.";                
        }
        return [$data, $message];
    }

    private function data(Request $request){        
        $data = $message = [];
        if($request->folio_grupo){
            $data = DB::table('tbl_cursos as tc')  
                ->select('tc.id','tc.folio_grupo','tc.unidad','tu.ubicacion', 'tc.clave','tc.curso','tc.nombre','tc.tipo_curso',
                    'tc.status_curso','tc.inicio', 'tc.termino', 'tc.hini', 'tc.hfin','tc.costo','tc.hombre','tc.mujer','tr.recibide',
                    'tr.fecha_expedicion','tr.recibio','tu.direccion','tu.delegado_administrativo','tr.id as id_recibo','tr.file_pdf',
                    'tr.importe_letra', 'tr.folio_recibo',
                    DB::raw('UPPER(tu.municipio) as municipio'),
                    DB::raw("
                        CASE WHEN tc.tipo = 'EXO' THEN 'EXONERACIÓN'
                        WHEN tc.tipo = 'PINS' THEN 'ORDINARIO'
                        WHEN tc.tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA' 
                        END as tpago"),                       
                    DB::raw("LEFT(tu.ubicacion,2) as uc"), 'tc.id','tc.clave',
                    DB::raw("(CASE
                        WHEN  tr.status is not null THEN tr.num_recibo 
                        WHEN max.status is null THEN (SELECT min(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status is null)
                        ELSE max.num_recibo+1
                        END) as num_recibo"),
                    DB::raw("(
                        CASE
                        WHEN tr.status is null THEN 'DISPONIBLE'
                        ELSE  tr.status
                        END) as status_recibo"),
                    DB::raw("(CASE
                        WHEN  tr.num_recibo = (SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status is not null and status!='ENVIADO') THEN true                        
                        ELSE false
                        END) as deshacer")
                )
                ->where('tc.folio_grupo',$request->folio_grupo)
                ->wherein('tc.unidad',$this->unidades)
                ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')
                ->leftjoin('tbl_recibos as tr', function ($join) {                    
                    $join->on('tr.folio_grupo','=','tc.folio_grupo')
                    ->whereNotIn('tr.status',['CANCELADO']); 
                })
                ->join('tbl_recibos as max', function ($join) {
                        $join->on('max.unidad', '=', 'tu.ubicacion')                    
                        ->where('max.num_recibo', '=', DB::raw("(SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and ( status!='ENVIADO' or status is null ))")); 
                })
                ->first();                     
                $_SESSION['data'] = $data;
                if(!$data) $message["ERROR"]= "EL FOLIO INGRESADO NO CORRESPONDE A LA UNIDAD $this->ubicacion.";           
                else{
                    if(!$data->recibide) $data->recibide = DB::table('alumnos_registro')->where('folio_grupo',$request->folio_grupo)->value('realizo');                
                    if(!$data->fecha_expedicion) $data->fecha_expedicion = date('Y-m-d');
                    if(!$data->recibio) $data->recibio = $data->delegado_administrativo;
                }     
        }elseif($request->BUSCAR) $message["ERROR"] = "INGRESE EL FOLIO DE GRUPO PARA EJECUTAR LA BUSQUEDA";

        return [$data, $message];
    }

    public function pdfRecibo(Request $request){
        if($_SESSION['data']){
            $data = $_SESSION['data'];        //dd($data->costo);            
            //dd($letra);
            $direccion = $data->direccion;
            $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
            $fecha = date('d/m/Y', strtotime($data->fecha_expedicion));            
            $pdf = PDF::loadView('grupos.recibos.pdfRecibo',compact('data','distintivo','direccion','fecha'));
            $pdf->setpaper('letter','portrait');            
            return $pdf->stream('nombre_del_archivo.pdf');            
        }else return "ACCIÓN INVÁlIDA";exit;
    }
    
    protected function upload_file($file, $name, $file_delete){       
        //php artisan storage:link
        $ext = $file->getClientOriginalExtension();
        $ext = strtolower($ext);
        $path=$url = $mgs= null;
        $up = false;
        if($ext == "pdf"){            
            $name = trim($name.".pdf");
            $path = $this->path_pdf.$name;            
            $up = Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
            if($up){
                $file_delete = "uploadFiles".$file_delete;
                if(Storage::exists($file_delete)){
                    Storage::delete($file_delete);
                    $msg = "El archivo ha sido reemplazado correctamente!";
                }else $msg = "El archivo ha sido cargado correctamente!";
            }
        }else $msg= "Formato de Archivo no válido, sólo PDF.";
                
        $data_file = ["message"=>$msg, 'url_file'=>$path, 'up'=>$up];
       
        return $data_file;
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
            $millar = floor(($entero % 1000000) / 1000); //dd($millar);
            $centena =  floor(($entero % 1000) / 100); //dd($centena);            
            $decena = floor(($entero % 100) / 10); //dd($decena);
            $unidad = $entero % 10;

            if ($millones > 0) $parteEntera .= $this->letras($millones) . " millón ";
            if ($millar > 0) {
                if ($millar == 1) $parteEntera .= "mil ";
                else $parteEntera .= $unidades[$millar] . " mil ";            
            }
            if ($centena > 0) $parteEntera .= $centenas[$centena] . " ";
            if ($decena > 0) $parteEntera .= $decenas[$decena] . " ";
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