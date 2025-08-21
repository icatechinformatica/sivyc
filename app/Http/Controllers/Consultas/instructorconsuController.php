<?php
namespace App\Http\Controllers\consultas;
use App\Http\Controllers\Controller;
use App\Models\instructor;
use App\Models\tbl_curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Excel\xls;
use Maatwebsite\Excel\Facades\Excel;

class instructorconsuController extends Controller
{
    public function index(Request $request){
        $unidades = DB::table('tbl_unidades')->pluck('unidad','unidad'); //dd($unidades);
        $consulta = $this->data($request);       
        return view('consultas.consultainstructor',compact('consulta','unidades','request'));
    }
   

    public function xls(Request $request){
            $data = $this->data($request);            
            if(count($data)==0){ return "NO EXISTEN REGISTROS QUE MOSTRAR";exit;}
            else{
                foreach($data as $key => $value){
                    if($data[$key]->tdias <=0) $data[$key]->tdias = $data[$key]->dias;
                    $data[$key]->dias ="";
                }                
            }

            $head = ['INSTRUCTOR','UNIDAD','GRUPO','CLAVE','MEMO','CURSO','ESPECIALIDAD','SERVICIO','DURA','CAPACITACIÓN','ESTATUS',
            'INICIO','TERMINO','HINI','HFIN','DIAS','LABORADOS','ESPACIO FÍSICO','OBSERVACIONES'];

            $title = $request->busqueda;
            $name = "CONSULTA_INSTRUCTORES_ASIGNADOS_".$request->busqueda."_".date('Ymd').".xlsx";

            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);
   }

   private function data(Request $request){
        $tipo = $request->tipo;
        $buscar= $request->busqueda;   //dd($request->all());
        $fecha_inicio = $request->fecha_inicio;
        $fecha_termino = $request->fecha_termino;
        $unidad = $request->unidad;        
        $consulta = null;
        if($unidad OR ($tipo AND $buscar) OR $fecha_inicio OR $fecha_termino){
            $consulta = DB::table('instructores')            
            ->select(
                DB::raw("
                    CASE 
                    WHEN tc.vb_dg=true  or tc.clave!= '0' THEN  tc.nombre
                    ELSE ''
                    END as nombre
                "),
                'unidad','folio_grupo','clave','munidad','curso','espe','tipo_curso','dura','tcapacitacion','status_curso',
                'inicio','termino','hini','hfin','dia',
                DB::raw("
                    CASE 
                        WHEN 
                            DATE_PART('day', tc.inicio::timestamp -  
                                (select  max(tcx.termino) from tbl_cursos as tcx where tcx.id_instructor= instructores.id  and tc.inicio>=tcx.inicio)::timestamp
                            )>=30
                        THEN
                            DATE_PART('day', tc.termino::timestamp - tc.inicio::timestamp )+1
                        ELSE
                            DATE_PART('day', tc.termino::timestamp -
                            (select  min(tcx.inicio) from tbl_cursos as tcx
                                where tcx.id_instructor= instructores.id and tcx.inicio<tc.termino  and 
                                tcx.inicio> COALESCE(
                                    (select max(inicio) from tbl_cursos as c where c.id_instructor = instructores.id  and c.termino<=tc.termino
                                    and COALESCE((select DATE_PART('day', c2.inicio::timestamp - c.termino::timestamp ) from tbl_cursos as c2 
                                    where c2.id_instructor = instructores.id  and c2.inicio>c.inicio and c2.inicio<=tc.termino order by c2.inicio ASC limit 1  )-1,0)>=30 ),
                                    (select min(inicio)::timestamp - interval '1 day' from tbl_cursos where inicio<=tc.termino and id_instructor = instructores.id )
                                )
                            )::timestamp)+1
                        END          
                as tdias"),
                'efisico','nota',
                DB::raw("DATE_PART('day', tc.inicio::timestamp -  
                (select  max(tcx.termino) from tbl_cursos as tcx where tcx.id_instructor= instructores.id  and tc.inicio>=tcx.inicio)::timestamp
            )as dias")
            )
            ->join('tbl_cursos as tc','instructores.id','=','tc.id_instructor');
            if (!empty($tipo) AND !empty($buscar)) {
                switch ($tipo) {
                    case 'instructor':                        
                        $buscar = trim($buscar,' ');
                        $buscar = $this->eliminar_tildes($buscar);
                        $consulta->where( DB::raw('replace(REPLACE(REPLACE(REPLACE(REPLACE(btrim(upper(CONCAT(instructores."apellidoPaterno", '."' '".' ,instructores."apellidoMaterno",'."' '".',instructores.nombre)),\' \'), \'Á\', \'A\'), \'É\',\'E\'), \'Í\', \'I\'), \'Ó\', \'O\'), \'Ú\',\'U\')'), 'LIKE', "%$buscar%");
                        break;
                    case 'curp':
                        $consulta->where( 'instructores.curp', '=', $buscar);
                        break;
                    case 'clave':
                        $consulta->where( 'tc.clave', '=', $buscar);
                        break;
                    case 'curso':
                        $buscar = trim($buscar,' ');
                        $buscar = $this->eliminar_tildes($buscar);                         
                        $consulta->where(DB::raw("replace(REPLACE(REPLACE(REPLACE(REPLACE(upper(btrim(tc.curso,' ')), 'Á', 'A'), 'É','E'), 'Í', 'I'), 'Ó', 'O'), 'Ú','U')"), 'like', "%$buscar%");
                        break;                    
                }
            
            }
            if(isset($fecha_inicio)&&isset($fecha_termino)){
                                $consulta = $consulta->whereRaw("(tc.inicio >= '$fecha_inicio' and tc.termino <= '$fecha_termino'
                                                    OR tc.inicio >= '$fecha_inicio' and tc.inicio <= '$fecha_termino'
                                                    OR tc.termino >= '$fecha_inicio' and tc.termino <= '$fecha_termino')");
            }elseif(isset($fecha_inicio)&&empty($fecha_termino)){
                $consulta = $consulta->where('tc.inicio','>=',$fecha_inicio);
            }elseif(empty($fecha_inicio)&&isset($fecha_termino)){
                $consulta = $consulta->where('tc.termino','<=',$fecha_termino);
            }

            if(isset($request->unidad)){
                $consulta = $consulta->where('tc.unidad','=',$request->unidad);
            }
            $consulta = $consulta->orderBy('tc.inicio','desc')->paginate(50,[DB::raw('CONCAT(instructores."apellidoPaterno", '."' '".' ,instructores."apellidoMaterno",'."' '".',instructores."nombre") as nombre'),
                'tc.efisico','tc.folio_grupo','tc.unidad','tc.curso','tc.status_curso','tc.inicio','tc.termino','tc.dia','tc.hini','tc.hfin','tc.horas','tipo_curso',
                'tc.dura','tcapacitacion','espe','tc.clave','tc.nota','tc.munidad'])->setPath('');
        }//dd($consulta);

        return $consulta;

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