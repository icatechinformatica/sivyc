<?php

namespace App\Models\cat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

trait catApertura
{      
    protected function motivo_arc02(){
        $tcuota= ["REPROGRAMACIÓN FECHA/HORA"=>"REPROGRAMACIÓN FECHA/HORA","CAMBIO DE INSTRUCTOR"=>"CAMBIO DE INSTRUCTOR","ERROR MECANOGRÁFICO"=>"ERROR MECANOGRÁFICO","CANCELACIÓN"=>"CANCELACIÓN"];
        return $tcuota;
       
    }

    protected function tcuota(){
        $tcuota= ["PINS"=>"PAGO DE INSCRIPCION","EXO"=>"EXONERACION TOTAL DE PAGO","EPAR"=>"EXONERACION PARCIAL DE PAGO"];
        return $tcuota;
       
    }    
    protected function tinscripcion(){
        $tinscripcion= ["PAGO DE INSCRIPCION"=>"PAGO DE INSCRIPCION","EXONERACION TOTAL DE PAGO"=>"EXONERACION TOTAL DE PAGO","EXONERACION PARCIAL DE PAGO"=>"EXONERACION PARCIAL DE PAGO","OTRO TIPO DE BECA"=>"OTRO TIPO DE BECA"];
        return $tinscripcion;
       
    }
    protected function abrinscri(){
        $abrinscri = ["PAGO DE INSCRIPCION"=>"PI","EXONERACION TOTAL DE PAGO"=>"ET","EXONERACION PARCIAL DE PAGO"=>"EP","OTRO TIPO DE BECA"=>"OT"];
        return $abrinscri;
    }
    protected function tcurso(){
        $tcurso = ["CURSO"=>"CURSO","CERTIFICACION"=>"CERTIFICACION"];
        return $tcurso;       
    }
    protected function medio_virtual(){
        $medio = ["ICATECH VIRTUAL"=>"ICATECH VIRTUAL","JITSI"=>"JITSI","MEET"=>"MEET","TELMEX"=>"TELMEX","SKYPE"=>"SKYPE","ZOOM"=>"ZOOM"];
        return $medio;       
    }
    
    protected function sector(){
        $sector = ["PRIVADO"=>"PRIVADO","PUBLICO"=>"PUBLICO","SOCIAL"=>"SOCIAL"];
        return $sector;       
    }

    protected function plantel(){  
        $plantel = ["UNIDAD"=>"UNIDAD","AULA MÓVIL"=>"AULA MÓVIL","ACCIÓN MÓVIL"=>"ACCIÓN MÓVIL", "CAPACITACIÓN EXTERNA"=>"CAPACITACIÓN EXTERNA"];
        return $plantel;       
    }
    
    protected function dependencia($unidad){  
        $dependencia = DB::table('convenios')->where('unidades','like','%'.$unidad.'%')->orderby('institucion')->distinct('institucion')->pluck('institucion','institucion');    
        return $dependencia;       
    }
    
    protected function convenio($unidad,$tipo){  
        $convenio = DB::table('convenios')->where('tipo_convenio',$tipo)->where('activo','true')->where('unidades','like','%'.$unidad.'%')->orderby('no_convenio')->pluck('no_convenio','id');    
        return $convenio;       
    }
    
    protected function exoneracion($id_unidad){  
        $exoneracion = DB::table('exoneraciones')->where('activo','true')->where('id_unidad_capacitacion',$id_unidad)->orderby('no_memorandum')->pluck('no_memorandum','no_memorandum');        
        return $exoneracion;       
    }

    protected function programa(){  
        $programa = DB::table('tbl_cursos')->where('programa','!=','0')
        ->where('programa','!=','NINGUNA')->distinct()->orderby('programa')->pluck('programa','programa');    
        return $programa;       
    }

    protected function municipio(){  
        $municipio = DB::table('tbl_municipios')->where('id_estado','7')->orderby('muni')->pluck('muni','id');    
        return $municipio;       
    }

    protected function instructor($unidad, $id_especialidad){         
        $hoy = date("Y-m-d");

        /**$fecha_actual = date("d-m-Y");

        echo date("d-m-Y",strtotime($fecha_actual."+ 12 month")); 

        */

        //$instructor= DB::table('instructores')->select('id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',nombre) as instructor'))        
       // ->where('nombre','!=','')->whereJsonContains('unidades_disponible', [$unidad])->orderby('instructor')->pluck('instructor','id');
        
         $instructor = DB::table('instructores')
            ->select('instructores.id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'))
            ->WHERE('estado',true)
            ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
            ->whereJsonContains('unidades_disponible', [$unidad])
            ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
            ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',DB::raw("TO_DATE(to_char(CURRENT_DATE,'YYYY-MM-DD'),'YYYY-MM-DD')"))
            ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            //->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
            //->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
            ->orderby('instructor')
            ->pluck('instructor','instructores.id');
                    
                    
        return $instructor;       
    }

}