<?php
namespace App\Http\Controllers\Solicitudes;
use App\Http\Controllers\Controller;
use App\Utilities\MyUtility;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class vbgruposController extends Controller
{
    function __construct() {
        
    }

    public function index(Request $request){           
        list($data, $status, $message) = $this->data($request);
        $estatus = ['PENDIENTES' => 'PENDIENTES', 'AUTORIZADOS' => 'AUTORIZADOS'];
        return view('solicitudes.vbgrupos.index', compact('message','data','estatus','status'));
    }

    private function data(Request $request){
        $data = $message = NULL;
        $clave = $request->clave;        
        if($request->estatus) $status = $request->estatus;
        else $status = "PENDIENTES";
        $ejercicio = date("Y");
        
        $data = DB::table('tbl_cursos')->where('clave','0')->whereYear('inicio',$ejercicio);
        if($status == "PENDIENTES") $data = $data->where('vb_dg', false);
        elseif($status == "AUTORIZADOS") $data = $data->where('vb_dg', true);

        if($clave) $data = $data->where(DB::raw("CONCAT(nombre,curso,unidad)"),'like','%'.$clave.'%');        
        //$data = $data->first();
        $data = $data->orderby('inicio','DESC')->paginate(15);

        if(!$data) $message = "No se encontraron registros.";
        return [$data, $status, $message, $clave];
    }

    public function vistobueno(Request $request){
        if($request->id and $request->estado){
            $id = $request->id;
            //$estado = $request->estado;
            
            if($request->estado == "true") $estado = true;
            else $estado = false;
            
            if (is_numeric($id)){
                $result =  DB::table('tbl_cursos')->where('id',$id)->update(['vb_dg' => $estado]);
                if($result){
                    if($estado == "true") $msg = "ACTIVADO";
                    else $msg = "DESACTIVADO";
                }else $msg = "Actualizar la página con F5 y por favor, vuelver a intentar.";
            }        
        }else{
            $msg = "Operación no valida.";
        }        
        return $msg;        
    }

    public function autodata(Request $request){        
        list($data, $status, $message, $clave) = $this->data($request);
        if($data){
            $filas = $checked = "";
            foreach ($data as $item){        
                if($item->vb_dg==true) $checked = 'checked';
                else $checked = '';

                $filas .= "
                    <tr>
                        <td class='text-center'>
                            <div class='form-check'>                                
                                <input class='form-check-input' type='checkbox' value='".$item->id."' name='activo_curso'   onchange='cambia_estado(".$item->id.",$(this))' $checked>
                            </div>
                        </td>                            
                        <td>".$item->curso."</td>
                        <td>".$item->nombre."</td>
                        <td>".$item->inicio."</td>
                        <td>".$item->termino."</td>
                        <td>".$item->unidad."</td>
                    </tr>
                ";
            }
        } else $filas = "Dato no encontrado, por favor intente de nuevo.";
        return $filas;        
    }   
}