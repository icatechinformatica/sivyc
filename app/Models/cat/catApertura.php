<?php

namespace App\Models\cat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\carbon;
use Carbon\CarbonPeriod;

trait catApertura
{      
    protected function motivo_arc02(){
        $tcuota= ["REPROGRAMACIÓN FECHA/HORA"=>"REPROGRAMACIÓN FECHA/HORA","CAMBIO DE INSTRUCTOR"=>"CAMBIO DE INSTRUCTOR","ERROR MECANOGRÁFICO"=>"ERROR MECANOGRÁFICO","CANCELACIÓN"=>"CANCELACIÓN"];
        return $tcuota;
       
    }

    protected function tcuota(){
        //$tcuota= ["PINS"=>"PAGO DE INSCRIPCION","EXO"=>"EXONERACION TOTAL DE PAGO","EPAR"=>"EXONERACION PARCIAL DE PAGO"];
        $tcuota= ["PINS"=>"PAGO ORDINARIO","EXO"=>"EXONERACION","EPAR"=>"REDUCCION DE CUOTA"];
        return $tcuota;
       
    }    
    protected function tinscripcion(){
        //$tinscripcion= ["PAGO DE INSCRIPCION"=>"PAGO ORDINARIO","EXONERACION TOTAL DE PAGO"=>"EXONERACION","EXONERACION PARCIAL DE PAGO"=>"REDUCCION DE CUOTA","OTRO TIPO DE BECA"=>"OTRO TIPO DE BECA"];
        $tinscripcion= ["PAGO ORDINARIO"=>"PAGO ORDINARIO","EXONERACION"=>"EXONERACION","REDUCCION DE CUOTA"=>"REDUCCION DE CUOTA","OTRO TIPO DE BECA"=>"OTRO TIPO DE BECA"];
        return $tinscripcion;
       
    }
    protected function abrinscri(){
        //$abrinscri = ["PAGO DE INSCRIPCION"=>"PI","EXONERACION TOTAL DE PAGO"=>"ET","EXONERACION PARCIAL DE PAGO"=>"EP","OTRO TIPO DE BECA"=>"OT"];
        $abrinscri = ["PAGO ORDINARIO"=>"PI","EXONERACION"=>"ET","REDUCCION DE CUOTA"=>"EP","OTRO TIPO DE BECA"=>"OT"];
        return $abrinscri;
    }
    protected function dia($dia){
        switch ($dia) {
            case '0':
                $dias = "DOMINGO";
                break;
            case '1':
                $dias = "LUNES";
                break;
            case '2':
                $dias = "MARTES";
                break;
            case '3':
                $dias = "MIERCOLES";
                break;
            case '4':
                $dias = "JUEVES";
                break;
            case '5':
                $dias = "VIERNES";
                break;
            case '6':
                $dias = "SABADO";
                break;
            default:
                # code...
                break;
        }
        return $dias;
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
        $plantel = ["UNIDAD"=>"UNIDAD DE CAPACITACIÓN","AULA MÓVIL"=>"AULA MÓVIL","ACCIÓN MÓVIL"=>"ACCIÓN MÓVIL", "CAPACITACIÓN EXTERNA"=>"CAPACITACIÓN EXTERNA","A DISTANCIA"=>"A DISTANCIA"];
        return $plantel;       
    }

    protected function efisico(){
        $efisico = ["EN LINEA"=>"EN LINEA","ACCIÓN MÓVIL"=>"ACCIÓN MÓVIL", "UNIDAD DE CAPACITACIÓN"=>"UNIDAD DE CAPACITACIÓN","AULA MÓVIL"=>"AULA MÓVIL", "OTRO"=>"OTRO"];
        return $efisico;
    }
    
    protected function dependencia($unidad){  
        $dependencia = DB::table('convenios')->where('unidades','like','%'.$unidad.'%')->orderby('institucion')->distinct('institucion')->pluck('institucion','institucion');    
        return $dependencia;       
    }
    
    protected function convenio($unidad,$tipo){  
        $convenio = DB::table('convenios')->where('tipo_convenio',$tipo)->where('activo','true')
        ->WHERE('fecha_vigencia','>=',DB::raw("TO_DATE(to_char(CURRENT_DATE,'YYYY-MM-DD'),'YYYY-MM-DD')"))
        ->where('unidades','like','%'.$unidad.'%')->orderby('no_convenio')->pluck('no_convenio','id');    
        return $convenio;       
    }
    
    protected function exoneracion($id_unidad){  
        $exoneracion = DB::table('exoneraciones')->where('activo','true')->whereIn('id_unidad_capacitacion',[$id_unidad,0])->orderby('no_memorandum')->pluck('no_memorandum','no_memorandum');        
        return $exoneracion;       
    }

    protected function programa(){  
        $programa = DB::table('tbl_cursos')->where('programa','!=','0')->where('programa','!=','N')->where('programa', 'not like', '%21%')
        ->where('programa','!=','NINGUNA')->distinct()->orderby('programa')->pluck('programa','programa');    
        return $programa;       
    }

    protected function municipio(){  
        $municipio = DB::table('tbl_municipios')->where('id_estado','7')->orderby('muni')->pluck('muni','id');    
        return $municipio;       
    }

    protected function instructor($id){         
        $instructor = DB::table('instructores')
            ->select('id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'))
            ->where('id',$id)
            ->first();

        return $instructor;       
    }

    protected function instructores($grupo){
        $id_muni = $grupo->id_muni;    //dd($grupo);
        $tipo_curso = $grupo->tcapacitacion;
        $unidad = $grupo->unidad;
        $id_especialidad = $grupo->id_especialidad;
        $id_curso = $grupo->id;
        $fhini = $grupo->inicio;    //$fhini = '2021-09-01 16:00:00';
        $ffinal = $grupo->termino;    //$ffinal = '2021-09-30 18:00:00';
        $hini = date('H:i',strtotime(str_replace('.','',substr($grupo->horario, 0, 5)))); 
        $hfin = date('H:i',strtotime(str_replace('.','',substr($grupo->horario, 8, 5))));
        $hinimes = Carbon::parse($fhini)->firstOfMonth();   
        $finmes = Carbon::parse($fhini)->endOfMonth();
        $es_lunes= Carbon::parse($fhini)->is('monday');
        $period = CarbonPeriod::create($fhini,$ffinal);
        $minutos_curso= Carbon::parse($hfin)->diffInMinutes($hini);
        $segundos_curso = Carbon::parse($hfin)->diffInSeconds($hini); //dd($period);
        $id_unidad = DB::table('tbl_unidades')->where('unidad',$grupo->unidad)->pluck('id')->first();
        $id= [];
        $uno= [];   $huno = [];
        $dos = [];  $hdos = [];
        $tres = []; $htres = [];
        $cuatro = [];   $hcuatro = [];
        $cinco = [];    $hcinco = [];
        $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
            ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id', DB::raw('count(id_curso) as total'))
            ->rightJoin('instructores','t.id_instructor','=','instructores.id')
            ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            ->join('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
            ->WHERE('estado',true)
            ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
            ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
            //->where('especialidad_instructor_curso.curso_id',$grupo->id_curso)
            //->where('especialidad_instructor_curso.activo', true)
            ->WHERE('fecha_validacion','<',$grupo->inicio)
            ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$grupo->termino);
            // ->whereNotIn('instructores.id', [DB::raw("select id_instructor from (select id_instructor, count(id) as total from
			// 								    (select id_instructor, id from tbl_cursos
			// 								    where inicio >= '$hinimes'
			// 								    and inicio<= '$finmes'
			// 								    and status != 'CANCELADO') as t
			// 								    group by id_instructor) as r
            //                                 where r.total > 3")])
            /*->whereNotIn('instructores.id', [DB::raw("select id_instructor from agenda
                                                      where ((date(agenda.start)>='$fhini' and date(agenda.start)<='$ffinal' and cast(agenda.start as time)>='$hini' and cast(agenda.start as time)<'$hfin')
                                                      or (date(agenda.end)>='$fhini' and date(agenda.end)<='$ffinal' and cast(agenda.end as time)>'$hini' and cast(agenda.end as time)<='$hfin'))
                                                      group by id_instructor")])*/
            
            //->orderby('instructor')
            //->pluck('instructor','instructores.id');
            //->groupBy('t.id_instructor','instructores.id')
            //->get();    dd($instructores);
        //CRITERIO8HRS
        // foreach ($period as $value) {
        //     $suma = 0;
        //     $a= Carbon::parse($value)->format('d-m-Y 22:00');
        //     $b= Carbon::parse($value)->format('d-m-Y 00:00');   
        //     $instructores_perio = DB::table('instructores')
        //         //->select('instructores.id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'))
        //         ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
        //         ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
        //         ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
        //         //->leftJoin('agenda','instructores.id','=','agenda.id_instructor')
        //         //->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
        //         //->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
        //         ->WHERE('estado',true)
        //         ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
        //         ->whereJsonContains('unidades_disponible', [$unidad])
        //         ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
        //         ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',DB::raw("TO_DATE(to_char(CURRENT_DATE,'YYYY-MM-DD'),'YYYY-MM-DD')"))
        //         ->orderby('instructor')
        //         ->pluck('instructores.id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'));   //dd($instructores_perio);
        //     foreach ($instructores_perio as $cacahuate) {
        //         $consulta_1= DB::table('agenda')->select('start','end','id_instructor')
        //                                          ->where('id_instructor','=',$cacahuate)
        //                                          ->where('start','<=',$a)
        //                                          ->where('end','>=',$b)
        //                                          ->orderByRaw("extract(hour from start) asc")
        //                                          ->get();   //dd($consulta_1);
        //         foreach ($consulta_1 as $key => $value) {
        //             $y= Carbon::parse($value->end)->format('H:i');
        //             $x= Carbon::parse($value->start)->format('H:i');    //dd($x);  //dd($x.'||'.$y);
        //             $minutos= Carbon::parse($y)->diffInMinutes($x);
        //             $suma += $minutos;
        //             if ($suma >360) {
        //                 if (($minutos_curso+$suma)>480) {
        //                     $id[]= $value->id_instructor;
        //                 }
        //             }
        //         }
        //     }
        // }
        // //CRITERIO 40hrs
        // if ($es_lunes) {
        //     $date = Carbon::parse($fhini)->startOfWeek();
        //     $datefin= Carbon::parse($date->format('d-m-Y 22:00:00'))->addDay(6);
        //     $period_semana = CarbonPeriod::create($fhini,$datefin);
        //     $total=0;
        //     $array1=[];
        //     foreach($period as $pan){
        //         if($pan <= $datefin){
        //             $total= $total+$segundos_curso;
        //         }else{
        //             $array1[]=$pan;
        //         }
        //     }
        //     $consulta_fechas= DB::table(DB::raw("(select id_instructor,
        //                                           ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total' as total
        //                                           from agenda
        //                                           where start >= '$date' and agenda.end <= '$datefin'
        //                                           group by id_instructor, agenda.end, agenda.start) t"))
        //                                          ->where('total','>','144000')
        //                                          ->pluck('id_instructor'); //dd($consulta_fechas);
        //     foreach ($consulta_fechas as $value) {
        //         if (empty(in_array($value,$id))) {
        //             $id[]= $value;
        //         }
        //     }
        //     if(!empty($array1)){
        //         $fechaInicio= Carbon::parse($array1[0])->format('d-m-Y 00:00:00');
        //         $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //         $datefin= Carbon::parse($datefin)->addDay(6);
        //         $array2=[];
        //         $total2=0;
        //         foreach($array1 as $item){
        //             if($item <= $datefin){
        //                 $total2= $total2+$segundos_curso;
        //             }else{
        //                 $array2[]= $item;
        //             }
        //         }
        //         $consulta_fechas2= DB::table(DB::raw("(select id_instructor,
        //                                           ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total2' as total
        //                                           from agenda
        //                                           where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                           group by id_instructor, agenda.end, agenda.start) t"))
        //                                          ->where('total','>','144000')
        //                                          ->pluck('id_instructor');   //dd($consulta_fechas2);
        //         foreach ($consulta_fechas2 as $value) {
        //             if (empty(in_array($value,$id))) {
        //                 $id[]= $value;
        //             }
        //         }
        //         if(!empty($array2)){
        //                 $fechaInicio = Carbon::parse($array2[0])->format('d-m-Y 00:00:00');
        //                 $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //                 $datefin = Carbon::parse($datefin)->addDay(6);
        //                 $array3=[];
        //                 $total3=0;
        //                 foreach($array2 as $item){
        //                     if($item <= $datefin){
        //                         $total3= $total3+$segundos_curso;
        //                     }else{
        //                         $array3[]= $item;
        //                     }
        //                 }
        //                 $consulta_fechas3= DB::table(DB::raw("(select id_instructor,
        //                                                   ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total3' as total
        //                                                   from agenda
        //                                                   where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                                   group by id_instructor, agenda.end, agenda.start) t"))
        //                                                  ->where('total','>','144000')
        //                                                  ->pluck('id_instructor');
        //                 foreach ($consulta_fechas3 as $value) {
        //                     if (empty(in_array($value,$id))) {
        //                         $id[]= $value;
        //                     }
        //                 }
        //                 if (!empty($array3)) {
        //                     $fechaInicio = Carbon::parse($array3[0])->format('d-m-Y 00:00:00');
        //                     $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //                     $datefin = Carbon::parse($datefin)->addDay(6);
        //                     $array4=[];
        //                     $total4=0;
        //                     foreach($array3 as $item){
        //                         if($item <= $datefin){
        //                             $total4= $total4+$segundos_curso;
        //                         }else{
        //                             $array4[]= $item;
        //                         }
        //                     }
        //                     $consulta_fechas4= DB::table(DB::raw("(select id_instructor,
        //                                                       ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total4' as total
        //                                                       from agenda
        //                                                       where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                                       group by id_instructor, agenda.end, agenda.start) t"))
        //                                                      ->where('total','>','144000')
        //                                                      ->pluck('id_instructor');
        //                     foreach ($consulta_fechas4 as $value) {
        //                         if (empty(in_array($value,$id))) {
        //                             $id[]= $value;
        //                         }
        //                     }
        //                     if (!empty($array4)) {
        //                         $fechaInicio = Carbon::parse($array4[0])->format('d-m-Y 00:00:00');
        //                         $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //                         $datefin = Carbon::parse($datefin)->addDay(6);
        //                         $array5=[];
        //                         $total5=0;
        //                         foreach($array4 as $item){
        //                             if($item <= $datefin){
        //                                 $total5= $total5+$segundos_curso;
        //                             }else{
        //                                 $array5[]= $item;
        //                             }
        //                         }
        //                         $consulta_fechas5= DB::table(DB::raw("(select id_instructor,
        //                                                           ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total5' as total
        //                                                           from agenda
        //                                                           where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                                           group by id_instructor, agenda.end, agenda.start) t"))
        //                                                          ->where('total','>','144000')
        //                                                          ->pluck('id_instructor');
        //                         foreach ($consulta_fechas5 as $value) {
        //                             if (empty(in_array($value,$id))) {
        //                                 $id[]= $value;
        //                             }
        //                         }
        //                     }
        //                 }
        //         }
        //     }
        // } else {
        //     $date= Carbon::parse($fhini)->startOfWeek();   //dd($date);   //obtener el primer dia de la semana
        //     $datefin= Carbon::parse($date->format('d-m-Y 22:00:00'))->addDay(6); //dd($datefin);
        //     $total=0;   //vamos a contar los minutos que dura el curso a la semana y crear array´s para comprobar si el curso comparte días con otra semana
        //     $array1=[];
        //     foreach($period as $pan){
        //         if($pan <= $datefin){
        //             $total= $total+$segundos_curso; 
        //         }else{
        //             $array1[]=$pan; 
        //         }
        //     }
        //     $consulta_fechas= DB::table(DB::raw("(select id_instructor,
        //                                           ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total' as total
        //                                           from agenda
        //                                           where start >= '$date' and agenda.end <= '$datefin'
        //                                           group by id_instructor, agenda.end, agenda.start) t"))
        //                                          ->where('total','>','144000')
        //                                          ->pluck('id_instructor'); //dd($consulta_fechas);
        //     foreach ($consulta_fechas as $key => $value) {
        //         if (empty(in_array( $value,$id))) {
        //             $id[]= $value;
        //         }
        //     }
        //     if(!empty($array1)){
        //         $fechaInicio= Carbon::parse($array1[0])->format('d-m-Y 00:00:00');
        //         $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //         $datefin= Carbon::parse($datefin)->addDay(6);                  // dd($array1);
        //         $array2=[];
        //         $total2=0;
        //         foreach($array1 as $item){
        //             if($item <= $datefin){
        //                 $total2= $total2+$segundos_curso;
        //             }else{
        //                 $array2[]= $item; //dd($array2);
        //             }
        //         }
        //         $consulta_fechas2= DB::table(DB::raw("(select id_instructor,
        //                                           ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total2' as total
        //                                           from agenda
        //                                           where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                           group by id_instructor, agenda.end, agenda.start) t"))
        //                                          ->where('total','>','144000')
        //                                          ->pluck('id_instructor');  //dd($consulta_fechas2);
        //         foreach ($consulta_fechas2 as $value) {
        //             if (empty(in_array($value,$id))) {
        //                 $id[]= $value;
        //             }
        //         }
        //         if(!empty($array2)){
        //                 $fechaInicio= Carbon::parse($array2[0])->format('d-m-Y 00:00:00');      //dd($array2);
        //                 $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //                 $datefin= Carbon::parse($datefin)->addDay(6);
        //                 $array3=[];
        //                 $total3=0;
        //                 foreach($array2 as $item){
        //                     if($item <= $datefin){
        //                         $total3= $total3+$segundos_curso;
        //                     }else{
        //                         $array3[]= $item;
        //                     }
        //                 }       //dd($array3);
        //                 $consulta_fechas3= DB::table(DB::raw("(select id_instructor,
        //                                                   ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total3' as total
        //                                                   from agenda
        //                                                   where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                                   group by id_instructor, agenda.end, agenda.start) t"))
        //                                                  ->where('total','>','144000')
        //                                                  ->pluck('id_instructor');  //dd($consulta_fechas3);
        //                 foreach ($consulta_fechas3 as $value) {
        //                     if (empty(in_array($value,$id))) {
        //                         $id[]= $value;
        //                     }
        //                 }  
        //             if (!empty($array3)) {
        //                 $fechaInicio= Carbon::parse($array3[0])->format('d-m-Y 00:00:00');      //dd($array3);
        //                 $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //                 $datefin= Carbon::parse($datefin)->addDay(6);
        //                 $array4=[];
        //                 $total4=0;
        //                 foreach($array3 as $item){
        //                     if($item <= $datefin){
        //                         $total4= $total4+$segundos_curso;
        //                     }else{
        //                         $array4[]= $item;
        //                     }
        //                 } 
        //                 $consulta_fechas4= DB::table(DB::raw("(select id_instructor,
        //                                                   ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total4' as total
        //                                                   from agenda
        //                                                   where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                                   group by id_instructor, agenda.end, agenda.start) t"))
        //                                                  ->where('total','>','144000')
        //                                                  ->pluck('id_instructor');  //dd($consulta_fechas4);
        //                 foreach ($consulta_fechas4 as $value) {
        //                     if (empty(in_array($value,$id))) {
        //                         $id[]= $value;
        //                     }
        //                 }
        //                 if (!empty($array4)) {
        //                     $fechaInicio= Carbon::parse($array4[0])->format('d-m-Y 00:00:00');      //dd($array4);
        //                     $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
        //                     $datefin= Carbon::parse($datefin)->addDay(6);
        //                     $array5=[];
        //                     $total5=0;
        //                     foreach($array4 as $item){
        //                         if($item <= $datefin){
        //                             $total5= $total5+$segundos_curso;
        //                         }else{
        //                             $array5[]= $item;
        //                         }
        //                     }       //dd($array5);
        //                     $consulta_fechas5= DB::table(DB::raw("(select id_instructor,
        //                                                       ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total5' as total
        //                                                       from agenda
        //                                                       where start >= '$fechaInicio' and agenda.end <= '$datefin'
        //                                                       group by id_instructor, agenda.end, agenda.start) t"))
        //                                                      ->where('total','>','144000')
        //                                                      ->pluck('id_instructor');  //dd($consulta_fechas5);
        //                     foreach ($consulta_fechas5 as $value) {
        //                         if (empty(in_array($value,$id))) {
        //                             $id[]= $value;
        //                         }
        //                     }
        //                 }
        //             } 
        //         }
                
        //     }
        // }
        // //CRITERIO 5 MESES
        // $fivem = [];    
        // for ($i=-5; $i < 5; $i++) { 
        //     $mesActivo= Carbon::parse($ffinal)->addMonth($i);   //dd($mesActivo);
        //     $mes = Carbon::parse($mesActivo)->format('d-m-Y');
        //     $mesInicio = Carbon::parse($mes)->firstOfMonth();
        //     $mesFin = Carbon::parse($mes)->endOfMonth();
        //     $consulta = DB::table(DB::raw('(select id_instructor, 
        //                                 count(id_instructor) as total,
        //                                 start,
        //                                 agenda.end
        //                                 from agenda
        //                                 group by id_instructor,
        //                                 start,
        //                                 agenda.end) as t'))
        //                                 ->where('t.start','>=',$mesInicio)
        //                                 ->where('t.end','<=',$mesFin)
        //                                 ->groupBy('id_instructor')
        //                                 ->pluck('id_instructor');    //dd($consulta);
        //     switch ($i) {
        //         case '-5':
        //             foreach ($consulta as $value) {
        //                 $cinco[]= $value;
        //             }
        //             break;
        //         case '-4':
        //             foreach ($consulta as $value) {
        //                 $cuatro[]= $value;
        //             }
        //             break;
        //         case '-3':
        //             foreach ($consulta as $value) {
        //                 $tres[]= $value;
        //             }
        //             break;
        //         case '-2':
        //             foreach ($consulta as $value) {
        //                 $dos[]= $value;
        //             }
        //             break;
        //         case '-1':
        //             foreach ($consulta as $value) {
        //                 $uno[]= ['idis'=>$value,'conteo'=>1];
        //             }
        //             foreach ($uno as $key=>$item) {
        //                 if (in_array($uno[$key]['idis'],$dos)) {
        //                     $uno[$key]['conteo'] += 1;
                            
        //                 }
        //                 if (in_array($uno[$key]['idis'],$tres)) {
        //                     $uno[$key]['conteo'] += 1;
        //                 }
        //                 if (in_array($uno[$key]['idis'],$cuatro)) {
        //                     $uno[$key]['conteo'] += 1;
        //                 }
        //                 if (in_array($uno[$key]['idis'],$cinco)) {
        //                     $uno[$key]['conteo'] += 1;
        //                 }
        //             }
        //             break;
        //         case '1':
        //             foreach ($consulta as $value) {
        //                 $huno[]= ['idis'=>$value,'conteo'=>1];
        //             }
        //             break;
        //         case '2':
        //             foreach ($consulta as $value) {
        //                 $hdos[]= ['idis'=>$value,'conteo'=>1];
        //             }
        //             break;
        //         case '3':
        //             foreach ($consulta as $value) {
        //                 $htres[]= ['idis'=>$value,'conteo'=>1];
        //             }
        //             break;
        //         case '4':
        //             foreach ($consulta as $value) {
        //                 $hcuatro[]= ['idis'=>$value,'conteo'=>1];
        //             }
        //             break;
        //         case '5':
        //             foreach ($consulta as $value) {
        //                 $hcinco[]= ['idis'=>$value,'conteo'=>1];
        //             }
        //             foreach ($huno as $key=>$item) {
        //                 if (in_array($huno[$key]['idis'],$hdos)) {
        //                     $huno[$key]['conteo'] += 1;
                            
        //                 }
        //                 if (in_array($huno[$key]['idis'],$htres)) {
        //                     $huno[$key]['conteo'] += 1;
        //                 }
        //                 if (in_array($huno[$key]['idis'],$hcuatro)) {
        //                     $huno[$key]['conteo'] += 1;
        //                 }
        //                 if (in_array($huno[$key]['idis'],$hcinco)) {
        //                     $huno[$key]['conteo'] += 1;
        //                 }
        //             }
        //             break;
        //     }
        // }
        // foreach ($uno as $key => $value) {
        //     if ($uno[$key]['conteo'] > 4) {
        //         $fivem[] = $uno[$key]['idis'];
        //     }
        // }
        // foreach ($huno as $key => $value) {
        //     if ($huno[$key]['conteo'] > 4) {
        //         if (empty(in_array($huno[$key]['idis'],$fivem))) {
        //             $fivem[] = $huno[$key]['idis'];
        //         }
        //     }else{
        //         foreach ($uno as $item => $value) {
        //             if ( $uno[$item]['idis'] == $huno[$key]['idis'] ) {
        //                 $a = $uno[$item]['conteo'] + $huno[$key]['conteo'];
        //                 if ($a>4) {
        //                     if (empty(in_array($huno[$key]['idis'],$fivem))) {
        //                         $fivem[] = $huno[$key]['idis'];
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        // //CRITERIO UNIDADES
        // if ($tipo_curso == 'PRESENCIAL') {
        //     //$instructores = $instructores->whereJsonContains('instructores.unidades_disponible',$unidad);
        //     /*$validos= DB::table('agenda')->select('agenda.id_instructor')
        //                              ->join('instructores','agenda.id_instructor','=','instructores.id')
        //                              ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
        //                              ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
        //                              ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
        //                              ->WHERE('estado',true)
        //                              ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
        //                              ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
        //                              ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',DB::raw("TO_DATE(to_char(CURRENT_DATE,'YYYY-MM-DD'),'YYYY-MM-DD')"))
        //                              ->groupBy('agenda.id_instructor')
        //                              ->get(); //dd($validos);
        //     foreach ($validos as $key => $value) {
        //         $y = $value->id_instructor;
        //         foreach ($period as $value) {
        //             $a= Carbon::parse($value)->format('d-m-Y 22:00');    //print_r($a.'||');
        //             $b= Carbon::parse($value)->format('d-m-Y 00:00');
        //             $consulta_unidad= DB::table('agenda')->select('start','end','id_unidad','id_municipio')
        //                                                  ->where('id_instructor','=',$y)
        //                                                  ->where('start','<=',$a)
        //                                                  ->where('end','>=',$b)
        //                                                  ->orderByRaw("extract(hour from start) asc")
        //                                                  ->get();    //dd($consulta_unidad);
        //             foreach ($consulta_unidad as $fecha) { 
        //                 if ($fecha->id_municipio != $id_muni) {
        //                     $tiempo_distance = 60;  //consulta tabla de tiempos
        //                     $horaInicio= Carbon::parse($fecha->start)->format('H:i');
        //                     $horaTermino= Carbon::parse($fecha->end)->format('H:i');
        //                     if ($hfin == $horaInicio||$hini == $horaTermino) {
        //                         if (empty(in_array($y,$fivem))) {
        //                             $fivem[] = $y;
        //                         }
        //                     }
        //                     if ($hini > $horaTermino) {
        //                         $diferiencia= Carbon::parse($horaTermino)->diffInMinutes($hini);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                     if($hfin < $horaInicio){
        //                         $diferiencia= Carbon::parse($horaInicio)->diffInMinutes($hfin);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                 }else{
        //                     $tiempo_distance = 30;
        //                     $horaInicio= Carbon::parse($fecha->start)->format('H:i');
        //                     $horaTermino= Carbon::parse($fecha->end)->format('H:i');
        //                     if ($hfin == $horaInicio||$hini == $horaTermino) {
        //                         if (empty(in_array($y,$fivem))) {
        //                             $fivem[] = $y;
        //                         }
        //                     }
        //                     if ($hini > $horaTermino) {
        //                         $diferiencia= Carbon::parse($horaTermino)->diffInMinutes($hini);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                     if($hfin < $horaInicio){
        //                         $diferiencia= Carbon::parse($horaInicio)->diffInMinutes($hfin);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }*/
        // }
        
        // foreach ($fivem as $key => $value) {
        //     if (empty(in_array($value,$id))) {
        //         $id[]= $value;
        //     }
        // } 
        $instructores = $instructores//->whereNotIn('instructores.id',$id)
                        /*->where(function ($query){
                            $query->whereExists('instructores.clave_loc', '=', 'TUXTLA GUTIERREZ')
                                  ->orWhere('name', 'John');
                        })*/
                        //->orderby('instructor')
                        //->orderByRaw("instructores.asentamiento = 'TUXTLA GUTIERREZ'")
                        //->pluck('instructor','instructores.id');
                        //->inRandomOrder()
                        ->groupBy('t.id_instructor','instructores.id')
                        //->orderBy(DB::raw('total, "apellidoPaterno"'))
                        ->orderBy('instructor')
                        ->get();
                        /*if (isset($grupo->id_instructor)) {
                           $instructores = $instructores->take(4)->get();
                        }else{
                           $instructores = $instructores->take(5)->get();
                        }*/
                        //dd($instructores);    
        return $instructores;
    }

    protected function instructores_disponibles($request){
        
        //$id_muni = $grupo->id_muni;   
        $tipo_curso = $request->tipo;
        $unidad = $request->unidad;        
        $id_curso = $request->id_curso;
        if($request->inicio)
            $fhini = $request->inicio; 
        else $fhini = date("Y-m-d");
       
        $ffinal = $request->termino;
        $hini = $request->hini;
        $hfin = $request->hfin;

        //$hini = date('H:i',strtotime(str_replace('.','',substr($grupo->horario, 0, 5)))); 
        //$hfin = date('H:i',strtotime(str_replace('.','',substr($grupo->horario, 0, 5))));
        $hinimes = Carbon::parse($fhini)->firstOfMonth();   
        $finmes = Carbon::parse($fhini)->endOfMonth();
        $es_lunes= Carbon::parse($fhini)->is('monday');
        $period = CarbonPeriod::create($fhini,$ffinal);
        $minutos_curso= Carbon::parse($hfin)->diffInMinutes($hini);
        $segundos_curso = Carbon::parse($hfin)->diffInSeconds($hini); //dd($period);
        $id_unidad = DB::table('tbl_unidades')->where('unidad',$request->unidad)->pluck('id')->first();
        $id_especialidad =  DB::table('cursos')->where('id',$request->id_curso)->value('id_especialidad');

        $id= [];
        $uno= [];   $huno = [];
        $dos = [];  $hdos = [];
        $tres = []; $htres = [];
        $cuatro = [];   $hcuatro = [];
        $cinco = [];    $hcinco = [];
        $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
            ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),
            'instructores.id','instructores.curp','especialidad_instructores.unidad_solicita','instructores.clave_unidad',
            'tbl_unidades.unidad','instructores.numero_control','especialidad_instructores.fecha_validacion','especialidades.nombre',
            'especialidad_instructores.memorandum_validacion','instructores.archivo_alta',
            DB::raw('count(id_curso) as total'))
            ->rightJoin('instructores','t.id_instructor','=','instructores.id')
            ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            ->join('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
            ->join('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->WHERE('estado',true)
            ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
            ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
            ->where('especialidad_instructor_curso.curso_id',$id_curso)
            ->where('especialidad_instructor_curso.activo', true)
            ->WHERE('fecha_validacion','<',$request->inicio)
            ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$request->termino)
            // ->whereNotIn('instructores.id', [DB::raw("select id_instructor from (select id_instructor, count(id) as total from
			// 								    (select id_instructor, id from tbl_cursos
			// 								    where inicio >= '$hinimes'
			// 								    and inicio<= '$finmes'
			// 								    and status != 'CANCELADO') as t
			// 								    group by id_instructor) as r
            //                                 where r.total > 3")])
            ->whereNotIn('instructores.id', [DB::raw("select id_instructor from agenda
                                                      where ((date(agenda.start)>='$fhini' and date(agenda.start)<='$ffinal' and cast(agenda.start as time)>='$hini' and cast(agenda.start as time)<'$hfin')
                                                      or (date(agenda.end)>='$fhini' and date(agenda.end)<='$ffinal' and cast(agenda.end as time)>'$hini' and cast(agenda.end as time)<='$hfin'))
                                                      group by id_instructor")]);
            
            //->orderby('instructor')
            //->pluck('instructor','instructores.id');
            //->groupBy('t.id_instructor','instructores.id')
            //->get();    dd($instructores);
        //CRITERIO8HRS
        foreach ($period as $value) {
            $suma = 0;
            $a= Carbon::parse($value)->format('d-m-Y 22:00');
            $b= Carbon::parse($value)->format('d-m-Y 00:00');   
            $instructores_perio = DB::table('instructores')
                //->select('instructores.id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'))
                ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
                ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
                ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                //->leftJoin('agenda','instructores.id','=','agenda.id_instructor')
                //->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                //->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
                ->WHERE('estado',true)
                ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
                ->whereJsonContains('unidades_disponible', [$unidad])
                ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
                ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',DB::raw("TO_DATE(to_char(CURRENT_DATE,'YYYY-MM-DD'),'YYYY-MM-DD')"))
                ->orderby('instructor')
                ->pluck('instructores.id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'));   //dd($instructores_perio);
            foreach ($instructores_perio as $cacahuate) {
                $consulta_1= DB::table('agenda')->select('start','end','id_instructor')
                                                 ->where('id_instructor','=',$cacahuate)
                                                 ->where('start','<=',$a)
                                                 ->where('end','>=',$b)
                                                 ->orderByRaw("extract(hour from start) asc")
                                                 ->get();   //dd($consulta_1);
                foreach ($consulta_1 as $key => $value) {
                    $y= Carbon::parse($value->end)->format('H:i');
                    $x= Carbon::parse($value->start)->format('H:i');    //dd($x);  //dd($x.'||'.$y);
                    $minutos= Carbon::parse($y)->diffInMinutes($x);
                    $suma += $minutos;
                    if ($suma >360) {
                        if (($minutos_curso+$suma)>480) {
                            $id[]= $value->id_instructor;
                        }
                    }
                }
            }
        }
        // //CRITERIO 40hrs
        if ($es_lunes) {
            $date = Carbon::parse($fhini)->startOfWeek();
            $datefin= Carbon::parse($date->format('d-m-Y 22:00:00'))->addDay(6);
            $period_semana = CarbonPeriod::create($fhini,$datefin);
            $total=0;
            $array1=[];
            foreach($period as $pan){
                if($pan <= $datefin){
                    $total= $total+$segundos_curso;
                }else{
                    $array1[]=$pan;
                }
            }
            $consulta_fechas= DB::table(DB::raw("(select id_instructor,
                                                  ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total' as total
                                                  from agenda
                                                  where start >= '$date' and agenda.end <= '$datefin'
                                                  group by id_instructor, agenda.end, agenda.start) t"))
                                                 ->where('total','>','144000')
                                                 ->pluck('id_instructor'); //dd($consulta_fechas);
            foreach ($consulta_fechas as $value) {
                if (empty(in_array($value,$id))) {
                    $id[]= $value;
                }
            }
            if(!empty($array1)){
                $fechaInicio= Carbon::parse($array1[0])->format('d-m-Y 00:00:00');
                $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                $datefin= Carbon::parse($datefin)->addDay(6);
                $array2=[];
                $total2=0;
                foreach($array1 as $item){
                    if($item <= $datefin){
                        $total2= $total2+$segundos_curso;
                    }else{
                        $array2[]= $item;
                    }
                }
                $consulta_fechas2= DB::table(DB::raw("(select id_instructor,
                                                  ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total2' as total
                                                  from agenda
                                                  where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                  group by id_instructor, agenda.end, agenda.start) t"))
                                                 ->where('total','>','144000')
                                                 ->pluck('id_instructor');   //dd($consulta_fechas2);
                foreach ($consulta_fechas2 as $value) {
                    if (empty(in_array($value,$id))) {
                        $id[]= $value;
                    }
                }
                if(!empty($array2)){
                        $fechaInicio = Carbon::parse($array2[0])->format('d-m-Y 00:00:00');
                        $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                        $datefin = Carbon::parse($datefin)->addDay(6);
                        $array3=[];
                        $total3=0;
                        foreach($array2 as $item){
                            if($item <= $datefin){
                                $total3= $total3+$segundos_curso;
                            }else{
                                $array3[]= $item;
                            }
                        }
                        $consulta_fechas3= DB::table(DB::raw("(select id_instructor,
                                                          ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total3' as total
                                                          from agenda
                                                          where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                          group by id_instructor, agenda.end, agenda.start) t"))
                                                         ->where('total','>','144000')
                                                         ->pluck('id_instructor');
                        foreach ($consulta_fechas3 as $value) {
                            if (empty(in_array($value,$id))) {
                                $id[]= $value;
                            }
                        }
                        if (!empty($array3)) {
                            $fechaInicio = Carbon::parse($array3[0])->format('d-m-Y 00:00:00');
                            $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                            $datefin = Carbon::parse($datefin)->addDay(6);
                            $array4=[];
                            $total4=0;
                            foreach($array3 as $item){
                                if($item <= $datefin){
                                    $total4= $total4+$segundos_curso;
                                }else{
                                    $array4[]= $item;
                                }
                            }
                            $consulta_fechas4= DB::table(DB::raw("(select id_instructor,
                                                              ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total4' as total
                                                              from agenda
                                                              where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                              group by id_instructor, agenda.end, agenda.start) t"))
                                                             ->where('total','>','144000')
                                                             ->pluck('id_instructor');
                            foreach ($consulta_fechas4 as $value) {
                                if (empty(in_array($value,$id))) {
                                    $id[]= $value;
                                }
                            }
                            if (!empty($array4)) {
                                $fechaInicio = Carbon::parse($array4[0])->format('d-m-Y 00:00:00');
                                $datefin = Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                                $datefin = Carbon::parse($datefin)->addDay(6);
                                $array5=[];
                                $total5=0;
                                foreach($array4 as $item){
                                    if($item <= $datefin){
                                        $total5= $total5+$segundos_curso;
                                    }else{
                                        $array5[]= $item;
                                    }
                                }
                                $consulta_fechas5= DB::table(DB::raw("(select id_instructor,
                                                                  ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total5' as total
                                                                  from agenda
                                                                  where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                                  group by id_instructor, agenda.end, agenda.start) t"))
                                                                 ->where('total','>','144000')
                                                                 ->pluck('id_instructor');
                                foreach ($consulta_fechas5 as $value) {
                                    if (empty(in_array($value,$id))) {
                                        $id[]= $value;
                                    }
                                }
                            }
                        }
                }
            }
        } else {
            $date= Carbon::parse($fhini)->startOfWeek();   //dd($date);   //obtener el primer dia de la semana
            $datefin= Carbon::parse($date->format('d-m-Y 22:00:00'))->addDay(6); //dd($datefin);
            $total=0;   //vamos a contar los minutos que dura el curso a la semana y crear array´s para comprobar si el curso comparte días con otra semana
            $array1=[];
            foreach($period as $pan){
                if($pan <= $datefin){
                    $total= $total+$segundos_curso; 
                }else{
                    $array1[]=$pan; 
                }
            }
            $consulta_fechas= DB::table(DB::raw("(select id_instructor,
                                                  ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total' as total
                                                  from agenda
                                                  where start >= '$date' and agenda.end <= '$datefin'
                                                  group by id_instructor, agenda.end, agenda.start) t"))
                                                 ->where('total','>','144000')
                                                 ->pluck('id_instructor'); //dd($consulta_fechas);
            foreach ($consulta_fechas as $key => $value) {
                if (empty(in_array( $value,$id))) {
                    $id[]= $value;
                }
            }
            if(!empty($array1)){
                $fechaInicio= Carbon::parse($array1[0])->format('d-m-Y 00:00:00');
                $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                $datefin= Carbon::parse($datefin)->addDay(6);                  // dd($array1);
                $array2=[];
                $total2=0;
                foreach($array1 as $item){
                    if($item <= $datefin){
                        $total2= $total2+$segundos_curso;
                    }else{
                        $array2[]= $item; //dd($array2);
                    }
                }
                $consulta_fechas2= DB::table(DB::raw("(select id_instructor,
                                                  ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total2' as total
                                                  from agenda
                                                  where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                  group by id_instructor, agenda.end, agenda.start) t"))
                                                 ->where('total','>','144000')
                                                 ->pluck('id_instructor');  //dd($consulta_fechas2);
                foreach ($consulta_fechas2 as $value) {
                    if (empty(in_array($value,$id))) {
                        $id[]= $value;
                    }
                }
                if(!empty($array2)){
                        $fechaInicio= Carbon::parse($array2[0])->format('d-m-Y 00:00:00');      //dd($array2);
                        $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                        $datefin= Carbon::parse($datefin)->addDay(6);
                        $array3=[];
                        $total3=0;
                        foreach($array2 as $item){
                            if($item <= $datefin){
                                $total3= $total3+$segundos_curso;
                            }else{
                                $array3[]= $item;
                            }
                        }       //dd($array3);
                        $consulta_fechas3= DB::table(DB::raw("(select id_instructor,
                                                          ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total3' as total
                                                          from agenda
                                                          where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                          group by id_instructor, agenda.end, agenda.start) t"))
                                                         ->where('total','>','144000')
                                                         ->pluck('id_instructor');  //dd($consulta_fechas3);
                        foreach ($consulta_fechas3 as $value) {
                            if (empty(in_array($value,$id))) {
                                $id[]= $value;
                            }
                        }  
                    if (!empty($array3)) {
                        $fechaInicio= Carbon::parse($array3[0])->format('d-m-Y 00:00:00');      //dd($array3);
                        $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                        $datefin= Carbon::parse($datefin)->addDay(6);
                        $array4=[];
                        $total4=0;
                        foreach($array3 as $item){
                            if($item <= $datefin){
                                $total4= $total4+$segundos_curso;
                            }else{
                                $array4[]= $item;
                            }
                        } 
                        $consulta_fechas4= DB::table(DB::raw("(select id_instructor,
                                                          ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total4' as total
                                                          from agenda
                                                          where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                          group by id_instructor, agenda.end, agenda.start) t"))
                                                         ->where('total','>','144000')
                                                         ->pluck('id_instructor');  //dd($consulta_fechas4);
                        foreach ($consulta_fechas4 as $value) {
                            if (empty(in_array($value,$id))) {
                                $id[]= $value;
                            }
                        }
                        if (!empty($array4)) {
                            $fechaInicio= Carbon::parse($array4[0])->format('d-m-Y 00:00:00');      //dd($array4);
                            $datefin= Carbon::parse($fechaInicio)->format('d-m-Y 22:00:00');
                            $datefin= Carbon::parse($datefin)->addDay(6);
                            $array5=[];
                            $total5=0;
                            foreach($array4 as $item){
                                if($item <= $datefin){
                                    $total5= $total5+$segundos_curso;
                                }else{
                                    $array5[]= $item;
                                }
                            }       //dd($array5);
                            $consulta_fechas5= DB::table(DB::raw("(select id_instructor,
                                                              ( (sum(extract(Epoch from cast(age(agenda.end,agenda.start) as time) )) ) * ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )+ '$total5' as total
                                                              from agenda
                                                              where start >= '$fechaInicio' and agenda.end <= '$datefin'
                                                              group by id_instructor, agenda.end, agenda.start) t"))
                                                             ->where('total','>','144000')
                                                             ->pluck('id_instructor');  //dd($consulta_fechas5);
                            foreach ($consulta_fechas5 as $value) {
                                if (empty(in_array($value,$id))) {
                                    $id[]= $value;
                                }
                            }
                        }
                    } 
                }
                
            }
        }
        // //CRITERIO 5 MESES
        $fivem = [];    
        for ($i=-5; $i < 5; $i++) { 
            $mesActivo= Carbon::parse($ffinal)->addMonth($i);   //dd($mesActivo);
            $mes = Carbon::parse($mesActivo)->format('d-m-Y');
            $mesInicio = Carbon::parse($mes)->firstOfMonth();
            $mesFin = Carbon::parse($mes)->endOfMonth();
            $consulta = DB::table(DB::raw('(select id_instructor, 
                                        count(id_instructor) as total,
                                        start,
                                        agenda.end
                                        from agenda
                                        group by id_instructor,
                                        start,
                                        agenda.end) as t'))
                                        ->where('t.start','>=',$mesInicio)
                                        ->where('t.end','<=',$mesFin)
                                        ->groupBy('id_instructor')
                                        ->pluck('id_instructor');    //dd($consulta);
            switch ($i) {
                case '-5':
                    foreach ($consulta as $value) {
                        $cinco[]= $value;
                    }
                    break;
                case '-4':
                    foreach ($consulta as $value) {
                        $cuatro[]= $value;
                    }
                    break;
                case '-3':
                    foreach ($consulta as $value) {
                        $tres[]= $value;
                    }
                    break;
                case '-2':
                    foreach ($consulta as $value) {
                        $dos[]= $value;
                    }
                    break;
                case '-1':
                    foreach ($consulta as $value) {
                        $uno[]= ['idis'=>$value,'conteo'=>1];
                    }
                    foreach ($uno as $key=>$item) {
                        if (in_array($uno[$key]['idis'],$dos)) {
                            $uno[$key]['conteo'] += 1;
                            
                        }
                        if (in_array($uno[$key]['idis'],$tres)) {
                            $uno[$key]['conteo'] += 1;
                        }
                        if (in_array($uno[$key]['idis'],$cuatro)) {
                            $uno[$key]['conteo'] += 1;
                        }
                        if (in_array($uno[$key]['idis'],$cinco)) {
                            $uno[$key]['conteo'] += 1;
                        }
                    }
                    break;
                case '1':
                    foreach ($consulta as $value) {
                        $huno[]= ['idis'=>$value,'conteo'=>1];
                    }
                    break;
                case '2':
                    foreach ($consulta as $value) {
                        $hdos[]= ['idis'=>$value,'conteo'=>1];
                    }
                    break;
                case '3':
                    foreach ($consulta as $value) {
                        $htres[]= ['idis'=>$value,'conteo'=>1];
                    }
                    break;
                case '4':
                    foreach ($consulta as $value) {
                        $hcuatro[]= ['idis'=>$value,'conteo'=>1];
                    }
                    break;
                case '5':
                    foreach ($consulta as $value) {
                        $hcinco[]= ['idis'=>$value,'conteo'=>1];
                    }
                    foreach ($huno as $key=>$item) {
                        if (in_array($huno[$key]['idis'],$hdos)) {
                            $huno[$key]['conteo'] += 1;
                            
                        }
                        if (in_array($huno[$key]['idis'],$htres)) {
                            $huno[$key]['conteo'] += 1;
                        }
                        if (in_array($huno[$key]['idis'],$hcuatro)) {
                            $huno[$key]['conteo'] += 1;
                        }
                        if (in_array($huno[$key]['idis'],$hcinco)) {
                            $huno[$key]['conteo'] += 1;
                        }
                    }
                    break;
            }
        }
        foreach ($uno as $key => $value) {
            if ($uno[$key]['conteo'] > 4) {
                $fivem[] = $uno[$key]['idis'];
            }
        }
        foreach ($huno as $key => $value) {
            if ($huno[$key]['conteo'] > 4) {
                if (empty(in_array($huno[$key]['idis'],$fivem))) {
                    $fivem[] = $huno[$key]['idis'];
                }
            }else{
                foreach ($uno as $item => $value) {
                    if ( $uno[$item]['idis'] == $huno[$key]['idis'] ) {
                        $a = $uno[$item]['conteo'] + $huno[$key]['conteo'];
                        if ($a>4) {
                            if (empty(in_array($huno[$key]['idis'],$fivem))) {
                                $fivem[] = $huno[$key]['idis'];
                            }
                        }
                    }
                }
            }
        }
        // //CRITERIO UNIDADES
        // if ($tipo_curso == 'PRESENCIAL') {
        //     //$instructores = $instructores->whereJsonContains('instructores.unidades_disponible',$unidad);
        //     /*$validos= DB::table('agenda')->select('agenda.id_instructor')
        //                              ->join('instructores','agenda.id_instructor','=','instructores.id')
        //                              ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
        //                              ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
        //                              ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
        //                              ->WHERE('estado',true)
        //                              ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')
        //                              ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
        //                              ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',DB::raw("TO_DATE(to_char(CURRENT_DATE,'YYYY-MM-DD'),'YYYY-MM-DD')"))
        //                              ->groupBy('agenda.id_instructor')
        //                              ->get(); //dd($validos);
        //     foreach ($validos as $key => $value) {
        //         $y = $value->id_instructor;
        //         foreach ($period as $value) {
        //             $a= Carbon::parse($value)->format('d-m-Y 22:00');    //print_r($a.'||');
        //             $b= Carbon::parse($value)->format('d-m-Y 00:00');
        //             $consulta_unidad= DB::table('agenda')->select('start','end','id_unidad','id_municipio')
        //                                                  ->where('id_instructor','=',$y)
        //                                                  ->where('start','<=',$a)
        //                                                  ->where('end','>=',$b)
        //                                                  ->orderByRaw("extract(hour from start) asc")
        //                                                  ->get();    //dd($consulta_unidad);
        //             foreach ($consulta_unidad as $fecha) { 
        //                 if ($fecha->id_municipio != $id_muni) {
        //                     $tiempo_distance = 60;  //consulta tabla de tiempos
        //                     $horaInicio= Carbon::parse($fecha->start)->format('H:i');
        //                     $horaTermino= Carbon::parse($fecha->end)->format('H:i');
        //                     if ($hfin == $horaInicio||$hini == $horaTermino) {
        //                         if (empty(in_array($y,$fivem))) {
        //                             $fivem[] = $y;
        //                         }
        //                     }
        //                     if ($hini > $horaTermino) {
        //                         $diferiencia= Carbon::parse($horaTermino)->diffInMinutes($hini);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                     if($hfin < $horaInicio){
        //                         $diferiencia= Carbon::parse($horaInicio)->diffInMinutes($hfin);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                 }else{
        //                     $tiempo_distance = 30;
        //                     $horaInicio= Carbon::parse($fecha->start)->format('H:i');
        //                     $horaTermino= Carbon::parse($fecha->end)->format('H:i');
        //                     if ($hfin == $horaInicio||$hini == $horaTermino) {
        //                         if (empty(in_array($y,$fivem))) {
        //                             $fivem[] = $y;
        //                         }
        //                     }
        //                     if ($hini > $horaTermino) {
        //                         $diferiencia= Carbon::parse($horaTermino)->diffInMinutes($hini);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                     if($hfin < $horaInicio){
        //                         $diferiencia= Carbon::parse($horaInicio)->diffInMinutes($hfin);
        //                         if ($diferiencia < $tiempo_distance) {
        //                             if (empty(in_array($y,$fivem))) {
        //                                 $fivem[] = $y;
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }*/
        // }
        
        // foreach ($fivem as $key => $value) {
        //     if (empty(in_array($value,$id))) {
        //         $id[]= $value;
        //     }
        // } 
        $instructores = $instructores//->whereNotIn('instructores.id',$id)
                        /*->where(function ($query){
                            $query->whereExists('instructores.clave_loc', '=', 'TUXTLA GUTIERREZ')
                                  ->orWhere('name', 'John');
                        })*/
                        //->orderby('instructor')
                        //->orderByRaw("instructores.asentamiento = 'TUXTLA GUTIERREZ'")
                        //->pluck('instructor','instructores.id');
                        //->inRandomOrder()
                        ->groupBy('t.id_instructor','instructores.id','especialidad_instructores.unidad_solicita','tbl_unidades.unidad','especialidad_instructores.fecha_validacion',
                        'especialidades.nombre','especialidad_instructores.memorandum_validacion')
                        //->orderBy(DB::raw('total, "apellidoPaterno"'))
                        ->orderBy('instructor')
                        ->get();
                        /*if (isset($grupo->id_instructor)) {
                           $instructores = $instructores->take(4)->get();
                        }else{
                           $instructores = $instructores->take(5)->get();
                        }*/
                        //dd($instructores);    
        return $instructores;
    }


}