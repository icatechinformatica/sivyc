<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\ProductoStock;
use App\Models\tbl_curso;
use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Redirect,Response;
use App\Models\InstructorPerfil;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class CursoValidadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function cv_inicio() {
        $cd = new tbl_curso();
        $data = $cd::SELECT('tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                            'instructores.nombre AS nombreins','tbl_cursos.pini')
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->GET();
        return view('layouts.pages.vstacvinicio', compact('data'));
    }

    public function cv_crear() {
        $curso = new curso();
        $data = $curso::where('id', '!=', '0')->latest()->get();
        return view('layouts.pages.frmcursovalidado',compact('data'));
    }

    public function solicitud_guardar(Request $request) {
        return redirect()->route('/supre/solicitud/inicio')
                         ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function fill1(Request $request) {
        $instructor = new instructor();
        $input = $request->numero_control;
        $newsAll = $instructor::where('numero_control', $input)->first();
        return response()->json($newsAll, 200);
    }
}
