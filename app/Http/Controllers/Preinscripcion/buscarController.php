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
use App\Utilities\MyUtility;

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
        $ejercicio = $request->ejercicio;
        $activar = $this->activar;
        $anios = MyUtility::ejercicios();
        $parameters = $request->all();
        if(!isset($parameters['ejercicio'])) $ejercicio = $parameters['ejercicio'] = date('Y');
        $data = DB::table('alumnos_registro as ar')
        ->select('ar.folio_grupo', 'ar.turnado', 'c.nombre_curso as curso', 'ar.unidad')
        ->join('cursos as c', 'ar.id_curso', '=', 'c.id');        

        if (preg_match('/^2B-\d{6}$/', $valor_buscar)){
            $data->where('ar.folio_grupo', 'like', '%' . $valor_buscar . '%');
            $parameters['ejercicio'] = $ejercicio = null;
        } else {
            $data->whereYear('ar.inicio', '=', $ejercicio)
            ->where(function ($query) use ($valor_buscar) {
                $query->where('ar.folio_grupo', 'like', '%' . $valor_buscar . '%')
                    ->orWhere('c.nombre_curso', 'like', '%' . $valor_buscar . '%');
            });     
        }

        if ($this->data['slug'] == 'vinculadores_administrativo') {
            $data->where('ar.iduser_created', $this->id_user);
        }

        if (!empty($_SESSION['unidades'])) {
            $data->whereIn('ar.unidad', $_SESSION['unidades']);
        }

        $data = $data->whereNotNull('ar.folio_grupo')
            ->groupBy('ar.folio_grupo', 'ar.turnado', 'c.nombre_curso', 'ar.unidad')
            ->orderBy('ar.folio_grupo', 'DESC')
            ->paginate(15);
        return view('preinscripcion.buscar.index',compact('data','activar','anios','parameters'));

    }

    public function show(Request $request){
        $_SESSION['folio_grupo'] = $request->folio_grupo;
        return redirect()->route('preinscripcion.grupo');
    }

}
