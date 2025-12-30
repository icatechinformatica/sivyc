<?php

namespace App\Http\Controllers\Preinscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportarGruposController extends Controller
{
    private $ejercicio;
    private $id_user;
    private $realizo;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->ejercicio = date('Y');
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;
            return $next($request);
        });
    }

    public function index()
    {
        return view('preinscripcion.importar_grupos');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        // Guardar archivo temporalmente con storeAs
        $file = $request->file('archivo_excel');
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        
        // Asegurar que el directorio temp existe
        if (!Storage::exists('temp')) {
            Storage::makeDirectory('temp');
        }
        
        $tempPath = $file->storeAs('temp', $fileName);

        // Parsear Excel
        $fullPath = Storage::path($tempPath);
        $spreadsheet = IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Validar headers (convertir a mayúsculas para comparar)
        $headers = array_map(function($h) {
            return strtoupper(trim($h));
        }, $rows[0]);

        $expectedHeaders = ['UNIDAD', 'CURSO', 'INICIO', 'FIN', 'HORA INICIO', 'HORA FIN', 'CURP'];
        
        // Verificar que todas las columnas requeridas existan (permite columnas extra y cualquier orden)
        $missingHeaders = array_diff($expectedHeaders, $headers);
        if (!empty($missingHeaders)) {
            Storage::delete($tempPath);
            return redirect()->route('preinscripcion.importar_grupos.index')
                ->with('error', 'Faltan las siguientes columnas requeridas: ' . implode(', ', $missingHeaders));
        }
        
        // Crear mapa de índices de columnas para acceso flexible
        $columnMap = array_flip($headers);

        // Procesar datos
        $data = [];
        $hasErrors = false;

        for ($i = 1; $i < count($rows); $i++) {
            if (empty(array_filter($rows[$i]))) continue; // Skip empty rows

            $row = $rows[$i];
            $rowData = [
                'fila' => $i + 1,
                'unidad' => trim($row[$columnMap['UNIDAD']]),
                'curso' => trim($row[$columnMap['CURSO']]),
                'inicio' => $this->parseDate($row[$columnMap['INICIO']]),
                'fin' => $this->parseDate($row[$columnMap['FIN']]),
                'hora_inicio' => trim($row[$columnMap['HORA INICIO']]),
                'hora_fin' => trim($row[$columnMap['HORA FIN']]),
                'curp' => trim($row[$columnMap['CURP']]),
                'errors' => [],
                'warnings' => []
            ];

            // Lookup Unidad
            // Normalizar caso específico: "Tuxtla Gutiérrez" -> "TUXTLA"
            $unidadBusqueda = $rowData['unidad'];
            if (stripos($unidadBusqueda, 'Tuxtla') !== false) {
                $unidadBusqueda = 'TUXTLA';
            }
            
            $unidad = DB::table('tbl_unidades')
                ->whereRaw("translate(lower(unidad), 'áéíóúüñ', 'aeiouun') = translate(lower(?), 'áéíóúüñ', 'aeiouun')", [$unidadBusqueda])
                ->first();

            if (!$unidad) {
                $rowData['errors'][] = 'Unidad no encontrada';
                $hasErrors = true;
            } else {
                $rowData['unidad_data'] = $unidad;
                $rowData['cct'] = $unidad->cct;
                $rowData['plantel'] = $unidad->plantel;
                $rowData['ze'] = $unidad->ze; // ZE from tbl_unidades
            }

            // Lookup Curso (con normalización de acentos y puntuación)
            $cursoNormalizado = $this->normalizarTexto($rowData['curso']);



            $curso = DB::table('cursos')
                ->select('cursos.id', 'cursos.nombre_curso', 'cursos.id_especialidad', 'cursos.horas', 'cursos.area as area_id', 'cursos.rango_criterio_pago_maximo')
                ->leftJoin('area', 'cursos.area', '=', 'area.id')
                ->addSelect('area.formacion_profesional as nombre_area')
                ->whereRaw(
                    "regexp_replace(
                        translate(
                            lower(trim(cursos.nombre_curso)), 
                            'áéíóúüñÁÉÍÓÚÜÑ.,;:!?¿¡()-', 
                            'aeiouunaeiouun'
                        ),
                        '\\s+', ' ', 'g'
                    ) = ?",
                    [$cursoNormalizado]
                )
                ->whereIn('cursos.modalidad', ['CAE Y EXT', 'EXT'])
                ->where('cursos.estado', true)
                ->first();

            if (!$curso) {
                $rowData['errors'][] = 'Curso no encontrado: ' . $rowData['curso'];
                $hasErrors = true;
            } else {
                $rowData['curso_data'] = $curso;
                
                // Lookup Especialidad
                $especialidad = DB::table('especialidades')
                    ->where('id', $curso->id_especialidad)
                    ->value('nombre');
                $rowData['especialidad'] = $especialidad;
            }

            // Lookup Instructor por CURP
            $instructor = $this->buscarInstructor($rowData['curp']);
            if (!$instructor) {
                $rowData['errors'][] = 'Instructor no encontrado con CURP: ' . $rowData['curp'];
                $hasErrors = true;
            } else {
                $rowData['instructor'] = $instructor;
            }

            // Lookup Municipio (solo para ID if needed, ze viene de unidad)
            $municipio = DB::table('tbl_municipios')
                ->where('muni', $rowData['unidad'])
                ->first();
            
            if ($municipio) {
                $rowData['id_municipio'] = $municipio->id;
            }

            // Generar preview de folios (sin guardar aún)
            if (isset($unidad)) {
                $rowData['folio_grupo_preview'] = $this->generarFolioPreview($unidad->cct);
                $rowData['id_preview'] = $this->generarIdPreview($unidad->plantel);
            }

            $data[] = $rowData;
        }

        return view('preinscripcion.importar_grupos_preview', [
            'data' => $data,
            'temp_file' => $tempPath,
            'has_errors' => $hasErrors,
            'total_rows' => count($data)
        ]);
    }

    public function store(Request $request)
    {
        $tempFile = $request->input('temp_file');
        
        if (!Storage::exists($tempFile)) {
            return redirect()->route('preinscripcion.importar_grupos.index')
                ->with('error', 'Archivo temporal no encontrado. Por favor, intente de nuevo.');
        }

        // Re-leer el archivo
        $fullPath = Storage::path($tempFile);
        $spreadsheet = IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $insertados = 0;
        $errores = [];

        DB::beginTransaction();
        
        try {
            for ($i = 1; $i < count($rows); $i++) {
                if (empty(array_filter($rows[$i]))) continue;

                $row = $rows[$i];
                $resultado = $this->procesarFila($row, $i + 1);
                
                if ($resultado['success']) {
                    $insertados++;
                } else {
                    $errores[] = "Fila " . ($i + 1) . ": " . $resultado['error'];
                }
            }

            if (!empty($errores)) {
                DB::rollBack();
                Storage::delete($tempFile);
                return redirect()->route('preinscripcion.importar_grupos.index')
                    ->with('error', 'Se encontraron errores: ' . implode(', ', $errores));
            }

            DB::commit();
            Storage::delete($tempFile);

            return redirect()->route('preinscripcion.importar_grupos.index')
                ->with('message', "¡Importación exitosa! Se insertaron {$insertados} grupos.");

        } catch (\Exception $e) {
            DB::rollBack();
            Storage::delete($tempFile);
            
            return redirect()->route('preinscripcion.importar_grupos.index')
                ->with('error', 'Error en la importación: ' . $e->getMessage());
        }
    }

    private function procesarFila($row, $numFila)
    {
        $unidad = trim($row[0]);
        $cursoNombre = trim($row[1]);
        $inicio = $this->parseDate($row[2]);
        $fin = $this->parseDate($row[3]);
        $horaInicio = trim($row[4]);
        $horaFin = trim($row[5]);
        $curp = trim($row[6]);

        // Lookup Unidad
        // Normalizar caso específico: "Tuxtla Gutiérrez" -> "TUXTLA"
        $unidadBusqueda = $unidad;
        if (stripos($unidadBusqueda, 'Tuxtla') !== false) {
            $unidadBusqueda = 'TUXTLA';
        }
        
        $unidadData = DB::table('tbl_unidades')
            ->whereRaw("translate(lower(unidad), 'áéíóúüñ', 'aeiouun') = translate(lower(?), 'áéíóúüñ', 'aeiouun')", [$unidadBusqueda])
            ->first();
        if (!$unidadData) {
            return ['success' => false, 'error' => 'Unidad no encontrada'];
        }

        // Lookup Instructor
        $instructor = $this->buscarInstructor($curp);
        if (!$instructor) {
            return ['success' => false, 'error' => 'Instructor no encontrado'];
        }

        // Lookup Curso (con normalización de acentos y puntuación)
        $cursoNormalizado = $this->normalizarTexto($cursoNombre);
        $curso = DB::table('cursos')
            ->select('cursos.*', 'area.formacion_profesional as nombre_area', 'cursos.rango_criterio_pago_maximo')
            ->leftJoin('area', 'cursos.area', '=', 'area.id')
            ->whereRaw(
                "regexp_replace(
                    translate(
                        lower(trim(cursos.nombre_curso)), 
                        'áéíóúüñÁÉÍÓÚÜÑ.,;:!?¿¡()-', 
                        'aeiouunaeiouun'
                    ),
                    '\\s+', ' ', 'g'
                ) = ?",
                [$cursoNormalizado]
            )
            ->whereIn('cursos.modalidad', ['CAE Y EXT', 'EXT'])
            ->where('cursos.estado', true)
            ->first();
        
        if (!$curso) {
            return ['success' => false, 'error' => 'Curso no encontrado'];
        }

        // Calcular Horas y Formato
        $timeStart = strtotime($horaInicio);
        $timeEnd = strtotime($horaFin);
        $diffHours = ($timeEnd - $timeStart) / 3600;
        
        $hiniFormatted = date('h:i A', $timeStart);
        $hfinFormatted = date('h:i A', $timeEnd);

        // Lookup Especialidad
        $especialidad = DB::table('especialidades')
            ->where('id', $curso->id_especialidad)
            ->value('nombre');

        // Lookup Municipio
        $municipio = DB::table('tbl_municipios')
            ->where('muni', $unidad)
            ->first();

        // Generar ID
        $id = $this->generarId($unidadData->plantel);
        // dd($id);
        // Generar folio_grupo
        $folioGrupo = $this->generarFolioGrupo($unidadData->cct);

        // dd($curso->rango_criterio_pago_maximo);
        // dd($instructor->criterio_pago);
        // Preparar soportes_instructor JSON
        $soportesInstructor = json_encode([
            'domicilio' => $instructor->domicilio,
            'archivo_domicilio' => $instructor->archivo_domicilio,
            'archivo_ine' => $instructor->archivo_ine,
            'archivo_bancario' => $instructor->archivo_bancario,
            'archivo_rfc' => $instructor->archivo_rfc,
            'banco' => $instructor->banco,
            'no_cuenta' => $instructor->no_cuenta,
            'interbancaria' => $instructor->interbancaria,
            'tipo_honorario' => $instructor->tipo_honorario
        ]);

        // Insertar en tbl_cursos
        DB::table('tbl_cursos')->insert([
            'id' => $id,
            'cct' => $unidadData->cct,
            'unidad' => $unidadData->unidad, // Usar nombre canónico de BD
            'nombre' => $instructor->nombre_completo,
            'curp' => $instructor->curp,
            'rfc' => $instructor->rfc,
            'clave' => '0',
            'mvalida' => '0',
            'mod' => 'EXT',
            'area' => $curso->nombre_area ?? '',
            'espe' => $especialidad ?? '',
            'curso' => $curso->nombre_curso, // Usar nombre normalizado de BD
            'inicio' => $inicio,
            'termino' => $fin,
            'dura' => $curso->horas,
            'hini' => $hiniFormatted,
            'hfin' => $hfinFormatted,
            'horas' => $diffHours, // Horas calculadas
            'ciclo' => $this->calcularCiclo(),
            'plantel' => 'A DISTANCIA',
            'depen' => 'CAPACITACION ABIERTA',
            'muni' => $unidadData->unidad, // Usar nombre canónico de BD
            'tipo_curso' => 'CURSO',
            'tcapacitacion' => 'A DISTANCIA',
            'efisico' => 'CURSO CON ACTIVIDAD NO PRESENCIAL, CAPACITACIÓN A DISTANCIA.',
            'mpaqueteria' => $curso->memo_validacion ?? '0',
            'hombre' => 0,
            'mujer' => 0,
            'tipo' => 'EXO',
            'cgeneral' => '0',
            'cp' => min($curso->rango_criterio_pago_maximo ?? 0, $instructor->criterio_pago ?? 0),
            'ze' => $unidadData->ze ?? '',
            'id_curso' => $curso->id,
            'id_instructor' => $instructor->id,
            'id_organismo' => 358,
            'id_especialidad' => $curso->id_especialidad,
            'id_municipio' => $municipio->id ?? null,
            'medio_virtual' => 'ZOOM',
            'programa' => 'NINGUNO',
            'folio_grupo' => $folioGrupo,
            'folio_unico' => null,
            'status' => 'NO REPORTADO',
            'turnado' => 'UNIDAD',
            'realizo' => strtoupper($this->realizo),
            'instructor_tipo_identificacion' => $instructor->tipo_identificacion,
            'instructor_folio_identificacion' => $instructor->folio_ine,
            'soportes_instructor' => $soportesInstructor,
            'nplantel' => $unidadData->plantel,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return ['success' => true];
    }

    private function buscarInstructor($curp)
    {
        $instructor = DB::table('instructores as i')
            ->leftJoin('instructor_perfil as ip', 'i.id', '=', 'ip.numero_control')
            ->leftJoin('especialidad_instructores as ei', 'ei.id_instructor', '=', 'i.id')
            ->select(
                'i.id',
                'i.apellidoPaterno',
                'i.apellidoMaterno',
                'i.nombre',
                'i.rfc',
                'i.curp',
                'i.tipo_identificacion',
                'i.folio_ine',
                'i.domicilio',
                'i.archivo_domicilio',
                'i.archivo_ine',
                'i.archivo_bancario',
                'i.archivo_rfc',
                'i.banco',
                'i.no_cuenta',
                'i.interbancaria',
                'i.tipo_honorario',
                'ei.criterio_pago_id as criterio_pago'
            )
            ->where('i.curp', $curp)
            ->first();

        if ($instructor) {
            $instructor->nombre_completo = trim($instructor->apellidoPaterno . ' ' . 
                                                 $instructor->apellidoMaterno . ' ' . 
                                                 $instructor->nombre);
        }

        return $instructor;
    }

    private function generarId($plantel)
    {
        $year = substr(date('Y'), -2);
        $plantelPadded = str_pad($plantel, 3, '0', STR_PAD_LEFT);
        $prefix = $year . $plantelPadded;

        $maxId = DB::table('tbl_cursos')
            ->where('id', 'like', $prefix . '%')
            ->max('id');

        if ($maxId) {
            $consecutivo = intval(substr($maxId, -4)) + 1;
        } else {
            $consecutivo = 1;
        }

        return $prefix . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    }

    private function generarFolioGrupo($cct)
    {
        // Procesar CCT
        $cctProcesado = substr($cct, -5);
        $cctProcesado = ltrim($cctProcesado, '0');

        // Año
        $year = substr(date('Y'), -2);

        // Consecutivo desde alumnos_registro
        $maxConsecutivo = DB::table('alumnos_registro')
            ->where('ejercicio', date('Y'))
            ->where('cct', $cct)
            ->where('eliminado', false)
            ->selectRaw("COALESCE(MAX(CAST(substring(folio_grupo from '.{4}$') AS int)), 0) as max_consec")
            ->value('max_consec');

        $consecutivo = ($maxConsecutivo ?? 0) + 1;

        return $cctProcesado . '-' . $year . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    }

    private function generarFolioPreview($cct)
    {
        $cctProcesado = ltrim(substr($cct, -5), '0');
        $year = substr(date('Y'), -2);
        return $cctProcesado . '-' . $year . 'XXXX';
    }

    private function generarIdPreview($plantel)
    {
        $year = substr(date('Y'), -2);
        $plantelPadded = str_pad($plantel, 3, '0', STR_PAD_LEFT);
        return $year . $plantelPadded . 'XXXX';
    }

    private function parseDate($value)
    {
        if (is_numeric($value)) {
            // Excel date serial number
            $unixDate = ($value - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        }
        
        return date('Y-m-d', strtotime($value));
    }

    private function calcularCiclo()
    {
        $mes_dia = date("m-d");
        $mes_julio = "07-01";
        
        if ($mes_dia >= $mes_julio) {
            return date("Y") . "-" . (date("Y") + 1);
        } else {
            return (date("Y") - 1) . "-" . date("Y");
        }
    }

    private function normalizarTexto($texto)
    {
        // Normalizar para coincidencias: lowercase, sin acentos, sin puntuación
        $normalized = mb_strtolower($texto, 'UTF-8');
        
        // Eliminar acentos y ñ
        $normalized = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $normalized
        );
        
        // Eliminar signos de puntuación
        $normalized = preg_replace('/[.,;:!?¿¡()-]/', '', $normalized);
        
        // Normalizar espacios (múltiples espacios a uno solo y trim)
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = trim($normalized);
        
        return $normalized;
    }
}
