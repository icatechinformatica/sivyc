<?php
//Creado por Romelia P�rez Nang�el�--rpnanguelu@gmail.com
namespace App\Http\Controllers\TableroControlller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AreaAdscripcion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UnidadesController extends Controller
{
    function __construct() {
      $this->meses = [1=>"ENERO",2=>"FEBRERO",3=>"MARZO",4=>"ABRIL",5=>"MAYO",6=>"JUNIO",7=>"JULIO",8=>"AGOSTO",9=>"SEPTIEMBRE", 10=>"OCTUBRE", 11=>"NOVIEMBRE",12=>"DICIEMBRE"];
      $this->field = [1=>"ene",2=>"feb",3=>"mar",4=>"abr",5=>"may",6=>"jun",7=>"jul",8=>"ago",9=>"sep", 10=>"oct", 11=>"nov",12=>"dic"];
    }

    public function index(Request $request) {
        $id_unidad = $request->get('id_unidad');
        $mes_inicio = $request->get('mes_inicio');
        $mes_fin = $request->get('mes_fin');
        $mes_hoy = date("m")*1;
        if($request->get('ejercicio'))$ejercicio = $request->get('ejercicio');
        else $ejercicio = date("Y");

        if(!$mes_inicio AND !$mes_fin){
            $mes_inicio = $mes_hoy;
            $cursos_p = $this->field[$mes_hoy];
            $horas_p =  "hr_".$this->field[$mes_hoy];
        }elseif($mes_inicio AND !$mes_fin){
            $cursos_p = $this->field[$mes_inicio];
            $horas_p =  "hr_".$this->field[$mes_inicio];
        }elseif($mes_inicio AND $mes_fin){
            $cad_cur = $cad_hr = $mes_hoy="";
            for ($i = $mes_inicio; $i <= $mes_fin; $i++) {
                if($cad_cur){
                    $cad_cur .=  "+".$this->field[$i];
                    $cad_hr .=  "+ hr_".$this->field[$i];
                }else{
                    $cad_cur =  $this->field[$i];
                    $cad_hr =  "hr_".$this->field[$i];
                }
            }
            $cursos_p = " ($cad_cur)";
            $horas_p = " ($cad_hr)";
        }
        $porc_cur_p = "(count(c.id)*100/$cursos_p)";
        $porc_hr_p = "COALESCE((sum(c.dura)*100/$horas_p),0)";
        //echo $cursos_p; exit;
        $data = DB::table('poa as p')->select('p.id','p.id_unidad','p.id_plantel','p.unidad',
            DB::raw($cursos_p." as cursos_p"), DB::raw('count(c.id) as cursos_r'),
            DB::raw("( CASE WHEN ". $cursos_p."='0' THEN 0  ELSE  ".$porc_cur_p." END) as porc_cur_p"),
            DB::raw("COALESCE(".$horas_p.", 0) as horas_p") ,DB::raw('COALESCE(sum(c.dura),0) as horas_r'),
            DB::raw("( CASE WHEN ". $horas_p."='0' THEN 0  ELSE  ".$porc_hr_p." END) as porc_hr_p"),
            DB::raw("COALESCE(p.promedio_benef*(".$cursos_p."),0) as benef_p"),'p.promedio_benef',
            DB::raw('COALESCE(sum(c.hombre+c.mujer),0) as benef_r'),
            DB::raw('COALESCE(sum(f.importe_total),0) as inversion'),
            DB::raw('COALESCE(count(f.*),0) as cursos_pagados')
            )
            ->leftJoin('tbl_cursos as c', function($join)use( $mes_inicio, $mes_fin,$ejercicio){
                    $ini = str_pad($mes_inicio, 2, "0", STR_PAD_LEFT);
                    if(!$mes_fin) $fin = $ini;
                    else $fin = str_pad($mes_fin, 2, "0", STR_PAD_LEFT);
                    $join->on('c.unidad','=','p.tbl_unidades_unidad');
                    $join->where('c.clave','>',0);
                    $join->whereMonth('fecha_apertura','>=',$ini);
                    $join->whereMonth('fecha_apertura','<=',$fin);
                    $join->whereYear('fecha_apertura','=',$ejercicio);
            })
            ->leftJoin('folios as f', function($join){
                $join->on('f.id_cursos', '=', 'c.id');
                $join->where('f.status','Finalizado');
            });
        if($id_unidad)$data = $data->where('id_unidad',$id_unidad);
        $data = $data->where('ejercicio',$ejercicio)->orderby("p.id")->groupby('p.id')->get();

        /* SUBTOTALES PROGRAMADOS Y REALIZADOS*/
        $dataTMP = json_decode(json_encode($data), true);/// object to array
        if($id_unidad){
            $labels = DB::table('poa')->where('id_unidad',$id_unidad)->where('id_plantel','>','0')->orderby('id')->pluck('tbl_unidades_unidad');
            $dataTMP = array_filter( $dataTMP, function( $e ){ return $e['id_plantel'] >0; });

        }else{
            $labels = DB::table('poa')->where('id_plantel',0)->orderby('id')->pluck('tbl_unidades_unidad');
            $dataTMP = array_filter( $dataTMP, function( $e ){ return $e['id_plantel'] ==0; });
        }
        $labels = json_decode(json_encode($labels), true); //object to array
        $dataP[] = array_column($dataTMP, 'cursos_p');
        $dataR[] = array_column($dataTMP, 'cursos_r');
        $dataP[] = array_column($dataTMP, 'benef_p');
        $dataR[] = array_column($dataTMP, 'benef_r');
        $dataP[] = array_column($dataTMP, 'horas_p');
        $dataR[] = array_column($dataTMP, 'horas_r');
        $dataR[] = array_column($dataTMP, 'inversion');
        //var_dump($dataP[2]); exit;
        /*FIN SUBTOTALES*/

        $breadcrumb = "Programados y Aperturados";
        $lst_unidad =  DB::table('poa')->where('id_plantel',0)->orderby('id_unidad','ASC')->pluck('unidad','id_unidad');
        $lst_meses = $this->meses;
        $lst_ejercicio =  DB::table('poa')->where('id_plantel',0)->orderby('ejercicio','ASC')->pluck('ejercicio','ejercicio');

       // var_dump($data); exit;
        $tthis = $this->tthis();
        return view('tablero.unidades.index', compact('data','dataP','dataR','labels','lst_unidad','lst_ejercicio','ejercicio',
                                            'breadcrumb','id_unidad','lst_meses','mes_inicio','mes_fin','tthis'));
    }

    public function tthis(){
        return $this;
    }

    public function totales($dataT, $id_unidad,$cursos_p, $horas_p){
        $dataT = json_decode(json_encode($dataT), true);
        $totales = array_filter( $dataT, function( $e )use($id_unidad) {
            return $e['id_unidad'] == $id_unidad AND $e['id_plantel']>0;
            });

        $t['cursos_r'] = $total_cursos_r = array_sum(array_column($totales, 'cursos_r'));
        $t['porc_cur_r'] = round($total_cursos_r*100/$cursos_p);
        $t['horas_r'] = $total_horas_r = array_sum(array_column($totales, 'horas_r'));
        $t['porc_hr_r'] = round($total_horas_r*100/$horas_p);
        $t['benef_r'] = array_sum(array_column($totales, 'benef_r'));
        $t['cursos_pagados'] = array_sum(array_column($totales, 'cursos_pagados'));
        $t['inversion'] = array_sum(array_column($totales, 'inversion'));

        return $t;
    }
}
