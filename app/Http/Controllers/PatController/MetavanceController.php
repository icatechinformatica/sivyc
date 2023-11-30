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

        return view('vistas_pat.metas_avances', compact('datos', 'datos_status_meta', 'fecha_meta_avance', 'datos_status_avance',
        'area_org', 'org', 'fechaNow', 'mesGlob', 'array_organismos', 'organismo', 'ejercicio', 'anio_eje'));
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
                  ->where('activo', '=', 'true')
                  ->where(DB::raw("date_part('year' , created_at )"), '=', '2023');
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

        return view('vistas_pat.metas_avances', compact('datos', 'datos_status_meta', 'fecha_meta_avance', 'datos_status_avance', 'area_org', 'org', 'fechaNow', 'dif_perfil', 'id_organismo', 'mesGlob'));
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

        //MOSTRAR FECHA
        $mesGlob = $this->arrayMes;
        $obtMes = intval(date('m')); $obtAnio = date('Y'); $obtDia =  date("d");
        $fechaNow =  $obtDia.' de '.$mesGlob[$obtMes-1].' del '.$obtAnio;


        //AREA DEL USUARIO / (AREA DPTO)
        $area_org = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as area_org', 'id_parent', 'fun.nombre as func', 'fun.cargo', 'fun.titulo')
        ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')
        ->where('o.id', $organismo)->first();


        // ORGANISMO DEL USUARIO / (DIRECCION)
        $org = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as org', 'fun.nombre as fun', 'fun.cargo', 'fun.titulo')
        ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')
        ->where('o.id', $area_org->id_parent)->first();

        // dd($area_org);
        #Validacion en caso de que sea dirección
        $firm_logueado =  array();
        if($area_org->id_parent == 1) $firm_logueado = array('user'=>Auth::user()->name, 'puesto'=>Auth::user()->puesto);


        //CONSULTA DE FUNCIONES
        $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();


        //CONSULTA DE PROCEDIMIENTOS POR FUNCION
        $procedimientos = [];
        for ($i=0; $i < count($funciones); $i++) {
            $val =  $funciones[$i]['id'];

            $proced = RegistrosProced::select('metas_avances_pat.id','metas_avances_pat.id_proced' ,'f.fun_proc', 'metas_avances_pat.total', 'metas_avances_pat.enero',
            'metas_avances_pat.febrero', 'metas_avances_pat.marzo', 'metas_avances_pat.abril', 'metas_avances_pat.mayo', 'metas_avances_pat.junio', 'metas_avances_pat.julio', 'metas_avances_pat.agosto',
            'metas_avances_pat.septiembre', 'metas_avances_pat.octubre', 'metas_avances_pat.noviembre', 'metas_avances_pat.diciembre', 'observaciones', 'observmeta', 'um.numero', 'um.unidadm', 'um.tipo_unidadm')
            ->Join('funciones_proced as f', 'f.id', 'metas_avances_pat.id_proced')
            ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
            ->where('metas_avances_pat.ejercicio', '=', $_SESSION['eje_pat_registros'])
            ->whereIn('f.id', function($query)use($val, $obtAnio)  {
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

        //Consulta de fechas
        $fechasPat = function ($organismo){
            $tblFechas = FechasPat::select('id', 'fechas_avance', 'fecha_meta')->where('id_org', '=', $organismo)
            ->where('periodo', '=', $_SESSION['eje_pat_registros'])->first();
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
            $tblFechas =  $fechasPat($organismo); #ejecutamos solo una vez en lugar de varias veces
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
            $pdf = PDF::loadView('vistas_pat.genpdfmeta', compact('area_org', 'org', 'funciones', 'procedimientos', 'fecha_meta', 'marca', 'firm_logueado'));
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
            $tblFechas =  $fechasPat($organismo); #Ejecutamos solo una vez
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

            $pdf = PDF::loadView('vistas_pat.genpdfavance', compact('area_org', 'org', 'funciones', 'procedimientos', 'mes_meta_avance', 'mes_avance', 'fecha_avance', 'marca', 'firm_logueado'));
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
