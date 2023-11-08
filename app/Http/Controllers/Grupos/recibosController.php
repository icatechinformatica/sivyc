<?php
namespace App\Http\Controllers\Grupos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Utilities\MyUtility;
use App\Models\Unidad;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Response;

class recibosController extends Controller
{   
    function __construct() {  
        session_start(); 
        $this->middleware('auth');
        $this->path = "/expedientes/";
        $this->path_files = env("APP_URL").'/storage/';
        $this->middleware(function ($request, $next) {   
            $this->user = Auth::user();
            $this->ubicacion = Unidad::where('id',$this->user->unidad)->value('ubicacion');
           if($this->user->roles[0]->slug =="admin")
                $this->unidades = Unidad::orderby('unidad')->pluck('unidad','unidad');
            else
                $this->unidades = Unidad::where('ubicacion',$this->ubicacion)->orderby('unidad')->pluck('unidad','unidad');
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
            if($data->deshacer)$movimientos = [ 'SUBIR' => 'SUBIR ARCHIVO PDF', 'ESTATUS'=>'CAMBIO DE ESTATUS', 'DESHACER'=>'DESHACER ASIGNACION'];
            elseif((!$data->status_curso and !in_array($data->status_folio, ['DISPONIBLE','IMPRENTA'])) OR in_array($data->status_folio,['ACEPTADO', 'CARGADO'])) $movimientos = [ 'SUBIR' => 'SUBIR ARCHIVO PDF', 'ESTATUS'=>'CAMBIO DE ESTATUS'];            
            
            if($data->status_folio=="ENVIADO" and $data->status_curso=='CANCELADO') $movimientos = [ 'CANCELAR' => 'CANCELAR'];
            elseif($data->status_folio=="ENVIADO" and $data->status_curso) $movimientos = [ 'SOPORTE' => 'SOLICITUD DE CAMBIO SOPORTES'];
        }     
        $path_files = $this->path_files;
        return  view('grupos.recibos.index', compact('message','data', 'request','movimientos','path_files')); 
    } 
    
    public function buscar(Request $request){ 
        $data = $message = [];
        if(!$request->ejercicio)$request->ejercicio = date('Y');
        if(!$request->status and !$request->folio_grupo) $request->status = "PENDIENTES";

        $data = DB::table('tbl_cursos as tc')             
            ->select('tc.curso','tc.nombre','tc.hombre','tc.mujer', 'tc.costo','tc.inicio','tc.termino','tc.hini','tc.hfin','tc.folio_grupo',
                DB::raw("CASE 
                    WHEN tc.clave ='0' and tc.status_curso IS NULL THEN 'EN TRAMITE UNIDAD' 
                    WHEN tc.clave ='0' and tc.status_curso IS NOT NULL THEN 'EN TRAMITE DTA' 
                    ELSE tc.clave
                    END as clave"),
                DB::raw("CASE 
                    WHEN tr.status_folio='CANCELADO' THEN 'CANCELADO' 
                    WHEN tr.status_folio='ENVIADO' THEN 'ENVIADO'
                    WHEN tr.status_folio='SOPORTE' THEN 'CAMBIO DE SOPORTE'
                    WHEN tr.status_folio='ACEPTADO' THEN 'CAMBIO ACEPTADO'
                    WHEN tr.status_folio='DENEGADO' THEN 'CAMBIO DENEGADO'
                    WHEN tc.comprobante_pago <> 'null' OR tr.folio_grupo <>'null' THEN 'ASIGNADO'
                    ELSE 'PENDIENTE'                     
                    END as status_folio"),
                DB::raw("
                    CASE 
                        WHEN tc.folio_pago <> 'null' THEN tc.folio_pago
                        WHEN tr.folio_recibo <> 'null' THEN tr.folio_recibo
                        ELSE 'NO DISPONIBLE'
                    END as folio_recibo
                "),
                DB::raw(" 
                    CASE
                        WHEN tc.comprobante_pago <> 'null' THEN concat('uploadFiles',tc.comprobante_pago)
                        WHEN tr.file_pdf <> 'null' THEN tr.file_pdf
                    END as file_pdf

                "),                
                'tu.unidad');         
            $data = $data->wherein('tc.unidad',$this->unidades);
            if($request->folio_grupo){
                    $data = $data->where(DB::raw('CONCAT(folio_recibo,tc.folio_grupo,tc.clave)'),'ILIKE','%'.$request->folio_grupo.'%');
                    $request->unidad = $request->status = null;
            }else{ 
                $data = $data->whereYear('tc.inicio', $request->ejercicio);
                if($request->unidad) $data = $data->where('tc.unidad', $request->unidad); 

                if($request->status){
                    switch($request->status){    
                        case "PENDIENTES":
                            $data = $data->where('tc.status_curso', NULL)->whereNull('tc.comprobante_pago')
                                ->whereNotExists(function ($query) {
                                    $query->from('tbl_recibos')->whereRaw('tc.folio_grupo = tr.folio_grupo'); 
                                })->orderby('tc.folio_grupo','ASC');
                        break; 
                        case "ASIGNADOS":
                            $data = $data->where(function ($query) {
                                $query->whereNotNull('tc.comprobante_pago')
                                    ->orWhereNotNull('tr.folio_grupo');
                            })->orderby('tc.folio_grupo','DESC');                            
                        break; 
                        case "ENVIADOS":
                            $data = $data->where('tr.status_folio', 'ENVIADO');
                        break;
                        case "POR COBRAR":
                            $data = $data->where('tr.status_recibo', 'POR COBRAR');
                        break;                
                        case "PAGADOS":
                            $data = $data->where('tr.status_recibo', 'PAGADO');
                        break;
                        case "CANCELADOS":
                            $data = $data->where('tr.status_folio', 'CANCELADO');
                        break;
                                     
                    }                    
                }
            }            
            $data = $data->where('tc.tipo','!=','EXO');
            $data = $data->leftjoin('tbl_recibos as tr', function ($join) {                    
                $join->on('tc.folio_grupo','=','tr.folio_grupo'); 
            })
            ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')            
            ->paginate(15); 

            $data->appends($request->except('page'));            
            $path_files = $this->path_files;
            $anios = MyUtility::ejercicios();
            $unidades = $this->unidades;           
            
        return  view('grupos.recibos.buscar', compact('message','data','request','path_files','anios','unidades')); 
    }

    public function asignar(Request $request) {         
        [$data , $message] = $this->data($request);
        if($data){
            $letras = MyUtility::letras($data->costo);
            $result = DB::table('tbl_recibos')->updateOrInsert(
                ['num_recibo' => $data->num_recibo, 'unidad' => $data->ubicacion],
                [ 'importe' => $data->costo, 'importe_letra' => $letras,'status_folio' => 'ASIGNADO',
                   'fecha_status' => date('Y-m-d h:m:s'), 'id_curso' => $data->id, 'folio_grupo' => $data->folio_grupo,
                   'fecha_expedicion' => $request->fecha_expedicion, 'recibio' => $request->recibio,
                   'recibio'=>$request->recibio,
                   'recibide'=>$request->recibide,
                   'fecha_expedicion' => $request->fecha,
                   'iduser_updated' => $this->user->id,
                   'created_at'=> date('Y-m-d H:m:s'),
                   'updated_at'=> date('Y-m-d H:m:s'),
                   'folio_recibo' => $data->uc.'-'.$data->num_recibo,
                   'status_recibo' => $request->status_recibo
                ]
            );
            if($request) $message["ALERT"] = "NÚMERO DE RECIBO ASIGNADO CORRECTAMENTE!!";
            else $message["ERROR"] = "NO SE REALIZÓ LA ASIGNACIÓN, POR FAVOR INTENTE DE NUEVO.";
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
                   'updated_at'=> date('Y-m-d H:m:s')
                ]
            );
            if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";
            else $message["ERROR"] = "LA OPERACIÓN NO SE EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    

   

    public function enviar(Request $request) {         
        [$data , $message] = $this->data_validate($request);
        if($data){            
            $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->where('status_folio','CARGADO')->update(                
                [ 'status_folio' => 'ENVIADO','fecha_status' => date('Y-m-d H:m:s'),                    
                   'iduser_updated' => $this->user->id, 'updated_at'=> date('Y-m-d H:m:s')
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
            $data = DB::table('tbl_recibos as tr')->join('tbl_cursos as tc','tr.folio_grupo','=','tc.folio_grupo')
                ->where('tr.folio_grupo',$request->folio_grupo)->whereNotIn('tr.status_recibo',['CANCELADO'])->first();     
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
                    'tr.fecha_expedicion','tr.recibio','tu.direccion','tu.delegado_administrativo','tr.id as id_recibo',
                    'tr.importe_letra', 'tr.folio_recibo', 'tr.status_recibo','tc.arc','tc.clave','tr.observaciones',
                    DB::raw(" 
                        CASE
                            WHEN tc.comprobante_pago <> 'null' THEN concat('uploadFiles',tc.comprobante_pago)
                            WHEN tr.file_pdf <> 'null' THEN tr.file_pdf
                        END as file_pdf"),
                    DB::raw('UPPER(tu.municipio) as municipio'),
                    DB::raw("
                        CASE 
                            WHEN tc.tipo = 'EXO' THEN 'EXONERACIÓN'
                            WHEN tc.tipo = 'PINS' THEN 'ORDINARIO'
                            WHEN tc.tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA' 
                        END as tpago"),                       
                    DB::raw("LEFT(tu.ubicacion,2) as uc"), 'tc.id',
                    DB::raw("CASE 
                        WHEN tc.clave ='0' and tc.status_curso IS NULL THEN 'EN TRAMITE EN LA UNIDAD' 
                        WHEN tc.clave ='0' and tc.status_curso IS NOT NULL THEN 'EN TRAMITE EN LA DTA'                     
                        ELSE tc.clave
                        END as status_clave"),                    
                    DB::raw("(
                        CASE
                            WHEN tc.comprobante_pago IS NOT NULL THEN (regexp_match(tc.folio_pago, '[0-9]+'))[1]::INTEGER 
                            WHEN  tr.status_folio is not null THEN tr.num_recibo 
                            WHEN max.status_folio is null THEN (SELECT min(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status_folio is null)
                            ELSE max.num_recibo+1
                        END) as num_recibo"),

                    DB::raw("(
                        CASE
                            WHEN tc.comprobante_pago IS NOT NULL  THEN 'IMPRENTA'
                            WHEN tr.status_folio is null THEN 'DISPONIBLE'
                            ELSE  tr.status_folio
                        END) as status_folio"),                    
                    DB::raw("(
                        CASE
                            WHEN  tr.num_recibo = (SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status_folio not in( null,'CANCELADO','ENVIADO','SOPORTE')) THEN true                        
                            ELSE false
                        END) as deshacer")
                )
                ->where('tc.folio_grupo',$request->folio_grupo)
                ->wherein('tc.unidad',$this->unidades)
                ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')
                ->leftjoin('tbl_recibos as tr', function ($join) {                    
                    $join->on('tr.folio_grupo','=','tc.folio_grupo')
                    ->whereNotIn('tr.status_folio',['CANCELADO']); 
                })
                ->join('tbl_recibos as max', function ($join) {
                        $join->on('max.unidad', '=', 'tu.ubicacion')                    
                        ->where('max.num_recibo', '=', DB::raw("(SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and ( status_folio!='CANCELADO' or status_folio is null ))")); 
                })
                ->first();                     
                $_SESSION['data'] = $data;
                if(!$data) $message["ERROR"]= "EL FOLIO NO ENCONTRATO.";           
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
            $file_name = "Recibo_".$data->folio_recibo.'.pdf';
            
            $pdf = PDF::loadView('grupos.recibos.pdfRecibo',compact('data','distintivo','direccion','fecha'));
            
            $pdf->setpaper('letter','portrait');      
        
            return $pdf->stream($file_name, ['Content-Type' => 'application/pdf']);
        }else return "ACCIÓN INVÁlIDA";exit;
    }

    public function aceptar(Request $request){ 
        [$data , $message] = $this->data_validate($request); 
        if($data){
            $message["ERROR"] = "LA OPERACIÓN NO SE HA EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
            switch($request->movimiento){
                case "ESTATUS": //CAMBIAR ESTATUS
                    $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                        [  'status_recibo'=>$request->status_recibo,
                           'iduser_updated' => $this->user->id,
                           'updated_at'=> date('Y-m-d H:i:s')
                        ]
                    );
                    if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";                    
                break;
                case "DESHACER": //DESHACER ASIGNACION DE FOLIO                    
                    $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->whereNotIn('status_folio', ['ENVIADO','DISPONIBLE'])->update(                
                        [ 
                            'importe' => 0, 'importe_letra' =>null,'status_folio' => null,
                            'fecha_status' => null, 'id_curso' => null, 'folio_grupo' => null,
                            'fecha_expedicion' => null, 'recibio' => null,                   
                            'recibio'=> null,
                            'recibide'=>null,
                            'fecha_expedicion' => null,
                            'file_pdf' => null,
                            'folio_recibo' => null,
                            'iduser_updated' => $this->user->id,
                            'updated_at'=> date('Y-m-d H:i:s')               
                        ]
                    );
                    if($result){                        
                        if(Storage::exists($data->file_pdf)){
                             Storage::delete($data->file_pdf);
                             $message["ALERT"] = "LA ASIGNACIÓN Y EL ARCHIVO HAN SIDO ELIMINADOS!!";  
                        }else $message["ALERT"] = "LA ASIGNACIÓN HA SIDO ELIMINADA!!";  
                    }                   
                break;
                case "SUBIR": //SUBIR PDF FIRMADO
                    if ($request->hasFile('file_recibo')) {    
                        $anio = date('Y', strtotime($data->inicio));
                        $name_file = $data->folio_recibo."_".date('ymdHis')."_". $this->user->id.".pdf";                                
                        $path = $anio.$this->path.$data->id."/"; //2023/expendientes/id/
                        $file = $request->file('file_recibo'); 
                        $file_result = MyUtility::upload_file($path,$file,$name_file,$data->file_pdf); //dd($file_result);
                        $url_file = $file_result["url_file"];                 
                        if($file_result['up']){                       
                            if($data){                        
                                $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                                    [  'status_folio' => 'CARGADO',
                                    'fecha_status' => date('Y-m-d H:m:s'),
                                    'iduser_updated' => $this->user->id,
                                    'updated_at'=> date('Y-m-d H:m:s'),
                                    'file_pdf' => $url_file.$name_file
                                    ]
                                );
                                if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";                                
                            }        
                        }                  
                    }else $message["ERROR"] = "POR FAVOR SELECCIONE EL ARCHIVO.";
                break;
                case "SOPORTE": //SOLICITAR CAMBIO DE SOPORTE
                    $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                        [  'status_folio'=> 'SOPORTE',
                           'fecha_status'=> date('Y-m-d H:i:s'),
                           'motivo' => $request->motivo,
                           'iduser_updated' => $this->user->id,
                           'updated_at'=> date('Y-m-d H:m:s')                           
                        ]
                    );
                    if($request) $message["ALERT"] = "SOLICITUD ENVIADA CORRECTAMENTE!!";                      
                break;
                case "DENEGADO": //SOLICITAR CAMBIO DE SOPORTE
                    $result = DB::table('tbl_recibos')->where('folio_grupo',$data->folio_grupo)->update(                
                        [   'status_folio'=> 'ENVIADO',                            
                            'fecha_status'=> date('Y-m-d H:i:s'),
                            'iduser_updated' => $this->user->id,
                            'updated_at'=> date('Y-m-d H:m:s')                             
                        ]
                    );
                    if($request) $message["ALERT"] = "SOLICITUD ENVIADA CORRECTAMENTE!!";                      
                break;
            }            
        }
        //dd($result);
        if(isset($message["ALERT"])) $message["ERROR"] = null;
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

}