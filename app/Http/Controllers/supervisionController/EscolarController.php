<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\calidad_encuestas;

class EscolarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct() {

    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $tipo = $request->get('tipo_busqueda');
        $valor = $request->get('valor_busqueda');
        $unidades = $user->unidades;
        $id_user = $user->id;

        if($request->get('fecha')) $fecha = $request->get('fecha');
        else $fecha = date("d/m/Y");

        $query = DB::table('tbl_cursos')->select('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad',DB::raw('COUNT(DISTINCT(i.id)) as total'),DB::raw('COUNT(DISTINCT(a.id)) as total_alumnos'),
        'token_i.id as token_instructor','token_i.ttl as ttl_instructor','token_a.id_curso as token_alumno');

        if($fecha)$query = $query->where('tbl_cursos.inicio','<=',$fecha)->where('tbl_cursos.termino','>=',$fecha);
        if($unidades) {
            $unidades = explode(',',$unidades);
            $query = $query->whereIn('tbl_cursos.unidad',$unidades);
        }
        if (!empty($tipo) AND !empty(trim($valor))) {
            switch ($tipo) {
                case 'nombre_instructor':
                    $query = $query->where('tbl_cursos.nombre', 'like', '%'.$valor.'%');
                    break;
                case 'clave_curso':
                    $query = $query->where('tbl_cursos.clave',$valor);
                    break;
                case 'nombre_curso':
                    $query = $query->where('tbl_cursos.curso', 'LIKE', '%'.$valor.'%');
                    break;
            }
        }
        $query = $query->where('tbl_cursos.clave', '>', '0');

        $query = $query->leftJoin('supervision_instructores as i', function($join)use($id_user){
                $join->on('i.id_tbl_cursos', '=', 'tbl_cursos.id');
                $join->where('i.id_user',$id_user);
                $join->groupBy('i.id_tbl_cursos');

            });
        $query = $query->leftJoin('supervision_alumnos as a', function($join)use($id_user){
                $join->on('a.id_tbl_cursos', '=', 'tbl_cursos.id');
                $join->where('a.id_user',$id_user);
                $join->groupBy('a.id_tbl_cursos');

            });

        $query = $query->leftJoin('supervision_tokens as token_i' ,function($join)use($id_user){
                $join->on('tbl_cursos.id', '=', 'token_i.id_curso');
                $join->on('token_i.id_instructor','=','tbl_cursos.id_instructor');
                $join->where('token_i.id_supervisor',$id_user);
                $join->where('token_i.id_instructor','>','0');
        });

        $query = $query->leftJoin('supervision_tokens as token_a' ,function($join)use($id_user){
                $join->on('tbl_cursos.id', '=', 'token_a.id_curso');
                $join->where('token_a.id_supervisor',$id_user);
                $join->where('token_a.id_alumno','>','0');
        });

        $query = $query->groupby('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad','i.id_tbl_cursos','a.id_tbl_cursos','token_i.id','token_i.ttl','token_a.id_curso');

        $data =  $query->orderBy('tbl_cursos.inicio', 'DESC')->paginate(15);
        //var_dump($data);exit;


        return view('supervision.escolar.index', compact('data','fecha'));
    }

    public function encuesta()
    {
        $encuesta = calidad_encuestas::where('activo', '=', 'true')->WHERE('idparent', '!=', '0')->GET();
        $titulo = calidad_encuestas::SELECT('nombre')->WHERE('activo', '=', 'true')->WHERE('idparent', '=', '0')->FIRST();
        return view('layouts.pages.frmencuesta', compact('encuesta','titulo'));
    }

    public function encuesta_save(Request $request)
    {
        $x = $request->get('optradio');
       /* $keys = array_keys($request->optradio);
        $validate_array = [current($keys) => 'required'];
        do
        {
            $validate_array[current($keys)] = 'required';
            print(current($keys));
            next($keys);
        } while(current($keys) != NULL);
        $this->validate($x, $validate_array );*/

        dd($x);


    }


}
