<?php

namespace App\Http\Controllers\Alumno;

use App\Models\pais;
use App\Models\Sexo;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\GradoEstudio;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use App\Models\GrupoVulnerable;
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
        $entidades = $estados;
        $municipios = Municipio::all();
        $gradoEstudios = GradoEstudio::all();
        $gruposVulnerables = GrupoVulnerable::orderBy('grupo_vulnerable')->get();

        $secciones = $datos->estatus[0];
        $viewData = compact('esNuevoRegistro', 'curp', 'datos', 'sexos', 'nacionalidades', 'estadosCiviles', 'paises', 'estados', 'entidades', 'municipios', 'gradoEstudios', 'gruposVulnerables', 'secciones');
        # checar la si hay una variable de sesión
        if (session()->has('grupo_id')) {
            // asignar la variable de session a compact
            $viewData['grupoId'] = session('grupo_id');
        }
        return view('alumnos.ver_datos', $viewData);
    }

    public function nuevoRegistroAlumno($encodeCURP, $grupoId = null)
    {
        $curp = base64_decode(urldecode($encodeCURP));
        $existeRegistroAlumno = $this->alumnoConsultaService->obtenerAlumnoPorCURP($curp);
        $encodeCURP = urlencode(base64_encode($curp));
        if ($existeRegistroAlumno) {
            return redirect()->route('alumnos.ver.registro.alumno', $encodeCURP); // * Redirige al método verRegistroAlumno
        }
        $sexos = Sexo::all();
        $nacionalidades = Nacionalidad::all();
        $estadosCiviles = EstadoCivil::all();
        $paises = pais::all();
        $estados = Estado::all();
        $entidades = $estados;
        $municipios = Municipio::all();
        $gradoEstudios = GradoEstudio::all();
        $gruposVulnerables = GrupoVulnerable::orderBy('grupo_vulnerable')->get();

        $esNuevoRegistro = true;
        $secciones = []; // Para nuevo registro, no hay secciones completadas
        $viewData = compact('esNuevoRegistro', 'curp', 'sexos', 'nacionalidades', 'estadosCiviles', 'paises', 'estados', 'entidades', 'municipios', 'gradoEstudios', 'gruposVulnerables', 'secciones');
        // Si no hay grupo_id en sesión y $grupoId no es nulo, guardar en sesión
        if (!empty($grupoId)) {
            # si la variable no está vacia checar si no se encuentra la variable de sesión vacia
            if (!session()->has('grupo_id')) {
                session(['grupo_id' => base64_decode($grupoId)]);
                // se crea variable de sesión y se asigna al compact
                $viewData['grupoId'] = session('grupo_id');
            }
        } else {
            # checar la si hay una variable de sesión
            if (session()->has('grupo_id')) {
                // asignar la variable de session a compact
                $viewData['grupoId'] = session('grupo_id');
            }
        }
        return view('alumnos.ver_datos', $viewData);
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
                $estatusResult = $this->actualizarEstatusService->actualizarAlumnoEstatus($alumnoId, 1, $seccion); // * 1 Es En Captura
                $response = ['success' => true, 'message' => 'Datos del alumno guardados correctamente.'];
                if (is_array($estatusResult) && isset($estatusResult['finalizado']) && $estatusResult['finalizado'] === true) {
                    $response['finalizado'] = true;
                }
                return response()->json($response);
            } else {
                return response()->json(['success' => false, 'error' => 'No se pudo guardar los datos del alumno.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error al guardar los datos del alumno: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error al guardar los datos del alumno.'], 500);
        }
    }
}
