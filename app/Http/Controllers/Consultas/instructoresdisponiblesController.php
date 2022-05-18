<?php

namespace App\Http\Controllers\consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\instructor;
use App\Models\tbl_curso;
use App\Models\cat\catUnidades;
use App\Models\cat\catApertura;

class instructoresdisponiblesController extends Controller
{
    use catUnidades;
    use catApertura;
    function __construct() {
        session_start();
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->path_pdf = "/DTA/solicitud_folios/";
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;
            $this->id_unidad = Auth::user()->unidad;            
            $this->data = $this->unidades_user('unidad');
            $_SESSION['unidades'] =  $this->data['unidades'];
            return $next($request);
        });
    }

    public function index(Request $request){
       $unidad = $request->unidad;       
       $consulta = $cursos = [];
       if($request->id_curso){
            $consulta = $this->instructores_disponibles($request);   
            $cursos = DB::table('cursos')
                    ->where('tipo_curso', $request->tipo)
                    ->where('cursos.estado', true)
                    ->where('modalidad','like',"%$request->mod%")
                    ->whereJsonContains('unidades_disponible', [$unidad])->orderby('cursos.nombre_curso')->pluck('nombre_curso', 'cursos.id');
         
       }
       $unidades = $_SESSION['unidades'];
       //var_dump($unidades);exit;
       return view('consultas.instructoresdisponibles',compact('consulta','request','unidades','cursos'));
    }

    public function eliminar_tildes($cadena){
        //Codificamos la cadena en formato utf8 en caso de que nos de errores
        $cadena = $cadena; //dd($cadena);
        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena );

    $cadena = str_replace(
        array('ç', 'Ç'),
        array('c', 'C'),
        $cadena
    );

    return $cadena;
    }
}
