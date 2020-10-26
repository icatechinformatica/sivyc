<?php
// Elaboró Romelia Pérez Nangüelú 
// rpnanguelu@gmail.com

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use App\Models\supervision\funcionario;
//use App\Models\tbl_curso;
//use App\Models\instructor;
use App\Models\curso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Redirect;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function __construct(){
       //session_start();  
      
    }
     
   
    
    public function index(Request $request, $token)
    {     
        $_SESSION["id"] = $token;
        $data = DB::table('oficinas')->select('clave','oficina','niveles')->where('activo',1)->orderby('clave')->get();
       //var_dump($data);exit; 
        return view('supervision.client.frmfuncionario',compact('data'));
    }    
    
    public function guardar(Request $request)
    {          
            $id = $_SESSION["id"];
        try {                         
            $anio = date("Y");
            $fecha = date("dmy"); 
            $inscrip = DB::table('tbl_inscripcion')->where('id', $id)->first();
            $curso = DB::table('tbl_cursos')->where('id', $inscrip->id_curso)->first();
            
            $fields = ['ok_nombre','ok_edad','ok_escolaridad','ok_fecha_inscripcion',           
                'ok_documentos','ok_curso','ok_numero_apertura','ok_fecha_autorizacion',
                'ok_modalidad','ok_fecha_inicio','ok_fecha_termino','ok_horario',
                'ok_tipo','ok_lugar','ok_cuota'];
            
            $instructor = new alumno();             
            $instructor->nombre = trim($request->nombre);
            $instructor->apellidoPaterno = trim($request->apellidoPaterno);
            $instructor->apellidoMaterno = trim($request->apellidoMaterno);
            $instructor->edad= $request->edad;
            $instructor->escolaridad = trim($request->escolaridad);
            $instructor->fecha_inscripcion = $request->fecha_inscripcion;
            $instructor->documentos = trim($request->documentos);            
            $instructor->curso = trim($request->curso);
            $instructor->numero_apertura = trim($request->numero_apertura);
            $instructor->fecha_autorizacion = $request->fecha_autorizacion;
            $instructor->modalidad = $request->modalidad;
            $instructor->fecha_inicio = $request->fecha_inicio;
            $instructor->fecha_termino = $request->fecha_termino;
            $instructor->hinicio = trim($request->hinicio);
            $instructor->hfin = trim($request->hfin);
            $instructor->tipo = $request->tipo;
            $instructor->lugar = trim($request->lugar);
            $instructor->cuota = $request->cuota;            
            $instructor->id_tbl_cursos = $curso->id;            
            $instructor->id_curso = $curso->id_curso;
            $instructor->id_instructor = $curso->id_instructor;
            $instructor->id_tbl_inscripcion = $inscrip->id;
            $instructor->unidad = $curso->unidad;            
            //$instructor->id_users = $this->id_users;
            foreach ($fields as $i => $value) {            
                    $instructor->$value = true;
            } 
                
            if($instructor->save()) $id_supervision = $instructor->id;
            
            /*subiendo foto del instructor*/
            //$path_dir = "C:\Users\Romelia\Documents\pruebas";
            $path_dir = "prueba";
            $file_name =  $id_supervision."-".$fecha."-";
                  
            $ext = explode (".", $_FILES["file_photo"]["name"]);
            $pos = count($ext)-1;            
            $ext = $ext[$pos]; 
            $fuente = $_FILES["file_photo"]["tmp_name"]; 
            $dir = opendir($path_dir);
        	$target_path = $path_dir.'/'.$file_name.'0.'.$ext; //indicamos la ruta de destino de los archivos
        	if(move_uploaded_file($fuente, $target_path)) {	
        	      echo "Los archivos $file_name se han cargado de forma correcta.<br>";
        	} else {	
        			echo "Se ha producido un error, por favor revise los archivos e intentelo de nuevo.<br>";
 			}
             
            
            $n = 0;     
            if(isset($_FILES['file_data'])){                
                foreach($_FILES["file_data"]['tmp_name'] as $key => $tmp_name){                    
            		//if($_FILES["file_data"]["name"][$key]) {
                        $n++;  		           
                        $ext = explode (".", $_FILES["file_data"]["name"][$key]);
                        $pos = count($ext)-1;            
                        $ext = $ext[$pos];                  
            			$archivonombre = $file_name.$n.".".$ext;                     
            			$fuente = $_FILES["file_data"]["tmp_name"][$key];
            			$target_path = $path_dir.'/'.$archivonombre;                             
            			if(move_uploaded_file($fuente, $target_path)) {	
                		  echo "Los archivos $archivonombre se han cargado de forma correcta.<br>";
                		} else {	
                		  echo "Se ha producido un error, por favor revise los archivos e intentelo de nuevo.<br>";
                		}
            		//}
            	}
            }            
           	closedir($dir); //Cerramos la conexion con la carpeta destino
            
            return redirect('supervision/alumno/0');            
        } catch (Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }
        
            
    }
}
