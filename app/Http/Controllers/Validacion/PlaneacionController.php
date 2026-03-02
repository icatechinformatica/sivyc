<?php

namespace App\Http\Controllers\Validacion;

use PDF;
use Carbon\Carbon;
use App\Models\Instituto;
use App\Models\tbl_curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\tbl_unidades;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport
use App\Services\HerramientasService;

class PlaneacionController extends Controller {

    public function __construct(HerramientasService $herramientas)
    {
        $this->herramientas = $herramientas;
    }

    public function index(Request $request) {
        // unidad a buscar
        $mes = null;
        $unidades = $request->get('busqueda_unidad');
        $mesSearch = $request->get('mesSearch');

        if ($mesSearch != null) {
            session(['mesBuscar' => $mesSearch]);
            session(['uniSearch' => $unidades]);
            $meses = array(1=>"ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
            $mes = $meses[(int)$mesSearch];
        }

        // anio actual
        $anioActual = Carbon::now()->year;
        $cursos_unidades_planeacion = dataFormatoT($unidades, ['PLANEACION'], null, $mesSearch, ['TURNADO_PLANEACION']);

        // las unidades
        $unidadesIndex = DB::table('tbl_unidades')->select('ubicacion')->groupBy('ubicacion')
            ->orderBy('ubicacion', 'asc')->get();

        return view('reportes.vista_planeacion_indice', compact('unidadesIndex', 'cursos_unidades_planeacion', 'unidades', 'mesSearch','mes'));
    }

    protected function sendtodta(Request $request)
    {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('checkCursos');
        /**
         * vamos al cargar el archivo que se sube
         */

        if (!empty($numero_memo)) {
            # en múmero de memo no está vacio
            if (!empty($cursoschk)) {
                # se cuenta con los cursos y no están vacios se procede a realizar el proceso
                if ($request->hasFile('memorandumNegativoFile')) {
                    // obtenemos el valor del archivo memo

                    $validator = Validator::make($request->all(), [
                        'memorandumNegativoFile' => 'mimes:pdf|max:2048'
                    ]);

                    if ($validator->fails()) {
                        # mandar un mensaje de error
                        return json_encode($validator);
                    } else {
                        $memo = str_replace('/', '_', $numero_memo);
                        /**
                         * aquí vamos a verificar que el archivo no se encuentre guardado
                         * previamente en el sistema de archivos del sistema de ser así se
                         * remplazará el archivo porel que se subirá a continuación
                         */
                        // construcción del archivo
                        $archivo_memo_regreso_dta = 'uploadFiles/memoRegresoDTA/' . $memo . '/' . $memo . '.pdf';
                        if (Storage::exists($archivo_memo_regreso_dta)) {
                            #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                            Storage::delete($archivo_memo_regreso_dta);
                        }

                        $archivo_memo_planeacion_regreso_dta = $request->file('memorandumNegativoFile'); # obtenemos el archivo
                        $url_archivo_memo_planeacion_regreso_dta = $this->uploaded_memo_dta_to_planeacion_file($archivo_memo_planeacion_regreso_dta, $memo, 'memoRegresoDTA'); #invocamos el método
                    }
                } else {
                    $url_archivo_memo_planeacion_regreso_dta = null;
                }

                if (!empty($cursoschk)) {
                    # vamos a checar sólo a los checkbox checados como propiedad
                    if (!empty($cursoschk)) {
                        $fecha_ahora = Carbon::now();
                        $date = $fecha_ahora->format('Y-m-d'); // fecha
                        $numero_memo = $request->get('numero_memo'); // número de memo

                        $turnado_revision_dta = [
                            'FECHA' => $date,
                            'NUMERO' => $numero_memo,
                            'MEMORANDUM' => $url_archivo_memo_planeacion_regreso_dta
                        ];
                        /**
                         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                         */
                        # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                        $data = explode(",", $cursoschk);
                        // poner una pila
                        $pilaSendDTA = [];
                        foreach ($data as $key) {
                            # volvamos las variables en el arreglo
                            array_push($pilaSendDTA, $key);
                        }
                        // $comentario = explode(",", $_POST['comentarios_planeacion']);
                        foreach (array_combine($pilaSendDTA, $_POST['comentariosPlaneacion']) as $key => $comentarios) {
                            $observaciones_revision_dta = [
                                'OBSERVACION_RETORNO' =>  $comentarios
                            ];
                            \DB::table('tbl_cursos')
                                ->where('id', $key)
                                ->update([
                                    'memos' => DB::raw("jsonb_set(memos, '{TURNADO_REVISION_DTA}','" . json_encode($turnado_revision_dta) . "', true)"),
                                    'status' => 'REVISION_DTA',
                                    'turnado' => 'REVISION_DTA',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{PLANEACION_ENVIDO_DTA}', '" . json_encode($observaciones_revision_dta) . "', true)"),
                                ]);
                        }
                    }

                    $json = json_encode('DONE');
                    return $json;
                } else {
                    $json = json_encode('EMPTYCURSOS');
                    return $json;
                }
            } else {
                # se envía un mensaje de error por cursos vacios
                $json = json_encode('EMPTYCURSOS');
                return $json;
            }
        } else {
            # el número de memo está vacio y se procede a enviar un mensaje
            $json = json_encode('EMPTYNUMMEMO');
            return $json;
        }
    }

    protected function finishPlaneacion(Request $request)
    {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('checkCursos');
        /**
         * vamos al cargar el archivo que se sube
         */
        if (!empty($numero_memo)) {
            # si el número de memo no está vacio se procede a trabajar
            if (!empty($cursoschk)) {
                # trabajamos en el desarrollo del módulo de envío
                if ($request->hasFile('memorandumPositivoFile')) {
                    // obtenemos el valor del archivo memo

                    $validator = Validator::make($request->all(), [
                        'memorandumPositivoFile' => 'mimes:pdf|max:2048'
                    ]);

                    if ($validator->fails()) {
                        # mandar un mensaje de error
                        return json_encode($validator);
                    } else {
                        $memo = str_replace('/', '_', $numero_memo);
                        /**
                         * aquí vamos a verificar que el archivo no se encuentre guardado
                         * previamente en el sistema de archivos del sistema de ser así se
                         * remplazará el archivo porel que se subirá a continuación
                         */
                        // construcción del archivo
                        $archivo_memo = 'uploadFiles/memoterminado/' . $memo . '/memorandum_planeacion_termino.pdf';
                        if (Storage::exists($archivo_memo)) {
                            #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                            Storage::delete($archivo_memo);
                        }

                        $archivo_memo_planeacion_terminado = $request->file('memorandumPositivoFile'); # obtenemos el archivo
                        $url_archivo_memo_planeacion_terminado = $this->uploaded_memo_dta_to_planeacion_file($archivo_memo_planeacion_terminado, $memo, 'memoterminado'); #invocamos el método
                    }
                } else {
                    $url_archivo_memo_planeacion_terminado = null;
                }

                if (!empty($cursoschk)) {
                    # vamos a checar sólo a los checkbox checados como propiedad
                    if (!empty($cursoschk)) {
                        $fecha_ahora = Carbon::now();
                        $date = $fecha_ahora->format('Y-m-d'); // fecha
                        $numero_memo = $request->get('numero_memo'); // número de memo

                        $cerrado_planeacion = [
                            'FECHA' => $date,
                            'NUMERO' => $numero_memo,
                            'MEMORANDUM' => $url_archivo_memo_planeacion_terminado
                        ];
                        /**
                         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                         */
                        # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                        $data = explode(",", $cursoschk);
                        $pilaPlaneacionFinish = [];
                        foreach ($data as $key) {
                            # vaciamos los datos en un arreglo
                            array_push($pilaPlaneacionFinish, $key);
                        }
                        /**
                         * obtener la fecha actual para guardar
                         */
                        $fechaActual = Carbon::now();
                        $fechaTermino = $fechaActual->format('Y-m-d');
                        // $comentario = explode(",", $_POST['comentariosPlaneacionTerminar']);
                        foreach (array_combine($pilaPlaneacionFinish, $_POST['comentariosPlaneacionTerminar']) as $key => $comentarios) {
                            $comentarios_regreso_unidad = [
                                'OBSERVACION_REPORTADO' =>  $comentarios
                            ];
                            \DB::table('tbl_cursos')
                                ->where('id', $key)
                                ->update([
                                    'memos' => DB::raw("jsonb_set(memos, '{CERRADO_PLANEACION}','" . json_encode($cerrado_planeacion) . "', true)"),
                                    'status' => 'REPORTADO',
                                    'turnado' => 'PLANEACION_TERMINADO',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_CERRADO_PLANEACION}', '" . json_encode($comentarios_regreso_unidad) . "', true)"),
                                    'proceso_terminado' => true
                                ]);
                        }
                    }

                    $json = json_encode('DONE');
                    return $json;
                } else {
                    $json = json_encode('EMPTYCURSOS');
                    return $json;
                }
            } else {
                # se envía un mensaje de error de los cursos vacios
                $json = json_encode('EMPTYCURSOS');
                return $json;
            }
        } else {
            # se envía un mensaje de error
            $json = json_encode('EMPTYNUMMEMO');
            return $json;
        }
    }

    protected function generarMemorandum(Request $request) {
        // variable pivote
        $anio = Carbon::now()->year;
        $generarMemo = $request->get('memorandumGenerado');
        $mesReport = $request->get('mesReport');
        switch ($mesReport) {
            case '01': $mesReport = 'ENERO'; break;
            case '02': $mesReport = 'FEBRERO'; break;
            case '03': $mesReport = 'MARZO'; break;
            case '04': $mesReport = 'ABRIL'; break;
            case '05': $mesReport = 'MAYO'; break;
            case '06': $mesReport = 'JUNIO'; break;
            case '07': $mesReport = 'JULIO'; break;
            case '08': $mesReport = 'AGOSTO'; break;
            case '09': $mesReport = 'SEPTIEMBRE'; break;
            case '10': $mesReport = 'OCTUBRE'; break;
            case '11': $mesReport = 'NOVIEMBRE'; break;
            case '12': $mesReport = 'DICIEMBRE'; break;
        }
        if (isset($generarMemo)) {
            # hacemos un switch...
            $leyenda = Instituto::first();
            $leyenda = $leyenda->distintivo;
            switch ($generarMemo) {
                case 'memorandumPositivo':
                    # generamos un switch
                    $value = 'JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO';
                    $jefdepto = 'JEFE DE DEPARTAMENTO DE CERTIFICACION Y CONTROL';
                    $unidadB = $request->get('unidad_busqueda');
                    $num_memo_planeacion = $request->get('num_memo');
                    // fecha actual
                    $fecha_ahora = Carbon::now();
                    $fecha = $fecha_ahora->format('Y-m-d'); // fecha
                    // arreglo de meses
                    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                    $fechaFormato = Carbon::parse($fecha);
                    $mes = $meses[($fechaFormato->format('n')) - 1];
                    $fecha_ahora_espaniol = $fechaFormato->format('d') . ' de ' . $mes . ' de ' . $fechaFormato->format('Y');
                    // registro de las unidades
                    // $reg_unidad = DB::table('tbl_unidades')->select(
                    //     'academico',
                    //     'vinculacion',
                    //     'dacademico',
                    //     'pdacademico',
                    //     'pdunidad',
                    //     'pacademico',
                    //     'pvinculacion',
                    //     'jcyc',
                    //     'pjcyc',
                    //     'dgeneral',
                    //     'pdgeneral'
                    // )->groupby(
                    //     'academico',
                    //     'vinculacion',
                    //     'dacademico',
                    //     'pdacademico',
                    //     'pdunidad',
                    //     'pacademico',
                    //     'pvinculacion',
                    //     'jcyc',
                    //     'pjcyc',
                    //     'dgeneral',
                    //     'pdgeneral'
                    // )->first();
                    // $directorio = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$value}%")->first();
                    $unidad = tbl_unidades::SELECT('tbl_unidades.unidad', 'tbl_unidades.cct','tbl_unidades.ubicacion','direccion')
                                ->WHERE('unidad', '=', 'TUXTLA')
                                ->FIRST();
                    // $direccion = explode("*", $unidad->direccion);
                    // $jefeDepto = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$jefdepto}%")->first();
                    // $directorPlaneacion = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('id', 14)->first();
                    $funcionarios = $this->funcionarios('TUXTLA');
                    $direccion = explode("*",$funcionarios['dacademico']['direccion']);

                    //Seccion para el layout correcto sacando el año
                    $layout_año = $this->herramientas->getPdfLayoutByDate($fechaFormato);

                    $pdf = PDF::loadView('layouts.pdfpages.memorandum_termino_satisfactorio_planeacion', compact('fecha_ahora_espaniol', 'num_memo_planeacion', 'mesReport', 'leyenda', 'anio','funcionarios','direccion','layout_año'));
                    return $pdf->stream('Memorandum_respuesta_satisfactorio_planeacion.pdf');
                    break;
                case 'memorandumNegativo':
                    # generamos un switch
                    $value = 'JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO';
                    $jefdepto = 'JEFE DE DEPARTAMENTO DE CERTIFICACION Y CONTROL';
                    $unidadB = $request->get('unidad_busqueda');
                    $num_memo_planeacion = $request->get('num_memo');
                    // fecha actual
                    $fecha_ahora = Carbon::now();
                    $fecha = $fecha_ahora->format('Y-m-d'); // fecha
                    // arreglo de meses
                    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                    $fechaFormato = Carbon::parse($fecha);
                    $mes = $meses[($fechaFormato->format('n')) - 1];
                    $fecha_ahora_espaniol = $fechaFormato->format('d') . ' de ' . $mes . ' de ' . $fechaFormato->format('Y');
                    // registro de las unidades
                    // $reg_unidad = DB::table('tbl_unidades')->select(
                    //     'academico',
                    //     'vinculacion',
                    //     'dacademico',
                    //     'pdacademico',
                    //     'pdunidad',
                    //     'pacademico',
                    //     'pvinculacion',
                    //     'jcyc',
                    //     'pjcyc',
                    //     'dgeneral',
                    //     'pdgeneral'
                    // )->groupby(
                    //     'academico',
                    //     'vinculacion',
                    //     'dacademico',
                    //     'pdacademico',
                    //     'pdunidad',
                    //     'pacademico',
                    //     'pvinculacion',
                    //     'jcyc',
                    //     'pjcyc',
                    //     'dgeneral',
                    //     'pdgeneral'
                    // )->first();
                    // $directorio = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$value}%")->first();
                    $unidad = tbl_unidades::SELECT('tbl_unidades.unidad', 'tbl_unidades.cct','tbl_unidades.ubicacion','direccion')
                                ->WHERE('unidad', '=', 'TUXTLA')
                                ->FIRST();
                    // $direccion = explode("*", $unidad->direccion);
                    // $jefeDepto = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$jefdepto}%")->first();
                    // $directorPlaneacion = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('id', 14)->first();
                    $funcionarios = $this->funcionarios('TUXTLA');
                    $direccion = explode("*",$funcionarios['dacademico']['direccion']);

                    //Seccion para el layout correcto sacando el año
                    $layout_año = $this->herramientas->getPdfLayoutByDate($fechaFormato);

                    $pdf = PDF::loadView('layouts.pdfpages.memorandum_termino_negativo_planeacion', compact('fecha_ahora_espaniol', 'num_memo_planeacion', 'mesReport', 'leyenda', 'anio','funcionarios','direccion','layout_año'));
                    return $pdf->stream('Memorandum_termino_negativo_planeacion.pdf');
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    protected function uploaded_memo_dta_to_planeacion_file($file, $memo, $sub)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim("memorandum_respuesta." . $extensionFile);
        $path = '/' . $sub . '/' . $memo . '/' . $documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/' . $sub . "/" . $memo . "/" . $documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function chkDateToDeliver()
    {
        $meses = array(1 => "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        return json_encode($fecha);
    }

    protected function xlsExportReporteT(Request $request) {
        // $anio_actual = Carbon::now()->year;
        $meses = array(1=>"ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        $uniSearch = session('uniSearch');
        $mesSearch = session('mesBuscar');

        $formatot_planeacion = dataFormatoT($uniSearch, ['PLANEACION'], null, $mesSearch, ['TURNADO_PLANEACION']);
        foreach ($formatot_planeacion as $key => $value) {
            $formatot_planeacion[$key]->fechaturnado = $meses[(int)$mesSearch];
            unset($value->id_tbl_cursos);
            unset($value->estadocurso);
            unset($value->turnados_enlaces);
            unset($value->madres_solteras);
            unset($value->observaciones_firma);
            unset($value->fecha_turnado);
            unset($value->numero_memo_retorno1);
            unset($value->comentario_enlaces_retorno);
            unset($value->sumatoria_total_ins_edad);
            unset($value->observaciones_enlaces);
            unset($value->observaciones_unidad);
            unset($value->etnia);
            unset($value->arc);
            unset($value->observacion_envio_to_planeacion);
        }

        $head = [
            'MES REPORTADO', 'UNIDAD DE CAPACITACION', 'TIPO DE PLANTEL (UNIDAD, AULA MOVIL, ACCION MOVIL O CAPACITACION EXTERNA)', 'CAMPO DE FORMACIÓN', 'ESPECIALIDAD', 'CURSO', 'CLAVE DEL GRUPO', 'MODALIDAD', 'DURACION TOTAL EN HORAS', 'TURNO', 'DIA INICIO', 'MES INICIO', 'DIA TERMINO', 'MES TERMINO', 'PERIODO', 'HRS. DIARIAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MASC',
            'EGRESADOS', 'EGRESADOS FEMENINO', 'EGRESADO MASCULINO', 'DESERCION', 'COSTO TOTAL DEL CURSO POR PERSONA', 'INGRESO TOTAL', 'CUOTA MIXTA', 'EXONERACION MUJERES', 'EXONERACION HOMBRES', 'REDUCCION CUOTA MUJERES', 'REDUCCION CUOTA HOMBRES', 'NUMERO DE CONVENIO ESPECIFICO', 'MEMO DE VALIDACION DEL CURSO', 'ESPACIO FISICO',
            'NOMBRE DEL INSTRUCTOR', 'CURP', 'RFC', 'ESCOLARIDAD DEL INSTRUCTOR', 'DOCUMENTO ADQUIRIDO', 'SEXO', 'MEMO DE VALIDACION', 'MEMO DE AUTORIZACION DE EXONERACION', 'EMPLEADOS', 'DESEMPLEADOS', 'DISCAPACITADOS', 'MIGRANTES',
            'ADOLECENTES EN CONDICION DE CALLE','MUJERES JEFAS DE FAMILIA','INDIGENA','RECLUSOS','PROGRAMA ESTRATEGICO', 'MUNICIPIO', 'ZE', 'REGION', 'DEPENDENCIA BENEFICIADA', 'CONVENIO GENERAL', 'CONVENIO CON EL SECTOR PUBLICO O PRIVADO', 'MEMO DE VALIDACION DE PAQUETERIA','GRUPO VULNERABLE',
            'INSCRITOS EDAD-1 MUJERES', 'INSCRITOS EDAD-1 HOMBRES',
            'INSCRITOS EDAD-2 MUJERES', 'INSCRITOS EDAD-2 HOMBRES',
            'INSCRITOS EDAD-3 MUJERES', 'INSCRITOS EDAD-3 HOMBRES',
            'INSCRITOS EDAD-4 MUJERES', 'INSCRITOS EDAD-4 HOMBRES',
            'INSCRITOS EDAD-5 MUJERES', 'INSCRITOS EDAD-5 HOMBRES',
            'INSCRITOS EDAD-6 MUJERES', 'INSCRITOS EDAD-6 HOMBRES',
            'INSCRITOS EDAD-7 MUJERES', 'INSCRITOS EDAD-7 HOMBRES',
            'INSCRITOS EDAD-8 MUJERES', 'INSCRITOS EDAD-8 HOMBRES',
            'INSCRITOS ESC-1 MUJERES', 'INSCRITOS ESC-1 HOMBRES',
            'INSCRITOS ESC-2 MUJERES', 'INSCRITOS ESC-2 HOMBRES',
            'INSCRITOS ESC-3 MUJERES', 'INSCRITOS ESC-3 HOMBRES',
            'INSCRITOS ESC-4 MUJERES', 'INSCRITOS ESC-4 HOMBRES',
            'INSCRITOS ESC-5 MUJERES', 'INSCRITOS ESC-5 HOMBRES',
            'INSCRITOS ESC-6 MUJERES', 'INSCRITOS ESC-6 HOMBRES',
            'INSCRITOS ESC-7 MUJERES', 'INSCRITOS ESC-7 HOMBRES',
            'INSCRITOS ESC-8 MUJERES', 'INSCRITOS ESC-8 HOMBRES',
            'INSCRITOS ESC-9 MUJERES', 'INSCRITOS ESC-9 HOMBRES',
            'ACREDITADOS ESC-1 MUJERES', 'ACREDITADOS ESC-1 HOMBRES',
            'ACREDITADOS ESC-2 MUJERES', 'ACREDITADOS ESC-2 HOMBRES',
            'ACREDITADOS ESC-3 MUJERES', 'ACREDITADOS ESC-3 HOMBRES',
            'ACREDITADOS ESC-4 MUJERES', 'ACREDITADOS ESC-4 HOMBRES',
            'ACREDITADOS ESC-5 MUJERES', 'ACREDITADOS ESC-5 HOMBRES',
            'ACREDITADOS ESC-6 MUJERES', 'ACREDITADOS ESC-6 HOMBRES',
            'ACREDITADOS ESC-7 MUJERES', 'ACREDITADOS ESC-7 HOMBRES',
            'ACREDITADOS ESC-8 MUJERES', 'ACREDITADOS ESC-8 HOMBRES',
            'ACREDITADOS ESC-9 MUJERES', 'ACREDITADOS ESC-9 HOMBRES',
            'DESERTORES ESC-1 MUJERES', 'DESERTORES ESC-1 HOMBRES',
            'DESERTORES ESC-2 MUJERES', 'DESERTORES ESC-2 HOMBRES',
            'DESERTORES ESC-3 MUJERES', 'DESERTORES ESC-3 HOMBRES',
            'DESERTORES ESC-4 MUJERES', 'DESERTORES ESC-4 HOMBRES',
            'DESERTORES ESC-5 MUJERES', 'DESERTORES ESC-5 HOMBRES',
            'DESERTORES ESC-6 MUJERES', 'DESERTORES ESC-6 HOMBRES',
            'DESERTORES ESC-7 MUJERES', 'DESERTORES ESC-7 HOMBRES',
            'DESERTORES ESC-8 MUJERES', 'DESERTORES ESC-8 HOMBRES',
            'DESERTORES ESC-9 MUJERES', 'DESERTORES ESC-9 HOMBRES',
            'OBSERVACIONES'
        ];

        $nombreLayout = "FORMATO_T_PARA_PLANEACION.xlsx";
        $titulo = "FORMATO T PARA LA DIRECCIÓN DE PLANEACIÓN";

        if (count($formatot_planeacion) > 0) {
            return Excel::download(new FormatoTReport($formatot_planeacion, $head, $titulo), $nombreLayout);
        }
    }

    /**
     * memorandum de planeacion
     */
    protected function memorandumplaneacion(Request $request)
    {
        // obtenemos la unidad en base a una sesion
        $unidadesIcatech = DB::table('tbl_unidades')->select('ubicacion')->groupby('ubicacion')->get();
        $busquedaPorMes = $request->get('busquedaMes');
        $busquedaPorUnidad = $request->get('busquedaPorUnidad');
        $meses = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');
        /**
         * CONSULTA PARA MOSTRAR INFORMACIÓN DE LOS MEMORANDUM DEL FORMATO T
         */
        if (isset($busquedaPorMes)) {
            # si la variable está inicializada se carga la consulta
            $queryGetMemo = DB::table('tbl_cursos')
                ->select(DB::raw("memos->'TURNADO_UNIDAD'->>'MEMORANDUM' AS memorandum_retorno_unidad"), 'tbl_cursos.unidad')
                ->join('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
                ->where('u.ubicacion', '=', $busquedaPorUnidad)
                ->where(DB::raw("EXTRACT(MONTH FROM TO_DATE(memos->'TURNADO_UNIDAD'->>'FECHA','YYYY-MM-DD'))"), '=', $busquedaPorMes)
                ->groupby(DB::raw("memos->'TURNADO_UNIDAD'->>'MEMORANDUM'"), 'tbl_cursos.unidad')
                ->paginate(5);
        } else {
            # si la variable no está inicializada no se carga la consulta
            $queryGetMemo = (array) null;
        }
        //dd($queryGetMemo);
        return view('reportes.memorandum_dta_formatot', compact('meses', 'queryGetMemo', 'unidadesIcatech'));
    }

    protected function cursosReportadosPlaneacion(Request $request)
    {
        if (empty($request->get('unidadseleccionado'))) {
            # si está vacio se agrega parte de la condicion
            $condition_ =  [
                'JIQUIPILAS', 'SAN CRISTOBAL', 'TAPACHULA', 'TONALA', 'YAJALON', 'REFORMA',
                'OCOSINGO', 'TUXTLA', 'CATAZAJA', 'COMITAN', 'VILLAFLORES'
            ];
        } else {
            # de no ser así se envía con la variable que tiene el request
            $condition_ = [$request->get('unidadseleccionado')];
        }


        if (empty($request->get('anio'))) {
            # si está vacio se toma el año actual
            $anio_ = Carbon::now()->year;
        } else {
            # code...
            $anio_ = $request->get('anio');
        }

        if (empty($request->get('messeleccionado'))) {
            # si está vacio se toma el mes actual
            $fecha = Carbon::parse(Carbon::now());
            $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
            $setMes_ = $meses[($fecha->format('n')) - 1];
        } else {
            $setMes_ = $request->get('messeleccionado');
        }
        // obtener el año actual --

        $meses = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');

        /**
         * OBTENEMOS LAS UNIDADES
         */
        $unidades_indice = DB::table('tbl_unidades')->select('ubicacion')->groupby('ubicacion')->get();

        $inner_ = DB::raw("(SELECT id_pre, no_control, id_curso, migrante, indigena, etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, migrante, indigena, etnia) as ar");

        $reportadosCurso = tbl_curso::select(
            'tbl_cursos.id AS id_tbl_cursos',
            'tbl_cursos.status AS estadocurso',
            'tbl_cursos.unidad',
            'tbl_cursos.plantel',
            'tbl_cursos.espe',
            'tbl_cursos.curso',
            'tbl_cursos.clave',
            'tbl_cursos.mod',
            'tbl_cursos.dura',
            DB::raw("case when extract(hour from to_timestamp(tbl_cursos.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
            DB::raw('extract(day from tbl_cursos.inicio) as diai'),
            DB::raw('extract(month from tbl_cursos.inicio) as mesi'),
            DB::raw('extract(day from tbl_cursos.termino) as diat'),
            DB::raw('extract(month from tbl_cursos.termino) as mest'),
            DB::raw("case when EXTRACT( Month FROM tbl_cursos.termino) between '7' and '9' then '1' when EXTRACT( Month FROM tbl_cursos.termino) between '10' and '12' then '2' when EXTRACT( Month FROM tbl_cursos.termino) between '1' and '3' then '3' else '4' end as pfin"),
            'tbl_cursos.horas',
            'tbl_cursos.dia',
            DB::raw("concat(tbl_cursos.hini,' ', 'A', ' ',tbl_cursos.hfin) as horario"),
            DB::raw('count(distinct(ca.id)) as tinscritos'),
            DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),
            DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),
            DB::raw("SUM(CASE WHEN ca.acreditado= 'X' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),
            DB::raw("SUM(CASE WHEN ca.acreditado='X' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),
            DB::raw("SUM(CASE WHEN ca.noacreditado='X' THEN 1 ELSE 0 END) as desertado"),
            DB::raw("SUM(DISTINCT(ins.costo)) as costo"),
            DB::raw("SUM(ins.costo) as ctotal"),
            DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),
            DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),
            DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
            DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),
            'tbl_cursos.cespecifico',
            'tbl_cursos.mvalida',
            'tbl_cursos.efisico',
            'tbl_cursos.nombre',
            'ip.grado_profesional',
            'ip.estatus',
            'i.sexo',
            'ei.memorandum_validacion',
            'tbl_cursos.mexoneracion',
            DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),
            DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
            DB::raw("sum(case when ap.discapacidad<> 'NINGUNA' then 1 else 0 end) as discapacidad"),
            DB::raw("sum(case when ar.migrante='true' then 1 else 0 end) as migrante"),
            DB::raw("sum(case when ar.indigena='true' then 1 else 0 end) as indigena"),
            DB::raw("sum(case when ar.etnia<> NULL then 1 else 0 end) as etnia"),
            'tbl_cursos.programa',
            'tbl_cursos.muni',
            'tbl_cursos.depen',
            'tbl_cursos.cgeneral',
            'tbl_cursos.sector',
            'tbl_cursos.mpaqueteria',

            DB::raw("sum( case when EXTRACT( year from (age(tbl_cursos.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),
            DB::raw("sum( CASE  WHEN  EXTRACT(YEAR FROM (AGE(tbl_cursos.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO'  THEN 1  ELSE 0 END ) as iem2"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),
            DB::raw("sum( CASE WHEN EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END ) as iem3"),
            DB::raw("sum( Case When EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 20 and 24 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),
            DB::raw("sum( CASE WHEN EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END ) as iem4"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 25 and 34 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
            db::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) between 55 and 64 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
            DB::raw("sum( case when EXTRACT(year from (age(tbl_cursos.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),

            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm5"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),

            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm1"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh1"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm2"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh2"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm3"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh3"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm4"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh4"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm5"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh5"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm6"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh6"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm7"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh7"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm8"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh8"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ca.acreditado='X' then 1 else 0 end) as aesm9"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.acreditado='X' then 1 else 0 end) as aesh9"),

            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm1"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh1"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm2"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh2"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm3"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh3"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm4"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh4"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm5"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh5"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm6"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh6"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm7"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh7"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm8"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh8"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ca.noacreditado='X' then 1 else 0 end) as naesm9"),
            DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ca.noacreditado='X' then 1 else 0 end) as naesh9"),

            DB::raw("case when tbl_cursos.arc='01' then nota else observaciones end as tnota"),
            DB::raw("count( ar.id_pre) AS totalinscripciones"),
            DB::raw("count( CASE  WHEN  ap.sexo ='MASCULINO' THEN ar.id_pre END ) AS masculinocheck"),
            DB::raw("count( CASE  WHEN ap.sexo ='FEMENINO' THEN ar.id_pre END ) AS femeninocheck"),
            DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH') AS mesturnado"),
        )
            ->JOIN('tbl_calificaciones as ca', 'tbl_cursos.id', '=', 'ca.idcurso')
            ->JOIN('instructores as i', 'tbl_cursos.id_instructor', '=', 'i.id')
            ->JOIN('instructor_perfil as ip', 'i.id', '=', 'ip.numero_control')
            ->JOIN('especialidad_instructores as ei', 'ip.id', '=', 'ei.perfilprof_id')
            ->JOIN('especialidades as e', function ($join) {
                $join->on('ei.especialidad_id', '=', 'e.id');
                $join->on('tbl_cursos.espe', '=', 'e.nombre');
            })
            ->JOIN($inner_, function ($join) {
                $join->on('ca.matricula', '=', 'ar.no_control');
                $join->on('tbl_cursos.id_curso', '=', 'ar.id_curso');
            })
            ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
            ->JOIN('tbl_inscripcion as ins', function ($join) {
                $join->on('ca.idcurso', '=', 'ins.id_curso');
                $join->on('ca.matricula', '=', 'ins.matricula');
            })
            ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
            ->WHERE('tbl_cursos.status', 'REPORTADO')
            ->WHERE('tbl_cursos.turnado', 'PLANEACION_TERMINADO')
            ->WHERE('tbl_cursos.clave', '!=', 'NULL')
            ->WHERE(DB::raw("extract(year from tbl_cursos.fecha_turnado)"), '=', $anio_)
            ->WHEREIN('u.ubicacion', $condition_);
        (!empty($request->get('messeleccionado'))) ? $cursosReporados = $reportadosCurso->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), '=', $setMes_) : " ";

        $cursosReporados = $reportadosCurso->groupby('tbl_cursos.id', 'ip.grado_profesional', 'ip.estatus', 'i.sexo', 'ei.memorandum_validacion')
            ->distinct()->get();


        return view('reportes.cursos_reportados_formatot_planeacion', compact('cursosReporados', 'meses', 'unidades_indice'));
    }

    public function funcionarios($unidad) {
        $query = clone $dacademico = clone $dacademico_unidad = clone $certificacion = clone $dunidad = clone $dplaneacion = clone $dgeneral = clone $progpres = DB::Table('tbl_organismos AS o')->Select('f.titulo','f.nombre','f.cargo','f.direccion','f.telefono','f.correo_institucional')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true')
            ->Where('f.titular', true);

        $dacademico = $dacademico->Where('o.id',16)->First();
        $certificacion = $certificacion->Where('o.id',18)->First();
        $dplaneacion = $dplaneacion->Where('o.id',6)->First();
        $dgeneral = $dgeneral->Where('o.id',1)->First();
        $progpres = $progpres->Where('o.id',9)->First();

        $dacademico_unidad = $dacademico_unidad->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DEPARTAMENTO ACADEMICO%')
            ->Where('u.unidad', $unidad)
            ->First();

        $dunidad = $dunidad->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $funcionarios = [
            'dacademico' => ['titulo'=>$dacademico->titulo,'nombre'=>$dacademico->nombre,'puesto'=>$dacademico->cargo,'direccion'=>$dacademico->direccion,'telefono'=>$dacademico->telefono,'correo'=>$dacademico->correo_institucional],
            'certificacion' => ['titulo'=>$certificacion->titulo,'nombre'=>$certificacion->nombre,'puesto'=>$certificacion->cargo,'direccion'=>$certificacion->direccion,'telefono'=>$certificacion->telefono,'correo'=>$certificacion->correo_institucional],
            'dplaneacion' => ['titulo'=>$dplaneacion->titulo,'nombre'=>$dplaneacion->nombre,'puesto'=>$dplaneacion->cargo,'direccion'=>$dplaneacion->direccion,'telefono'=>$dplaneacion->telefono,'correo'=>$dplaneacion->correo_institucional],
            'dgeneral' => ['titulo'=>$dgeneral->titulo,'nombre'=>$dgeneral->nombre,'puesto'=>$dgeneral->cargo,'direccion'=>$dgeneral->direccion,'telefono'=>$dgeneral->telefono,'correo'=>$dgeneral->correo_institucional],
            'progpres' => ['titulo'=>$progpres->titulo,'nombre'=>$progpres->nombre,'puesto'=>$progpres->cargo,'direccion'=>$progpres->direccion,'telefono'=>$progpres->telefono,'correo'=>$progpres->correo_institucional],
            'dacademico_unidad' => ['titulo'=>$dacademico_unidad->titulo,'nombre'=>$dacademico_unidad->nombre,'puesto'=>$dacademico_unidad->cargo,'direccion'=>$dacademico_unidad->direccion,'telefono'=>$dacademico_unidad->telefono,'correo'=>$dacademico_unidad->correo_institucional],
            'dunidad' => ['titulo'=>$dunidad->titulo,'nombre'=>$dunidad->nombre,'puesto'=>$dunidad->cargo,'direccion'=>$dunidad->direccion,'telefono'=>$dunidad->telefono,'correo'=>$dunidad->correo_institucional],
            'elabora' => ['nombre'=>strtoupper(Auth::user()->name),'puesto'=>strtoupper(Auth::user()->puesto)]
        ];

        return $funcionarios;
    }
}
