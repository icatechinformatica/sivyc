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
use App\Models\Grupo;
use App\Agenda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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

        $ultimoEstatus = $grupo->estatus()->orderBy('fecha_cambio', 'desc')->first();
        $ultimaSeccion = $ultimoEstatus ? $ultimoEstatus->pivot->seccion : null;
        return view('grupos.create', compact('tiposImparticion', 'grupo', 'modalidades',  'cursos',  'unidades',  'municipios',  'servicios',  'localidades',  'organismos_publicos',  'esNuevoRegistro',  'ultimoEstatus', 'ultimaSeccion'));
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

            $grupo_id = $grupo->id;
            return response()->json(['success' => true, 'message' => 'Datos del grupo guardados correctamente.', 'grupo_id' => $grupo_id]);
        } catch (\Exception $e) {
            Log::error('Error al guardar sección de grupo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al guardar sección'], 500);
        }
    }

    public function asignarAlumnos()
    {
        return view('grupos.asignar_alumnos');
    }

    /**
     * Obtener agenda (eventos) del grupo en formato FullCalendar
     */
    public function getAgenda(Grupo $grupo)
    {
        try {
            $eventos = $grupo->fechasAgenda()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => 'Sesión',
                    'start' => Carbon::parse($item->fecha_inicio)->toIso8601String(),
                    'end' => Carbon::parse($item->fecha_fin)->toIso8601String(),
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

            // Validar traslape (end exclusivo)
            $existeTraslape = Agenda::where('id_grupo', $grupo->id)
                ->where(function ($q) use ($start, $end) {
                    $q->where('fecha_inicio', '<', $end)
                      ->where('fecha_fin', '>', $start);
                })
                ->exists();

            if ($existeTraslape) {
                return response()->json(['message' => 'El horario seleccionado se traslapa con otro existente.'], 422);
            }

            $agenda = Agenda::create([
                'id_grupo' => $grupo->id,
                'fecha_inicio' => $start,
                'fecha_fin' => $end,
            ]);

            return response()->json([
                'id' => $agenda->id,
                'title' => 'Sesión',
                'start' => $agenda->fecha_inicio,
                'end' => $agenda->fecha_fin,
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
                    $q->where('fecha_inicio', '<', $end)
                      ->where('fecha_fin', '>', $start);
                })
                ->exists();

            if ($existeTraslape) {
                return response()->json(['message' => 'El horario seleccionado se traslapa con otro existente.'], 422);
            }

            $agenda->update([
                'fecha_inicio' => $start,
                'fecha_fin' => $end,
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
}
