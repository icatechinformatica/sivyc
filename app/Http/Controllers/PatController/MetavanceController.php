<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use App\Models\ModelPat\Metavance;
use App\Models\ModelPat\RegistrosProced;
use App\Models\ModelPat\FechasPat;
use App\Models\ModelPat\HistoryPat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Http;
// use App\Models\FirmaElectronica\EfirmaPat;
use App\Models\DocumentosFirmar;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\Tokens_icti;
use Illuminate\Support\Facades\View;
use Vyuldashev\XmlToArray\XmlToArray;
use PHPQRCode\QRcode;

class MetavanceController extends Controller
{
    protected $arrayMes;

    public function __construct()
    {
        session_start();

        $this->arrayMes = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre',
        'octubre', 'noviembre', 'diciembre'];
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idorg = null)
    {
        #Año de ejercicio
        $sel_eje = $request->sel_ejercicio;
        $ejercicio = [];
        for ($i=2023; $i <= intval(date('Y')); $i++) {array_push($ejercicio, $i);}
        if($sel_eje == null && isset($_SESSION['eje_pat_registros']) == ''){
            $_SESSION['eje_pat_registros'] = date('Y');
        }elseif($sel_eje != null){
            $_SESSION['eje_pat_registros'] = $sel_eje;
        }
        $anio_eje = $_SESSION['eje_pat_registros'];


        $json_org = Auth::user()->id_organismos_json;
        $array_org = json_decode($json_org, true);
        $id_orgconst = $idorg;
        $id_organismo = ($id_orgconst) ? $id_orgconst = intval($id_orgconst) : $id_orgconst = $array_org[0];
        $_SESSION['id_organsmog'] = $id_organismo;
        $organismo = $_SESSION['id_organsmog'];

        //MOSTRAR FECHA
        $mesGlob = $this->arrayMes;
        $obtMes = intval(date('m')); $obtAnio = date('Y'); $obtDia =  date("d");
        $fechaNow =  $obtDia.'/'.$mesGlob[$obtMes-1].'/'.$obtAnio;

        //AREA DEL USUARIO / (AREA DPTO)

        #consultamos organismos
        $array_organismos = DB::table('tbl_organismos')->select('id', 'nombre')
        ->whereIn('id', $array_org)->get();

        $area_org = DB::table('tbl_organismos as o')->select('o.id', 'nombre', 'id_parent')
        ->where('o.id', $organismo)->first();

        // ORGANISMO DEL USUARIO / (DIRECCION)
        $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->where('o.id', $area_org->id_parent)->first();

        //CONSULTA DE FUNCIONES
        $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();


        //CONSULTA DE PROCEDIMIENTOS POR FUNCION
        $datos = [];
        for ($i=0; $i < count($funciones); $i++) {
            $val =  $funciones[$i]['id'];

            $proced = DB::table('funciones_proced as f')->select('m.id','m.id_proced' ,'f.fun_proc', 'm.total', 'm.observmeta', 'm.enero',
            'm.febrero', 'm.marzo', 'm.abril', 'm.mayo', 'm.junio', 'm.julio', 'm.agosto', 'm.septiembre', 'm.octubre', 'm.noviembre', 'm.diciembre', 'observaciones', 'um.unidadm', 'um.numero', 'um.tipo_unidadm')
            ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
            ->Join('metas_avances_pat as m', 'f.id', 'm.id_proced')
            ->where('m.ejercicio', '=', $anio_eje)
            ->whereIn('f.id', function($query)use($val, $obtAnio)  {
            $query->select('id')
                  ->from('funciones_proced')
                  ->where('id_parent', '=', $val)
                  ->where('activo', '=', 'true');
                //   ->where(DB::raw("date_part('year' , created_at )"),  $obtAnio);
            })
            ->orderBy('f.id')
            ->get();
            array_push($datos, $funciones[$i]['fun_proc'], $proced);
        }

        // FUNCION PARA VALIDACION DE METAS Y AVANCES
        $dosarray = $this->validacionMeta($organismo);
        $datos_status_meta = $dosarray[0]; //para mostrar mensajes y habilitar o deshabilitar los inputs
        $datos_status_avance = $dosarray[1];
        $fecha_meta_avance = $dosarray[2]; //para ir verificando el status


        $datos_efirma = $this->datos_firmado_meta($fecha_meta_avance);
        list($consul_efirma, $consul_efirma_avance, $meses_pendientes, $meses_validados) = $datos_efirma;

        // dd($consul_efirma, $consul_efirma_avance, $meses_pendientes, $meses_validados);

        //Validacion de avances efirma
        return view('vistas_pat.metas_avances', compact('datos', 'datos_status_meta', 'fecha_meta_avance', 'datos_status_avance',
        'area_org', 'org', 'fechaNow', 'mesGlob', 'array_organismos', 'organismo', 'ejercicio', 'anio_eje', 'consul_efirma','consul_efirma_avance','meses_pendientes','meses_validados'));
    }

    public function datos_firmado_meta($fecha_meta_avance){
         // $efirma_id = DB::table('documentos_firmar')->where('id', $fecha_meta_avance->fecha_meta['id_efirma'])->where('status', 'EnFirma')->value('id');

        $consul_efirma = array('idEfirmaMeta' => '', 'cadena_original' => '', 'firmante_uno' => '', 'firmante_dos' => '', 'firmado_uno' => '', 'firmado_dos' => '', 'token_efirma' => '',
            'curp_activo' => '', 'status_firma' => '', 'status_doc' => '', 'pos_firm_activo' => '', 'id_meta_valid' => ''
        );

        ## CONSULTAS
        $consulta_e_meta = false;
        if(isset($fecha_meta_avance->fecha_meta['id_efirma']) && !empty($fecha_meta_avance->fecha_meta['id_efirma'])){
            $consulta_e_meta = DocumentosFirmar::select('id','cadena_original','obj_documento', 'status')->where('id', $fecha_meta_avance->fecha_meta['id_efirma'])->where('nombre_archivo', 'META_ANUAL')->whereIn('status', ['EnFirma', 'VALIDADO'])->first();
        }

        $getToken = Tokens_icti::latest()->first();

        ##Obtenemos curp, email del firmante para validar si le pertenece firmar el documento
        $curpUser = DB::Table('users')->Select('tbl_funcionarios.curp', 'tbl_funcionarios.correo')->Join('tbl_funcionarios','tbl_funcionarios.correo','users.email')
        ->Where('users.id', Auth::user()->id)->First();

        ## DATOS PRINCIPALES QUE SE CARGAN
        if($getToken){$consul_efirma['token_efirma'] = $getToken->token;}
        if($curpUser){$consul_efirma['curp_activo'] = $curpUser->curp;}


        if($consulta_e_meta) {
            ## Llenar los campos cuando el documento este en firma
            if($consulta_e_meta->status == 'EnFirma'){
                $json_documento = json_decode($consulta_e_meta->obj_documento, true);

                $consul_efirma['idEfirmaMeta'] = $consulta_e_meta->id;
                $consul_efirma['cadena_original'] = $consulta_e_meta->cadena_original;
                $consul_efirma['status_doc'] = $consulta_e_meta->status;

                ##Nombre de firmantes y validar si ya firmaron
                $consul_efirma['firmante_uno'] = $json_documento['firmantes']['firmante'][0][0]['_attributes']['nombre_firmante'];
                $consul_efirma['firmante_dos'] = $json_documento['firmantes']['firmante'][0][1]['_attributes']['nombre_firmante'];

                if(!empty($json_documento['firmantes']['firmante'][0][0]['_attributes']['firma_firmante'])){$consul_efirma['firmado_uno'] = 'SI';}
                else{$consul_efirma['firmado_uno'] = 'NO';}

                if(!empty($json_documento['firmantes']['firmante'][0][1]['_attributes']['firma_firmante'])){$consul_efirma['firmado_dos'] = 'SI';}
                else{$consul_efirma['firmado_dos'] = 'NO';}

                ##Obtener el firmante activo en base al documento efirma
                foreach ($json_documento['firmantes']['firmante'][0] as $key => $value) {
                    if($value['_attributes']['curp_firmante'] == $consul_efirma['curp_activo']){
                        if(!empty($json_documento['firmantes']['firmante'][0][$key]['_attributes']['firma_firmante'])){
                            $consul_efirma['status_firma'] = 'FIRMADO';
                            $consul_efirma['pos_firm_activo'] = $key;
                        }else{
                            $consul_efirma['status_firma'] = '';
                            $consul_efirma['pos_firm_activo'] = '';
                        }
                    }
                }

            }else if($consulta_e_meta->status == 'VALIDADO'){
                $consul_efirma['status_doc'] = $consulta_e_meta->status;
                ## Id para visualizar los documentos pdf
                $consul_efirma['id_meta_valid'] = $consulta_e_meta->id;
            }

        }


        ## FIRMADO ELECTRONICO DE AVANCES

        $consul_efirma_avance = array('idEfirmaAvance' => '', 'cadena_original_ava' => '', 'firmante_uno_ava' => '', 'firmante_dos_ava' => '', 'firmado_uno_ava' => '', 'firmado_dos_ava' => '',
            'status_firma_ava' => '', 'status_doc_ava' => '', 'pos_firm_activo_ava' => '', 'mes_activo' => '', 'active_form_avance' => '', 'id_avance_valid' => ''
        );

        // $array_pendientes = array();
        $meses_pendientes = $meses_validados = array();
        if($fecha_meta_avance){
            $consulta_e_avance = false;
            $mes_activo = '';
            foreach ($fecha_meta_avance->fechas_avance as $key => $item) {
                if(isset($item['mod_documento']) && $item['mod_documento'] == 'efirma'){ //Si no se cumple es porque no existe o se canceló el documento
                    if(!empty($item['id_efirma'])){ //Buscamos el registro del documento y cortamos ciclo para darle prioridad.
                        //Hacer la consulta y dar break
                        $consulta_e_avance = DocumentosFirmar::select('id','cadena_original','obj_documento', 'status')->where('id', $item['id_efirma'])->where('status', 'EnFirma')->first();
                        if(!empty($consulta_e_avance)){
                            $mes_activo = $key;
                            break;
                        }
                    }
                }
            }
            // dd($consulta_e_avance);

            ## LLenado del array para avances
            if($consulta_e_avance) {
                ## Llenar los campos cuando el documento este en firma
                $consul_efirma_avance['active_form_avance'] = 'activo';
                $consul_efirma_avance['mes_activo'] = $mes_activo;
                if($consulta_e_avance->status == 'EnFirma'){
                    $json_documento = json_decode($consulta_e_avance->obj_documento, true);

                    $consul_efirma_avance['idEfirmaAvance'] = $consulta_e_avance->id;
                    $consul_efirma_avance['cadena_original_ava'] = $consulta_e_avance->cadena_original;
                    $consul_efirma_avance['status_doc_ava'] = $consulta_e_avance->status;

                    ##Nombre de firmantes y validar si ya firmaron
                    $consul_efirma_avance['firmante_uno_ava'] = $json_documento['firmantes']['firmante'][0][0]['_attributes']['nombre_firmante'];
                    $consul_efirma_avance['firmante_dos_ava'] = $json_documento['firmantes']['firmante'][0][1]['_attributes']['nombre_firmante'];

                    if(!empty($json_documento['firmantes']['firmante'][0][0]['_attributes']['firma_firmante'])){$consul_efirma_avance['firmado_uno_ava'] = 'SI';}
                    else{$consul_efirma_avance['firmado_uno'] = 'NO';}

                    if(!empty($json_documento['firmantes']['firmante'][0][1]['_attributes']['firma_firmante'])){$consul_efirma_avance['firmado_dos_ava'] = 'SI';}
                    else{$consul_efirma_avance['firmado_dos'] = 'NO';}

                    ##Obtener el firmante activo en base al documento efirma
                    foreach ($json_documento['firmantes']['firmante'][0] as $key => $value) {
                        if($value['_attributes']['curp_firmante'] == $consul_efirma['curp_activo']){
                            if(!empty($json_documento['firmantes']['firmante'][0][$key]['_attributes']['firma_firmante'])){
                                $consul_efirma_avance['status_firma_ava'] = 'FIRMADO';
                                $consul_efirma_avance['pos_firm_activo_ava'] = 0;
                            }else{
                                $consul_efirma_avance['status_firma'] = '';
                                $consul_efirma_avance['pos_firm_activo_ava'] = '';
                            }
                        }
                    }

                }else if($consulta_e_meta->status == 'VALIDADO'){
                    $consul_efirma_avance['status_doc_ava'] = $consulta_e_meta->status;
                    ## Id para visualizar los documentos pdf
                    $consul_efirma_avance['id_avance_valid'] = $consulta_e_meta->id;
                }

            }else{
                ## NO hay ningun documento con efirma por lo tanto todo deberia ir vacio y seguir con el proceso
                ## Hacemos recorrido de los meses que ya estan validados pero que no tienen archivos cargados.
                foreach ($fecha_meta_avance->fechas_avance as $key => $item) {
                    if($item['statusmes'] == 'autorizado' && empty($item['urldoc_firmav']) ){
                        if( empty($item['mod_documento']) ){
                            array_push($meses_pendientes, $key);
                        }
                    }
                }
                if(count($meses_pendientes) > 0){
                    $consul_efirma_avance['active_form_avance'] = 'activo';
                }
            }

            ## OBTENER LOS ID DE LOS DOCUMENTOS FIRMADOS DE AVANCES
            foreach ($fecha_meta_avance->fechas_avance as $key => $value) {
                if($value['statusmes'] == 'autorizado' && !empty($value['id_efirma']) && !empty($value['mod_documento']) ){
                    $resul_id = DocumentosFirmar::where('id', $value['id_efirma'])->where('status', 'VALIDADO')->value('id');
                    if(!empty($resul_id)) $meses_validados [$key] = $resul_id;
                }
            }


        }

        return [$consul_efirma, $consul_efirma_avance, $meses_pendientes, $meses_validados];

    }

    public function validacionMeta($organismo)
    {
        //CONSULTA PARA FECHAS Y STATUS
        $fecha_meta_avance = FechasPat::select('fecha_meta', 'fechas_avance', 'status_meta', 'status_avance')
            ->where('id_org', '=', $organismo)
            ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();

        //CONVERTIR FECHAS EN TIPO NUMERO PARA PODER COMPARARLOS
        $fecha_actual = date('d-m-Y');
        $fecha_actual_conv = strtotime($fecha_actual);

        $fech_meta_emi = $fecha_meta_avance->fecha_meta['fechaemi']; $fech_meta_lim = $fecha_meta_avance->fecha_meta['fechalimit'];
        $fech_meta_emi_conv = strtotime($fech_meta_emi); $fech_meta_lim_conv = strtotime($fech_meta_lim);



        //VALIDACION DE FECHAS Y STATUS PARA ENVIARLOS A LA VISTA
        $datos_status_meta = [];
        $fechas_texto_enviar = $this->fecha_calendar_meta($fecha_meta_avance->fecha_meta['fechaemi'], $fecha_meta_avance->fecha_meta['fechalimit']);
        if ($fech_meta_emi_conv <= $fecha_actual_conv && $fecha_actual_conv <= $fech_meta_lim_conv && $fecha_meta_avance->status_meta['statusmeta'] == 'activo') {

            //Obtenemos fecha en texto para mandarlo a la vista y mostrarlo en el calendar
            if ($fecha_meta_avance->status_meta['captura'] == '1') {
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'En captura', 'activo', $fechas_texto_enviar];

            }else if ($fecha_meta_avance->status_meta['proceso'] == '1') {
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'En proceso', 'inactivo', $fechas_texto_enviar];

            }else if ($fecha_meta_avance->status_meta['retornado'] == '1') {
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'Retornado', 'activo', $fechas_texto_enviar];

            }else if ($fecha_meta_avance->status_meta['validado'] == '1') { //esto pienso que nunca va a entrar
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'Validado', 'inactivo', $fechas_texto_enviar];

            }else{
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'inactivo', 'inactivo'];
            }

        }else if($fecha_meta_avance->status_meta['statusmeta'] == 'inactivo' and $fecha_meta_avance->status_meta['validado'] == '1'){
            $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'Validado', 'inactivo', $fechas_texto_enviar];
        }else{
            $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'inactivo', 'inactivo', 'inactivo'];
        }


        //VALIDACION DE AVANCE

        if ($fecha_meta_avance->status_meta['validado'] == '1') {

            //OBTENEMOS LA FECHA EN TEXTO Y CONVERTIMOS EN TIPO NUMERO PARA REALIZAR LA COMPARACION
            $mesGlob = $this->arrayMes;

            $fechini = $fecha_actual_conv; //fecha actual
            $fech1 = 0;
            $fech2 = 0;
            $obtMes = '';

            //Variables que se usara para hace una condicion  y llenar array para validación
            $fech_avan_emi_conv = 0;
            $fech_avan_lim_conv = 0;

            for ($i=0; $i < count($mesGlob); $i++) {
                if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['statusmes'] != 'autorizado') {
                    //Hacemos la conversion
                    $fech1 = strtotime($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['fechaemision']);
                    $fech2 = strtotime($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['fechafin']);
                    if ($fech1 <= $fechini && $fechini <= $fech2) {
                        $fech_avan_emi_conv = $fech1;
                        $fech_avan_lim_conv = $fech2;
                        $obtMes = $mesGlob[$i];
                        break;
                    }

                }
            }
            $mes_enviar = $obtMes;

            //VALIDAMOS CON FECHAS Y STATUS DE AVANCE
            $datos_status_avance = [];
            if ($fech_avan_emi_conv <= $fecha_actual_conv && $fecha_actual_conv <= $fech_avan_lim_conv && $fecha_meta_avance->status_avance['statusavance'] == 'activo') {

                //Obtenemos fecha en texto para mandarlo a la vista y mostrarlo en el calendar
                $fechas_texto_enviar = $this->fecha_calendar_meta($fecha_meta_avance->fechas_avance[$mes_enviar]['fechaemision'], $fecha_meta_avance->fechas_avance[$mes_enviar]['fechafin']);

                if ($fecha_meta_avance->status_avance['captura'] == '1') {
                    $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'En captura', $mes_enviar, $fechas_texto_enviar, 'activo'];

                }else if ($fecha_meta_avance->status_avance['proceso'] == '1') {
                    $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'En proceso', $mes_enviar, $fechas_texto_enviar, 'inactivo'];

                }else if ($fecha_meta_avance->status_avance['retornado'] == '1') {
                    $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'Retornado', $mes_enviar, $fechas_texto_enviar, 'activo'];

                }else if ($fecha_meta_avance->status_avance['autorizado'] == '1') {
                    $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'Autorizado', $mes_enviar, $fechas_texto_enviar, 'inactivo'];

                }else{
                    $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'inactivo', 'inactivo', 'inactivo', 'inactivo'];
                }
            }else if($fecha_meta_avance->status_avance['statusavance'] == 'inactivo' and $fecha_meta_avance->status_avance['autorizado'] == '1'){
                $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'Autorizado', $mes_enviar, $fechas_texto_enviar, 'inactivo'];
            }else{
                $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'inactivo', 'inactivo', 'inactivo', 'inactivo'];
            }

        }else{
            $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'inactivo', 'inactivo', 'inactivo', 'inactivo'];
        }

        $dosarray = [$datos_status_meta, $datos_status_avance, $fecha_meta_avance ];
        return $dosarray;
    }

    public function fecha_calendar_meta($fec_emi, $fec_lim)
    {
        $mesGlob = $this->arrayMes;
        $variables_fechas = [$fec_emi, $fec_lim];
        $fechas_texto = [];


        for( $i=0; $i < count($variables_fechas); $i++ )
        {
            $mes_convert = explode("-", $variables_fechas[$i]); //parte en 3 la fecha
            $mes = Carbon::parse($variables_fechas[$i]); //obtenemos en numero el mes
            $mes_texto = $mesGlob[$mes->month-1]; //obtenemos en letra el mes
            $texto = $mes_convert[0].' de '.$mes_texto.' de ' .$mes_convert[2]; //dia / mes / año
            array_push($fechas_texto, $texto);
        }

        return $fechas_texto;
    }

    /**
     * vista planeacion
     */
    public function planeindex(Request $request)
    {
        //Realizamos la busqueda de organismos para llenar el combo
        $organismos = DB::table('tbl_organismos')->select('id', 'id_parent', 'nombre')
        ->where('activo', 'true')->get();


        return view('vistas_pat.validar_planeacion', compact('organismos'));
    }

    /**
     * Mostrar a la vista con los datos obtenidos para validacion
     */
    public function valid_planeacion($id_getuser)
    {
        $anio_eje = $_SESSION['eje_pat_buzon'];  // La sesion del año se crea cuando se ingresa al buzon de validacion
        $id_organismo = $id_getuser;
        $dif_perfil = true;

        //MOSTRAR FECHA
        $mesGlob = $this->arrayMes;
        $obtMes = intval(date('m')); $obtAnio = date('Y'); $obtDia =  date("d");
        $fechaNow =  $obtDia.'/'.$mesGlob[$obtMes-1].'/'.$obtAnio;

        //AREA DEL USUARIO / (AREA DPTO)
        $area_org = DB::table('tbl_organismos as o')->select('o.id', 'nombre', 'id_parent')
        ->where('o.id', $id_organismo)->first();


        // ORGANISMO DEL USUARIO / (DIRECCION)
        $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->where('o.id', $area_org->id_parent)->first();

        //CONSULTA DE FUNCIONES
        $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $id_organismo)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();

        //CONSULTA DE PROCEDIMIENTOS POR FUNCION
        $datos = [];
        for ($i=0; $i < count($funciones); $i++) {
            $val =  $funciones[$i]['id'];

            $proced = DB::table('funciones_proced as f')->select('m.id','m.id_proced' ,'f.fun_proc', 'm.observmeta', 'm.total', 'm.enero',
            'm.febrero', 'm.marzo', 'm.abril', 'm.mayo', 'm.junio', 'm.julio', 'm.agosto', 'm.septiembre', 'm.octubre', 'm.noviembre', 'm.diciembre', 'observaciones', 'um.unidadm', 'um.numero', 'um.tipo_unidadm')
            ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
            ->Join('metas_avances_pat as m', 'f.id', 'm.id_proced')
            ->where('m.ejercicio', '=', $anio_eje)
            ->whereIn('f.id', function($query)use($val, $obtAnio)  {
            $query->select('id')
                  ->from('funciones_proced')
                  ->where('id_parent', '=', $val)
                  ->where('activo', '=', 'true');
                //   ->where(DB::raw("date_part('year' , created_at )"), '=', '2023');
            })
            ->orderBy('f.id')
            ->get();
            array_push($datos, $funciones[$i]['fun_proc'], $proced);
        }

        //CONSULTA PARA FECHAS Y STATUS
        $fecha_meta_avance = FechasPat::select('fecha_meta', 'fechas_avance', 'status_meta', 'status_avance')
            ->where('id_org', '=', $id_organismo)->where('periodo', '=', $anio_eje)->first();

        //condicion si la meta esta activo y si esta en proceso de validacion
        $fechas_texto_enviar = $this->fecha_calendar_meta($fecha_meta_avance->fecha_meta['fechaemi'], $fecha_meta_avance->fecha_meta['fechalimit']);

        if($fecha_meta_avance->status_meta['statusmeta'] == 'activo'){
            if($fecha_meta_avance->status_meta['captura'] == '1'){
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'En captura', 'inactivo', $fechas_texto_enviar];
            }
            if($fecha_meta_avance->status_meta['proceso'] == '1'){
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'En proceso', 'activo', $fechas_texto_enviar];
            }
            if($fecha_meta_avance->status_meta['retornado'] == '1'){
                $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'Retornado', 'inactivo', $fechas_texto_enviar];
            }
        }else if($fecha_meta_avance->status_meta['statusmeta'] == 'inactivo' and $fecha_meta_avance->status_meta['validado'] == '1') {
            $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'Validado', 'inactivo', $fechas_texto_enviar];

        }else{
            $datos_status_meta = [$fecha_meta_avance->status_meta['statusmeta'], 'inactivo', 'inactivo', $fechas_texto_enviar];
        }


        //VALIDACION AVANCE
        $fech1 = 0;
        $fech2 = 0;
        $obtMes = '';
        $fechini = strtotime(date('d-m-Y'));

        for ($i=0; $i < count($mesGlob); $i++) {
            if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['statusmes'] != 'autorizado') {
                //Hacemos la conversion
                $fech1 = strtotime($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['fechaemision']);
                $fech2 = strtotime($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['fechafin']);
                if ($fech1 <= $fechini && $fechini <= $fech2) {
                    $obtMes = $mesGlob[$i];
                    break;
                }

            }
        }


        try {
            $fechas_texto_enviar = $this->fecha_calendar_meta($fecha_meta_avance->fechas_avance[$obtMes]['fechaemision'], $fecha_meta_avance->fechas_avance[$obtMes]['fechafin']);
        } catch (\Throwable $th) {
            $fechas_texto_enviar = 'null';
        }
        //$fech_avan_emi = $fecha_meta_avance->fechas_avance[$obtMes]['fechaemision'];
        // $mes_convert = Carbon::parse($fech_avan_emi);
        // $mes_enviar = $mesGlob[$mes_convert->month-1];
        $mes_enviar = $obtMes;


        if($fecha_meta_avance->status_avance['statusavance'] == 'activo'){
            if($fecha_meta_avance->status_avance['captura'] == '1'){
                $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'En captura', $mes_enviar, $fechas_texto_enviar, 'inactivo'];
            }
            if($fecha_meta_avance->status_avance['proceso'] == '1'){
                $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'En proceso', $mes_enviar, $fechas_texto_enviar, 'activo'];
            }
            if($fecha_meta_avance->status_avance['retornado'] == '1'){
                $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'Retornado', $mes_enviar, $fechas_texto_enviar, 'inactivo'];
            }
        }else if($fecha_meta_avance->status_avance['statusavance'] == 'inactivo' and $fecha_meta_avance->status_avance['autorizado'] == '1') {
            $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'Autorizado', $mes_enviar, $fechas_texto_enviar, 'inactivo'];

        }else{
            $datos_status_avance = [$fecha_meta_avance->status_avance['statusavance'], 'inactivo', 'inactivo', $fechas_texto_enviar, 'inactivo'];
        }

        ## FIRMA ELECTRONICA
        $datos_efirma = $this->datos_firmado_meta($fecha_meta_avance);
        list($consul_efirma, $consul_efirma_avance, $meses_pendientes, $meses_validados) = $datos_efirma;


        return view('vistas_pat.metas_avances', compact('datos', 'datos_status_meta', 'fecha_meta_avance', 'datos_status_avance', 'area_org', 'org', 'fechaNow', 'dif_perfil', 'id_organismo', 'mesGlob', 'consul_efirma', 'consul_efirma_avance', 'meses_pendientes', 'meses_validados'));
    }

    public function registrar_validacion(Request $request)
    {
        try {
            $id_plane_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        $tipo_valid = $request->tipo_valid;
        $status_meta = $request->status_meta;
        $status_avance = $request->status_avance;
        $datos = $request->datos;
        $id_org = $request->id_orga;
        $mes = $request->mes;


        //Obtener el id de la tabla de fechas que contienen los status
        $id_fech_pat = FechasPat::select('id')->where('id_org', '=', $id_org)
        ->where('periodo', '=', $_SESSION['eje_pat_buzon'])->first();

        $id_reg_fecha = $id_fech_pat->id;

        $save_status_obser_met = function($cap, $pro, $ret, $val, $sta, $retvalid) use($id_plane_user, $id_reg_fecha) {
            //Cambiar status a tabla fechas
            $fechas_pat = FechasPat::find($id_reg_fecha);
            $status = $fechas_pat->status_meta;
            $status['captura'] = $cap;
            $status['proceso'] = $pro;
            $status['retornado'] = $ret;
            $status['validado'] = $val;
            $status['statusmeta'] = $sta;
            $fechas_pat->status_meta = $status;

            //fecha retorno y validacion
            $fech = $fechas_pat->fecha_meta;
            if ($retvalid == 'retornar') {$fech['fecmetretorno'] = date("Y-m-d H:i");}
            else if($retvalid == 'validar') {$fech['fecmetvalid'] = date("Y-m-d H:i");}
            $fechas_pat->fecha_meta = $fech;

            $fechas_pat->updated_at = date('Y-m-d');
            $fechas_pat->iduser_updated = $id_plane_user;
            $fechas_pat->save();

            return 'ok';
        };

        //VALIDACION DE RETORNO DE META
        if ($tipo_valid == 'retornar' and $status_meta == 'activo') {
            for ($i=0; $i < count($datos); $i++) {
                $metavances = RegistrosProced::find($datos[$i][0]);
                $metavances->observaciones = $datos[$i][1];
                $metavances->updated_at = date('Y-m-d');
                $metavances->iduser_updated = $id_plane_user;
                $metavances->save();
            }
           $save_status_obser_met('0', '0', '1', '0', 'activo', $tipo_valid); //cap pro ret val sta
           //Guardamos el historial como retornado
           $this->save_history($id_org, 'returned', 'meta', 'null');
        }

        //VALIDACION DE VALIDAR META
        if ($tipo_valid == 'validar' and $status_meta == 'activo') {
            for ($i=0; $i < count($datos); $i++) {
                $metavances = RegistrosProced::find($datos[$i][0]);
                $metavances->observaciones = '';
                $metavances->updated_at = date('Y-m-d');
                $metavances->iduser_updated = $id_plane_user;
                $metavances->save();
            }
            $save_status_obser_met('0', '0', '0', '1', 'inactivo', $tipo_valid); //cap pro ret val sta
            //Guardamos el historial como validado
            $this->save_history($id_org, 'validated', 'meta', 'null');
        }

        /**VALIDACION DE LA PARTE DE AVANCE */

        $save_status_obser_ava = function($cap, $pro, $ret, $aut, $sta, $mes, $retvalid) use($id_plane_user, $id_reg_fecha) {
            //Cambiar status a tabla fechas
            $fechas_pat = FechasPat::find($id_reg_fecha);
            $status = $fechas_pat->status_avance;
            $status['captura'] = $cap;
            $status['proceso'] = $pro;
            $status['retornado'] = $ret;
            $status['autorizado'] = $aut;
            $status['statusavance'] = $sta;
            $fechas_pat->status_avance = $status;
            //Haremos que un campo de status en fechas se actualize a validado

            if ($retvalid == 'retornar') {
                $mes_bd = $fechas_pat->fechas_avance;
                $mes_bd[$mes]['fecavanreturn'] = date("Y-m-d H:i");
                $fechas_pat->fechas_avance = $mes_bd;
            }
            else if ($retvalid == 'validar') {
                $mes_bd = $fechas_pat->fechas_avance;
                $mes_bd[$mes]['fecavanvalid'] = date("Y-m-d H:i");
                $mes_bd[$mes]['statusmes'] = 'autorizado';
                $fechas_pat->fechas_avance = $mes_bd;
            }

            $fechas_pat->updated_at = date('Y-m-d');
            $fechas_pat->iduser_updated = $id_plane_user;
            $fechas_pat->save();

            return 'ok';
        };

        //VALIDACION DE RETORNO DE AVANCE
        if ($tipo_valid == 'retornar' and $status_avance == 'activo') {
            for ($i=0; $i < count($datos); $i++) {
                $metavances = RegistrosProced::find($datos[$i][0]);
                $metavances->observaciones = $datos[$i][1];
                $metavances->updated_at = date('Y-m-d');
                $metavances->iduser_updated = $id_plane_user;
                $metavances->save();
            }
           $save_status_obser_ava('0', '0', '1', '0', 'activo', $mes, $tipo_valid); //cap pro ret val sta
           $this->save_history($id_org, 'returned', 'avance', $mes);
        }

         //VALIDACION DE VALIDACION DE AVANCE
         if ($tipo_valid == 'validar' and $status_avance == 'activo') {
            for ($i=0; $i < count($datos); $i++) {
                $metavances = RegistrosProced::find($datos[$i][0]);
                $metavances->observaciones = '';
                $metavances->updated_at = date('Y-m-d');
                $metavances->iduser_updated = $id_plane_user;
                $metavances->save();
            }
           $save_status_obser_ava('0', '0', '0', '0', 'inactivo', $mes, $tipo_valid); //cap pro ret val sta
           $this->save_history($id_org, 'validated', 'avance', $mes);
        }

        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Funcion que realiza el guardado de Metas
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = $request->datos;

        for ($i=0; $i < count($datos); $i++) {
            $total = $datos[$i][3] + $datos[$i][5] + $datos[$i][7] + $datos[$i][9] + $datos[$i][11] + $datos[$i][13] + $datos[$i][15] +
            $datos[$i][17] + $datos[$i][19] + $datos[$i][21] + $datos[$i][23] + $datos[$i][25];

            $metavances = RegistrosProced::find($datos[$i][0]);
            //Update a json
            //3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25
            //posocion 26 es de la pbservacion ojo
            $mes = $metavances->enero; $mes['meta'] = $datos[$i][3]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->enero = $mes; //queda pendiente lo de observaciones
            $mes = $metavances->febrero; $mes['meta'] = $datos[$i][5]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->febrero = $mes;
            $mes = $metavances->marzo; $mes['meta'] = $datos[$i][7]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->marzo = $mes;
            $mes = $metavances->abril; $mes['meta'] = $datos[$i][9]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->abril = $mes;
            $mes = $metavances->mayo; $mes['meta'] = $datos[$i][11]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->mayo = $mes;
            $mes = $metavances->junio; $mes['meta'] = $datos[$i][13]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->junio = $mes;
            $mes = $metavances->julio; $mes['meta'] = $datos[$i][15]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->julio = $mes;
            $mes = $metavances->agosto; $mes['meta'] = $datos[$i][17]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->agosto = $mes;
            $mes = $metavances->septiembre; $mes['meta'] = $datos[$i][19]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->septiembre = $mes;
            $mes = $metavances->octubre; $mes['meta'] = $datos[$i][21]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->octubre = $mes;
            $mes = $metavances->noviembre; $mes['meta'] = $datos[$i][23]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->noviembre = $mes;
            $mes = $metavances->diciembre; $mes['meta'] = $datos[$i][25]; $mes['fechmetasave'] = date("Y-m-d H:i"); $metavances->diciembre = $mes;

            $metavances->total = $total;
            $metavances->observmeta = $datos[$i][26];
            $metavances->updated_at = date('Y-m-d');
            $metavances->iduser_updated = Auth::user()->id;
            $metavances->save();
        }
        /**Busqueda de id para agregar fechas al registro */
        $id_reg_fecha = FechasPat::select('id')->where('id_org', '=', $_SESSION['id_organsmog'])
        ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();
        $statusmeta = FechasPat::find($id_reg_fecha->id);
        $statusf = $statusmeta->fecha_meta;
        $statusf['fecmetasave'] = date("Y-m-d H:i");
        $statusmeta->fecha_meta = $statusf;

        //VALIDAMOS SI ES ENVIAR A PLANEACION EJECUTAMOS ESTE CODIGO DE LO CONTRARIO NO
        if ($request->tipo_accion == 'send') {
            //Actualizamos el status de las meta
            $status = $statusmeta->status_meta;
            $status['captura'] = '0';
            $status['retornado'] = '0';
            $status['proceso'] = '1';
            $statusmeta->status_meta = $status;

            //Creamos una fecha de envio a planeacion
            $envplane = $statusmeta->fecha_meta;
            $envplane['fecenvioplane_m'] = date("Y-m-d H:i");
            $statusmeta->fecha_meta = $envplane;
        }
        $statusmeta->save();

        return response()->json([
            'status' => 200,
            'mensaje' => 'entro al metodo de manera exitosa',
            'accion' => $request->tipo_accion,
            'datos' => $id_reg_fecha
        ]);
    }

     /**
     * Funcion que realiza el guardado de Avances
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function avances(Request $request)
    {
        $datos = $request->datos;
        $mesUpdate = $datos[0][0];

        $id_reg_fecha = FechasPat::select('id')->where('id_org', '=', $_SESSION['id_organsmog'])
        ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();
        $statusavance = FechasPat::find($id_reg_fecha->id);

        for ($i=0; $i < count($datos); $i++) {
        //    0 mes, 1 avance, 2 id, 3 valor
            $metavances = RegistrosProced::find($datos[$i][2]);
            //Agregar al json avances
            if ($mesUpdate == 'enero') {$mes = $metavances->enero; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->enero = $mes;}
            else if ($mesUpdate == 'febrero') {$mes = $metavances->febrero; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->febrero = $mes;}
            else if ($mesUpdate == 'marzo') {$mes = $metavances->marzo; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->marzo = $mes;}
            else if ($mesUpdate == 'abril') {$mes = $metavances->abril; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->abril = $mes;}
            else if ($mesUpdate == 'mayo') { $mes = $metavances->mayo; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->mayo = $mes;}
            else if ($mesUpdate == 'junio') {$mes = $metavances->junio; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->junio = $mes;}
            else if ($mesUpdate == 'julio') {$mes = $metavances->julio; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->julio = $mes;}
            else if ($mesUpdate == 'agosto') {$mes = $metavances->agosto; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->agosto = $mes;}
            else if ($mesUpdate == 'septiembre') {$mes = $metavances->septiembre; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->septiembre = $mes;}
            else if ($mesUpdate == 'octubre') {$mes = $metavances->octubre; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->octubre = $mes;}
            else if ($mesUpdate == 'noviembre') {$mes = $metavances->noviembre; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->noviembre = $mes;}
            else if ($mesUpdate == 'diciembre') {$mes = $metavances->diciembre; $mes['avance'] = $datos[$i][3]; $mes['fechavasave'] = date("Y-m-d H:i"); $mes['expdesviaciones'] = $datos[$i][4]; $metavances->diciembre = $mes;}
            $metavances->updated_at = date('Y-m-d');
            $metavances->iduser_updated = Auth::user()->id;
            $metavances->save();
        }

        //Agregamos fecha de guardado en la bd tabla fechas
        $fecha = $statusavance->fechas_avance;
        $fecha[$mesUpdate]['fechasave'] = date("Y-m-d H:i");
        $statusavance->fechas_avance = $fecha;

        //Actualizamos el status de avance al dar ckick en enviar
        if ($request->tipo_accion == 'send') {
            $status = $statusavance->status_avance;
            $status['captura'] = '0';
            $status['retornado'] = '0';
            $status['proceso'] = '1';
            $statusavance->status_avance = $status;

            //Creamos una fecha cuando se envia a planeación
            $envplane = $statusavance->fechas_avance;
            $envplane[$mesUpdate]['fecenvioplane_a'] = date("Y-m-d H:i");
            $statusavance->fechas_avance = $envplane;

        }
        $statusavance->save();

        return response()->json([
            'status' => 200,
            'mensaje' => 'entro al metodo de manera exitosa',
            'accion' => $request->tipo_accion,
        ]);
    }


    /**
     * Funcion para realizar consultas y generar pdfs
     * get(accion)
     */
    public function genpdf($accion, $idorg)
    {
        //Obtenemos los id org, area del usuario quien ingresa
        try {
            if ($idorg != 'null') {
                $organismo = $idorg;
            }else {
                // $organismo = $_SESSION['id_organsmog'];
                $organismo = $_SESSION['id_organsmog'];
            }
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        // dd($organismo);
        $nom_direc_depto = DB::table('tbl_organismos as o')
        ->join('tbl_organismos as p', 'o.id_parent', '=', 'p.id')
        ->where('o.id', $organismo)
        ->select('p.nombre as direccion', 'o.nombre as depto')
        ->first();

        //MOSTRAR FECHA
        $mesGlob = $this->arrayMes;
        $obtMes = intval(date('m')); $obtAnio = date('Y'); $obtDia =  date("d");
        $fechaNow =  $obtDia.' de '.$mesGlob[$obtMes-1].' del '.$obtAnio;

        ## Obtenemos datos de los firmantes
        $firmantes = $this->datos_firmantes($organismo, 'tradicional');
        list($area_org, $org) = $firmantes;


        //CONSULTA DE FUNCIONES
        $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();

        ##Validamos las variables globales de planeacion y area normal
        $global_ejercicio = strval(date('Y'));
        if (isset($_SESSION['eje_pat_buzon'])){
            $global_ejercicio = $_SESSION['eje_pat_buzon'];
        }else if(isset($_SESSION['eje_pat_registros'])){
            $global_ejercicio = $_SESSION['eje_pat_registros'];
        }
        //CONSULTA DE PROCEDIMIENTOS POR FUNCION
        $procedimientos = [];
        for ($i=0; $i < count($funciones); $i++) {
            $val =  $funciones[$i]['id'];

            $proced = RegistrosProced::select('metas_avances_pat.id','metas_avances_pat.id_proced' ,'f.fun_proc', 'metas_avances_pat.total', 'metas_avances_pat.enero',
            'metas_avances_pat.febrero', 'metas_avances_pat.marzo', 'metas_avances_pat.abril', 'metas_avances_pat.mayo', 'metas_avances_pat.junio', 'metas_avances_pat.julio', 'metas_avances_pat.agosto',
            'metas_avances_pat.septiembre', 'metas_avances_pat.octubre', 'metas_avances_pat.noviembre', 'metas_avances_pat.diciembre', 'observaciones', 'observmeta', 'um.numero', 'um.unidadm', 'um.tipo_unidadm')
            ->Join('funciones_proced as f', 'f.id', 'metas_avances_pat.id_proced')
            ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
            ->where('metas_avances_pat.ejercicio', '=', $global_ejercicio)
            ->whereIn('f.id', function($query)use($val, $obtAnio)  {
            $query->select('id')
                  ->from('funciones_proced')
                  ->where('id_parent', '=', $val)
                  ->where('activo', '=', 'true'); //validar si esta activo
                //   ->where(DB::raw("date_part('year' , created_at )"), '=', '2023');
            })
            ->orderBy('f.id')
            ->get();

            array_push($procedimientos, $proced);
        }
        //$_SESSION['eje_pat_registros']
        //Consulta de fechas
        $fechasPat = function ($organismo, $anio_eje){
            $tblFechas = FechasPat::select('id', 'fechas_avance', 'fecha_meta')->where('id_org', '=', $organismo)
            ->where('periodo', '=', $anio_eje)->first();
            return $tblFechas;
        };


        //Funcion Generar porcentaje
        $porcentaje = function($resta, $meta) {
            $multi = $resta * 100;
            $num_div = $meta == 0 ? $num_div = 1 : $num_div = $meta;
            $num_div = $multi / $num_div;
            return $num_div;
        };

        //Condiciones meses obtener avances con las acumulaciones
        $condiciones_meses = function($mes, $i, $e)use($procedimientos){
            $response = [];
            switch ($mes) {
                case 'enero':
                    $desviaciones = $procedimientos[$i][$e]->enero['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->enero['fechavasave'];
                    $meta_info = (int) $procedimientos[$i][$e]->enero['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->enero['avance'];
                    //Suma como es enero no hay nada que sumar
                    $meta_acum = $meta_info;
                    $avance_acum = $avance_info;
                    break;

                case 'febrero':
                    $desviaciones = $procedimientos[$i][$e]->febrero['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->febrero['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $meta_acum = $meta_info + $enero_meta;
                    $avance_acum = $avance_info + $enero_avance;

                    break;
                case 'marzo':
                    $desviaciones = $procedimientos[$i][$e]->marzo['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->marzo['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $meta_acum = $meta_info + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $febrero_avance + $enero_avance;
                    break;

                case 'abril':
                    $desviaciones = $procedimientos[$i][$e]->abril['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->abril['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->abril['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->abril['avance'];
                    $meta_acum = $meta_info + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'mayo':
                    $desviaciones = $procedimientos[$i][$e]->mayo['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->mayo['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $meta_acum = $meta_info + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'junio':
                    $desviaciones = $procedimientos[$i][$e]->junio['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->junio['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->junio['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->junio['avance'];
                    $meta_acum = $meta_info + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'julio':
                    $desviaciones = $procedimientos[$i][$e]->julio['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->julio['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->julio['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->julio['avance'];
                    $meta_acum = $meta_info + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'agosto':
                    $desviaciones = $procedimientos[$i][$e]->agosto['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->agosto['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $meta_acum = $meta_info + $julio_meta + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $julio_avance + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'septiembre':
                    $desviaciones = $procedimientos[$i][$e]->septiembre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->septiembre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $meta_acum = $meta_info + $agosto_meta + $julio_meta + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $agosto_avance + $julio_avance + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'octubre':
                    $desviaciones = $procedimientos[$i][$e]->octubre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->octubre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $sep_meta = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->octubre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->octubre['avance'];
                    $meta_acum = $meta_info + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                 $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                 $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'noviembre':
                    $desviaciones = $procedimientos[$i][$e]->noviembre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->noviembre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $sep_meta = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $oct_meta = (int) $procedimientos[$i][$e]->octubre['meta'];
                    $oct_avance = (int) $procedimientos[$i][$e]->octubre['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->noviembre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->noviembre['avance'];
                    $meta_acum = $meta_info + $oct_meta + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                    $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $oct_avance + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                    $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'diciembre':
                    $desviaciones = $procedimientos[$i][$e]->diciembre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->diciembre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $sep_meta = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $oct_meta = (int) $procedimientos[$i][$e]->octubre['meta'];
                    $oct_avance = (int) $procedimientos[$i][$e]->octubre['avance'];
                    $nov_meta = (int) $procedimientos[$i][$e]->noviembre['meta'];
                    $nov_avance = (int) $procedimientos[$i][$e]->noviembre['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->diciembre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->diciembre['avance'];
                    $meta_acum = $meta_info + $nov_meta + $oct_meta + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                    $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $nov_avance + $oct_avance + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                    $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                default:
                    # code...
                    break;
                }

                return $response = ["meta_info" => $meta_info, "avance_info" => $avance_info,
                                    "meta_acum" => $meta_acum, "avance_acum" => $avance_acum,
                                    "fecha_guardado" => $fecha_guardado, "exp_desviacion" => $desviaciones];

        };

        //Guardar fecha en fecha_meta
        $savefechmeta_pdf = function($id_org){
            $statusmeta = FechasPat::find($id_org);
            $statusf = $statusmeta->fecha_meta;
            $statusf['fecmetapdf'] = date("Y-m-d");
            // $statusf['mod_documento'] = 'tradicional';
            $statusmeta->fecha_meta = $statusf;
            $statusmeta->save();

            return 'ok';
        };

        //Guardar fecha en fecha avances
        $savefechava_pdf = function($id_org, $mes){
            $statusava = FechasPat::find($id_org);
            $statusf = $statusava->fechas_avance;
            $statusf[$mes]['fecavanpdf'] = date("Y-m-d");
            $statusava->fechas_avance = $statusf;
            $statusava->save();

            return 'ok';
        };


        $separador = explode("_", $accion); //contiene (meta)(avance_'mes')
        $mes_meta_avance = [];

        if($separador[0] == 'meta'){
            $fecha_enviar = '';
            $tblFechas =  $fechasPat($organismo, $global_ejercicio); #ejecutamos solo una vez en lugar de varias veces
            switch ($separador[1]) {
                case 'crear':
                    // $tblFechas =  $fechasPat($organismo);
                    $fechapdf = $savefechmeta_pdf($tblFechas->id);
                    $fecha_enviar = $tblFechas->fecha_meta['fecmetapdf'];
                    break;
                case 'generar':
                    // $tblFechas =  $fechasPat($organismo);
                    $fecha_enviar = $tblFechas->fecha_meta['fecmetapdf'];
                    break;
                case 'genOrigin':
                    // $tblFechas =  $fechasPat($organismo);
                    $fecha_enviar = $tblFechas->fecha_meta['fecmetapdf'];
                    break;
                case 'genActual':
                    $fecha_enviar = date("Y-m-d");
                    break;

                default:
                    # code...
                    break;
            }

            #Validacion de borrador de pdf
            $borrador_meta = $tblFechas->fecha_meta['fecmetvalid'];
            $marca = true;
            if($borrador_meta != "") $marca = false;


            //Guardamos la fecha antes de la generación
            $fech_carbon = Carbon::parse($fecha_enviar);
            $fecha_meta = $fech_carbon->format('d/m/Y');
            // $html_content = View::make('vistas_pat.genpdfmeta', compact('area_org', 'org', 'funciones', 'procedimientos', 'fecha_meta', 'marca', 'firm_logueado', 'global_ejercicio'))->render();
            // dd($html_content);
            $pdf = PDF::loadView('vistas_pat.genpdfmeta', compact('area_org', 'org', 'funciones', 'procedimientos', 'fecha_meta', 'marca', 'global_ejercicio', 'nom_direc_depto'));
            $pdf->setpaper('letter', 'landscape');
            return $pdf->stream('PAT-ICATECH-002.1.pdf');

        }else if($separador[0] == 'avances'){

            //Realiza todo el proceso de ir sumando mes con mes
            $mes_avance = "";
            for ($i=0; $i < count($funciones) ; $i++) {
                $arrayacum = [];
                    for ($e=0; $e < count($procedimientos[$i]); $e++) {
                        foreach ($mesGlob as $mes) {
                            if ($separador[1] == $mes) {
                                $resul = $condiciones_meses($separador[1], $i, $e);
                                //fecha mes avance
                                $mes_avance = $resul['fecha_guardado'];
                                $explic_desviacion = $resul['exp_desviacion'];
                                //info
                                $resta_info = ($resul["avance_info"] - $resul["meta_info"]);
                                $porcentaje_info = $porcentaje($resta_info, $resul["meta_info"]);
                                //acum
                                $resta_acum = ($resul["avance_acum"] - $resul["meta_acum"]);
                                $porcentaje_acum = $porcentaje($resta_acum, $resul["meta_acum"]);

                                $acum = ["meta" => $resul["meta_info"], "avance" => $resul["avance_info"], "resta" => $resta_info,
                                        "porcentaje" => round($porcentaje_info, 4), "metas_acum" => $resul["meta_acum"], "avance_acum" => $resul["avance_acum"],
                                        "resta_acum" => $resta_acum, "porcentaje_acum" => round($porcentaje_acum, 4), "exp_desviacion" => $explic_desviacion];
                            }
                        }
                        array_push($arrayacum, $acum);
                    }
                    array_push($mes_meta_avance, $arrayacum);
            }

            //condicion de fechas
            $fecha_enviar = '';
            $tblFechas =  $fechasPat($organismo, $global_ejercicio); #Ejecutamos solo una vez
            switch ($separador[2]) {
                case 'crear':
                    // $tblFechas =  $fechasPat($organismo);
                    //(id, mes)
                    $savefechava_pdf($tblFechas->id, $separador[1]);
                    $fecha_enviar = $tblFechas->fechas_avance[$separador[1]]['fecavanpdf'];
                    break;
                case 'generar':
                    // $tblFechas =  $fechasPat($organismo);
                    $fecha_enviar = $tblFechas->fechas_avance[$separador[1]]['fecavanpdf'];
                    break;
                case 'genOrigin':
                    // $tblFechas =  $fechasPat($organismo);
                    $fecha_enviar = $tblFechas->fechas_avance[$separador[1]]['fecavanpdf'];
                    break;
                case 'genActual':
                    $fecha_enviar = date("Y-m-d");
                    break;

                default:
                    # code...
                    break;
            }

            #Validacion de borrador de pdf
            $borrador_avance = $tblFechas->fechas_avance[$separador[1]]['fecavanvalid'];
            $marca = true;
            if($borrador_avance != "") $marca = false;

            //Convertir fechas
            $fech_carbon = Carbon::parse($fecha_enviar); // seperador[1] contiene el mes
            // $mes_avance = $mesGlob[$fech_carbon->month-1].'-'.$fech_carbon->day; // Obtenemos
            $mes_avance = $separador[1];
            $fecha_avance = $fech_carbon->format('d/m/Y');

            // dd($mes_meta_avance, $funciones);

            $pdf = PDF::loadView('vistas_pat.genpdfavance', compact('area_org', 'org', 'funciones', 'procedimientos', 'mes_meta_avance', 'mes_avance', 'fecha_avance', 'marca', 'global_ejercicio', 'nom_direc_depto'));
            $pdf->setpaper('letter', 'landscape');
            return $pdf->stream('PAT-ICATECH-002.2.pdf');
        }

    }

    /** Funcion para subir pdf al servidor
     * @param string $pdf, $id, $nom
     */
    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/pat/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/pat/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return [$pdfUrl, $pdfFile];
    }

    /**
     * Realizamos la subida de pdf para metas
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadpdfmeta(Request $request)
    {
        $id = FechasPat::select('id')->where('id_org', '=', $_SESSION['id_organsmog'])
        ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();
        $mensaje = "";

        if($request->hasFile('archivoPDF') and $id->id != null){

            if($request->acciondoc == 'libre'){}
            else if($request->acciondoc == 'reemplazar'){
                $filePath = 'uploadFiles/pat/'.$id->id.'/'.$request->nomDoc;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                } else { return response()->json(['status' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
            }
            //Guardamos la url
            $meta = FechasPat::find($id->id);
            $doc = $request->file('archivoPDF'); # obtenemos el archivo
            $urldoc = $this->pdf_upload($doc, $id->id, 'metafirmado'); # invocamos el método
            $url = $meta->fecha_meta;
            $url['urldoc_firm'] = $urldoc[0];
            $url['nomdoc_firm'] = $urldoc[1];
            $meta->fecha_meta = $url; # guardamos el path
            $meta->save();
            $mensaje = "SE SUBIÓ CORRECTAMENTE EL DOCUMENTO!";

        }else{ $mensaje = "ERROR AL SUBIR EL DOCUMENTO!"; }
        return response()->json(['status' => 200, 'mensaje' => $mensaje ]);
    }

     /**
     * Realizamos la subida de pdf para avances por mes
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadpdfavance(Request $request)
    {
        $id = FechasPat::select('id','fechas_avance')->where('id_org', '=', $_SESSION['id_organsmog'])
        ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();
        $mensaje = "";

        if($request->hasFile('archivoPDF') and $id->id != null){

            if(!empty($id->fechas_avance[$request->mes]['nomdoc_firmav'])){
                $filePath = 'uploadFiles/pat/'.$id->id.'/'.$id->fechas_avance[$request->mes]['nomdoc_firmav'];
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
            //Guardamos la url
            $avance = FechasPat::find($id->id);
            $doc = $request->file('archivoPDF'); # obtenemos el archivo
            $urldoc = $this->pdf_upload($doc, $id->id, 'avancefirmado'.$request->mes); # invocamos el método

            $url = $avance->fechas_avance;
            $url[$request->mes]['urldoc_firmav'] = $urldoc[0];
            $url[$request->mes]['nomdoc_firmav'] = $urldoc[1];
            $avance->fechas_avance = $url; # guardamos el path
            $avance->save();
            $mensaje = "SE SUBIÓ CORRECTAMENTE EL DOCUMENTO!";

        }else{ $mensaje = "ERROR AL SUBIR EL DOCUMENTO!"; }
        return response()->json(['status' => 200, 'mensaje' => $mensaje ]);
    }

     /**
     * Funcion para guardar historial en la bd
     *
     */
    public function save_history($id_org, $valid_return, $meta_avance, $mesavance)
    {
        //Obtenemos los id org, area del usuario quien ingresa
        try {
            // $organismo = $_SESSION['id_organsmog'];
            $organismo = $id_org;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        #AREA DEL USUARIO / (AREA DPTO)
        $area_org = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as area_org', 'id_parent', 'fun.nombre as func', 'fun.cargo')
        ->Join('tbl_funcionarios as fun', 'fun.id_org', 'o.id')
        ->where('o.id', $organismo)->first();


        # ORGANISMO DEL USUARIO / (DIRECCION)
        $org = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as org', 'fun.nombre as fun', 'fun.cargo')
        ->Join('tbl_funcionarios as fun', 'fun.id_org', 'o.id')
        ->where('o.id', $area_org->id_parent)->first();

        #CONSULTA DE FUNCIONES
        $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();



        #CONSULTA DE PROCEDIMIENTOS POR FUNCION
        $procedimientos = [];
        for ($i=0; $i < count($funciones); $i++) {
            $val =  $funciones[$i]['id'];

            $proced = RegistrosProced::select('metas_avances_pat.id','metas_avances_pat.id_proced' ,'f.fun_proc', 'metas_avances_pat.total', 'metas_avances_pat.enero',
            'metas_avances_pat.febrero', 'metas_avances_pat.marzo', 'metas_avances_pat.abril', 'metas_avances_pat.mayo', 'metas_avances_pat.junio',
            'metas_avances_pat.julio', 'metas_avances_pat.agosto', 'metas_avances_pat.septiembre', 'metas_avances_pat.octubre', 'metas_avances_pat.noviembre',
            'metas_avances_pat.diciembre', 'metas_avances_pat.observaciones', 'metas_avances_pat.observmeta', 'metas_avances_pat.updated_at','metas_avances_pat.ejercicio',
            'metas_avances_pat.iduser_updated', 'um.numero', 'um.unidadm', 'um.tipo_unidadm')
            ->Join('funciones_proced as f', 'f.id', 'metas_avances_pat.id_proced')
            ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
            ->where('ejercicio', '=', $_SESSION['eje_pat_registros'])
            ->whereIn('f.id', function($query)use($val)  {
            $query->select('id')
                    ->from('funciones_proced')
                    ->where('id_parent', '=', $val)
                    ->where('activo', '=', 'true') //validar si esta activo
                    ->where(DB::raw("date_part('year' , created_at )"), '=', '2023');
            })
            ->orderBy('f.id')
            ->get();

            array_push($procedimientos, $proced);
        }

        #CONSULTA DE FECHAS
        $tblFechas = FechasPat::select('id', 'fechas_avance', 'fecha_meta')->where('id_org', '=', $organismo)
        ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();

        if ($meta_avance == 'meta') {
            #DATOS META
            $content_datos = array();
            for ($i=0; $i<count($funciones); $i++){
                $arrayproced = [];
                for ($p=0; $p<count($procedimientos[$i]); $p++) {
                    $procedimiento = array(
                        "id" => $procedimientos[$i][$p]->id,
                        "nombre" => $procedimientos[$i][$p]->fun_proc,
                        "ejercicio" => $procedimientos[$i][$p]->ejercicio,
                        "total" => $procedimientos[$i][$p]->total,
                        "numeroum" => $procedimientos[$i][$p]->numero,
                        "unidadm" => $procedimientos[$i][$p]->unidadm,
                        "tipo_unidadm" => $procedimientos[$i][$p]->tipo_unidadm,
                        "observ_meta" => $procedimientos[$i][$p]->observmeta,
                        "observ_plane" => $procedimientos[$i][$p]->observaciones,
                        // "updated_at" => $procedimientos[$i][$p]->updated_at,
                        "iduser_updated" => $procedimientos[$i][$p]->iduser_updated,
                        "enero" => array(
                            "metas" => $procedimientos[$i][$p]->enero['meta']
                        ),
                        "febrero" => array(
                            "metas" => $procedimientos[$i][$p]->febrero['meta']
                        ),
                        "marzo" => array(
                            "metas" => $procedimientos[$i][$p]->marzo['meta']
                        ),
                        "abril" => array(
                            "metas" => $procedimientos[$i][$p]->abril['meta']
                        ),
                        "mayo" => array(
                            "metas" => $procedimientos[$i][$p]->mayo['meta']
                        ),
                        "junio" => array(
                            "metas" => $procedimientos[$i][$p]->junio['meta']
                        ),
                        "julio" => array(
                            "metas" => $procedimientos[$i][$p]->julio['meta']
                        ),
                        "agosto" => array(
                            "metas" => $procedimientos[$i][$p]->agosto['meta']
                        ),
                        "septiembre" => array(
                            "metas" => $procedimientos[$i][$p]->septiembre['meta']
                        ),
                        "octubre" => array(
                            "metas" => $procedimientos[$i][$p]->octubre['meta']
                        ),
                        "noviembre" => array(
                            "metas" => $procedimientos[$i][$p]->noviembre['meta']
                        ),
                        "diciembre" => array(
                            "metas" => $procedimientos[$i][$p]->diciembre['meta']
                        )
                    );
                    $arrayproced[] = $procedimiento;
                }
                $content = array(
                    "funcion" => $funciones[$i]->fun_proc,
                    "procedimientos" => $arrayproced
                );
                array_push($content_datos, $content);
            }
            #FECHAS META
            $arrayFechas = array(
                "fecha_retorno" => $tblFechas->fecha_meta['fecmetretorno'],
                "fecha_save" => $tblFechas->fecha_meta['fecmetasave'],
                "fecha_envio_plane" => $tblFechas->fecha_meta['fecenvioplane_m'],
                "fecha_validado" => $tblFechas->fecha_meta['fecmetvalid'],
                "fecha_emision" => $tblFechas->fecha_meta['fechaemi'],
                "fecha_limite" => $tblFechas->fecha_meta['fechalimit']
            );
        }

        if ($meta_avance == 'avance') {
            #DATOS AVANCE
            $content_datos = array();
            for ($i=0; $i<count($funciones); $i++){
                $arrayproced = [];
                for ($p=0; $p<count($procedimientos[$i]); $p++) {
                    $procedimiento = array(
                        "id" => $procedimientos[$i][$p]->id,
                        "nombre" => $procedimientos[$i][$p]->fun_proc,
                        "ejercicio" => $procedimientos[$i][$p]->ejercicio,
                        "total" => $procedimientos[$i][$p]->total,
                        "numeroum" => $procedimientos[$i][$p]->numero,
                        "unidadm" => $procedimientos[$i][$p]->unidadm,
                        "tipo_unidadm" => $procedimientos[$i][$p]->tipo_unidadm,
                        "observ_plane" => $procedimientos[$i][$p]->observaciones,
                        "updated_at" => $procedimientos[$i][$p]->updated_at,
                        "iduser_updated" => $procedimientos[$i][$p]->iduser_updated,
                        $mesavance => array(
                            "avances" => $procedimientos[$i][$p]->{$mesavance}['avance'],
                            "exp_desviacion" => $procedimientos[$i][$p]->{$mesavance}['expdesviaciones']
                        ),
                    );
                    $arrayproced[] = $procedimiento;
                }
                $content = array(
                    "funcion" => $funciones[$i]->fun_proc,
                    "procedimientos" => $arrayproced
                );
                array_push($content_datos, $content);
            }
            #FECHAS AVANCE
            $arrayFechas = array(
                "fecha_retorno" => $tblFechas->fechas_avance[$mesavance]['fecavanreturn'],
                "fecha_save" => $tblFechas->fechas_avance[$mesavance]['fechasave'],
                "fecha_envio_plane" => $tblFechas->fechas_avance[$mesavance]['fecenvioplane_a'],
                "fecha_validado" => $tblFechas->fechas_avance[$mesavance]['fecavanvalid'],
                "status_mes" => $tblFechas->fechas_avance[$mesavance]['statusmes'],
                "fecha_emision" => $tblFechas->fechas_avance[$mesavance]['fechaemision'],
                "fecha_limite" => $tblFechas->fechas_avance[$mesavance]['fechafin']
            );
        }

        #ESTRUCTURA DEL ARRAY
        $campoPadre = $valid_return.'_'.date('Y-m-d').'_'.date('H:i');
        $datos = array(
            $campoPadre => array(
                "datos" => array(
                    $content_datos
                ),
                "fechas" => $arrayFechas
            )
        );
        // Convertir el array en una cadena JSON
        // $jsonHistorial = json_encode($datos);

        #CONSULTA A LA TABLA HISTORY_PAT
        $historial = new HistoryPat;
        $historial['id_org'] = $id_org;
        $historial['direccion'] = $area_org->area_org;
        $historial['area'] =  $org->org;
        $historial['fecha'] = date('Y-m-d H:i');
        $historial['iduser_created'] = Auth::user()->id;
        $historial['created_at'] = date('Y-m-d H:i');
        $historial['updated_at'] = date('Y-m-d H:i');
        if($meta_avance == 'meta')$historial['meta_json'] = $datos;
        if($meta_avance == 'avance')$historial['avance_json'] = $datos;
        $historial->save();


    }

    ###Cancelar subida de archivo meta y avance
    public function cancel_accion_docs(Request $request){
        $documento = $request->input('documento'); //meta o avance
        $tipo_doc = $request->input('tipo_doc'); //tradicional o efirma
        $anio = $request->input('anio'); //ejercicio
        $org = $request->input('org'); //idorg
        //Obtenemos el id de efirma para hacer el cancelado en la tabla de documentos_firmar
        $idEfirma = $request->input('idEfirma');

        //Partimos la cadena en caso de ser necesario
        $particion_valor = explode("_", $documento);

        //Carga tradicional Meta Anual
        if($particion_valor[0] == 'meta' && $tipo_doc == 'tradicional'){
            try {
                DB::table('fechas_pat')
                ->where('id_org', $org)
                ->where('periodo', $anio)
                ->update([
                    'fecha_meta' => DB::raw("jsonb_set(jsonb_set(fecha_meta, '{fecmetapdf}', '\"\"'), '{mod_documento}', '\"\"')")
                ]);
                return response()->json(['status' => 200,'mensaje' => 'Cancelado con exito!']);
            } catch (\Throwable $th) {
                return response()->json(['status' => 500,'mensaje' => $th->getMessage()]);
            }

        }else if($particion_valor[0] == 'meta' && $tipo_doc == 'efirma'){
            try {
                DB::table('fechas_pat')
                ->where('id_org', $org)
                ->where('periodo', $anio)
                ->update([
                    // 'fecha_meta' => DB::raw("jsonb_set(fecha_meta, '{mod_documento}', '\"\"')")
                    // 'fecha_meta' => DB::raw("jsonb_set(jsonb_set(fecha_meta, '{mod_documento}', '\"\"'), '{status_efirma}', '\"\"')")
                    'fecha_meta' => DB::raw("jsonb_set(jsonb_set(fecha_meta, '{mod_documento}', '\"\"'::jsonb), '{status_efirma}', '\"\"'::jsonb)")
                ]);

                ##En caso de haber un documento pendiente con firma electronica pasarlo al estado de cancelado.
                DB::table('documentos_firmar')
                ->where('id', $idEfirma)
                ->update([
                    'status' => 'CANCELADO'
                ]);
                return response()->json(['status' => 200,'mensaje' => 'Cancelado con exito!']);
            } catch (\Throwable $th) {
                return response()->json(['status' => 500,'mensaje' => $th->getMessage()]);
            }

        }else if($particion_valor[0] == 'avance' && $tipo_doc == 'efirma'){
            try {
                DB::table('fechas_pat')
                ->where('id_org', $org)
                ->where('periodo', $anio)
                ->update([
                    // 'fechas_avance' => DB::raw("jsonb_set(fechas_avance, '{" . $particion_valor[1] . ", mod_documento}', '\"\"')")
                    'fechas_avance' => DB::raw(
                    "jsonb_set(
                        jsonb_set(fechas_avance, '{" . $particion_valor[1] . ", mod_documento}', '\"\"'::jsonb),
                        '{" . $particion_valor[1] . ", status_efirma}', '\"\"'::jsonb
                    )"
                )

                ]);

                ##En caso de haber un documento pendiente con firma electronica pasarlo al estado de cancelado.
                DB::table('documentos_firmar')
                ->where('id', $idEfirma)
                ->update([
                    'status' => 'CANCELADO'
                ]);
                return response()->json(['status' => 200,'mensaje' => 'Cancelado con exito!']);
            } catch (\Throwable $th) {
                return response()->json(['status' => 500,'mensaje' => $th->getMessage()]);
            }
        }
    }

    public function show_carga_tradi(Request $request){
        $ejercicio = $request->input('ejercicio');
        $organismo = $request->input('id_organismo');
        $tipo = $request->input('tipo');

        if($tipo == 'meta'){
            DB::table('fechas_pat')
                ->where('id_org', $organismo)
                ->where('periodo', $ejercicio)
                ->update([
                    'fecha_meta' => DB::raw("jsonb_set(fecha_meta, '{mod_documento}', '\"tradicional\"')")
            ]);
        }
        return response()->json(['status' => 200, 'mensaje' => 'exitoso' ]);

    }

    ##Generar estructura xml para firmado electronico
    public function generar_xml_meta(Request $request){
        // dd($request->all());
        $organismo = $request->get('organismo');
        $ejercicio = $request->get('ejercicio');
        $tipo_efirma = $request->get('tipo_efirma');
        $mesavance = $request->get('mesavance');

        $nombre_reporte = '';
        $nombre_oficio = '';
        $body = $cont_html = '';
        $nombre_archivo = '';

        ##OBTENEMOS DATOS DE LOS FIRMANTES
        $firmantes = $this->datos_firmantes($organismo, 'efirma');
        list($firmanteUno, $firmanteDos) = $firmantes;

        ## Obtenemos la clave que le pertenece al organismo para construir el numero de oficio
        $clave_org = DB::table('tbl_organismos')->where('id', $organismo)->where('activo', 'true')->value('clave');
        $clave_plane = DB::table('tbl_organismos')->where('id', 8)->where('activo', 'true')->value('clave'); //Planeacion organizacion y evaluación
        if(empty($clave_org) || empty($clave_plane)){
            return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', 'No se encontraron las claves del departamento.');
        }

        //Proceso para genera xml de ambos documentos
        if($tipo_efirma == 'META_ANUAL'){
            $nombre_archivo = $tipo_efirma;

            // $cont_sinhtml = strip_tags($cont_html); //contenido sin html
            // //Limpiar cadena
            // $del_html = preg_replace('/@page\s*\{.*?\}\s*\/\*.*?\*\/|\.tb\s*\{.*?\}|\#titulo\s*\{.*?\}|\.tablaf\s*\{.*?\}|\.showlast\s*\{.*?\}|\.showborders\s*\{.*?\}|\.prueba\s*\{.*?\}|\.direccion\s*\{.*?\}|\.mielemento\s*\{.*?\}|p\s*\{.*?\}|body\s*\{.*?\}|header\s*\{.*?\}|footer\s*\{.*?\}|if\s*\(\s*isset\(\$pdf\)\s*\)\s*\{.*?\}/s', '', $cont_sinhtml);
            // // Eliminar líneas en blanco o espacios innecesarios.
            // $del_espacios = preg_replace('/\s+/', ' ', $del_html);
            // //Elimina css restantes
            // $body = preg_replace('/[.#][\w\s-]+[\w\s,.]*\{[^}]*\}\s*/', '', $del_espacios);

            $nombre_reporte = 'Reporte PAT Meta Anual';

            //NO OFICIO
            $temp = $clave_plane.'-'.'CAPAT'.'-'.'F01'.'-'.$clave_org.'-'.'%'.'-'.$ejercicio;
            $totalFolios = DB::table('documentos_firmar')->where('num_oficio', 'LIKE', $temp)->where('status', 'CANCELADO ICTI')->count();
            // if($totalFolios != 1){$conta_folio = $totalFolios + 1;}else{$conta_folio = 1;}
            $conta_folio = $totalFolios + 1;
            $totalFolios = str_pad($conta_folio, 2, '0', STR_PAD_LEFT);
            $numOficio = $clave_plane.'-'.'CAPAT'.'-'.'F01'.'-'.$clave_org.'-'.$totalFolios.'-'.$ejercicio;
            $nameFileOriginal = 'reporte '.$nombre_oficio.'.pdf';
            $cont_html = $this->render_html('meta', $organismo, $numOficio); //html del pdf

            $body = $this->body_doc_efirma($organismo, $ejercicio, $tipo_efirma, $numOficio, '');

        }else if($tipo_efirma == 'AVANCE_MES'){ //avance_enero
            $nombre_archivo = $tipo_efirma.'_'.strtoupper($mesavance);

            // $cont_sinhtml = strip_tags($cont_html); //contenido sin html
            // $del_html = preg_replace('/@page\s*\{.*?\}\s*\/\*.*?\*\/|\.tb\s*\{.*?\}|\#titulo\s*\{.*?\}|\.tablaf\s*\{.*?\}|\.showlast\s*\{.*?\}|\.showborders\s*\{.*?\}|\.prueba\s*\{.*?\}|\.direccion\s*\{.*?\}|\.mielemento\s*\{.*?\}|p\s*\{.*?\}|body\s*\{.*?\}|header\s*\{.*?\}|footer\s*\{.*?\}|if\s*\(\s*isset\(\$pdf\)\s*\)\s*\{.*?\}/s', '', $cont_sinhtml);
            // $del_espacios = preg_replace('/\s+/', ' ', $del_html);
            // $body = preg_replace('/[.#][\w\s-]+[\w\s,.]*\{[^}]*\}\s*/', '', $del_espacios);

            $nombre_reporte = 'Reporte PAT Avance Mensual';

            $cut_mes = strtoupper(substr($mesavance, 0, 3));
            $temp = $clave_plane.'-'.'AMPAT'.'-'.'F02'.'-'.$clave_org.'-'.'%'.'-'.$cut_mes.'-'.$ejercicio;
            $totalFolios = DB::table('documentos_firmar')->where('num_oficio', 'LIKE', $temp)->where('status', 'CANCELADO ICTI')->count();
            // if($totalFolios != 1){$conta_folio = $totalFolios + 1;}else{$conta_folio = 1;}
            $conta_folio = $totalFolios + 1;
            $totalFolios = str_pad($conta_folio, 2, '0', STR_PAD_LEFT);
            $numOficio = $clave_plane.'-'.'AMPAT'.'-'.'F02'.'-'.$clave_org.'-'.$totalFolios.'-'.$cut_mes.'-'.$ejercicio;
            $nameFileOriginal = 'reporte '.$nombre_oficio.'.pdf';
            $cont_html = $this->render_html('avances_'.$mesavance, $organismo, $numOficio);

            $body = $this->body_doc_efirma($organismo, $ejercicio, $tipo_efirma, $numOficio, $mesavance);
        }

        ## Validar la curp y los correos de los firmantes
        if(empty($firmanteUno->curp) || empty($firmanteDos->curp) || empty($firmanteUno->correo) || empty($firmanteDos->correo)){
            return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', 'No se encontró la curp del firmante');
        }

        $numFirmantes = '2';
        $arrayFirmantes = [];

        //Llenado de funcionarios firmantes
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $firmanteUno->curp,
                'nombre_firmante' => $firmanteUno->funcionario,
                'email_firmante' => $firmanteUno->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);

        $temp = ['_attributes' =>
            [
                'curp_firmante' => $firmanteDos->curp,
                'nombre_firmante' => $firmanteDos->funcionario,
                'email_firmante' => $firmanteDos->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);


        ### XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $firmanteUno->funcionario,
                    'cargo_emisor' => $firmanteUno->cargo,
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas'
                    // 'curp_emisor' => $dataEmisor->curp
                ],
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal
                    // 'md5_archivo' => $md5
                    // 'checksum_archivo' => utf8_encode($text)
                ],
                // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
                'cuerpo' => [$body]
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' => [
                    $arrayFirmantes
                ]
            ],
        ];

        //Creacion de estampa de hora exacta de creacion
        $date = Carbon::now();
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        $result = ArrayToXml::convert($ArrayXml, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => '2.0',
                'fecha_creacion' => $dateFormat,
                'no_oficio' => $numOficio,
                'dependencia_origen' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                'asunto_docto' => $nombre_reporte,
                'tipo_docto' => 'OFC',
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);
        //Generacion de cadena unica mediante el ICTI
        $xmlBase64 = base64_encode($result);
        $getToken = Tokens_icti::all()->last();
        if ($getToken) {
            $response = $this->getCadenaOriginal($xmlBase64, $getToken->token);
            if ($response->json() == null) {
                $token = $this->generarToken();
                $response = $this->getCadenaOriginal($xmlBase64, $token);
            }
        } else {// no hay registros
            $token = $this->generarToken();
            $response = $this->getCadenaOriginal($xmlBase64, $token);
        }
        //Guardado de cadena unica
        if ($response->json()['cadenaOriginal'] != null) {

            try {
                ##VALIDAR ESTA CONSULTA CUANDO SEA META ANUAL Y AVANCE MENSUAL HASTA AQUI VAMOS AVANZANDO
                $id_efirm = '';
                if($tipo_efirma == 'META_ANUAL'){
                    $idEfirma = DB::table('fechas_pat as fp')
                    ->where('fp.id_org', $organismo)
                    ->where('fp.periodo', $ejercicio)
                    ->where('df.status', 'CANCELADO')
                    ->join('documentos_firmar as df', function ($join) {
                        $join->on(DB::raw("df.id::text"), '=', DB::raw("fp.fecha_meta->>'id_efirma'"));
                    })
                    ->selectRaw("fp.fecha_meta->>'id_efirma' as id_efirma")
                    ->value('id_efirma');

                }else if($tipo_efirma == 'AVANCE_MES'){
                    $idEfirma = DB::table('fechas_pat as fp')
                    ->where('fp.id_org', $organismo)
                    ->where('fp.periodo', $ejercicio)
                    ->where('df.status', 'CANCELADO')
                    ->join('documentos_firmar as df', function ($join) use ($mesavance) {
                        $join->on(DB::raw("df.id::text"), '=', DB::raw("fp.fechas_avance->'$mesavance'->>'id_efirma'"));
                    })
                    ->selectRaw("fp.fechas_avance->'$mesavance'->>'id_efirma' as id_efirma")
                    ->value('id_efirma');
                }

                // dd($tipo_efirma,$id_efirm);
                $dataInsert = '';
                if ($idEfirma) {
                    $dataInsert = DocumentosFirmar::where('id', $idEfirma)->where('status', 'CANCELADO')->first();
                }
                if(empty($dataInsert)) {
                    $dataInsert = new DocumentosFirmar();
                }

                $dataInsert->obj_documento = json_encode($ArrayXml); //Doc en json
                // $dataInsert->obj_documento_interno = $cont_html;
                $dataInsert->body_html = $cont_html;
                $dataInsert->status = 'EnFirma';
                $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
                $dataInsert->tipo_archivo = 'Reporte PAT';
                $dataInsert->numero_o_clave = $organismo; //id del organismo
                $dataInsert->nombre_archivo = $nombre_archivo; //Tipor de reporte (META_ANUAL / AVANCE_MES_ENERO)
                $dataInsert->documento = $result; //Doc en xml
                $dataInsert->num_oficio = $numOficio;
                $dataInsert->save();
                // $dataInsert->documento_interno = $result; // Opcional para agregar otro dato

                $idNew = $dataInsert->id;

                $idfechaPat = FechasPat::where('id_org', '=', $organismo)->where('periodo', '=', $ejercicio)->value('id');
                if($idfechaPat){
                    $fecPat = FechasPat::find($idfechaPat);
                    if($tipo_efirma == 'META_ANUAL'){
                        $campo = $fecPat->fecha_meta;
                        $campo['mod_documento'] = 'efirma';
                        $campo['status_efirma'] = 'EnFirma';
                        $campo['id_efirma'] = $idNew;
                        $fecPat->fecha_meta = $campo;

                    }else if($tipo_efirma == 'AVANCE_MES'){
                        $campo = $fecPat->fechas_avance;
                        $campo[$mesavance]['mod_documento'] = 'efirma';
                        $campo[$mesavance]['status_efirma'] = 'EnFirma';
                        $campo[$mesavance]['id_efirma'] = $idNew;
                        $fecPat->fechas_avance = $campo;
                    }
                    $fecPat->save();

                }

            } catch (\Throwable $th) {
                // return "ERROR AL GUARDAR LOS DATOS EN LA TABLA: ".$th->getMessage();
                return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', '¡Error al guardar los datos! '.$th->getMessage() );
            }

        } else {
            // return "ERROR AL ENVIAR Y VALIDAR. INTENTE NUEVAMENTE EN UNOS MINUTOS";
            return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', '¡Error al enviar y validad. Intente nuevamente en uno minutos!');
        }

        // return redirect()->route('pat.metavance.mostrar');
        return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', '¡Documento generado de manera exitosa!');

    }

    private function body_doc_efirma($organismo, $ejercicio, $tipo_efirma, $num_oficio, $mes_avance) {
        ## Prueba para realizar el cuerpo xml de reporte meta anual.
        $nom_direc_depto = DB::table('tbl_organismos as o')
        ->join('tbl_organismos as p', 'o.id_parent', '=', 'p.id')
        ->where('o.id', $organismo)
        ->select('p.nombre as padre', 'o.nombre as hijo')
        ->first();

        if($tipo_efirma == 'META_ANUAL'){
            $results_meta = DB::table('metas_avances_pat as metas')
            ->join('funciones_proced as proced', 'metas.id_proced', '=', 'proced.id')
            ->join('funciones_proced as func', 'func.id', '=', 'proced.id_parent')
            ->join('unidades_medida as um', 'um.id', '=', 'proced.id_unidadm')
            ->where('proced.id_org', $organismo)
            ->where('metas.ejercicio', $ejercicio)
            ->select(
                'func.fun_proc as funcion',
                'proced.fun_proc as actividad',
                'um.numero',
                'um.unidadm',
                'um.tipo_unidadm',
                'metas.total as meta_anual',
                DB::raw("metas.enero->>'meta' as meta_enero"),
                DB::raw("metas.febrero->>'meta' as meta_febrero"),
                DB::raw("metas.marzo->>'meta' as meta_marzo"),
                DB::raw("metas.abril->>'meta' as meta_abril"),
                DB::raw("metas.mayo->>'meta' as meta_mayo"),
                DB::raw("metas.junio->>'meta' as meta_junio"),
                DB::raw("metas.julio->>'meta' as meta_julio"),
                DB::raw("metas.agosto->>'meta' as meta_agosto"),
                DB::raw("metas.septiembre->>'meta' as meta_septiembre"),
                DB::raw("metas.octubre->>'meta' as meta_octubre"),
                DB::raw("metas.noviembre->>'meta' as meta_noviembre"),
                DB::raw("metas.diciembre->>'meta' as meta_diciembre"),
                'metas.observmeta'
                )
            ->orderBy('proced.id', 'ASC')
            ->get();

            $body = "SUBSECRETARÍA DE EDUCACIÓN MEDIA SUPERIOR\n".
            "DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO\n".
            "CALENDARIZADO ANUAL PROGRAMÁTICO DEL PROGRAMA ANUAL DE TRABAJO ".$ejercicio."\n".
            "INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS.\n\n".
            "\n\n Dirección: ". $nom_direc_depto->padre.
            "\n Departamento: ". $nom_direc_depto->hijo.
            "\n Oficio: ". $num_oficio."\n\n";


            $body .= "No FUN |  FUNCIONES |  ACTIVIDADES |  UNIDAD DE MEDIDA  |  TIPO DE U.M  |   META ANUAL  |  CALENDARIZACION (I II III IV V VI VII VIII IX X XI XII) |  OBSERVACIÓN \n\n";

            $lastFuncion = null;
            $funcionCounter = 0;
            foreach ($results_meta as $key => $item) {
                if ($item->funcion !== $lastFuncion) {
                    $funcionCounter++;
                    $body .= "\n\n".$funcionCounter.'. '.$item->funcion."\n";
                    $lastFuncion = $item->funcion;
                } else {
                    // Si es el mismo, no se agrega 'funcion' y solo se deja en blanco
                    // $body .= ' | ';
                }
                $body .= $item->actividad.' | '. '('.$item->numero.')'.$item->unidadm.' | '.$item->tipo_unidadm.' | '.$item->meta_anual.' | '.
                    $item->meta_enero.' | '.$item->meta_febrero.' | '.$item->meta_marzo.' | '.$item->meta_abril.' | '.$item->meta_mayo.' | '.$item->meta_junio.' | '.$item->meta_julio.' | '.
                    $item->meta_agosto.' | '.$item->meta_septiembre.' | '.$item->meta_octubre.' | '.$item->meta_noviembre.' | '.$item->meta_diciembre.' | '.$item->observmeta."\n";
            }

            return $body;

        }else if($tipo_efirma == 'AVANCE_MES'){ //AVANCES
            $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            $mes = $mes_avance; // El mes que estás procesando

            $selectFields = [
                'func.fun_proc as funcion',
                'proced.fun_proc as actividad',
                'um.numero',
                'um.unidadm',
                'um.tipo_unidadm',
                'metas.total as meta_anual',
                DB::raw("$mes->>'meta' AS meta_mes"),  // Se utiliza la variable $mes
                DB::raw("$mes->>'avance' AS avance_mes"),
                DB::raw("(($mes->>'avance')::numeric - ($mes->>'meta')::numeric) AS resta_mes"),
                DB::raw("ROUND((($mes->>'avance')::numeric - ($mes->>'meta')::numeric) * 100 / NULLIF(($mes->>'meta')::numeric, 0), 4) AS desviacion_mes"),
                DB::raw("$mes->>'expdesviaciones' AS mensaje_desv")
            ];

            $acumMeta = [];
            $acumAvance = [];

            // Ciclo para crear las acumulaciones
            foreach ($meses as $index => $nombreMes) {
                if ($index > array_search($mes, $meses)) {
                    break; // Si ya llegamos al mes actual, salir del ciclo
                }
                $acumMeta[] = "($nombreMes->>'meta')::numeric";
                $acumAvance[] = "($nombreMes->>'avance')::numeric";
            }
            // Generar las consultas acumulativas
            $selectFields[] = DB::raw("SUM(" . implode(' + ', $acumMeta) . ") AS acum_meta");
            $selectFields[] = DB::raw("SUM(" . implode(' + ', $acumAvance) . ") AS acum_avance");
            $selectFields[] = DB::raw("(SUM(" . implode(' + ', $acumAvance) . ") - SUM(" . implode(' + ', $acumMeta) . ")) AS resta_acum");
            $selectFields[] = DB::raw("ROUND((SUM(" . implode(' + ', $acumAvance) . ") - SUM(" . implode(' + ', $acumMeta) . ")) * 100 / NULLIF(SUM(" . implode(' + ', $acumMeta) . "), 0), 4) AS desviacion_acum");

            $results_avance = DB::table('metas_avances_pat as metas')
            ->join('funciones_proced as proced', 'metas.id_proced', '=', 'proced.id')
            ->join('funciones_proced as func', 'func.id', '=', 'proced.id_parent')
            ->join('unidades_medida as um', 'um.id', '=', 'proced.id_unidadm')
            ->select($selectFields)
            ->where('proced.id_org', '=', $organismo)
            ->where('metas.ejercicio', '=', $ejercicio)
            ->groupBy(
                'proced.id',
                'func.fun_proc',
                'proced.fun_proc',
                'um.numero',
                'um.unidadm',
                'um.tipo_unidadm',
                'metas.total',
                DB::raw("$mes->>'meta'"),
                DB::raw("$mes->>'avance'"),
                DB::raw("$mes->>'expdesviaciones'")
            )
            ->orderBy('proced.id', 'ASC')
            ->get();


            //Creacion del cuerpo
            $body = "SUBSECRETARÍA DE EDUCACIÓN MEDIA SUPERIOR\n".
            "DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO\n".
            "INFORME MENSUAL DE AVANCE PROGRAMÁTICO DEL PROGRAMA ANUAL DE TRABAJO ".$ejercicio."\n".
            "INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS.\n\n".
            "\n\n Dirección: ". $nom_direc_depto->padre.
            "\n Departamento: ". $nom_direc_depto->hijo.
            "\n Oficio: ". $num_oficio."\n\n";

            $body .= "No FUN |  FUNCIONES |  ACTIVIDADES |  UNIDAD DE MEDIDA  |  TIPO DE U.M  |   META ANUAL  |  MES QUE INFORMA (PROGRAMADO | ALCANZADO | NUM | %) | ACUMULADO (PROGRAMADO | ALCANZADO | NUMERO | %) | EXPLICACIÓN A LAS DESVIACIONES \n\n";

            $lastFuncion = null;
            $funcionCounter = 0;
            foreach ($results_avance as $key => $item) {
                if ($item->funcion !== $lastFuncion) {
                    $funcionCounter++;
                    $body .= "\n\n".$funcionCounter.'. '.$item->funcion."\n";
                    $lastFuncion = $item->funcion;
                } else {
                    //No hacer nada
                }
                $body .= $item->actividad.' | '. '('.$item->numero.')'.$item->unidadm.' | '.$item->tipo_unidadm.' | '.$item->meta_anual.' | '.
                    $item->meta_mes.' | '.$item->avance_mes.' | '.$item->resta_mes.' | '.$item->desviacion_mes.' | '.$item->acum_meta.' | '.$item->acum_avance.' | '.$item->resta_acum.' | '.
                    $item->desviacion_acum.' | '.$item->mensaje_desv."\n";
            }

            return $body;
        }

    }

    ## DATOS DE FIRMANTES
    private function datos_firmantes($organismo, $tipo_doc){

        //Consultamos si el organismo es direccion o departamento
        $id_dpto_direc = DB::table('tbl_organismos as o')
        ->join('tbl_organismos as p', 'o.id_parent', '=', 'p.id')
        ->where('o.id', $organismo)
        ->select('p.id as id_direccion', 'o.id as id_depto')
        ->first();

        if($id_dpto_direc->id_direccion == 1){ //Auxiliar
            if($tipo_doc == 'efirma'){
                //Obtener el id Auth y buscar en en tbl_funcionarios por medio del correo
                $dataUno = DB::table('tbl_funcionarios as fun')->select('fun.cargo','fun.nombre as funcionario', 'fun.correo', 'fun.curp', 'fun.incapacidad', 'fun.titulo')
                ->join('users as us', 'us.email', '=', 'fun.correo')
                ->where('fun.activo', 'true')->where('fun.correo', Auth::user()->email)->first();
                if(empty($dataUno)){return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', 'El usuario '.Auth::user()->name.' no esta dado de alta para firmar de manera electronica');}

            }else if($tipo_doc == 'tradicional'){
                //Buscamos primero en la tabla funcionarios, si no hay entonces con los datos de incio de sesion
                $dataUno = DB::table('tbl_funcionarios as fun')->select('fun.cargo','fun.nombre as funcionario', 'fun.correo', 'fun.curp', 'fun.incapacidad', 'fun.titulo')
                ->join('users as us', 'us.email', '=', 'fun.correo')
                ->where('fun.activo', 'true')->where('fun.correo', Auth::user()->email)->first();

                if(empty($dataUno)){
                    $dataUno = DB::table('users')->select('name as funcionario', 'puesto as cargo', DB::raw("'' as titulo"))->where('email', Auth::user()->email)->first();
                    // $dataUno = array('funcionario'=>Auth::user()->name, 'puesto'=>Auth::user()->puesto);
                }
            }

            $dataDos = DB::table('tbl_organismos as o')->select('fun.cargo','fun.nombre as funcionario', 'fun.correo', 'fun.curp', 'fun.incapacidad', 'fun.titulo')
            ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')->where('o.id', $id_dpto_direc->id_depto)->where('fun.activo', 'true')->where('fun.titular', true)->first();


        }else{ //Buscar al funcionario del dpto
            $dataUno = DB::table('tbl_organismos as o')->select('fun.cargo','fun.nombre as funcionario', 'fun.correo', 'fun.curp', 'fun.incapacidad', 'fun.titulo')
            ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')->where('o.id', $id_dpto_direc->id_depto)->where('fun.activo', 'true')->where('fun.titular', true)->first();

            $dataDos = DB::table('tbl_organismos as o')->select('fun.cargo','fun.nombre as funcionario', 'fun.correo', 'fun.curp', 'fun.incapacidad', 'fun.titulo')
            ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')->where('o.id', $id_dpto_direc->id_direccion)->where('fun.activo', 'true')->where('fun.titular', true)->first();
        }

        if(!empty($dataUno->incapacidad)) {
            $incapacidadFirmante = $this->incapacidad(json_decode($dataUno->incapacidad), $dataUno->funcionario);
            if($incapacidadFirmante != false) {
                $dataUno = $incapacidadFirmante;
            }
        }

        if(!empty($dataDos->incapacidad)) {
            $incapacidadFirmante = $this->incapacidad(json_decode($dataDos->incapacidad), $dataDos->funcionario);
            if($incapacidadFirmante != false) {
                $dataDos = $incapacidadFirmante;
            }
        }

        if(empty($dataUno) || empty($dataDos) ){
            return redirect()->route('pat.metavance.mostrar', ['idorg' => $organismo])->with('message', 'Error en la busqueda de firmantes');
        }

        return $data = [$dataUno, $dataDos];

    }

    ##Generar token para efirma
    public function generarToken() {
        ##Producción
        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            'nombre' => 'SISTEM_IVINCAP',
            'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
        ]);

        ##Prueba
        // $resToken = Http::withHeaders([
        //     'Accept' => 'application/json'
        // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
        //     'nombre' => 'FirmaElectronica',
        //     'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
        // ]);

        $token = $resToken->json();
        Tokens_icti::create(['token' => $token]);
        return $token;
    }

    ## obtener la cadena original para efirma
    public function getCadenaOriginal($xmlBase64, $token) {
        ##Produccion
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        ##Prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        return $response1;
    }

    public function render_html($accion, $idorg, $num_oficio){
         //Obtenemos los id org, area del usuario quien ingresa
         try {
            if ($idorg != 'null') {
                $organismo = $idorg;
            }else {
                // $organismo = $_SESSION['id_organsmog'];
                $organismo = $_SESSION['id_organsmog'];
            }
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        //MOSTRAR FECHA
        $mesGlob = $this->arrayMes;
        $obtMes = intval(date('m')); $obtAnio = date('Y'); $obtDia =  date("d");
        $fechaNow =  $obtDia.' de '.$mesGlob[$obtMes-1].' del '.$obtAnio;

        $nom_direc_depto = DB::table('tbl_organismos as o')
        ->join('tbl_organismos as p', 'o.id_parent', '=', 'p.id')
        ->where('o.id', $organismo)
        ->select('p.nombre as direccion', 'o.nombre as depto')
        ->first();

        //CONSULTA DE FUNCIONES
        $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();

        ##Validamos las variables globales de planeacion y area normal
        $global_ejercicio = strval(date('Y'));
        if (isset($_SESSION['eje_pat_buzon'])){
            $global_ejercicio = $_SESSION['eje_pat_buzon'];
        }else if(isset($_SESSION['eje_pat_registros'])){
            $global_ejercicio = $_SESSION['eje_pat_registros'];
        }
        //CONSULTA DE PROCEDIMIENTOS POR FUNCION
        $procedimientos = [];
        for ($i=0; $i < count($funciones); $i++) {
            $val =  $funciones[$i]['id'];

            $proced = RegistrosProced::select('metas_avances_pat.id','metas_avances_pat.id_proced' ,'f.fun_proc', 'metas_avances_pat.total', 'metas_avances_pat.enero',
            'metas_avances_pat.febrero', 'metas_avances_pat.marzo', 'metas_avances_pat.abril', 'metas_avances_pat.mayo', 'metas_avances_pat.junio', 'metas_avances_pat.julio', 'metas_avances_pat.agosto',
            'metas_avances_pat.septiembre', 'metas_avances_pat.octubre', 'metas_avances_pat.noviembre', 'metas_avances_pat.diciembre', 'observaciones', 'observmeta', 'um.numero', 'um.unidadm', 'um.tipo_unidadm')
            ->Join('funciones_proced as f', 'f.id', 'metas_avances_pat.id_proced')
            ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
            ->where('metas_avances_pat.ejercicio', '=', $global_ejercicio)
            ->whereIn('f.id', function($query)use($val, $obtAnio)  {
            $query->select('id')
                  ->from('funciones_proced')
                  ->where('id_parent', '=', $val)
                  ->where('activo', '=', 'true'); //validar si esta activo
                //   ->where(DB::raw("date_part('year' , created_at )"), '=', '2023');
            })
            ->orderBy('f.id')
            ->get();

            array_push($procedimientos, $proced);
        }

        //Funcion Generar porcentaje
        $porcentaje = function($resta, $meta) {
            $multi = $resta * 100;
            $num_div = $meta == 0 ? $num_div = 1 : $num_div = $meta;
            $num_div = $multi / $num_div;
            return $num_div;
        };

        //Condiciones meses obtener avances con las acumulaciones
        $condiciones_meses = function($mes, $i, $e)use($procedimientos){
            $response = [];
            switch ($mes) {
                case 'enero':
                    $desviaciones = $procedimientos[$i][$e]->enero['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->enero['fechavasave'];
                    $meta_info = (int) $procedimientos[$i][$e]->enero['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->enero['avance'];
                    //Suma como es enero no hay nada que sumar
                    $meta_acum = $meta_info;
                    $avance_acum = $avance_info;
                    break;

                case 'febrero':
                    $desviaciones = $procedimientos[$i][$e]->febrero['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->febrero['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $meta_acum = $meta_info + $enero_meta;
                    $avance_acum = $avance_info + $enero_avance;

                    break;
                case 'marzo':
                    $desviaciones = $procedimientos[$i][$e]->marzo['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->marzo['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $meta_acum = $meta_info + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $febrero_avance + $enero_avance;
                    break;

                case 'abril':
                    $desviaciones = $procedimientos[$i][$e]->abril['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->abril['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->abril['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->abril['avance'];
                    $meta_acum = $meta_info + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'mayo':
                    $desviaciones = $procedimientos[$i][$e]->mayo['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->mayo['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $meta_acum = $meta_info + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'junio':
                    $desviaciones = $procedimientos[$i][$e]->junio['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->junio['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->junio['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->junio['avance'];
                    $meta_acum = $meta_info + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'julio':
                    $desviaciones = $procedimientos[$i][$e]->julio['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->julio['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->julio['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->julio['avance'];
                    $meta_acum = $meta_info + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'agosto':
                    $desviaciones = $procedimientos[$i][$e]->agosto['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->agosto['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $meta_acum = $meta_info + $julio_meta + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $julio_avance + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'septiembre':
                    $desviaciones = $procedimientos[$i][$e]->septiembre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->septiembre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $meta_acum = $meta_info + $agosto_meta + $julio_meta + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $agosto_avance + $julio_avance + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'octubre':
                    $desviaciones = $procedimientos[$i][$e]->octubre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->octubre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $sep_meta = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->octubre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->octubre['avance'];
                    $meta_acum = $meta_info + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                 $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                 $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'noviembre':
                    $desviaciones = $procedimientos[$i][$e]->noviembre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->noviembre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $sep_meta = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $oct_meta = (int) $procedimientos[$i][$e]->octubre['meta'];
                    $oct_avance = (int) $procedimientos[$i][$e]->octubre['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->noviembre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->noviembre['avance'];
                    $meta_acum = $meta_info + $oct_meta + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                    $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $oct_avance + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                    $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'diciembre':
                    $desviaciones = $procedimientos[$i][$e]->diciembre['expdesviaciones'];
                    $fecha_guardado = $procedimientos[$i][$e]->diciembre['fechavasave'];
                    $enero_meta = (int) $procedimientos[$i][$e]->enero['meta'];
                    $enero_avance = (int) $procedimientos[$i][$e]->enero['avance'];
                    $febrero_meta = (int) $procedimientos[$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $procedimientos[$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $procedimientos[$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $procedimientos[$i][$e]->marzo['avance'];
                    $abril_meta = (int) $procedimientos[$i][$e]->abril['meta'];
                    $abril_avance = (int) $procedimientos[$i][$e]->abril['avance'];
                    $mayo_meta = (int) $procedimientos[$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $procedimientos[$i][$e]->mayo['avance'];
                    $junio_meta = (int) $procedimientos[$i][$e]->junio['meta'];
                    $junio_avance = (int) $procedimientos[$i][$e]->junio['avance'];
                    $julio_meta = (int) $procedimientos[$i][$e]->julio['meta'];
                    $julio_avance = (int) $procedimientos[$i][$e]->julio['avance'];
                    $agosto_meta = (int) $procedimientos[$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $procedimientos[$i][$e]->agosto['avance'];
                    $sep_meta = (int) $procedimientos[$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $procedimientos[$i][$e]->septiembre['avance'];
                    $oct_meta = (int) $procedimientos[$i][$e]->octubre['meta'];
                    $oct_avance = (int) $procedimientos[$i][$e]->octubre['avance'];
                    $nov_meta = (int) $procedimientos[$i][$e]->noviembre['meta'];
                    $nov_avance = (int) $procedimientos[$i][$e]->noviembre['avance'];
                    $meta_info = (int) $procedimientos[$i][$e]->diciembre['meta'];
                    $avance_info = (int) $procedimientos[$i][$e]->diciembre['avance'];
                    $meta_acum = $meta_info + $nov_meta + $oct_meta + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                    $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $nov_avance + $oct_avance + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                    $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                default:
                    # code...
                    break;
                }

                return $response = ["meta_info" => $meta_info, "avance_info" => $avance_info,
                                    "meta_acum" => $meta_acum, "avance_acum" => $avance_acum,
                                    "fecha_guardado" => $fecha_guardado, "exp_desviacion" => $desviaciones];

        };


        $separador = explode("_", $accion); //contiene (meta)(avance_'mes')

        if($separador[0] == 'meta'){
            $html_content = View::make('vistas_pat.pdf_render', compact('funciones', 'procedimientos', 'global_ejercicio', 'num_oficio', 'nom_direc_depto'))->render();
            return $html_content;
        }

        //Efirma de avances
        else if($separador[0] == 'avances'){

            //Realiza todo el proceso de ir sumando mes con mes
            $mes_meta_avance = [];
            $mes_avance = "";
            for ($i=0; $i < count($funciones) ; $i++) {
                $arrayacum = [];
                    for ($e=0; $e < count($procedimientos[$i]); $e++) {
                        foreach ($mesGlob as $mes) {
                            if ($separador[1] == $mes) {
                                $resul = $condiciones_meses($separador[1], $i, $e);
                                //fecha mes avance
                                $mes_avance = $resul['fecha_guardado'];
                                $explic_desviacion = $resul['exp_desviacion'];
                                //info
                                $resta_info = ($resul["avance_info"] - $resul["meta_info"]);
                                $porcentaje_info = $porcentaje($resta_info, $resul["meta_info"]);
                                //acum
                                $resta_acum = ($resul["avance_acum"] - $resul["meta_acum"]);
                                $porcentaje_acum = $porcentaje($resta_acum, $resul["meta_acum"]);

                                $acum = ["meta" => $resul["meta_info"], "avance" => $resul["avance_info"], "resta" => $resta_info,
                                        "porcentaje" => round($porcentaje_info, 4), "metas_acum" => $resul["meta_acum"], "avance_acum" => $resul["avance_acum"],
                                        "resta_acum" => $resta_acum, "porcentaje_acum" => round($porcentaje_acum, 4), "exp_desviacion" => $explic_desviacion];
                            }
                        }
                        array_push($arrayacum, $acum);
                    }
                    array_push($mes_meta_avance, $arrayacum);
            }

            // $fecha_enviar = '';
            $fecha_enviar = date("Y-m-d");

            //Convertir fechas
            $fech_carbon = Carbon::parse($fecha_enviar); // seperador[1] contiene el mes
            // $mes_avance = $mesGlob[$fech_carbon->month-1].'-'.$fech_carbon->day; // Obtenemos
            $mes_avance = $separador[1];
            $fecha_avance = $fech_carbon->format('d/m/Y');

            $html_content = View::make('vistas_pat.pdf_render_avance', compact('funciones', 'procedimientos', 'mes_meta_avance', 'global_ejercicio', 'mes_avance', 'num_oficio', 'nom_direc_depto'))->render();
            return $html_content;

        }
    }

    private function incapacidad($incapacidad, $incapacitado) {
        $fechaActual = now();
        if(!is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = DB::Table('tbl_funcionarios AS fun')->Select('fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo', 'fun.titulo', 'fun.incapacidad')
                    ->where('fun.id', $incapacidad->id_firmante)->where('fun.activo', 'true')
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

    ##Firmar de manera electronica
    public function firmar_documento(Request $request){
        // dd($request->all());
        $documento = DocumentosFirmar::where('id', $request->idDoc)->first();

        $obj_documento = json_decode($documento->obj_documento, true);

        if (empty($obj_documento['archivo']['_attributes']['md5_archivo'])) {
            $obj_documento['archivo']['_attributes']['md5_archivo'] = $documento->md5_file;
        }

        foreach ($obj_documento['firmantes']['firmante'][0] as $key => $value) {
            if ($value['_attributes']['curp_firmante'] == $request->curp) {
                $value['_attributes']['fecha_firmado_firmante'] = $request->fechaFirmado;
                $value['_attributes']['no_serie_firmante'] = $request->serieFirmante;
                $value['_attributes']['firma_firmante'] = $request->firma;
                $value['_attributes']['certificado'] = $request->certificado;
                $obj_documento['firmantes']['firmante'][0][$key] = $value;
            }
        }

        $array = XmlToArray::convert($documento->documento);
        $array['DocumentoChis']['firmantes'] = $obj_documento['firmantes'];

        $result = ArrayToXml::convert($obj_documento, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => $array['DocumentoChis']['_attributes']['version'],
                'fecha_creacion' => $array['DocumentoChis']['_attributes']['fecha_creacion'],
                'no_oficio' => $array['DocumentoChis']['_attributes']['no_oficio'],
                'dependencia_origen' => $array['DocumentoChis']['_attributes']['dependencia_origen'],
                'asunto_docto' => $array['DocumentoChis']['_attributes']['asunto_docto'],
                'tipo_docto' => $array['DocumentoChis']['_attributes']['tipo_docto'],
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);

        DocumentosFirmar::where('id', $request->idDoc)
            ->update([
                'obj_documento' => json_encode($obj_documento),
                'documento' => $result,
            ]);

        // return redirect()->route('firma.inicio')->with('warning', 'Documento firmado exitosamente!');
        // return back()->with('message', '¡Documento firmado exitosamente!');
        return redirect()->route('pat.metavance.mostrar', ['idorg' => $documento->numero_o_clave])
                 ->with('message', '¡Documento firmado de manera exitosa!');


    }

    public function show_pdf_efirma($id_registro){
        $cadena_html_meta  = $qrCodeBase64 = $uuid = $cadena_sello = $fecha_sello = $no_oficio = '';
        $firmantes = [];
        $id_organismo = $_SESSION['id_organsmog'];
        // dd($_SESSION['id_organsmog']);
        $ids_org = DB::table('tbl_organismos as o')
        ->join('tbl_organismos as p', 'o.id_parent', '=', 'p.id')
        ->where('o.id', $id_organismo)
        ->select('p.id as id_direccion', 'o.id as id_depto')
        ->first();

        ##Consulta de firma electronica
        $firma_electronica = DocumentosFirmar::where('id', $id_registro)->whereIn('status', ['EnFirma', 'VALIDADO'])->first();

        if(!empty($firma_electronica)){
            if($firma_electronica->status == 'VALIDADO'){
                $objeto = json_decode($firma_electronica->obj_documento,true);
                $no_oficio = $firma_electronica->num_oficio;
                $uuid = $firma_electronica->uuid_sellado;
                $cadena_sello = $firma_electronica->cadena_sello;
                $fecha_sello = $firma_electronica->fecha_sellado;
                // $folio = $firma_electronica->nombre_archivo;
                // $tipo_archivo = $firma_electronica->tipo_archivo;
                // $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];

                $curpUser1 = $objeto['firmantes']['firmante'][0][0]['_attributes']['curp_firmante'];
                $curpUser2 = $objeto['firmantes']['firmante'][0][1]['_attributes']['curp_firmante'];
                $emailUser1 = $objeto['firmantes']['firmante'][0][0]['_attributes']['email_firmante'];
                $emailUser2 = $objeto['firmantes']['firmante'][0][1]['_attributes']['email_firmante'];

                //Nuevo algoritmo de busqueda de funcionarios
                if($ids_org->id_direccion == 1){
                    if($curpUser1 == $curpUser2){
                        //Si un usuario tiene a cargo dos deparamentos
                        $puesto_firmUno = DB::table('tbl_funcionarios')->where('curp', '=', $curpUser1)->where('activo', 'true')->where('id_org', '!=', $ids_org->id_depto)->value('cargo');
                        $puesto_firmDos = DB::table('tbl_funcionarios')->where('curp', '=', $curpUser2)->where('activo', 'true')->where('id_org', $ids_org->id_depto)->value('cargo');
                    }else{
                        $puesto_firmUno = DB::table('tbl_funcionarios')->where('curp', '=', $curpUser1)->where('activo', 'true')->value('cargo');
                        $puesto_firmDos = DB::table('tbl_funcionarios')->where('curp', '=', $curpUser2)->where('activo', 'true')->value('cargo');
                    }
                }else{
                    $puesto_firmUno = DB::table('tbl_funcionarios')->where('curp', '=', $curpUser1)->where('activo', 'true')->value('cargo');
                    $puesto_firmDos = DB::table('tbl_funcionarios')->where('curp', '=', $curpUser2)->where('activo', 'true')->value('cargo');
                }

                if(empty($puesto_firmUno) || empty($puesto_firmDos)){
                    return back()->with('message', '¡No se encontraron los datos del los funcionarios!');
                }

                $arrayfirmantes  = $objeto['firmantes']['firmante'][0];
                foreach ($arrayfirmantes as $key => $value) {
                    $nombre = $value['_attributes']['nombre_firmante'];
                    $firma = $value['_attributes']['firma_firmante'];
                    $fechafirm = $value['_attributes']['fecha_firmado_firmante'];
                    $seriefirm = $value['_attributes']['no_serie_firmante'];
                    if($key == 0) $puesto = $puesto_firmUno;
                    else $puesto = $puesto_firmDos;
                    $firmantes[] = ['nombre' => $nombre,'firma' => $firma,'fecha_firma' => $fechafirm,'serie' => $seriefirm, 'puesto' => $puesto];
                }


                //Generacion de QR
                // $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                ob_start();
                QRcode::png($verificacion);
                $qrCodeData = ob_get_contents();
                ob_end_clean();
                $qrCodeBase64 = base64_encode($qrCodeData);

            }
            $cadena_html_meta = $firma_electronica->body_html;
            $pdf = PDF::loadView('vistas_pat.pdf_efirma_pat', compact('cadena_html_meta', 'firmantes', 'qrCodeBase64', 'uuid', 'cadena_sello', 'fecha_sello', 'no_oficio'));
            $pdf->setpaper('letter', 'landscape');
            return $pdf->stream('PAT-ICATECH-002.pdf');

        }else{
            return back()->with('message', '¡No se encuentra el documento!');
        }
    }

    public function sellar_documento(Request $request){

        $organismo = $request->input('organismo');
        $ejercicio = $request->input('ejercicio');
        $tipo_documento = $request->input('tipo_doc');
        $documento = DocumentosFirmar::where('id', $request->input('txtIdFirmado'))->first();
        $xmlBase64 = base64_encode($documento->documento);

        $getToken = Tokens_icti::latest()->first();

        $response = $this->sellarFile($xmlBase64, $getToken->token);
        if ($response->json() == null) {
            $request = new Request();
            $token = $this->generarToken($request);
            $response = $this->sellarFile($xmlBase64, $token);
        }
        if ($response->json()['status'] == 1) { //exitoso

            try {
                $decode = base64_decode($response->json()['xml']);
                DocumentosFirmar::where('id', $documento->id)
                    ->update([
                        'status' => 'VALIDADO',
                        'uuid_sellado' => $response->json()['uuid'],
                        'fecha_sellado' => $response->json()['fecha_Sellado'],
                        'documento' => $decode,
                        'cadena_sello' => $response->json()['cadenaSello']
                    ]);

                //Actualizamos status en fechas_pat
                if(!empty($tipo_documento)){
                    if($tipo_documento == 'meta'){
                        DB::table('fechas_pat')
                        ->where('id_org', $organismo)
                        ->where('periodo', $ejercicio)
                        ->update([
                            'fecha_meta' => DB::raw("jsonb_set(fecha_meta, '{status_efirma}', '\"validado\"'::jsonb)")
                        ]);

                    }else{
                        DB::table('fechas_pat')
                        ->where('id_org', $organismo)
                        ->where('periodo', $ejercicio)
                        ->update([
                            'fechas_avance' => DB::raw("jsonb_set(fechas_avance, '{\"" . $tipo_documento . "\", \"status_efirma\"}', '\"validado\"'::jsonb)")
                        ]);
                    }
                }
                return back()->with('message', '¡Documento sellado exitosamente!');

            } catch (\Throwable $th) {
                return back()->with('message', 'Error: '.$th->getMessage());
            }

        } else {
            $respuesta_icti = [
                'uuid' => $response->json()['uuid'],
                'descripcion' => $response->json()['descripcionError']
            ];
            $message = 'UUID: ' . $respuesta_icti['uuid'] . ', Descripción: ' . $respuesta_icti['descripcion'];
            return back()->with('message', $message);

            // $respuesta_icti = ['uuid' => $response->json()['uuid'], 'descripcion' => $response->json()['descripcionError']];
            // return back()->with('message', $respuesta_icti);
        }
    }

    public function sellarFile($xml, $token) {
        // Sellado de producción
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
            'xml_Firmado' => $xml
        ]);

        // Sellado de prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
        //     'xml_Firmado' => $xml
        // ]);
        return $response1;
    }

}
