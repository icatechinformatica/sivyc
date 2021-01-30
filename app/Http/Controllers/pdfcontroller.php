<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class pdfcontroller extends Controller
{
    public function index(Request $request)
    {
        
        return view('reportes.vista_arc');
        
    }
    public function arc(Request $request)
    {
        $memo_apertura = $request->get("memo_apertura");
        $fecha_termino=$request->get("fecha_apertura");
        $botonarc = $request->get("submitbutton");
        if ($memo_apertura and $fecha_termino)
        {
            if ($botonarc=="ARC01")
            {
                return $this->ape01($memo_apertura,$fecha_termino);
            }
            elseif($botonarc=="ARC02")
            {
                return $this->ape02($memo_apertura,$fecha_termino);
            }
        }
        else 
        {
            return "TECLEE MEMORANDUM DE APERTURA Y FECHA DEL MEMORANDUM";
        }    
    }
    private function ape01($memo_apertura,$fecha_termino)
    {
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
        ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', '=', 'unidad')
        ->value('roles.slug');        
        $_SESSION['unidades']=NULL;
        //var_dump($rol);exit;
        if($rol=='unidad')
        { 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');
            if(count($unidades)==0) 
            {
                $unidades =[$unidad];
                $_SESSION['unidad'] = $unidades;
            }
            else
            {
                $_SESSION['unidad'] = $unidad;             
            }
        }
        //var_dump($_SESSION['unidades']);exit;
        $fecha_memo=date('d-m-yy',strtotime($fecha_termino));
        $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
        DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','efisico','hombre','mujer','tipo','opcion',
        'motivo','cp','ze','tcapacitacion');
        if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
        {
            $reg_cursos=$reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();
            
        }                
        //var_dump($reg_cursos);exit;
        if(count($reg_cursos)==0)
        {
            return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
        }
        else
        {
            $reg_unidad=DB::table('tbl_unidades')->select('dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
            'pvinculacion')->where('unidad',$_SESSION['unidad'])->first();
            //dd($reg_unidad);
            $pdf = PDF::loadView('reportes.arc01',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura'));
            //return view('reportes.arc01');
            //var_dump($pdf);exit;
            $pdf->setpaper('letter','landscape');
            return $pdf->stream('apertura.pdf');
        }
    }
    public function ape02($memo_apertura,$fecha_termino)
    {
            $id_user = Auth::user()->id;
            $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
                ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', '=', 'unidad')
                ->value('roles.slug');        
            $_SESSION['unidades']=NULL;
            //var_dump($rol);exit;
            if($rol=='unidad')
            { 
                $unidad = Auth::user()->unidad;
                $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
                $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');
                if(count($unidades)==0) $unidades =[$unidad];       
                    $_SESSION['unidades'] = $unidades; 
                $_SESSION['unidad'] = $unidad;             
            }
            $fecha_memo=date('d-m-yy',strtotime($fecha_termino));
            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','nombre','clave','mvalida','mod','curso','inicio','termino','dura',
            'efisico','opcion','motivo','nmunidad','observaciones','realizo','tcapacitacion');
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
                $reg_cursos=$reg_cursos->WHERE('nmunidad', '=', $memo_apertura)->orderby('espe')->get();
            if(count($reg_cursos)==0)
            {
                return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
            }
            else
            {
                $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico',
                'pvinculacion')->where('unidad',$_SESSION['unidad'])->first();
                $pdf = PDF::loadView('reportes.arc02',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('apertura.pdf');
            }      
    }
}
