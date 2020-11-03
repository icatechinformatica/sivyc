<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\Tbl_curso;

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
        $anio = date("Y");

        if($request->get('fecha')) $fecha = $request->get('fecha');
        else $fecha = date("d/m/Y");

        $query = DB::table('tbl_cursos')->select('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad',DB::raw('COUNT(DISTINCT(i.id)) as total'),DB::raw('COUNT(DISTINCT(a.id)) as total_alumnos'),
        'token_i.id as token_instructor','token_i.ttl as ttl_instructor','token_a.id_curso as token_alumno',
        'tbl_cursos.json_supervision');

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

    public function updateCurso(Request $request)
    {
        $id_supervisor = Auth::user()->id;
        $id_curso = $request->input('id_curso');
        $fecha = date("dmy");
        $anio = date("Y");
        $archivo = "#";
        if($id_curso AND $request->input('status_supervision') AND $request->input('obs_supervision') AND $request->file('file_soporte')){
            $status = $request->input('status_supervision');
            if ($request->file('file_soporte')) {
                $ext = $request->file('file_soporte')->extension();
                $file_name =  $status."-".$id_curso."-".$fecha.".".$ext;
                $path_file = '/supervisiones/'.$anio.'/cursos';
                $archivo =  'storage'.$path_file.'/'.$file_name;
            }

            $RegisterExists->respuestas->respuestas = $array;
            $id_encuesta = $RegisterExists->id_encuesta;
            $RegisterExists->save();

            $token->cantidad_usuarios = $token->cantidad_usuarios - 1;
            if ($token->cantidad_usuarios == 0 )
            {
                $token = tokenEncuesta::WHERE('url_token' , '=', $request->token)->DELETE();
            }
            else
            {
                $token->save();
            }

        }
        else
        {
            $cursoValidado = tbl_curso::WHERE('id', '=', $token->id_curso);
            $encuesta = calidad_encuestas::SELECT('id','respuestas')->WHERE('activo', '=', 'true')->WHERE('idparent', '!=', '0')->WHERE('respuestas', '!=', NULL)->GET();

            $save_respuestas = new calidad_respuestas;
            $save_respuestas->id_encuesta = $request->id_encuesta;
            $save_respuestas->id_tbl_cursos = $token->id_curso;
            $save_respuestas->id_curso = $cursoValidado->id_curso;
            $save_respuestas->id_instructor = $cursoValidado->id_instructor;
            $save_respuestas->unidad = $cursoValidado->unidad;
            $save_respuestas->fecha_aplicacion = Carbon::now();

            foreach($encuesta as $item)
            {
                $key = $item->respuestas;
                foreach ($key as $data)
                {
                    if($data == current($x))
                    {
                        $array_respuestas[$item->id][$data] = '1';
                    }
                    else
                    {
                        $array_respuestas[$item->id][$data] = '0';
                    }
                }
                next($x);
            }
            dd($array_respuestas);
            $save_respuestas->respuestas = $array_respuestas;
            $id_encuesta = $encuesta->id;
            $save_respuestas->save();

            $token->cantidad_usuarios = $token->cantidad_usuarios - 1;
            if ($token->cantidad_usuarios == 0 )
            {
                $token = tokenEncuesta::WHERE('url_token' , '=', $request->token)->DELETE();
            }
            else
            {
                $token->save();
            }
        }

        $inscripcion = Inscripcion::WHERE('matricula', '=', $request->matricula)->WHERE('id_curso', '=', $token->id_curso)->FIRST();

        $respuesta_alumno = new calidad_respuestas_alumnos;
        $respuesta_alumno->id_inscripcion = $inscripcion->id;
        $respuesta_alumno->matricula = $inscripcion->matricula;
        $respuesta_alumno->nombre = $inscripcion->alumno;
        $respuesta_alumno->id_tbl_cursos = $id_curso;
        $respuesta_alumno->id_encuesta = $id_encuesta;
        $respuesta_alumno->respuestas = $x;
        $respuesta_alumno->save();
    }

    public function prueba() {
        /*$save_respuestas = new calidad_respuestas;
        $save_respuestas->id_encuesta = '1';
        $save_respuestas->id_tbl_cursos = '2';
        $save_respuestas->id_curso = '3';
        $save_respuestas->id_instructor = '4';
        $save_respuestas->unidad = '5';
        $save_respuestas->fecha_aplicacion = '12-12-2020';*/





        //$prueba = calidad_encuestas::SELECT('id, 'respuestas')->WHERE('activo', '=', 'true')->WHERE('idparent', '!=', '0')->WHERE('respuestas', '!=', NULL)->GET();
        /*$keys = array_keys($prueba);
        foreach($prueba as $item)
        {
            $key = $item->respuestas;
            foreach ($key as $data)
            {
                $array_respuestas[$item->id][$data] = '0';
            }
        }
        $save_respuestas->respuestas = $array_respuestas;
        $save_respuestas->save();*/
        $pruebas = calidad_respuestas::SELECT('respuestas')->WHERE('id', '=', '5')->first();
        $array = $pruebas->respuestas;
        $pointerid = array_keys($array);
        dd(current($pointerid));
        foreach ($array as $data)
        {
            $keys = array_keys($data);
            foreach($keys as $item)
            {
                if($item == 'Malo')
                {
                    $array['3'][$item] = $array['3'][$item] + 1;
                    dd($array['3'][$item]);
                }
                print($item . ' ');
            }

        }
        return 0;
    }

}
