<?php

namespace App\Http\Controllers\reportesController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\faCADE as PDF;
use Illuminate\Support\Facades\Auth;

class rdcdController extends Controller
{
    public function index(Request $request){
        $id_user = Auth::user()->id;//dd($id_user);
        $id_unidad= Auth::user()->unidad;
        $unidad= $request->unidades;//dd($unidad);
        $modalidad=$request->modalidad;//dd($modalidad);
        
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
                //return view('reportes.rdcd08.rdcdFormu', compact('unidades','tipo'));
            }
            else{
                //var_dump($uni->unidad);
                $unidades=$uni->unidad;
                $tipo= gettype($unidades);
                //dd($unidades);
                //dd($tipo);
               // return view('reportes.rdcd08.rdcdFormu', compact('unidades', 'tipo'));
            }

        } else {
            //dd('no trae unidad');
            $unidades = DB::table('tbl_unidades')->groupBy('unidad')->orderBy('unidad')->pluck('unidad');//dd($unidades);
            $tipo= gettype($unidades);//dd($tipo);
            //dd($unidades);
            //var_dump($unidades);
            //return view('reportes.rdcd08.rdcdFormu', compact('unidades','tipo'));
        }
        //dd($tipo);
        if($tipo =='string'){
            $actas=DB::table('tbl_banco_folios')
                ->select('id','unidad','mod','finicial','ffinal','total','facta',)
                ->where('unidad','=',$unidades);
            if($modalidad == "TODO"){$actas=$actas;}else{
                if($request->modalidad) $actas = $actas->where('mod',$request->modalidad);
            }
            if($unidad == "TODO"){$actas=$actas;}else{
                if($request->unidad) $actas = $actas->where('unidad',$request->unidad);
            }            
        }
        else{
            $actas=DB::table('tbl_banco_folios as tb')
                ->select('tb.id','tb.unidad','tb.mod','tb.finicial', 'tb.ffinal', 'tb.total', 'tb.facta')
                ->whereIn('unidad',$unidades);

            if($modalidad == "TODO"){$actas=$actas;}else{
                if($request->modalidad) $actas = $actas->where('mod',$request->modalidad);
            } 
            if($unidad == "TODO"){$actas=$actas;}else{
                if($unidad) $actas = $actas->where('unidad',$unidad);
            } 
        }
        $actas=$actas->orderby('id','DESC')->get();
        // dd($actas);
        $cuerpo2 = [];
        foreach ($actas as $acta) {
            $cuerpo=DB::table('tbl_folios as tf')
                ->select(DB::raw("(sum(case when tf.movimiento in ('EXPEDIDO','CANCELADO','REEXPEDIDO','DUPLICADO') then 1 else 0 end) )as expedidos"))
                ->where('tf.folio', '<=', $acta->ffinal)
                ->where('tf.folio', '>=', $acta->finicial)
                ->get();
            // $cuerpo->push($acta->finicial);
            // $cuerpo->push($acta->ffinal);
            // $cuerpo->push($acta->facta);
            // $cuerpo->push($acta->unidad);
            array_push($cuerpo2, $cuerpo);
        }
        
        // dd($cuerpo2[0][0]->expedidos);
        return view('reportes.rdcd08.rdcdFormu', compact('unidades','tipo','actas', 'cuerpo2'));
        
    }
     public function none($id){
         $consulta=DB::table('tbl_banco_folios as tb')->select('tb.finicial', 'tb.ffinal', 'tb.total', 'tb.facta','tb.unidad','tb.mod')
         ->where('tb.id','=',$id)
         ->get();

         $cuerpo=DB::table('tbl_folios as tf')
         ->select(DB::raw('min(tf.folio) as mini'), 
         DB::raw('max(tf.folio) as maxi'), 
         'tf.fecha_expedicion',
         DB::raw("(sum(case when tf.movimiento in ('EXPEDIDO','REEXPEDIDO','DUPLICADO') then 1 else 0 end) )as expedidos"),
         DB::raw("(sum(case when tf.movimiento='CANCELADO' then 1 else 0 end) )as cancelados"))
         ->where('tf.folio', '<=', $consulta[0]->ffinal)
         ->where('tf.folio', '>=', $consulta[0]->finicial)
         ->groupBy('tf.id_curso','tf.fecha_expedicion')
         ->orderBy('mini')
         ->get();
 
         $fcancelados=DB::table('tbl_folios as tf')
         ->select('tf.folio as cance','tf.motivo')
         ->where('tf.unidad', '=', $consulta[0]->unidad)
         ->where('tf.folio', '<=', $consulta[0]->ffinal)
         ->where('tf.folio', '>=', $consulta[0]->finicial)
         ->where('tf.movimiento', '=', 'CANCELADO')
         ->get();

         $cct=DB::table('tbl_unidades')
         ->select('cct','dunidad')->where('unidad', '=', $consulta[0]->unidad)
         ->first();//dd($cct->cct);
         $unidad=$consulta[0]->unidad;
         $modalidad=$consulta[0]->mod;

         $pdf = PDF::loadView('reportes.rdcd08.rdcd', compact('cuerpo','consulta','cct','unidad','modalidad','fcancelados'));
         $pdf->setPaper('A4', 'portrait');
         //portrait
         return $pdf-> stream('rdcd.pdf');
     }
}