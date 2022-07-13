<?php
//Creado por Orlando Chavez
namespace App\Http\Controllers\WebController;

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
use PDF;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Pagination\Paginator;
use DateTime;

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
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        $querySupre = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus, $unidad, $mes)
        ->SELECT('tabla_supre.id','tabla_supre.no_memo',
        'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status','folios.permiso_editar',
        'folios.recepcion','folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
        'contratos.docs','contratos.id_contrato','contratos.fecha_status','contratos.created_at',
        'contratos.observacion','tbl_cursos.termino AS fecha_termino',
        'tbl_cursos.inicio AS fecha_inicio',
        DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif"))
            ->WHERE('folios.status', '!=', 'En_Proceso')
            ->WHERE('folios.status', '!=', 'Finalizado')
            ->WHERE('folios.status', '!=', 'Rechazado')
            ->WHERE('folios.status', '!=', 'Cancelado')
            ->WHERE('folios.status', '!=', 'Validado')
            ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
            ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
            // ->WHERE('folios.status', '!=', 'Verificando_Pago')
            ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
            ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
            ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
            ->orderBy('contratos.created_at','desc');
            // ->orderBy('folios.status','desc')->orderBy('contratos.created_at','asc');

        $querySupre2 = $contratos::busquedaporcontrato($tipoContrato, $busqueda_contrato, $tipoStatus, $unidad, $mes)
            ->SELECT('tabla_supre.id','tabla_supre.no_memo',
            'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','folios.status','folios.permiso_editar',
            'folios.recepcion','folios.id_folios', 'folios.folio_validacion', 'tbl_unidades.ubicacion',
            'contratos.docs','contratos.id_contrato','contratos.fecha_status','contratos.created_at',
            'contratos.observacion','tbl_cursos.termino AS fecha_termino',
            'tbl_cursos.inicio AS fecha_inicio',
            DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif"))
                ->WHERE('folios.status', '=', 'Verificando_Pago')
                ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
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
                    ->WHERE('folios.status', '!=', 'Finalizado')
                    ->WHERE('folios.status', '!=', 'Rechazado')
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
                    ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
                    ->RIGHTJOIN('folios', 'contratos.id_folios', '=', 'folios.id_folios')
                    ->RIGHTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                    ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                    ->RIGHTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                    ->orderBy('contratos.created_at', 'desc')
                    ->PAGINATE(25, [
                        'tabla_supre.id','tabla_supre.no_memo',
                        'tabla_supre.unidad_capacitacion', 'tabla_supre.fecha','contratos.created_at',
                        'folios.status','folios.id_folios', 'folios.folio_validacion','folios.permiso_editar',
                        'folios.recepcion','tbl_unidades.ubicacion','contratos.docs','contratos.id_contrato',
                        'contratos.fecha_status','contratos.observacion','tbl_cursos.termino AS fecha_termino',
                        'tbl_cursos.inicio AS fecha_inicio',
                        DB::raw("(DATE_PART('day', CURRENT_DATE::timestamp - termino::timestamp)) fecha_dif")]);
            break;
        }
        // dd($querySupre2);
        // dd($querySupre);
        return view('layouts.pages.vstacontratoini', compact('querySupre','unidades','array_ejercicio', 'año_pointer'));
    }

    /**
     * Show the form for creating a new resourc.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $folio = new folio();
        $perfil = new InstructorPerfil();
        $data = $folio::SELECT('folios.id_folios', 'folios.folio_validacion', 'folios.importe_total','folios.iva', 'tbl_cursos.unidad','tbl_cursos.clave','tbl_cursos.termino','tbl_cursos.curso','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id')
                        ->WHERE('id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();

        $perfil_prof = $perfil::SELECT('especialidades.nombre AS nombre_especialidad', 'especialidad_instructores.id AS id_espins')
                                ->WHERE('instructor_perfil.numero_control', '=', $data->id)
                                ->WHERE('especialidad_instructores.activo', '=', TRUE)
                                ->LEFTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')->GET();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $pago = round($data->importe_total-$data->iva, 2);


        $año_referencia = '01-01-' . CARBON::now()->format('Y');
        $uni_contrato = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('unidad', '=', $data->unidad)->FIRST();

        //CONSECUTIVO DE NUMERO DE CONTRATO DEPENDIENTE DE FOLIO DE VALIDACION DE SUPRE
        // $consecutivo = intval(substr($data->folio_validacion, 10, 3));
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


        // CONSECUTIVO DE NUMERO DE CONTRATO INDEPENDIENTE
        /*$consecutivo = DB::TABLE('contratos')
                        ->WHERE('tbl_unidades.ubicacion', '=', $uni_contrato->ubicacion)
                        ->WHERE('contratos.fecha_firma','>=', $año_referencia)
                        ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                        ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', 'tbl_cursos.unidad')
                        ->LATEST('contratos.created_at')
                        ->VALUE('numero_contrato');*/
                        // dd($consecutivo);
        if ($consecutivo == NULL)
        {
            $consecutivo = '0001';
        }
        else
        {
            // FUNCION DE NUMERO DE CONTRATO INDEPENDIENTE
            /*if ($uni_contrato->ubicacion == 'TUXTLA' || $uni_contrato->ubicacion == 'COMITAN')
            {
                $consecutivo = substr($consecutivo, 10, 4) + 1;
                dd('a');
            }
            else
            {
                $consecutivo = substr($consecutivo, 11, 4) + 1;
                // dd('a');
            }*/
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
        // dd($uni_contrato);

        return view('layouts.pages.frmcontrato', compact('data','nombrecompleto','perfil_prof','pago','term','unidades','uni_contrato'));
    }

    public function contrato_save(Request $request)
    {
        $check_contrato = contratos::SELECT('numero_contrato')
            ->WHERE('numero_contrato', '=', $request->numero_contrato)
            ->FIRST();
        if(isset($check_contrato))
        {
            return back()->withErrors(sprintf('LO SENTIMOS, EL NUMERO DE CONTRATO INGRESADO YA SE ENCUENTRA REGISTRADO', $request->numero_contrato));
        }
        // dd($request->numero_contrato);
        $contrato = new contratos();
        $contrato->numero_contrato = $request->numero_contrato;
        $contrato->instructor_perfilid = $request->perfil_instructor;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->id_folios = $request->id_folio;
        $contrato->fecha_status = carbon::now();
        // $contrato->tipo_factura = $request->tipo_factura;
        $file = $request->file('factura'); # obtenemos el archivo
        if ($file != NULL)
        {
            $urldocs = $this->pdf_upload($file, $request->id_contrato,'factura');
            $contrato->arch_factura = $urldocs;
        }
        $contrato->save();

        $id_contrato = contratos::SELECT('id_contrato')->WHERE('numero_contrato', '=', $request->numero_contrato)->FIRST();
        $directorio = new contrato_directorio();
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->id_contrato = $id_contrato->id_contrato;
        $directorio->save();

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Validando_Contrato']);

        $idc = $id_contrato->id_contrato;

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

        return view('layouts.pages.contratocheck', compact('idc'));
    }

    public function modificar($id)
    {
        $folio = new folio();
        $especialidad = new especialidad();
        $perfil = new InstructorPerfil();

        $datacon = contratos::WHERE('id_contrato', '=', $id)->FIRST();
        $data = $folio::SELECT('folios.id_folios','folios.iva','tbl_cursos.clave','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id', 'tbl_cursos.curso')
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

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $unidadsel = tbl_unidades::SELECT('unidad')->WHERE('unidad', '=', $datacon->unidad_capacitacion)->FIRST();
        $unidadlist = tbl_unidades::SELECT('unidad')->WHERE('unidad', '!=', $datacon->unidad_capacitacion)->GET();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        return view('layouts.pages.modcontrato', compact('data','nombrecompleto','perfil_prof','perfil_sel','datacon','director','testigo1','testigo2','testigo3','data_directorio','unidadsel','unidadlist'));
    }

    public function save_mod(Request $request){
        $contrato = contratos::find($request->id_contrato);
        $contrato->numero_contrato = $request->numero_contrato;
        if($request->perfilinstructor != NULL)
        {
            $contrato->instructor_perfilid = $request->perfilinstructor;
        }
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->fecha_status = carbon::now();
        // $contrato->tipo_facutra = $request->tipo_factura;

        if($request->factura != NULL)
        {
            $file = $request->file('factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato,'factura');
            $contrato->arch_factura = $urldocs;
        }

        $contrato->save();

        $folio = folio::find($request->id_folio);
        $folio->status = 'Validando_Contrato';
        $folio->save();


        $directorio = contrato_directorio::find($request->id_directorio);
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->save();

        $idc = $request->id_contrato;
        return view('layouts.pages.contratocheck', compact('idc'));
    }

    public function validar_contrato($id){
        $data = contratos::SELECT('contratos.id_contrato','contratos.numero_contrato','contratos.cantidad_letras1','contratos.fecha_firma',
                                 'contratos.municipio','contratos.arch_factura','contratos.id_folios','contratos.instructor_perfilid','contratos.unidad_capacitacion',
                                 'contratos.cantidad_numero','contratos.arch_factura','folios.iva','folios.id_cursos','folios.id_supre','tabla_supre.doc_validado',
                                 'tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.id_curso','tbl_cursos.mod','tbl_cursos.pdf_curso',
                                 'tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion',
                                 'instructores.nombre AS insnom','instructores.apellidoPaterno','instructores.tipo_honorario','tbl_cursos.dura',
                                 'tbl_cursos.hombre','tbl_cursos.mujer','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.efisico','tbl_cursos.dia',
                                 'tbl_cursos.hini','tbl_cursos.instructor_mespecialidad','tbl_cursos.hfin','tbl_cursos.folio_grupo','tbl_cursos.modinstructor','instructores.apellidoMaterno','instructores.id','especialidad_instructores.especialidad_id',
                                 'instructores.archivo_ine','instructores.archivo_domicilio','instructores.archivo_alta','instructores.archivo_bancario',
                                 'instructores.archivo_fotografia','instructores.archivo_estudios','instructores.archivo_otraid','instructores.archivo_rfc','especialidad_instructores.memorandum_validacion',
                                 'especialidades.nombre AS especialidad','tbl_inscripcion.costo','cursos.perfil','alumnos_registro.comprobante_pago')
                            ->WHERE('id_contrato', '=', $id)
                            ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                            ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                            ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                            ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                            ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                            ->LEFTJOIN('tbl_inscripcion', 'tbl_inscripcion.id_curso', '=', 'tbl_cursos.id')
                            ->LEFTJOIN('cursos','cursos.id', '=', 'tbl_cursos.id_curso')
                            ->LEFTJOIN('alumnos_registro', 'alumnos_registro.folio_grupo', '=', 'tbl_cursos.folio_grupo')
                            ->FIRST();

        // $comprobante_pago = DB::TABLE('alumnos_registro')->WHERE('folio_grupo')->VALUE('comprobante_pago');
        $data->comprobante_pago = '/storage/uploadFiles' . $data->comprobante_pago;
        $cupo = $data->hombre + $data->mujer;

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        return view('layouts.pages.vstvalidarcontrato', compact('data','director','testigo1','testigo2','testigo3','cupo'));
    }

    public function rechazar_contrato(Request $request){
        $contrato = contratos::find($request->idContrato);
        $contrato->observacion = $request->observaciones;
        $contrato->chk_rechazado = TRUE;
        $contrato->fecha_status = carbon::now();
        if($contrato->fecha_rechazo == NULL)
        {
            $contrato->fecha_rechazo = array(array('fecha' => carbon::now()->toDateString(), 'observacion' => $request->observaciones));
        }
        else
        {
            $new = array('fecha' => carbon::now()->toDateString(), 'observacion' => $request->observaciones);
            $old = $contrato->fecha_rechazo;
            array_push($old, $new);
            $contrato->fecha_rechazo = $old;
        }
        $contrato->save();

        $folio = folio::find($request->idfolios);
        $folio->fecha_rechazado = carbon::now();
        $folio->status = 'Contrato_Rechazado';
        $folio->save();

        //Notificacion!
        $letter = [
            'titulo' => 'Solicitud de Contrato Rechazada',
            'cuerpo' => 'La solicitud de contrato ' . $contrato->numero_contrato . ' ha sido rechazada',
            'memo' => $contrato->numero_contrato,
            'unidad' => $contrato->unidad_capacitacion,
            'url' => '/contrato/modificar/' . $contrato->id,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        return redirect()->route('contrato-inicio')
                        ->with('success','Contrato Rechazado Exitosamente');
    }

    public function valcontrato(Request $request){
        contratos::where('id_folios', '=', $request->id)
        ->update(['fecha_status' => carbon::now(),
                  'observacion' => $request->observaciones]);

        $contrato = contratos::WHERE('id_folios', '=', $request->id)->FIRST();

        $folio = folio::find($request->id);
        $folio->status = "Contratado";
        $folio->save();
        return redirect()->route('contrato-inicio')
                        ->with('success','Contrato Validado Exitosamente');

        //Notificacion!
        $letter = [
            'titulo' => 'Solicitud de Contrato Validada',
            'cuerpo' => 'La solicitud de contrato ' . $contrato->numero_contrato . ' ha sido validada',
            'memo' => $contrato->numero_contrato,
            'unidad' => $contrato->unidad_capacitacion,
            'url' => '/contrato/' . $contrato->id,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));
    }

    public function solicitud_pago($id){
        $X = new contratos();
        $folio = new folio();
        $dataf = $folio::where('id_folios', '=', $id)->first();
        $datac = $X::where('id_folios', '=', $id)->first();
        $regimen = DB::TABLE('tbl_cursos')->SELECT('modinstructor','tipo_curso')->WHERE('id', '=', $dataf->id_cursos)->FIRST();
        $bancario = tbl_curso::SELECT('instructores.archivo_bancario','instructores.id AS idins','instructores.banco',
                                      'instructores.no_cuenta','instructores.interbancaria')
                                ->WHERE('tbl_cursos.id', '=', $dataf->id_cursos)
                                ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')->FIRST();
        return view('layouts.pages.vstasolicitudpago', compact('datac','dataf','bancario','regimen'));
    }

    public function save_doc(Request $request){
        $check_pago = pago::SELECT('no_memo')->WHERE('no_memo', '=', $request->no_memo)->FIRST();
        $urldocs = null;
        if(isset($check_pago))
        {
            return back()->withErrors(sprintf('LO SENTIMOS, EL MEMORANDUM DE PAGO INGRESADO YA SE ENCUENTRA REGISTRADO', $request->no_memo));
        }

        // $pago = new pago();
        // $pago->no_memo = $request->no_memo;
        // $pago->id_contrato = $request->id_contrato;
        // $pago->liquido = $request->liquido;
        // $pago->solicitud_fecha = $request->solicitud_fecha;

        $file = $request->file('arch_asistencia'); # obtenemos el archivo
        $urldocs = $this->pago_upload($file, $request->id_contrato, 'asistencia'); #invocamos el método
        // // guardamos en la base de datos
        // $pago->arch_asistencia = trim($urldocs);

        if ($request->arch_evidencia != NULL)
        {
            $file = $request->file('arch_evidencia'); # obtenemos el archivo
            $urldocs2 = $this->pdf_upload($file, $request->id_contrato, 'evidencia'); #invocamos el método
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
                'arch_asistencia' => trim($urldocs),
                'arch_evidencia' => trim($urldocs2),
                'fecha_status' => carbon::now(),
                'created_at' => carbon::now(),
                'updated_at' => carbon::now()
            ]
        );

        contrato_directorio::where('id_contrato', '=', $request->id_contrato)
        ->update(['solpa_iddirector' => $request->id_remitente,
                  'solpa_elaboro' => $request->id_elabora,
                  'solpa_para' => $request->id_destino,
                  'solpa_ccp1' => $request->id_ccp1,
                  'solpa_ccp2' => $request->id_ccp2,
                  'solpa_ccp3' => $request->id_ccp3]);
        if($request->arch_factura != NULL)
        {
            $file = $request->file('arch_factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, 'factura'); #invocamos el método
            // guardamos en la base de datos
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura = trim($urldocs);
            $contrato->save();
        }

        if ($request->file('arch_bancario') != null)
        {
            $banco = $request->file('arch_bancario'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload($banco, $request->id_instructor, 'banco'); # invocamos el método
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

    public function mod_solicitud_pago($id){
        $X = new contratos();
        $folio = new folio();
        $dataf = $folio::where('id_folios', '=', $id)->first();
        $regimen = DB::TABLE('tbl_cursos')->SELECT('modinstructor','tipo_curso')->WHERE('id', '=', $dataf->id_cursos)->FIRST();
        $datac = $X::where('id_folios', '=', $id)->first();
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

        return view('layouts.pages.vstamodsolicitudpago', compact('datac','dataf','datap','regimen','bancario','directorio','elaboro','para','ccp1','ccp2','ccp3','director'));
    }

    public function save_mod_solpa(Request $request){

        $pago = pago::find($request->id_pago);
        $pago->no_memo = $request->no_memo;
        $pago->id_contrato = $request->id_contrato;
        $pago->liquido = $request->liquido;
        $pago->solicitud_fecha = $request->solicitud_fecha;
        $pago->fecha_status = carbon::now();

        if($request->arch_asistencia != NULL)
        {
            $file = $request->file('arch_asistencia'); # obtenemos el archivo
            $urldocs = $this->pago_upload($file, $request->id_contrato, 'asistencia'); #invocamos el método
            // guardamos en la base de datos
            $pago->arch_asistencia = trim($urldocs);
        }

        if($request->arch_evidencia != NULL)
        {
            $file = $request->file('arch_evidencia'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, 'evidencia'); #invocamos el método
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

        if($request->arch_factura != NULL)
        {
            $file = $request->file('arch_factura'); # obtenemos el archivo
            $urldocs = $this->pdf_upload($file, $request->id_contrato, 'factura'); #invocamos el método
            // guardamos en la base de datos
            $contrato = contratos::find($request->id_contrato);
            $contrato->arch_factura = trim($urldocs);
            $contrato->save();
        }

        if ($request->file('arch_bancario') != null)
        {
            $banco = $request->file('arch_bancario'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload($banco, $request->id_instructor, 'banco'); # invocamos el método
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

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id', 'tbl_cursos.clave','tbl_cursos.tipo_curso','tbl_cursos.horas','tbl_cursos.modinstructor','instructores.nombre',
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
        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas',
                                  'tbl_cursos.tipo_curso', 'tbl_cursos.clave','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion','instructores.rfc','tbl_cursos.modinstructor',
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

        $fecha_act = new Carbon('23-06-2022');
        $fecha_fir = new Carbon($data_contrato->fecha_firma);
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));

        if($data->tipo_curso == 'CURSO')
        {
            if ($data->modinstructor == 'HONORARIOS') {
                $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir'));
            }else {
                $pdf = PDF::loadView('layouts.pdfpages.contratohasimilados', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir'));
            }
        }
        else
        {
            $pdf = PDF::loadView('layouts.pdfpages.contratocertificacion', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir'));
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

        $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
        $data = folio::SELECT('tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.espe','tbl_cursos.mod','tbl_cursos.inicio','tbl_cursos.tipo_curso',
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
        //dd($data);
        $pdf = PDF::loadView('layouts.pdfpages.procesodepago', compact('data','D','M','Y','elaboro','para','ccp1','ccp2','ccp3','director','distintivo'));
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
    protected function pdf_upload($pdf, $id, $nom)
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
}
