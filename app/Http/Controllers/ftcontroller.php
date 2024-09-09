<?php
/**
 * DESARROLLADO POR MIS LSC DANIEL MÉNDEZ CRUZ
 */
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport
use App\Models\Instituto;
use App\Models\tbl_curso;
use Hamcrest\Core\HasToString;

class ftcontroller extends Controller {

    public function index(Request $request) {
        // obtener el año actual --
        $anio_actual = Carbon::now()->year;
        $anio=$request->get("anio");
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')
                ->select('roles.slug')
                ->leftjoin('roles', 'roles.id', '=', 'role_user.role_id')
                ->where([['role_user.user_id', '=', $id_user], ['roles.slug', 'like', '%unidad%']])
                ->orwhere([['role_user.user_id', '=', $id_user], ['roles.slug', 'like', '%admin%']])
                ->get();
        $_SESSION['unidades'] = NULL;
        $meses = array(1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'Julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'diciembre');
        $enFirma = DB::table('tbl_cursos')->where('status', '=', 'EN_FIRMA')->get();
        $retornoUnidad = DB::table('tbl_cursos')->where('status', 'RETORNO_UNIDAD')->get();
        if (!empty($rol[0]->slug)) {
            # si no está vacio
            if(count($rol) > 0) {

                $unidad = Auth::user()->unidad;

                $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                $_SESSION['unidad'] = $unidad;
            }
            $var_cursos = dataFormatoT($_SESSION['unidad'],null,null,null, ['NO REPORTADO', 'EN_FIRMA', 'RETORNO_UNIDAD'], null);
            foreach ($var_cursos as $value) {

                //--- RUBRO FEDERAL ---
                $inscritosEdadFederal = $value->iem1f + $value->ieh1f +
                                        $value->iem2f + $value->ieh2f +
                                        $value->iem3f + $value->ieh3f +
                                        $value->iem4f + $value->ieh4f +
                                        $value->iem5f + $value->ieh5f +
                                        $value->iem6f + $value->ieh6f +
                                        $value->iem7f + $value->ieh7f +
                                        $value->iem8f + $value->ieh8f;

                // //-- RUBRO ESTATAL ---
                // $inscritosEdad = $value->iem1 + $value->ieh1 + $value->iel1 +
                //                 $value->iem2 + $value->ieh2 + $value->iel2 +
                //                 $value->iem3 + $value->ieh3 + $value->iel3 +
                //                 $value->iem4 + $value->ieh4 + $value->iel4; //+
                                // $value->iem5 + $value->ieh5 + //$value->iel5 +
                                // $value->iem6 + $value->ieh6; //+ $value->iel6;

                // $inscritosEsc = $value->iesm1 + $value->iesh1 + //$value->iesl1 +
                //                 $value->iesm2 + $value->iesh2 + //$value->iesl2 +
                //                 $value->iesm3 + $value->iesh3 + //$value->iesl3 +
                //                 $value->iesm4 + $value->iesh4 + //$value->iesl4 +
                //                 $value->iesm5 + $value->iesh5 + //$value->iesl5 +
                //                 $value->iesm6 + $value->iesh6 + //$value->iesl6 +
                //                 $value->iesm7 + $value->iesh7 + //$value->iesl7 +
                //                 $value->iesm8 + $value->iesh8 + //$value->iesl8 +
                //                 $value->iesm9 + $value->iesh9; //+ $value->iesl9;

                // $acreditadosEsc = $value->aesm1 + $value->aesh1 + //$value->aesl1 +
                //                 $value->aesm2 + $value->aesh2 + //$value->aesl2 +
                //                 $value->aesm3 + $value->aesh3 + //$value->aesl3 +
                //                 $value->aesm4 + $value->aesh4 + //$value->aesl4 +
                //                 $value->aesm5 + $value->aesh5 + //$value->aesl5 +
                //                 $value->aesm6 + $value->aesh6 + //$value->aesl6 +
                //                 $value->aesm7 + $value->aesh7 + //$value->aesl7 +
                //                 $value->aesm8 + $value->aesh8 + //$value->aesl8 +
                //                 $value->aesm9 + $value->aesh9; //+ $value->aesl9;

                // $desertoresEsc = $value->naesm1 + $value->naesh1 + //$value->naesl1  +
                //                 $value->naesm2 + $value->naesh2 + //$value->naesl2 +
                //                 $value->naesm3 + $value->naesh3 + //$value->naesl3 +
                //                 $value->naesm4 + $value->naesh4 + //$value->naesl4 +
                //                 $value->naesm5 + $value->naesh5 + //$value->naesl5 +
                //                 $value->naesm6 + $value->naesh6 + //$value->naesl6 +
                //                 $value->naesm7 + $value->naesh7 + //$value->naesl7 +
                //                 $value->naesm8 + $value->naesh8 + //$value->naesl8 +
                //                 $value->naesm9 + $value->naesh9; //+ $value->naesl9;

                $sumaHM = $value->ihombre + $value->imujer; //+ $value->ilgbt;
                $sumaED = $value->egresado + $value->desertado;
                $sumaEmDe = $value->empleado + $value->desempleado;
                $sumaEgresados = $value->emujer + $value->ehombre; //+ $value->elgbt;

                $value->inscritosEdadFederal = $inscritosEdadFederal;
                // $value->inscritosEdad = $inscritosEdad;
                // $value->inscritosEsc = $inscritosEsc;
                // $value->acreditadosEsc = $acreditadosEsc;
                // $value->desertoresEsc = $desertoresEsc;
                $value->sumaHM = $sumaHM;
                $value->sumaED = $sumaED;
                $value->sumaEmDe = $sumaEmDe;
                $value->sumaEgresados = $sumaEgresados;
            }
        } else {
            # si se encuentra vacio
            $var_cursos = null;
        }

        $meses_ = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mesActual = $meses_[($fecha->format('n')) - 1];
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega', 'mes_informar')->where('mes_informar', $mesActual)->first();
        $dateNow = $fechaEntregaActual->fecha_entrega."-".$anioActual;
        $mesInformar = $fechaEntregaActual->mes_informar;
        $mesComparador = Carbon::now()->month;

        $convertfEAc = date_create_from_format('d-m-Y', $dateNow);
        $mesEntrega = $meses_[($convertfEAc->format('n')) - 1];
        $fechaEntregaFormatoT = $convertfEAc->format('d') . ' DE ' . $mesEntrega . ' DE ' . $convertfEAc->format('Y');
        $diasParaEntrega = $this->chkDateToDeliver();

        return view('reportes.vista_formatot',compact('var_cursos', 'meses', 'enFirma', 'retornoUnidad', 'fechaEntregaFormatoT', 'mesInformar', 'diasParaEntrega', 'unidad', 'mesComparador'));
    }

    /**
     * enviar a validación enlaces DTA - envía los cursos para su validación de las unidades a los enlaces DTA
     */
    public function paso2(Request $request) {
        $numero_memo = $request->get('numero_memo'); // número de memo
        $cursoschk = $request->get('check_cursos_dta');

        if (!empty($numero_memo)) {
            # si el número de memo no está vacio hay que iniciar todo el proceso
            if (!empty($cursoschk)) {
                /**
                 * vamos al cargar el archivo que se sube
                */
                if ($request->hasFile('cargar_archivo_formato_t')) {
                    // obtenemos el valor del archivo memo

                    $validator = Validator::make($request->all(), [
                        'cargar_archivo_formato_t' => 'mimes:pdf|max:10240'
                    ]);

                    if ($validator->fails()) {
                         # mandar mensaje de error si falla el cargado del archivo
                         return back()->withInput()->withErrors([$validator]);
                    } else {
                        $memo = str_replace('/', '_', $numero_memo);
                        /**
                         * aquí vamos a verificar que el archivo no se encuentre guardado
                         * previamente en el sistema de archivos del sistema de ser así se
                         * remplazará el archivo porel que se subirá a continuación
                         */
                        // construcción del archivo
                        $archivo_memo = 'uploadFiles/memoValidacion/'.$memo.'/'.$memo.'.pdf';
                        if (Storage::exists($archivo_memo)) {
                            #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                            Storage::delete($archivo_memo);
                        }

                        $archivo_memo_to_dta = $request->file('cargar_archivo_formato_t'); # obtenemos el archivo
                        $url_archivo_memo = $this->uploaded_memo_validacion_file($archivo_memo_to_dta, $memo, 'memoValidacion'); #invocamos el método
                    }
                } else {
                    $url_archivo_memo = null;
                }
                # vamos a checar sólo a los checkbox checados como propiedad
                if (!empty($cursoschk)) {
                    // se agregar un arreglo con los meses del año calendario
                    $mesesCalendarizado = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
                    $fecha_ahora = Carbon::now();
                    $fechaActual = Carbon::parse($fecha_ahora);
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $numero_memo = $request->get('numero_memo'); // número de memo
                    $fecha_nueva=$fecha_ahora->format('Y-m-d');

                    $anioActual = $fecha_ahora->year; // año actual

                    $currentMonth = $mesesCalendarizado[($fechaActual->format('n')) - 1];
                    $fechaEntregaAct = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $currentMonth)->first();
                    $fEAct = $fechaEntregaAct->fecha_entrega."-".$anioActual;
                    /**
                     * convertirlo en un formato fecha
                     */
                    $convertfEAct = date_create_from_format('d-m-Y', $fEAct);
                    $confEAct = date_format($convertfEAct, 'd-m-Y');
                    /**
                     * fecha actual
                     */
                    $fecha_actual = strtotime($fecha_nueva);
                    $fechaEntregaSpring = strtotime($confEAct);
                    /**
                     * se compara la fecha actual en la que se envía el paquete con la fecha establecida
                     * en la entrega del calendario del formato t
                     */
                    // se generar el arreglo que enviara al paquete DTA
                    $memos_DTA = [
                        'NUMERO' => $numero_memo,
                        'FECHA' => $date,
                        'MEMORANDUM' => $url_archivo_memo
                    ];

                    if ($fechaEntregaSpring >= $fecha_actual) {
                        // dd($fecha_nueva);
                        $actualSpring = \DB::table('calendario_formatot')->select('fecha_entrega')->where('fecha','>=', $fecha_nueva)->orderby('id','asc')->first();
                        // $actualSpring = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $actualMonth)->first();
                        $fechActualSpring = $actualSpring->fecha_entrega."-".$anioActual;
                        $fechActSpring = date_create_from_format('d-m-Y', $fechActualSpring);
                        $formatFechaActual = date_format($fechActSpring, 'Y-m-d');
                        // dd($formatFechaActual);
                        # la fecha de entrega debe siempre ser mayor o igual sobre la fecha actual que se envía el paquete.

                        /**
                         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                         */
                        # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                        $data = explode(",", $cursoschk);
                        /**
                         * forzamos un nuevo registro con datos a un arreglo
                         */
                        $pila = [];
                        foreach ($data as $key ) {
                            array_push($pila, $key);
                        }
                        //$comentario_unidad = explode(",", $_POST['comentarios_unidad_to_dta']); // obtenemos los comentarios
                        // dd($_POST['comentarios_unidad_to_dta']);
                        // DB::enableQueryLog(); // Enable query log
                        foreach(array_combine($pila, $_POST['comentarios_unidad_to_dta']) as $key => $comentariosUnidad){
                            $comentarios_envio_dta = [
                                'COMENTARIOS_UNIDAD' =>  $comentariosUnidad
                            ];
                            $array_memosDTA = [
                                'TURNADO_DTA' => $memos_DTA
                            ];
                            \DB::table('tbl_cursos')
                                ->where('id', $key)
                                ->update([
                                    'observaciones_formato_t' => DB::raw("'".json_encode($comentarios_envio_dta)."'::jsonb"),
                                    'memos' => \DB::raw("'".json_encode($array_memosDTA)."'::jsonb"),
                                    'status' => 'TURNADO_DTA',
                                    'turnado' => 'DTA',
                                    'fecha_turnado' => $formatFechaActual,
                                    'fecha_envio' => $date
                                ]);
                        }
                        // dd(DB::getQueryLog());

                    } else {
                        # si la condición no se cumple se tiene que tomar el envío con fecha del siguiente spring
                        #obtenemos el mes después
                        // dd('a');
                        $nextMonth = $mesesCalendarizado[($fechaActual->format('n')) + 0];
                        $fechaNextSpring = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $nextMonth)->first();
                        $fechNextSpring = $fechaNextSpring->fecha_entrega."-".$anioActual;
                        $nextSpring = date_create_from_format('d-m-Y', $fechNextSpring);
                        $formatFechaSiguiente = date_format($nextSpring, 'Y-m-d');

                        /**
                         * TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
                         */
                        # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                        $data = explode(",", $cursoschk);
                        /**
                         * forzamos un nuevo registro con datos a un arreglo
                         */
                        $pila = [];
                        foreach ($data as $key ) {
                            array_push($pila, $key);
                        }
                        // $comentario_unidad = explode(",", $_POST['comentarios_unidad_to_dta']); // obtenemos los comentarios
                        // DB::enableQueryLog(); // Enable query log
                        foreach(array_combine($pila, $_POST['comentarios_unidad_to_dta']) as $key => $comentariosUnidad){
                            $comentarios_envio_dta = [
                                'COMENTARIOS_UNIDAD' =>  $comentariosUnidad
                            ];
                            $array_memosDTA = [
                                'TURNADO_DTA' => $memos_DTA
                            ];
                            \DB::table('tbl_cursos')
                                ->where('id', $key)
                                ->update([
                                    'observaciones_formato_t' => DB::raw("'".json_encode($comentarios_envio_dta)."'::jsonb"),
                                    'memos' => \DB::raw("'".json_encode($array_memosDTA)."'::jsonb"),
                                    'status' => 'TURNADO_DTA',
                                    'turnado' => 'DTA',
                                    'fecha_turnado' => $formatFechaSiguiente,
                                    'fecha_envio' => $date
                                ]);
                        }
                        // dd(DB::getQueryLog());
                    }

                    /**
                     * GENERAMOS UNA REDIRECCIÓN HACIA EL INDEX
                     */
                    return redirect()->route('vista_formatot')
                           ->with('success', sprintf('CURSOS TURNADOS PARA VALIDACIÓN A LA DIRECCIÓN TÉCNICA ACÁDEMICA!'));

                }
            } else {
                # enviamos un mensaje que no se puede porque no hay registros
                return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE HAN SELECCIONADO CURSOS!']);
            }
        } else {
            # si el número de memo está vacio por ende se regresa y se le comenta al usuario que se necesita adjuntar ese dato
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, DEBIDO A QUE NO SE ASIGNO EL NÚMERO DE MEMORANDUM!']);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (isset($_POST['generarMemoAFirma']))
        {

            # vamos a checar sólo a los checkbox checados como propiedad
            if (!empty($_POST['chkcursos_list'])) {

                try {
                    /**
                     * unidades
                     */

                    //aqui generamos las consultas pertinentes
                    $fecha_ahora = Carbon::now();
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $numero_memo = $request->get('numero_memo'); // número de memo
                    $fecha_nueva=$fecha_ahora->format('d/m/Y');

                    // buscamos si hay cursos con ese numero de memo y se reinician
                    $cursosChecks = \DB::select("SELECT id FROM tbl_cursos as c where c.status = 'EN_FIRMA' and c.memos->'TURNADO_EN_FIRMA'->>'NUMERO' = '$numero_memo'");
                    if($cursosChecks != null) {
                        foreach ($cursosChecks as $value) {
                            \DB::table('tbl_cursos')
                                ->where('id', '=', $value->id)
                                ->update([
                                    'status' => 'NO REPORTADO',
                                    'memos' => null,
                                    'observaciones_formato_t' => null
                            ]);
                        }
                    }

                    $memos = [
                        'TURNADO_EN_FIRMA' => [
                            'NUMERO' => $numero_memo,
                            'FECHA' => $date
                        ]
                    ];
                    # sólo obtenemos a los que han sido chequeados para poder continuar con la actualización
                    foreach($_POST['chkcursos_list'] as $key => $value){
                        $comentarios_envio_firma = [
                            'OBSERVACION_FIRMA' =>  $_POST['comentarios_unidad'][$key]
                        ];
                        \DB::table('tbl_cursos')
                            ->where('id', $value)
                            ->update([
                                'memos' => $memos,
                                'status' => 'EN_FIRMA',
                                'turnado' => 'UNIDAD',
                                'observaciones_formato_t' => DB::raw("'".json_encode($comentarios_envio_firma)."'::jsonb")
                            ]);
                    }
                    $total=count($_POST['chkcursos_list']);
                    $id_user = Auth::user()->id;
                    $rol = DB::table('role_user')->select('roles.slug')->leftjoin('roles', 'roles.id', '=', 'role_user.role_id')
                    ->where([['role_user.user_id', '=', $id_user], ['roles.slug', 'like', '%unidad%']])
                    ->orWhere([['role_user.user_id', '=', $id_user], ['roles.slug', 'like', '%admin%']])->get();
                    if(count($rol) > 0){
                        $unidad = Auth::user()->unidad;
                        $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                        $_SESSION['unidad'] = $unidad;
                    }
                    $mes=date("m");

                    $reg_cursos=DB::table('tbl_cursos')->select(db::raw("sum(case when extract(month from termino) = ".$mes." then 1 else 0 end) as tota"),'unidad','curso','mod','inicio','termino',db::raw("sum(hombre + mujer) as cupo"),'nombre','clave','ciclo',
                                'memos->TURNADO_EN_FIRMA->FECHA as fecha', DB::raw("case when arc='01' then nota else observaciones end as tnota"))
                    ->where(DB::raw("memos->'TURNADO_EN_FIRMA'->>'NUMERO'"), $numero_memo)
                    ->where('status', 'EN_FIRMA')
                    ->groupby('unidad','curso','mod','inicio','termino','nombre','clave','ciclo','memos->TURNADO_EN_FIRMA->FECHA', DB::raw("observaciones_formato_t->'OBSERVACION_PARA_FIRMA'->>'OBSERVACION_FIRMA'"), 'arc', 'nota', 'observaciones')->get();

                    $reg_unidad=DB::table('tbl_unidades')->select('unidad','ubicacion','codigo_postal')->where('unidad',$_SESSION['unidad'])->whereNotIn('direccion', ['N/A', 'null'])->first();

                    $leyenda = Instituto::first();
                    $leyenda = $leyenda->distintivo;

                    $funcionarios = $this->funcionarios($unidad);

                    $pdf = PDF::loadView('reportes.memodta',compact('reg_cursos','reg_unidad','numero_memo','total','fecha_nueva', 'leyenda','funcionarios'));
                    return $pdf->stream('Memo_unidad_para_DTA.pdf');
                    /**
                     * GENERAMOS UNA REDIRECCIÓN HACIA EL INDEX
                     */
                    return redirect()->route('vista_formatot')
                            ->with('success', sprintf('GENERACIÓN DE DOCUMENTO MEMORANDUM PARA ESPERA DE FIRMA!'));
                } catch (QueryException  $th) {
                    //throw $th;
                    return back()->withErrors([$th->getMessage()]);
                }

            } else {
                return back()->withInput()->withErrors(['ERROR AL MOMENTO DE GUARDAR LOS REGISTROS, DEBEN DE ESTAR SELECCIONADOS LOS CHECKBOX CORRESPONDIENTES']);
            }
        }

            // TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”]
            // “TURNADO_DTA”:”2021-01-28”
            /**
             *  {TURNADO_DTA:[“NUMERO”:”XXXXXX”,”FECHA”:” XXXX-XX-XX”],
             * TURNADO_PLANEACION[“NUMERO”:”XXXXXX”,FECHA:”XXXX-XX-XX”],
             * TUNADO_UNIDAD:[“NUMERO”:”XXXXXX”,FECHA:”XXXX-XX-XX”]}
             */
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendToPlaneacion(Request $request) {
        // checamos que se envía el número de memorandum y no esté vacio
        if (!empty($request->get('numeroMemo'))) {
            // checamos si hay cursos si no regresamos con un mensaje de error
            if (!empty($_POST['checkedCursos'])) {
                try {
                    //iniciamos el código verdadero
                    $fecha_ahora = Carbon::now();
                    $date = $fecha_ahora->format('Y-m-d'); // fecha
                    $fecha_nueva=$fecha_ahora->format('d-m-Y');
                    $numero_memo = $request->get('numeroMemo'); // número de memo
                    /**
                    * vamos al cargar el archivo que se sube
                    */
                    if ($request->hasFile('cargar_memorandum_to_planeacion')) {
                        # optener el valor del archivo
                        $validator = Validator::make($request->all(), [
                            'cargar_memorandum_to_planeacion' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # enviamos mensaje de error si la validacion falla
                            return back()->withErrors([$validator]);
                        } else {
                            # de lo contrario proseguimos con nuestra información
                            $memo = str_replace('/', '_', $numero_memo);
                            /**
                            * aquí vamos a verificar que el archivo no se encuentre guardado
                            * previamente en el sistema de archivos del sistema de ser así se
                            * remplazará el archivo porel que se subirá a continuación
                            */
                            // construcción del archivo
                            $archivo_memo = 'uploadFiles/memoTurnadoPlaneacion/'.$memo.'/'.$memo.'.pdf';
                            if (Storage::exists($archivo_memo)) {
                                #checamos si hay algún documento, de ser así, procedemos a eliminarlo
                                Storage::delete($archivo_memo);
                            }

                            $archivo_memo_send_to_planeacion = $request->file('cargar_memorandum_to_planeacion'); # obtenemos el archivo
                            $url_memo_turnado_planeacion = $this->uploaded_memo_validacion_file($archivo_memo_send_to_planeacion, $memo, 'memoTurnadoPlaneacion'); #invocamos el método
                        }

                    } else {
                        $url_memo_turnado_planeacion = null;
                    }

                    // empezamos a turnar a planeacion
                    $memo_turnado_planeacion = [
                        'PLANEACION' => [
                            'NUMERO' => $numero_memo,
                            'FECHA' => $date,
                            'MEMORANDUM' => $url_memo_turnado_planeacion
                        ]
                    ];

                    $data = explode(",", $_POST['checkedCursos']);
                    // GENERARMOS UN ARREGLO O PILA
                    $pilasendtoplaneacion = [];
                    foreach ($data as $key ) {
                        array_push($pilasendtoplaneacion, $key);
                    }
                    // $comentarioDireccionDTA = explode(",", $_POST['comentarios_direccionDta']);
                    foreach (array_combine($pilasendtoplaneacion, $_POST['comentarios_direccionDta']) as $key => $value) {
                        $comentarios_envio_planeacion = [
                            'OBSERVACION_ENVIO_PLANEACION' => $value
                        ];
                        # entramos en el ciclo para guardar cada registro
                        \DB::table('tbl_cursos')
                            ->where('id', $key)
                            ->update([
                                'memos' => DB::raw("jsonb_set(memos, '{TURNADO_PLANEACION}', '".json_encode($memo_turnado_planeacion)."', true)"),
                                'status' => 'TURNADO_PLANEACION',
                                'turnado' => 'PLANEACION',
                                'observaciones_formato_t' => DB::raw("jsonb_set(observaciones_formato_t, '{COMENTARIO_ENVIO_PLANEACION}', '".json_encode($comentarios_envio_planeacion)."', true)"),
                            ]);
                    }
                    // enviar  a la página de inicio del módulo si el proceso fue satisfactorio
                    return redirect()->route('validacion.dta.revision.cursos.indice')
                            ->with('success', sprintf('CURSOS TURNADOS A PLANEACIÓN SATISFACTORIAMENTE!'));

                } catch (QueryException $th) {
                    //excepción de consulta
                    return back()->withErrors([$th->getMessage()]);
                }
            } else {
                return back()->withInput()->withErrors(['ERROR AL MOMENTO DE GUARDAR LOS REGISTROS, SE DEBE DE ESTAR SELECCIONADOS LOS CURSOS CORRESPONDIENTES']);
            }
        } else {
            # mensaje de error por no tener el número de memorandum
            return back()->withInput()->withErrors(['NO PUEDE REALIZAR ESTA OPERACIÓN, SE NECESITA EL NÚMERO DE MEMORANDUM']);
        }
    }

    protected function uploaded_memo_validacion_file($file, $memo, $subpath) {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($memo.".".$extensionFile);
        $path = '/'.$subpath.'/'.$memo.'/'.$documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('custom_folder_1')->url('/uploadFiles/'.$subpath.'/'.$memo."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function chkDateToDeliver() {
        $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $fecha = Carbon::parse(Carbon::now());
        $anioActual = Carbon::now()->year;
        $mes = $meses[($fecha->format('n')) - 1];
        $fechaActual = Carbon::now()->format('d-m-Y');
        /* hacemos una consulta a la tabla para obtener el mes correspondiente */
        $fechaEntregaActual = \DB::table('calendario_formatot')->select('fecha_entrega')->where('mes_informar', $mes)->first();
        $fEAc = $fechaEntregaActual->fecha_entrega."-".$anioActual;
        $comfechaActual = strtotime($fechaActual);
        $convertfEAc = date_create_from_format('d-m-Y', $fEAc);
        $confEAc = date_format($convertfEAc, 'd-m-Y');
        $comconfEAc = strtotime($confEAc); // fecha actual de entrega
        $dias = (strtotime($confEAc) - strtotime($fechaActual))/86400;
        $dias = abs($dias); $dias = floor($dias);

        return $dias;
    }

    protected function xlxExportReporteTbyUnidad(Request $request){
        // dd('a');
        $anio_actual = Carbon::now()->year;
        $unidad_ = $request->unidadesFormatoT;

        $formatot_planeacion_unidad = dataFormatoT($unidad_,null,null,null, ['NO REPORTADO', 'EN_FIRMA', 'RETORNO_UNIDAD'], null);
        foreach ($formatot_planeacion_unidad as $value) {
            unset($value->fechaturnado);
            unset($value->id_tbl_cursos);
            unset($value->estadocurso);
            unset($value->madres_solteras);
            unset($value->observaciones_firma);
            // unset($value->totalinscripciones);
            // unset($value->masculinocheck);
            // unset($value->femeninocheck);
            unset($value->sumatoria_total_ins_edad);
            unset($value->observaciones_enlaces);
            unset($value->termino);
            unset($value->turnados_enlaces);
            unset($value->etnia);
            unset($value->arc);
        }

        // 'id curso', 'ESTADO DEL CURSO', discapacitados, ->, madres solteras
        $head = [
            'UNIDAD DE CAPACITACION','TIPO DE PLANTEL (UNIDAD, AULA MOVIL, ACCION MOVIL O CAPACITACION EXTERNA)',
            'ESPECIALIDAD','CURSO','CLAVE DEL GRUPO','MODALIDAD','DURACION TOTAL EN HORAS','TURNO','DIA INICIO',
            'MES INICIO','DIA TERMINO','MES TERMINO', 'PERIODO', 'HRS. DIARIAS', 'DIAS', 'HORARIO', 'INSCRITOS',
            'FEM', 'MASC','EGRESADOS', 'EGRESADOS FEMENINO', 'EGRESADO MASCULINO', 'DESERCION', 'COSTO TOTAL DEL CURSO POR PERSONA',
            'INGRESO TOTAL', 'CUOTA MIXTA', 'EXONERACION MUJERES', 'EXONERACION HOMBRES', 'REDUCCION CUOTA MUJERES', 'REDUCCION CUOTA HOMBRES',
            'NUMERO DE CONVENIO ESPECIFICO', 'MEMO DE VALIDACION DEL CURSO', 'ESPACIO FISICO', 'NOMBRE DEL INSTRUCTOR',
            'ESCOLARIDAD DEL INSTRUCTOR', 'STATUS', 'SEXO', 'MEMO DE VALIDACION', 'MEMO DE AUTORIZACION DE EXONERACION',
            'EMPLEADOS', 'DESEMPLEADOS', 'DISCAPACITADOS',  'MIGRANTES','ADOLESCENTES EN CONDICION DE CALLE','MUJERES JEFAS DE FAMILIA', 'INDIGENA', 'RECLUSOS', 'PROGRAMA ESTRATEGICO',
            'MUNICIPIO', 'ZE', 'REGION', 'DEPENDENCIA BENEFICIADA', 'CONVENIO GENERAL',
            'CONVENIO CON EL SECTOR PUBLICO O PRIVADO', 'MEMO DE VALIDACION DE PAQUETERIA','GRUPO VULNERABLE',
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
            'OBSERVACIONES',


            // 'OBSERVACIONES FIRMA', 'TOTAL INSCRIPCIONES', 'MASCULINO', 'FEMENINO', 'SUMATORIA TOTAL', 'COMENTARIOS ENLACES'
        ];

        $nombreLayout = "FORMATO_T_PARA_LA_UNIDAD_".$unidad_.".xlsx";
        $titulo = "FORMATO T DE LA UNIDAD ".$unidad_;

        if(count($formatot_planeacion_unidad)>0) {
            return Excel::download(new FormatoTReport($formatot_planeacion_unidad,$head, $titulo), $nombreLayout);
        }
    }

    /**
     * nuevo método para generar  el módulo
     */
    protected function memorandumporunidad(Request $request){
        // obtenemos la unidad en base a una sesion
        $unidad = Auth::user()->unidad;
        $unidadstr = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
        // dd($unidadstr);
        $busquedaPorMes = $request->get('busquedaMes');
        $meses = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');
        /**
         * CONSULTA PARA MOSTRAR INFORMACIÓN DE LOS MEMORANDUM DEL FORMATO T
         */
        if (isset($busquedaPorMes)) {
            # si la variable está inicializada se carga la consulta
            // DB::connection()->enableQueryLog();
            $queryGetMemo = DB::table('tbl_cursos')
                        ->select(
                            DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'MEMORANDUM' AS ruta"),
                            DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'NUMERO' AS numero_memo"),
                            DB::raw("CASE  WHEN tbl_cursos.memos->'TURNADO_DTA'->>'NUMERO' is not NULL THEN 'MEMORANDUM TURNADO DTA' END AS tipo_memo")
                        )
                        ->join('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
                        ->where('u.ubicacion', '=', $unidadstr)
                        ->where(DB::raw("EXTRACT(MONTH FROM TO_DATE(memos->'TURNADO_DTA'->>'FECHA','YYYY-MM-DD'))") , '=' , $busquedaPorMes)
                        ->groupby(DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'MEMORANDUM'"),
                            DB::raw("tbl_cursos.memos->'TURNADO_DTA'->>'NUMERO'")
                        )
                        ->paginate(5);
            // dd(DB::getQueryLog());

            $queryGetMemoRetorno = DB::table('tbl_cursos')
                                ->select(
                                    DB::raw("tbl_cursos.memos->'TURNADO_UNIDAD'->>'MEMORANDUM' AS ruta"),
                                    DB::raw("tbl_cursos.memos->'TURNADO_UNIDAD'->>'NUMERO' AS numero_memo"),
                                    DB::raw("CASE WHEN tbl_cursos.memos->'TURNADO_UNIDAD'->>'NUMERO' is not NULL THEN 'MEMORANDUM TURNADO UNIDAD' END AS tipo_memo")
                                )
                                ->join('tbl_unidades as u', 'u.unidad', '=', 'tbl_cursos.unidad')
                                ->where('u.ubicacion', '=', $unidadstr)
                                ->where(DB::raw("EXTRACT(MONTH FROM TO_DATE(memos->'TURNADO_UNIDAD'->>'FECHA','YYYY-MM-DD'))") , '=' , $busquedaPorMes)
                                ->groupby(
                                    DB::raw("tbl_cursos.memos->'TURNADO_UNIDAD'->>'MEMORANDUM'"),
                                    DB::raw("tbl_cursos.memos->'TURNADO_UNIDAD'->>'NUMERO'")
                                )
                                ->paginate(5);
        } else {
            # si la variable no está inicializada no se carga la consulta
            $queryGetMemo = (array) null;
            $queryGetMemoRetorno = (array) null;
        }
        //dd($queryGetMemo);
        return view('reportes.memorandum_unidad_formatot', compact('meses', 'queryGetMemo', 'unidadstr', 'queryGetMemoRetorno'));
    }

    public function funcionarios($unidad) {
        $query = clone $dacademico = clone $dacademico_unidad = clone $certificacion = clone $dunidad = DB::Table('tbl_organismos AS o')->Select('f.titulo','f.nombre','f.cargo','f.direccion','f.telefono','f.correo_institucional')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true');

        $dacademico = $dacademico->Where('o.id',16)->First();
        $certificacion = $certificacion->Where('o.id',18)->First();

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
            'dacademico_unidad' => ['titulo'=>$dacademico_unidad->titulo,'nombre'=>$dacademico_unidad->nombre,'puesto'=>$dacademico_unidad->cargo,'direccion'=>$dacademico_unidad->direccion,'telefono'=>$dacademico_unidad->telefono,'correo'=>$dacademico_unidad->correo_institucional],
            'dunidad' => ['titulo'=>$dunidad->titulo,'nombre'=>$dunidad->nombre,'puesto'=>$dunidad->cargo,'direccion'=>$dunidad->direccion,'telefono'=>$dunidad->telefono,'correo'=>$dunidad->correo_institucional],
            'elabora' => ['nombre'=>strtoupper(Auth::user()->name),'puesto'=>strtoupper(Auth::user()->puesto)]
        ];

        return $funcionarios;
    }
}
