<?php

namespace App\Http\Controllers\Grupo;

use App\Models\curso;
use App\Models\Unidad;
use App\Models\localidad;
use App\Models\Municipio;
use Illuminate\Http\Request;
use App\Models\ServicioCurso;
use App\Models\ModalidadCurso;
use App\Models\ImparticionCurso;
use App\Models\organismosPublicos;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Grupo\GrupoService;

class GrupoController extends Controller
{

    protected $grupoService;

    public function __construct(GrupoService $grupoService)
    {
        $this->grupoService = $grupoService;
    }

    public function index(Request $request)
    {

        try {
            $registrosPorPagina = $request->get('per_page', 15);
            $busqueda = $request->get('busqueda');
            $grupos = $this->grupoService->obtenerGrupos($registrosPorPagina, $busqueda);
            return view('grupos.index', compact('grupos'));
        } catch (\Exception $e) {
            Log::error('Error al obtener grupos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los grupos'], 500);
        }
    }

    public function editarGrupo($id)
    {
        $grupo = $this->grupoService->obtenerGrupoPorId($id);
        $esNuevoRegistro = false;
        $cursos = curso::limit(100)->get();
        $tiposImparticion = ImparticionCurso::all();
        $modalidades = ModalidadCurso::all();
        $unidades = Unidad::all();
        $servicios = ServicioCurso::all();
        $localidades = localidad::where('clave_municipio', $grupo->id_municipio)->get();
        $municipios = Municipio::where('id_estado', 7)->get(); // CHIAPAS FIJO
        $organismos_publicos = organismosPublicos::orderBy('organismo', 'asc')->get();
        // dd($grupo);
        return view('grupos.create', compact('tiposImparticion', 'grupo', 'modalidades', 'cursos', 'unidades', 'municipios', 'servicios', 'localidades', 'organismos_publicos', 'esNuevoRegistro'));
    }

    public function create(Request $request)
    {

        if ($request->id) {
            return redirect()->route('grupos.editar', $request->id);
        }
        $cursos = curso::limit(100)->get();
        $tiposImparticion = ImparticionCurso::all();
        $modalidades = ModalidadCurso::all();
        $unidades = Unidad::all();
        $servicios = ServicioCurso::all();

        $municipios = Municipio::where('id_estado', 7)->get(); // CHIAPAS FIJO
        $localidades = []; // Inicialmente vacío
        $esNuevoRegistro = true;
        $organismos_publicos = organismosPublicos::orderBy('organismo', 'asc')->get();

        return view('grupos.create', compact('tiposImparticion', 'modalidades', 'cursos', 'unidades', 'municipios', 'servicios', 'localidades', 'organismos_publicos', 'esNuevoRegistro'));
    }

    public function getLocalidades($municipioId)
    {
        try {
            $localidades = localidad::where('clave_municipio', $municipioId)->get();
            return response()->json($localidades);
        } catch (\Exception $e) {
            Log::error('Error al obtener localidades: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener localidades'], 500);
        }
    }

    public function getOrganismoInfo($organismoId)
    {
        try {
            $organismo = organismosPublicos::select('nombre_titular', 'telefono')
                ->where('id', $organismoId)
                ->where('activo', true)
                ->first();

            if ($organismo) {
                return response()->json([
                    'nombre_titular' => $organismo->nombre_titular,
                    'telefono' => $organismo->telefono
                ]);
            } else {
                return response()->json([
                    'nombre_titular' => '',
                    'telefono' => ''
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener información del organismo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener información del organismo'], 500);
        }
    }

    public function guardarSeccionGrupo(Request $request)
    {
        try {
            $seccion = $request->input('seccion');
            $datos = $request->except('_token');

            $id_grupo = $request->input('id_grupo') ?? null;

            $grupo = $this->grupoService->obtenerSeccion($seccion, $datos, $id_grupo);

            if ($grupo) {
                $grupo_id = $grupo->id;
                return response()->json(['success' => true, 'message' => 'Datos del grupo guardados correctamente.', 'grupo_id' => $grupo_id]);
            } else {
                return response()->json(['error' => 'No se pudo guardar la sección del grupo'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error al guardar sección de grupo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al guardar sección'], 500);
        }
    }

    /**
     * Obtener el historial de estatus de un grupo
     */
    public function obtenerHistorialEstatus($id)
    {
        try {
            $historialEstatus = $this->grupoService->obtenerHistorialEstatus($id);
            return response()->json([
                'success' => true,
                'historial' => $historialEstatus
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener historial de estatus: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener historial de estatus'], 500);
        }
    }

    /**
     * Obtener el estatus actual de un grupo
     */
    public function obtenerEstatusActual($id)
    {
        try {
            $estatusActual = $this->grupoService->obtenerEstatusActual($id);
            return response()->json([
                'success' => true,
                'estatus_actual' => $estatusActual
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estatus actual: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener estatus actual'], 500);
        }
    }

    public function store()
    {
        dd('Registrando grupo...');
    }

    public function asignarAlumnos()
    {
        return view('grupos.asignar_alumnos');
    }
}
