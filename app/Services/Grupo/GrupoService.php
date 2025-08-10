<?php

namespace App\Services\Grupo;

use App\Models\Estatus;
use App\Models\Municipio;
use App\Repositories\GrupoRepository;
use App\Interfaces\Repositories\GrupoRepositoryInterface;

class GrupoService
{

    public function __construct(private GrupoRepositoryInterface $grupoRepository)
    {
        $this->grupoRepository = $grupoRepository;
    }

    public function obtenerGrupos($registrosPorPagina = 15, $busqueda = null)
    {
        if ($busqueda) {
            return $this->grupoRepository->buscarPaginado($busqueda, $registrosPorPagina);
        }

        return $this->grupoRepository->obtenerTodos($registrosPorPagina);
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
        $grupo = $this->grupoRepository->actualizarOrCrear($infoGeneral);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'info_general');
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

        $grupo = $this->grupoRepository->actualizarOrCrear($ubicacion);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'ubicacion');
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

        $grupo = $this->grupoRepository->actualizarOrCrear($organismo);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'organismo');
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

        $grupo = $this->grupoRepository->actualizarOrCrear($opciones);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'opciones');
        return $grupo;
    }

    public function guardarAgenda($datos, $id_grupo = null)
    {
        $grupo = $this->grupoRepository->actualizarOrCrear($datos);
        $id_grupo = $grupo->id ?? $id_grupo;
        $this->actualizarEstatusGrupo($id_grupo, 'agenda');
        return $grupo;
    }

    public function actualizarEstatusGrupo($grupoId, $seccion)
    {
        $nombreEstatus = $seccion === 'REVISION' ? 'EN REVISION' : 'EN CAPTURA';
        return $this->grupoRepository->actualizarEstatus($grupoId, $seccion, $nombreEstatus);
    }

    public function validarSeccionesCompletas($grupoId)
    {
        $grupo = $this->grupoRepository->obtenerPorId($grupoId);

        if (!$grupo) {
            return false;
        }

        // Validar que todos los campos requeridos estén presentes
        $camposRequeridos = [
            'id_imparticion',
            'id_modalidad',
            'id_unidad',
            'id_servicio',
            'id_curso',
            'id_municipio',
            'id_localidad',
            'efisico',
            'id_organismo_publico',
            'organismo_representante',
        ];

        foreach ($camposRequeridos as $campo) {
            if (empty($grupo->$campo)) {
                return false;
            }
        }

        return true;
    }
}
