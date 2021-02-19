<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class foliosController extends Controller
{   
    function __construct() {
        session_start();
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = NULL;
        if(session('message')) $message = session('message');
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad'.'id');
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;              
        }
        
        if(!$unidades ){
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','id');
            $_SESSION['unidades'] = $unidades;  
        }
        $data = DB::table('tbl_afolios');
            if($request->num_acta) $data = $data->where('num_acta','like','%'.$request->num_acta.'%');
            //if($_SESSION['unidades']) $data = $data->wherein('num_acta','like','%'.$request->num_acta.'%');
            $data =$data->orderby('id','DESC')->paginate(15);        
        return view('solicitudes.folios.index', compact('message','data', 'unidades'));     
    }  
    
   
    public function store(Request $request){
        
        $unidades = json_decode(json_encode($_SESSION['unidades']), true);
        $unidades = array_flip($unidades);
        $unidad = array_search($request->id_unidad, $unidades);
        $num_inicio = substr($request->finicial,1, strlen($request->finicial))*1;
        $num_fin = substr($request->ffinal,1, strlen($request->ffinal))*1;

       $result = DB::table('tbl_afolios')->insertOrIgnore(
            ['unidad' => $unidad, 'finicial' => $request->finicial, 'ffinal' => $request->ffinal, 'total' => $request->total,
            'mod' => $request->mod, 'facta'=> $request->facta, 'num_inicio' => $num_inicio, 'num_fin' => $num_fin,
            'id_unidad' => $request->id_unidad, 'contador' =>  $num_inicio, 'num_acta' => $request->num_acta,
            'activo' => $request->publicar, 'iduser_created' => Auth::user()->id ]
        );
        if($result) $message = "Operación exitosa!! el registro ha sido guardado correctamente.";
        else $message = "Operación inválida, es probable que exista el registro, por favor corrobore.";
        return redirect('/solicitudes/folios')->with(['message'=>$message]);
    }
}