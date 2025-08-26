<?php

namespace App\Http\Controllers\Grupo;

use App\Agenda;
use Carbon\Carbon;
use App\Models\curso;
use App\Models\Grupo;
use App\Models\Alumno;
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
use App\Services\Grupo\AgendaService;
use Illuminate\Support\Facades\Validator;
use App\Services\Unidades\UnidadesService;
use App\Services\Grupo\GrupoEstatusService;
use App\Services\Municipio\MunicipioService;

class GrupoController extends Controller
{

    protected $grupoService;
    protected $grupoEstatusService;
    protected $unidadesService;
    protected $municipiosService;
    protected $agendaService;

    public function __construct(GrupoService $grupoService, GrupoEstatusService $grupoEstatusService, UnidadesService $unidadesService, MunicipioService $municipiosService, AgendaService $agendaService)
    {
        $this->grupoService = $grupoService;
        $this->grupoEstatusService = $grupoEstatusService;
        $this->unidadesService = $unidadesService;
        $this->municipiosService = $municipiosService;
        $this->agendaService = $agendaService;
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

    public function editarGrupo($id, $curp = null)
    {
        $esNuevoRegistro = false;
        $grupo = $this->grupoService->obtenerGrupoPorId($id);

        $cursos = curso::limit(100)->get(); // ! Variable que se pasa al BLade
        $tiposImparticion = ImparticionCurso::all(); // ? Variable que se pasa al BLade
        $modalidades = ModalidadCurso::all(); // ? Variable que se pasa al BLade
        $servicios = ServicioCurso::all();
        $localidades = localidad::where('clave_municipio', $grupo->id_municipio)->get();
        $organismos_publicos = organismosPublicos::orderBy('organismo', 'asc')->get();

        $unidades = $this->unidadesService->obtenerUnidadesPorUsuario();
        $municipios = $this->municipiosService->municipiosPorUnidadDisponible($grupo);

        $ultimoEstatus = $grupo->estatusActual();
        $ultimaSeccion = $grupo->seccion_captura ?? null;

        $compactObject = compact('grupo', 'tiposImparticion', 'modalidades',  'cursos',  'unidades',  'municipios',  'servicios',  'localidades',  'organismos_publicos',  'esNuevoRegistro',  'ultimoEstatus', 'ultimaSeccion');

        if (!empty($curp)) {
            # si no está vacio el grupo procedemos a cargarlo en el compact
            $uncodeCurp = base64_decode($curp);
            $compactObject['uncodeCurp'] = $uncodeCurp;
        }
        return view('grupos.create', $compactObject);
    }

    public function create(Request $request)
    {

        if ($request->id) {
            return redirect()->route('grupos.editar', $request->id);
        }
        $cursos = curso::limit(100)->get();
        $tiposImparticion = ImparticionCurso::all();
        $modalidades = ModalidadCurso::all();
        $unidadUsuario = auth()->user()->unidad;
        $unidad_disponible = $unidadUsuario?->unidad;
        $unidades = Unidad::where('ubicacion', $unidad_disponible)->get();
        $servicios = ServicioCurso::all();

        $municipios = Municipio::where('id_estado', 7)->get(); // CHIAPAS FIJO
        $localidades = []; // Inicialmente vacío
        $esNuevoRegistro = true;
        $organismos_publicos = organismosPublicos::orderBy('organismo', 'asc')->get();
        $ultimaSeccion = null; // No hay avance todavía
        return view('grupos.create', compact('tiposImparticion', 'modalidades', 'cursos', 'unidades', 'municipios', 'servicios', 'localidades', 'organismos_publicos', 'esNuevoRegistro', 'ultimaSeccion'));
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

    /**
     * Devuelve los municipios disponibles para la unidad seleccionada (por nombre de unidad)
     */
    public function getMunicipiosByUnidad(Request $request)
    {
        try {
            $nombreUnidad = $request->query('unidad');
            $municipios = $this->municipiosService->municipiosPorNombreUnidad($nombreUnidad);
            return response()->json($municipios);
        } catch (\Exception $e) {
            Log::error('Error al obtener municipios por unidad: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener municipios'], 500);
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

            if (!$grupo) {
                return response()->json(['error' => 'No se pudo guardar la sección del grupo'], 500);
            }

            if ($grupo instanceof \Illuminate\Http\JsonResponse) {
                return $grupo;
            }

            $grupo_id = $grupo->id;
            return response()->json(['success' => true, 'message' => 'Datos del grupo guardados correctamente.', 'grupo_id' => $grupo_id]);
        } catch (\Exception $e) {
            Log::error('Error al guardar sección de grupo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al guardar sección'], 500);
        }
    }

    public function asignarAlumnos(Request $request)
    {
        // Vista simple opcional si es GET
        if (!$request->isMethod('post')) {
            return view('grupos.asignar_alumnos');
        }

        $grupoId = $request->input('grupo_id');
        $curp = strtoupper(trim($request->input('curp')));

        // * Verificar la existencia del grupo 
        $grupo = Grupo::find($grupoId);
        if (!$grupo) {
            return redirect()->route('grupos.editar', $grupoId)->with('error', 'Grupo no encontrado.');
        }

        // * Verifica la existencia del alumno por CURP 
        $alumno = Alumno::where('curp', $curp)->first();
        if (!$alumno) {
            return redirect()->route('grupos.editar', $grupoId)
                ->with('error', 'Alumno no encontrado con la CURP proporcionada.')
                ->with('curp', $curp)
                ->with('bandera', true)
                ->with('grupo_id', $grupoId);
        }

        // * Verificar que el registro del alumno este completo

        if (!$alumno->registroCompleto()) {
            return redirect()->route('grupos.editar', $grupoId)->with('error', 'El registro del alumno no está completo.');
        }

        // * Verificar que el alumno tenga 15 años a la fecha de inicio del grupo.
        $fechaInicioGrupo = $grupo->fecha_inicio();
        $fechaReferencia = $fechaInicioGrupo ? Carbon::parse($fechaInicioGrupo) : Carbon::now();
        $edadAlumno = Carbon::parse($alumno->fecha_nacimiento)->diffInYears($fechaReferencia);

        if ($edadAlumno < 15) {
            return redirect()->route('grupos.editar', $grupoId)
                ->with('error', 'El alumno debe tener al menos 15 años para ser asignado a este grupo.');
        }

        // * Evitar duplicados 
        $existe = $grupo->alumnos()->where('tbl_alumnos.id', $alumno->id)->exists();
        if ($existe) {
            return redirect()->route('grupos.editar', $grupoId)->with('info', 'El alumno ya está asignado a este grupo.');
        }

        try {

            $idsGruposVulnerables = $alumno->gruposVulnerables()->pluck('grupo_vulnerable_id')->toArray();

            $grupo->alumnos()->attach($alumno->id, [
                'id_ultimo_grado' => $alumno->id_ultimo_grado_estudios,
                'grupos_vulnerables' => json_encode($idsGruposVulnerables),
                'medio_entero' => $alumno->medio_entero,
                'medio_confirmacion' => $alumno->medio_confirmacion,
            ]);
            // eliminar variable de sesión si existe
            session()->forget('grupo_id');
            // Actualiza estatus de sección
            // $this->grupoService->actualizarEstatusGrupo($grupoId, 'alumnos'); // ! PENDIENTE REVISAR
            return redirect()->route('grupos.editar', $grupoId)
                ->with('success', 'Alumno agregado al grupo.');
        } catch (\Throwable $e) {
            Log::error('Error asignando alumno al grupo', [
                'grupo_id' => $grupoId,
                'alumno_id' => $alumno->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('grupos.editar', $grupoId)->with('error', 'No se pudo agregar el alumno al grupo.');
        }
    }

    /**
     * Eliminar un alumno del grupo
     */
    public function eliminarAlumno(Request $request, $grupo_id)
    {
        try {
            $grupo = Grupo::findOrFail($grupo_id);
            $alumno = Alumno::findOrFail($request->input('alumno_id'));
            $grupo->alumnos()->detach($alumno->id);
            return redirect()->route('grupos.editar', $grupo->id)->with('success', 'Alumno eliminado del grupo.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar alumno del grupo', [
                'grupo_id' => $grupo->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('grupos.editar', $grupo->id)
                ->with('error', 'No se pudo eliminar el alumno del grupo.');
        }
    }

    /**
     * Obtener agenda (eventos) del grupo en formato FullCalendar
     */
    public function getAgenda(Grupo $grupo)
    {
        try {
            $eventos = $this->agendaService->obtenerEventosFullcalendar($grupo->id);
            return response()->json($eventos);
        } catch (\Exception $e) {
            Log::error('Error al obtener agenda: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener agenda'], 500);
        }
    }

    /**
     * Crear un evento de agenda para el grupo
     */
    public function storeAgenda(Request $request, Grupo $grupo)
    {
        // return response()->json(['message' => 'Hora inicio: ' . $request->input('start') . ' Hora fin: ' . $request->input('end')], 201);
        try {
            $data = $request->only(['start', 'end', 'hora_alimentos']);
            $validator = Validator::make($data, [
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'hora_alimentos' => 'sometimes|boolean',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
            }

            $start = Carbon::parse($data['start']);
            $end = Carbon::parse($data['end']);
            $horaAlimentos = (bool) ($data['hora_alimentos'] ?? false);

            $agenda = $this->agendaService->crear($grupo->id, $start, $end, $horaAlimentos);

            return response()->json([
                'id' => $agenda->id,
                'title' => 'Sesión',
                'start' => Carbon::parse($agenda->fecha_inicio . ' ' . $agenda->hora_inicio)->toIso8601String(),
                'end' => Carbon::parse($agenda->fecha_fin . ' ' . $agenda->hora_fin)->toIso8601String(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear evento de agenda: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear evento'], 500);
        }
    }

    /**
     * Actualizar un evento de agenda (drag/resize)
     */
    public function updateAgenda(Request $request, Grupo $grupo, Agenda $agenda)
    {
        try {
            if ($agenda->id_grupo !== $grupo->id) {
                return response()->json(['message' => 'No encontrado'], 404);
            }

            $data = $request->only(['start', 'end', 'hora_alimentos']);
            $validator = Validator::make($data, [
                'start' => 'required|date',
                'end' => 'required|date|after:start',
                'hora_alimentos' => 'sometimes|boolean',
            ]);



            if ($validator->fails()) {
                return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
            }

            $start = Carbon::parse($data['start']);
            $end = Carbon::parse($data['end']);
            $horaAlimentos = (bool) ($data['hora_alimentos'] ?? false);

            $this->agendaService->actualizar($agenda->id, $grupo->id, $start, $end, $horaAlimentos);

            return response()->json(['message' => 'Actualizado']);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar evento de agenda: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar evento'], 500);
        }
    }

    /**
     * Eliminar un evento de agenda
     */
    public function destroyAgenda(Grupo $grupo, Agenda $agenda)
    {
        try {
            if ($agenda->id_grupo !== $grupo->id) {
                return response()->json(['message' => 'No encontrado'], 404);
            }
            $this->agendaService->eliminar($agenda->id);
            return response()->json(['message' => 'Eliminado']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar evento de agenda: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar evento'], 500);
        }
    }

    public function turnarGrupo(Request $request)
    {
        try {

            $grupo = Grupo::find($request->grupo_id);
            $nuevo_estatus_id = $request->estatus_id;

            if (!$grupo) {
                return response()->json(['message' => 'Grupo no encontrado'], 404);
            }

            // Retornar la respuesta correspondiente del servicio (incluye códigos 200/400/404)
            $seccion = $request->input('seccion');
            return $this->grupoEstatusService->cambiarEstatus($grupo, (int) $nuevo_estatus_id, $seccion);
        } catch (\Exception $e) {
            Log::error('Error al turnar grupo: ' . $e->getMessage());
            return response()->json(['message' => 'Error al turnar grupo'], 500);
        }
    }
}
