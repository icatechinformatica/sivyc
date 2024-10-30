<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Http\Controllers\efirma\EValsupreController;
use App\Http\Controllers\efirma\ESupreController;
use \setasign\Fpdi\PdfParser\StreamReader;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\contrato_directorio;
use Illuminate\Support\Facades\DB;
use App\Events\NotificationEvent;
use App\Models\DocumentosFirmar;
use App\Models\supre_directorio;
use App\Exports\FormatoTReport;
use App\Models\criterio_pago;
use Illuminate\Http\Request;
use App\Models\tbl_unidades;
use App\Models\directorio;
use App\Models\instructor;
use App\Models\tbl_curso;
use App\Models\contratos;
use setasign\Fpdi\Fpdi;
use App\Models\supre;
use PHPQRCode\QRcode;
use App\Models\folio;
use App\Models\pago;
use App\Models\ISR;
use Carbon\Carbon;
use App\User;
use PDF;

class supreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function solicitud_supre_inicio(Request $request) {
        $array_ejercicio = $status_doc = array();
        $año_pointer = CARBON::now()->format('Y');
        $unidaduser = tbl_unidades::SELECT('ubicacion')->WHERE('id',Auth::user()->unidad)->FIRST();
        $roles = DB::table('role_user')
            ->Join('roles', 'roles.id', '=', 'role_user.role_id')
            ->Select('roles.slug AS role_name')
            ->Where('role_user.user_id', '=', Auth::user()->id)
            ->First();
        /**
         * parametros de busqueda
         */
        $busqueda_suficiencia = $request->get('busquedaporSuficiencia');
        $tipoSuficiencia = $request->get('tipo_suficiencia');
        $tipoStatus = $request->get('tipo_status');
        $unidad = $request->get('unidad');

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

        $supre = new supre();
        $data = $supre::BusquedaSupre($tipoSuficiencia, $busqueda_suficiencia, $tipoStatus, $unidad)
            ->Select('tabla_supre.*','folios.permiso_editar')
            ->selectSub(function($query) {
                $query->From('documentos_firmar')
                    ->SelectRaw('CASE WHEN COUNT(*) > 0 THEN true ELSE false END')
                    ->WhereColumn('documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
                    ->Where('documentos_firmar.tipo_archivo', 'supre')
                    ->Where('documentos_firmar.status', ['VALIDADO','EnFirma']);
                }, 'supre_sellado')
            ->SelectSub(function($query) {
                $query->From('documentos_firmar')
                    ->Select('status')
                    ->WhereColumn('documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
                    ->Where('documentos_firmar.tipo_archivo', 'supre');
                    // ->WhereIn('documentos_firmar.status', ['VALIDADO','EnFirma']);
                }, 'efirma_status_supre')
            ->SelectSub(function($query) {
                $query->From('documentos_firmar')
                    ->SelectRaw('CASE WHEN COUNT(*) > 0 THEN true ELSE false END')
                    ->WhereColumn('documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
                    ->Where('documentos_firmar.tipo_archivo', 'valsupre')
                    ->WhereIn('documentos_firmar.status', ['VALIDADO','EnFirma']);
                }, 'valsupre_sellado')
                ->SelectSub(function($query) {
                    $query->From('documentos_firmar')
                    ->Select('status')
                    ->WhereColumn('documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
                    ->Where('documentos_firmar.tipo_archivo', 'valsupre')
                    ->Limit(1);
                    // ->WhereIn('documentos_firmar.status', ['VALIDADO','EnFirma']);
                }, 'efirma_status_valsupre')
            ->Where('tabla_supre.id', '!=', '0')
            ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
            ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
            ->WHERE('tabla_supre.status', '!=', 'Cancelado');

        if($roles->role_name != 'admin' && $roles->role_name != 'planeacion') {
            $data = $data->Where('unidad_capacitacion', $unidaduser->ubicacion);
        }

        $data = $data->RightJoin('folios', 'folios.id_supre', '=', 'tabla_supre.id')
            ->RightJoin('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
            ->LeftJoin('documentos_firmar','documentos_firmar.numero_o_clave','=', 'tbl_cursos.clave')
            ->LeftJoin('pagos', 'pagos.id_curso', 'folios.id_cursos')
            ->OrderBy('tabla_supre.status','ASC')
            ->OrderBy('tabla_supre.updated_at','DESC')
            ->GroupBy('tabla_supre.id','folios.permiso_editar','clave')
            // ->paginate(25, ['tabla_supre.*','folios.permiso_editar',\DB::raw('supre_sellado'),\DB::raw('valsupre_sellado'),'pagos.status_recepcion']);
            ->paginate(25, ['tabla_supre.*','folios.permiso_editar',\DB::raw('supre_sellado'),\DB::raw('valsupre_sellado'),'pagos.status_recepcion']);

        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstasolicitudsupre', compact('data', 'unidades','array_ejercicio','año_pointer'));
    }

    public function frm_formulario() {
        $funcionarios = array();
        $unidad = tbl_unidades::SELECT('ubicacion','id','clave_contrato')->WHERE('id',Auth::user()->unidad)->FIRST();
        $year = Carbon::now()->year;
        $inicio = $year.'-01-01';
        $final = $year.'-12-30';

        $countCursos = DB::Table('folios')->Where('tabla_supre.unidad_capacitacion',$unidad->ubicacion)
        ->WhereBetween('tabla_supre.fecha',['2024-01-01','2024-12-30'])
        ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
        ->Count('folios.id_folios');

        do {
            $countCursos++;
            switch (strlen($countCursos)) {
                case 1:
                    $countCursos = '00'.$countCursos;
                break;
                case 2:
                    $countCursos = '0'.$countCursos;
                break;
                // case 3:
                //     $countCursos = '0'.$countCursos;
                // break;
                default:

                break;
            }
            $folio_validacion = 'DPP-SP-'.substr($unidad->ubicacion, 0, 2).'-'.$countCursos.'-'.$year;
            $folioExists = DB::Table('folios')->Where('folio_validacion',$folio_validacion)->First();
        } while (!is_null($folioExists));

        $funcionarios = $this->funcionarios_supre($unidad->ubicacion);
        return view('layouts.pages.delegacionadmin', compact('unidad','funcionarios','year','folio_validacion'));
    }

    public function store(Request $request) {
        // dd($request);
        $generalarr = $arrmov = array();
        $claveUnidad = tbl_unidades::Where('id',Auth::user()->unidad)->Value('clave_contrato');
        $fecha = Carbon::parse($request->input('fecha'));
        $year = $fecha->year;
        $memorandum = 'ICATECH/'.$claveUnidad.'/'.preg_replace('/\D/', '', $request->memorandum).'/'.$year;
        $memo = supre::SELECT('no_memo')->WHERE('no_memo', '=', $memorandum)->FIRST();
        if (is_null($memo))
        {
            foreach ($request->addmore as $key => $value)
            {
                $validacion_folio = folio::SELECT('folio_validacion')
                 ->WHERE('folio_validacion', '=', $value['folio'])
                 ->WHERE('status', '!=', 'Cancelado')
                 ->FIRST();
                 if (isset($validacion_folio))
                 {
                    return redirect()->route('frm-supre')
                    ->withErrors(sprintf('LO SENTIMOS, EL NUMERO DE FOLIO INGRESADO YA SE ENCUENTRA REGISTRADO', $validacion_folio));
                 }
                 $claveval = tbl_curso::SELECT('id','modinstructor')->WHERE('clave', '=', $value['clavecurso'])->FIRST();
                 $validacion_curso = folio::SELECT('id_cursos')
                 ->WHERE('id_cursos', '=', $claveval->id)
                 ->WHERE('status', '!=', 'Cancelado')
                 ->FIRST();
                 if (isset($validacion_curso))
                 {
                    return redirect()->route('frm-supre')
                    ->withErrors(sprintf('LO SENTIMOS, EL CURSO INGRESADO YA SE ENCUENTRA REGISTRADO', $validacion_curso));
                 }
            }
            $supre = new supre();
            $curso_validado = new tbl_curso();

            //Guarda Solicitud
            $supre->unidad_capacitacion = strtoupper($request->unidad);
            $supre->no_memo = strtoupper($memorandum);
            $supre->fecha = strtoupper($request->fecha);
            $supre->status = 'En_Proceso';
            $supre->fecha_status = strtoupper($request->fecha);
            $supre->elabora = ['nombre' => $request->nombre_elabora,
                               'puesto' => $request->puesto_elabora];

            $supre->save();
            // auth()->user()->notify(new SupreNotification($supre));

            $id = $supre->id;

            //Guarda Folios
            foreach ($request->addmore as $key => $value)
            {
                // dd($value);
                $folio = new folio();
                $folio->folio_validacion = strtoupper($value['folio']);
                $folio->iva = $value['iva'];
                $folio->comentario = $value['comentario'];
                $clave = strtoupper($value['clavecurso']);
                $hora = $curso_validado->SELECT('tbl_cursos.dura','tbl_cursos.id')
                        ->WHERE('tbl_cursos.clave', '=', $clave)
                        ->FIRST();
                if($value['iva'] == 0)
                {
                    $importe = $value['importe'];
                }
                else
                {
                    $importe = $value['importe']/1.16;
                }
                $X = $hora->dura;
                if ($X != NULL)
                {
                    if (strpos($hora->dura, " "))
                    {
                        # si tiene un espacio en blanco la cadena
                        $str_horas = explode (" ", $hora->dura);
                        $horas = (int) $str_horas[0];
                    } else
                    {
                        $horas = (int) $hora->dura;
                    }
                    $importe_hora = floatval(number_format($importe / $horas, 2, '.', ''));
                    $folio->importe_hora = $importe_hora;
                    $folio->importe_total = $value['importe'];
                    $folio->id_supre = $id;
                    $folio->id_cursos = $hora->id;
                    $folio->status = 'En_Proceso';

                    //Calculo del nuevo campo impuestos
                    if($claveval->modinstructor ==  'HONORARIOS')
                    {
                        $folio->impuestos = $this->honorarios(floatval(number_format($importe, 2, '.', '')));
                    }
                    else
                    {
                        $folio->impuestos = $this->asimilados(floatval(number_format($importe, 2, '.', '')));
                    }

                    $folio->save();
                }
                else
                {
                    supre::WHERE('id', '=', $id)->DELETE();
                    return redirect()->route('supre-inicio')
                            ->with('success','Error Interno. Intentelo mas tarde.');
                }
            }
            // Notificacion!
            $letter = [
                'titulo' => 'Suficiencia Presupuestal',
                'cuerpo' => 'La suficicencia presupuestal ' . $supre->no_memo . ' ha sido agregada para su validación',
                'memo' => $supre->no_memo,
                'unidad' => $supre->unidad_capacitacion,
                'url' => '/supre/validacion/' . $id,
            ];
            //$users = User::where('id', 1)->get();
            // dd($users);
            //event((new NotificationEvent($users, $letter)));

            $id = base64_encode($id);
            return redirect()->route('modificar_supre', ['id' => $id])
                             ->with('success','Solicitud de Suficiencia Presupuestal Guardado');
        }
        else
        {
            return redirect()->route('frm-supre')
                    ->withErrors(sprintf('LO SENTIMOS, EL NUMERO DE MEMORANDUM INGRESADO YA SE ENCUENTRA REGISTRADO', $memorandum));
        }
    }

    public function solicitud_modificar($id)
    {
        $id = base64_decode($id);
        $supre = new supre();
        $folio = new folio();
        $generarEfirmaSupre = TRUE;

        $getsupre = $supre::WHERE('id', '=', $id)->FIRST();
        $getsupre->no_memo = explode('/', $getsupre->no_memo);

        $unidadsel = tbl_unidades::SELECT('id','unidad','clave_contrato')->WHERE('unidad', '=', $getsupre->unidad_capacitacion)->FIRST();

        $getfolios = $folio::SELECT('folios.id_folios','folios.folio_validacion','folios.comentario',
                                'folios.importe_total','folios.iva','tbl_cursos.clave',
                                'tbl_cursos.mov_bancario', 'tbl_cursos.folio_pago','tbl_cursos.id')
                            ->WHERE('id_supre','=', $getsupre->id)
                            ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                            ->GET();

        $agenda = DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo','o.id_parent')
            ->Join('tbl_funcionarios AS f', 'f.id_org','o.id')
            ->Where('o.id_unidad',$unidadsel->id)->where('f.titular', true)
            ->Get();

        Foreach($agenda as $moist) {
            if($moist->id_parent == 1){
                $funcionarios['director'] = $moist->nombre;
                $funcionarios['directorp'] = $moist->cargo;
            }
            if(str_contains($moist->cargo, 'ADMINISTRATIVO')) {
                $funcionarios['delegado'] = $moist->nombre;
                $funcionarios['delegadop'] = $moist->cargo;
            }

        }

        $getfolios[0]->mov_bancario = json_decode($getfolios[0]->mov_bancario);

        $recibo = DB::Table('tbl_recibos')->Select('fecha_expedicion','folio_recibo')
            ->Where('id_concepto',1)
            ->Where('id_curso',$getfolios[0]->id)
            ->First();

        if($recibo == null) {
            $recibo = DB::Table('tbl_cursos')->Select('fecha_pago AS fecha_expedicion','folio_pago AS folio_recibo')
                ->Where('id',$getfolios[0]->id)
                ->First();
        }

        // check para validar si todavia se puede firmar electronicamente el contrato
        $status_doc = DB::Table('documentos_firmar')->Where('numero_o_clave',$getfolios[0]->clave)
            ->Where('tipo_archivo','supre')
            ->Get();

        foreach($status_doc as $mxs) {
            if(!is_null($mxs)) {
                if(in_array($mxs->status, ['CANCELADO ICTI','VALIDADO'])) {
                    $generarEfirmaSupre = FALSE;
                } elseif($mxs->status == 'EnFirma') {
                    $firmantes = json_decode($mxs->obj_documento, true);
                    foreach($firmantes['firmantes']['firmante']['0'] as $firmante) {
                        if(isset($firmante['_attributes']['certificado'])) {
                            $generarEfirmaSupre = FALSE;
                        }
                    }
                }
            }
        }
        // FINAL del check
        return view('layouts.pages.modsupre',compact('getsupre','getfolios','unidadsel','recibo','funcionarios','generarEfirmaSupre'));
    }

    public function solicitud_mod_guardar(Request $request)
    {
        // dd($request);
        $generalarr = $arrmov = array();
        $supre = new supre();
        $curso_validado = new tbl_curso();

        $claveUnidad = tbl_unidades::Where('id',Auth::user()->unidad)->Value('clave_contrato');
        $fecha = Carbon::parse($request->input('fecha'));
        $year = $fecha->year;
        $memorandum = 'ICATECH/'.$claveUnidad.'/'.preg_replace('/\D/', '', $request->no_memo).'/'.$year;

        $elabora = ['nombre' => $request->nombre_elabora,
                    'puesto' => $request->puesto_elabora];

        supre::where('id', '=', $request->id_supre)
        ->update(['status' => 'En_Proceso',
                  'unidad_capacitacion' => $request->unidad,
                  'no_memo' => $memorandum,
                  'fecha' => $request->fecha,
                  'fecha_status' => carbon::now(),
                  'elabora' => $elabora]);

        if($request->id_supre != NULL)
        {
            $folio = folio::WHERE('id_supre', '=', $request->id_supre)->FIRST();
        }

        //Guarda Folios
        $value = $request->addmore[0];

            $folio->folio_validacion = $value['folio'];
            $folio->iva = $value['iva'];
            $folio->comentario = $value['comentario'];
            $clave = $value['clavecurso'];
            $hora = $curso_validado->SELECT('tbl_cursos.dura','tbl_cursos.id','tbl_cursos.modinstructor')
                    ->WHERE('tbl_cursos.clave', '=', $clave)
                    ->FIRST();
            if($value['iva'] == 0)
            {
                $importe = $value['importe'];
            }
            else
            {
                $importe = $value['importe']/1.16;
            }
            $importe_hora = $importe / $hora->dura;
            $folio->importe_hora = $importe_hora;
            $folio->importe_total = $value['importe'];
            $folio->id_supre = $request->id_supre;
            $folio->id_cursos = $hora->id;
            $folio->status = 'En_Proceso';

            if($hora->modinstructor ==  'HONORARIOS')
            {
                $folio->impuestos = $this->honorarios(floatval(number_format($importe, 2, '.', '')));
            }
            else
            {
                $folio->impuestos = $this->asimilados(floatval(number_format($importe, 2, '.', '')));
            }

            $folio->save();

        return redirect()->route('modificar_supre', ['id' => base64_encode($request->id_supre)])
            ->with('success','Solicitud de Suficiencia Presupuestal Modificada');
    }

    public function generar_supre_efirma(request $request) {
        // dd($request);

        $status_doc = DB::Table('documentos_firmar')->Where('numero_o_clave',$request->clave_curso)->Where('tipo_archivo','supre')->Get();
        if(!is_null($status_doc)) {
            foreach($status_doc as $mxs) {
                if(!is_null($mxs) && in_array($mxs->status, ['VALIDADO', 'EnFirma'])){
                    if(!is_null($mxs->uuid_sellado)) {
                        return redirect()->route('modificar_supre', ['id' => base64_encode($request->ids)])
                                    ->with('error','Error: El documento ha sido sellado anteriormente (3)');
                    }
                    $firmantes = json_decode($mxs->obj_documento, true);
                    foreach($firmantes['firmantes']['firmante']['0'] as $firmante) {
                        if(isset($firmante['_attributes']['certificado'])) {
                            return redirect()->route('modificar_supre', ['id' => base64_encode($request->ids)])
                                    ->with('error','Error: El documento esta en proceso de firmado (4)');
                        }
                    }
                }
            }
        }

        $supreController = new ESupreController();
        $result = $supreController->generar_xml($request->ids);

        if(isset($result['error'])) {
            return redirect()->route('modificar_supre', ['id' => base64_encode($request->ids)])
                             ->with('error','Hubo un error al generar el documento electronico, favor de avisar en el grupo de soporte técnico. ('.$result['error'].')');
        }

        return redirect()->route('modificar_supre', ['id' => base64_encode($request->ids)])
                             ->with('success','Solicitud de Suficiencia Presupuestal Electronico Generado Exitosamente');
    }

    public function validacion_supre_inicio(){
        return view('layouts.pages.initvalsupre');
    }

    public function validacion($id){
        $year = Carbon::now()->year;
        $id = base64_decode($id);
        $supre = new supre();
        $data =  $supre::WHERE('id', '=', $id)->FIRST();
        $fecha_apertura = DB::Table('tabla_supre')
            ->Join('folios','folios.id_supre','tabla_supre.id')
            ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Where('tabla_supre.id',$data->id)
            ->Value('fecha_apertura');

        $funcionarios = $this->funcionarios_supre($data->unidad_capacitacion);
        $criterio_pago = DB::TABLE('criterio_pago')
            ->SELECT('cp','perfil_profesional')
            ->JOIN('tbl_cursos','tbl_cursos.cp','criterio_pago.id')
            ->JOIN('folios','folios.id_cursos','tbl_cursos.id')
            ->WHERE('folios.id_supre', $data->id)
            ->FIRST();
        if($criterio_pago == null) {
            $criterio_pago = DB::TABLE('criterio_pago')->SELECT('id AS cp','perfil_profesional')->WHERE('id','11')->FIRST();
        }


        // $notification = DB::table('notifications')
        //                 ->WHERE('data', 'LIKE', '%"supre_id":'.$id.'%')->WHERE('read_at', '=', NULL)
        //                 ->UPDATE(['read_at' => Carbon::now()->toDateTimeString()]);
        // dd($notification);

        return view('layouts.pages.valsupre',compact('data','criterio_pago','fecha_apertura','funcionarios','year'));
    }

    public function supre_rechazo(Request $request){
        $supre = supre::find($request->id);
        $supre->observacion = $request->comentario_rechazo;
        $supre->fecha_status = carbon::now();
        $supre->fecha_rechazado = carbon::now();
        $supre->status = 'Rechazado';
        //dd($supre);
        $supre->save();

        // Notificacion!
        $letter = [
            'titulo' => 'Suficiencia Presupuestal Rechazada',
            'cuerpo' => 'La suficicencia presupuestal ' . $supre->no_memo . ' ha sido rechazada',
            'memo' => $supre->no_memo,
            'unidad' => $supre->unidad_capacitacion,
            'url' => '/supre/solicitud/modificar/' . $supre->id,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));
            return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Rechazado');
    }

    public function supre_validado(Request $request){
        // dd($request);
        $fecha = Carbon::parse($request->input('fecha_val'));
        $year = $fecha->year;
        $folio_validacion= 'ICATECH/500.1/H/'.preg_replace('/\D/', '', $request->folio_validacion).'/'.$year;
        $supre = supre::find($request->id);
        $supre->status = 'Validado';
        $supre->folio_validacion = $folio_validacion;
        $supre->fecha_validacion = $request->fecha_val;
        $supre->financiamiento = $request->financiamiento;
        switch($request->financiamiento) {
            case 'FEDERAL':
                $porcentajeFinanciamiento = ['estatal' => '0', 'federal' => '100'];
            break;
            case 'FEDERAL':
                $porcentajeFinanciamiento = ['estatal' => '100', 'federal' => '0'];
            break;
            case 'FEDERAL Y ESTATAL':
                $porcentajeFinanciamiento = ['estatal' => '40', 'federal' => '60'];
            break;
        }
        $supre->permiso_editar = FALSE;
        $supre->fecha_status = carbon::now();
        $supre->observacion_validacion = $request->observacion;
        $supre->save();

        $folio = folio::where('id_supre', '=', $request->id)->First();

        // se analiza si ya tiene un contrato y un solpa para darle diferentes status y evitar el validado
        $chk_contrato_pago = DB::Table('contratos AS c')->Select('c.id_contrato','p.id AS id_pago')
            ->LeftJoin('pagos AS p','p.id_contrato','c.id_contrato')
            ->Where('c.id_folios',$folio->id_folios)
            ->First();
        if(isset($chk_contrato_pago->id_contrato) && !is_null($chk_contrato_pago->id_contrato)) {
            if(!is_null($chk_contrato_pago->id_pago)) {
                $folio->status = 'Pago_Verificado';
            } else {
                $folio->status = 'Capturando';
            }
        } else {
            $folio->status = 'Validado';
        }

        $folio->save();

        $id = $request->id;
        $idb64 = base64_encode($id);
        $directorio_id = $request->directorio_id;

        // Notificacion!
        $letter = [
            'titulo' => 'Suficiencia Presupuestal Validada',
            'cuerpo' => 'La suficicencia presupuestal ' . $supre->no_memo . ' ha sido validada',
            'memo' => $supre->no_memo,
            'unidad' => $supre->unidad_capacitacion,
            'url' => '/supre/validacion/pdf/' . $supre->id,
        ];

        return redirect()->route('valsupre-mod', ['id' => base64_encode($id)])
                             ->with('success', 'Validación de Suficiencia Presupuestal Guardada Exitosamente');
        // return view('layouts.pages.valsuprecheck', compact('id', 'directorio_id','idb64'));
    }

    public function valsupre_mod($id){
        $id = base64_decode($id);
        $data = supre::find($id);
        $generarEfirmaValsupre = TRUE;
        $data->folio_validacion = explode('/', $data->folio_validacion);

        $clave = DB::Table('tabla_supre')
            ->Join('folios','folios.id_supre','tabla_supre.id')
            ->join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Where('tabla_supre.id',$id)
            ->Value('tbl_cursos.clave');

        $criterio_pago = DB::TABLE('criterio_pago')
            ->SELECT('cp','perfil_profesional')
            ->JOIN('tbl_cursos','tbl_cursos.cp','criterio_pago.id')
            ->JOIN('folios','folios.id_cursos','tbl_cursos.id')
            ->WHERE('folios.id_supre', $data->id)
            ->FIRST();

        if($criterio_pago == null) {
            $criterio_pago = DB::TABLE('criterio_pago')->SELECT('id AS cp','perfil_profesional')->WHERE('id','11')->FIRST();
        }

        // $delegado = DB::TABLE('tbl_unidades')->SELECT('delegado_administrativo','pdelegado_administrativo')->WHERE('unidad',$data->unidad_capacitacion)->FIRST();
        $funcionarios = $this->funcionarios_valsupre($data->unidad_capacitacion);

        $status_doc = DB::Table('documentos_firmar')->Where('numero_o_clave',$clave)
            ->Where('tipo_archivo','valsupre')
            ->Get();

        foreach($status_doc as $mxs) {
            if(!is_null($mxs)) {
                if(in_array($mxs->status, ['CANCELADO ICTI','VALIDADO'])) {
                    $generarEfirmaValsupre = FALSE;
                } elseif($mxs->status == 'EnFirma') {
                    $firmantes = json_decode($mxs->obj_documento, true);
                    foreach($firmantes['firmantes']['firmante']['0'] as $firmante) {
                        if(isset($firmante['_attributes']['certificado'])) {
                            $generarEfirmaValsupre = FALSE;
                        }
                    }
                }
            }
        }

        return view('layouts.pages.valsupremod', compact('data','criterio_pago','funcionarios','generarEfirmaValsupre','clave'));
    }

    public function generar_valsupre_efirma(request $request) {
        // dd($request);

        $status_doc = DB::Table('documentos_firmar')->Where('numero_o_clave',$request->clave_curso)->Where('tipo_archivo','valsupre')->Get();
        if(!is_null($status_doc)) {
            foreach($status_doc as $mxs) {
                if(!is_null($mxs) && in_array($mxs->status, ['VALIDADO', 'EnFirma'])){
                    if(!is_null($mxs->uuid_sellado)) {
                        return redirect()->route('valsupre_mod', ['id' => base64_encode($request->ids)])
                                    ->with('error','Error: El documento ha sido sellado anteriormente (3)');
                    }
                    $firmantes = json_decode($mxs->obj_documento, true);
                    foreach($firmantes['firmantes']['firmante']['0'] as $firmante) {
                        if(isset($firmante['_attributes']['certificado'])) {
                            return redirect()->route('valsupre_mod', ['id' => base64_encode($request->ids)])
                                    ->with('error','Error: El documento esta en proceso de firmado (4)');
                        }
                    }
                }
            }
        }

        $valsupreController = new EValsupreController();
        $result = $valsupreController->generar_xml($request->ids);

        if(isset($result['error'])) {
            return redirect()->route('valsupre-mod', ['id' => base64_encode($request->ids)])
                             ->with('error','Hubo un error al generar el documento electronico, favor de avisar en el grupo de soporte técnico. ('.$result['error'].')');
        }

        return redirect()->route('valsupre-mod', ['id' => base64_encode($request->ids)])
                             ->with('success','Solicitud de Suficiencia Presupuestal Electronico Generado Exitosamente');
    }

    public function delete($id)
    {
        // supre_directorio::WHERE('id_supre', '=', $id)->DELETE();
        // folio::where('id_supre', '=', $id)->delete();
        // supre::where('id', '=', $id)->delete();
        $folio = folio::WHERE('id_supre','=',$id)->FIRST();
        $folio->status = 'Cancelado';
        $folio->save();
        $supre = supre::WHERE('id', '=', $id)->FIRST();
        $supre->status = 'Cancelado';
        $supre->save();

        return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Eliminada');
    }

    public function restartSupre($id)
    {
        // dd($id);
        $item = folio::SELECT('id_folios')->WHERE('id_supre', '=', $id)->First();

        DB::table("folios")->WHERE('id_folios', $item->id_folios)->update(['status' => 'Rechazado']);
        DB::table('tabla_supre')->WHERE('id', $id)->UPDATE(['status' => 'Rechazado', 'doc_validado' => '']);

        return redirect()->route('supre-inicio')
                    ->with('success','Solicitud y Validación de Suficiencia Presupuestal Reiniciada');
    }

    public function reporte_solicitados(Request $request)
    {
        //dd($request->all());
        $fecha_inicio = $request->fecha_inicio;
        $fecha_termino = $request->fecha_termino;
        $consulta1 = DB::table('tabla_supre')->SELECT('tbl_unidades.ubicacion',
            DB::raw('SUM(CASE WHEN tabla_supre.id != 0 THEN 1 ELSE 0 END) as supre_total'),
            DB::raw("SUM(CASE WHEN tabla_supre.status = 'En_Proceso' THEN 1 ELSE 0 END) as supre_proceso"),
            DB::raw("SUM(CASE WHEN tabla_supre.status = 'Validado' THEN 1 ELSE 0 END) as supre_validados"),
            DB::raw("SUM(CASE WHEN tabla_supre.status = 'Rechazado' THEN 1 ELSE 0 END) as supre_rechazados"),
            DB::raw("ARRAY(SELECT tabla_supre.fecha_rechazado FROM tabla_supre WHERE status = 'Rechazado'
                    AND tabla_supre.unidad_capacitacion = tbl_unidades.ubicacion) as supre_fecha_rechazo"),
            DB::raw("ARRAY(SELECT tabla_supre.observacion FROM tabla_supre WHERE status = 'Rechazado'
                    AND tabla_supre.unidad_capacitacion = tbl_unidades.ubicacion) as supre_observaciones"))
            ->join('tbl_unidades','tbl_unidades.unidad','=','tabla_supre.unidad_capacitacion');

        $consulta2 = DB::table('folios')->SELECT('tbl_unidades.ubicacion',
        DB::raw("SUM(CASE WHEN folios.status in ('Validando_Contrato','Contratado','Contrato_Rechazado') THEN 1
                ELSE 0 END) as contrato_total"),
        DB::raw("SUM(CASE WHEN folios.status = 'Validando_Contrato' THEN 1 ELSE 0 END) as contrato_proceso"),
        DB::raw("SUM(CASE WHEN folios.status = 'Contratado' THEN 1 ELSE 0 END) as contrato_validados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Contrato_Rechazado' THEN 1 ELSE 0 END) as contrato_rechazados"),
        DB::raw("ARRAY(SELECT folios.fecha_rechazado FROM tabla_supre
                INNER JOIN folios ON folios.id_supre = tabla_supre.id
                WHERE folios.status = 'Contrato_Rechazado'
                AND tbl_unidades.ubicacion = tabla_supre.unidad_capacitacion) as contrato_fecha_rechazo"),
        DB::raw("ARRAY(SELECT contratos.observacion FROM folios
                INNER JOIN contratos ON contratos.id_folios = folios.id_folios
                WHERE folios.status = 'Contrato_Rechazado'
                AND contratos.unidad_capacitacion = tbl_unidades.ubicacion) as contrato_observaciones"),
        DB::raw("SUM(CASE WHEN folios.status in ('Verificando_Pago','Pago_Verificado','Finalizado','Pago_Rechazado')
                THEN 1 END) as pago_total"),
        DB::raw("SUM(CASE WHEN folios.status = 'Verificando_Pago' THEN 1 ELSE 0 END) as pago_proceso"),
        DB::raw("SUM(CASE WHEN folios.status = 'Pago_Verificado' THEN 1 ELSE 0 END) as pago_validados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Finalizado' THEN 1 ELSE 0 END) as pago_finalizados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Pago_Rechazado' THEN 1 ELSE 0 END) as pago_rechazados"),
        DB::raw("ARRAY(SELECT folios.fecha_rechazado FROM tabla_supre
                INNER JOIN folios ON folios.id_supre = tabla_supre.id
                WHERE folios.status = 'Pago_Rechazado'
                AND tbl_unidades.ubicacion = tabla_supre.unidad_capacitacion) as pago_fecha_rechazo"),
        DB::raw("ARRAY(SELECT pagos.observacion FROM folios
                INNER JOIN contratos ON contratos.id_folios = folios.id_folios
                INNER JOIN pagos ON pagos.id_contrato = contratos.id_contrato
                WHERE folios.status = 'Pago_Rechazado'
                AND contratos.unidad_capacitacion = tbl_unidades.ubicacion) as pago_observaciones"))
        ->join('tabla_supre','tabla_supre.id', '=', 'folios.id_supre')
        ->join('tbl_unidades','tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion');

        if(isset($fecha_inicio)){
            if(isset($fecha_termino)){
                $consulta1 = $consulta1->where('tabla_supre.created_at','>=',$fecha_inicio)
                                    ->where('tabla_supre.created_at','<=',$fecha_termino);

                $consulta2 = $consulta2->where('tabla_supre.created_at','>=',$fecha_inicio)
                                    ->where('tabla_supre.created_at','<=',$fecha_termino);
            }else{
                $fidefault = DB::table('tabla_supre')->SELECT('created_at')->WHERE('id', '!=', '0')
                                                    ->orderBy('id', 'asc')->FIRST();
                $ftdefault = DB::table('tabla_supre')->SELECT('created_at')->WHERE('id', '!=', '0')->LATEST();
                $fecha_inicio = $fidefault->created_at;
                $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d');
                $fecha_termino = $ftdefault->created_at;
                $fecha_termino = Carbon::parse($fecha_termino)->format('Y-m-d');
                    //dd($fidefault);
                return redirect()->route('reporte-solicitados')
                ->withErrors(sprintf('INGRESE UNA FECHA DE INICIO Y TERMINO'));
            }
        }
        else
        {
            $fidefault = DB::table('tabla_supre')->SELECT('created_at','id')->WHERE('id', '!=', '0')
                                                ->orderBy('id', 'asc')->FIRST();
            $ftdefault = DB::table('tabla_supre')->SELECT('created_at')->WHERE('id', '!=', '0')->LATEST()->FIRST();
            // dd($fidefault);
            $fecha_inicio = $fidefault->created_at;
            $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d');
            $fecha_termino = $ftdefault->created_at;
            $fecha_termino = Carbon::parse($fecha_termino)->format('Y-m-d');
            // dd($fecha_termino);
        }

        $consulta1 = $consulta1->orderBy('tbl_unidades.ubicacion','asc')->groupBy('tbl_unidades.ubicacion')->GET();
        $consulta2 = $consulta2->orderBy('tbl_unidades.ubicacion','asc')->groupBy('tbl_unidades.ubicacion')->GET();

        $unidades = DB::table('tbl_unidades')->SELECT('ubicacion as unidad')->groupBy('ubicacion')
            ->orderBy('ubicacion','asc')->GET();
        // dd($consulta1);
        return view('layouts.pages.vstareportesolicitados',compact('consulta1','consulta2','fecha_inicio','fecha_termino','unidades','fecha_inicio','fecha_termino'));
    }

    public function reporte_solicitados_detail($un, $ini, $fin)
    {
        //dd($fin);
        $consulta1 = DB::table('tabla_supre')->SELECT('tbl_unidades.ubicacion',
        DB::raw('SUM(CASE WHEN tabla_supre.id != 0 THEN 1 ELSE 0 END) as supre_total'),
        DB::raw("SUM(CASE WHEN tabla_supre.status = 'En_Proceso' THEN 1 ELSE 0 END) as supre_proceso"),
        DB::raw("SUM(CASE WHEN tabla_supre.status = 'Validado' THEN 1 ELSE 0 END) as supre_validados"),
        DB::raw("SUM(CASE WHEN tabla_supre.status = 'Rechazado' THEN 1 ELSE 0 END) as supre_rechazados"),
        DB::raw("ARRAY(SELECT tabla_supre.fecha_rechazado FROM tabla_supre WHERE status = 'Rechazado'
                AND tabla_supre.unidad_capacitacion = tbl_unidades.ubicacion) as supre_fecha_rechazo"),
        DB::raw("ARRAY(SELECT tabla_supre.observacion FROM tabla_supre WHERE status = 'Rechazado'
                AND tabla_supre.unidad_capacitacion = tbl_unidades.ubicacion) as supre_observaciones"))
        ->WHERE('tbl_unidades.ubicacion', '=', $un)
        ->join('tbl_unidades','tbl_unidades.unidad','=','tabla_supre.unidad_capacitacion');

        $consulta2 = DB::table('folios')->SELECT('tbl_unidades.ubicacion',
        DB::raw("SUM(CASE WHEN folios.status in ('Validando_Contrato','Contratado','Contrato_Rechazado') THEN 1
                ELSE 0 END) as contrato_total"),
        DB::raw("SUM(CASE WHEN folios.status = 'Validando_Contrato' THEN 1 ELSE 0 END) as contrato_proceso"),
        DB::raw("SUM(CASE WHEN folios.status = 'Contratado' THEN 1 ELSE 0 END) as contrato_validados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Contrato_Rechazado' THEN 1 ELSE 0 END) as contrato_rechazados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Verificando_Pago' THEN 1 ELSE 0 END) as pago_proceso"),
        DB::raw("SUM(CASE WHEN folios.status = 'Pago_Verificado' THEN 1 ELSE 0 END) as pago_validados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Finalizado' THEN 1 ELSE 0 END) as pago_finalizados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Pago_Rechazado' THEN 1 ELSE 0 END) as pago_rechazados"))
        ->WHERE('tbl_unidades.ubicacion', '=', $un)
        ->join('tabla_supre','tabla_supre.id', '=', 'folios.id_supre')
        ->join('tbl_unidades','tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion');


        $cadwell = DB::table('tabla_supre')->SELECT('tabla_supre.unidad_capacitacion','tabla_supre.no_memo',
            'tabla_supre.folio_validacion', 'tabla_supre.observacion', 'tabla_supre.created_at',
            'tabla_supre.updated_at', 'tabla_supre.status')
            ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
            ->whereRaw("tabla_supre.status in ('Validado','En_Proceso','Rechazado')")
            ->WHERE('tbl_unidades.ubicacion', '=', $un);

        $cadwell2 = DB::table('tabla_supre')->SELECT('folios.status','folios.iva','folios.importe_total',
                    'contratos.updated_at','contratos.created_at', 'contratos.numero_contrato',
                    'contratos.observacion','contratos.fecha_status','contratos.chk_rechazado',
                    'contratos.fecha_rechazo','tbl_cursos.unidad','tbl_cursos.curso', 'tbl_cursos.nombre')
        ->JOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
        ->JOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
        ->JOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
        ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
        ->WHERE('tbl_unidades.ubicacion', '=', $un)
        ->whereRaw("folios.status in ('Validando_Contrato', 'Contratado', 'Contrato_Rechazado')");

        $cadwell3 = DB::table('tabla_supre')->SELECT('folios.status','folios.iva','folios.importe_total',
                        'pagos.observacion','pagos.updated_at','pagos.created_at','pagos.fecha_validado',
                        'pagos.fecha_rechazo','pagos.chk_rechazado','pagos.no_memo','pagos.liquido',
                        'tbl_cursos.unidad')
        ->JOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
        ->JOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
        ->JOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
        ->JOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
        ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
        ->WHERE('tbl_unidades.ubicacion', '=', $un)
        ->whereRaw("folios.status in ('Verificando_Pago', 'Pago_Verificado', 'Finalizado', 'Pago_Rechazado')");
        if($ini != 0 ||  $fin != 0)
        {
            // dd($fin);
            $consulta1 = $consulta1->where('tabla_supre.created_at','>=',$ini)
                                    ->where('tabla_supre.created_at','<=',$fin);


            $consulta2 = $consulta2->where('tabla_supre.created_at','>=',$ini)
                                    ->where('tabla_supre.created_at','<=',$fin);

            $cadwell = $cadwell->WHERE('tabla_supre.created_at', '>=', $ini)
                                ->WHERE('tabla_supre.created_at', '<=', $fin);

            $cadwell2 = $cadwell2->WHERE('tabla_supre.created_at', '>=', $ini)
                                ->WHERE('tabla_supre.created_at', '<=', $fin);

            $cadwell3 = $cadwell3->WHERE('tabla_supre.created_at', '>=', $ini)
                                ->WHERE('tabla_supre.created_at', '<=', $fin);

            $separa = explode("-",$ini);
            $ini = $separa[2] . ' DE ' .$this->monthToString($separa[1]) . ' ' . $separa[0];
            $separa = explode("-",$fin);
            $fin = $separa[2] . ' DE ' .$this->monthToString($separa[1]) . ' ' . $separa[0];
        }

        $consulta1 = $consulta1->groupBy('tbl_unidades.ubicacion')->FIRST();
        $consulta2 = $consulta2->groupBy('tbl_unidades.ubicacion')->FIRST();
        $cadwell = $cadwell->GET();
        $cadwell2 = $cadwell2->GET();
        $cadwell3 = $cadwell3->GET();
        // dd($cadwell);
        return view('layouts.pages.vstareportesolicitadosdetail',compact('consulta1','consulta2','ini','fin','un','cadwell','cadwell2', 'cadwell3'));
    }

    public function cancelFolio(Request $request)
    {
        $userName = Auth::user()->name;
        $folio = folio::find($request->idf);
        $folio->observacion_cancelacion = $request->observaciones;
        $folio->cancelo = $userName;
        $folio->status = 'Cancelado';
        $folio->save();

        return redirect()->route('supre-inicio')
                    ->with('success','Folio de Suficiencia Presupuestal Cancelada');
    }

    protected function getcursostats(Request $request)
    {
        $criterio_fecha = date('Y-m-d', strtotime('12-10-2023'));
        if (isset($request->valor)){
            $total=[];
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $claveCurso = $request->valor;//$request->valor;
            $Curso = new tbl_curso();
            $Cursos = $Curso->SELECT('tbl_cursos.ze','tbl_cursos.cp','tbl_cursos.dura',
                    'tbl_cursos.modinstructor', 'tbl_cursos.tipo_curso',
                    'tbl_cursos.folio_pago','movimiento_bancario','fecha_movimiento_bancario',
                    'factura','fecha_factura','tbl_cursos.fecha_apertura AS inicio','tbl_cursos.id')
                                    ->WHERE('clave', '=', $claveCurso)->FIRST();

            if($Cursos != NULL)
            {
                // $inicio = carbon::parse($Cursos->inicio);
                $inicio = date('Y-m-d', strtotime($Cursos->inicio));

                if($inicio < date('Y-m-d', strtotime('12-10-2023')) && $Cursos->cp > 5) {
                    $Cursos->cp = $Cursos->cp - 1;
                } else if ($inicio < date('Y-m-d', strtotime('12-10-2023')) && $Cursos->cp == 5) {
                    $Cursos->cp = 55; // este id es del antiguo C.P. 5
                }

                if ($Cursos->ze == 'II')
                {
                    $queryraw = "jsonb_array_elements(ze2->'vigencias') AS vigencia";
                }
                else
                {
                    $queryraw = "jsonb_array_elements(ze3->'vigencias') AS vigencia";
                }

                $criterio = DB::table('criterio_pago')->select('fecha', 'monto')
                    ->fromSub(function ($query) use ($Cursos, $inicio, $queryraw) {
                        $query->selectRaw("(vigencia->>'fecha')::date AS fecha, (vigencia->>'monto')::numeric AS monto")
                            ->from('criterio_pago')
                            ->crossJoin(DB::raw($queryraw))
                            ->where('id', $Cursos->cp)
                            ->whereRaw("(vigencia->>'fecha')::date <= ?", [$inicio]);
                    }, 'sub')
                    ->orderBy('fecha', 'DESC')
                    ->limit(1)
                    ->first();

                if($criterio != NULL)
                {
                    // if($inicio >= $criterio_fecha) {
                    //     $criterio->monto = ($criterio->monto / 1.16);
                    // }
                    if($Cursos->tipo_curso == 'CERTIFICACION')
                    {
                        array_push($total, $criterio->monto * 10);
                        array_push($total, $Cursos->modinstructor);
                        //$aviso = TRUE;
                    }
                    else
                    {
                            array_push($total, $criterio->monto * $Cursos->dura);
                            array_push($total, $Cursos->modinstructor);
                    }
                }
                else
                {
                    $total = 'N/A';
                }
            }
            else
            {
                $total = 'N/A';
            }
            $total['recibo'] = $Cursos->folio_pago;
            $total['movimiento_bancario'] = $Cursos->movimiento_bancario;
            $total['fecha_movimiento_bancario'] = $Cursos->fecha_movimiento_bancario;
            $total['factura'] = $Cursos->factura;
            $total['fecha_factura'] = $Cursos->fecha_factura;

            if($Cursos->modinstructor == 'HONORARIOS')
            {
                if($inicio >= $criterio_fecha) {
                    $total['iva'] = floatval(number_format($total[0] * 0.16, 2, '.', ''));
                    $total['importe_total'] = floatval(number_format($total[0], 2, '.', ''));
                    $total['tabuladorConIva'] = TRUE;
                } else {
                    $total['iva'] = floatval(number_format($total[0] * 0.16, 2, '.', ''));
                    $total['importe_total'] = floatval(number_format($total[0] + $total['iva'], 2, '.', ''));
                }
            }
            else
            {
                $total['iva'] = 0.00;
                $total['importe_total'] = $total[0];
            }

            // obtener recibo
            $recibo = DB::Table('tbl_recibos')->Select('fecha_expedicion','folio_recibo')
                ->Where('id_concepto',1)
                ->Where('id_curso',$Cursos->id)
                ->First();

            if($recibo == null) {
                $recibo = DB::Table('tbl_cursos')->Select('fecha_pago AS fecha_expedicion','folio_pago AS folio_recibo')
                    ->Where('id',$Cursos->id)
                    ->First();
            }

            $total['fecha_expedicion'] = $recibo->fecha_expedicion;
            $total['folio_recibo'] = $recibo->folio_recibo;


            $json=json_encode($total); //dura 10 cp 6
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        // dd($Cursos->inicio);

        return $json;
    }

    protected function gettipocurso(Request $request)
    {
        $claveCurso = $request->valor;
        $Curso = new tbl_curso();
        $Cursos = $Curso->SELECT('tbl_cursos.tipo_curso')
                                ->WHERE('clave', '=', $claveCurso)->FIRST();

        if($Cursos != NULL)
        {
            switch ($Cursos->tipo_curso) {
                case 'CERTIFICACION':
                    $tipo = 'CERT';
                break;
                case 'NORMAL':
                    $tipo = 'NORMAL';
                break;
                default:
                    $tipo = 'ERROR';
                break;
            }
        }
        else
        {
            $tipo = 'ERROR';
        }

        $json=json_encode($tipo);
        return $json;
    }


    protected function getfoliostats(Request $request)
    {
        if (isset($request->valor))
        {
            $folio = folio::WHERE('folio_validacion', '=', $request->valor)->FIRST();
            if($folio == NULL)
            {
                $folio = 'N/A';
            }
        }
        else
        {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }
            $json=json_encode($folio);


        return $json;
    }

    protected function getfoliostatsmodal(Request $request)
    {
        if (isset($request->valor))
        {
            $folio = folio::SELECT('id_folios', 'folio_validacion')
            ->WHERE('id_supre', '=', $request->valor)
            ->GET();
            if($folio == NULL)
            {
                $folio = 'N/A';
            }
        }
        else
        {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }
            $json=json_encode($folio);


        return $json;
    }

    public function dar_permiso_folio(Request $request)
    {
        $folio = folio::find($request->folios);
        $folio->permiso_editar = TRUE;
        $folio->save();

        return redirect()->route('supre-inicio')
                    ->with('success','Permiso Otorgado');
    }
    public function dar_permiso_valsupre($id)
    {
        $supre = supre::find($id);
        $supre->permiso_editar = TRUE;
        $supre->save();

        return redirect()->route('supre-inicio')
                    ->with('success','Permiso Otorgado');
    }


    public function doc_valsupre_upload(Request $request)
    {
        if ($request->hasFile('doc_validado')) {

            if($request->idinsmod != NULL)
            {
                $supre = supre::find($request->idinsmod);
                $doc = $request->file('doc_validado'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $request->idinsmod, 'valsupre_firmado'); # invocamos el método
                $supre->doc_validado = $urldoc; # guardamos el path
            }
            else
            {
                $supre = supre::find($request->idinsmod2);
                $doc = $request->file('doc_validado'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $request->idinsmod2, 'valsupre_firmado'); # invocamos el método
                $supre->doc_validado = $urldoc; # guardamos el path
            }

            $supre->save();
            return redirect()->route('supre-inicio')
                    ->with('success','Validación de Suficiencia Presupuestal Firmada ha sido cargada con Extio');
        }
    }

    public function doc_supre_upload(Request $request)
    {
        // dd($request);
        if ($request->hasFile('doc_supre'))
        {

            if($request->idsupmod != NULL)
            {
                $supre = supre::find($request->idsupmod);
                $doc = $request->file('doc_supre'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $request->idsupmod, 'supre_firmado'); # invocamos el método
                $supre->doc_supre = $urldoc; # guardamos el path
            }
            else
            {
                $supre = supre::find($request->idsupmod2);
                $doc = $request->file('doc_supre'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $request->idsupmod2, 'supre_firmado'); # invocamos el método
                $supre->doc_supre = $urldoc; # guardamos el path

                $folio = folio::WHERE('id_supre', '=', $request->idsupmod2)->FIRST();
                $folio->permiso_editar = FALSE;
                $folio->save();
            }

            $supre->save();

            return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Firmada ha sido cargada con Extio');
        }
    }

    public function cancelados_reporte()
    {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstareportecancelados', compact('unidades'));
    }

    public function planeacion_reporte()
    {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstareporteplaneacion', compact('unidades'));
    }

    public function reporte_costeo_supre()
    {
        return view('layouts.pages.vstareportecosteoplaneacion');
    }

    public function folio_edicion_especial($id)
    {
        // $id = base64_decode($id);
        $getdestino = null;
        $getremitente = null;
        $getvalida = null;
        $getelabora = null;
        $getccp1 = null;
        $getccp2 = null;

        $folio = folio::WHERE('id_folios', '=', $id)->FIRST();
        $supre = supre::WHERE('id', '=', $folio->id_supre)->FIRST();
        $clave = tbl_curso::SELECT('clave')->WHERE('id', '=', $folio->id_cursos)->FIRST();

        $directorio = supre_directorio::WHERE('id_supre', '=', $supre->id)->FIRST();
        $unidadsel = tbl_unidades::SELECT('unidad')->WHERE('unidad', '=', $supre->unidad_capacitacion)->FIRST();
        $unidadlist = tbl_unidades::SELECT('unidad')->WHERE('unidad', '!=', $supre->unidad_capacitacion)->GET();

        $getdestino = directorio::WHERE('id', '=', $directorio->supre_dest)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getvalida = directorio::WHERE('id', '=', $directorio->supre_valida)->FIRST();
        $getelabora = directorio::WHERE('id', '=', $directorio->supre_elabora)->FIRST();
        $getccp1 = directorio::WHERE('id', '=', $directorio->supre_ccp1)->FIRST();
        $getccp2 = directorio::WHERE('id', '=', $directorio->supre_ccp2)->FIRST();


        return view('layouts.pages.modespecialfolio',compact('supre','folio','clave','getdestino','getremitente','getvalida','getelabora','getccp1','getccp2','directorio', 'unidadsel','unidadlist'));
    }

    public function folio_edicion_especial_save(Request $request)
    {
        //dd($request->id_folio);
        //dd($request);
        $curso_validado = new tbl_curso();
        $clave = $request->addmore[0]['clavecurso'];
        $hora = $curso_validado->SELECT('tbl_cursos.dura','tbl_cursos.id')
                ->WHERE('tbl_cursos.clave', '=', $clave)
                ->FIRST();
        $importe = $request->addmore[0]['importe']/1.16;
        $importe_hora = $importe / $hora->dura;

        supre::where('id', '=', $request->id_supre)
        ->update(['unidad_capacitacion' => $request->unidad,
                  'no_memo' => $request->no_memo,
                  'fecha' => $request->fecha,
                  'fecha_status' => carbon::now()]);

        supre_directorio::where('id', '=', $request->id_directorio)
        ->update([//'supre_dest' => $request->id_destino,
                  'supre_rem' => $request->id_remitente,
                  'supre_valida' => $request->id_valida,
                  'supre_elabora' => $request->id_elabora,
                  //'supre_ccp1' => $request->id_ccp1,
                  //'supre_ccp2' => $request->id_ccp2,
                ]);

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['folio_validacion' => $request->addmore[0]['folio'],
                  'iva' => $request->addmore[0]['iva'],
                  'comentario' => $request->addmore[0]['comentario'],
                  'importe_hora' => $importe_hora,
                  'importe_total' => $request->addmore[0]['importe'],
                  'id_supre' => $request->id_supre,
                  'id_cursos' => $hora->id,
                //   'permiso_editar' => FALSE
                ]);

        $idc = DB::TABLE('contratos')->WHERE('id_folios', '=', $request->id_folio)->FIRST();
        // dd($idc);
        if($idc != NULL)
        {
            contratos::where('id_contrato', '=', $idc->id_contrato)
                  ->update(['cantidad_numero' => floatval(number_format($request->addmore[0]['importe']-$request->addmore[0]['iva'], 2, '.', ''))]);
        }

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function planeacion_reportepdf(Request $request)
    {

        $filtrotipo = (isset($request->filtro) ? $request->filtro: 0);
        $idcurso = (isset($request->id_curso) ? $request->id_curso : 0);
        $unidad = (empty($request->unidad) ? $request->unidad : 0);
        $idInstructor = (isset($request->id_instructor)? $request->id_instructor : 0);
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;

        # si el arreglo nos retorna un número mayor a cero
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();
        return view('layouts.pages.vstareporteplaneacion', compact('unidades', 'filtrotipo','idcurso','unidad','idInstructor','fecha1','fecha2'));

        // dd($data);
    }

    public function supre_pdf($id){
        $id = base64_decode($id);
        $uuid = $objeto = $qrCodeBase64 = $bodyCcp = null;
        $user_data = DB::Table('users')->Select('ubicacion','role_user.role_id')
            ->Join('tbl_unidades','tbl_unidades.id','users.unidad')
            ->Join('role_user','role_user.user_id','users.id')
            ->Where('users.id', Auth::user()->id)
            ->First();
        $supre = new supre();
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $data_supre = $supre::WHERE('id', '=', $id)->FIRST(); //cambiar data2 a data_supre en tabla supre
        $unidad = tbl_unidades::SELECT('tbl_unidades.unidad', 'tbl_unidades.cct','tbl_unidades.ubicacion','direccion')
            ->WHERE('unidad', '=', $data_supre->unidad_capacitacion)
            ->FIRST();
        $unidad->cct = substr($unidad->cct, 0, 4);
        $funcionarios = $this->funcionarios_supre($data_supre->unidad_capacitacion);
        $direccion = explode("*", $unidad->direccion);
        $puestos = array();

        //validacion de unidad del usuario y el contrato. con esto evitamos que lo vea cualquier usuario fuera de la unidad correcta
        if($user_data->ubicacion != $unidad->ubicacion && !in_array($user_data->role_id, ['1','4','9','10'])) {
            return redirect()->route('supre-inicio')->with('warning','Acceso denegado para visualizar esta Suficiencia Presupuestal.');
        }
        //fin

        //body en firma electronica
        $clave = DB::table('folios')->Where('folios.id_supre',$id)
            ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Value('tbl_cursos.clave');
        $documento = DocumentosFirmar::where('numero_o_clave', $clave)
            ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
            ->Where('tipo_archivo','supre')
            ->first();

        if(is_null($documento)) {
            $firma_electronica = false;
            $supreController = new ESupreController();
            $body_html = $supreController->create_body($id);
            $bodySupre = $body_html['supre'];
            $bodyCcp = $body_html['ccp'];
            $bodyTabla = $body_html['tabla'];
        } else {
            $firma_electronica = true;
            $body_html = json_decode($documento->obj_documento_interno);
            $bodySupre = $body_html->supre;
            $bodyTabla = $body_html->tabla;
            $bodyCcp = $body_html->ccp;
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
        // $pdf1 = PDF::loadView('layouts.pdfpages.presupuestaria',compact('data_supre','bodySupre','funcionarios','unidad','distintivo','direccion','firma_electronica','uuid'));
        // $pdf2 = PDF::loadView('layouts.pdfpages.solicitudsuficiencia', compact('funcionarios','distintivo','direccion','bodyTabla','firma_electronica','uuid'))->setPaper('a4', 'landscape');
        // return $pdf2->stream("prueba.pdf");

        $pdf1 = PDF::loadView('layouts.pdfpages.presupuestaria',compact('data_supre','bodySupre','bodyCcp','funcionarios','unidad','distintivo','direccion','firma_electronica','uuid','objeto','puestos','qrCodeBase64'))->output();
        $pdf2 = PDF::loadView('layouts.pdfpages.solicitudsuficiencia', compact('funcionarios','distintivo','direccion','bodyTabla','firma_electronica','uuid','objeto','puestos','qrCodeBase64'))
            ->setPaper('a4', 'landscape')  // Configurar tamaño y orientación
            ->output();

        // Combinar los PDFs usando FPDI
        // Crear un archivo temporal para cada PDF
        $file1 = tempnam(sys_get_temp_dir(), 'pdf1');
        $file2 = tempnam(sys_get_temp_dir(), 'pdf2');

        // Escribir los datos del PDF en los archivos temporales
        file_put_contents($file1, $pdf1);
        file_put_contents($file2, $pdf2);

        // Combinar los PDFs usando FPDI
        $pdf = new Fpdi();
        $pdf->AddPage();
        $pageCount1 = $pdf->setSourceFile($file1);
        $tplIdx1 = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx1);

        $pdf->AddPage('L');
        $pageCount2 = $pdf->setSourceFile($file2);
        $tplIdx2 = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx2);

        // Eliminar los archivos temporales
        unlink($file1);
        unlink($file2);

        return $pdf->Output('medium.pdf', 'I');
    }

    protected function planeacion_reporte_canceladospdf(Request $request){
        $i = 0;
        set_time_limit(0);
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();

        if ($request->filtro == "general")
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario','folios.cancelo',
                           'folios.observacion_cancelacion','folios.updated_at','instructores.nombre',
                           'instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.curso',
                           'tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre','tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $request->fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $request->fecha2)
                           ->WHERE('folios.status', '=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($request->filtro == 'unidad')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario','folios.cancelo',
                           'folios.observacion_cancelacion','instructores.nombre','instructores.apellidoPaterno',
                           'instructores.apellidoMaterno','tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze',
                           'tbl_cursos.dura','tbl_cursos.hombre','tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $request->fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $request->fecha2)
                           ->WHERE('tabla_supre.unidad_capacitacion', '=', $request->unidad)
                           ->WHERE('folios.status', '=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }

        $pdf = PDF::loadView('layouts.pdfpages.reportefolioscancelados', compact('data','distintivo'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Download('formato de control '. $request->fecha1 . ' - '. $request->fecha2 .'.pdf');
        return view('layouts.pdfpages.reportefolioscancelados', compact('data'));
    }

    public function valsupre_pdf($id){
        // dd($id);
        $puestos = array();
        $uuid = $objeto = $qrCodeBase64 = NULL;
        $user_data = DB::Table('users')->Select('ubicacion','role_user.role_id')
        ->Join('tbl_unidades','tbl_unidades.id','users.unidad')
        ->Join('role_user','role_user.user_id','users.id')
        ->Where('users.id', Auth::user()->id)
        ->First();
        $firma_electronica = True;
        $id = base64_decode($id);
        $data2 = supre::WHERE('id', '=', $id)->FIRST();

        //validacion de unidad del usuario y el contrato. con esto evitamos que lo vea cualquier usuario fuera de la unidad correcta
        if($user_data->ubicacion != $data2->unidad_capacitacion && !in_array($user_data->role_id, ['1','4','9','10'])) {
            return redirect()->route('supre-inicio')->with('warning','Acceso denegado para visualizar esta Suficiencia Presupuestal.');
        }
        //fin

        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $funcionarios = $this->funcionarios_valsupre($data2->unidad_capacitacion);

         //body en firma electronica
        $clave = DB::table('folios')->Where('folios.id_supre',$id)
            ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Value('tbl_cursos.clave');
        $documento = DocumentosFirmar::where('numero_o_clave', $clave)
            ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
            ->Where('tipo_archivo','valsupre')
            ->first();

        if(is_null($documento)) { //cambiarlo al final de pruebas
            $firma_electronica = false;
            $supreController = new EValsupreController();
            $array_html = $supreController->create_body($id);
            $body_html = $array_html['body'];
            $ccp_html = $array_html['ccp'];
        } else {
            $firma_electronica = true;
            $array_html = json_decode($documento->obj_documento_interno);
            $body_html = $array_html->body;
            $ccp_html = $array_html->ccp;
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

        $pdf = PDF::loadView('layouts.pdfpages.valsupre', compact('distintivo','funcionarios','body_html','ccp_html','uuid','objeto','puestos','qrCodeBase64'));
        $pdf->setPaper('A4', 'Landscape');
        return $pdf->stream('medium.pdf');
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

    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/supre/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/supre/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    public function get_curso(Request $request){

        $search = $request->search;

        if (isset($search)) {
            # si la variable está inicializada
            if($search == ''){
                $curso = tbl_curso::orderby('curso','asc')->select('id','curso')->limit(5)->get();
            }else{
                $curso = tbl_curso::orderby('curso','asc')->select('id','curso')->where('curso', 'like', '%' .$search . '%')->limit(5)->get();
            }

            $response = array();
            foreach($curso as $dir){
                $response[] = array("value"=>$dir->id,"label"=>$dir->curso);
            }

            echo json_encode($response);
            exit;
        }
    }

    public function get_ins(Request $request){

        $search = $request->search;

        if (isset($search)) {
            # si la variable está inicializada
            if($search == ''){
                $instructor = instructor::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno')->limit(10)->get();
            }else{
                $instructor = instructor::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno')->where('nombre', 'like', '%' .$search . '%')->limit(10)->get();
            }

            $response = array();
            foreach($instructor as $dir){
                $response[] = array("value"=>$dir->id,"label"=>$dir->nombre . " " .$dir->apellidoPaterno . " " . $dir->apellidoMaterno);
            }

            echo json_encode($response);
            exit;
        }
    }

    protected function numberFormat($numero)
    {
        $part = explode(".", $numero);
        $part[0] = floatval(number_format($part['0']));
        $cadwell = implode(".", $part);
        return ($cadwell);
    }

    /**
     * agregar métodos - generate_report_supre_pdf -
     */
    protected function generate_report_supre_pdf($filtrotipo, $idcurso, $unidad, $idInstructor, $fecha1, $fecha2){
        $i = 0;
        set_time_limit(0);
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();

        if ($filtrotipo == "general")
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer', 'tbl_cursos.tipo_curso')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'curso')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer', 'tbl_cursos.tipo_curso')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->WHERE('tbl_cursos.id', '=', $idcurso)
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'unidad')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer', 'tbl_cursos.tipo_curso')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'instructor')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer', 'tbl_cursos.tipo_curso')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('instructores.id', '=', $idInstructor)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }


        foreach($data as $cadwell)
        {
            $risr[$i] = $this->numberFormat( floatval(number_format($cadwell->importe_total * 0.10, 2, '.', '')));
            $riva[$i] = $this->numberFormat( floatval(number_format($cadwell->importe_total * 0.1066, 2, '.', '')));

            $iva[$i] = $this->numberFormat( floatval($cadwell->iva));
            $cantidad[$i] = $this->numberFormat( floatval($cadwell->importe_total));

            $hm = $cadwell->hombre+$cadwell->mujer;
            if ($hm < 10)
            {
                $recursos[$i] = "Estatal";
            }
            else
            {
                $recursos[$i] = "Federal";
            }
            $i++;
        }

        $pdf = PDF::loadView('layouts.pdfpages.reportesupres', compact('data','recursos','risr','riva','cantidad','iva','distintivo'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Download('formato de control '. $fecha1 . ' - '. $fecha2 .'.pdf');
    }

    /**
     *
     */

    public function planeacion_costeo_excel(Request $request)
    {
        // dd($request);

        $data = DB::TABLE('tbl_cursos')
        ->SELECT(
        'tbl_cursos.unidad',
        'tbl_cursos.curso',
        'tbl_cursos.clave',
        'tbl_cursos.nombre',
        'tbl_cursos.fecha_apertura',
        'tbl_cursos.ze',
        'tbl_cursos.dura',
        'tbl_cursos.muni',
        'tbl_localidades.localidad',
        'tbl_cursos.cp',
        'tbl_cursos.inicio')
        ->WhereNotIn('tbl_cursos.id', DB::Table('folios')->JOIN('tbl_cursos','tbl_cursos.id','=','folios.id_cursos')->WHERE('folios.status','!=','Rechazado')->pluck('folios.id_cursos'))
        ->whereDate('tbl_cursos.inicio', '>=', $request->fecha1)
        ->whereDate('tbl_cursos.inicio', '<=', $request->fecha2)
        ->WHERE('status_curso','AUTORIZADO')
        ->WHERE('tbl_cursos.clave','!=','0')
        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
        ->LEFTJOIN('tbl_localidades', 'tbl_localidades.clave', '=', 'tbl_cursos.clave_localidad')
        ->GROUPBY('tbl_cursos.unidad',
        'tbl_cursos.curso',
        'tbl_cursos.clave',
        'tbl_cursos.nombre',
        'tbl_cursos.fecha_apertura',
        'tbl_cursos.ze',
        'tbl_cursos.dura',
        'tbl_cursos.muni',
        'tbl_localidades.localidad',
        'tbl_cursos.cp',
        'tbl_cursos.inicio')
        ->ORDERBY('fecha_apertura', 'ASC')
        ->GET();

        foreach($data as $key => $cadwell)
        {
            // dd($cadwell);
            $cp = DB::TABLE('criterio_pago')->WHERE('id',$cadwell->cp)->FIRST();
            if($cadwell->ze == 'II')
            {
                $point = 'ze2_';
            }
            else
            {
                $point = 'ze3_';
            }

            if($cadwell->inicio < '01-11-2022')
            {

                $point = $point.(carbon::now()->year - 1);
                $data[$key]->importe_hora = $cp->$point;
            }
            else
            {
                $point = $point.'2022';
                // $point = $point.carbon::now()->year;dd($cp);
                $data[$key]->importe_hora = $cp->$point;
            }
            $data[$key]->importe_total = floatval(number_format($cadwell->dura * $cp->$point, 2, '.', ''));
            $data[$key]->iva = floatval(number_format($data[$key]->importe_total * 0.16, 2, '.', ''));
            $data[$key]->isr = floatval(number_format($data[$key]->importe_total * 0.10, 2, '.', ''));
            unset($data[$key]->inicio);
            unset($data[$key]->cp);
        }

        $cabecera = [
            'UNIDAD/A.M DE CAP.', 'CURSO', 'CLAVE DEL GRUPO', 'INSTRUCTOR', 'FECHA DE APERTURA', 'Z.E.',
            'HSM', 'MUNICIPIO','LOCALIDAD', 'IMPORTE POR HORA', 'IMPORTE TOTAL', 'IVA 16%',
            'RETENCIÓN IVA', 'RETENCIÓN ISR'
        ];

        $nombreLayout = "formato de costeo".$request->fecha1 . ' - '. $request->fecha2 . " creado el " . carbon::now() . ".xlsx";
        $titulo = "formato de costeo ".$request->fecha1 . ' - '. $request->fecha2 . " creado el " . carbon::now();
        if(count($data)>0)
        {
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    public function honorarios($importe)
    {
        $impuestos = array();
        $impuestos['regimen'] = 'HONORARIOS';
        $impuestos['IVA'] = floatval(number_format($importe * 0.16, 2, '.', ''));
        $impuestos['subtotal'] = floatval(number_format($importe + $impuestos['IVA'], 2, '.', ''));
        $impuestos['retencion_isr'] = floatval(number_format($importe * 0.1, 2, '.', ''));
        $impuestos['retencion_iva'] = floatval(number_format($impuestos['IVA']/3*2, 2, '.', ''));
        $impuestos['importe_neto'] = floatval(number_format($impuestos['subtotal']-$impuestos['retencion_isr']-$impuestos['retencion_iva'], 2, '.', ''));
        return $impuestos;
    }

    public function asimilados($importe)
    {
        $impuestos = array();
        $impuestos['regimen'] = 'ASIMILADOS A SALARIO';
        $impuestos['IVA'] = floatval(number_format($importe * 0.16, 2, '.', ''));
        $impuestos['subtotal'] = floatval(number_format($importe + $impuestos['IVA'], 2, '.', ''));
        $impuestos['limite_inferior'] = $this->isr_finder($impuestos['subtotal'], '1');
        $impuestos['excedente'] = floatval(number_format($impuestos['subtotal'] - $impuestos['limite_inferior'], 2, '.', ''));
        $isr_info = $this->isr_finder($impuestos['excedente'], '2');
        $impuestos['tasa_impuesto'] = $isr_info->porcentaje;
        $impuestos['impuesto_marginal'] = floatval(number_format($impuestos['excedente'] * ($impuestos['tasa_impuesto'] / 100), 2, '.', ''));
        $impuestos['cuota_fija'] = $isr_info->cuota_fija;
        $impuestos['isr_determinado'] = floatval(number_format($impuestos['impuesto_marginal'] + $impuestos['cuota_fija'], 2, '.', ''));
        $impuestos['ingreso_neto'] = floatval(number_format($impuestos['subtotal'] - $impuestos['isr_determinado'], 2, '.', ''));
        return $impuestos;
    }

    public function funcionarios_supre($unidad) {
        $query = clone $direc = clone $ccp1 = clone $ccp2 = clone $delegado = clone $destino = DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo','f.incapacidad')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true')
            ->Where('f.titular', true);

        $direc = $direc->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $destino = $destino->Where('o.id',9)->First();
        $ccp1 = $ccp1->Where('o.id',6)->First();
        $ccp2 = $ccp2->Where('o.id',13)->First();
        $delegado = $delegado->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $unidad)
            ->First();

        //parte de checado de incapacidad
        $direc = $this->incapacidad(json_decode($direc->incapacidad), $direc->nombre) ?: $direc;
        $delegado = $this->incapacidad(json_decode($delegado->incapacidad), $delegado->nombre) ?: $delegado;

        $funcionarios = [
            'director' => $direc->nombre,
            'directorp' => $direc->cargo,
            'destino' => $destino->nombre,
            'destinop' => $destino->cargo,
            'ccp1' => $ccp1->nombre,
            'ccp1p' => $ccp1->cargo,
            'ccp2' => $ccp2->nombre,
            'ccp2p' => $ccp2->cargo,
            'delegado' => $delegado->nombre,
            'delegadop' => $delegado->cargo,
            'elabora' => strtoupper(Auth::user()->name),
            'elaborap' => strtoupper(Auth::user()->puesto)
        ];

        return $funcionarios;
    }

    public function funcionarios_valsupre($unidad) {
        $query = clone $direc = clone $ccp1 = clone $ccp2 = clone $ccp3 = clone $delegado = clone $remitente = DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo','f.incapacidad')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true')
            ->Where('f.titular', true);

        $direc = $direc->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $remitente = $remitente->Where('o.id',9)->First();
        $ccp1 = $ccp1->Where('o.id',1)->First();
        $ccp2 = $ccp2->Where('o.id',6)->First();
        $ccp3 = $ccp3->Where('o.id',13)->First();
        $delegado = $delegado->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $unidad)
            ->First();

        //parte de checado de incapacidad
        $direc = $this->incapacidad(json_decode($direc->incapacidad), $direc->nombre) ?: $direc;
        $delegado = $this->incapacidad(json_decode($delegado->incapacidad), $delegado->nombre) ?: $delegado;

        $funcionarios = [
            'director' => $direc->nombre,
            'directorp' => $direc->cargo,
            'remitente' => $remitente->nombre,
            'remitentep' => $remitente->cargo,
            'ccp1' => $ccp1->nombre,
            'ccp1p' => $ccp1->cargo,
            'ccp2' => $ccp2->nombre,
            'ccp2p' => $ccp2->cargo,
            'ccp3' => $ccp3->nombre,
            'ccp3p' => $ccp3->cargo,
            'delegado' => $delegado->nombre,
            'delegadop' => $delegado->cargo,
            'elabora' => strtoupper(Auth::user()->name),
            'elaborap' => strtoupper(Auth::user()->puesto)
        ];

        return $funcionarios;
    }

    public function isr_finder($importe, $consulta)
    {
        if($consulta == '1') //$consulta es la variable para saber si es la primera consulta del impuesto o el segundo
        {
            return ISR::WHERE('limite_inferior', '<=', $importe)->WHERE('limite_superior', '>=', $importe)->VALUE('limite_inferior');
        }
        else
        {
            return ISR::WHERE('limite_inferior', '<=', $importe)->WHERE('limite_superior', '>=', $importe)->FIRST();
        }
    }

    protected function generate_report_supre_xls($filtrotipo, $idcurso, $unidad, $idInstructor, $fecha1, $fecha2){
        $i = 0;
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        if ($filtrotipo == "general")
        {
            $data = supre::SELECT('tabla_supre.no_memo',
                    'folios.folio_validacion as suf',
                    'tabla_supre.created_at as prue',
                    'tabla_supre.fecha',
                    \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tbl_cursos.unidad',
                    \DB::raw("CASE WHEN tbl_cursos.tipo_curso = 'CURSO' THEN 'CURSO' ELSE 'CERTIFICACION EXTRAORDINARIA' END AS tipo_curso"),
                    'tbl_cursos.curso',
                    \DB::raw('tbl_cursos.hombre + tbl_cursos.mujer'),
                    'tbl_cursos.clave',
                    'tbl_cursos.ze',
                    'tbl_cursos.dura',
                    'tbl_cursos.muni',
                    'tbl_localidades.localidad',
                    // \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99') AS importe_hora"),
                    \DB::raw("CAST(folios.importe_hora AS DECIMAL(10, 2)) AS importe_hora"),

                    // \DB::raw("TO_CHAR(folios.iva, '999,999.99') AS importe_iva_16"),
                    \DB::raw("CAST(folios.iva AS DECIMAL(10, 2)) AS importe_iva_16"),

                    \DB::raw("'12101 Honorarios' AS partida_concepto"),

                    // \DB::raw("CASE WHEN tabla_supre.financiamiento = 'FEDERAL' OR tabla_supre.financiamiento IS NULL THEN TO_CHAR(folios.importe_total, '999,999.99') WHEN tabla_supre.financiamiento = 'FEDERAL Y ESTATAL' THEN TO_CHAR(folios.importe_total * 0.6, '999,999.99') END AS importe_federal"),
                    \DB::raw("
                        CASE
                            WHEN tabla_supre.financiamiento = 'FEDERAL' OR tabla_supre.financiamiento IS NULL THEN
                                CAST(folios.importe_total AS DECIMAL(10, 2))
                            WHEN tabla_supre.financiamiento = 'FEDERAL Y ESTATAL' THEN
                                CAST(folios.importe_total * (CAST(tabla_supre.porcentaje_financiamiento->>'federal' AS DECIMAL) / 100) AS DECIMAL(10, 2))
                        END AS importe_federal
                    "),

                    // \DB::raw("CASE WHEN tabla_supre.financiamiento = 'ESTATAL' THEN TO_CHAR(folios.importe_total, '999,999.99') WHEN tabla_supre.financiamiento = 'FEDERAL Y ESTATAL' THEN TO_CHAR(folios.importe_total * 0.4, '999,999.99') END AS importe_estatal"),
                    \DB::raw("
                    CASE
                        WHEN tabla_supre.financiamiento = 'ESTATAL' OR tabla_supre.financiamiento IS NULL THEN
                            CAST(folios.importe_total AS DECIMAL(10, 2))
                        WHEN tabla_supre.financiamiento = 'FEDERAL Y ESTATAL' THEN
                            CAST(folios.importe_total * (CAST(tabla_supre.porcentaje_financiamiento->>'estatal' AS DECIMAL) / 100) AS DECIMAL(10, 2))
                    END AS importe_estatal
                    "),


                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2) AS retencion_isr"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2) AS retencion_iva"),
                    'tabla_supre.folio_validacion AS memo_validacion',
                    'tabla_supre.fecha_validacion AS fecha_registro',
                    'folios.comentario AS observaciones',
                    \DB::raw("hombre + mujer AS total_estudiantes"))
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->WHERE('folios.status', '!=', 'Rechazado')
                           ->WHERE('tbl_cursos.status_curso','=','AUTORIZADO')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->LEFTJOIN('tbl_localidades', 'tbl_localidades.clave', '=', 'tbl_cursos.clave_localidad')
                           ->GET();
        }
        else if ($filtrotipo == 'curso')
        {
            $data = supre::SELECT('tabla_supre.no_memo',
                    'folios.folio_validacion as suf',
                    'tabla_supre.created_at',
                    'tabla_supre.fecha',
                    \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tbl_cursos.unidad',
                    \DB::raw("CASE WHEN tbl_cursos.tipo_curso = 'CURSO' THEN 'CURSO' ELSE 'CERTIFICACION EXTRAORDINARIA' END AS tipo_curso"),
                    'tbl_cursos.curso',
                    \DB::raw('tbl_cursos.hombre + tbl_cursos.mujer'),
                    'tbl_cursos.clave',
                    'tbl_cursos.ze',
                    'tbl_cursos.dura',
                    'tbl_cursos.muni',
                    'tbl_localidades.localidad',
                    // \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99')"),
                    // \DB::raw("TO_CHAR(folios.iva, '999,999.99')"),
                    \DB::raw("CAST(folios.importe_hora AS DECIMAL(10, 2)) AS importe_hora"),
                    \DB::raw("CAST(folios.iva AS DECIMAL(10, 2)) AS importe_iva_16"),

                    \DB::raw("'12101 Honorarios'"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'FEDERAL' OR tabla_supre.financiamiento = NULL THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'ESTATAL' THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),

                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),
                    // \DB::raw("floatval(number_format(folios.importe_total * 0.10, 2)"),
                    // \DB::raw("floatval(number_format(folios.importe_total * 0.1066, 2)"),

                    \DB::raw("ROUND(folios.importe_total * 0.10, 2) AS retencion_isr"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2) AS retencion_iva"),

                    'tabla_supre.folio_validacion',
                    'tabla_supre.fecha_validacion',
                    'folios.comentario')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('tbl_cursos.id', '=', $idcurso)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->LEFTJOIN('tbl_localidades', 'tbl_localidades.clave', '=', 'tbl_cursos.clave_localidad')
                           ->GET();
        }
        else if ($filtrotipo == 'unidad')
        {
            $data = supre::SELECT('tabla_supre.no_memo', 'folios.folio_validacion as suf','tabla_supre.created_at',
                    'tabla_supre.fecha', \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tbl_cursos.unidad',
                    \DB::raw("CASE WHEN tbl_cursos.tipo_curso = 'CURSO' THEN 'CURSO' ELSE 'CERTIFICACION EXTRAORDINARIA' END AS tipo_curso"),
                    'tbl_cursos.curso',
                    \DB::raw('tbl_cursos.hombre + tbl_cursos.mujer'),
                     'tbl_cursos.clave',
                    'tbl_cursos.ze',
                    'tbl_cursos.dura',
                    'tbl_cursos.muni',
                    'tbl_localidades.localidad',
                    // \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99')"),
                    // \DB::raw("TO_CHAR(folios.iva, '999,999.99')"),
                    \DB::raw("CAST(folios.importe_hora AS DECIMAL(10, 2)) AS importe_hora"),
                    \DB::raw("CAST(folios.iva AS DECIMAL(10, 2)) AS importe_iva_16"),

                    \DB::raw("'12101 Honorarios'"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'FEDERAL' OR tabla_supre.financiamiento = NULL THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'ESTATAL' THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),


                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),
                    // \DB::raw("floatval(number_format(folios.importe_total * 0.10, 2)"),
                    // \DB::raw("floatval(number_format(folios.importe_total * 0.1066, 2)"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2) AS retencion_isr"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2) AS retencion_iva"),

                    'tabla_supre.folio_validacion',
                    'tabla_supre.fecha_validacion',
                    'folios.comentario')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->LEFTJOIN('tbl_localidades', 'tbl_localidades.clave', '=', 'tbl_cursos.clave_localidad')
                           ->GET();
        }
        else if ($filtrotipo == 'instructor')
        {
            $data = supre::SELECT('tabla_supre.no_memo', 'folios.folio_validacion as suf','tabla_supre.created_at',
                    'tabla_supre.fecha', \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tbl_cursos.unidad',
                    \DB::raw("CASE WHEN tbl_cursos.tipo_curso = 'CURSO' THEN 'CURSO' ELSE 'CERTIFICACION EXTRAORDINARIA' END AS tipo_curso"),
                    'tbl_cursos.curso',
                    \DB::raw('tbl_cursos.hombre + tbl_cursos.mujer'),
                     'tbl_cursos.clave',
                    'tbl_cursos.ze',
                    'tbl_cursos.dura',
                    'tbl_cursos.muni',
                    'tbl_localidades.localidad',
                    // \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99')"),
                    // \DB::raw("TO_CHAR(folios.iva, '999,999.99')"),
                    \DB::raw("CAST(folios.importe_hora AS DECIMAL(10, 2)) AS importe_hora"),
                    \DB::raw("CAST(folios.iva AS DECIMAL(10, 2)) AS importe_iva_16"),

                    \DB::raw("'12101 Honorarios'"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'FEDERAL' OR tabla_supre.financiamiento = NULL THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'ESTATAL' THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'FEDERAL Y ESTATAL' THEN TO_CHAR(folios.importe_total * 0.6, '999,999.99') END AS importe_federal"),
                    \DB::raw("CASE WHEN tabla_supre.financiamiento = 'FEDERAL Y ESTATAL' THEN TO_CHAR(folios.importe_total * 0.4, '999,999.99') END AS importe_estatal"),


                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    // \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),
                    // \DB::raw("floatval(number_format(folios.importe_total * 0.10, 2)"),
                    // \DB::raw("floatval(number_format(folios.importe_total * 0.1066, 2)"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2) AS retencion_isr"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2) AS retencion_iva"),

                    'tabla_supre.folio_validacion',
                    'tabla_supre.fecha_validacion',
                    'folios.comentario')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('instructores.id', '=', $idInstructor)
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->LEFTJOIN('tbl_localidades', 'tbl_localidades.clave', '=', 'tbl_cursos.clave_localidad')
                           ->GET();
        }

        $cabecera = [
            'MEMO. SOLICITADO', 'NO. DE SUFICIENCIA', 'FECHA DE CREACION EN EL SISTEMA', 'FECHA',
            'INSTRUCTOR', 'UNIDAD/A.M DE CAP.', 'CURSO/CERTIFICACION', 'CURSO', 'CUPO', 'CLAVE DEL GRUPO',
            'Z.E.','HSM','MUNICIPIO','LOCALIDAD', 'IMPORTE POR HORA', 'IVA 16%', 'PARTIDA/CONCEPTO', 'IMPORTE TOTAL FEDERAL',
            'IMPORTE TOTAL ESTATAL', 'RETENCIÓN ISR', 'RETENCIÓN IVA', 'MEMO PRESUPUESTA',
            'FECHA REGISTRO', 'OBSERVACIONES','BENEFICIARIOS'
        ];

        $nombreLayout = "formato de control".$fecha1 . ' - '. $fecha2 . " creado el " . carbon::now() . ".xlsx";
        $titulo = "formato de control ".$fecha1 . ' - '. $fecha2 . " creado el " . carbon::now();
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    private function incapacidad($incapacidad, $incapacitado) {
        $fechaActual = now();
        if(isset($incapacidad->fecha_inicio) && !is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre','fun.curp','fun.cargo','fun.correo','org.nombre as org_nombre','fun.incapacidad')
                    ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                    ->Where('fun.id', $incapacidad->id_firmante)
                    ->First();

                return($firmanteIncapacidad);
            } else {
                // La fecha de hoy NO está dentro del rango
                if($fechaTermino->isPast()) {
                    $newIncapacidadHistory = 'Ini:'.$incapacidad->fecha_inicio.'/Fin:'.$incapacidad->fecha_termino.'/IdFun:'.$incapacidad->id_firmante;
                    array_push($incapacidad->historial, $newIncapacidadHistory);
                    $incapacidad->fecha_inicio = $incapacidad->fecha_termino = $incapacidad->id_firmante = null;
                    $incapacidad = json_encode($incapacidad);

                    DB::Table('tbl_funcionarios')->Where('nombre',$incapacitado)
                        ->Update([
                            'incapacidad' => $incapacidad
                    ]);
                }

                return false;
            }
        }
        return false;
    }
}
//A
