<?php

namespace App\Http\Controllers\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Excel\xlsEstadisticas;
use Maatwebsite\Excel\Facades\Excel;

class ecursosController extends Controller
{   
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $message = $cursos = $unidad = $tcapacitacion =  $finicial = $ffinal = $unidadess = $status_curso = NULL;
        $curso = $instructor = true;
        $tregistros = 10;
        $unidades = DB::table('tbl_unidades')->where(DB::raw('substr(cct,1,5)'),'07EIC')->orderby('unidad','ASC')->pluck('unidad','unidad');
       
        if($request->tregistros){     
            $unidad = $request->unidad;
            $tcapacitacion = $request->tcapacitacion;
            $finicial = $request->finicial;
            $ffinal = $request->ffinal;
            $tregistros = $request->tregistros;
            $instructor = $request->instructor;
            $curso = $request->curso;
            $status_curso =  $request->status_curso;
            
           if($curso AND $instructor) $cursos = DB::table('tbl_cursos as c')->select('c.curso','c.nombre as instructor',DB::raw('count(*) as total'));
           elseif($instructor) $cursos = DB::table('tbl_cursos as c')->select('c.nombre as instructor',DB::raw('count(*) as total'));
           else $cursos = DB::table('tbl_cursos as c')->select('c.curso',DB::raw('count(*) as total'));
           
           if($unidad) $unidadess = DB::table('tbl_unidades')->where('ubicacion', $unidad)->pluck('unidad','unidad');
           
           $cursos = $cursos->where('c.status_curso','AUTORIZADO')->where('c.clave','!=','0');
                if($unidadess) $cursos = $cursos->whereIn('c.unidad', $unidadess);
                if($request->tcapacitacion AND $tcapacitacion!='TODOS') $cursos = $cursos->where('c.tcapacitacion',$request->tcapacitacion);
                if($request->finicial) $cursos = $cursos->where('c.termino','>=',$request->finicial);
                if($request->ffinal) $cursos = $cursos->where('c.termino','<=',$request->ffinal);
                if($request->status_curso) $cursos = $cursos->where('c.status_curso',$request->status_curso);
               
                if($curso AND $instructor) $cursos = $cursos->groupby('c.curso','c.nombre');
                elseif($instructor) $cursos = $cursos->groupby('c.nombre');
                else{$cursos = $cursos->groupby('c.curso'); $curso=true;}           
                
                $cursos = $cursos->limit($tregistros)->orderby(DB::raw('count(*)'), 'DESC')->get();
              
        }

        return view('estadisticas.ecursos', compact('message','unidades','cursos','unidad', 'tcapacitacion', 'finicial', 'ffinal', 'tregistros','instructor', 'curso', 'status_curso'));     
    }  
    
    public function xls(Request $request){
        $message = $cursos = $unidad = $tcapacitacion =  $finicial = $ffinal = $unidadess = $status_curso = NULL;
        $curso = $instructor = true;
        $tregistros = 10;
        if($request->tregistros){     
            $unidad = $request->unidad;
            $tcapacitacion = $request->tcapacitacion;
            $finicial = $request->finicial;
            $ffinal = $request->ffinal;
            $tregistros = $request->tregistros;
            $instructor = $request->instructor;
            $curso = $request->curso;
            $status_curso =  $request->status_curso;
            
           if($curso AND $instructor) $cursos = DB::table('tbl_cursos as c')->select('c.curso','c.nombre as instructor',DB::raw('count(*) as total'));
           elseif($instructor) $cursos = DB::table('tbl_cursos as c')->select('c.nombre as instructor',DB::raw('count(*) as total'));
           else $cursos = DB::table('tbl_cursos as c')->select('c.curso',DB::raw('count(*) as total'));
           
           if($unidad) $unidadess = DB::table('tbl_unidades')->where('ubicacion', $unidad)->pluck('unidad','unidad');
           
           //$cursos = $cursos->where('c.status_curso','AUTORIZADO')->where('c.clave','!=','0');
                if($unidadess) $cursos = $cursos->whereIn('c.unidad', $unidadess);
                if($request->tcapacitacion AND $tcapacitacion!='TODOS') $cursos = $cursos->where('c.tcapacitacion',$request->tcapacitacion);
                if($request->finicial) $cursos = $cursos->where('c.termino','>=',$request->finicial);
                if($request->ffinal) $cursos = $cursos->where('c.termino','<=',$request->ffinal);
                if($request->status_curso) $cursos = $cursos->where('c.status_curso',$request->status_curso);
               
                if($curso AND $instructor) $cursos = $cursos->groupby('c.curso','c.nombre');
                elseif($instructor) $cursos = $cursos->groupby('c.nombre');
                else{$cursos = $cursos->groupby('c.curso'); $curso=true;}           
                
                $cursos = $cursos->limit($tregistros)->orderby(DB::raw('count(*)'), 'DESC')->get();
              
      
            
            if(count($cursos)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
                                
     
            if($curso AND $instructor) $head = ['CURSO','INSTRUCTOR','TOTAL_CURSOS'];
            elseif($instructor) $head = ['INSTRUCTOR','TOTAL_CURSOS'];
            else $head = ['CURSO','TOTAL_CURSOS'];

            $name= "ESTADISTICAS_CURSOS_".$unidad.".xlsx";
            $title = "ESTADISTICAS_CURSOS_".$unidad;    
    
            if(count($cursos)>0)return Excel::download(new xlsEstadisticas($cursos,$head, $title), $name);
             
                
        }else return "INGRESE EL TOTAL DE REGISTROS";        
    } 
    
}