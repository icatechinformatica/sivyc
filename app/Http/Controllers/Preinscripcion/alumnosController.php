<?php
namespace App\Http\Controllers\Preinscripcion;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;

class alumnosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $buscar = $request->get('busquedapor');
        $message = null;
        if($buscar){            
            $alumnos = Alumno::busqueda($buscar,'alumno')->paginate(25);
            if(!count($alumnos)) $message["ERROR"] = "NO SE ENCONTRARON REGISTROS EN LA BÚSQUEDA DE ".$buscar;
        }else{
             $alumnos = null;             
        }
        return view('preinscripcion.alumnos.index', compact('alumnos','message','buscar'));
    }
}