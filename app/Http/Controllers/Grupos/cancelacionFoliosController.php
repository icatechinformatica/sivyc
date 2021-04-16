<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class cancelacionfoliosController extends Controller
{   
    function __construct() {
        session_start();
        $this->motivo = ['NO SOLICITADO'=>'NO SOLICITADO','ERROR MECANOGRAFICO'=>'ERROR MECANOGRAFICO','ROBO O EXTRAVIO'=>'ROBO O EXTRAVIO','DETERIORO'=>'DETERIORO'];
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = $data = NULL;
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
        if(session('clave')) $clave = session('clave');
        else{
             $clave = $request->clave;
             
        }
        if($clave){
                
            $data = DB::table('tbl_inscripcion as i')
                ->select('i.id as id_inscripcion','i.matricula','i.alumno','i.reexpedicion',
                'f.id as id_folio','f.folio','f.fecha_expedicion','f.movimiento','f.motivo','c.unidad','c.clave','c.curso')
                ->leftJoin('tbl_folios as f', function($join){                                        
                        $join->on('f.id_curso', '=', 'i.id_curso');
                        $join->on('f.matricula', '=', 'i.matricula');                            
                    }) 
                //->LEFTJOIN('tbl_folios as f', 'f.id', '=', 'i.id_folio')
                ->LEFTJOIN('tbl_cursos as c', 'c.id', '=', 'i.id_curso')
                ->where('c.clave',$clave);
                if($request->matricula) $data = $data->where('i.matricula',$request->matricula);
                
                $data =$data->orderby('i.alumno','ASC')->get();
                
            if(!$data) $message= "No hay folios asignados.";
            else $_SESSION['clave'] = $clave;  
        } //else $message = "Clave del curso requerido para la cancelación";

        $motivo = $this->motivo;
        return view('grupos.cancelacionfolios.index', compact('message','data', 'unidades', 'motivo', 'clave'));
    }  
    
   
    public function store(Request $request){
        $clave = $_SESSION['clave'];
        $ids = $request->ids; //var_dump($ids);exit;
        $message = NULL;
        
        if($ids){
            $cancelar = DB::table('tbl_inscripcion as i')
            ->select('f.id')
            ->LEFTJOIN('tbl_folios as f', 'f.id', '=', 'i.id_folio')
            ->LEFTJOIN('tbl_cursos as c', 'c.id', '=', 'i.id_curso')
            ->where('c.clave',$clave)
            ->wherein('f.id',$ids)->pluck('f.id');
            
            //var_dump($cancelar);exit;
            if(count($cancelar)>0){
                 $result = DB::table('tbl_folios')->wherein('id',$cancelar)->update(
                    ['movimiento' => 'CANCELADO', 'motivo' => $request->motivo, 'iduser_updated' => Auth::user()->id, 'realizo'=>Auth::user()->name ]
                 );                        
                 if($result) $message = "Operación exitosa!! el registro ha sido guardado correctamente.";
            }else $message = "No existen folios que cancelar.";
        }
        return redirect('/grupos/cancelacionfolios')->with(['message'=>$message, 'clave'=>$clave]);
    }
}