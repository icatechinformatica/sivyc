<?php
//Creado por Romelia Pérez Nangüelú--rpnanguelu@gmail.com 
namespace App\Http\Controllers\TableroControlller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    public function index(Request $request){
        $ubicacion = $request->get('ubicacion');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_termino = $request->get('fecha_termino');
        
        $data = tbl_curso::BusquedaTablero($ubicacion, $fecha_inicio, $fecha_termino)
                ->LEFTJOIN('folios as f', 'f.id_cursos', '=', 'tbl_cursos.id')               
                ->GROUPBY('tbl_cursos.id')->GROUPBY('tbl_cursos.unidad')->groupBy('tbl_cursos.clave')->groupBy('tbl_cursos.curso')
                ->GROUPBY('tbl_cursos.mod')->groupBy('tbl_cursos.nombre')
                ->GROUPBY('f.status')->groupBy('f.importe_total')->groupBy('tbl_cursos.dura')
                ->GROUPBY('tbl_cursos.efisico')->groupBy('tbl_cursos.depen')
                ->ORDERBY('tbl_cursos.unidad')
                ->PAGINATE(10, [
                    'tbl_cursos.id','tbl_cursos.unidad', 'tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.mod as modalidad','tbl_cursos.nombre as instructor',
                    'f.status as status_pago', DB::raw('COALESCE(f.importe_total,0) as honorarios'),DB::raw('(tbl_cursos.hombre+tbl_cursos.mujer) as total_alumnos'),
                    'tbl_cursos.dura as horas' ,'tbl_cursos.tcapacitacion as tipo', 'tbl_cursos.depen as organismo','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.fecha_apertura',
                    'tbl_cursos.status_curso','tbl_cursos.hini','tbl_cursos.hfin'
                ]);
        $breadcrumb = "Cursos Aperturados";
        $lst_ubicacion =  DB::table('tbl_unidades')->orderby('ubicacion','ASC')->pluck('ubicacion','ubicacion');        
        return view('tablero.cursos.index', compact('data','lst_ubicacion','breadcrumb','ubicacion','fecha_inicio','fecha_termino'));
    }
   
}
