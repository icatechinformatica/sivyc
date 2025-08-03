<?php

namespace App\Http\Controllers\Alumno;

use App\Models\pais;
use App\Models\Sexo;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\GradoEstudio;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ConsultaDatosCURPService;
use App\Models\estado_civil as EstadoCivil;
use App\Services\Alumno\AlumnoConsultaService;
use App\Services\Alumno\GuardarSeccionService;
use App\Services\Estatus\ActualizarEstatusService;

class AlumnoController extends Controller
{
    protected $alumnoConsultaService;
    protected $consultaDatosCURPService;
    protected $registroTempService;
    protected $guardarSeccionService;
    protected $actualizarEstatusService;

    public function __construct(AlumnoConsultaService $alumnoConsultaService, ConsultaDatosCURPService $consultaDatosCURPService, GuardarSeccionService $guardarSeccionService, ActualizarEstatusService $actualizarEstatusService)
    {
        $this->alumnoConsultaService = $alumnoConsultaService;
        $this->consultaDatosCURPService = $consultaDatosCURPService;
        $this->guardarSeccionService = $guardarSeccionService;
        $this->actualizarEstatusService = $actualizarEstatusService;
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
        $paises = pais::all();
        $estados = Estado::all();
        $municipios = Municipio::all();
        $gradoEstudios = GradoEstudio::all();

        // dd($datos->gradoEstudio); // * Para depuración, eliminar en producción

        return view('alumnos.ver_datos', compact('esNuevoRegistro', 'curp', 'datos', 'sexos', 'nacionalidades', 'estadosCiviles', 'paises', 'estados', 'municipios', 'gradoEstudios'));
    }

    public function nuevoRegistroAlumno($encodeCURP)
    {
        $curp = base64_decode(urldecode($encodeCURP));
        $sexos = Sexo::all();
        $nacionalidades = Nacionalidad::all();
        $estadosCiviles = EstadoCivil::all();
        $paises = pais::all();
        $estados = Estado::all();
        $municipios = Municipio::all();
        $gradoEstudios = GradoEstudio::all();

        $esNuevoRegistro = true;
        return view('alumnos.ver_datos', compact('esNuevoRegistro', 'curp', 'sexos', 'nacionalidades', 'estadosCiviles', 'paises', 'estados', 'municipios', 'gradoEstudios'));
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

            $archivo = $seccion === 'datos_personales' ? $request->file('documento_curp') : ($seccion === 'capacitacion' ? $request->file('documento_ultimo_grado') : ($seccion === 'cerss' ? $request->file('documento_ficha_cerss') : null));

            $alumno = $this->guardarSeccionService->obtenerSeccion($seccion, $datos, $archivo);
            if ($alumno) {
                $alumnoId = $alumno->id;
                $this->actualizarEstatusService->actualizarAlumnoEstatus($alumnoId, 1, $seccion); // * 1 Es En Captura
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
