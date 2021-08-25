<?php

namespace App\Http\Controllers\consultas;

use App\Http\Controllers\Controller;
use App\Models\instructor;
use App\Models\tbl_curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class instructorconsuController extends Controller
{
    public function index(Request $request){

        $tipo = $request->tipo;
        $buscar= $request->busqueda;   //dd($request->all());
        $fecha_inicio = $request->fecha_inicio;
        $fecha_termino = $request->fecha_termino;
        $unidad = DB::table('tbl_unidades')->pluck('unidad'); //dd($unidades);
        $consulta = DB::table('instructores')->join('tbl_cursos as tc','instructores.id','=','tc.id_instructor');
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty($buscar)) {
                # empezamos
                switch ($tipo) {
                    case 'nombre_instructor':
                        # code...
                         $consulta->where( DB::raw('CONCAT(instructores."apellidoPaterno", '."' '".' ,instructores."apellidoMaterno",'."' '".',instructores.nombre)'), 'LIKE', "%$buscar%");
                        break;
                    case 'curp':
                         $consulta->where( 'instructores.curp', '=', $buscar);
                        break;
                    case 'curso':
                         $consulta->where( 'tc.clave', '=', $buscar);
                        break;
                    case 'nombre_curso':
                         $consulta->where(DB::raw('UPPER(tc.curso)'), 'like', "%$buscar%");
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        if(isset($fecha_inicio)){
            $consulta = $consulta->where('tc.termino','>=',$fecha_inicio);
        }
        if(isset($fecha_termino)){
            $consulta = $consulta->where('tc.termino','<=',$fecha_termino);
        }
        if(isset($request->unidad)){
            $consulta = $consulta->where('tc.unidad','=',$request->unidad);
        }
        $consulta = $consulta->orderBy('tc.termino','desc')->paginate(15,[DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno") as nombre'),'tc.unidad','tc.curso','tc.status_curso','tc.inicio','tc.termino','tc.dia','tc.hini','tc.hfin','tc.horas']);

        return view('consultas.consultainstructor',compact('consulta','unidad'));
    }
}
