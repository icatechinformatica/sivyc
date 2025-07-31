<?php

namespace App\Http\Controllers\Alumno;

use App\Models\Sexo;
use App\Services\Alumno\GuardarSeccionService;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ConsultaDatosCURPService;
use App\Models\estado_civil as EstadoCivil;
use App\Services\Alumno\AlumnoConsultaService;

class AlumnoController extends Controller
{
    protected $alumnoConsultaService;
    protected $consultaDatosCURPService;
    protected $registroTempService;
    protected $guardarSeccionService;

    public function __construct(AlumnoConsultaService $alumnoConsultaService, ConsultaDatosCURPService $consultaDatosCURPService, GuardarSeccionService $guardarSeccionService)
    {
        $this->alumnoConsultaService = $alumnoConsultaService;
        $this->consultaDatosCURPService = $consultaDatosCURPService;
        $this->guardarSeccionService = $guardarSeccionService;
    }

    public function index(Request $request)
    {
        try {
            $registrosPorPagina = $request->get('per_page', 15);
            $busqueda = $request->get('busqueda');
            $alumnos = $this->alumnoConsultaService->obtenerAlumnos($registrosPorPagina, $busqueda);
            return view('alumnos.index', compact('alumnos'));
        } catch (\Exception $e) {
            Log::error('Error en AlumnoController@index: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los alumnos'], 500);
        }
    }

    public function consultarCurp(Request $request)
    {
        $curp = $request->input('curp');
        $existeRegistroAlumno = $this->alumnoConsultaService->obtenerAlumnoPorCURP($curp);
        $encodeCURP = urlencode(base64_encode($curp));
        if ($existeRegistroAlumno) {
            return redirect()->route('alumnos.ver.registro.alumno', $encodeCURP); // * Redirige al método verRegistroAlumno
        } else {
            return redirect()->route('alumnos.nuevo.registro.alumno', $encodeCURP); // * Redirige al método nuevoRegistroAlumno
        }
    }

    public function verRegistroAlumno($encodeCURP)
    {
        $curp = base64_decode(urldecode($encodeCURP));
        $esNuevoRegistro = false;
        $datos = $this->alumnoConsultaService->obtenerAlumnoPorCURP($curp);
        $sexos = Sexo::all();
        $nacionalidades = Nacionalidad::all();
        $estadosCiviles = EstadoCivil::all();
        return view('alumnos.ver_datos', compact('esNuevoRegistro', 'curp', 'datos', 'sexos', 'nacionalidades', 'estadosCiviles'));
    }

    public function nuevoRegistroAlumno($encodeCURP)
    {
        $curp = base64_decode(urldecode($encodeCURP));
        $sexos = Sexo::all();
        $nacionalidades = Nacionalidad::all();
        $estadosCiviles = EstadoCivil::all();

        $esNuevoRegistro = true;
        return view('alumnos.ver_datos', compact('esNuevoRegistro', 'curp', 'sexos', 'nacionalidades', 'estadosCiviles'));
    }

    // * Función que sera llamada desde la vista para obtener los datos del CURP
    public function obtenerDatosCurp($encodeCURP)
    {
        $curp = base64_decode(urldecode($encodeCURP));
        $APIdatosCURP = $this->consultaDatosCURPService->consultarDatosPorCurp($curp);
        if ($APIdatosCURP) {
            return response()->json([
                'success' => true,
                'data' => $APIdatosCURP
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'No se encontraron datos para el CURP proporcionado.'
            ], 404);
        }
    }

    public function guardarSeccionAlumno(Request $request)
    {
        try {
            $seccion = $request->input('seccion');
            $datos = $request->except(['_token', 'documento_curp']);
            $archivoCurp = $request->file('documento_curp');
            $resultado = $this->guardarSeccionService->obtenerSeccion($seccion, $datos, $archivoCurp);
            if ($resultado) {
                return response()->json(['success' => true, 'message' => 'Datos del alumno guardados correctamente.']);
            } else {
                return response()->json(['success' => false, 'error' => 'No se pudo guardar los datos del alumno.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error al guardar los datos del alumno: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error al guardar los datos del alumno.'], 500);
        }
    }
}
