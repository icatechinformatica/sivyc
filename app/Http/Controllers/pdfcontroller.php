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
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');
        $_SESSION['unidades'] = NULL;
        $id_unidad = Auth::user()->unidad;
        $unidad = DB::table('tbl_unidades')->where('id',$id_unidad)->value('unidad');
        $_SESSION['unidad'] = $unidad;
        if($rol){
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');
            if(count($unidades)==0) $unidades =[$unidad];
            $_SESSION['unidades'] = $unidades;
        }

        $memo_apertura = $request->get("memo_apertura");
        $fecha_termino=$request->get("fecha_apertura");
        $botonarc = $request->get("submitbutton");

        if ($memo_apertura and $fecha_termino){
            if ($botonarc=="ARC01") return $this->ape01($memo_apertura,$fecha_termino);
            elseif($botonarc=="ARC02")return $this->ape02($memo_apertura,$fecha_termino);
        }else  return "TECLEE MEMORANDUM DE APERTURA Y FECHA DEL MEMORANDUM";
    }

    private function ape01($memo_apertura,$fecha_termino){
        $fecha_memo=date('d-m-Y',strtotime($fecha_termino));
        $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
            DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','efisico','hombre','mujer','tipo','opcion',
            'motivo','cp','ze','tcapacitacion','tipo_curso');
        if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);
        $reg_cursos = $reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();

        if(count($reg_cursos)>0){
            $reg_unidad=DB::table('tbl_unidades')->select('dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion');
            if($_SESSION['unidad'])$reg_unidad = $reg_unidad->where('unidad',$_SESSION['unidad']);
            $reg_unidad = $reg_unidad->first();

            $pdf = PDF::loadView('reportes.arc01',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura'));
            $pdf->setpaper('letter','landscape');
            return $pdf->stream('apertura.pdf');
        }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
    }

    public function ape02($memo_apertura,$fecha_termino) {
        $fecha_memo=date('d-m-Y',strtotime($fecha_termino));

        $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','curso','inicio','termino','dura',
            'efisico','opcion','motivo','nmunidad','observaciones','realizo','tcapacitacion','tipo_curso');
        if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);
        $reg_cursos = $reg_cursos->WHERE('nmunidad', '=', $memo_apertura)->orderby('espe')->get();

        if(count($reg_cursos)>0){
            $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion');
            if($_SESSION['unidad'])$reg_unidad = $reg_unidad->where('unidad',$_SESSION['unidad']);
            $reg_unidad = $reg_unidad->first();

            $pdf = PDF::loadView('reportes.arc02',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura'));
            $pdf->setpaper('letter','landscape');
            return $pdf->stream('apertura.pdf');
        }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
    }
}
