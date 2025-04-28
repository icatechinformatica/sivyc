<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use App\Utilities\MyUtility;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class consultasController extends Controller
{
    function __construct() {
        session_start();
        $this->anios = MyUtility::ejercicios();
    }

    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');
        $_SESSION['unidades']=NULL;
        
        if($rol){
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');
            if(count($unidades)==0) $unidades =[$unidad];
            $_SESSION['unidades'] = $unidades;
        }
        
        $clave = $request->clave;
        $data = $message = NULL;
        $is_string =  preg_match("/^[a-z\s]+$/i", $clave);
        if($request->ejercicio) $ejercicio = $request->ejercicio;
        else $ejercicio = date("Y");

        $data = DB::table('tbl_cursos')->whereYear('inicio',$ejercicio)->where('turnado','<>','VINCULACION')
        ->select('*', 
            DB::raw("
                CASE 
                    WHEN status IN ('EN_FIRMA') THEN 'NO REPORTADO'                
                    WHEN status IN ('REVISION_DTA','TURNADO_DTA') THEN 'REPORTADO'                
                ELSE status
                END as status"),
            DB::raw("
                CASE
                    WHEN vb_dg = false AND turnado IN ('UNIDAD') AND clave = '0' THEN 'PENDIENTE' 
                    WHEN vb_dg = true AND turnado = 'UNIDAD'  THEN 'APROBADO' 
                    WHEN turnado = 'PLANEACION_TERMINADO' THEN 'PLANEACION'
                    WHEN turnado IN ('REVISION_DTA', 'TURNADO_DTA') THEN 'DTA'            
                    WHEN turnado = 'MEMO_TURNADO_RETORNO' THEN 'UNIDAD'            
                    ELSE turnado
                END as turnado")
        );
        if($clave) $data = $data->where(DB::raw("CONCAT(folio_grupo, clave, nombre)"),'like','%'.$clave.'%');            
        if($_SESSION['unidades'])$data = $data->whereIn('unidad',$_SESSION['unidades']);
            $data = $data->orderby('folio_grupo','DESC')->paginate(15);
        
        if(!$data) $message = "Clave inválida.";
        $anios = $this->anios;
        return view('grupos.consultas.index', compact('message','data','anios','ejercicio'));
    }

    public function calificaciones(Request $request){
        $clave = $request->clave;
        $message = NULL;
        return redirect('grupos/calificaciones/buscar')->with(['message'=>$message, 'clave'=>$clave]);
    }

    public function folios(Request $request){
        $clave = $request->clave;
        return redirect('grupos/asignarfolios')->with(['clave'=>$clave]);
    }

    public function cancelarfolios(Request $request){
        $clave = $request->clave;
        return redirect('grupos/cancelacionfolios')->with(['clave'=>$clave]);
    }
}
