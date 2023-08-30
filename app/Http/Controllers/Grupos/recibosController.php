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

class recibosController extends Controller
{   
    function __construct() {   
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
                   'unidad' => $data->unidad
                ]
            );
            if($request) $message["ALERT"] = "NÚMERO DE RECIBO ASIGNADO CORRECTAMENTE!!";
            else $message["ERROR"] = "NO SE REALIZÓ LA ASIGNACIÓN, INTENTE DE NUEVO.";
        }
        //dd($result);
        return redirect('grupos/recibos/index')->with(['message'=>$message, 'folio_grupo'=>$data->folio_grupo]);
    }

    private function data(Request $request){        
        $data = $message = [];
        if($request->folio_grupo){

            $data = DB::table('tbl_cursos as tc')  
                ->select('tc.id','tc.folio_grupo','tc.unidad','tu.ubicacion', 'tc.clave','tc.curso','tc.nombre','tc.status_curso','tc.inicio', 'tc.termino', 'tc.hini', 'tc.hfin',
                    'tc.costo','tc.hombre','tc.mujer',
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
                //dd($data);
                if(!$data) $message["ERROR"]= "EL FOLIO INGRESADO NO CORRESPONDE A LA UNIDAD $this->ubicacion.";                
        }elseif($request->BUSCAR) $message["ERROR"] = "INGRESE EL FOLIO DE GRUPO PARA EJECUTAR LA BUSQUEDA";

        return [$data, $message];
    }


}