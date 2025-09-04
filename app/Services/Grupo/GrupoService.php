<?php

namespace App\Services\Grupo;

use App\Models\Grupo;
use App\Models\Unidad;
use App\Models\Estatus;
use App\Models\Municipio;
use App\Repositories\GrupoRepository;
use Illuminate\Support\Facades\Crypt;
use App\Interfaces\Repositories\GrupoRepositoryInterface;

class GrupoService
{

    protected $ordenSecciones = ['info_general', 'ubicacion', 'organismo', 'opciones', 'agenda', 'alumnos'];

    public function __construct(private GrupoRepositoryInterface $grupoRepository)
    {
        $this->grupoRepository = $grupoRepository;
    }

    public function obtenerGrupos($registrosPorPagina = 15, $busqueda = null)
    {
        $usuario = auth()->user();
        $tieneAllAccess = false;
        if ($usuario) {
            $roles = $usuario->roles ?? collect();
            // Si es colección (N:M), buscar alguno con especial='all-access'
            if ($roles instanceof \Illuminate\Support\Collection) {
                $tieneAllAccess = $roles->contains('especial', 'all-access');
            } elseif (is_object($roles)) { // Por si fuera 1:1 en algún caso
                $tieneAllAccess = ($roles->especial ?? null) === 'all-access';
            }
        }

        if ($tieneAllAccess or is_null($usuario->unidad)) {
            if ($busqueda) {
                return $this->grupoRepository->buscarPaginado($busqueda, $registrosPorPagina);
            }

            return $this->grupoRepository->obtenerTodos($registrosPorPagina);
        }

        if ($busqueda) {
            return $this->grupoRepository->buscarPaginadoPorUnidad($busqueda, $registrosPorPagina);
        }

        return $this->grupoRepository->obtenerTodosPorUnidad($registrosPorPagina);
    }

    public function obtenerCursosDisponibles($id_imparticion, $id_modalidad, $id_servicio, $id_unidad)
    {
        return $this->grupoRepository->obtenerCursosDisponibles($id_imparticion, $id_modalidad, $id_servicio, $id_unidad);
    }

    public function obtenerGrupoPorId($id)
    {
        return $this->grupoRepository->obtenerPorId($id);
    }

    public function obtenerSeccion($seccion, $datos, $id_grupo = null)
    {
        switch ($seccion) {
            case 'info_general':
                return $this->guardarInfoGeneral($datos, $id_grupo);
            case 'ubicacion':
                return $this->guardarUbicacion($datos, $id_grupo);
            case 'organismo':
                return $this->guardarOrganismo($datos, $id_grupo);
            case 'opciones':
                return $this->guardarOpciones($datos, $id_grupo);
            case 'agenda':
                return $this->guardarAgenda($datos, $id_grupo);
            default:
                throw new \Exception('Sección no reconocida');
        }
    }

    public function guardarInfoGeneral($datos, $id_grupo = null)
    {
        $infoGeneral = [
            'id_tipo_curso' => $datos['id_tipo_curso'],
            'id_modalidad_curso' => $datos['id_modalidad_curso'],
            'id_unidad' => $datos['id_unidad'],
            'id_categoria_formacion' => $datos['id_categoria_formacion'],
            'id_curso' => $datos['id_curso'],
        ];

        if ($id_grupo) {
            $infoGeneral['id'] = $id_grupo;
        }

        // Avanza seccion_captura solo si corresponde
        $infoGeneral['seccion_captura'] = $this->obtenerSeccionCaptura($id_grupo, 'info_general');

        $grupo = $this->grupoRepository->actualizarOrCrear($infoGeneral);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'EN CAPTURA');
        $this->generarFolio($id_grupo);
        return $grupo;
    }

    public function exportarAlumnosGrupo($grupoAlumnos_id, $id_grupo)
    {
        $grupoAlumnos_id = Crypt::decryptString($grupoAlumnos_id);
        $grupoOrigen = Grupo::find($grupoAlumnos_id);
        $grupoDestino = Grupo::find($id_grupo);

        if (!$grupoOrigen || !$grupoDestino) {
            throw new \Exception('Grupo origen o destino no encontrado');
        }

        $alumnos = $grupoOrigen->alumnos;

        foreach ($alumnos as $alumno) {
            // Evitar duplicados
            if (!$grupoDestino->alumnos->contains($alumno->id)) {
                $grupoDestino->alumnos()->attach($alumno->id);
            }
        }
    }

    public function guardarUbicacion($datos, $id_grupo = null)
    {
        $ubicacion = [
            'id_municipio' => $datos['id_municipio'],
            'id_localidad' => $datos['id_localidad'],
        ];

        if ($id_grupo) {
            $ubicacion['id'] = $id_grupo;
        }

        $municipio = Municipio::find($datos['id_municipio']);
        $estado = $municipio ? $municipio->estado : null;

        // * efisico esta construido de la siguiente manera:
        // * Lugar, Colonia, Calle y Número, Código Postal, Municipio, Estado y Referencias adicionales.
        $ubicacion['efisico'] = $datos['nombre_lugar'] . ', ' . $datos['colonia'] . ', ' .  $datos['calle_numero']  . ', ' .  'C.P.' . ' ' . $datos['codigo_postal'] . ', ' .  ($municipio ? $municipio->muni : '') . ', ' .  ($estado ? $estado->nombre : '') .  ', ' . ($datos['referencias'] ?? '') . '.';

        // Avanza seccion_captura solo si corresponde
        $ubicacion['seccion_captura'] = $this->obtenerSeccionCaptura($id_grupo, 'ubicacion');

        $grupo = $this->grupoRepository->actualizarOrCrear($ubicacion);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'EN CAPTURA');
        return $grupo;
    }

    public function guardarOrganismo($datos, $id_grupo = null)
    {

        $organismo = [
            'id_organismo_publico' => $datos['id_organismo_publico'],
            'organismo_representante' => $datos['organismo_representante'],
            'organismo_telefono_representante' => $datos['organismo_telefono_representante'],
        ];

        if ($id_grupo) {
            $organismo['id'] = $id_grupo;
        }

        // Avanza seccion_captura solo si corresponde
        $organismo['seccion_captura'] = $this->obtenerSeccionCaptura($id_grupo, 'organismo');

        $grupo = $this->grupoRepository->actualizarOrCrear($organismo);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'EN CAPTURA');
        return $grupo;
    }

    public function guardarOpciones($datos, $id_grupo = null)
    {
        $opciones = [
            'cespecifico' => $datos['convenio_especifico'],
            'fecha_cespecifico' => $datos['fecha_convenio'],
        ];

        if ($datos['id_imparticion'] == 2) {
            $opciones['medio_virtual'] = $datos['medio_virtual'];
            $opciones['link_virtual'] = $datos['enlace_virtual'];
        }

        if ($id_grupo) {
            $opciones['id'] = $id_grupo;
        }

        // Avanza seccion_captura solo si corresponde
        $opciones['seccion_captura'] = $this->obtenerSeccionCaptura($id_grupo, 'opciones');

        $grupo = $this->grupoRepository->actualizarOrCrear($opciones);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'EN CAPTURA');
        return $grupo;
    }

    public function guardarAgenda($datos, $id_grupo = null)
    {
        // Corroborar que las horas del grupo sean las definidas por el curso
        $grupo = Grupo::find($id_grupo);
        $horasCurso = $grupo ? $grupo->curso->horas : null;

        if ($grupo->horasTotales() !== $horasCurso) {
            return response()->json(['mensaje' => 'No cubre las horas totales del grupo']);
        }

        // Si cubre las horas, avanza seccion_captura solo si corresponde
        if ($grupo) {
            $this->grupoRepository->actualizarOrCrear([
                'id' => $grupo->id,
                'seccion_captura' => $this->obtenerSeccionCaptura($grupo->id, 'agenda')
            ]);
        }

        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'EN CAPTURA');
        return $grupo?->fresh();
    }

    private function obtenerSeccionCaptura($id_grupo, string $nuevaSeccion)
    {
        // Validar que la nueva sección exista en el orden
        $indiceNueva = array_search($nuevaSeccion, $this->ordenSecciones, true);
        if ($indiceNueva === false) {
            throw new \InvalidArgumentException('Sección no reconocida');
        }

        // Si no hay grupo aún, se inicia con la nueva sección
        if (empty($id_grupo)) {
            return $nuevaSeccion;
        }

        $grupo = Grupo::find($id_grupo);
        $actual = $grupo?->seccion_captura;

        // Si no hay sección actual o no es válida, colocar la nueva
        $indiceActual = $actual ? array_search($actual, $this->ordenSecciones, true) : false;
        if ($indiceActual === false) {
            return $nuevaSeccion;
        }

        // Solo avanzar si la nueva es posterior a la actual
        return $indiceNueva > $indiceActual ? $nuevaSeccion : $actual;
    }

    public function actualizarEstatusGrupo($id_grupo, $estatus)
    {
        // Lógica para actualizar el estatus del grupo
        $grupo = Grupo::find($id_grupo);
        $this->grupoRepository->actualizarEstatus($grupo->id, $estatus);
    }

    public function generarFolio($id_grupo)
    {
        // Folio = CCT-EJERCICIO-CONSECUTIVO
        // Folio = XX-YYZZZZ
        $grupo = Grupo::find($id_grupo);
        if (!$grupo) {
            return null;
        }

        // Si ya tiene folio/clave asignado, no reemplazar
        if (!empty($grupo->clave_grupo)) {
            return $grupo->clave_grupo;
        }

        // Recuperar unidad (relación o búsqueda por id)
        $unidad = $grupo->unidad ?: Unidad::find($grupo->id_unidad);
        if (!$unidad || empty($unidad->cct)) {
            // No es posible generar sin CCT
            return null;
        }

        $cct = substr($unidad->cct, -2);
        $ejercicio = date('y');

        // Prefijo: XX-YY
        $prefijo = strtoupper($cct) . '-' . $ejercicio;

        // Buscar el último folio existente con ese prefijo y obtener su consecutivo
        $ultimo = Grupo::where('clave_grupo', 'like', $prefijo . '%')
            ->orderBy('clave_grupo', 'desc')
            ->first();

        $siguiente = 1;
        if ($ultimo && !empty($ultimo->clave_grupo)) {
            $num = (int) substr($ultimo->clave_grupo, -4);
            $siguiente = $num + 1;
        }

        if ($siguiente > 9999) {
            // Sin espacio para más consecutivos en el ejercicio actual
            throw new \RuntimeException('Se alcanzó el límite de consecutivos para el ejercicio ' . $ejercicio);
        }

        $folio = $prefijo . str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);

        // Asignar y guardar
        $grupo->clave_grupo = $folio;
        $grupo->save();

        return $folio;
    }

    /**
     * Guardar costos de alumnos en el pivote y actualizar id_tipo_exoneracion del grupo.
     * Reglas:
     * - Si alguno tiene costo 0 => EXONERACION (3)
     * - Si la suma de las cuotas de todos los alumnos es menor al costo total del curso => REDUCCION (2)
     * - En otro caso => PAGO ORDINARIO (1)
     * Además: si nadie tiene costo actualmente y se provee cuota_general, usarla para autollenar valores faltantes.
     */
    public function guardarCostosYTipoExoneracion(Grupo $grupo, array $costos, $cuotaGeneral = null)
    {
        // Obtener costos actuales del pivote
        $alumnos = $grupo->alumnos()->get();
        $ningunoTieneCostoActual = $alumnos->every(function ($a) {
            $v = $a->pivot->costo;
            return $v === null || $v === '';
        });

        // Preparar updates
        $updates = [];
        foreach ($alumnos as $alumno) {
            $id = $alumno->id;
            $valor = array_key_exists($id, $costos) ? $costos[$id] : null;

            if ($ningunoTieneCostoActual && ($valor === null || $valor === '')) {
                $valor = $cuotaGeneral;
            }

            if ($valor === '' || $valor === null) {
                continue;
            }

            $updates[$id] = ['costo' => (float) $valor];
        }

        // Persistir cambios de costos
        foreach ($updates as $alumnoId => $valores) {
            $grupo->alumnos()->updateExistingPivot($alumnoId, $valores, false);
        }

        // Recalcular tipo de exoneración del grupo
        $alumnosRefrescados = $grupo->alumnos()->get();
        $cuotas = $alumnosRefrescados->map(function ($a) {
            $v = $a->pivot->costo;
            return is_null($v) ? null : (float) $v;
        })->filter(function ($v) {
            return $v !== null;
        })->values();

        // Obtener costo total del curso
        $costoCurso = $grupo->curso->costo ?? null;

        $tol = 0.01;
        $tipoId = 1; // Ordinario por defecto

        // Exoneración si algún alumno tiene costo ≈ 0
        if ($cuotas->some(function ($v) use ($tol) {
            return abs($v) < $tol;
        })) {
            $tipoId = 3; // Exoneración
        } else {
            // Reducción si la suma total de cuotas es menor al costo del curso (con tolerancia)
            $sumCuotas = $cuotas->sum();
            $hasAnyValue = $cuotas->isNotEmpty();
            if (!is_null($costoCurso) && $hasAnyValue && ($sumCuotas + $tol) < (float) $costoCurso) {
                $tipoId = 2; // Reducción
            }
        }

        // Guardar en grupo
        $grupo->id_tipo_exoneracion = $tipoId;
        $grupo->save();

        return $grupo->fresh(['alumnos']);
    }

    public function clonarGrupo($grupo_id)
    {
        $grupo = Grupo::findOrFail($grupo_id);
        $nuevoGrupo = $grupo->replicate();

        // ? Columnas que no se clonan exactamente igual
        $nuevoGrupo->clave_grupo = null;
        $nuevoGrupo->id_usuario_captura = auth()->id();
        $nuevoGrupo->asis_finalizado = false;
        $nuevoGrupo->calif_finalizado = false;
        $nuevoGrupo->num_revision = null;
        $nuevoGrupo->num_revision_arc02 = null;
        $nuevoGrupo->evidencia_fotografica = null;
        $nuevoGrupo->vb_dg = false;
        $nuevoGrupo->save();

        // ? Ahora si asginamos folio
        $this->generarFolio($nuevoGrupo->id);


        // ? Clonamos los alumnos N : M
        foreach ($grupo->alumnos as $alumno) {
            $nuevoGrupo->alumnos()->attach($alumno->id);
        }

        // ? Actualizamos estatus
        $this->actualizarEstatusGrupo($nuevoGrupo->id, 'EN CAPTURA');

        return $nuevoGrupo;
    }
}
