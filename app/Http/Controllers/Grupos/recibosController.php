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

use setasign\Fpdi\Fpdi;
use \setasign\Fpdi\PdfParser\StreamReader;

class recibosController extends Controller
{   
    function __construct(Request $request) {  
        session_start(); 
        $this->middleware('auth');
        $this->path = "/expedientes/";
        $this->path_files = env("APP_URL").'/storage/';
        $this->path_files_cancelled = env("APP_URL").'/grupos/recibo/descargar?folio_recibo=';
        $this->key = "MGSERA";
        //$this->id_recibo = $this->decryptData($request->id_recibo, $this->key);
        
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
        if(session('id_concepto'))$request->id_concepto = session('id_concepto');  
        if(!$request->id_concepto) $request->id_concepto = 1;        
        
        [$data , $message] = $this->data($request);
        if(session('message')) $message = session('message');
        if($data){            
            if($data->deshacer)$movimientos = [ 'SUBIR' => 'SUBIR ARCHIVO PDF', 'ESTATUS'=>'CAMBIO DE ESTATUS', 'DESHACER'=>'DESHACER ASIGNACION'];
            elseif((!$data->status_curso and !in_array($data->status_folio, ['DISPONIBLE','IMPRENTA'])) OR in_array($data->status_folio,['ACEPTADO', 'CARGADO','ASIGNADO'])) $movimientos = [ 'SUBIR' => 'SUBIR ARCHIVO PDF', 'ESTATUS'=>'CAMBIO DE ESTATUS'];            
            
            if($data->status_folio=="ENVIADO" and $data->status_curso=='CANCELADO') $movimientos = [ 'CANCELAR' => 'CANCELAR'];
            elseif($data->status_folio=="ENVIADO" and ($data->status_curso OR $data->id_concepto>1)) $movimientos = [ 'SOPORTE' => 'SOLICITUD DE CAMBIO SOPORTES'];
        }     
        $path_files = $this->path_files;
        $conceptos = DB::table('cat_conceptos')->WHERE('tipo','CUOTA')->WHERE('activo',true)->ORDERBY('id')->pluck('concepto','id'); 
        if($request->id_concepto==1)
            $status_recibo = ["PAGADO"=>"PAGADO", "POR COBRAR"=>"POR COBRAR"];
        else
            $status_recibo = ["PAGADO"=>"PAGADO"];
        return  view('grupos.recibos.index', compact('message','data', 'request','movimientos','path_files','conceptos','status_recibo')); 
    } 
    
    public function buscar(Request $request){// dd($request->id_concepto);
        $data = $message = [];
        if(!$request->ejercicio)$request->ejercicio = date('Y');        
        if(!$request->id_concepto) $request->id_concepto = 1;
        
        switch($request->id_concepto){
            case 1: /// PAGO DE CURSO
                if(!$request->status and !$request->folio_grupo) $request->status = "PENDIENTE";
                $data = DB::table('tbl_cursos as tc')             
                    ->select('tc.id as id_curso','tc.curso','tc.nombre','tc.hombre','tc.mujer', 'tc.costo','tc.inicio','tc.termino','tc.hini','tc.hfin','tc.folio_grupo','tr.status_recibo',
                        DB::raw('COALESCE(tr.id_concepto,1) as id_concepto'),'tr.id as id_recibo',
                        DB::raw("CASE 
                            WHEN tc.clave ='0' and tc.status_curso IS NULL THEN 'EN TRAMITE UNIDAD' 
                            WHEN tc.clave ='0' and tc.status_curso IS NOT NULL  and tc.status_curso !='CANCELADO' THEN 'EN TRAMITE DTA' 
                            WHEN tc.clave ='0' and tc.status_curso ='CANCELADO' THEN 'CANCELADO' 
                            ELSE tc.clave
                            END as clave"),
                        DB::raw("CASE                             
                            WHEN tr.status_folio='CANCELADO' THEN 'CANCELADO' 
                            WHEN tr.status_folio='ENVIADO' OR  tc.comprobante_pago <> 'null' THEN 'ENVIADO'
                            WHEN tr.status_folio='SOPORTE' THEN 'CAMBIO DE SOPORTE'
                            WHEN tr.status_folio='ACEPTADO' THEN 'CAMBIO ACEPTADO'
                            WHEN tr.status_folio='DENEGADO' THEN 'CAMBIO DENEGADO'
                            WHEN tr.folio_grupo <>'null' THEN 'ASIGNADO'
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
                            WHEN tr.status_folio='CANCELADO' THEN concat('".$this->path_files_cancelled."',tr.folio_recibo)
                            WHEN tc.comprobante_pago <> 'null' THEN concat('".$this->path_files."uploadFiles',tc.comprobante_pago)
                            WHEN tr.file_pdf <> 'null' THEN concat('".$this->path_files."',tr.file_pdf)
                        END as file_pdf"),                                            
                        DB::raw("(
                            CASE
                                WHEN tr.status_folio IS NOT NULL AND tr.status_folio<>'ENVIADO' THEN true                                        
                            ELSE false
                            END) as editar"),
                        'tu.unidad','tr.id');         
                $data = $data->wherein('tc.unidad',$this->unidades);
                if($request->folio_grupo){
                    $data = $data->where(DB::raw('CONCAT(folio_recibo,tc.folio_grupo,tc.clave)'), 'ILIKE', '%'.$request->folio_grupo.'%');
                    $request->unidad = $request->status = null;
                }else{ 
                    $data = $data->whereYear('tc.inicio', $request->ejercicio);
                    if($request->unidad) $data = $data->where('tc.unidad', $request->unidad); 

                    if($request->status){
                        switch($request->status){    
                            case "PENDIENTE":
                                $data = $data->where('tc.status_curso', NULL)->whereNull('tc.comprobante_pago')
                                    ->whereNotExists(function ($query) {
                                        $query->from('tbl_recibos')->whereRaw('tc.folio_grupo = tr.folio_grupo'); 
                                    })->orderby('tc.folio_grupo','ASC');
                            break;                           
                            case "ENVIADO":
                                $data = $data->where(function ($query) use($request) {
                                    $query->whereNotNull('tc.comprobante_pago')
                                        ->orWhereNotNull('tr.folio_grupo')->where('tr.status_folio', $request->status);
                                })->orderby('tc.folio_grupo','DESC');                            
                            break; 
                            case "POR COBRAR": case "PAGADO":
                                $data = $data->where('tr.status_recibo', $request->status);                            
                            break; 
                            default: ///ASIGNADOS, CANCELADOS
                                $data = $data->where('tr.status_folio', $request->status);              
                            break;                                           
                        }                    
                    }
                }            
                $data = $data->where('tc.tipo','!=','EXO');
                $data = $data->leftjoin('tbl_recibos as tr', function ($join) {                    
                        $join->on('tc.folio_grupo','=','tr.folio_grupo')->where('tr.id_concepto','1'); 
                })
                ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')            
                ->paginate(15); 
            break;
            default: 
                if($request->status == "PENDIENTE") $request->status = null;
                $data = DB::table('tbl_recibos as tr')->where('id_concepto','>',1)->join('cat_conceptos as cc','cc.id','=','tr.id_concepto')
                    ->select('tr.*','cc.concepto','tr.id as id_recibo',
                    DB::raw("(
                        CASE
                            WHEN tr.status_folio IS NOT NULL AND tr.status_folio<>'ENVIADO' THEN true                                        
                        ELSE false
                        END) as editar"),
                        DB::raw(" 
                        CASE
                            WHEN tr.status_folio='CANCELADO' THEN concat('".$this->path_files_cancelled."',tr.folio_recibo)                            
                            WHEN tr.file_pdf <> 'null' THEN concat('".$this->path_files."',tr.file_pdf)
                        END as file_pdf"),
                    );                                        
                if($request->folio_grupo){
                    $data = $data->where(DB::raw('CONCAT(tr.id,tr.folio_recibo,tr.folio_grupo)'), 'ILIKE', '%'.$request->folio_grupo.'%');                        
                }
                if($request->status){
                    switch($request->status){                                                   
                        case "POR COBRAR":  case "PAGADO":
                            $data = $data->where('tr.status_recibo', $request->status);
                        break;                                  
                        default:
                            $data = $data->where('tr.status_folio', $request->status);              
                        break;
                    }                    
                }
                $data = $data->join('tbl_unidades as tu','tr.unidad', '=', 'tu.unidad') ->wherein('tu.unidad',$this->unidades)
                    ->where('tr.id_concepto',$request->id_concepto)->paginate(15); 
            break;
        }

        $data->appends($request->except('page'));     //dd($data);
        $path_files = $this->path_files;
        $anios = MyUtility::ejercicios();
        $unidades = $this->unidades; 
        $conceptos = DB::table('cat_conceptos')->WHERE('tipo','CUOTA')->WHERE('activo',true)->ORDERBY('id')->pluck('concepto','id'); 
            
        return  view('grupos.recibos.buscar', compact('message','data','request','path_files','anios','unidades','conceptos')); 
    }

    public function asignar(Request $request) { 
        if($request->id_concepto>1) $request->ID = "NUEVO";
        [$data , $message] = $this->data($request);// dd($data);
        if($data){            
            switch($request->id_concepto){
                case 1: /// PAGO DE CURSO 
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
                        'status_recibo' => $request->status_recibo,
                        'id_concepto' => 1
                        ]
                    );
                break;      
                default:
                    $request->importe = MyUtility::numerico($request->importe);
                    $letras = MyUtility::letras($request->importe);
                    $message = $this->data_validate($data);                    
                    $result = DB::table('tbl_recibos')->updateOrInsert(
                        ['num_recibo' => $data->num_recibo, 'unidad' => $data->ubicacion],
                        [ 
                        'constancias' => $request->constancias,'cantidad' => $request->cantidad,
                        'importe' => $request->importe, 'descripcion' => $request->descripcion,
                        'importe_letra' => $letras,'status_folio' => 'ASIGNADO',
                        'fecha_status' => date('Y-m-d h:m:s'), 'id_curso' => $data->id, 'folio_grupo' => $data->folio_grupo,
                        'fecha_expedicion' => $request->fecha_expedicion, 'recibio' => $request->recibio,
                        'recibio'=>$request->recibio,
                        'recibide'=>$request->recibide,
                        'fecha_expedicion' => $request->fecha,
                        'iduser_updated' => $this->user->id,
                        'created_at'=> date('Y-m-d H:m:s'),
                        'updated_at'=> date('Y-m-d H:m:s'),
                        'folio_recibo' => $data->uc.'-'.$data->num_recibo,
                        'status_recibo' => $request->status_recibo,
                        'id_concepto' => $request->id_concepto,
                        'id_curso' => $data->id_curso ?? null,
                        'matricula' => $request->matricula ?? null,                        
                        'cantidad' => $request->cantidad ?? null
                        ]
                    );

                    if($result AND $request->constancias){
                        $id_recibo = DB::table('tbl_recibos')->where('num_recibo', $data->num_recibo)->where('unidad', $data->ubicacion)->value('id');
                         $this->update_tbl_folios_idrecibo($request->constancias, $id_recibo); 
                    }
                    if(!$request->folio_grupo) $request->folio_grupo = $data->uc.'-'.$data->num_recibo;
                break;
            }
            if($request) $message["ALERT"] = "NÚMERO DE RECIBO ASIGNADO CORRECTAMENTE!!";
            else $message["ERROR"] = "NO SE REALIZÓ LA ASIGNACIÓN, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$request->folio_grupo, 'id_concepto'=>$request->id_concepto]);
    }    

    public function modificar(Request $request) {         
        [$data , $message] = $this->recibo_validate($request->id_recibo);//dd($data);
        $result = $depositos= $suma_importe = null;
        if($data){     
            
            //CONSTRUYENDO JSON APARTIR DE DATOS DE LOS COMPROBANTES DE PAGOS
            if($request->folio_deposito[1] AND $request->importe_deposito[1] AND $request->fecha_deposito[1] AND $request->status_recibo=="PAGADO"){
                $depositos = array_filter( 
                    array_map(
                        function ($clave, $valor1, $valor2) {
                            if($clave) return ['folio' =>$clave, 'importe' => $valor1, 'fecha' => $valor2];
                        }, $request->folio_deposito, $request->importe_deposito, $request->fecha_deposito                        
                    ),
                    function ($element) {
                    return $element !== null;
                    }
                );

                $suma_importe = array_reduce(
                    $depositos,
                    function ($carry, $item) {
                        if( $item['importe'])return $carry + $item['importe'];
                    },
                    0
                );                
                $depositos = json_encode($depositos); 

                //dd($suma_importe);
            }else $message["ERROR"] = "ES NECESARIO INGRESAR LOS DATOS DE LOS COMPROBANTES DE DEPÓSITOS.";
            
            //TIPOS DE CONCEPTOS
            switch($request->id_concepto){
                case 1: /// PAGO DE CURSO
                    $importe = MyUtility::numerico($request->precio_unitario);
                    $letras = MyUtility::letras($request->precio_unitario);// dd($letras);
                    $result = DB::table('tbl_recibos')->where('id',$data->id)->update(                
                        [  'importe' => $importe, 
                            'importe_letra' => $letras,
                            'recibio'=>$request->recibio,
                            'recibide'=>$request->recibide,
                            'fecha_expedicion' => $request->fecha,
                            'iduser_updated' => $this->user->id,
                            'updated_at'=> date('Y-m-d H:m:s'),
                            'depositos' => $depositos ?? null
                        ]);                    
                break;
                default: 
                    [$data , $message] = $this->data($request); //dd($data);                        
                    $message = $this->data_validate($data);
                    $request->importe = MyUtility::numerico($request->importe);
                    $letras = MyUtility::letras($request->importe);// dd($request->importe);

                    $result = DB::table('tbl_recibos')->where('id',$data->id_recibo)->where('status_folio','<>','ENVIADO')->update(                
                        [  'id_curso' => $data->id_curso ?? null,
                            'folio_grupo' => $data->folio_grupo ?? null,
                            'matricula' => $request->matricula ?? null,
                            'constancias' =>$request->constancias,
                            'cantidad' => $request->cantidad ?? null,
                            'importe' => $request->importe,
                            'importe_letra' => $letras,
                            'depositos' => $depositos ?? null,
                            'descripcion' => $request->descripcion,
                            'recibio'=>$request->recibio,
                            'recibide'=>$request->recibide,
                            'fecha_expedicion' => $request->fecha,
                            'iduser_updated' => $this->user->id,
                            'updated_at'=> date('Y-m-d H:m:s')
                        ]);
                    if($result and $data->id and $request->constancias){ 
                        $tfolios = $this->update_tbl_folios_idrecibo($request->constancias, $data->id); 
                        if($tfolios!=$request->cantidad) $message["ERROR"] = "LA CANTIDAD ESPECIFICADA NO COINCIDE CON EN TOTAL DE FOLIOS DE CONSTANCIAS (ENCONTRADOS $tfolios), POR FAVOR VERIFIQUE.";
                    }                        
                    if(!$request->folio_grupo) $request->folio_grupo = $data->folio_recibo;                        
                break;
            }
            
            if($suma_importe <> $data->importe) $message["ERROR"] = "LOS IMPORTES DE DEPOSITO NO COINCIDE CON EL TOTAL DEL IMPORTE.";
            elseif($result) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";            
            else $message["ERROR"] = "LA OPERACIÓN NO SE EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$request->folio_grupo, 'id_concepto'=>$data->id_concepto]);
    }   

    public function enviar(Request $request) {         
        if(in_array($request->idconcepto, [2,4]))
            [$data , $message] = $this->data($request); 
        else [$data , $message] = $this->recibo_validate($request->id_recibo); 

        if($data){    
            $message = $this->data_validate($data);      
            if(!$message)  {
                $result = DB::table('tbl_recibos')->where('id',$data->id)->where('status_folio','CARGADO')->update(                
                    [ 'status_folio' => 'ENVIADO','fecha_status' => date('Y-m-d H:m:s'),                    
                    'iduser_updated' => $this->user->id, 'updated_at'=> date('Y-m-d H:m:s')
                    ]
                );
                if($request) $message["ALERT"] = "EL RECIBO DE PAGO HA SIDO ENVIADO CORRECTAMENTE!!";
                else $message["ERROR"] = "EL RECIBO DE PAGO NO SE ENVIADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
            }            
        }        
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$request->folio_grupo, 'id_concepto'=>$data->id_concepto]);
    }

    private function recibo_validate($id_recibo){        
        $data = $message = [];
        if($id_recibo){
            $data = DB::table('tbl_recibos as tr')
            ->where('tr.id',$id_recibo)->whereNotIn('tr.status_recibo',['CANCELADO'])->wherein('tr.unidad',$this->unidades)->first();     
            if(!$data) $message["ERROR"]= "VERIFIQUE EL FOLIO INGRESADO NO CORRESPONDE A LA UNIDAD $this->ubicacion.";
        }
        return [$data, $message];
    }
    
    private function data_validate($data){
        $message = [];
        if(in_array($data->id_concepto, [2,4])){
            if(!$data->alumno)
                $message["ERROR"] = "ALUMNO NO ENCONTRADO!! POR FAVOR, CORROBORE QUE LA MATRICULA O LA CLAVE DE CURSO SEAN CORRECTAS.";
            if($data->calificacion=='NP')
                $message["ERROR"] = "EL ALUMNO NO PRESENTO!! POR FAVOR, CORROBORE QUE LA MATRICULA SEA CORRECTA.";
        }
        return $message;
    }

    private function data(Request $request){//dd($request->ID);
        $data = $message = [];
        if(isset($request->idconcepto) and $request->ID<>"NUEVO") $request->id_concepto = $request->idconcepto;        
        if($request->ID=="NUEVO" and $request->id_concepto==1) $request->ID = null;

        if($request->ID OR $request->folio_grupo){
            switch($request->id_concepto){
                case 1: /// PAGO DE CURSO
                    $data = DB::table('tbl_cursos as tc')  
                    ->select('tc.id as id_curso','tc.folio_grupo','tc.unidad','tu.ubicacion', 'tc.clave','tc.curso','tc.nombre','tc.tipo_curso',
                        'tc.status_curso','tc.inicio', 'tc.termino', 'tc.hini', 'tc.hfin','tc.costo','tc.hombre','tc.mujer','tr.recibide',
                        'tr.fecha_expedicion','tr.recibio','tu.direccion','tu.delegado_administrativo','tr.id as id_recibo',
                        'tr.importe_letra', 'tr.folio_recibo', 'tr.status_recibo','tc.arc','tc.clave','tr.observaciones','tr.importe',
                        DB::raw('1 as id_concepto'),'cc.concepto','tc.costo as precio_unitario', 'tr.depositos', 'tc.munidad',
                        DB::raw(" 
                            CASE
                            WHEN tr.status_folio='CANCELADO' THEN concat('".$this->path_files_cancelled."',tr.folio_recibo)
                            WHEN tc.comprobante_pago <> 'null' THEN concat('".$this->path_files."uploadFiles',tc.comprobante_pago)
                            WHEN tr.file_pdf <> 'null' THEN concat('".$this->path_files."',tr.file_pdf)
                        END as file_pdf"
                        ),
                        DB::raw('UPPER(tc.unidad) as municipio'),
                        DB::raw("
                            CASE 
                                WHEN tc.tipo = 'EXO' THEN 'EXONERACIÓN'
                                WHEN tc.tipo = 'PINS' THEN 'ORDINARIO'
                                WHEN tc.tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA' 
                            END as tpago"),                       
                        DB::raw("LEFT(tu.ubicacion,2) as uc"), 'tc.id',
                        DB::raw("CASE 
                            WHEN tc.clave ='0' and (tc.status_curso IS NULL OR tc.status_curso ='CANCELADO')THEN 'EN TRAMITE EN LA UNIDAD'                             
                            WHEN tc.clave ='0' and tc.status_curso IS NOT NULL  and tc.status_curso !='CANCELADO' THEN 'EN TRAMITE DTA'                             
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
                                WHEN  tr.num_recibo = (SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status_folio is not null) THEN true
                                ELSE false
                            END) as deshacer"),

                        DB::raw("(
                            CASE
                                WHEN tc.status_curso IS NULL AND tr.status_folio IS NOT NULL THEN true                                
                                WHEN  tr.status_folio='ACEPTADO'  THEN true
                                ELSE false
                            END) as editar")
                    );
                    if($request->folio_grupo)
                        $data = $data->where(DB::raw('CONCAT(tr.folio_recibo,tc.folio_grupo)'), 'ILIKE', '%'.$request->folio_grupo.'%'); 
                    else
                        $data =  $data->where('tc.id',$request->ID);

                        $data = $data->wherein('tc.unidad',$this->unidades)
                    ->join('tbl_unidades as tu','tu.unidad', '=', 'tc.unidad')
                    ->leftjoin('tbl_recibos as tr', function ($join) use ($request) {                    
                        $join->on('tr.folio_grupo','=','tc.folio_grupo')
                        ->where('tr.id_concepto','1');
                        //->whereNotIn('tr.status_folio',['CANCELADO']); 
                    })
                    ->join('tbl_recibos as max', function ($join) {
                            $join->on('max.unidad', '=', 'tu.ubicacion')                    
                            ->where('max.num_recibo', '=', DB::raw("(SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and ( status_folio!='CANCELADO' or status_folio is null ))")); 
                    })        
                    ->leftjoin('cat_conceptos as cc','cc.id','=','tr.id_concepto')->first(); //dd($data);
                    if(!$request->folio_grupo) $request->folio_grupo = $data->folio_grupo;
                break;
                default:
                    $data = DB::table('cat_conceptos as cc')  
                        ->select('cc.*','tr.*','tr.id as id_recibo','tu.ubicacion','tu.direccion','tu.delegado_administrativo','tc.clave','tc.curso',
                            'tr.importe as costo','cc.id as id_concepto','cc.importe as precio_unitario', 'tc.id as id_curso','ti.alumno','tc.tipo_curso','ti.calificacion',
                            DB::raw("CASE WHEN tr.importe is null THEN cc.importe ELSE  tr.importe END as importe"),
                            DB::raw("CASE WHEN tr.folio_grupo is null THEN tc.folio_grupo ELSE  tr.folio_grupo END as folio_grupo"),
                            DB::raw("LEFT(tu.ubicacion,2) as uc"),'cc.concepto',
                            DB::raw('UPPER(tu.unidad) as municipio'),DB::raw('null as status_curso'),
                            DB::raw(" 
                                CASE
                                    WHEN tr.status_folio='CANCELADO' THEN concat('".$this->path_files_cancelled."',tr.folio_recibo)                                    
                                    WHEN tr.file_pdf <> 'null' THEN concat('".$this->path_files."',tr.file_pdf)
                                END as file_pdf2"),
                            DB::raw("(
                                CASE                                
                                    WHEN  tr.status_folio is not null THEN tr.num_recibo 
                                    WHEN max.status_folio is null THEN (SELECT min(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status_folio is null)
                                    ELSE max.num_recibo+1
                                END) as num_recibo"), 
                            DB::raw("(
                                CASE                                    
                                    WHEN tr.status_folio is null THEN 'DISPONIBLE'
                                    ELSE  tr.status_folio
                                END) as status_folio"),         
                            DB::raw("(
                                CASE
                                    WHEN  tr.num_recibo = (SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion and status_folio in('ASIGNADO','CARGADO')) THEN true
                                    ELSE false
                                END) as deshacer"),
                            DB::raw("(
                                CASE
                                    WHEN tr.status_folio IS NOT NULL AND tr.status_folio<>'ENVIADO' THEN true
                                    WHEN  tr.status_folio='ACEPTADO'  THEN true                                    
                                    ELSE false
                                END) as editar")
                        )                        
                        ->where('cc.id',$request->id_concepto)
                        ->leftjoin('tbl_recibos as tr', function ($join) use ($request) {                    
                            $join->on('cc.id','=','tr.id_concepto')
                            ->where('tr.id_concepto','>','1')
                            ->whereNotIn('tr.status_folio',['CANCELADO'])                           
                            ->where(function ($query) use ($request) {
                                if($request->folio_grupo)$query->where(DB::raw('CONCAT(tr.folio_recibo, tr.folio_grupo)'), 'ILIKE', '%'.$request->folio_grupo.'%');
                                elseif($request->ID=="NUEVO") $query->orWhere('tr.id', 0);
                                else $query->orWhere('tr.id', $request->ID);
                            });                                                    
                        })
                        ->join('tbl_unidades as tu',function ($join) use($request){
                            if($request->ID=="NUEVO") {                                
                                if($request->unidad) $join->on('tu.unidad', '=', DB::raw("'".$request->unidad."'"));
                                else $join->on('tu.id',  DB::raw($this->user->unidad));
                            }else $join->on('tu.unidad', '=', 'tr.unidad');
                            
                        })->wherein('tu.unidad',$this->unidades)                                                                      
                        ->join('tbl_recibos as max', function ($join) {
                                $join->on('max.unidad', '=', 'tu.ubicacion')                    
                                ->where('max.num_recibo', '=', DB::raw("(SELECT max(num_recibo) FROM tbl_recibos WHERE unidad = tu.ubicacion)")); 
                        })                        
                        ->leftjoin('tbl_cursos as tc', function ($join) use ($request){                             
                            $join->on('tc.id','=','tr.id_curso');
                            if($request->clave)
                                $join->orWhere(DB::raw('CONCAT(tc.folio_grupo,tc.clave)'), 'ILIKE', '%' . $request->clave . '%');
                        }) 
                        ->leftjoin('tbl_inscripcion as ti', function ($join) use ($request){                             
                            $join->on('ti.id_curso','=','tc.id');
                            if($request->matricula)
                                $join->where('ti.matricula',$request->matricula);
                            else
                                $join->on('ti.matricula','tr.matricula');

                            //->where('ti.matricula','tr.matricula');
                            //if($request->clave)
                              //  $join->Where(DB::raw('CONCAT(tc.folio_grupo,tc.clave)'), 'ILIKE', '%' . $request->clave . '%');
                        }) 
                        ->first(); //dd($data);
                        if(!$request->folio_grupo) $request->folio_grupo = $data->folio_recibo;
                        
                break;
            }//dd($data);
            if(!$data) $message["ERROR"]= "EL FOLIO NO ENCONTRATO.";
            else{
                $_SESSION['data'] = $data;
                //if($data->id_recibo) $data->id_recibo = $this->encryptData($data->id_recibo, $this->key); 
                //else  $data->id_recibo = $this->encryptData(0, $this->key); 
                if(!$data->recibide and $request->id_concepto==1) $data->recibide = DB::table('alumnos_registro')->where('folio_grupo',$request->folio_grupo)->value('realizo');                
                if(!$data->fecha_expedicion) $data->fecha_expedicion = date('Y-m-d');
                if(!$data->recibio) $data->recibio = $data->delegado_administrativo;
                if(isset($data->depositos)) $data->depositos = json_decode($data->depositos,true); 
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
        [$data , $message] = $this->recibo_validate($request->id_recibo); 
        if($data){
            $message["ERROR"] = "LA OPERACIÓN NO SE HA EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";
            switch($request->movimiento){
                case "ESTATUS": //CAMBIAR ESTATUS
                    $result = DB::table('tbl_recibos')->where('id',$data->id)->where('id_concepto',1)->update(                
                        [  'status_recibo'=>$request->status_recibo,
                           'iduser_updated' => $this->user->id,
                           'updated_at'=> date('Y-m-d H:i:s')
                        ]
                    );
                    if($request) $message["ALERT"] = "LA OPERACIÓN SE EJECUTADO CORRECTAMENTE!!";                    
                break;
                case "DESHACER": //DESHACER ASIGNACION DE FOLIO   
                    $result = null;
                    [$data , $message] = $this->data($request);// dd($data);
                    if($data->deshacer){
                        $result = DB::table('tbl_recibos')->where('id',$data->id_recibo)->whereIn('status_folio', ['ASIGNADO','CARGADO','ENVIADO'])->update(                                        [ 
                                'importe' => 0, 'importe_letra' =>null,'status_folio' => null,
                                'fecha_status' => null, 'id_curso' => null, 'folio_grupo' => null,
                                'fecha_expedicion' => null, 'recibio' => null,                   
                                'recibio'=> null,
                                'recibide'=>null,
                                'fecha_expedicion' => null,
                                'file_pdf' => null,
                                'folio_recibo' => null,
                                'iduser_updated' => $this->user->id,
                                'updated_at'=> date('Y-m-d H:i:s'),
                                'status_recibo' => null,
                                'motivo' => null,
                                'observaciones' => null,
                                'id_concepto' => null,
                                'descripcion' => null,
                                'matricula' => null,
                                'cantidad' => null,
                                'constancias' => null,
                                'depositos' => null
                            ]
                        );
                    }else $message["ERROR"] = "FOLIO NO VÁLIDO PARA DESHACER LA ASIGNACIÓN, POR FAVOR VERIFIQUE.";
                    if($result){       
                       if($data->id_concepto==3) DB::table('tbl_folios')->where('id_recibo',$data->id_recibo)->update(['id_recibo' => null]);//limpia en caso de DESHACER

                        $request->ID="NUEVO";
                        if(Storage::exists($data->file_pdf)){
                             Storage::delete($data->file_pdf);
                             $message["ALERT"] = "LA ASIGNACIÓN Y EL ARCHIVO HAN SIDO ELIMINADOS!!";  
                        }else $message["ALERT"] = "LA ASIGNACIÓN HA SIDO ELIMINADA!!";  
                    }                   
                break;
                case "SUBIR": //SUBIR PDF FIRMADO
                    if ($request->hasFile('file_recibo')) {    
                        if(isset($data->inicio))$anio = date('Y', strtotime($data->inicio));
                        else $anio = date('Y');
                        $name_file = $data->folio_recibo."_".date('ymdHis')."_". $this->user->id.".pdf";                                
                        $path = $anio.$this->path."/recibos_pago/"; //2023/expendientes/recibos_pago/
                        $file = $request->file('file_recibo'); 
                        $file_result = MyUtility::upload_file($path,$file,$name_file,$data->file_pdf); //dd($file_result);
                        $url_file = $file_result["url_file"];                 
                        if($file_result['up']){                       
                            if($data){                        
                                $result = DB::table('tbl_recibos')->where('id',$data->id)->update(                
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
                    $result = DB::table('tbl_recibos')->where('id',$data->id)->update(                
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
                    $result = DB::table('tbl_recibos')->where('id',$data->id)->update(                
                        [   'status_folio'=> 'ENVIADO',                            
                            'fecha_status'=> date('Y-m-d H:i:s'),
                            'iduser_updated' => $this->user->id,
                            'updated_at'=> date('Y-m-d H:m:s')                             
                        ]
                    );
                    if($request) $message["ALERT"] = "SOLICITUD ENVIADA CORRECTAMENTE!!";                      
                break;
                case "CANCELAR": //CANCELACION POR ARC02                    
                    $result = DB::table('tbl_recibos')->where('id',$data->id)->update(                
                        [  'status_folio'=> 'CANCELADO',
                           'motivo' => $request->motivo,
                           'fecha_status'=> date('Y-m-d H:i:s'),                           
                           'iduser_updated' => $this->user->id,
                           'updated_at'=> date('Y-m-d H:m:s')                           
                        ]
                    );
                    if($request) $message["ALERT"] = "LA CANCELACION HA SIDO ENVIADA CORRECTAMENTE!!";                      
                break;
            }            
        }
        //dd($result);
        
        if(isset($message["ALERT"])) $message["ERROR"] = null;
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$request->folio_grupo , 'id_concepto'=>$data->id_concepto]);
    }

    private function update_tbl_folios_idrecibo($constancias, $id_recibo){
         //IDENTIFICANDO LOS FOLIOS DE CONSTANCIAS, PARA ASIGNAR LOS id_recibos en tlb_folios
         $folios =array_map('trim', explode(",",$constancias));
         $rangos_folios = preg_grep("/-/i", $folios);                        
         $folios = array_diff($folios, $rangos_folios);//separando folios de los rangos

         $condiciones = array();
         foreach ($rangos_folios as $r) {                                
             $f = explode('-', $r);
             $condiciones[] = "folio >= '$f[0]' AND folio <= '$f[1]'";
         }
         
         $id_folios = DB::table('tbl_folios')->wherein('folio',$folios);
         foreach ($rangos_folios as $r) {
             $id_folios->orWhere(function ($query) use ($r) {
                 $f = explode('-', $r);
                 $query->where('folio', '>=', $f[0])
                       ->where('folio', '<=', $f[1]);
             });
         }
         $id_folios = $id_folios->pluck('id','id');

         DB::table('tbl_folios')->where('id_recibo',$id_recibo)->update(['id_recibo' => null]);//limpia en caso de correción
         $result = DB::table('tbl_folios')->wherein('id',$id_folios)->update(['id_recibo' => $id_recibo]);//asigna nuevamente                            

         return count($id_folios);
    }

    
    public function pdfDescargar(Request $request){ //PASAN SOLO PDF CANCELADOS
        //$url = $request->input('folio_recibo'); dd($url);
        //dd($request->query());
        $folio_recibo = $file = null;
        if($request->query('folio_recibo')){
            $folio_recibo = $request->query('folio_recibo'); 
            $row = DB::table('tbl_recibos')->where('folio_recibo',$folio_recibo)->first(['file_pdf','status_folio']);
            if($row){
                $file = $row->file_pdf;
                $status_folio = $row->status_folio;            
                if (file_exists(storage_path('app/public/'.$file))) {               
                    $pdfFile = fopen(storage_path('app/public/'.$file), 'r');        

                    $name_pdf= substr(strrchr($file, "/"), 1);        
                    $name_pdf = substr($name_pdf, 0, strpos($name_pdf, "_"));        
                    $outputFile = 'recibo_'.$name_pdf.'.pdf';
                    if($status_folio == 'CANCELADO')  $watermarkText = "CANCELADO";
                    else $watermarkText = null;

                    $pdf = new Fpdi();        
                    $pageCount = $pdf->setSourceFile($pdfFile);        
                    for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                        $pdf->AddPage('P', array(216, 280)); 
                        $template = $pdf->importPage($pageNumber);                        
                        $pdf->useTemplate($template);                        
                        if($watermarkText){
                            $pdf->SetFont('Arial', 'B', 80);
                            $pdf->SetTextColor(127, 127, 127);
                            $pdf->SetXY(10, 100); 
                            $pdf->Cell(0, 0, $watermarkText, 0, 1, 'C');    
                            }
                    }        
                    return $pdf->Output('I', $outputFile);
                }echo "El archivo no existe en la ruta especificada.";
            } else {
                // El archivo no existe, maneja esta situación de acuerdo a tus necesidades
                echo "El archivo no existe en la ruta especificada.";
            }
        
        }else echo  "OPERACIÓN NO VÁLIDA!!";
    }

}