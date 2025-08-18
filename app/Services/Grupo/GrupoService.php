<?php

namespace App\Services\Grupo;

use App\Models\Estatus;
use App\Models\Municipio;
use App\Repositories\GrupoRepository;
use App\Interfaces\Repositories\GrupoRepositoryInterface;
use App\Models\Grupo;

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

        if ($tieneAllAccess) {
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
            'id_imparticion' => $datos['id_imparticion'],
            'id_modalidad' => $datos['id_modalidad'],
            'id_unidad' => $datos['id_unidad'],
            'id_servicio' => $datos['id_servicio'],
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
        return $grupo;
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
}
