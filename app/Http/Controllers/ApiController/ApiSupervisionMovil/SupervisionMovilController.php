<?php

namespace App\Http\Controllers\ApiController\ApiSupervisionMovil;

use App\Http\Controllers\Controller;
use App\Models\api\Curso;
use App\Models\tbl_curso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisionMovilController extends Controller
{

    public function getCurso(Request $request)
    {
        $tbl_cursos = DB::table('tbl_cursos')->select('id', 'curso', 'cct', 'unidad', 'clave', 'mod', 'inicio', 'termino', 'area', 'espe', 'tcapacitacion', 'depen', 'tipo_curso')->WHERE('clave', '=', $request->clave)->get();

        return response()->json($tbl_cursos, 200);
    }

    //obtiene la informacion de los alumnos por clave de cursp
    public function getAlumnos($id_tbl_curso)
    {
        $response = DB::SELECT(DB::raw("(SELECT 
        P.id,I.matricula, P.nombre, P.apellido_paterno, P.apellido_materno, P.correo, P.telefono, P.sexo, P.fecha_nacimiento, P.domicilio, P.colonia, P.municipio, P.estado, 
        P.estado_civil, I.curp,I.id_curso FROM tbl_inscripcion as I INNER JOIN alumnos_pre AS P on P.curp = I.curp WHERE i.id_curso = '$id_tbl_curso' )"));

        return response()->json($response, 200);
    }
    
    //obtiene info de curso por clave 
    public function getCursosPorSupervisar(Request $request)
    {

        // $current_date = Carbon::now()->format('Y-m-d');
        // $last_date = date("Y-m-t", strtotime(Carbon::now()));
        $current_date = '2022-09-1';
        $last_date = date("Y-m-t", strtotime(Carbon::now()));

        $response = DB::SELECT(DB::raw("(SELECT tc.id, tc.curso, tc.cct, tc.unidad, tc.clave, tc.mod, tc.inicio, tc.termino, tc.area, tc.espe, tc.tcapacitacion, tc.depen, tc.tipo_curso  FROM tbl_cursos as tc
        JOIN tbl_unidades as tu on tu.unidad = tc.unidad  
        WHERE  (tc.clave IS NOT NULl AND tc.clave <> '0') AND tu.ubicacion = 'TUXTLA' AND tc.tcapacitacion = 'PRESENCIAL' AND tc.inicio >='$current_date' AND tc.termino <= '$last_date' )"));


        return response()->json($response, 200);
    }
}
