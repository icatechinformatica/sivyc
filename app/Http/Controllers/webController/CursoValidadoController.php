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
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Support\Facades\DB;

class CursoValidadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function cv_inicio(Request $request) {
        // parametros de busqueda
        $buscarcursoValidado = $request->get('busqueda_curso_validado');

        $tipoCursoValidad = $request->get('tipobusquedacursovalidado');

        // obtener el usuario y su unidad
        $unidadUser = Auth::user()->unidad;

        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();

        // obtener unidades
        $unidades = new tbl_unidades;
        $unidadPorUsuario = $unidades->WHERE('id', $unidadUser)->FIRST();

        switch ($roles[0]->role_name) {
            case 'dta_certificacion_control':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
            break;
            case 'depto_academico_cursos':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
            break;
            case 'depto_academico':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
            break;
            case 'unidad.ejecutiva':
                # code... DTA - Información e Innovación Académica - Jefatura
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
            break;
            case 'direccion.general':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
                break;
            case 'planeacion':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
                break;
            case 'financiero_verificador':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
                break;
            case 'financiero_pago':
                # code...
                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
                break;
                case 'admin':
                    # code...
                    $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                        ->WHERE('tbl_cursos.clave', '!=', '0')
                        ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                        ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                        ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                        ->orderBy('tbl_cursos.id', 'desc')
                        ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                        'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                        'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
                    break;
            default:
                # code...
                // obtener unidades
                $unidadUsuario = DB::table('tbl_unidades')->WHERE('id', $unidadUser)->FIRST();
                /**
                 * contratos - contratos
                 */

                $data = tbl_curso::busquedacursovalidado($tipoCursoValidad, $buscarcursoValidado)
                    ->WHERE('tbl_cursos.clave', '!=', '0')
                    ->WHERE('tbl_unidades.ubicacion', '=', $unidadPorUsuario->ubicacion)
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores','instructores.id','=','tbl_cursos.id_instructor')
                    ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->orderBy('tbl_cursos.id', 'desc')
                    ->PAGINATE(25, ['tbl_cursos.id','tbl_cursos.clave','cursos.nombre_curso AS nombrecur',
                    'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.archivo_alta',
                    'tbl_cursos.inicio','tbl_cursos.termino', 'tbl_cursos.unidad','tbl_cursos.pdf_curso']);
                break;
        }


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
