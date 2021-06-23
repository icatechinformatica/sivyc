<?php

namespace App\Http\Controllers\reportesController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\faCADE as PDF;
use Illuminate\Support\Facades\Auth;

class rdcdController extends Controller
{
    public function index(){
        $id_user = Auth::user()->id;//dd($id_user);
        $id_unidad= Auth::user()->unidad;

        $unidades = $unidad = NULL;
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
            $actas=DB::table('tbl_banco_folios')->select('id','unidad','mod','finicial','ffinal','total','facta')->where('unidad','=',$unidades);
        }
        else{
            $actas=DB::table('tbl_banco_folios as tb')->select('id','tb.unidad','tb.mod','tb.finicial', 'tb.ffinal', 'tb.total', 'tb.facta')->whereIn('unidad',$unidades);
        }
        //dd($actas);
        $actas=$actas->orderby('id','DESC')->paginate(10);
        return view('reportes.rdcd08.rdcdFormu', compact('unidades','tipo','actas'));
        
    }
    public function store(Request $request){
        //dd($request->all());
        $request->validate([
             'unidades'=>'required',
             'fecha_acta'=>'required',
             'modalidad'=>'required'
         ]);
         $unidad= $request->unidades;//dd($unidad);
         $fecha_acta=$request->fecha_acta;//dd($fecha_acta);
         $modalidad=$request->modalidad;
 
         $cct=DB::table('tbl_unidades')
         ->select('cct')->where('unidad', '=', $unidad)
         ->first();//dd($cct->cct);
         $consulta=DB::table('tbl_banco_folios as tb')->select('tb.finicial', 'tb.ffinal', 'tb.total', 'tb.facta')
         ->where('tb.unidad','=',$unidad)
         ->where('tb.facta', '=', $fecha_acta)
         ->get();//dd($consulta[0]->finicial);
 
         $cuerpo=DB::table('tbl_folios as tf')
         ->select(DB::raw('min(tf.folio) as mini'), 
         DB::raw('max(tf.folio) as maxi'), 
         'tf.fecha_expedicion',
         DB::raw("(sum(case when tf.movimiento='EXPEDIDO' then 1 else 0 end) )as expedidos"),
         DB::raw("(sum(case when tf.movimiento='CANCELADO' then 1 else 0 end) )as cancelados"))
         ->where('tf.mod', '=', $modalidad)
         ->where('tf.unidad', '=', $unidad)
         ->where('tf.folio', '<=', $consulta[0]->ffinal)
         ->where('tf.folio', '>=', $consulta[0]->finicial)
         ->groupBy('tf.id_curso','tf.fecha_expedicion')
         ->get();//dd($cuerpo);
 
         $fcancelados=DB::table('tbl_folios as tf')
         ->select('tf.folio as cance','tf.motivo')
         ->where('tf.mod', '=', $modalidad)
         ->where('tf.unidad', '=', $unidad)
         ->where('tf.folio', '<=', $consulta[0]->ffinal)
         ->where('tf.folio', '>=', $consulta[0]->finicial)
         ->where('tf.movimiento', '=', 'CANCELADO')
         
         ->get();//dd($fcancelados);
 
         $pdf = PDF::loadView('reportes.rdcd08.rdcd', compact('cuerpo','consulta','cct','unidad','modalidad','fcancelados'));
         $pdf->setPaper('A4', 'portrait');
         //portrait
         return $pdf-> stream('rdcd.pdf');
         
     }
     public function none($id){
         $consulta=DB::table('tbl_banco_folios as tb')->select('tb.finicial', 'tb.ffinal', 'tb.total', 'tb.facta','tb.unidad','tb.mod')
         ->where('tb.id','=',$id)
         ->get();//dd($consulta);

         $cuerpo=DB::table('tbl_folios as tf')
         ->select(DB::raw('min(tf.folio) as mini'), 
         DB::raw('max(tf.folio) as maxi'), 
         'tf.fecha_expedicion',
         DB::raw("(sum(case when tf.movimiento='EXPEDIDO' then 1 else 0 end) )as expedidos"),
         DB::raw("(sum(case when tf.movimiento='CANCELADO' then 1 else 0 end) )as cancelados"))
         ->where('tf.folio', '<=', $consulta[0]->ffinal)
         ->where('tf.folio', '>=', $consulta[0]->finicial)
         ->groupBy('tf.id_curso','tf.fecha_expedicion')
         ->get();//dd($cuerpo);
 
         $fcancelados=DB::table('tbl_folios as tf')
         ->select('tf.folio as cance','tf.motivo')
         ->where('tf.mod', '=', $consulta[0]->mod)
         ->where('tf.unidad', '=', $consulta[0]->unidad)
         ->where('tf.folio', '<=', $consulta[0]->ffinal)
         ->where('tf.folio', '>=', $consulta[0]->finicial)
         ->where('tf.movimiento', '=', 'CANCELADO')
         ->get();//dd($fcancelados);

         $cct=DB::table('tbl_unidades')
         ->select('cct')->where('unidad', '=', $consulta[0]->unidad)
         ->first();//dd($cct->cct);
         $unidad=$consulta[0]->unidad;
         $modalidad=$consulta[0]->mod;

         $pdf = PDF::loadView('reportes.rdcd08.rdcd', compact('cuerpo','consulta','cct','unidad','modalidad','fcancelados'));
         $pdf->setPaper('A4', 'portrait');
         //portrait
         return $pdf-> stream('rdcd.pdf');
     }
}