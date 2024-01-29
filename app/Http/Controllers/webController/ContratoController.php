<?php
//Creado por Orlando Chavez
namespace App\Http\Controllers\webController;

use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\contratos;
use App\Models\InstructorPerfil;
use App\Models\supre;
use App\Models\folio;
use App\Models\pago;
use App\Models\especialidad_instructor;
use App\Models\directorio;
use App\Models\tbl_curso;
use App\Models\contrato_directorio;
use App\Models\especialidad;
use App\Models\instructor;
use App\Models\DocumentosFirmar;
use PDF;
use PHPQRCode\QRcode;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Pagination\Paginator;
use DateTime;
use App\Http\Controllers\efirma\EContratoController;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $array_ejercicio =[];
        $año_pointer = CARBON::now()->format('Y');
        /**
         * parametros para iniciar la busqueda
         */
        $tipoContrato = $request->get('tipo_contrato');
        $busqueda_contrato = $request->get('busquedaPorContrato');
        $tipoStatus = $request->get('tipo_status');
        $unidad = $request->get('unidad');
        $mes = $request->get('mes');
        // obtener el usuario y su unidad
        $usuarioUnidad = Auth::user()->unidad;
        // obtener el id
        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();
        if($request->ejercicio == NULL)
        {
            $año_referencia = '01-01-' . CARBON::now()->format('Y');
            $año_referencia2 = '31-12-' . CARBON::now()->format('Y');
        }
        else
        {
            $año_referencia = '01-01-' . $request->ejercicio;
            $año_referencia2 = '31-12-' . $request->ejercicio;
            $año_pointer = $request->ejercicio;
        }

        for($x = 2020; $x <= intval(CARBON::now()->format('Y')); $x++)
        {
            array_push($array_ejercicio, $x);
        }
        $contratos = new contratos();
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->WHERE('cct','LIKE','07EI%')->GET();

        $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus, $unidad, $mes)
        ->SELECT('tabla_supre.id','tabla_supre.no_memo',
        'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status','folios.permiso_editar',
        'folios.id_folios', 'folios.id_supre', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
        'contratos.docs','contratos.id_contrato','contratos.fecha_status','contratos.created_at',
        'contratos.observacion','tbl_cursos.termino AS fecha_termino',
        'tbl_cursos.inicio AS fecha_inicio','pagos.status_recepcion','pagos.id',
        DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif"))
            ->WHERE('folios.status', '!=', 'En_Proceso')
            ->WHERE('folios.status', '!=', 'Finalizado')
            ->WHERE('folios.status', '!=', 'Rechazado')
            ->WHERE('folios.status', '!=', 'Cancelado')
            // ->WHERE('folios.status', '!=', 'Validado')
            ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
            ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
            // ->WHERE('folios.status', '!=', 'Verificando_Pago')
            ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
            ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
            ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
            ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
            ->orderBy('contratos.created_at','desc');
            // ->orderBy('folios.status','desc')->orderBy('contratos.created_at','asc');

        $querySupre2 = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus, $unidad, $mes)
            ->SELECT('tabla_supre.id','tabla_supre.no_memo',
            'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status','folios.permiso_editar',
            'folios.id_folios', 'folios.id_supre', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
            'contratos.docs','contratos.id_contrato','contratos.fecha_status','contratos.created_at',
            'contratos.observacion','tbl_cursos.termino AS fecha_termino',
            'tbl_cursos.inicio AS fecha_inicio','pagos.status_recepcion','pagos.id',
            DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif"))
                ->WHERE('folios.status', '=', 'Verificando_Pago')
                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                ->orderBy('contratos.created_at', 'asc');
                // ->orderBy('folios.status','desc')->orderBy('contratos.created_at','asc');
            // ->orderBy(DB::raw("array_position(array['Validando_Contrato','Contratado',
            //     'Verificando_Pago','Contrato_Rechazado']::varchar[], folios.status)"));
        //dd($roles[0]->role_name);

        switch ($roles[0]->role_name) {
            case 'admin':
                # code...
                $querySupre = $querySupre->PAGINATE(25);
                $querySupre2 = $querySupre2->PAGINATE(25);
                    // dd($querySupre);
            break;
            case 'unidad.ejecutiva':
                # code...
                $querySupre = $querySupre->PAGINATE(25);
            break;
            case 'direccion.general':
                # code...
                $querySupre = $querySupre->PAGINATE(25);
            break;
            case 'planeacion':
                # code...
                $querySupre = $querySupre->PAGINATE(25);
            break;
            case 'financiero_verificador':
                # code...
                $querySupre = $querySupre->PAGINATE(25);
            break;
            case 'financiero_pago':
                # code...
                $querySupre = $querySupre->PAGINATE(25);
            break;
            default:
                # code...
                // obtener unidades
                $unidadUsuario = DB::table('tbl_unidades')->WHERE('id', $usuarioUnidad)->FIRST();
                /**
                 * contratos - contratos
                 */
                $contratos = new contratos();

                $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus, $unidad, $mes)
                    ->WHERE('tbl_unidades.ubicacion', '=', $unidadUsuario->ubicacion)
                    ->WHERE('folios.status', '!=', 'En_Proceso')
                    // ->WHERE('folios.status', '!=', 'Finalizado')
                    ->WHERE('folios.status', '!=', 'Rechazado')
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
                    ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
                    ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                    ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                    ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                    ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                    ->orderBy('contratos.created_at', 'desc')
                    ->PAGINATE(25, [
                        'tabla_supre.id','tabla_supre.no_memo',
                        'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','contratos.created_at',
                        'folios.status','folios.id_folios', 'folios.folio_validacion','folios.permiso_editar',
                        'tbl_unidades.ubicacion','contratos.docs','contratos.id_contrato',
                        'contratos.fecha_status','contratos.observacion','tbl_cursos.termino AS fecha_termino',
                        'tbl_cursos.inicio AS fecha_inicio','pagos.status_recepcion','pagos.id',
                        DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")]);
            break;
        }
        // dd($querySupre2);
        // dd($querySupre[2]);
        return view('layouts.pages.vstacontratoini', compact('querySupre','unidades','array_ejercicio', 'año_pointer'));
    }

    /**
     * Show the form for creating a new resourc.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $director = $testigo1 = $testigo2 = $testigo3 = null;
        $folio = new folio();
        $perfil = new InstructorPerfil();

        $contrato = contratos::WHERE('id_folios',$id)->FIRST();
        if(isset($contrato))
        {
            $dir = contrato_directorio::WHERE('id_contrato',$contrato->id_contrato)->FIRST();
            $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $dir->contrato_iddirector)->FIRST();
            $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $dir->contrato_idtestigo1)->FIRST();
            $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $dir->contrato_idtestigo2)->FIRST();
            $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $dir->contrato_idtestigo3)->FIRST();
        }
        // dd($contrato);
        $data = $folio::SELECT('folios.id_folios', 'folios.folio_validacion', 'folios.importe_total',
                            'folios.iva', 'tbl_cursos.unidad','tbl_cursos.clave','tbl_cursos.termino', 'tbl_cursos.instructor_mespecialidad','tbl_cursos.fecha_apertura',
                            'tbl_cursos.curso','tbl_cursos.clave_especialidad','tbl_cursos.espe','tbl_cursos.soportes_instructor','instructores.nombre AS insnom',
                            'instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.id','instructores.archivo_alta','instructores.banco',
                            'instructores.no_cuenta','instructores.interbancaria','instructores.archivo_bancario')
                        ->WHERE('id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();
        $data->soportes_instructor = json_decode($data->soportes_instructor);
        // dd($data->soportes_instructor->banco);
        $data->unidad = DB::table('tbl_unidades')->WHERE('unidad', $data->unidad)->VALUE('ubicacion');

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
                                    ->SELECT('especialidad_instructores.id','especialidades.nombre')
                                    // ->WHERE('especialidad_instructores.memorandum_validacion',$data->instructor_mespecialidad)
                                    ->WHERE('especialidades.nombre', '=', $data->espe)
                                    ->WHERE('especialidad_instructores.id_instructor',$data->id)
                                    ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                                    ->FIRST();
                                    // dd($data->instructor_mespecialidad);

        $perfil_prof = $perfil::SELECT('especialidades.nombre AS nombre_especialidad', 'especialidad_instructores.id AS id_espins')
                                ->WHERE('instructor_perfil.numero_control', '=', $data->id)
                                ->WHERE('especialidad_instructores.activo', '=', TRUE)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')->GET();

        if(isset($especialidad_seleccionada->id))
        {
            $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $data->instructor_mespecialidad]])->value('hvalidacion');
        }
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if(isset($me['memo_val']) && $me['memo_val'] == $data->instructor_mespecialidad)
                {
                    $memoval = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $memoval = $data->archivo_alta;
        }

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        if($data->fecha_apertura < '2023-10-12') {
            $pago = round($data->importe_total-$data->iva, 2);
        } else {
            $pago = $data->importe_total;
        }

        $año_referencia = '01-01-' . CARBON::now()->format('Y');
        $uni_contrato = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('unidad', '=', $data->unidad)->FIRST();

        $xpld = explode('-', $data->folio_validacion);
        $counter = strlen($xpld[3]);
        if($counter == 4)
        {
            $consecutivo = $xpld[3];
        }
        if($counter == 3)
        {
            $consecutivo = '0' . $xpld[3];
        }
        // dd($consecutivo);
        if ($consecutivo == NULL)
        {
            $consecutivo = '0001';
        }
        else
        {
            switch (strlen($consecutivo))
            {
                case 1:
                    $consecutivo = '000' . $consecutivo;
                break;
                case 2:
                    $consecutivo = '00' . $consecutivo;
                break;
                case 3:
                    $consecutivo = '0' . $consecutivo;
                break;
            }
        }
        // dd($consecutivo);
        if($uni_contrato->ubicacion == 'SAN CRISTOBAL')
        {
            $uni_contrato = 'SC'.'/DA/'.DB::TABLE('tbl_unidades')->WHERE('unidad', '=', $uni_contrato->ubicacion)->VALUE('clave_contrato') . '/' . $consecutivo . '/'. CARBON::now()->format('Y');
        }
        else
        {
            $uni_contrato = substr($uni_contrato->ubicacion, 0, 2).'/DA/'.DB::TABLE('tbl_unidades')->WHERE('unidad', '=', $uni_contrato->ubicacion)->VALUE('clave_contrato') . '/' . $consecutivo . '/'. CARBON::now()->format('Y');
        }

        $date = strtotime($data->termino);
        $dacarbon = strtotime(Carbon::now());

        if($dacarbon > $date)
        {
            $term = TRUE;
        }
        else
        {
            $term = FALSE;
        }

        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        // --- APARTADO DE SOLICITUD DE PAGO ---
        $X = new contratos();
        $folio_p = new folio();
        $dataf = $folio_p::where('id_folios', '=', $id)->first();
        // $datac = $X::where('id_folios', '=', $id)->first();
        $regimen = DB::TABLE('tbl_cursos')->SELECT('modinstructor','tipo_curso')->WHERE('id', '=', $dataf->id_cursos)->FIRST();
        $bancario = tbl_curso::SELECT('instructores.archivo_bancario','instructores.id AS idins','instructores.banco',
                                      'instructores.no_cuenta','instructores.interbancaria',
                                      'tbl_cursos.nombre','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino')
                                ->WHERE('tbl_cursos.id', '=', $dataf->id_cursos)
                                ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')->FIRST();
        // dd($uni_contrato);

        return view('layouts.pages.frmcontrato', compact('data','nombrecompleto','perfil_prof','pago','term','unidades','uni_contrato', 'especialidad_seleccionada','memoval','regimen','contrato','director','testigo1','testigo2','testigo3'));
    }

    public function contrato_save(Request $request)
    {
        // dd($request);
        $id_curso = folio::where('id_folios', '=', $request->id_folio)->value('id_cursos');
        $check_contrato = contratos::SELECT('numero_contrato')
            ->WHERE('numero_contrato', '=', $request->numero_contrato)
            ->FIRST();
        if(isset($check_contrato))
        {
            return back()->withErrors(sprintf('LO SENTIMOS, EL NUMERO DE CONTRATO INGRESADO YA SE ENCUENTRA REGISTRADO', $request->numero_contrato));
        }
        // dd($request->numero_contrato);
        $contrato = contratos::WHERE('id_folios',$request->id_folios)->FIRST();
        if(is_null($contrato))
        {
            $contrato = new contratos();
        }

        $contrato->numero_contrato = $request->numero_contrato;
        // $contrato->instructor_perfilid = $request->perfil_instructor;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->id_folios = $request->id_folio;
        $contrato->folio_fiscal = $request->folio_fiscal;
        $contrato->id_curso = $id_curso;
        $contrato->fecha_status = carbon::now();
        // $contrato->tipo_factura = $request->tipo_factura;
        $file = $request->file('factura'); # obtenemos el archivo
        if ($file != NULL)
        {
            $urldocs = $this->pdf_upload($file, $request->id_contrato,'factura_pdf');
            $contrato->arch_factura = $urldocs;
        }
        $file_xml = $request->file('factura_xml'); # obtenemos el archivo
        if ($file_xml != NULL)
        {
            $urldocs = $this->xml_upload($file_xml, $request->id_contrato,'factura_xml');
            $contrato->arch_factura_xml = $urldocs;
        }
        $contrato->save();

        $id_contrato = contratos::SELECT('id_contrato')->WHERE('numero_contrato', '=', $request->numero_contrato)->FIRST();

        $idInstructor = DB::Table('tbl_cursos')->Select('id_instructor')->Where('id',$id_curso)->First();
        $instructoresPermitidos = [574, 1605, 1157, 1594, 1562, 1335];

        // // Metodo de XML para contrato
        $contratoController = new EContratoController();
        $result = $contratoController->xml($id_contrato->id_contrato);

        // Eliminar el guardado de directorio y reemplazar por la tabla de funcionarios
        $directorio = new contrato_directorio();
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->id_contrato = $id_contrato->id_contrato;
        $directorio->save();

        // fin de reemplazo

        $idc = $id_contrato->id_contrato;

        folio::where('id_folios', '=', $request->id_folio)
            ->update(['status' => 'Capturando']);

        //Notificacion
        $letter = [
            'titulo' => 'Solicitud de Contrato',
            'cuerpo' => 'La solicitud de contrato ' . $contrato->numero_contrato . ' ha sido agregada para su validación',
            'memo' => $contrato->numero_contrato,
            'unidad' => $contrato->unidad_capacitacion,
            'url' => '/contrato/validar/' . $contrato->id,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        // GUARDADO DE SOLICITUD DE PAGO
        if($this->setsolpa($request) == true) {
            $check_pago = pago::SELECT('no_memo')->WHERE('no_memo', '=', $request->no_memo)->FIRST();
            $urldocs = $urldocs2 = null;
            $created = DB::TABLE('contratos')->WHERE('id_folios','=', $request->id_folio)->VALUE('created_at');

            if($created <= '2023-06-05')
            {
                $status_recepcion = 'recepcion tradicional';
            }
            else
            {
                $status_recepcion = null;
            }

            $id_instructor  = DB::TABLE('contratos')
                ->JOIN('folios','folios.id_folios','contratos.id_folios')
                ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->WHERE('contratos.id_contrato', $idc)
                ->VALUE('tbl_cursos.id_instructor');

            if(isset($check_pago))
            {
                return back()->withErrors(sprintf('LO SENTIMOS, EL MEMORANDUM DE PAGO INGRESADO YA SE ENCUENTRA REGISTRADO', $request->no_memo));
            }

            // $pago = new pago();
            // $pago->no_memo = $request->no_memo;
            // $pago->id_contrato = $request->id_contrato;
            // $pago->liquido = $request->liquido;
            // $pago->solicitud_fecha = $request->solicitud_fecha;

                if ($request->arch_asistencia != NULL)
                {
                    $file = $request->file('arch_asistencia'); # obtenemos el archivo
                    $urldocs = $this->pdf_upload($file, $idc, $id_instructor, 'lista_asistencia'); #invocamos el método
                    // guardamos en la base de datos
                    // $pago->arch_asistencia = trim($urldocs);
                }

                if ($request->arch_evidencia != NULL)
                {
                    $file = $request->file('arch_evidencia'); # obtenemos el archivo
                    $urldocs2 = $this->pdf_upload($file, $idc, $id_instructor, 'lista_evidencia'); #invocamos el método
                    // guardamos en la base de datos
                    // $pago->arch_evidencia = trim($urldocs);
                }
                else
                {
                    $urldocs2 = NULL;
                }
            // $pago->fecha_status = carbon::now();
            // $pago->save();


            pago::updateOrInsert(
                ['id_contrato' => $idc],
                [
                    'no_memo' => $request->no_memo,
                    'liquido' => $request->liquido,
                    'solicitud_fecha' => $request->solicitud_fecha,
                    // 'fecha_agenda' => $request->fecha_agenda,
                    'arch_asistencia' => trim($urldocs),
                    'arch_evidencia' => trim($urldocs2),
                    'fecha_status' => carbon::now(),
                    'created_at' => carbon::now(),
                    'updated_at' => carbon::now(),
                    'status_recepcion' => $status_recepcion,
                    'id_curso' => $id_curso
                ]
            );

            contrato_directorio::where('id_contrato', '=', $idc)
            ->update(['solpa_iddirector' => $request->id_remitente,
                    'solpa_elaboro' => $request->id_elabora,
                    'solpa_para' => $request->id_destino,
                    'solpa_ccp1' => $request->id_ccp1,
                    'solpa_ccp2' => $request->id_ccp2,
                    'solpa_ccp3' => $request->id_ccp3]);

            if(isset($request->arch_factura))
            {
                $file = $request->file('arch_factura'); # obtenemos el archivo
                $urldocs = $this->pdf_upload($file, $idc, $id_instructor, 'factura_pdf'); #invocamos el método
                $contrato = contratos::find($idc);
                $contrato->arch_factura = trim($urldocs);
                $contrato->save();
            }
            if(isset($request->arch_factura_xml))
            {
                $file_xml = $request->file('arch_factura_xml'); # obtenemos el archivo
                $urldocs = $this->xml_upload($file_xml, $idc, $id_instructor, 'factura_xml'); #invocamos el método
                $contrato = contratos::find($idc);
                $contrato->arch_factura_xml = trim($urldocs);
                $contrato->save();
            }


            if ($request->file('arch_bancario') != null)
            {
                $banco = $request->file('arch_bancario'); # obtenemos el archivo
                $urlbanco = $this->pdf_upload_bancario($banco, $request->id_instructor, 'banco'); # invocamos el método
                $instructor = instructor::find($request->id_instructor);
                $instructor->archivo_bancario = trim($urlbanco);
                $instructor->banco = $request->nombre_banco;
                $instructor->no_cuenta = $request->numero_cuenta;
                $instructor->interbancaria = $request->clabe;
                $instructor->save();
            }

            folio::where('id_folios', '=', $request->id_folio)
            ->update(['status' => 'Pago_Verificado']);
        }

        //Notificacion!!
        $letter = [
            'titulo' => 'Solicitud de Pago',
            'cuerpo' => 'La solicitud de pago ' . $request->no_memo . ' ha sido agregada para su validación',
            'memo' => $request->no_memo,
            'unidad' => Auth::user()->unidad,
            'url' => '/pago/verificar_pago/' . $idc,
        ];

        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));
        // if($result == TRUE) {
            return redirect()->route('contrato-inicio')
                            ->with('success','Contrato y/o Solicitud de Pago Agregado');
        // } else {
            return redirect()->route('contrato-inicio')
                            ->with('warning','Contrato y/o Solicitud de Pago Agregado. Ocurrio un error al obtener la cadena original, por favor intente de nuevo');
        // }

        // return view('layouts.pages.contratocheck', compact('idc'));
    }

    public function modificar($id)
    {
        $folio = new folio();
        $especialidad = new especialidad();
        $perfil = new InstructorPerfil();

        $datacon = contratos::WHERE('id_contrato', '=', $id)->FIRST();
        $data = $folio::SELECT('folios.id_folios','folios.importe_total','folios.iva','tbl_cursos.clave','tbl_cursos.espe','tbl_cursos.id_instructor','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.archivo_alta','instructores.id','tbl_cursos.instructor_mespecialidad', 'tbl_cursos.curso','tbl_cursos.fecha_apertura')
                        ->WHERE('id_folios', '=', $datacon->id_folios)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();

        $perfil_sel = contratos::SELECT('especialidades.nombre AS nombre_especialidad', 'contratos.instructor_perfilid AS id_espins')
                                ->WHERE('id_contrato', '=', $id)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.id','=','contratos.instructor_perfilid')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                                ->FIRST();


        $insPer = especialidad_instructor::SELECT('perfilprof_id')->WHERE('id', '=', $datacon->instructor_perfilid)->FIRST();

        $perfil_prof = $perfil::SELECT('especialidades.nombre AS nombre_especialidad', 'especialidad_instructores.id AS id_espins')
                                ->WHERE('instructor_perfil.numero_control', '=', $data->id)
                                ->WHERE('especialidad_instructores.id', '!=', $perfil_sel->id_espins)
                                ->WHERE('especialidad_instructores.activo', '=', TRUE)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')->GET();
                                // dd($perfil_prof);

        $memoval = especialidad_instructor::WHERE('id_instructor',$data->id_instructor)
        ->whereJsonContains('hvalidacion', [['memo_val' => $data->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if($me['memo_val'] == $data->instructor_mespecialidad)
                {
                    $memoval = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $memoval = $data->archivo_alta;
        }

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $unidadsel = tbl_unidades::SELECT('unidad')->WHERE('unidad', '=', $datacon->unidad_capacitacion)->FIRST();
        $unidadlist = tbl_unidades::SELECT('unidad')->WHERE('unidad', '!=', $datacon->unidad_capacitacion)->GET();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        // Para pagos
        $X = new contratos();
        $foliop = new folio();
        $dataf = $foliop::where('id_folios', '=', $data->id_folios)->first();
        $regimen = DB::TABLE('tbl_cursos')->SELECT('modinstructor','tipo_curso')->WHERE('id', '=', $dataf->id_cursos)->FIRST();
        $datac = $X::where('id_folios', '=', $data->id_folios)->first();
        $bancario = tbl_curso::SELECT('instructores.archivo_bancario','instructores.id AS idins','instructores.banco',
                                      'instructores.no_cuenta','instructores.interbancaria')
                                ->WHERE('tbl_cursos.id', '=', $dataf->id_cursos)
                                ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')->FIRST();

        $datap = pago::WHERE('id_contrato', '=', $datac->id_contrato)->FIRST();
        $directorio = contrato_directorio::where('id_contrato', '=', $datac->id_contrato)->FIRST();
        $elaboro = directorio::WHERE('id', '=', $directorio->solpa_elaboro)->FIRST();
        if(isset($directorio->solpa_iddirector))
        {
            $director = directorio::WHERE('id', '=', $directorio->solpa_iddirector)->FIRST();
        }
        else
        {
            $director = directorio::WHERE('id', '=', $directorio->contrato_iddirector)->FIRST();
        }
        $para = directorio::WHERE('id', '=', $directorio->solpa_para)->FIRST();
        $ccp1 = directorio::WHERE('id', '=', $directorio->solpa_ccp1)->FIRST();
        $ccp2 = directorio::WHERE('id', '=', $directorio->solpa_ccp2)->FIRST();
        $ccp3 = directorio::WHERE('id', '=', $directorio->solpa_ccp3)->FIRST();

        if($data->fecha_apertura < '2023-10-12') {
            $pago = round($data->importe_total-$data->iva, 2);
        } else {
            $pago = $data->importe_total;
        }

        return view('layouts.pages.modcontrato', compact('data','nombrecompleto','perfil_prof','perfil_sel','datacon','director','testigo1','testigo2','testigo3','data_directorio','unidadsel','unidadlist','memoval','datap','elaboro','para','directorio','regimen','datac','ccp1','ccp2','ccp3','pago'));
    }

    public function save_mod(Request $request){
        $id_curso = folio::where('id_folios', '=', $request->id_folio)->value('id_cursos');
        $contrato = contratos::find($request->id_contrato);
        $contrato->numero_contrato = $request->numero_contrato;
        // if($request->perfilinstructor != NULL)
        // {
        //     $contrato->instructor_perfilid = $request->perfilinstructor;
        // }
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->folio_fiscal = $request->folio_fiscal;
        $contrato->id_curso = $id_curso;
        $contrato->fecha_status = carbon::now();
        // $contrato->tipo_facutra = $request->tipo_factura;

        if($request->factura != NULL)
        {
            $file = $request->file('factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato,'factura');
            $contrato->arch_factura = $urldocs;
        }
        if($request->factura_xml != NULL)
        {
            $file_xml = $request->file('factura_xml'); # obtenemos el archivo
            $urldocs = $this->xml_upload($file_xml, $request->id_contrato,'factura_xml');
            $contrato->arch_factura_xml = $urldocs;
        }

        $contrato->save();


        // Metodo de XML para contrato AGREGAR BORRADO DE DOCUMENTO CON CADENA UNICA SOLO SI TODAVIA NO ESTA SELLADO
        $status_doc = DB::Table('documentos_firmar')->Where('numero_o_clave',$clave_curso->clave)
            ->Where('tipo_archivo','Contrato')
            ->Where('status', 'CANCELADO')
            ->First();

        if(!is_null($status_doc)){
            $clave_curso = DB::Table('tbl_cursos')->Select('clave')->Where('id',$id_curso)->First();
            $documento = DB::Table('documentos_firmar')->Where('numero_o_clave',$clave_curso->clave)
                ->Where('tipo_archivo','Contrato')
                ->Where('status', 'CANCELADO') //poner que evite el status de CANCELADO ICTI
                ->Delete();
            $contratoController = new EContratoController();
            $result = $contratoController->xml($request->id_contrato);
        }


        $folio = folio::find($request->id_folio);
        $folio->status = 'Capturando';
        $folio->save();


        $directorio = contrato_directorio::find($request->id_directorio);
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->save();

        $idc = $request->id_contrato;

        // metodo de solicitud de pagos
        if($this->setsolpa($request) == true) {

            $id_instructor  = DB::TABLE('contratos')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->WHERE('contratos.id_contrato', $request->id_contrato_agenda)
            ->VALUE('tbl_cursos.id_instructor');

            $pago = pago::find($request->id_pago);
            if(is_null($pago))
            {
                $pago = new pago();
                $check_pago = pago::SELECT('no_memo')->WHERE('no_memo', '=', $request->no_memo)->FIRST();
                $urldocs = $urldocs2 = null;
                $created = DB::TABLE('contratos')->WHERE('id_folios','=', $request->id_folio)->VALUE('created_at');

                if($created <= '2023-06-05')
                {
                    $pago->status_recepcion = 'recepcion tradicional';
                }
                else
                {
                    $pago->status_recepcion = null;
                }

                if(isset($check_pago))
                {
                    return back()->withErrors(sprintf('LO SENTIMOS, EL MEMORANDUM DE PAGO INGRESADO YA SE ENCUENTRA REGISTRADO', $request->no_memo));
                }
            }

            $pago->no_memo = $request->no_memo;
            $pago->id_contrato = $request->id_contrato;
            $pago->liquido = $request->liquido;
            $pago->solicitud_fecha = $request->solicitud_fecha;
            $pago->id_curso = $id_curso;
            // $pago->fecha_agenda = $request->fecha_agenda;
            $pago->fecha_status = carbon::now();

            if($request->arch_asistencia != NULL)
            {
                $file = $request->file('arch_asistencia'); # obtenemos el archivo
                $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'asistencia'); #invocamos el método
                // guardamos en la base de datos
                $pago->arch_asistencia = trim($urldocs);
            }

            if($request->arch_evidencia != NULL)
            {
                $file = $request->file('arch_evidencia'); # obtenemos el archivo
                $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'evidencia'); #invocamos el método
                // guardamos en la base de datos
                $pago->arch_evidencia = trim($urldocs);
            }

            $pago->save();
            contrato_directorio::updateOrInsert(
                ['id_contrato' => $request->id_contrato],
                ['solpa_iddirector' => $request->id_remitente,
                    'solpa_elaboro' => $request->id_elabora,
                    'solpa_para' => $request->id_destino,
                    'solpa_ccp1' => $request->id_ccp1,
                    'solpa_ccp2' => $request->id_ccp2,
                    'solpa_ccp3' => $request->id_ccp3]
            );

            if(isset($request->arch_factura))
            {
                $file = $request->file('arch_factura'); # obtenemos el archivo
                $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'factura_pdf'); #invocamos el método
                $contrato = contratos::find($request->id_contrato);
                $contrato->arch_factura = trim($urldocs);
                $contrato->save();
            }
            if(isset($request->arch_factura_xml))
            {
                $file_xml = $request->file('arch_factura_xml'); # obtenemos el archivo
                $urldocs = $this->xml_upload($file_xml, $request->id_contrato, $id_instructor, 'factura_xml'); #invocamos el método
                $contrato = contratos::find($request->id_contrato);
                $contrato->arch_factura_xml = trim($urldocs);
                $contrato->save();
            }

            if ($request->file('arch_bancario') != null)
            {
                $banco = $request->file('arch_bancario'); # obtenemos el archivo
                $urlbanco = $this->pdf_upload_bancario($banco, $request->id_instructor, 'banco'); # invocamos el método
                $instructor = instructor::find($request->id_instructor);
                $instructor->archivo_bancario = trim($urlbanco);
                $instructor->banco = $request->nombre_banco;
                $instructor->no_cuenta = $request->numero_cuenta;
                $instructor->interbancaria = $request->clabe;
                $instructor->save();
            }

            if($this->setsolpa($request) == true) {
                folio::where('id_folios', '=', $request->id_folio)
                ->update(['status' => 'Pago_Verificado']);
            }
        }

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Modificado');
    }

    public function solicitud_pago($id){
        $X = new contratos();
        $folio = new folio();
        $dataf = $folio::where('id_folios', '=', $id)->first();
        $datac = $X::where('id_folios', '=', $id)->first();
        $regimen = DB::TABLE('tbl_cursos')->SELECT('modinstructor','tipo_curso')->WHERE('id', '=', $dataf->id_cursos)->FIRST();
        $bancario = tbl_curso::SELECT('instructores.archivo_bancario','instructores.id AS idins','instructores.banco',
                                      'instructores.no_cuenta','instructores.interbancaria',
                                      'tbl_cursos.nombre','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino')
                                ->WHERE('tbl_cursos.id', '=', $dataf->id_cursos)
                                ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')->FIRST();
        return view('layouts.pages.vstasolicitudpago', compact('datac','dataf','bancario','regimen'));
    }

    public function save_doc(Request $request){
        // dd($request);
        $check_pago = pago::SELECT('no_memo')->WHERE('no_memo', '=', $request->no_memo)->FIRST();
        $urldocs = $urldocs2 = null;
        $created = DB::TABLE('contratos')->WHERE('id_folios','=', $request->id_folio)->VALUE('created_at');

        if($created <= '2023-06-05')
        {
            $status_recepcion = 'recepcion tradicional';
        }
        else
        {
            $status_recepcion = null;
        }

        $id_instructor  = DB::TABLE('contratos')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->WHERE('contratos.id_contrato', $request->id_contrato)
            ->VALUE('tbl_cursos.id_instructor');

        if(isset($check_pago))
        {
            return back()->withErrors(sprintf('LO SENTIMOS, EL MEMORANDUM DE PAGO INGRESADO YA SE ENCUENTRA REGISTRADO', $request->no_memo));
        }

        // $pago = new pago();
        // $pago->no_memo = $request->no_memo;
        // $pago->id_contrato = $request->id_contrato;
        // $pago->liquido = $request->liquido;
        // $pago->solicitud_fecha = $request->solicitud_fecha;

        if ($request->arch_asistencia != NULL)
        {
            $file = $request->file('arch_asistencia'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'lista_asistencia'); #invocamos el método
            // guardamos en la base de datos
            // $pago->arch_asistencia = trim($urldocs);
        }

        if ($request->arch_evidencia != NULL)
        {
            $file = $request->file('arch_evidencia'); # obtenemos el archivo
            $urldocs2 = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'lista_evidencia'); #invocamos el método
            // guardamos en la base de datos
            // $pago->arch_evidencia = trim($urldocs);
        }
        else
        {
            $urldocs2 = NULL;
        }
        // $pago->fecha_status = carbon::now();
        // $pago->save();


        pago::updateOrInsert(
            ['id_contrato' => $request->id_contrato],
            [
                'no_memo' => $request->no_memo,
                'liquido' => $request->liquido,
                'solicitud_fecha' => $request->solicitud_fecha,
                // 'fecha_agenda' => $request->fecha_agenda,
                'arch_asistencia' => trim($urldocs),
                'arch_evidencia' => trim($urldocs2),
                'fecha_status' => carbon::now(),
                'created_at' => carbon::now(),
                'updated_at' => carbon::now(),
                'status_recepcion' => $status_recepcion
            ]
        );

        contrato_directorio::where('id_contrato', '=', $request->id_contrato)
        ->update(['solpa_iddirector' => $request->id_remitente,
                  'solpa_elaboro' => $request->id_elabora,
                  'solpa_para' => $request->id_destino,
                  'solpa_ccp1' => $request->id_ccp1,
                  'solpa_ccp2' => $request->id_ccp2,
                  'solpa_ccp3' => $request->id_ccp3]);

        if(isset($request->arch_factura))
        {
            $file = $request->file('arch_factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'factura_pdf'); #invocamos el método
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura = trim($urldocs);
            $contrato->save();
        }
        if(isset($request->arch_factura_xml))
        {
            $file_xml = $request->file('arch_factura_xml'); # obtenemos el archivo
            $urldocs = $this->xml_upload($file_xml, $request->id_contrato, $id_instructor, 'factura_xml'); #invocamos el método
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura_xml = trim($urldocs);
            $contrato->save();
        }


        if ($request->file('arch_bancario') != null)
        {
            $banco = $request->file('arch_bancario'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload_bancario($banco, $request->id_instructor, 'banco'); # invocamos el método
            $instructor = instructor::find($request->id_instructor);
            $instructor->archivo_bancario = trim($urlbanco);
            $instructor->banco = $request->nombre_banco;
            $instructor->no_cuenta = $request->numero_cuenta;
            $instructor->interbancaria = $request->clabe;
            $instructor->save();
        }

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Verificando_Pago']);

        //Notificacion!!
        $letter = [
            'titulo' => 'Solicitud de Pago',
            'cuerpo' => 'La solicitud de pago ' . $request->no_memo . ' ha sido agregada para su validación',
            'memo' => $request->no_memo,
            'unidad' => Auth::user()->unidad,
            'url' => '/pago/verificar_pago/' . $request->id_contrato,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Agregado');

    }

    public function save_mod_solpa(Request $request)
    {
        $id_instructor  = DB::TABLE('contratos')
        ->JOIN('folios','folios.id_folios','contratos.id_folios')
        ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
        ->WHERE('contratos.id_contrato', $request->id_contrato_agenda)
        ->VALUE('tbl_cursos.id_instructor');

        $pago = pago::find($request->id_pago);
        $pago->no_memo = $request->no_memo;
        $pago->id_contrato = $request->id_contrato;
        $pago->liquido = $request->liquido;
        $pago->solicitud_fecha = $request->solicitud_fecha;
        // $pago->fecha_agenda = $request->fecha_agenda;
        $pago->fecha_status = carbon::now();

        if($request->arch_asistencia != NULL)
        {
            $file = $request->file('arch_asistencia'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'asistencia'); #invocamos el método
            // guardamos en la base de datos
            $pago->arch_asistencia = trim($urldocs);
        }

        if($request->arch_evidencia != NULL)
        {
            $file = $request->file('arch_evidencia'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'evidencia'); #invocamos el método
            // guardamos en la base de datos
            $pago->arch_evidencia = trim($urldocs);
        }

        $pago->save();

        contrato_directorio::where('id_contrato', '=', $request->id_contrato)
        ->update(['solpa_iddirector' => $request->id_remitente,
                  'solpa_elaboro' => $request->id_elabora,
                  'solpa_para' => $request->id_destino,
                  'solpa_ccp1' => $request->id_ccp1,
                  'solpa_ccp2' => $request->id_ccp2,
                  'solpa_ccp3' => $request->id_ccp3]);

        if(isset($request->arch_factura))
        {
            $file = $request->file('arch_factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, $id_instructor, 'factura_pdf'); #invocamos el método
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura = trim($urldocs);
            $contrato->save();
        }
        if(isset($request->arch_factura_xml))
        {
            $file_xml = $request->file('arch_factura_xml'); # obtenemos el archivo
            $urldocs = $this->xml_upload($file_xml, $request->id_contrato, $id_instructor, 'factura_xml'); #invocamos el método
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura_xml = trim($urldocs);
            $contrato->save();
        }

        if ($request->file('arch_bancario') != null)
        {
            $banco = $request->file('arch_bancario'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload_bancario($banco, $request->id_instructor, 'banco'); # invocamos el método
            $instructor = instructor::find($request->id_instructor);
            $instructor->archivo_bancario = trim($urlbanco);
            $instructor->banco = $request->nombre_banco;
            $instructor->no_cuenta = $request->numero_cuenta;
            $instructor->interbancaria = $request->clabe;
            $instructor->save();
        }

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Verificando_Pago']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Modificado');
    }

    public function historial_validado($id){
        $data = contratos::SELECT('contratos.id_contrato','contratos.numero_contrato','contratos.cantidad_letras1','contratos.fecha_firma',
                                 'contratos.municipio','contratos.arch_factura','contratos.id_folios','contratos.instructor_perfilid','contratos.unidad_capacitacion',
                                 'contratos.cantidad_numero','contratos.arch_factura','contratos.observacion','folios.iva','folios.id_cursos','folios.id_supre','folios.status',
                                 'tabla_supre.doc_validado','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.id_curso','tbl_cursos.mod',
                                 'instructores.nombre AS insnom','instructores.apellidoPaterno','instructores.tipo_honorario','tbl_cursos.dura',
                                 'tbl_cursos.hombre','tbl_cursos.mujer','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.efisico','tbl_cursos.dia',
                                 'tbl_cursos.hini','tbl_cursos.hfin','instructores.apellidoMaterno','instructores.id','especialidad_instructores.especialidad_id',
                                 'instructores.archivo_ine','instructores.archivo_domicilio','instructores.archivo_alta','instructores.archivo_bancario',
                                 'instructores.archivo_fotografia','instructores.archivo_estudios','instructores.archivo_otraid','instructores.archivo_rfc','especialidad_instructores.memorandum_validacion',
                                 'especialidades.nombre AS especialidad','tbl_inscripcion.costo','cursos.perfil')
                            ->WHERE('id_contrato', '=', $id)
                            ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                            ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                            ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                            ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                            ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                            ->LEFTJOIN('tbl_inscripcion', 'tbl_inscripcion.id_curso', '=', 'tbl_cursos.id')
                            ->LEFTJOIN('cursos','cursos.id', '=', 'tbl_cursos.id_curso')
                            ->FIRST();

        $cupo = $data->hombre + $data->mujer;

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        return view('layouts.pages.vsthistorialvalidarcontrato', compact('data','director','testigo1','testigo2','testigo3','cupo'));
    }

    public function delete($id)
    {
        $id_supre = folio::SELECT('id_supre')->WHERE('id_folios', '=', $id)->FIRST();
        $list = folio::SELECT('id_folios')->WHERE('id_supre', '=', $id_supre->id_supre)->GET();
        foreach($list as $item)
        {
            $idcontrato = contratos::SELECT('id_contrato')->WHERE('id_folios', '=', $item->id_folios)->FIRST();
            if($idcontrato != NULL)
            {
                contrato_directorio::WHERE('id_contrato', '=', $idcontrato->id_contrato)->DELETE();
                contratos::where('id_folios', '=', $item->id_folios)->DELETE();
            }
            $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $item->id_folios)->update(['status' => 'eliminado']);
        }
        DB::table('tabla_supre')->WHERE('id', $id_supre->id_supre)->UPDATE(['status' => 'Rechazado']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Contrato Eliminado');
    }

    public function contractRestart($id)
    {
        $id_contrato = contratos::SELECT('id_contrato')->WHERE('id_folios', '=', $id)->FIRST();
        $id_pago = pago::SELECT('id')->WHERE('id_contrato', '=', $id_contrato->id_contrato)->FIRST();
        if ($id_pago != NULL)
        {
            pago::WHERE('id', '=', $id_pago->id)->delete();
        }
        $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $id)->update(['status' => 'Contrato_Rechazado']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Contrato Reiniciado');
    }

    public function recepcion(Request $request)
    {
        //A
        folio::where('id_folios', '=', $request->idf)
                        ->update(['recepcion' => $request->fecha_recepcion]);

        return redirect()->route('contrato-inicio')
                        ->with('success','Recepción de Documentos Confirmada');
    }

    public function get_directorio(Request $request){

        $search = $request->search;

        if (isset($search)) {
            # si la variable está inicializada
            if($search == ''){
                $directorio = directorio::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno','puesto')->limit(5)->get();
            }else{
                $directorio = directorio::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno','puesto')->where('nombre', 'like', '%' .$search . '%')->limit(5)->get();
            }

            $response = array();
            foreach($directorio as $dir){
                $response[] = array("value"=>$dir->id,"label"=>$dir->nombre . " " .$dir->apellidoPaterno . " " . $dir->apellidoMaterno, "charge"=>$dir->puesto);
            }

            echo json_encode($response);
            exit;
        }
    }

    public function pre_contratoPDF($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.espe','tbl_cursos.clave','tbl_cursos.tipo_curso','tbl_cursos.horas','tbl_cursos.modinstructor','instructores.nombre',
                                  'instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion','instructores.rfc','instructores.curp',
                                  'instructores.domicilio')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();
        $fecha_act = new Carbon('23-06-2022');
        $fecha_fir = new Carbon($data_contrato->fecha_firma);
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));
        //dd($data);

        if($data->tipo_curso == 'CURSO')
        {
            if ($data->modinstructor == 'HONORARIOS') {
                $pdf = PDF::loadView('layouts.pdfpages.precontratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir'));
            }else {
                $pdf = PDF::loadView('layouts.pdfpages.precontratohasimilados', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir'));
            }
        }
        else
        {
            $pdf = PDF::loadView('layouts.pdfpages.precontratocertificacion', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir'));
        }

        return $pdf->stream("Precontrato-Instructor-$data_contrato->numero_contrato.pdf");
    }

    public function contrato_pdf($id)
    {
        $uuid = $objeto = $no_oficio = $dataFirmantes = $qrCodeBase64 = $cadena_sello = $fecha_sello = null;
        $contrato = new contratos();
        $puestos = array();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','tbl_cursos.fecha_apertura',
                                  'tbl_cursos.tipo_curso','tbl_cursos.espe', 'tbl_cursos.clave','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion',
                                  'instructores.rfc','tbl_cursos.modinstructor','instructores.curp','instructores.domicilio','tabla_supre.fecha_validacion')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();

        $fecha_act = new Carbon('23-06-2022');
        $fecha_fir = new Carbon($data_contrato->fecha_firma);
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));

        // carga de firmas electronicas

        $documento = DocumentosFirmar::where('numero_o_clave', $data->clave)
            ->Where('status','VALIDADO')
            ->Where('tipo_archivo','Contrato')
            ->first();
        if(is_null($documento)) {
            $firma_electronica = false;
            $date = strtotime($data_contrato->fecha_firma);
            $D = date('d', $date);
            $M = $this->toMonth(date('m', $date));
            $Y = date("Y", $date);
        } else {
            $firma_electronica = true;
            $date = strtotime($data->fecha_validacion);
            $D = date('d', $date);
            $M = $this->toMonth(date('m', $date));
            $Y = date("Y", $date);
        }
        if(isset($documento->uuid_sellado)){
            $objeto = json_decode($documento->obj_documento,true);
            $no_oficio = json_decode(json_encode(simplexml_load_string($documento['documento_interno'], "SimpleXMLElement", LIBXML_NOCDATA),true));
            $no_oficio = $no_oficio->{'@attributes'}->no_oficio;
            $uuid = $documento->uuid_sellado;
            $cadena_sello = $documento->cadena_sello;
            $fecha_sello = $documento->fecha_sellado;
            $folio = $documento->nombre_archivo;
            $tipo_archivo = $documento->tipo_archivo;

            $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];

            $dataFirmantes = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre')
                            ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                            ->Where('org.id', Auth::user()->id_organismo)
                            ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                            ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                            ->Get();
            //Generacion de QR
            //Verifica si existe link de verificiacion, de lo contrario lo crea y lo guarda
            if(isset($documento->link_verificacion)) {
                $verificacion = $documento->link_verificacion;
            } else {
                $documento->link_verificacion = $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                $documento->save();
            }
            ob_start();
            QRcode::png($verificacion);
            $qrCodeData = ob_get_contents();
            ob_end_clean();
            $qrCodeBase64 = base64_encode($qrCodeData);
            // Fin de Generacion
            foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist) {
                $puesto = DB::Table('tbl_funcionarios')->Select('cargo')->Where('curp',$moist['_attributes']['curp_firmante'])->First();
                if(!is_null($puesto)) {
                    array_push($puestos,$puesto->cargo);
                    // <td height="25px;">{{$search_puesto->cargo}}</td>
                } else {
                    array_push($puestos,'INSTRUCTOR');
                }
            }

        }

        if($data->tipo_curso == 'CURSO')
        {
            if ($data->modinstructor == 'HONORARIOS') {
                $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir','uuid','objeto','no_oficio','dataFirmantes','qrCodeBase64','cadena_sello','fecha_sello','puestos','firma_electronica'));
            }else {
                $pdf = PDF::loadView('layouts.pdfpages.contratohasimilados', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir','uuid','objeto','no_oficio','dataFirmantes','qrCodeBase64','cadena_sello','fecha_sello','puestos','firma_electronica'));
            }
        }
        else
        {
            $pdf = PDF::loadView('layouts.pdfpages.contratocertificacion', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir','uuid','objeto','no_oficio','dataFirmantes','qrCodeBase64','cadena_sello','fecha_sello','puestos','firma_electronica'));
        }

        $pdf->setPaper('LETTER', 'Portrait');
        return $pdf->stream("Contrato-Instructor-$data_contrato->numero_contrato.pdf");
    }

    public function contrato_web($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas',
                                  'tbl_cursos.tipo_curso', 'tbl_cursos.clave','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion','instructores.rfc',
                                  'instructores.curp','instructores.domicilio')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));



        if($data->tipo_curso == 'CURSO')
        {
            return view('layouts.pdfpages.contratohonorariosweb', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad'));
        }
        else
        {
            return view('layouts.pdfpages.contratocertificacionweb', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad'));
        }

        return $pdf->stream('Contrato Instructor.pdf');
    }

    public function solicitudpago_pdf($id){
        // dd($id);
        $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
        $data = folio::SELECT('tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.espe','tbl_cursos.mod','tbl_cursos.inicio','tbl_cursos.tipo_curso','tbl_cursos.instructor_mespecialidad',
                              'tbl_cursos.termino','tbl_cursos.modinstructor','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.id AS id_curso','instructores.nombre',
                              'instructores.apellidoPaterno','instructores.apellidoMaterno','especialidad_instructores.id', 'tbl_cursos.instructor_mespecialidad as memorandum_validacion',//'especialidad_instructores.memorandum_validacion',
                              'instructores.rfc','instructores.id AS id_instructor','instructores.banco','instructores.no_cuenta',
                              'instructores.interbancaria','folios.importe_total','folios.id_folios','contratos.unidad_capacitacion',
                              'contratos.id_contrato','contratos.numero_contrato','pagos.created_at','pagos.solicitud_fecha','pagos.no_memo','pagos.liquido')
                        ->WHERE('folios.id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->LEFTJOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
                        ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                        ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
                        ->FIRST();
        if($data->solicitud_fecha == NULL)
        {
            $date = strtotime($data->created_at);
            $D = date('d', $date);
            $M = $this->toMonth(date('m',$date));
            $Y = date("Y",$date);
        }
        else
        {
            $date = strtotime($data->solicitud_fecha);
            $D = date('d', $date);
            $M = $this->toMonth(date('m',$date));
            $Y = date("Y",$date);
        }

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $data->id_contrato)->FIRST();
        $elaboro = directorio::WHERE('id', '=', $data_directorio->solpa_elaboro)->FIRST();
        if(isset($data_directorio->solpa_iddirector))
        {
            $director = directorio::WHERE('id', '=', $data_directorio->solpa_iddirector)->FIRST();
        }
        else
        {
            $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        }
        $para = directorio::WHERE('id', '=', $data_directorio->solpa_para)->FIRST();
        $ccp1 = directorio::WHERE('id', '=', $data_directorio->solpa_ccp1)->FIRST();
        $ccp2 = directorio::WHERE('id', '=', $data_directorio->solpa_ccp2)->FIRST();
        $ccp3 = directorio::WHERE('id', '=', $data_directorio->solpa_ccp3)->FIRST();
        // dd($para);

        $direccion = tbl_unidades::WHERE('unidad',$data->unidad_capacitacion)->VALUE('direccion');
        $direccion = explode("*", $direccion);

        $pdf = PDF::loadView('layouts.pdfpages.procesodepago', compact('data','D','M','Y','elaboro','para','ccp1','ccp2','ccp3','director','distintivo','direccion'));
        $pdf->setPaper('Letter','portrait');
        return $pdf->stream('solicitud de pago.pdf');

    }

    public function contrato_certificacion_pdf($id)
    {
        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','instructores.tipo_identificacion','instructores.folio_ine','instructores.rfc','instructores.curp',
                                  'instructores.domicilio')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));

        $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad'));

        return $pdf->stream('Contrato Certificacion.pdf');

    }

    public function docs($docs){
        return response()->download($docs);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    protected function pago_upload($pdf, $id)
    {
        $tamanio = $pdf->getSize(); #obtener el tamaño del archivo del cliente
        $extensionPdf = $pdf->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $pdfFile = trim("docs"."_".date('YmdHis')."_".$id.".".$extensionPdf);
        $pdf->storeAs('/uploadContrato/contrato/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadContrato/contrato/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function pdf_upload($pdf, $id, $idins, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadContrato/instructor/'.$idins.'/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadContrato/instructor/'.$idins."/".$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }
    protected function xml_upload($xml, $id, $idins, $nom)
    {
        # nuevo nombre del archivo
        $xmlFile = trim($nom."_".date('YmdHis')."_".$id.".xml");
        $xml->storeAs('/uploadContrato/instructor/'.$idins .'/'.$id, $xmlFile); // guardamos el archivo en la carpeta storage
        $xmlUrl = Storage::url('/uploadContrato/instructor/'.$idins."/".$id."/".$xmlFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $xmlUrl;
    }

    protected function pdf_upload_bancario($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/instructor/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/instructor/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function toMonth($m)
    {
        switch ($m) {
            case 1:
                return "Enero";
            break;
            case 2:
                return "Febrero";
            break;
            case 3:
                return "Marzo";
            break;
            case 4:
                return "Abril";
            break;
            case 5:
                return "Mayo";
            break;
            case 6:
                return "Junio";
            break;
            case 7:
                return "Julio";
            break;
            case 8:
                return "Agosto";
            break;
            case 9:
                return "Septiembre";
            break;
            case 10:
                return "Octubre";
            break;
            case 11:
                return "Noviembre";
            break;
            case 12:
                return "Diciembre";
            break;


        }
    }

    protected function numberFormat($numero)
    {
        $part = explode(".", $numero);
        $part[0] = number_format($part['0']);
        $cadwell = implode(".", $part);
        return ($cadwell);
    }

    public function setsolpa($data)
    {
        $requiredFields = [
            'no_memo',
            'liquido',
            'solicitud_fecha',
            'id_remitente',
            'id_elabora',
            'id_destino',
            'id_ccp1',
            'id_ccp2',
            'id_ccp3',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data->$field)) {
                return false;
            }
        }

        return true;
    }
}
