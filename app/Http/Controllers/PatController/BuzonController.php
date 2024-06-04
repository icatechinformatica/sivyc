<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelPat\FechasPat;
use App\Models\ModelPat\Organismos;
use App\Models\ModelPat\Metavance;
use App\Models\ModelPat\RegistrosProced;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use ZipArchive;

class BuzonController extends Controller
{
    protected $arrayMes;

    public function __construct()
    {
        session_start();

        $this->arrayMes = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre',
        'octubre', 'noviembre', 'diciembre'];
    }

    /**
    * Index vista principal
    */
    public function index(Request $request)
    {
        $mesGlob = $this->arrayMes;
        $mes = $request->sel_mes;
        $sel_status = $request->sel_status;
        $sel_meta = $request->sel_meta;
        $sel_eje = $request->sel_ejercicio;
        $ejercicio = [];
        for ($i=2023; $i <= intval(date('Y')); $i++) {array_push($ejercicio, $i);}

        if($sel_eje == null && isset($_SESSION['eje_pat_buzon']) == ''){
            $_SESSION['eje_pat_buzon'] = date('Y');
        }elseif($sel_eje != null){
            $_SESSION['eje_pat_buzon'] = $sel_eje;
        }
        $anio = $_SESSION['eje_pat_buzon'];


        $data = FechasPat::BusquedaStatus($sel_status, $mes, $sel_meta)
        ->select('fechas_pat.*', 'o.nombre', 'o.id_parent')
        ->Join('tbl_organismos as o', 'o.id', 'fechas_pat.id_org')
        ->where('periodo', '=', $anio)
        ->paginate(15, ['fechas_pat.*']);


        return view('vistas_pat.buzon_pat', compact('data', 'mesGlob', 'mes', 'sel_status', 'sel_meta', 'ejercicio', 'anio'));
    }

    /**
    * Generar pdf de todos los organismos
    */
    public function pdforg_direc($mes_get, $idorg_get)
    {
        //Obtenemos viariable global
        $global_ejercicio = strval(date('Y'));
        if (isset($_SESSION['eje_pat_buzon'])){
            $global_ejercicio = $_SESSION['eje_pat_buzon'];
        }
        //MOSTRAR FECHA
        $mesGlob = $this->arrayMes;
        $obtMes = intval(date('m')); $obtAnio = date('Y'); $obtDia =  date("d");
        $fechaNow =  $obtDia.' de '.$mesGlob[$obtMes-1].' del '.$obtAnio;

        ## OBTENEMOS LAS AREAS EN BASE AL ORGANISMO INDICADO
        $org_id = intval($idorg_get);
        $areas = DB::table('tbl_organismos as o')
        ->join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')
        ->select('o.id', 'o.id_parent', 'o.nombre as nom_dpto', 'fun.nombre as funcionario', 'fun.cargo')
        ->where('o.activo', '=', 'true')
        ->where(function($query) use ($org_id) {
            $query->where('o.id', '=', $org_id)
                ->orWhere('o.id_parent', '=', $org_id);
        })
        ->orderBy('o.id', 'asc')
        ->get();


        ## OBTENER EL NOMBRE DE FUNCIONARIO DEL ORGNISMO PADRE
        $contenedor = array();
        $direcciones = array();
        for ($i=0; $i < count($areas); $i++) {
            $contenedor = Organismos::select('o.id', 'o.nombre as nom_direc', 'fun.nombre as funcionario', 'fun.cargo')
            ->Join('tbl_funcionarios as fun', 'fun.id_org', 'o.id')
            ->from('tbl_organismos as o')
            ->where('o.id', '=', $areas[$i]->id_parent)
            ->first();
            array_push($direcciones, $contenedor);
        }

        #PROCESO PARA OBTENER LOS VALORES DE AVANCE FUNCIONES Y PROCEDIMIENTOS
        $funciones_global = [];
        $proced_global = [];
        for ($o = 0; $o < count($areas) ; $o ++) {
            #FUNCIONES
            $funciones = Metavance::select('id', 'id_parent', 'fun_proc')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $areas[$o]->id)
            ->where('activo', '=', 'true')
            ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderBy('funciones_proced.id')->get();

            array_push($funciones_global, $funciones);

            #PROCEDIMIENTOS POR FUNCIÓN
            $procedimientos = [];
            for ($i=0; $i < count($funciones); $i++) {
                $val =  $funciones[$i]['id'];

                $proced = RegistrosProced::select('map.id','map.id_proced' ,'f.fun_proc', 'map.total', 'map.enero',
                'map.febrero', 'map.marzo', 'map.abril', 'map.mayo', 'map.junio', 'map.julio', 'map.agosto',
                'map.septiembre', 'map.octubre', 'map.noviembre', 'map.diciembre', 'observaciones', 'observmeta', 'um.numero', 'um.unidadm', 'um.tipo_unidadm')
                ->Join('funciones_proced as f', 'f.id', 'map.id_proced')
                ->Join('unidades_medida as um', 'f.id_unidadm', 'um.id')
                ->where('map.ejercicio', '=', $global_ejercicio)
                ->from('metas_avances_pat as map')
                ->whereIn('f.id', function($query)use($val)  {
                $query->select('id')
                    ->from('funciones_proced')
                    ->where('id_parent', '=', $val)
                    ->where('activo', '=', 'true'); //validar si esta activo
                })
                ->orderBy('f.id')
                ->get();

                array_push($procedimientos, $proced);
            }
            array_push($proced_global, $procedimientos);
        }

        // dd($funciones_global[1][0]->fun_proc); #(organismo), (su funcion)
        // dd($proced_global[1][0][1]->fun_proc); #(organismo), (funcion perteneciente), (que procedimiento es)

        #OPERACIONES A REALIZAR PARA MOSTRARLAS EN LA TABLA
        ##GENERAR PORCENTAJE
        $porcentaje = function($resta, $meta) {
            $multi = $resta * 100;
            $num_div = $meta == 0 ? $num_div = 1 : $num_div = $meta;
            $num_div = $multi / $num_div;
            return $num_div;
        };

        ##CONDICIONES PARA OBTENER ACUMULACIONES POR MES DE AVANCES
        $condiciones_meses = function($mes, $g, $i, $e) use($proced_global){
            $response = [];
            switch ($mes) {
                case 'enero':
                    $desviaciones = $proced_global[$g][$i][$e]->enero['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->enero['fechavasave'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    //Suma como es enero no hay nada que sumar
                    $meta_acum = $meta_info;
                    $avance_acum = $avance_info;
                    break;

                case 'febrero':
                    $desviaciones = $proced_global[$g][$i][$e]->febrero['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->febrero['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $meta_acum = $meta_info + $enero_meta;
                    $avance_acum = $avance_info + $enero_avance;

                    break;
                case 'marzo':
                    $desviaciones = $proced_global[$g][$i][$e]->marzo['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->marzo['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $meta_acum = $meta_info + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $febrero_avance + $enero_avance;
                    break;

                case 'abril':
                    $desviaciones = $proced_global[$g][$i][$e]->abril['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->abril['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $meta_acum = $meta_info + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'mayo':
                    $desviaciones = $proced_global[$g][$i][$e]->mayo['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->mayo['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $meta_acum = $meta_info + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'junio':
                    $desviaciones = $proced_global[$g][$i][$e]->junio['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->junio['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $meta_acum = $meta_info + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'julio':
                    $desviaciones = $proced_global[$g][$i][$e]->julio['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->julio['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $junio_meta = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $junio_avance = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->julio['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->julio['avance'];
                    $meta_acum = $meta_info + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'agosto':
                    $desviaciones = $proced_global[$g][$i][$e]->agosto['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->agosto['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $junio_meta = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $junio_avance = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $julio_meta = (int) $proced_global[$g][$i][$e]->julio['meta'];
                    $julio_avance = (int) $proced_global[$g][$i][$e]->julio['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->agosto['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->agosto['avance'];
                    $meta_acum = $meta_info + $julio_meta + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $julio_avance + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'septiembre':
                    $desviaciones = $proced_global[$g][$i][$e]->septiembre['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->septiembre['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $junio_meta = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $junio_avance = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $julio_meta = (int) $proced_global[$g][$i][$e]->julio['meta'];
                    $julio_avance = (int) $proced_global[$g][$i][$e]->julio['avance'];
                    $agosto_meta = (int) $proced_global[$g][$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $proced_global[$g][$i][$e]->agosto['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->septiembre['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->septiembre['avance'];
                    $meta_acum = $meta_info + $agosto_meta + $julio_meta + $junio_meta + $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $agosto_avance + $julio_avance + $junio_avance + $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'octubre':
                    $desviaciones = $proced_global[$g][$i][$e]->octubre['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->octubre['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $junio_meta = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $junio_avance = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $julio_meta = (int) $proced_global[$g][$i][$e]->julio['meta'];
                    $julio_avance = (int) $proced_global[$g][$i][$e]->julio['avance'];
                    $agosto_meta = (int) $proced_global[$g][$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $proced_global[$g][$i][$e]->agosto['avance'];
                    $sep_meta = (int) $proced_global[$g][$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $proced_global[$g][$i][$e]->septiembre['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->octubre['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->octubre['avance'];
                    $meta_acum = $meta_info + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                 $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                 $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'noviembre':
                    $desviaciones = $proced_global[$g][$i][$e]->noviembre['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->noviembre['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $junio_meta = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $junio_avance = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $julio_meta = (int) $proced_global[$g][$i][$e]->julio['meta'];
                    $julio_avance = (int) $proced_global[$g][$i][$e]->julio['avance'];
                    $agosto_meta = (int) $proced_global[$g][$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $proced_global[$g][$i][$e]->agosto['avance'];
                    $sep_meta = (int) $proced_global[$g][$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $proced_global[$g][$i][$e]->septiembre['avance'];
                    $oct_meta = (int) $proced_global[$g][$i][$e]->octubre['meta'];
                    $oct_avance = (int) $proced_global[$g][$i][$e]->octubre['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->noviembre['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->noviembre['avance'];
                    $meta_acum = $meta_info + $oct_meta + $sep_meta + $agosto_meta + $julio_meta + $junio_meta +
                                    $mayo_meta + $abril_meta + $marzo_meta + $febrero_meta + $enero_meta;
                    $avance_acum = $avance_info + $oct_avance + $sep_avance + $agosto_avance + $julio_avance + $junio_avance +
                                    $mayo_avance + $abril_avance + $marzo_avance + $febrero_avance + $enero_avance;
                    break;

                case 'diciembre':
                    $desviaciones = $proced_global[$g][$i][$e]->diciembre['expdesviaciones'];
                    $fecha_guardado = $proced_global[$g][$i][$e]->diciembre['fechavasave'];
                    $enero_meta = (int) $proced_global[$g][$i][$e]->enero['meta'];
                    $enero_avance = (int) $proced_global[$g][$i][$e]->enero['avance'];
                    $febrero_meta = (int) $proced_global[$g][$i][$e]->febrero['meta'];
                    $febrero_avance = (int) $proced_global[$g][$i][$e]->febrero['avance'];
                    $marzo_meta = (int) $proced_global[$g][$i][$e]->marzo['meta'];
                    $marzo_avance = (int) $proced_global[$g][$i][$e]->marzo['avance'];
                    $abril_meta = (int) $proced_global[$g][$i][$e]->abril['meta'];
                    $abril_avance = (int) $proced_global[$g][$i][$e]->abril['avance'];
                    $mayo_meta = (int) $proced_global[$g][$i][$e]->mayo['meta'];
                    $mayo_avance = (int) $proced_global[$g][$i][$e]->mayo['avance'];
                    $junio_meta = (int) $proced_global[$g][$i][$e]->junio['meta'];
                    $junio_avance = (int) $proced_global[$g][$i][$e]->junio['avance'];
                    $julio_meta = (int) $proced_global[$g][$i][$e]->julio['meta'];
                    $julio_avance = (int) $proced_global[$g][$i][$e]->julio['avance'];
                    $agosto_meta = (int) $proced_global[$g][$i][$e]->agosto['meta'];
                    $agosto_avance = (int) $proced_global[$g][$i][$e]->agosto['avance'];
                    $sep_meta = (int) $proced_global[$g][$i][$e]->septiembre['meta'];
                    $sep_avance = (int) $proced_global[$g][$i][$e]->septiembre['avance'];
                    $oct_meta = (int) $proced_global[$g][$i][$e]->octubre['meta'];
                    $oct_avance = (int) $proced_global[$g][$i][$e]->octubre['avance'];
                    $nov_meta = (int) $proced_global[$g][$i][$e]->noviembre['meta'];
                    $nov_avance = (int) $proced_global[$g][$i][$e]->noviembre['avance'];
                    $meta_info = (int) $proced_global[$g][$i][$e]->diciembre['meta'];
                    $avance_info = (int) $proced_global[$g][$i][$e]->diciembre['avance'];
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

        #Envio de Mes
        $mes_input = $mes_get;

        #CONSULTA DE FECHAS
        // $fechasPat = function ($organismo) use ($mesGlob, $mes_input){
        //     $tblFechas = FechasPat::select('id', 'fechas_avance')->where('id_org', '=', $organismo)->first();
        //     $fecha_enviar = $tblFechas->fechas_avance[$mes_input]['fecavanpdf'];
        //     $fech_carbon = Carbon::parse($fecha_enviar); // separador[1] contiene el mes
        //     $day = ($fech_carbon->day < 10) ? '0'.$fech_carbon->day : $fech_carbon->day;
        //     $mes_avance = $mesGlob[$fech_carbon->month-1].'-'.$day;   // Obtenemos
        //     $fecha_avance = $fech_carbon->format('d/m/Y');
        //     return ["mes_avance" => $mes_avance, "fecha_avance" => $fecha_avance];
        // };

        #REALIZA LAS OPERACIONES PARA ENVIARLAS A LA VISTA
        // $funciones_global[0]; //funciones del organismo cero;
        // $proced_global[0][0]; //funciones del organismo cero con la funicion uno;

        $mes_avance = "";
        $fechas_pdf_global = [];
        $mes_meta_avance_global = [];

        for ($g=0; $g < count($areas); $g++) { //For principal
            $mes_meta_avance = [];
            // $fecha_pdf_avance = $fechasPat($areas[$g]->id); //Obtenemos fecha de gen pdf
            for ($i=0; $i < count($funciones_global[$g]) ; $i++) {
                $arrayacum = [];
                    for ($e=0; $e < count($proced_global[$g][$i]); $e++) {
                        foreach ($mesGlob as $mes) {
                            if ($mes_input == $mes) {
                                $resul = $condiciones_meses($mes_input, $g, $i, $e);
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
            array_push($mes_meta_avance_global, $mes_meta_avance); //Guardamos los datos numericos de avances
            // array_push($fechas_pdf_global, $fecha_pdf_avance); //Guardamos las fechas de los avances
        }

        //(organismo), (funcion), (procedimientos), (valores)
        //dd($mes_meta_avance_global[1][0][0]);

        #GENERA EL PDF
        $pdf = PDF::loadView('vistas_pat.genpdfgeneral', compact('areas', 'direcciones', 'funciones_global', 'proced_global', 'mes_avance', 'mes_meta_avance_global', 'mes_get', 'global_ejercicio'));
        $pdf->setpaper('letter', 'landscape');
        return $pdf->stream('GENERAL_PDF.1.pdf');
    }

    // public function pdforg_direc($mes_get, $opcion_get){
    //     $zip = new ZipArchive();
    //     $zipFileName = public_path('example.zip');

    //     if ($zip->open($zipFileName, ZipArchive::CREATE)) {

    //         $directorioEnZip = 'new_directory/';

    //         // Añade el directorio vacío
    //         // $zip->addEmptyDir($directorioEnZip);
    //         $zip->addEmptyDir($directorioEnZip);

    //         // Add files or directories to the ZIP archive
    //         $zip->addFile(public_path('storage/uploadFiles/pat/2/avancefirmadoagosto_20230815151916_2.pdf'), $directorioEnZip . 'pdfUno.pdf');
    //         $zip->addFile(public_path('storage/uploadFiles/pat/2/avancefirmadojulio_20230815152704_2.pdf'), $directorioEnZip . 'pdfDos.pdf');

    //         $zip->close();
    //         // Verificar si el archivo ZIP se creó correctamente
    //         if (file_exists($zipFileName)) {
    //             return response()->download($zipFileName, 'example.zip');
    //             // return 'ZIP archive created successfully.';
    //         } else {
    //             return 'Failed to create ZIP archive.';
    //         }
    //     } else {
    //         return 'Failed to open ZIP archive.';
    //     }
    // }
}
