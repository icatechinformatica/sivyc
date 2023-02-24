<?php

namespace App\Models\cat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

trait catAlumnos
{
     function __construct() {
        $this->discapacidad = ["AUDITIVA"=>"AUDITIVA","DEL HABLA"=>"DEL HABLA","INTELECTUAL"=>"INTELECTUAL", "MOTRIZ"=>"MOTRIZ", "VISUAL"=>"VISUAL","NINGUNA"=>"NINGUNA"];
        $this->escolaridad = ["PRIMARIA INCONCLUSA"=>"PRIMARIA INCONCLUSA","PRIMARIA TERMINADA"=>"PRIMARIA TERMINADA","SECUNDARIA INCONCLUSA"=>"SECUNDARIA INCONCLUSA","SECUNDARIA TERMINADA"=>"SECUNDARIA TERMINADA",
        "NIVEL MEDIO SUPERIOR INCONCLUSO"=>"NIVEL MEDIO SUPERIOR INCONCLUSO","NIVEL MEDIO SUPERIOR TERMINADO"=>"NIVEL MEDIO SUPERIOR TERMINADO","NIVEL SUPERIOR INCONCLUSO"=>"NIVEL SUPERIOR INCONCLUSO","NIVEL SUPERIOR TERMINADO"=>"NIVEL SUPERIOR TERMINADO","POSTGRADO"=>"POSTGRADO"];
        $this->etnia= ["AKATECOS"=>"AKATECOS","CH'OLES"=>"CH'OLES","CHUJES"=>"CHUJES","JAKALTECOS"=>"JAKALTECOS","K'ICHES"=>"K'ICHES","LACANDONES"=>"LACANDONES","MAMES"=>"MAMES","MOCH&Oacute;S"=>"MOCH&Oacute;S","TEKOS"=>"TEKOS","TOJOLABALES"=>"TOJOLABALES","TSELTALES"=>"TSELTALES","TSOTSILES"=>"TSOTSILES","ZOQUES"=>"ZOQUES"];
        $this->estado_civil= ["SOLTERO(A)"=>"SOLTERO(A)","CASADO(A)"=>"CASADO(A)","UNI&Oacute;N LIBRE"=>"UNI&Oacute;N LIBRE","DIVORCIADO(A)"=>"DIVORCIADO(A)","VIUDO(A)"=>"VIUDO(A)"];
        $this->tinscripcion= ["PAGO DE INSCRIPCION"=>"PAGO DE INSCRIPCION","EXONERACION TOTAL DE PAGO"=>"EXONERACION TOTAL DE PAGO","EXONERACION PARCIAL DE PAGO"=>"EXONERACION PARCIAL DE PAGO","OTRO TIPO DE BECA"=>"OTRO TIPO DE BECA"];
        $this->abrinscri = ["PAGO DE INSCRIPCION"=>"PI","EXONERACION TOTAL DE PAGO"=>"ET","EXONERACION PARCIAL DE PAGO"=>"EP","OTRO TIPO DE BECA"=>"OT"];
        $this->medio_entero = ["PRENSA" => "PRENSA", "TELEVISI&Oacute;N" => "TELEVISI&Oacute;N", "RADIO" => "RADIO", "FOLLETOS, CARTELES, VOLANTES" => "FOLLETOS, CARTELES, VOLANTES"];
        $this->motivo_eleccion = ["PARA EMPLEARSE O AUTOEMPLEARSE" => "PARA EMPLEARSE O AUTOEMPLEARSE", "MEJORAR SU SITUACI&Oacute;N EN EL TRABAJO" => "MEJORAR SU SITUACI&Oacute;N EN EL TRABAJO", "PARA AHORRAR GASTOS AL INGRESO FAMILIAR" => "PARA AHORRAR GASTOS AL INGRESO FAMILIAR", "POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCI&Oacute;N EDUCATIVA" => "POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCI&Oacute;N EDUCATIVA", "POR DISPOSICI&Oacute;N DE TIEMPO LIBRE" => "POR DISPOSICI&Oacute;N DE TIEMPO LIBRE"];       
        $this->path_file ="/storage/uploadFiles/alumnos/";
        
    }
    protected function discapacidad(){  
        return $this->discapaciodad;       
    }
   
    protected function escolaridad(){  
        return $this->escolaridad;       
    }
    
    protected function etnia(){
        return $this->etnia;
    }   
    
    protected function estado_civil(){
        return $this->estado_civil;
    }
    
    protected function tinscripcion(){  
        return $this->tinscripcion;       
    }
    
    protected function abrinscri(){  
        return $this->abrinscri;       
    }
    
    protected function medio_entero(){  
        return $this->medio_entero;       
    }
    
    protected function motivo_eleccion(){  
        return $this->motivo_eleccion;       
    }
    
    protected function path_file(){  
        return $this->path_file;       
    }
    
    protected function estado(){
        $estado = DB::table('estados')->pluck('nombre','nombre');
        return $estado;
    }
    
    protected function municipio($id_estado){
        $municipio = DB::table('estados')->where('')->pluck('nombre','nombre');
        return $municipio;
    }  
    
    
}