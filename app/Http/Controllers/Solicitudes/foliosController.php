<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class foliosController extends Controller
{   
    function __construct() {
        session_start();
        $this->path_pdf = "/DTA/solicitud_folios/";        
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
    }
    
    public function index(Request $request){
        $message = NULL;
        if(session('message')) $message = session('message');
        
        if($request->num_acta) $valor = $request->num_acta;
        elseif(session('valor')) $valor = session('valor'); 
        else $valor = null;
                
        $data = DB::table('tbl_banco_folios');
        if($valor){            
            if(date('Y-m-d', strtotime($valor)) == $valor) $data = $data->where('facta',$valor);
            elseif(ctype_alpha(str_replace(' ', '', $valor))) $data = $data->where('unidad','like','%'.$valor.'%');
            else $data = $data->where('num_acta','like','%'.$valor.'%');
             $_SESSION['valor'] = $valor;
        }            
        $data =$data->orderby('id','DESC')->paginate(15);
        $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','id');
         
        $path_file = $this->path_files;        
        return view('solicitudes.folios.index', compact('message','data', 'unidades', 'path_file','valor'));     
    }  
    
    public function edit(Request $request){
        $request->id;
        $json = DB::table('tbl_banco_folios')->select('id','id_unidad','mod', 'num_inicio','num_fin','num_acta','facta','activo')->where('id',$request->id)->first();
        $json = json_decode(json_encode($json), true);
        return $json;
    }

    public function store(Request $request){  
        $valor = $request->valor;
        $id = $request->id;
        $num_inicio = $request->finicial;
        $num_fin = $request->ffinal;
        $num_acta = $request->num_acta;
        $id_unidad = $request->id_unidad;
        
        if($id_unidad OR $id){
            
            
            if(!$request->publicar) $request->publicar=false;
            if($num_fin>$num_inicio){
                $folio_inicial = $folio_final = NULL;
                if($request->mod=="EXT") $prefijo = "D";
                elseif($request->mod=="CAE") $prefijo = "C";
                else $prefijo = "A";
                            
                if($num_inicio)$folio_inicial = $prefijo.str_pad($num_inicio, 6, "0", STR_PAD_LEFT);
                if($num_fin)$folio_final = $prefijo.str_pad($num_fin, 6, "0", STR_PAD_LEFT);
                        
                $total = $num_fin-$num_inicio+1;
    
                if($total>0){
                    ///Validación que no exista el rango de folio en la misma Unidad y modalida.
                    $valido = DB::table('tbl_banco_folios')->where('mod',$request->mod);
                        if($id)$valido = $valido->where('id','<>',$id);
                        $valido = $valido->where('finicial',$folio_inicial)->where('ffinal',$folio_final)->doesntExist();                
                    
                    if($valido){
                        $url_file = NULL;
                        if ($request->hasFile('file_acta') AND $num_acta) {
                            $num_acta = $request->num_acta;
                            $file = $request->file('file_acta');
                            $file_result = $this->upload_file($file, $num_acta);
                            //var_dump($file_result);exit;
                            $url_file = $file_result["url_file"];
                        }else $message = "Archivo inválido";
                        
                        $asignados = DB::table('tbl_folios')->where('folio', '>=', $folio_inicial)
                        ->where('folio', '<=', $folio_final)->value(DB::raw('count(distinct(folio))'));                        
                           
                        if(!$asignados)$asignados=0;
                        $unidad = DB::table('tbl_unidades')->where('id',$id_unidad)->value('unidad');
                        if($unidad){
                            if($id){                                           
                                $data = [ 'id_unidad' => $id_unidad,'unidad' => $unidad,'finicial' => $folio_inicial,'ffinal' => $folio_final, 'total' => $total, 'facta'=> $request->facta, 
                                    'num_inicio' => $num_inicio, 'num_fin' => $num_fin, 'contador' =>  $asignados, 'num_acta' => $num_acta,
                                    'activo' => $request->publicar, 'iduser_updated' => Auth::user()->id,'updated_at'=>date('Y-m-d H:i:s')];
                                if($url_file ) $data['file_acta'] = $url_file;
                                
                                $result = DB::table('tbl_banco_folios')->where('id',$id)->update($data);
                            }else{            
                                $result = DB::table('tbl_banco_folios')->Insert(                        
                                    ['unidad' => $unidad, 'finicial' => $folio_inicial, 'ffinal' => $folio_final, 'total' => $total,
                                    'mod' => $request->mod, 'facta'=> $request->facta, 'num_inicio' => $num_inicio, 'num_fin' => $num_fin,
                                    'id_unidad' => $id_unidad, 'contador' =>  $asignados, 'num_acta' => $num_acta,
                                    'activo' => $request->publicar, 'iduser_created' => Auth::user()->id, 'file_acta' =>$url_file, 
                                    'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')
                                    ]
                                );   
                            }
                        }else $message = "Registro no válido, seleccione la Unidad.";
        
                        if($result) $message = "Operación exitosa!! El registro ha sido guardado correctamente.";
                        else $message = "Operación inválida, es probable que exista el registro, por favor corrobore.";
    
                    }else $message = "El rango de folio ya esta dado de alta Modalidad.";
                   
                }else $message = "Rango de Folios no válido.";
            }else $message = "Rango de Folios no válido.";
        
        }else $message = "Por favor, seleccione una unidad.";
        return redirect('/solicitudes/folios')->with(['message'=>$message,'valor'=>$valor]);
    }

    protected function upload_file($file,$name)
    {       
        $ext = $file->getClientOriginalExtension(); // extension de la imagen
        $ext = strtolower($ext);
        $url = $mgs= null;

        if($ext == "pdf"){
            $name = trim($name.".pdf");
            $path = $this->path_pdf.$name;
            Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
            //echo $url = Storage::disk('custom_folder_1')->url($path); exit;
            $msg = "El archivo ha sido cargado o reemplazado correctamente.";            
        }else $msg= "Formato de Archivo no válido, sólo PDF.";
                
        $data_file = ["message"=>$msg, 'url_file'=>$path];
       
        return $data_file;
    }
   
}