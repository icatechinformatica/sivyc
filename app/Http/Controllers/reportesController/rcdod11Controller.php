<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;

class rcdod11Controller extends Controller
{
    function __construct() {
        $this->periodo = ["7"=>"1","8"=>"1","9"=>"1","10"=>"2","11"=>"2","12"=>"2","1"=>"3","2"=>"3","3"=>"3","4"=>"4","5"=>"4","6"=>"4"];     
    }
    public function index(Request $request){
        $id_user = Auth::user()->id;//dd($id_user);
        $id_unidad= Auth::user()->unidad;
        //$id_user=2;
        //$id_unidad=1;
        $consulta=null;

        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
        ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');//dd($rol);
        if ($rol) {
            //dd('si trae unidad');
            $uni = DB::table('tbl_unidades')->select(DB::raw('SUBSTR(cct,1,5) as clave'),DB::raw('SUBSTR(cct,6,10) as cct'),'ubicacion','unidad')->where('id',$id_unidad)->first();
            //dd($uni);
            if($uni->clave=='07EIC'){
                $unidades = DB::table('tbl_unidades')->where('ubicacion',$uni->unidad)->pluck('unidad');
                $tipo= gettype($unidades);
                //dd($unidades);
                //var_dump($unidades);
            }
            else{
                //var_dump($uni->unidad);
                $unidades=$uni->unidad;
                $tipo= gettype($unidades);
                //dd($tipo);
            }

        } else {
            //dd('no trae unidad');
            $unidades = DB::table('tbl_unidades')->groupBy('unidad')->orderBy('unidad')->pluck('unidad');//dd($unidades);
            $tipo= gettype($unidades);//dd($tipo);
            //dd($unidades);
            //var_dump($unidades);
        }
        //dd($request);
        $unidad= $request->unidades; //dd($unidad);
        $finicio=$request->fecha_inicio;
        $ftermino=$request->fecha_termino;
        if($unidad or $finicio or $ftermino){

            // $consulta=DB::table('tbl_cursos as tc')
            // ->join('tbl_inscripcion as i','tc.id','=','i.id_curso')
            // ->join('tbl_folios as tf', 'i.id_folio','=','tf.id')
            // ->select('tf.matricula','i.alumno','tf.folio',DB::raw("(select f.folio from tbl_folios as f where f.movimiento='DUPLICADO' and i.matricula=f.matricula and f.id_curso=i.id_curso)as duplicado"))
            // ->where('tc.status','=','REPORTADO')
            // ->whereIn('tf.motivo',['ROBO O EXTRAVIO','NO SOLICITADO'])
            // ->where('tf.movimiento','=','CANCELADO');
            $consulta=DB::table('tbl_cursos as tc')
            ->join('tbl_folios as tf', 'tc.id','=','tf.id_curso')
            ->select('tf.matricula','tf.nombre as alumno',
                DB::raw("(select f.folio from tbl_folios as f where f.movimiento='CANCELADO' and f.motivo in ('ROBO O EXTRAVIO','NO SOLICITADO') and tf.matricula=f.matricula and f.id_curso=tc.id)as folio"),
                'tf.folio as duplicado')
            ->whereIn('tc.status',['REPORTADO','TURNADO_PLANEACION'])
            ->where('tf.movimiento','=','DUPLICADO');
            if($finicio){$consulta=$consulta->where('tc.termino','>=',$finicio);}
            if($ftermino){$consulta=$consulta->where('tc.termino','<=',$ftermino);}
            if($unidad=="TODO"){$consulta=$consulta->whereIn('tc.unidad',$unidades);}else{$consulta=$consulta->where('tc.unidad',$unidad);}
            $consulta=$consulta->orderBy('tf.nombre')->get();//dd($consulta);
        }

        
        return view('reportes.rcdod11.rcdod11formu', compact('unidades','tipo','consulta','request'));

    }
    public function pdf(Request $request){
        $unidad= $request->unidades; //dd($unidad);
        $finicio=$request->fecha_inicio;
        $ftermino=$request->fecha_termino;

        if($unidad==null||$unidad=='TODO'){return redirect()->route('carter')->with('success', 'Selecione una unidad');}
        if($finicio==null||$ftermino==null){return redirect()->route('carter')->with('success', 'Selecione un rango de fecha');}
        $sq=DB::table('tbl_unidades')->select('unidad','cct','plantel','dunidad','pdunidad')->where('unidad',$unidad)->first();
        
        $consulta=DB::table('tbl_cursos as tc')
            ->join('tbl_folios as tf', 'tc.id','=','tf.id_curso')
            ->select('tf.matricula','tf.nombre as alumno','tc.mod','tc.espe',
                DB::raw("(select f.folio from tbl_folios as f where f.movimiento='CANCELADO' and f.motivo in ('ROBO O EXTRAVIO','NO SOLICITADO') and tf.matricula=f.matricula and f.id_curso=tc.id)as folio"),
                'tf.folio as duplicado')
            ->whereIn('tc.status',['REPORTADO','TURNADO_PLANEACION'])
            ->where('tf.movimiento','=','DUPLICADO')
            ->where('tc.unidad',$unidad)
            ->where('tc.termino','>=',$finicio)
            ->where('tc.termino','<=',$ftermino)
            ->orderBy('tf.nombre')
            ->get();
            
        if($request->fecha_termino){
            $fecha_objeto = new DateTime($request->fecha_termino);
            $mes_termino = ltrim($fecha_objeto->format('m'), '0');        
            $periodo = $this->periodo[$mes_termino];
        }else $periodo = "DATO REQUERIDO";

        if(count($consulta)==0){return redirect()->route('carter')->with('success', 'No se ha encontrado registros');}

        $pdf = PDF::loadView('reportes.rcdod11.rcdod11pdf', compact('sq','consulta','periodo'));
    	$pdf->setPaper('A4', 'landscape');
    	//portrait
    	return $pdf-> stream('rcdod11.pdf');
    }
}
