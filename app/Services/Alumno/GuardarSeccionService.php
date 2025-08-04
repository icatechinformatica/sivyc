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

    public function obtenerSeccion($seccion, $datos, $archivo = null)
    {
        switch ($seccion) {
            case 'datos_personales':
                return $this->guardarDatosPersonales($datos, $archivo);
            case 'domicilio':
                return $this->guardarDomicilio($datos);
            case 'contacto':
                return $this->guardarContacto($datos);
            case 'grupos_vulnerables':
                return $this->guardarGruposVulnerables($datos);
            case 'capacitacion':
                return $this->guardarCapacitacion($datos, $archivo);
            case 'empleado':
                return $this->guardarEmpleado($datos);
            case 'cerss':
                return $this->guardarCerss($datos, $archivo);
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
            'domicilio' => $datos['domicilio'] ?? null,
            'colonia' => $datos['colonia'] ?? null,
            'clave_localidad' => $datos['clave_localidad'] ?? null,
            'cp' => $datos['cp'] ?? null,
            'id_usuario_realizo' => $datos['id_usuario_realizo'] ?? null
        ];
        return $this->alumnoRepository->actualizarOrCrearPorCURP($domicilio);
    }
    private function guardarContacto($datos)
    {
        $curp = strtoupper($datos['curp']);
        $contacto = [
            'curp' => $curp,
            'correo' => $datos['correo_electronico'] ?? null,
            'telefono_celular' => $datos['telefono_celular'] ?? null,
            'telefono_casa' => $datos['telefono_casa'] ?? null,
            'facebook' => $datos['facebook'] ?? null,
            'check_bolsa' => $datos['autoriza_bolsa_trabajo'] == 1 ? true : false,
            'id_usuario_realizo' => $datos['id_usuario_realizo'] ?? null,
        ];
        return $this->alumnoRepository->actualizarOrCrearPorCURP($contacto);
    }

    private function guardarGruposVulnerables($datos)
    {
        // Lógica para guardar los grupos vulnerables
    }

    private function guardarCapacitacion($datos, $archivoUltimoGrado = null)
    {
        $curp = strtoupper($datos['curp']);
        $capacitaciones = [
            'curp' => $curp,
            'medio_entero' => $datos['medio_enterado_sistema'] ?? null,
            'sistema_capacitacion_especificar' => $datos['motivo_eleccion_capacitacion'] ?? null,
            'medio_confirmacion' => $datos['medio_confirmacion'] ?? null,
            'id_ultimo_grado_estudios' => $datos['ultimo_grado_estudios'] ?? null,
            'id_usuario_realizo' => $datos['id_usuario_realizo'] ?? null
        ];

        // Manejo de archivos_documentos JSON
        $archivos_documentos = [];

        // Obtener archivos_documentos actuales (incluye curp si existe)
        $archivos_documentos_actual = $this->alumnoRepository->obtenerArchivosDocumentos($curp);
        if ($archivos_documentos_actual) {
            $archivos_documentos = is_array($archivos_documentos_actual) ? $archivos_documentos_actual : json_decode($archivos_documentos_actual, true) ?? [];
        }

        // Si se sube archivo del último grado de estudio
        if ($archivoUltimoGrado && $archivoUltimoGrado->isValid()) {
            $anio = '2026';
            $carpeta = "{$anio}/AlumnosRegistro/{$curp}";
            $fecha = now()->format('Ymd_His');
            $nombreArchivo = "ULTIMO_GRADO_ESTUDIO_{$curp}_{$fecha}." . $archivoUltimoGrado->getClientOriginalExtension();
            $ruta = $archivoUltimoGrado->storeAs($carpeta, $nombreArchivo);

            $archivos_documentos['ultimo_grado_estudio'] = [
                'capturado' => true,
                'ruta' => $ruta,
                'fecha_expedicion' => $datos['fecha_documento_ultimo_grado'] ?? null
            ];
        } elseif (isset($datos['fecha_documento_ultimo_grado'])) {
            if (isset($archivos_documentos['ultimo_grado_estudio'])) {
                // Mantener todos los datos actuales y solo actualizar la fecha de expedición
                $archivos_documentos['ultimo_grado_estudio']['fecha_expedicion'] = $datos['fecha_documento_ultimo_grado'];
            } else {
                // Si no existe registro previo, crear uno nuevo solo con la fecha
                $archivos_documentos['ultimo_grado_estudio'] = [
                    'capturado' => true,
                    'ruta' => null,
                    'fecha_expedicion' => $datos['fecha_documento_ultimo_grado']
                ];
            }
        }

        if (!empty($archivos_documentos)) {
            $capacitaciones['archivos_documentos'] = json_encode($archivos_documentos);
        }

        return $this->alumnoRepository->actualizarOrCrearPorCURP($capacitaciones);
    }

    private function guardarEmpleado($datos)
    {
        $curp = strtoupper($datos['curp']);

        $empleado = [
            'curp' => $curp,
            'empleado' => $datos['empleado_aspirante'] ?? null,
            'id_usuario_realizo' => $datos['id_usuario_realizo'] ?? null
        ];

        if ($datos['empleado_aspirante'] == 1) {
            $empleado['empresa_trabaja'] = $datos['nombre_empresa'] ?? null;
            $empleado['puesto_empresa'] = $datos['puesto_trabajo'] ?? null;
            $empleado['antiguedad'] = $datos['antiguedad'] ?? null;
            $empleado['direccion_empresa'] = $datos['direccion_trabajo'] ?? null;
        }
        return $this->alumnoRepository->actualizarOrCrearPorCURP($empleado);
    }

    private function guardarCerss($datos, $documento_ficha_cerss = null)
    {
        $curp = strtoupper($datos['curp']);
        
        // Crear estructura JSON directa para cerss
        $cerss_data = [
            'aspirante_cerss' => $datos['aspirante_cerss'] ?? null,
            'numero_expediente' => $datos['numero_expediente'] ?? null,
            'id_usuario_realizo' => $datos['id_usuario_realizo'] ?? null,
            'ficha_cerss' => null
        ];

        // Manejo de archivo ficha_cerss
        if ($documento_ficha_cerss && $documento_ficha_cerss->isValid()) {
            $anio = '2026';
            $carpeta = "{$anio}/AlumnosRegistro/{$curp}";
            $fecha = now()->format('Ymd_His');
            $nombreArchivo = "FICHA_CERSS_{$curp}_{$fecha}." . $documento_ficha_cerss->getClientOriginalExtension();
            $ruta = $documento_ficha_cerss->storeAs($carpeta, $nombreArchivo);

            $cerss_data['ficha_cerss'] = $ruta;
        } elseif (isset($datos['ficha_cerss'])) {
            // Si ficha_cerss ya está definido en los datos, usarlo directamente
            $cerss_data['ficha_cerss'] = $datos['ficha_cerss'];
        } elseif (isset($datos['fecha_documento_ficha_cerss'])) {
            $cerss_actual = $this->alumnoRepository->obtenerCERSSPorCURP($curp);
            if (isset($cerss_actual['ficha_cerss'])) {
                // Mantener la ruta actual si existe
                $cerss_data['ficha_cerss'] = $cerss_actual['ficha_cerss'];
            }
        }

        $cerss = [
            'curp' => $curp,
            'cerss' => json_encode($cerss_data)
        ];

        return $this->alumnoRepository->actualizarOrCrearPorCURP($cerss);
    }
}
