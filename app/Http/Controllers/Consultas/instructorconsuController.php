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
                         $buscar = trim($buscar,' ');
                         $buscar = $this->eliminar_tildes($buscar);    //dd($buscar);
                        //  dd($buscar);
                         //$consulta->where( DB::raw('(btrim(CONCAT(instructores."apellidoPaterno", '."' '".' ,instructores."apellidoMaterno",'."' '".',instructores.nombre)))'), 'LIKE', "%$buscar%");
                         //$consulta->where(DB::raw('replace(REPLACE(REPLACE(REPLACE(REPLACE(upper(CONCAT(instructores."apellidoPaterno", '."' '".' ,instructores."apellidoMaterno",'."' '".',instructores.nombre)), Á, A), É,E), Í, I), Ó, O), Ú,U)'),'like',"%$buscar%");
                         $consulta->where( DB::raw('replace(REPLACE(REPLACE(REPLACE(REPLACE(btrim(upper(CONCAT(instructores."apellidoPaterno", '."' '".' ,instructores."apellidoMaterno",'."' '".',instructores.nombre)),\' \'), \'Á\', \'A\'), \'É\',\'E\'), \'Í\', \'I\'), \'Ó\', \'O\'), \'Ú\',\'U\')'), 'LIKE', "%$buscar%");
                        break;
                    case 'curp':
                         $consulta->where( 'instructores.curp', '=', $buscar);
                        break;
                    case 'curso':
                         $consulta->where( 'tc.clave', '=', $buscar);
                        break;
                    case 'nombre_curso':
                         $buscar = trim($buscar,' ');
                         $buscar = $this->eliminar_tildes($buscar); //dd($buscar);
                         //$consulta->where(DB::raw('UPPER(tc.curso)'), 'like', "%$buscar%");
                         $consulta->where(DB::raw("replace(REPLACE(REPLACE(REPLACE(REPLACE(upper(btrim(tc.curso,' ')), 'Á', 'A'), 'É','E'), 'Í', 'I'), 'Ó', 'O'), 'Ú','U')"), 'like', "%$buscar%");
                         //dd($consulta);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        if(isset($fecha_inicio)&&isset($fecha_termino)){
                /*$consulta = $consulta->where(function ($query,$fecha_inicio,$fecha_termino) {
                                        $query->where('tc.inicio', '>=', $fecha_inicio)
                                              ->Where('tc.termino', '<=', $fecha_termino);})
                                    ->orWhere(function ($query,$fecha_inicio,$fecha_termino) {
                                        $query->where('tc.inicio','>=', $fecha_inicio)
                                              ->where('tc.inicio','<=', $fecha_termino);})
                                    ->orWhere(function ($query,$fecha_inicio,$fecha_termino) {
                                        $query->where('tc.termino','>=', $fecha_inicio)
                                              ->where('tc.termino','<=', $fecha_termino);
                                    });*/
                $consulta = $consulta->whereRaw("(tc.inicio >= '$fecha_inicio' and tc.termino <= '$fecha_termino'
                                                OR tc.inicio >= '$fecha_inicio' and tc.inicio <= '$fecha_termino'
                                                OR tc.termino >= '$fecha_inicio' and tc.termino <= '$fecha_termino')");
                                    //  ->orwhereRaw("tc.inicio >= '$fecha_inicio' and tc.inicio <= '$fecha_termino'")
                                    //  ->orWhereRaw("tc.termino >= '$fecha_inicio' and tc.termino <= '$fecha_termino')");
        }elseif(isset($fecha_inicio)&&empty($fecha_termino)){
            $consulta = $consulta->where('tc.inicio','>=',$fecha_inicio);
        }elseif(empty($fecha_inicio)&&isset($fecha_termino)){
            $consulta = $consulta->where('tc.termino','<=',$fecha_termino);
        }

        if(isset($request->unidad)){
            $consulta = $consulta->where('tc.unidad','=',$request->unidad);
        }
        $consulta = $consulta->orderBy('tc.termino','desc')->paginate(15,[DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno") as nombre'),'tc.unidad','tc.curso','tc.status_curso','tc.inicio','tc.termino','tc.dia','tc.hini','tc.hfin','tc.horas','tipo_curso','tcapacitacion','espe']);
        // dd($consulta);
        return view('consultas.consultainstructor',compact('consulta','unidad'));
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
