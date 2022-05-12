<?php

namespace App\Http\Controllers\ApiController\ApiSupervisionMovil;

use App\Http\Controllers\Controller;
use App\Models\api\Curso;
use App\Models\tbl_curso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisionMovilController extends Controller
{
    
    //obtiene info de curso por clave 
    public function getCurso(Request $request)
    {

        $tbl_cursos = DB::table('tbl_cursos')->select('id', 'curso', 'cct', 'unidad', 'clave', 'mod', 'inicio', 'termino', 'area', 'espe', 'tcapacitacion', 'depen', 'tipo_curso')->WHERE('clave', '=', $request->clave)->get();        
        return response()->json($tbl_cursos, 200);
    }

    public function getAlumnos($id_tbl_curso)
    {
        $response = DB::SELECT(DB::raw("(SELECT 
        P.id,I.matricula, P.nombre, P.apellido_paterno, P.apellido_materno, P.correo, P.telefono, P.sexo, P.fecha_nacimiento, P.domicilio, P.colonia, P.municipio, P.estado, 
        P.estado_civil, I.curp,I.id_curso FROM tbl_inscripcion as I INNER JOIN alumnos_pre AS P on P.curp = I.curp WHERE i.id_curso = '$id_tbl_curso' )"));
        
        return response()->json($response, 200);
    }
}
