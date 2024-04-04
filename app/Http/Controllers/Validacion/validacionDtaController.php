<?php

namespace App\Http\Controllers\Validacion;

use PDF;
use Carbon\Carbon;
use App\Models\Instituto;
use App\Models\tbl_curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use function GuzzleHttp\json_decode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport

class validacionDtaController extends Controller {

    public function index(Request $request) {
        $unidad = $request->get('busqueda_unidad');
        $mesSearch = $request->get('mesSearchE');

        if ($mesSearch != null) {
            session(['mesBuscarE' => $mesSearch]);
        }

        $anio_actual = Carbon::now()->year; // año actual obtenido del servidor

        $formato_respuesta = DB::Table('tbl_cursos')->Select('tbl_cursos.id','tbl_cursos.resumen_formatot_unidad')
        ->Join('tbl_unidades','tbl_unidades.unidad', 'tbl_cursos.unidad')
        ->Where('tbl_unidades.ubicacion', $unidad)
        ->whereIn('tbl_cursos.turnado', ['PLANEACION','PLANEACION_TERMINADO','REPORTADO'])
        ->whereIn('tbl_cursos.status', ['TURNADO_PLANEACION','REPORTADO'])
        ->WhereMonth('tbl_cursos.fecha_turnado', $mesSearch)
        ->Where('tbl_cursos.resumen_formatot_unidad', '!=', null)
        ->First();
        // dd($formato_respuesta);


        $cursos_validar = dataFormatoT($unidad, ['DTA', 'MEMO_TURNADO_RETORNO'], null, $mesSearch, ['TURNADO_DTA']);
        // foreach ($cursos_validar as $key => $value) {
        //     // array de folios
        //     // dd($value);
        //     $temp = substr($value->folios,1);
        //     $temp = substr($temp,0, -1);
        //     $array = explode(',', $temp);
        //     // array de movimientos
        //     $temp2 = substr($value->movimientos,1);
        //     $temp2 = substr($temp2,0, -1);
        //     $array2 = explode(',', $temp2);

        //     $tempFoliosCancel = ''; $folios = ''; $bloqueFolios = '';
        //     foreach ($array2 as $key => $movimiento) {
        //         if ($movimiento == 'EXPEDIDO') {
        //             if ($bloqueFolios == '') {
        //                 $bloqueFolios = $array[$key].'-';
        //             }
        //         }
        //         if ($movimiento == 'CANCELADO') {
        //             if (isset($array[$key-1])) {
        //                 $bloqueFolios = $bloqueFolios.$array[$key-1];
        //                 $folios = $folios.','.$bloqueFolios;
        //                 $bloqueFolios = '';
        //             }
        //             $tempFoliosCancel = $tempFoliosCancel.$array[$key].',';
        //         }
        //         if (($key + 1) == count($array2) && $movimiento != 'CANCELADO') {
        //             $bloqueFolios = $bloqueFolios.$array[$key];
        //             $folios = $folios.','.$bloqueFolios;
        //             $bloqueFolios = '';
        //         }
        //     }
        //     if ($folios != '') {
        //         $folios = substr($folios,1);
        //     }
        //     if ($tempFoliosCancel != '') {
        //         $tempFoliosCancel = substr($tempFoliosCancel,0, -1);
        //     }
        //     $value->bloques_folios = $folios;
        //     $value->folios_cancelados = $tempFoliosCancel;
        // }

        $memorandum = DB::table('tbl_cursos')
            ->select(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM' AS memorandum, memos->'TURNADO_EN_FIRMA'->>'NUMERO' AS num_memo"))
            ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->where('turnado', '=', 'DTA')
            ->where('tbl_unidades.ubicacion', '=', $unidad)
            ->groupby(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM', memos->'TURNADO_EN_FIRMA'->>'NUMERO'"))
            ->first();

        $regresar_unidad = DB::table('tbl_cursos')
            ->leftjoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->where('turnado', '=', 'REVISION_DTA')
            ->where('status', '=', 'REVISION_DTA')
            ->get();

        $unidades = DB::table('tbl_unidades')->select('unidad')->where('cct', 'LIKE', '%07EIC%')->orderBy('unidad', 'asc')->get();

        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mesActual = $meses[($fecha->format('n')) - 1];
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mesActual)->first();
        $dateNow = $fechaEntregaActual->fecha_entrega . "-" . $anioActual;
        $mesInformar = $fechaEntregaActual->mes_informar;

        $convertfEAc = date_create_from_format('d-m-Y', $dateNow);
        $mesEntrega = $meses[($convertfEAc->format('n')) - 1];
        $fechaEntregaFormatoT = $convertfEAc->format('d') . ' DE ' . $mesEntrega . ' DE ' . $convertfEAc->format('Y');
        $diasParaEntrega = $this->getFechaDiff();

        return view('reportes.vista_validaciondta', compact('cursos_validar', 'unidades', 'memorandum', 'regresar_unidad', 'fechaEntregaFormatoT', 'mesInformar', 'unidad', 'diasParaEntrega', 'mesSearch', 'formato_respuesta'));
    }

    public function indexRevision(Request $request) {
        $unidades_busqueda = $request->get('busqueda_unidad');
        $mesSearch = $request->get('mesSearchD');

        $ac = Carbon::now()->year; // año actual obtenido del servidor
        $cursos_validar = dataFormatoT($unidades_busqueda, ['REVISION_DTA'], null, $mesSearch, ['REVISION_DTA']);

        $formato_respuesta = DB::Table('tbl_cursos')->Select('tbl_cursos.id','tbl_cursos.resumen_formatot_unidad')
        ->Join('tbl_unidades','tbl_unidades.unidad', 'tbl_cursos.unidad')
        ->Where('tbl_unidades.ubicacion', $unidades_busqueda)
        ->whereIn('tbl_cursos.turnado', ['PLANEACION','PLANEACION_TERMINADO','REPORTADO'])
        ->whereIn('tbl_cursos.status', ['TURNADO_PLANEACION','REPORTADO'])
        ->WhereMonth('tbl_cursos.fecha_turnado', $mesSearch)
        ->Where('tbl_cursos.resumen_formatot_unidad', '!=', null)
        ->First();

        $memorandum = DB::table('tbl_cursos')
            ->select(
                DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM' AS memorandum, memos->'TURNADO_EN_FIRMA'->>'NUMERO' AS num_memo, tbl_unidades.unidad, tbl_cursos.resumen_formatot_unidad")
            )->join('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->where('tbl_unidades.cct', 'LIKE', '%07EIC%') // verificar que solo sean unidades
            ->where('tbl_cursos.turnado', '=', 'REVISION_DTA')
            ->where('tbl_cursos.status', '=', 'REVISION_DTA')
            ->whereMonth('tbl_cursos.fecha_turnado', $mesSearch)
            ->groupby(DB::raw("memos->'TURNADO_DTA'->>'MEMORANDUM', memos->'TURNADO_EN_FIRMA'->>'NUMERO', tbl_unidades.unidad, tbl_cursos.resumen_formatot_unidad"))
            ->get();

        $unidades = DB::table('tbl_unidades')->select('unidad')->where('cct', 'LIKE', '%07EIC%')->orderBy('unidad', 'asc')->get();

        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mesActual = $meses[($fecha->format('n')) - 1];
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mesActual)->first();
        $dateNow = $fechaEntregaActual->fecha_entrega . "-" . $anioActual;
        $mesInformar = $fechaEntregaActual->mes_informar;

        $convertfEAc = date_create_from_format('d-m-Y', $dateNow);
        $mesEntrega = $meses[($convertfEAc->format('n')) - 1];
        $fechaEntregaFormatoT = $convertfEAc->format('d') . ' DE ' . $mesEntrega . ' DE ' . $convertfEAc->format('Y');

        $diasParaEntrega = $this->getFechaDiff();

        return view('reportes.vista_supervisiondta', compact('cursos_validar', 'unidades', 'memorandum', 'unidades_busqueda', 'diasParaEntrega', 'mesInformar', 'fechaEntregaFormatoT', 'diasParaEntrega', 'mesSearch', 'formato_respuesta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // variables y creación de la fecha de retorno
        $fecha_actual = Carbon::now();
        $date = $fecha_actual->format('Y-m-d'); // fecha
        $fecha_nueva = $fecha_actual->format('d-m-Y');

        // validamos que este inicializada la variable
        if (isset($_POST['envioDireccionDta'])) {
            # en esta parte se envía a la jefa de DTA para validación y envío a Planeación
            // TURNADO_VALIDACION_DIRECCION_DTA[FECHA: "XXXX-XX-XX"]
            $turnado_revision_dta = [
                'FECHA' => $date
            ];
            if (!empty($_POST['chkcursos'])) {
                # entramos al loop
                foreach ($_POST['chkcursos'] as $key => $value) {
                    $observaciones_revision_dta = [
                        'OBSERVACION_REVISION_JEFE_DTA' =>  $_POST['comentarios_enlaces'][$key]
                    ];
                    # modificaciones
                    \DB::table('tbl_cursos')
                        ->where('id', $value)
                        ->update([
                            'memos' => DB::raw("jsonb_set(memos, '{TURNADO_REVISION_DTA}', '" . json_encode($turnado_revision_dta) . "', true)"),
                            'status' => 'REVISION_DTA',
                            'turnado' => 'REVISION_DTA',
                            'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_REVISION_DIRECCION_DTA}','" . json_encode($observaciones_revision_dta) . "', true)"),
                        ]);
                }
                return redirect()->route('validacion.cursos.enviados.dta')
                    ->with('success', sprintf('CURSOS ENVIADOS A LA DIRECCIÓN PARA REVISIÓN FINAL!'));
            } else {
                # regresamos y mandamos un mensaje de error
                return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
            }
        } else {
            # si la variable no fue inicializada se genera la siguiente validación para generar el documento
            $validacion = $request->get('validarEnDta');
            if (isset($validacion)) {
                # hacemos un switch
                switch ($validacion) {
                    case 'GenerarMemorandum':
                        $_SESSION['memo_retorno1'] = $nume_memo = $request->num_memo_devolucion;
                        # entramos a un loop y antes checamos que se haya seleccionado cursos para realizar esta operacion
                        $unidadSeleccionada = $request->get('unidadActual');
                        if ($unidadSeleccionada != 'all') {
                            if (!empty($_POST['chkcursos'])) {
                                $unidadeSearch = \DB::table('tbl_unidades')->where('ubicacion', '=', $unidadSeleccionada)->pluck('unidad');

                                $cursosNul = \DB::table('tbl_cursos')
                                    ->select('id', 'memos', 'observaciones_formato_t')
                                    ->wherein('unidad', $unidadeSearch)
                                    ->where('status', '=', 'TURNADO_DTA')
                                    ->where('memos', '=', null)
                                    ->where('observaciones_formato_t', '=', null)->get();

                                if (count($cursosNul) > 0) {
                                    foreach ($cursosNul as $key => $value) {
                                        $memos1 = $value->memos != null ? json_decode($value->memos, true) : null;
                                        $observaciones1 = $value->observaciones_formato_t != null ? json_decode($value->observaciones_formato_t, true) : null;

                                        $comentarios_envio_dta1 = [
                                            'COMENTARIOS_UNIDAD' =>  ''
                                        ];
                                        $array_memosDTA1 = [
                                            'TURNADO_DTA' => ''
                                        ];

                                        \DB::table('tbl_cursos')
                                            ->where('id', '=', $value->id)
                                            ->update([
                                                'memos' => $memos1 != null ? $memos1 : \DB::raw("'" . json_encode($comentarios_envio_dta1) . "'::jsonb"),
                                                'turnado' => 'DTA',
                                                'observaciones_formato_t' => $observaciones1 != null ? $observaciones1 : \DB::raw("'" . json_encode($array_memosDTA1) . "'::jsonb"),
                                            ]);
                                    }
                                }

                                // se reinician los cursos marcados anteriormente
                                $cursosChecks = \DB::table('tbl_cursos')
                                    ->wherein('unidad', $unidadeSearch)
                                    ->where('status', '=', 'TURNADO_DTA')->update([
                                        'memos->ENLACE_TURNADO_RETORNO' => '',
                                        'turnado' => 'DTA'
                                    ]);

                                /* if ($cursosChecks != null) {
                                    foreach ($cursosChecks as $value) {
                                        $memos = $value->memos != null ? json_decode($value->memos, true) : null;
                                        $observaciones_enlace = $value->observaciones_formato_t != null ? json_decode($value->observaciones_formato_t, true) : null;

                                        if ($memos != null && $observaciones_enlace != null) {
                                            foreach ($memos as $key => $value1) {
                                                if ($key == 'ENLACE_TURNADO_RETORNO') {
                                                    unset($memos[$key]);
                                                }
                                            }

                                            foreach ($observaciones_enlace as $key2 => $value2) {
                                                if ($key2 == 'OBSERVACION_ENLACES_RETORNO_UNIDAD') {
                                                    unset($observaciones_enlace[$key2]);
                                                }
                                            }

                                            \DB::table('tbl_cursos')
                                            ->where('id', '=', $value->id)
                                            ->update([
                                                'memos' => $memos,
                                                'turnado' => 'DTA',
                                                'observaciones_formato_t' => $observaciones_enlace
                                            ]);
                                        }
                                    }
                                } */

                                $memos_retorno = [
                                    'NUMERO_MEMO' => $nume_memo,
                                    'FECHA' => $date
                                ];

                                # si no están vacios enviamos a un loop
                                foreach (array_combine($_POST['chkcursos'], $_POST['comentarios_enlaces']) as $key => $value) {
                                    // $comentarios_retorno_unidad = [
                                    //     'OBSERVACION_ENLACES_RETORNO_UNIDAD' =>  $value
                                    // ];
                                    /**
                                     * se actualizan los registros seleccionados para ver el curso
                                     */
                                    \DB::table('tbl_cursos')
                                        ->where('id', $key)
                                        ->update([
                                            'memos' => DB::raw("jsonb_set(memos, '{ENLACE_TURNADO_RETORNO}', '" . json_encode($memos_retorno) . "', true)"),
                                            'turnado' => 'MEMO_TURNADO_RETORNO',
                                            'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_ENLACES_RETORNO_UNIDAD}', '" . json_encode($value) . "', true)")
                                        ]);
                                }

                                // $unidadSeleccionada = $request->get('unidadActual');
                                $total = count($_POST['chkcursos']);
                                $mes = '1';

                                $reg_cursos = DB::table('tbl_cursos')
                                    ->select(
                                        DB::raw("case when EXTRACT( Month FROM termino) = '1' then 'ENERO' when EXTRACT( Month FROM termino) = '2' then 'FEBRERO' when EXTRACT( Month FROM termino) = '3' then 'MARZO' when EXTRACT( Month FROM termino) = '4' then 'ABRIL' when EXTRACT( Month FROM termino) = '5' then 'MAYO' when EXTRACT( Month FROM termino) = '6' then 'JUNIO' when EXTRACT( Month FROM termino) = '7' then 'JULIO' when EXTRACT( Month FROM termino) = '8' then 'AGOSTO' when EXTRACT( Month FROM termino) = '9' then 'SEPTIEMBRE' when EXTRACT( Month FROM termino) = '10' then 'OCTUBRE' when EXTRACT( Month FROM termino) = '11' then 'NOVIEMBRE' else 'DICIEMBRE' end AS mes"),
                                        'unidad',
                                        'espe',
                                        'curso',
                                        'clave',
                                        'status',
                                        DB::raw("extract(year from termino) AS fecha_termino"),
                                        DB::raw("observaciones_formato_t->'OBSERVACION_ENLACES_RETORNO_UNIDAD' AS comentario_enlaces_retorno")
                                    )
                                    ->where(DB::raw("memos->'ENLACE_TURNADO_RETORNO'->>'NUMERO_MEMO'"), $nume_memo)
                                    ->where('turnado', 'MEMO_TURNADO_RETORNO')
                                    ->groupby(
                                        'unidad',
                                        'curso',
                                        'mod',
                                        'inicio',
                                        'termino',
                                        'nombre',
                                        'clave',
                                        'ciclo',
                                        'memos->TURNADO_EN_FIRMA->FECHA',
                                        DB::raw("observaciones_formato_t->'OBSERVACION_ENLACES_RETORNO_UNIDAD'"),
                                        'espe',
                                        'status'
                                    )
                                    ->orderby('mes')
                                    ->get();


                                // OTRO REGISTRO PARA CARGAR EL TOTAL DE REGISTROS
                                $total_turnado_dta = DB::table('tbl_cursos')
                                    ->select(DB::raw("COUNT(tbl_cursos.id) AS total_cursos_turnado_dta"))
                                    ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                    ->WHEREIN('tbl_cursos.status', ['TURNADO_DTA', 'TURNADO_PLANEACION'])
                                    ->WHEREIN('tbl_cursos.turnado', ['DTA', 'PLANEACION'])
                                    ->WHERE('tbl_unidades.ubicacion', '=', $unidadSeleccionada)
                                    ->WHEREIN(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), ['MARZO', 'ABRIL'])
                                    ->get();

                                // ENVIADOS A PLANEACION
                                /* $total_turnado_planeacion = DB::table('tbl_cursos')
                                    ->select(DB::raw("COUNT(tbl_cursos.id) AS total_cursos_turnado_planeacion"))
                                    ->JOIN('tbl_unidades','tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                                    ->WHERE('tbl_cursos.status', '=', 'TURNADO_PLANEACION')
                                    ->WHERE('tbl_cursos.turnado', '=', 'PLANEACION')
                                    ->WHERE('tbl_unidades.ubicacion', '=', $unidadSeleccionada)
                                    ->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), '=', 'ABRIL')
                                    ->get(); */

                                $fechaTurnado = DB::table('tbl_cursos')->where('id', '=', $_POST['chkcursos'][0])->get();
                                $total_turnado_planeacion = DB::table('tbl_cursos')
                                    ->select(DB::raw("COUNT(tbl_cursos.id) AS total_cursos_turnado_planeacion"))
                                    ->whereIn('unidad', $unidadeSearch)
                                    ->where('fecha_turnado', '=', $fechaTurnado[0]->fecha_turnado)
                                    ->where('fecha_envio', '=', $fechaTurnado[0]->fecha_envio)
                                    ->get();
                                    // ->whereIn('turnado', ['DTA', 'MEMO_TURNADO_RETORNO'])
                                    // ->where('turnado', '=', 'DTA')
                                    // ->orWhere('turnado', '=', 'MEMO_TURNADO_RETORNO')

                                // $sum_total = $total_turnado_planeacion[0]->total_cursos_turnado_planeacion + $total;
                                $fecha_envio = $fechaTurnado[0]->fecha_envio;
                                $sum_total = $total_turnado_planeacion[0]->total_cursos_turnado_planeacion;
                                $totalReportados = $total_turnado_planeacion[0]->total_cursos_turnado_planeacion - $total;

                                $mesReportado = Carbon::parse($fechaTurnado[0]->fecha_envio);
                                $mesReportado2 = $mesReportado->format("F");
                                switch ($mesReportado2) {
                                    case 'January':
                                        $mesReportado2 = 'Enero';
                                        break;
                                    case 'February':
                                        $mesReportado2 = 'Febrero';
                                        break;
                                    case 'March':
                                        $mesReportado2 = 'Marzo';
                                        break;
                                    case 'April':
                                        $mesReportado2 = 'Abril';
                                        break;
                                    case 'May':
                                        $mesReportado2 = 'Mayo';
                                        break;
                                    case 'June':
                                        $mesReportado2 = 'Junio';
                                        break;
                                    case 'July':
                                        $mesReportado2 = 'Julio';
                                        break;
                                    case 'August':
                                        $mesReportado2 = 'Agosto';
                                        break;
                                    case 'September':
                                        $mesReportado2 = 'Septiembre';
                                        break;
                                    case 'October':
                                        $mesReportado2 = 'Octubre';
                                        break;
                                    case 'November':
                                        $mesReportado2 = 'Noviembre';
                                        break;
                                    case 'December':
                                        $mesReportado2 = 'Diciembre';
                                        break;
                                }
                                $fechaArray = explode('-', $mesReportado);
                                $diaArray = explode(' ', $fechaArray[2]);

                                $comentarios_enviados = $_POST['comentarios_enlaces'];
                                $elabora = Auth::user()->name;
                                $correo_institucional = Auth::user()->correo_institucional;
                                $reg_unidad = DB::table('tbl_unidades')->select(
                                    'unidad',
                                    'dunidad',
                                    'academico',
                                    'vinculacion',
                                    'dacademico',
                                    'pdacademico',
                                    'pdunidad',
                                    'pacademico',
                                    'pvinculacion',
                                    'jcyc',
                                    'pjcyc',
                                    'ubicacion'
                                )->where('unidad', $unidadSeleccionada)->first();
                                $leyenda = Instituto::first();
                                $leyenda = $leyenda->distintivo;
                                $direccion = DB::table('tbl_unidades')->WHERE('unidad',$unidadSeleccionada)->VALUE('direccion');
                                $direccion = explode("*", $direccion);
                                $pdf = PDF::loadView('reportes.memounidad', compact('reg_cursos', 'reg_unidad', 'nume_memo', 'total', 'fecha_nueva', 'elabora', 'total_turnado_dta', 'comentarios_enviados', 'total_turnado_planeacion', 'sum_total', 'totalReportados', 'mesReportado2', 'diaArray', 'leyenda','correo_institucional','direccion','fecha_envio'));
                                return $pdf->stream('Memo_Unidad.pdf');
                            } else {
                                return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                            }
                        } else {
                            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE DEBE SELECCIONAR UNA UNIDAD ANTES DE GENERAR UN MEMORANDUM']);
                        }
                        break;

                    default:
                        # break
                        break;
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storetodta(Request $request)
    {
        // variables y creación de la fecha de retorno
        $fecha_actual = Carbon::now();
        $date = $fecha_actual->format('Y-m-d'); // fecha

        $validacion = $request->get('validarDireccionDta');
        if (isset($validacion)) {
            switch ($validacion) {
                case 'EnviarPlaneacion':
                    # enviar a planeación
                    # en esta parte del código tenemos que envíar a planeación
                    // TURNADO_PLANEACION[“NUMERO”:”XXXXXX”,FECHA:”XXXX-XX-XX”]
                    $turnado_planeacion = [
                        'FECHA' => $date
                    ];
                    if (!empty($request->get('chkcursos'))) {
                        # checamos que la variable no se encuentre vacia
                        foreach ($_POST['chkcursos'] as $key => $value) {
                            // $observaciones_revision_a_planeacion = [
                            //     'OBSERVACION_REVISION_A_PLANEACION' =>
                            // ];
                            # entremos en el loop
                            \DB::table('tbl_cursos')
                                ->where('id', $value)
                                ->update([
                                    'memos' => DB::raw("jsonb_set(memos, '{TURNADO_PLANEACION}', '" . json_encode($turnado_planeacion) . "', true)"),
                                    'status' => 'TURNADO_PLANEACION',
                                    'turnado' => 'PLANEACION',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_REVISION_A_PLANEACION}', '" . json_encode($_POST['comentarios'][$key]) . "', true)")
                                ]);
                        }
                        return redirect()->route('validacion.dta.revision.cursos.indice')
                            ->with('success', sprintf('CURSOS ENVIADOS A PLANEACIÓN PARA REVISIÓN!'));
                    } else {
                        # hay cursos vacios, regresamos y mandamos un mensaje de error
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }
                    break;
                case 'RegresarEnlaceDta':
                    # regresar a la unidad
                    $regresar_enlace_dta = [
                        'FECHA' => $date
                    ];
                    if (!empty($request->get('chkcursos'))) {
                        # si no está vacio la variable iniciamos un loop
                        foreach ($_POST['chkcursos'] as $key => $value) {
                            # entramos en el bucle para actualizar los registros datos y enviarlos nuevamente a los enlaces
                            $observaciones_revision_dta_enlaces = [
                                'OBSERVACION_RETORNO_ENLACES' =>  $_POST['comentarios'][$key]
                            ];
                            # entremos en el loop
                            \DB::table('tbl_cursos')
                                ->where('id', $value)
                                ->update([
                                    'memos' => DB::raw("jsonb_set(memos, '{TURNADO_ENLACE_DTA}','" . json_encode($regresar_enlace_dta) . "'::jsonb)"),
                                    'status' => 'TURNADO_DTA',
                                    'turnado' => 'DTA',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_REVISION_ENLACES_DTA}', '" . json_encode($observaciones_revision_dta_enlaces) . "'::jsonb)")
                                ]);
                        }
                        return redirect()->route('validacion.dta.revision.cursos.indice')
                            ->with('success', sprintf('CURSOS ENVIADOS A PLANEACIÓN PARA REVISIÓN!'));
                    } else {
                        # hay cursos vacios, regresamos y mandamos un mensaje de error
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }
                    break;

                default:
                    # por defecto
                    break;
            }
        }
    }

    public function storedtafile(Request $request)
    {

        $numero_memo = $request->get('numero_memo_devolucion'); // número de memo
        $cursoschk = $request->get('check_cursos_dta');
        /***
         * vamos a checar el curso de dta
         */
        //checamos si trae el número de memo
        if (!empty($numero_memo)) {
            # seguimos la validación y lógica del sistema
            if (!empty($cursoschk)) {
                # si entramos en esta parte es que hay registros de cursos
                if ($request->hasFile('memorandum_regreso_unidad')) {
                    # obtenemos el valor del archivo memo
                    $validator = validator::make($request->all(), [
                        'memorandum_regreso_unidad' => 'mimes:pdf|max:2048'
                    ]);
                    if ($validator->fails()) {
                        # mandar mensaje de error si falla el cargado del archivo
                        return back()->withInput()->withErrors([$validator]);
                    } else {
                        # si la validación no falla es hora de subir el archivo
                        $memo = str_replace('/', '_', $numero_memo);
                        /**
                         * aquí vamos a verificar que el archivo no se encuentre guardado
                         * previamente en el sistema de archivos del sistema de ser así se
                         * remplazará el archivo porel que se subirá a continuación
                         */
                        // construcción del archivo
                        $archivo_memo = 'uploadFiles/memoRegresoUnidad/' . $memo . '/memorandum_regreso_unidad.pdf';
                        if (Storage::exists($archivo_memo)) {
                            #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                            Storage::delete($archivo_memo);
                        }
                        $archivo_memo_to_dta = $request->file('memorandum_regreso_unidad'); # obtenemos el archivo
                        $url_archivo_memo = $this->uploaded_memo_retorno_unidad_file($archivo_memo_to_dta, $memo, 'memoRegresoUnidad'); #invocamos el método
                    }
                } else {
                    # si está vacio sólo cargamos la url
                    $url_archivo_memo = null;
                }
                $fecha_ahora = Carbon::now();
                $date = $fecha_ahora->format('Y-m-d'); // fecha
                /**
                 * aquí vamos a vaciar el arreglo en un ciclo que vamos a iterar para obtener los valores y hacer multiples
                 * actualizaciones de los registros para enviar la información
                 */
                $turnado_unidad = [
                    'FECHA' => $date,
                    'MEMORANDUM' => $url_archivo_memo,
                    'NUMERO' => $numero_memo
                ];
                /**
                 * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                 */
                # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                $data = explode(",", $cursoschk);
                // GENERARMOS UN ARREGLO O PILA
                $pila = [];
                foreach ($data as $key) {
                    array_push($pila, $key);
                }

                foreach (array_combine($pila, $_POST['comentarios_enlaces']) as $key => $comentarios) {
                    $comentarios_regreso_unidad = [
                        'OBSERVACION_RETORNO_UNIDAD' =>  $comentarios
                    ];
                    \DB::table('tbl_cursos')
                        ->where('id', $key)
                        ->update([
                            'memos' => DB::raw("jsonb_set(memos, '{TURNADO_UNIDAD}', '" . json_encode($turnado_unidad) . "', true)"),
                            'status' => 'RETORNO_UNIDAD',
                            'turnado' => 'UNIDAD',
                            'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACION_RETORNO_UNIDAD}', '" . json_encode($comentarios) . "', true)")
                        ]);
                }
                // enviar  a la página de inicio del módulo si el proceso fue satisfactorio
                return redirect()->route('validacion.cursos.enviados.dta')
                    ->with('success', sprintf('CURSOS TURNADO A LA UNIDAD CORRESPONDIENTE!'));
            } else {
                # no hay cursos (están vacios) se tiene que cargar un mensaje de error
                return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
            }
        } else {
            # no hay número de memo está vacio se envía un mensaje de error
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE CUENTA CON NÚMERO DE MEMORANDUM!']);
        }
    }

    protected function uploaded_memo_retorno_unidad_file($file, $memo, $subpath)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim("memorandum_regreso_unidad." . $extensionFile);
        $path = '/' . $subpath . '/' . $memo . '/' . $documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/' . $subpath . '/' . $memo . "/" . $documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function entrega_planeacion(Request $request)
    {
        $valor = $request->get('validarDireccionDta');
        $mesUnity = $request->get('txtUnity');
        $totalCursos = $request->get('totalCursos');
        if (isset($valor)) {
            # si la variable está inicializada procedemos a meterlo en el switch
            switch ($valor) {
                case 'generarMemoPlaneacion':
                    # generamos el memo de entrega a planeacion.
                    $numMemo = $request->get('num_memo_devolucion');
                    return $this->generarMemorandumPlaneacion($numMemo, $mesUnity, $totalCursos);
                    break;
                case 'RegresarEnlaceDta':
                    /**
                     * TURNADO_RETORNO_ENLACES
                     */
                    # regresamos el paquete a los enlaces que no está bien
                    $cursoschk = $request->get('chkcursos');
                    if (!empty($cursoschk)) {
                        $fecha_ahora = Carbon::now();
                        $date = $fecha_ahora->format('Y-m-d'); // fecha
                        # generamos el código para enviar de regreso a los enlaces los cursos que no han sido satisfactorios
                        $turnado_retorno_unidad = [
                            'FECHA' => $date,
                        ];

                        // DB::enableQueryLog(); // Enable query log

                        foreach ($_POST['chkcursos'] as $key => $value) {
                            # recorremos el bucle para vaciar nuestro contenido en la consulta
                            $observaciones_retorno_enlace = [
                                'OBSERVACION_PARA_ENLACES_DTA' =>  $_POST['comentarios_direccion_dta'][$key],
                            ];
                            # modificaciones
                            \DB::table('tbl_cursos')->where('id', $value)
                                ->update([
                                    'memos' =>  DB::raw("jsonb_set(memos, '{TURNADO_RETORNO_ENLACES}', '" . json_encode($turnado_retorno_unidad) . "', true)"),
                                    'status' => 'TURNADO_DTA',
                                    'turnado' => 'DTA',
                                    'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{OBSERVACIONES_RETORNO_ENLACES}', '" . json_encode($observaciones_retorno_enlace) . "', true)")
                                ]);
                        }


                        // dd(DB::getQueryLog()); // Show results of log

                        return redirect()->route('validacion.dta.revision.cursos.indice')
                            ->with('success', sprintf('CURSOS ENVIADOS DE REGRESO PARA LOS ENLACES DTA!'));
                    } else {
                        # enviamos un mensaje de que no se pudo generar debido a que no hay registros
                        return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
                    }

                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    private function generarMemorandumPlaneacion($num_memo_planeacion, $mesUnity, $totalCursos)
    {
        if (isset($num_memo_planeacion)) {
            /**
             * obtener el mes de los cursos que se encuentran en el registro del módulo
             * de la dirección DTA
             */
            /*$queryMesMemo = DB::table('tbl_cursos')
                ->select(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH') AS mes_obtenido"))
                ->WHERE("turnado", 'REVISION_DTA')
                ->groupBy(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"))
                ->orderBy(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), 'desc')
                ->limit(1)
                ->get(); */

            # GENERAMOS EL DOCUMENTO EN PDF
            $value = 'JEFE DE DEPARTAMENTO DE PROGRAMACION Y PRESUPUESTO';
            $jefdepto = 'JEFE DE DEPARTAMENTO DE CERTIFICACION Y CONTROL';
            // fecha actual
            $fecha_ahora = Carbon::now();
            $fecha = $fecha_ahora->format('Y-m-d'); // fecha
            // arreglo de meses
            $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $fechaFormato = Carbon::parse($fecha);
            $mes = $meses[($fechaFormato->format('n')) - 1];
            $fecha_ahora_espaniol = $fechaFormato->format('d') . ' de ' . $mes . ' de ' . $fechaFormato->format('Y');
            // registro de las unidades
            $reg_unidad = DB::table('tbl_unidades')->select(
                'academico',
                'vinculacion',
                'dacademico',
                'pdacademico',
                'pdunidad',
                'pacademico',
                'pvinculacion',
                'jcyc',
                'pjcyc',
                'dgeneral',
                'pdgeneral'
            )->groupby(
                'academico',
                'vinculacion',
                'dacademico',
                'pdacademico',
                'pdunidad',
                'pacademico',
                'pvinculacion',
                'jcyc',
                'pjcyc',
                'dgeneral',
                'pdgeneral'
            )->first();

            switch ($mesUnity) {
                case '01':
                    $mesUnity = 'ENERO';
                    break;
                case '02':
                    $mesUnity = 'FEBRERO';
                    break;
                case '03':
                    $mesUnity = 'MARZO';
                    break;
                case '04':
                    $mesUnity = 'ABRIL';
                    break;
                case '05':
                    $mesUnity = 'MAYO';
                    break;
                case '06':
                    $mesUnity = 'JUNIO';
                    break;
                case '07':
                    $mesUnity = 'JULIO';
                    break;
                case '08':
                    $mesUnity = 'AGOSTO';
                    break;
                case '09':
                    $mesUnity = 'SEPTIEMBRE';
                    break;
                case '10':
                    $mesUnity = 'OCTUBRE';
                    break;
                case '11':
                    $mesUnity = 'NOVIEMBRE';
                    break;
                case '12':
                    $mesUnity = 'DICIEMBRE';
                    break;
            }

            $directorio = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$value}%")->first();
            $jefeDepto = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('puesto', 'LIKE', "%{$jefdepto}%")->first();
            $directorPlaneacion = DB::table('directorio')->select('nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto')->where('id', 14)->first();
            $leyenda = Instituto::first();
            $leyenda = $leyenda->distintivo;
            $direccion = DB::table('tbl_unidades')->WHERE('unidad','TUXTLA')->VALUE('direccion');
            $direccion = explode("*", $direccion);
            $pdf = PDF::loadView('layouts.pdfpages.formatot_entrega_planeacion', compact('fecha_ahora_espaniol', 'reg_unidad', 'num_memo_planeacion', 'directorio', 'jefeDepto', 'directorPlaneacion', 'mesUnity', 'totalCursos', 'leyenda','direccion'));
            // return $pdf->stream('Memorandum_entrega_formato_t_a_planeacion.pdf');
            return $pdf->stream('Memorandum_entrega_formato_t_a_planeacion.pdf');
        } else {
            # enviamos mensaje de error o direccionamos para enviarlo con el mensaje de error
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, SE NECESITA EL NÚMERO DE MEMORANDUM']);
        }
    }

    protected function getFechaDiff() {
        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mes = $meses[($fecha->format('n')) - 1];
        $fechaActual = Carbon::now()->format('d-m-Y');
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $mes)->first();
        $fEAc = $fechaEntregaActual->fecha_entrega . "-" . $anioActual;
        $comfechaActual = strtotime($fechaActual);
        $convertfEAc = date_create_from_format('d-m-Y', $fEAc);
        $confEAc = date_format($convertfEAc, 'd-m-Y');
        $comconfEAc = strtotime($confEAc); // fecha actual de entrega
        $dias = (strtotime($confEAc) - strtotime($fechaActual)) / 86400;
        $dias = abs($dias);
        $dias = floor($dias);

        return $dias;
    }

    protected function xlsExportReporteFormatotEnlacesUnidad(Request $request) {
        $anio_actual = Carbon::now()->year;
        $unidadActual = $request->unidad_;
        $mesSearch = $request->mes_;

        $formatot_enlace_dta = dataFormatoT($unidadActual, ['DTA', 'MEMO_TURNADO_RETORNO'], null, $mesSearch, ['TURNADO_DTA']);
        foreach ($formatot_enlace_dta as $value) {
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

        //     // array de folios
        //     $temp = substr($value->folios,1);
        //     $temp = substr($temp,0, -1);
        //     $array = explode(',', $temp);
        //     // array de movimientos
        //     $temp2 = substr($value->movimientos,1);
        //     $temp2 = substr($temp2,0, -1);
        //     $array2 = explode(',', $temp2);

        //     $tempFoliosCancel = ''; $folios = ''; $bloqueFolios = '';
        //     foreach ($array2 as $key => $movimiento) {
        //         if ($movimiento == 'EXPEDIDO') {
        //             if ($bloqueFolios == '') {
        //                 $bloqueFolios = $array[$key].'-';
        //             }
        //         }
        //         if ($movimiento == 'CANCELADO') {
        //             if (isset($array[$key-1])) {
        //                 $bloqueFolios = $bloqueFolios.$array[$key-1];
        //                 $folios = $folios.','.$bloqueFolios;
        //                 $bloqueFolios = '';
        //             }
        //             $tempFoliosCancel = $tempFoliosCancel.$array[$key].',';
        //         }
        //         if (($key + 1) == count($array2) && $movimiento != 'CANCELADO') {
        //             $bloqueFolios = $bloqueFolios.$array[$key];
        //             $folios = $folios.','.$bloqueFolios;
        //             $bloqueFolios = '';
        //         }
        //     }
        //     if ($folios != '') {
        //         $folios = substr($folios,1);
        //     }
        //     if ($tempFoliosCancel != '') {
        //         $tempFoliosCancel = substr($tempFoliosCancel,0, -1);
        //     }
        //     $value->folios = $folios;
        //     $value->movimientos = $tempFoliosCancel;
        }

        $head = [
            'MES REPORTADO', 'UNIDAD DE CAPACITACION', 'TIPO DE PLANTEL (UNIDAD, AULA MOVIL, ACCION MOVIL O CAPACITACION EXTERNA)', 'ESPECIALIDAD', 'CURSO', 'CLAVE DEL GRUPO', 'MODALIDAD', 'DURACION TOTAL EN HORAS', 'TURNO', 'DIA INICIO', 'MES INICIO', 'DIA TERMINO', 'MES TERMINO', 'PERIODO', 'HRS. DIARIAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MASC',
            'EGRESADOS', 'EGRESADOS FEMENINO', 'EGRESADO MASCULINO', 'DESERCION', 'COSTO TOTAL DEL CURSO POR PERSONA', 'INGRESO TOTAL', 'CUOTA MIXTA', 'EXONERACION MUJERES', 'EXONERACION HOMBRES', 'REDUCCION CUOTA MUJERES', 'REDUCCION CUOTA HOMBRES', 'NUMERO DE CONVENIO ESPECIFICO', 'MEMO DE VALIDACION DEL CURSO', 'ESPACIO FISICO',
            'NOMBRE DEL INSTRUCTOR', 'ESCOLARIDAD DEL INSTRUCTOR', 'DOCUMENTO ADQUIRIDO', 'SEXO', 'MEMO DE VALIDACION', 'MEMO DE AUTORIZACION DE EXONERACION', 'EMPLEADOS', 'DESEMPLEADOS', 'DISCAPACITADOS', 'MIGRANTES','ADOLESCENTES EN CONDICION DE CALLE','MUJERES JEFAS DE FAMILIA',
            'INDIGENA', 'RECLUSOS', 'PROGRAMA ESTRATEGICO', 'MUNICIPIO', 'ZE', 'REGION', 'DEPENDENCIA BENEFICIADA', 'CONVENIO GENERAL', 'CONVENIO CON EL SECTOR PUBLICO O PRIVADO', 'MEMO DE VALIDACION DE PAQUETERIA', 'GRUPO VULNERABLE',
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
            'OBSERVACIONES'
        ];

        $nombreLayout = "FORMATO_T_PARA_ENLACES_DIRECCION_TECNICA_ACADEMICA.xlsx";
        $titulo = "FORMATO T PARA LOS ENLACES DE DIRECCIÓN TÉCNICA ACADÉMICA";

        if (count($formatot_enlace_dta) > 0) {
            return Excel::download(new FormatoTReport($formatot_enlace_dta, $head, $titulo), $nombreLayout);
        }
    }

    /**
     * funcion protegida hecha para exportar el reporte T de formato para Directores de la dirección DTA
     */
    protected function xlsExportReporteFormatoTDirectorDTA(Request $request)
    {
        $anioActual = Carbon::now()->year;

        $reporteDirectorDTA = dataFormatoT($request->unidadD, ['REVISION_DTA'], null, $request->mesSearch, ['REVISION_DTA']);
        foreach ($reporteDirectorDTA as $value) {
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
        }

        $cabecera = [
            'MES REPORTADO', 'UNIDAD DE CAPACITACION', 'TIPO DE PLANTEL (UNIDAD, AULA MOVIL, ACCION MOVIL O CAPACITACION EXTERNA)', 'ESPECIALIDAD', 'CURSO', 'CLAVE DEL GRUPO', 'MODALIDAD', 'DURACION TOTAL EN HORAS', 'TURNO', 'DIA INICIO', 'MES INICIO', 'DIA TERMINO', 'MES TERMINO', 'PERIODO', 'HRS. DIARIAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MASC',
            'EGRESADOS', 'EGRESADOS FEMENINO', 'EGRESADO MASCULINO', 'DESERCION', 'COSTO TOTAL DEL CURSO POR PERSONA', 'INGRESO TOTAL', 'CUOTA MIXTA', 'EXONERACION MUJERES', 'EXONERACION HOMBRES', 'REDUCCION CUOTA MUJERES', 'REDUCCION CUOTA HOMBRES', 'NUMERO DE CONVENIO ESPECIFICO', 'MEMO DE VALIDACION DEL CURSO', 'ESPACIO FISICO',
            'NOMBRE DEL INSTRUCTOR', 'ESCOLARIDAD DEL INSTRUCTOR', 'DOCUMENTO ADQUIRIDO', 'SEXO', 'MEMO DE VALIDACION', 'MEMO DE AUTORIZACION DE EXONERACION', 'EMPLEADOS', 'DESEMPLEADOS', 'DISCAPACITADOS', 'MIGRANTES','ADOLESCENTES EN CONDICION DE CALLE','MUJERES JEFAS DE FAMILIA',
            'INDIGENA', 'RECLUSOS', 'PROGRAMA ESTRATEGICO', 'MUNICIPIO', 'ZE', 'REGION', 'DEPENDENCIA BENEFICIADA', 'CONVENIO GENERAL', 'CONVENIO CON EL SECTOR PUBLICO O PRIVADO', 'MEMO DE VALIDACION DE PAQUETERIA', 'GRUPO VULNERABLE',
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
            'OBSERVACIONES'
        ];

        $nombreLayout = "FORMATO_T_PARA_DIRECTOR_DE_DIRECCION_TECNICA_ACADEMICA.xlsx";
        $titulo = "FORMATO T PARA DIRECTOR/A DE DIRECCIÓN TÉCNICA ACADÉMICA";

        if (count($reporteDirectorDTA) > 0) {
            return Excel::download(new FormatoTReport($reporteDirectorDTA, $cabecera, $titulo), $nombreLayout);
        }
    }

    /**
     * funciones de aperturado en el indice de reporte de apertura
     */
    protected function ReporteAperturaIndexDta(Request $request)
    {
        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");

        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mesActual = $meses[($fecha->format('n')) - 1];
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mesActual)->first();
        $dateNow = $fechaEntregaActual->fecha_entrega . "-" . $anioActual;
        $convertfEAc = date_create_from_format('d-m-Y', $dateNow);
        $mesEntrega = $meses[($convertfEAc->format('n')) - 1];
        $fechaEntregaFormatoT = $convertfEAc->format('d') . ' DE ' . $mesEntrega . ' DE ' . $convertfEAc->format('Y');

        $diasParaEntrega = $this->getFechaDiff();

        return view('reportes.reportes_aperturado', compact('fechaEntregaFormatoT', 'diasParaEntrega'));
    }

    /***
     * generar reporte de apertura en excel
     */
    protected function generarreporteapertura(Request $request)
    {

        $fecha_inicio = $request->get('fechainicio');
        $fecha_fin = $request->get('fechatermino');

        // dd($fecha_inicio.'   '.$fecha_fin);

        if ($fecha_inicio != null && $fecha_fin != null) {
            // fecha inicio
            $fechaini = explode("-", $fecha_inicio);
            $fechaini = $fechaini[2] . "-" . $fechaini[1] . "-" . $fechaini[0];

            //fecha fin
            $fechatermino = explode("-", $fecha_fin);
            $fechatermino = $fechatermino[2] . "-" . $fechatermino[1] . "-" . $fechatermino[0];

            $inner_ = DB::raw("(SELECT id_pre, no_control, id_curso, migrante, indigena, etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, migrante, indigena, etnia) as ar");

            $reporteDirectorDTA =
                tbl_curso::select(
                    DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH') AS fechaturnado"),
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
                    DB::raw("case when tbl_cursos.arc='01' then nota else observaciones end as tnota")
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
                ->WHERE('tbl_cursos.inicio', '>=', $fechaini)
                ->WHERE('tbl_cursos.termino', '<=', $fechatermino)
                ->WHERE('tbl_cursos.clave', '!=', 'NULL')
                ->groupby('tbl_cursos.id', 'ip.grado_profesional', 'ip.estatus', 'i.sexo', 'ei.memorandum_validacion')
                ->distinct()->get();

            // dd($reporteDirectorDTA);

            $cabecera = [
                'MES REPORTADO', 'UNIDAD DE CAPACITACION', 'TIPO DE PLANTEL (UNIDAD, AULA MOVIL, ACCION MOVIL O CAPACITACION EXTERNA)', 'ESPECIALIDAD', 'CURSO', 'CLAVE DEL GRUPO', 'MODALIDAD', 'DURACION TOTAL EN HORAS', 'TURNO', 'DIA INICIO', 'MES INICIO', 'DIA TERMINO', 'MES TERMINO', 'PERIODO', 'HRS. DIARIAS', 'DIAS', 'HORARIO', 'INSCRITOS', 'FEM', 'MASC',
                'EGRESADOS', 'EGRESADOS FEMENINO', 'EGRESADO MASCULINO', 'DESERCION', 'COSTO TOTAL DEL CURSO POR PERSONA', 'INGRESO TOTAL', 'CUOTA MIXTA', 'EXONERACION TOTAL MUJERES', 'EXONERACION TOTAL HOMBRES', 'EXONERACION PARCIAL MUJERES', 'EXONERACION PARCIAL HOMBRES', 'NUMERO DE CONVENIO ESPECIFICO', 'MEMO DE VALIDACION DEL CURSO', 'ESPACIO FISICO',
                'NOMBRE DEL INSTRUCTOR', 'ESCOLARIDAD DEL INSTRUCTOR', 'DOCUMENTO ADQUIRIDO', 'SEXO', 'MEMO DE VALIDACION', 'MEMO DE AUTORIZACION DE EXONERACION', 'EMPLEADOS', 'DESEMPLEADOS', 'DISCAPACITADOS', 'MIGRANTES',
                'INDIGENA', 'ETNIA', 'PROGRAMA ESTRATEGICO', 'MUNICIPIO', 'DEPENDENCIA BENEFICIADA', 'CONVENIO GENERAL', 'CONVENIO CON EL SECTOR PUBLICO O PRIVADO', 'MEMO DE VALIDACION DE PAQUETERIA',
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

            $nombreLayout = "REPORTE_DEL_FORMATO_T_CURSOS_DE_APERTURAS.xlsx";
            $titulo = "CURSOS APERTURADOS DEL FORMATO T";

            if (count($reporteDirectorDTA) > 0) {
                return Excel::download(new FormatoTReport($reporteDirectorDTA, $cabecera, $titulo), $nombreLayout);
            } else {
                return redirect()->route('indice.dta.aperturado.indice')->with('success', 'NO SE ENCONTRARON REGISTROS');
            }
        } else {
            return redirect()->route('indice.dta.aperturado.indice')->with('success', 'SELECCIONE LA FECHA DE INICIO Y TERMINO');
        }
    }

    /**
     * METODO QUE NOS GENERA EL MODULO DE MEMORANDUM
     */
    protected function memorandumpordta(Request $request)
    {
        // obtenemos la unidad en base a una sesion
        $unidadstr = DB::table('tbl_unidades')->select('ubicacion')->groupBy('ubicacion')->orderBy('ubicacion', 'asc')->get();
        // dd($unidadstr);
        $busquedaPorMes = $request->get('busquedaMes');
        $meses = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');
        /**
         * CONSULTA PARA MOSTRAR INFORMACIÓN DE LOS MEMORANDUM DEL FORMATO T
         */
        if (isset($busquedaPorMes)) {
            # si la variable está inicializada se carga la consulta
            // DB::connection()->enableQueryLog();
            $queryGetMemo = tbl_curso::searchbydata($request->get('busquedaPorUnidad'))
                ->select(
                    DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'MEMORANDUM' AS ruta"),
                    DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'NUMERO' AS numero_memo"),
                    DB::raw("CASE  WHEN tbl_cursos.memos->'TURNADO_DTA'->>'NUMERO' is not NULL THEN 'MEMORANDUM TURNADO DTA' END AS tipo_memo")
                )
                ->join('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
                ->where(DB::raw("EXTRACT(MONTH FROM TO_DATE(tbl_cursos.memos->'TURNADO_DTA'->>'FECHA','YYYY-MM-DD'))"), '=', $busquedaPorMes)
                ->groupby(
                    DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'MEMORANDUM'"),
                    DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'NUMERO'")
                )
                ->paginate(5);
            // dd(DB::getQueryLog());
        } else {
            # si la variable no está inicializada no se carga la consulta
            $queryGetMemo = (array) null;
        }
        return view('reportes.memorandum_dta_formatot', compact('meses', 'queryGetMemo', 'unidadstr'));
    }

    protected function cursosReportadosDta(Request $request)
    {

        $unidades_busqueda = $request->get('unidadseleccionado');

        if (empty($request->get('unidadseleccionado'))) {
            # si está vacio se agrega parte de la condicion
            $condition =  [
                'JIQUIPILAS', 'SAN CRISTOBAL', 'TAPACHULA', 'TONALA', 'YAJALON', 'REFORMA',
                'OCOSINGO', 'TUXTLA', 'CATAZAJA', 'COMITAN', 'VILLAFLORES'
            ];
        } else {
            # de no ser así se envía con la variable que tiene el request
            $condition = [$request->get('unidadseleccionado')];
        }


        if (empty($request->get('anio'))) {
            # si está vacio se toma el año actual
            $ac = Carbon::now()->year;
        } else {
            # code...
            $ac = $request->get('anio');
        }

        if (empty($request->get('messeleccionado'))) {
            # si está vacio se toma el mes actual
            $fecha = Carbon::parse(Carbon::now());
            $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
            $setMes = $meses[($fecha->format('n')) - 1];
        } else {
            $setMes = $request->get('messeleccionado');
        }
        // obtener el año actual --

        $meses = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');

        /**
         * OBTENEMOS LAS UNIDADES
         */
        $unidades_indice = DB::table('tbl_unidades')->select('ubicacion')->groupby('ubicacion')->get();

        $inner_ = DB::raw("(SELECT id_pre, no_control, id_curso, migrante, indigena, etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, migrante, indigena, etnia) as ar");

        $cursos_reportados = tbl_curso::select(
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
            ->WHERE(DB::raw("extract(year from tbl_cursos.fecha_turnado)"), '=', $ac)
            ->WHEREIN('u.ubicacion', $condition);
        (!empty($request->get('messeleccionado'))) ? $cursosReporados = $cursos_reportados->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), '=', $setMes) : " ";

        $cursosReporados = $cursos_reportados->groupby('tbl_cursos.id', 'ip.grado_profesional', 'ip.estatus', 'i.sexo', 'ei.memorandum_validacion')
            ->distinct()->get();


        return view('reportes.cursos_reportados_formatot_dta', compact('cursosReporados', 'meses', 'unidades_indice'));
    }

    protected function cursosReportatosDireccionDta(Request $request)
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


        return view('reportes.cursos_reportados_direccion_dta', compact('cursosReporados', 'meses', 'unidades_indice'));
    }

    public function resumen_unidad_pdf(Request $request)
    {
        $leyenda = Instituto::first();
        $leyenda = $leyenda->distintivo;
        $numero_memo = $request->memo_reporte_unidad; // proceso
        // Fecha
        $fecha_ahora = strtotime(Carbon::now());
        $D = date('d', $fecha_ahora);
        $M = $this->monthToString(date('m',$fecha_ahora));
        $Y = date("Y",$fecha_ahora);
        $MT = $this->monthToString($request->mes_reporte);
        // Fin Fecha
        // Info cursos
        $count_cursos = array();
        $cursos = DB::Table('tbl_cursos')
            ->Join('calendario_formatot', 'calendario_formatot.fecha', 'tbl_cursos.fecha_turnado')
            ->Join('tbl_unidades', 'tbl_unidades.unidad', 'tbl_cursos.unidad')
            ->whereIn('tbl_cursos.turnado', ['PLANEACION','PLANEACION_TERMINADO','REPORTADO'])
            ->whereIn('tbl_cursos.status', ['TURNADO_PLANEACION','REPORTADO'])
            ->Where('fecha_entrega', 'LIKE', '%'.$request->mes_reporte)
            ->Where('tbl_unidades.ubicacion', $request->unidad_reporte)
            ->OrderBy('fecha_envio', 'DESC')
            ->Get();

        $info_cursos = [
            'fecha_envio' => date('d', strtotime($cursos[0]->fecha_envio)) . ' de ' . $this->monthToString(date('m', strtotime($cursos[0]->fecha_envio))),
            'total_cursos' => count($cursos)
        ];

        foreach($cursos as $data) {
            if(array_key_exists($data->unidad,$count_cursos)) {
                $count_cursos[$data->unidad]++;
            } else {
                $count_cursos[$data->unidad] = 1;
            }
        }
        // Fin info cursos
        // Info meses anteriores va con la fecha final del curso
        $mes_reporte_anterior = (int)$request->mes_reporte;
        $moist = 1;
        $historial_meses = $historial_fin = null;
        while($moist <= $mes_reporte_anterior) {
            $cursos_validar = null;
            $mes_fin = Carbon::createFromDate($Y, $moist,1)->endOfMonth();
            $mes_inicio = Carbon::createFromDate($Y, $moist,1)->startOfMonth();
            $mes_fin = $mes_fin->toDateString();
            $mes_inicio = $mes_inicio->toDateString();

            $cursos_validar = DB::Table('tbl_cursos')->Join('tbl_unidades','tbl_unidades.unidad','tbl_cursos.unidad')
                ->Where('tbl_unidades.ubicacion',$request->unidad_reporte)
                ->WhereBetween('tbl_cursos.termino',[$mes_inicio,$mes_fin])
                ->WhereIn('tbl_cursos.status',['NO REPORTADO','RETORNO_UNIDAD'])
                ->Where('tbl_cursos.status_curso','AUTORIZADO')
                ->First();

            if(!is_null($cursos_validar)){
                if(isset($historial_fin)){
                    $rango = $historial_meses . $historial_fin;
                }
                break;
                // $historial_meses[$moist] = ['mes' => $this->monthToString($moist),
                //                             'pendiente' => true];
            } else {
                if(is_null($historial_meses)){
                    $historial_meses = $this->monthToString($moist);

                } else {
                    $historial_fin = '-' . $this->monthToString($moist);
                    $rango = $historial_meses . $historial_fin;

                }
                // $historial_meses[$moist] = ['mes' => $this->monthToString($moist),
                //                             'pendiente' => false];
            }


            $moist++;
        }

        if(isset($rango)) {

            $historial_meses = $rango;
        } else {
            $historial_meses = null;
        }
        // Fin info meses anteriores

        $unidad = DB::Table('tbl_unidades')->Where('unidad', $request->unidad_reporte)->FIRST();
        $elabora = ['nombre' => $elabora = Auth::user()->name, 'puesto' => $elabora = Auth::user()->puesto];
        $direccion = DB::table('tbl_instituto')->Select('direccion','telefono','correo')->First();
        $direccion->direccion = explode("*", $direccion->direccion);
        $pdf = PDF::loadView('reportes.resumen_unidad_formatot', compact('leyenda','numero_memo','D','M','Y','MT','unidad','info_cursos','count_cursos','historial_meses','elabora','direccion'));
        return $pdf->Stream('Memo_unidad_para_DTA.pdf');
    }

    public function subir_resumen_unidad_pdf(Request $request)
    {
        // dd($request);
        $archivo = $request->file('subir_memo_reporte_unidad'); # obtenemos el archivo
        $url = $this->pdf_upload($archivo, $request->unidad_reporte, 'Resumen_formatoT'); # invocamos el método

        $cursos = DB::Table('tbl_cursos')->Select('tbl_cursos.id')
            ->Join('calendario_formatot', 'calendario_formatot.fecha', 'tbl_cursos.fecha_turnado')
            ->Join('tbl_unidades', 'tbl_unidades.unidad', 'tbl_cursos.unidad')
            ->whereIn('tbl_cursos.turnado', ['PLANEACION','PLANEACION_TERMINADO','REPORTADO'])
            ->whereIn('tbl_cursos.status', ['TURNADO_PLANEACION','REPORTADO'])
            ->Where('fecha_entrega', 'LIKE', '%-'.$request->mes_reporte)
            ->Where('tbl_unidades.ubicacion', $request->unidad_reporte)
            ->OrderBy('fecha_envio', 'DESC')
            ->Get();

        foreach ($cursos as $data) {
            // dd($data);
            $update = DB::Table('tbl_cursos')->WHERE('id',$data->id)
                ->Update([
                    'resumen_formatot_unidad' => $url
                ]);
        }

        return redirect()->route('validacion.cursos.enviados.dta')
                            ->with('success', sprintf('ARCHIVO CARGADO CORRECTAMENTE!'));
    }

    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/DTA/FormatoT/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/DTA/FormatoT/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function monthToString($month)
    {
        switch ($month)
        {
            case 1:
                return 'ENERO';
            break;

            case 2:
                return 'FEBRERO';
            break;

            case 3:
                return 'MARZO';
            break;

            case 4:
                return 'ABRIL';
            break;

            case 5:
                return 'MAYO';
            break;

            case 6:
                return 'JUNIO';
            break;

            case 7:
                return 'JULIO';
            break;

            case 8:
                return 'AGOSTO';
            break;

            case 9:
                return 'SEPTIEMBRE';
            break;

            case 10:
                return 'OCTUBRE';
            break;

            case 11:
                return 'NOVIEMBRE';
            break;

            case 12:
                return 'DICIEMBRE';
            break;
        }
    }
}
