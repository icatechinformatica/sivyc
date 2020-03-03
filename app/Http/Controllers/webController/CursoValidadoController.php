<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\ProductoStock;
use App\Models\cursoValidado;
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
        $cd = new cursoValidado();
        $data = $cd::SELECT('curso_validado.id','curso_validado.clave_curso','cursos.nombre_curso AS nombrecur',
                            'instructores.nombre AS nombreins','curso_validado.fecha_inicio','curso_validado.fecha_termino')
                    ->WHERE('curso_validado.clave_curso', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','curso_validado.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','curso_validado.id_instructor')
                    ->GET();
        return view('layouts.pages.vstacvinicio', compact('data'));
    }

    public function cv_crear() {
        return view('layouts.pages.frmcursovalidado');
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
