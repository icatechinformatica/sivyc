<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $data = []; //prueba
        if($request->mes||$request->ejercicio){
            $mes = $request->mes;
            $anio = $request->ejercicio;
            $mes1 = $request->ejercicio."-".$request->mes."-"."01";
            $mesInicio = date((Carbon::parse($mes1)->startOfMonth())->format('Y-m-d'));
            $mesFin = date((Carbon::parse($mes1)->endOfMonth())->format('Y-m-d'));
        }else {
            $mes = date('m');
            $anio = date("Y");
            $mesInicio = date((Carbon::parse(date("Y-m-d"))->startOfMonth())->format('Y-m-d'));
            $mesFin = date((Carbon::parse(date("Y-m-d"))->endOfMonth())->format('Y-m-d'));
        }
        $meses = ["01"=>"ENERO","02"=>"FEBRERO","03"=>"MARZO","04"=>"ABRIL","05"=>"MAYO","06"=>"JUNIO","07"=>"JULIO","08"=>"AGOSTO","09"=>"SEPTIEMBRE","10"=>"OCTUBRE", "11"=>"NOVIEMBRE","12"=>"DICIEMBRE"];
        $ejercicios = [2022=>2022,2023=>2023,2024=>2024,2025=>2025,2026=>2026,2027=>2027,2028=>2028,2029=>2029,2030=>2030];
        $cursos = DB::table('tbl_cursos as tc')->select('tu.ubicacion',DB::raw("count(tc.id) as total_cursos"),
                                                        DB::raw("sum(case when tc.mextemporaneo is not null then 1 else 0 end) as extemporaneos_01"),
                                                        DB::raw("sum(case when tc.mextemporaneo_arc02 is not null then 1 else 0 end) as extemporaneos_02"))
                    ->join('tbl_unidades as tu','tc.unidad','=','tu.unidad')
                    ->where('tc.status_curso','AUTORIZADO')
                    ->where('tc.fecha_apertura','>=',$mesInicio)
                    ->where('tc.fecha_apertura','<=',$mesFin)
                    ->groupBy('tu.ubicacion')
                    ->orderBy('tu.ubicacion')
                    ->get();
        foreach ($cursos as $key => $value) {
            $data['label'][] = $value->ubicacion;
            $data['total'][] = $value->total_cursos;
            $data['ex1'][] = $value->extemporaneos_01;
            $data['ex2'][] = $value->extemporaneos_02;
        }
        $data = json_encode($data);
        
        if($request->mes)$mes_ant = $request->mes;
        else $mes_ant = date("n")-1;
        $mes_ant = $meses[($mes_ant==0) ? 12 : str_pad($mes_ant, 2, '0', STR_PAD_LEFT)];
                
        $data_asistencia = DB::table('tbl_instituto')->where('id',1)->value('asistencia_tecnica->E'.$anio.'->'.$mes_ant);
        if(!$data_asistencia){
            $mes_ant = date("n")-1;
            $mes_ant = $meses[($mes_ant==0) ? 12 : str_pad($mes_ant, 2, '0', STR_PAD_LEFT)];             
            $data_asistencia = DB::table('tbl_instituto')->where('id',1)->value('asistencia_tecnica->E'.$anio.'->'.$mes_ant);
        }
             //dd($data_asistencia);
        return view('layouts.pages.home', compact('cursos','meses','ejercicios','mes','anio','data','data_asistencia','mes_ant'));
    }
}
