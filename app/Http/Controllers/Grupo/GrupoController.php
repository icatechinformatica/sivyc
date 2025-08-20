<?php

namespace App\Http\Controllers\Grupo;

use App\Agenda;
use Carbon\Carbon;
use App\Models\curso;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Unidad;
use App\Models\Estatus;
use App\Models\localidad;
use App\Models\Municipio;
use Illuminate\Http\Request;
use App\Models\ServicioCurso;
use App\Models\ModalidadCurso;
use App\Models\ImparticionCurso;
use App\Models\organismosPublicos;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Grupo\GrupoEstatusService;
use App\Services\Grupo\GrupoService;
use Google\Service\ServiceControl\Auth;
use Illuminate\Support\Facades\Validator;

class GrupoController extends Controller
{

    protected $grupoService;
    protected $grupoEstatusService;

    public function __construct(GrupoService $grupoService, GrupoEstatusService $grupoEstatusService)
    {
        $this->grupoService = $grupoService;
        $this->grupoEstatusService = $grupoEstatusService;
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
        $grupo = $this->grupoService->obtenerGrupoPorId($id);
        $esNuevoRegistro = false;
        $cursos = curso::limit(100)->get();
        $tiposImparticion = ImparticionCurso::all();
        $modalidades = ModalidadCurso::all();
        $unidadUsuario = auth()->user()->unidad;
        $unidad_disponible = $unidadUsuario?->unidad;
        $unidades = Unidad::where('ubicacion', $unidad_disponible)->get();
        $servicios = ServicioCurso::all();
        $localidades = localidad::where('clave_municipio', $grupo->id_municipio)->get();
        $municipios = Municipio::where('id_estado', 7)->get(); // CHIAPAS FIJO
        $organismos_publicos = organismosPublicos::orderBy('organismo', 'asc')->get();
        $ultimoEstatus = $grupo->estatusActual();
        $ultimaSeccion = $grupo->seccion_captura ?? null;
        $compactObject = compact('tiposImparticion', 'grupo', 'modalidades',  'cursos',  'unidades',  'municipios',  'servicios',  'localidades',  'organismos_publicos',  'esNuevoRegistro',  'ultimoEstatus', 'ultimaSeccion');

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

        // POST: asignar por CURP
        $validator = Validator::make($request->all(), [
            'grupo_id' => 'required|exists:tbl_grupos,id',
            'curp' => 'required|string|size:18',
        ], [
            'curp.size' => 'La CURP debe tener 18 caracteres.'
        ]);

        if ($validator->fails()) {
            $gid = $request->input('grupo_id');
            return redirect()->route('grupos.editar', $gid)
                ->withErrors($validator)
                ->withInput();
        }

        $grupoId = (int) $request->input('grupo_id');
        $curp = strtoupper(trim($request->input('curp')));

        $grupo = Grupo::find($grupoId);
        if (!$grupo) {
            return redirect()->route('grupos.editar', $grupoId)
                ->with('error', 'Grupo no encontrado.');
        }

        // verifica la existencia del alumno por CURP

        $alumno = Alumno::where('curp', $curp)->first();
        if (!$alumno) {
            return redirect()->route('grupos.editar', $grupoId)
                ->with('error', 'Alumno no encontrado con la CURP proporcionada.')
                ->with('curp', $curp)
                ->with('bandera', true)
                ->with('grupo_id', $grupoId);
        }

        // Evitar duplicados
        $existe = $grupo->alumnos()->where('tbl_alumnos.id', $alumno->id)->exists();
        if ($existe) {
            return redirect()->route('grupos.editar', $grupoId)
                ->with('info', 'El alumno ya está asignado a este grupo.');
        }

        try {
            $grupo->alumnos()->attach($alumno->id);
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
            return redirect()->route('grupos.editar', $grupoId)
                ->with('error', 'No se pudo agregar el alumno al grupo.');
        }
    }

    /**
     * Eliminar un alumno del grupo
     */
    public function eliminarAlumno(Grupo $grupo, Alumno $alumno)
    {
        try {
            $grupo->alumnos()->detach($alumno->id);
            return redirect()->route('grupos.editar', $grupo->id)
                ->with('success', 'Alumno eliminado del grupo.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar alumno del grupo', [
                'grupo_id' => $grupo->id,
                'alumno_id' => $alumno->id,
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
            $eventos = $grupo->fechasAgenda()->get()->map(function ($item) {
                $start = Carbon::parse($item->fecha_inicio . ' ' . $item->hora_inicio);
                $end = Carbon::parse($item->fecha_fin . ' ' . $item->hora_fin);
                return [
                    'id' => $item->id,
                    'title' => 'Sesión',
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                ];
            });
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
            $data = $request->only(['start', 'end']);
            $validator = Validator::make($data, [
                'start' => 'required|date',
                'end' => 'required|date|after:start',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
            }

            $start = Carbon::parse($data['start']);
            $end = Carbon::parse($data['end']);

            // Caso: si start y end caen en el mismo día, es un periodo de 1 día.
            // Si no, se considera un periodo multi-día, y guardaremos un solo registro desde start hasta end.

            // Validar traslape (end exclusivo)
            $existeTraslape = Agenda::where('id_grupo', $grupo->id)
                ->where(function ($q) use ($start, $end) {
                    $q->whereRaw("CONCAT(fecha_inicio, ' ', hora_inicio) < ?", [$end->format('Y-m-d H:i:s')])
                        ->whereRaw("CONCAT(fecha_fin, ' ', hora_fin) > ?", [$start->format('Y-m-d H:i:s')]);
                })
                ->exists();

            if ($existeTraslape) {
                return response()->json(['message' => 'El horario seleccionado se traslapa con otro existente.'], 422);
            }

            $agenda = Agenda::create([
                'id_grupo' => $grupo->id,
                'fecha_inicio' => $start->toDateString(),
                'hora_inicio' => $start->format('H:i:s'),
                'fecha_fin' => $end->toDateString(),
                'hora_fin' => $end->format('H:i:s'),
            ]);

            return response()->json([
                'id' => $agenda->id,
                'title' => 'Sesión',
                'start' => Carbon::parse($agenda->fecha_inicio . ' ' . $agenda->hora_inicio)->toIso8601String(),
                'end' => Carbon::parse($agenda->fecha_fin . ' ' . $agenda->hora_fin)->toIso8601String(),
            ], 201);
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

            $data = $request->only(['start', 'end']);
            $validator = Validator::make($data, [
                'start' => 'required|date',
                'end' => 'required|date|after:start',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
            }

            $start = Carbon::parse($data['start']);
            $end = Carbon::parse($data['end']);

            // Validar traslape con otros eventos, excluyendo el actual
            $existeTraslape = Agenda::where('id_grupo', $grupo->id)
                ->where('id', '<>', $agenda->id)
                ->where(function ($q) use ($start, $end) {
                    $q->whereRaw("CONCAT(fecha_inicio, ' ', hora_inicio) < ?", [$end->format('Y-m-d H:i:s')])
                        ->whereRaw("CONCAT(fecha_fin, ' ', hora_fin) > ?", [$start->format('Y-m-d H:i:s')]);
                })
                ->exists();

            if ($existeTraslape) {
                return response()->json(['message' => 'El horario seleccionado se traslapa con otro existente.'], 422);
            }

            $agenda->update([
                'fecha_inicio' => $start->toDateString(),
                'hora_inicio' => $start->format('H:i:s'),
                'fecha_fin' => $end->toDateString(),
                'hora_fin' => $end->format('H:i:s'),
            ]);

            return response()->json(['message' => 'Actualizado']);
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
            $agenda->delete();
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
