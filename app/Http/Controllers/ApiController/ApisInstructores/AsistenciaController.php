<?php

namespace App\Http\Controllers\ApiController\ApisInstructores;

use App\Models\tbl_curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use Exception;

class AsistenciaController extends Controller
{
    public function getCurso($clave) {
        return response()->json(tbl_curso::where('clave', $clave)->first(), 200);
    }

    public function getAlumnos($idCurso) {
        $alumnos = DB::table('tbl_inscripcion as i')->select(
            'i.id',
            'i.matricula',
            'i.alumno',
            'i.calificacion',
            'f.folio',
            'i.asistencias'
        )->leftJoin('tbl_folios as f', function ($join) {
            $join->on('f.id', '=', 'i.id_folio');
        })->where('i.id_curso', $idCurso)
            ->where('i.status', 'INSCRITO')
            ->orderby('i.alumno')->get();

        return response()->json($alumnos, 200);
    }

    public function updateAsistencias(Request $request) {
        $fechas = $request[0]['fechas'];
        $alumnos = $request[0]['alumnos'];
        $asistencias = $request[0]['asistencias'];

        try {
            foreach ($alumnos as $alumno) {
                $asisAlumno = [];
                foreach ($fechas as $fecha) {
                    $bandera = false;
                    foreach ($asistencias as $asistencia) {
                        if ($alumno == explode(' ', $asistencia)[0] && $fecha == explode(' ', $asistencia)[1]) $bandera = true;
                    }
                    if ($bandera) {
                        $temp = [
                            'fecha' => $fecha,
                            'asistencia' => true
                        ];
                    } else {
                        $temp = [
                            'fecha' => $fecha,
                            'asistencia' => false
                        ];
                    }
                    array_push($asisAlumno, $temp);
                }
                // se actualiza el alumno en la bd
                Inscripcion::where('id', '=', $alumno)->update(['asistencias' => $asisAlumno]);
            }
            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 501);
        }
    }

    public function getCursoAsistenciaPdf($clave) {
        $curso = DB::table('tbl_cursos')
            ->select(
                'tbl_cursos.*',
                DB::raw('right(clave,4) as grupo'),
                'inicio',
                'termino',
                DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
                DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
                'u.plantel',
            )->where('clave',$clave);
        $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();

        return response()->json($curso, 200);
    }

    public function updateAsisFinalizado($id) {
        try {
            tbl_curso::where('id', $id)->update(['asis_finalizado' => true]);
            return response()->json('success', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 501);
        }
    }
}
