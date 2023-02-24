<?php

namespace App\Http\Controllers\Preinscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\cat\catUnidades; 
class buscarController extends Controller
{   
    use catUnidades;
    function __construct() {
        session_start();
        $this->ejercicio = date("y");         
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;  
            $this->id_unidad = Auth::user()->unidad;
            
            $this->data = $this->unidades_user('vincula');
            $_SESSION['unidades'] =  $this->data['unidades'];
            if($this->data['slug']=='unidad_vinculacion')$this->activar = true;
            else $this->activar = false;  
            
            return $next($request); 
        });
        
    }
    public function index(Request $request){
        $valor_buscar = $request->valor_buscar;
        
        $data = DB::table('alumnos_registro as ar')->select('ar.folio_grupo','ar.turnado','c.nombre_curso as curso','ar.unidad')->join('cursos as c','ar.id_curso','c.id');
        if($valor_buscar) $data = $data->where(DB::raw("CONCAT(ar.folio_grupo,c.nombre_curso)"),'like','%'.$valor_buscar.'%');
        
        if($this->data['slug']=='vinculadores_administrativo')$data = $data->where('ar.iduser_created',$this->id_user);
        if($_SESSION['unidades']) $data = $data->whereIn('ar.unidad',$_SESSION['unidades']);
        $data = $data->where('folio_grupo','<>',null)->groupby('ar.folio_grupo','ar.turnado','c.nombre_curso','ar.unidad')->paginate(15);        
        
        $activar = $this->activar;
        return view('preinscripcion.buscar.index',compact('data','activar'));        

    }  

    public function show(Request $request){
        $_SESSION['folio_grupo'] = $request->folio_grupo;
        return redirect()->route('preinscripcion.grupo');
    }    
    
}