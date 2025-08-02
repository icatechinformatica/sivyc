<?php
namespace App\Services\Alumno;
use App\Repositories\AlumnoSeccionesRepositoryInterface;

class GuardarSeccionService
{
    protected $alumnoRepository;

    public function __construct(AlumnoSeccionesRepositoryInterface $alumnoRepository)
    {
        $this->alumnoRepository = $alumnoRepository;
    }

    public function obtenerSeccion($seccion, $datos, $archivoCurp = null)
    {
        switch ($seccion) {
            case 'datos_personales':
                return $this->guardarDatosPersonales($datos, $archivoCurp);
            case 'domicilio':
                return $this->guardarDomicilio($datos);
            case 'contacto':
                return $this->guardarContacto($datos);
            case 'grupos_vulnerables':
                return $this->guardarGruposVulnerables($datos);
            case 'capacitaciones':
                return $this->guardarCapacitaciones($datos);
            case 'empleado':
                return $this->guardarEmpleado($datos);
            case 'cerss':
                return $this->guardarCerss($datos);
            default:
                throw new \Exception('Sección no reconocida');
        }
    }

    private function guardarDatosPersonales($datos, $archivoCurp = null)
    {
        $curp = strtoupper($datos['curp']);
        $datos_personales = [
            'nombre' => strtoupper($datos['nombre']),
            'apellido_materno' => strtoupper($datos['apellido_materno']),
            'apellido_paterno' => strtoupper($datos['apellido_paterno']),
            'curp' => $curp,
            'fecha_nacimiento' => $datos['fecha_de_nacimiento'],
            'id_estado_civil' => $datos['id_estado_civil'],
            'id_usuario_realizo' => $datos['id_usuario_realizo'],
            'id_sexo' => $datos['id_sexo'],
            'id_nacionalidad' => $datos['id_nacionalidad']
        ];

        // Manejo de archivos_documentos JSON
        $archivos_documentos = [];
        if (isset($datos['archivos_documentos'])) {
            $archivos_documentos = is_array($datos['archivos_documentos']) ? $datos['archivos_documentos'] : json_decode($datos['archivos_documentos'], true) ?? [];
        }

        // Si se sube archivo, se guarda todo el objeto curp
        if ($archivoCurp && $archivoCurp->isValid()) {
            $anio = '2026';
            $carpeta = "{$anio}/AlumnosRegistro/{$curp}";
            $fecha = now()->format('Ymd_His');
            $nombreArchivo = "CURP_{$curp}_{$fecha}." . $archivoCurp->getClientOriginalExtension();
            $ruta = $archivoCurp->storeAs($carpeta, $nombreArchivo);

            $archivos_documentos['curp'] = [
                'capturado' => true,
                'ruta' => $ruta,
                'fecha_expedicion' => $datos['fecha_documento_curp'] ?? null
            ];
        } elseif (isset($datos['fecha_documento_curp'])) {
            $archivos_documentos_actual = $this->alumnoRepository->obtenerArchivosDocumentos($curp);
            if (isset($archivos_documentos_actual['curp'])) {
                // Mantener todos los datos actuales y solo actualizar la fecha de expedición
                $archivos_documentos['curp'] = $archivos_documentos_actual['curp'];
                $archivos_documentos['curp']['fecha_expedicion'] = $datos['fecha_documento_curp'];
            } else {
                // Si no existe registro previo, crear uno nuevo solo con la fecha
                $archivos_documentos['curp'] = [
                    'capturado' => true,
                    'ruta' => null,
                    'fecha_expedicion' => $datos['fecha_documento_curp']
                ];
            }
        }

        // Solo guardar archivos_documentos si existe el objeto curp
        if (isset($archivos_documentos['curp'])) {
            $datos_personales['archivos_documentos'] = json_encode($archivos_documentos);
        }

        return $this->alumnoRepository->actualizarOrCrearPorCURP($datos_personales);
    }
    private function guardarDomicilio($datos)
    {
        $curp = strtoupper($datos['curp']);
        $domicilio = [
            'curp' => $curp,
            'id_pais' => $datos['id_pais'] ?? null,
            'id_estado' => $datos['id_estado'] ?? null,
            'id_municipio' => $datos['id_municipio'] ?? null,
            'clave_localidad' => $datos['clave_localidad'] ?? null,
            'cp' => $datos['cp'] ?? null,
            'id_usuario_realizo' => $datos['id_usuario_realizo'] ?? null
        ];
        return $this->alumnoRepository->actualizarOrCrearPorCURP($domicilio);
    }
    private function guardarContacto($datos)
    {
        // Lógica para guardar el contacto
    }

    private function guardarGruposVulnerables($datos)
    {
        // Lógica para guardar los grupos vulnerables
    }

    private function guardarCapacitaciones($datos)
    {
        // Lógica para guardar las capacitaciones
    }

    private function guardarEmpleado($datos)
    {
        // Lógica para guardar el empleado
    }

    private function guardarCerss($datos)
    {
        // Lógica para guardar el CERS
    }
}
