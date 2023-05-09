@extends('theme.formatos.hlayout')
@if($opt=="ARC-02")
    @section('title', 'AUTORIZACIÓN ARC-02 | SIVyC Icatech')
@else
    @section('title', 'AUTORIZACIÓN ARC-01 | SIVyC Icatech')
@endif
@section('css')
    <style>          
         @page { margin-bottom: 115px;}
        .tablas{border-collapse: collapse; width: 100%; }        
        .tablas tr th {padding:0px;margin:0px; page-break-inside: avoid; }
        .tablas th, .tablas td{page-break-inside: avoid;font-size: 7px; border: gray 1px solid; text-align: center;font-weight:bold;}
        
        .tablaf { page-break-inside: avoid; border-collapse: collapse; width: 100%; white-space: nowrap; height: auto; margin-top:15px;}     
        .tablaf tr td { font-size: 7px; text-align: center; padding: 0px 0px;}
        .tablad { page-break-inside: avoid; font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px 2px 2px 0; border-collapse: collapse; }
        .tablad tr td{padding: 1px 5px 0 5px; font-size: 7px;}
        

        #titulo { position: fixed; top: 35px; }
        #para {position: relative; top: -30px; height:auto; width:60%; font-size: 8px; font-weight:bold; margin-bottom:-40px;}  
    
    </style>    
@endsection
@section('header') 
    @php
        if(strpos($reg_unidad->unidad.$reg_unidad->cct, "07EIC0")) 
            $nombre_unidad = "UNIDAD DE CAPACITACIÓN ";
        else
            $nombre_unidad = "ACCIÓN MÓVIL ";   
        
        $valido= $reg_cursos[0]->valido;
        $munidad = $reg_cursos[0]->munidad;
        $nmunidad = $reg_cursos[0]->nmunidad;
        $fecha = $asunto = $det = $memo =  $obs = "";
        switch($opt){
            case 'ARC-01':
                $fecha = $reg_cursos[0]->fecha_apertura;
                $memo = $reg_cursos[0]->mvalida;
                $asunto = "AUTORIZACIÓN DE ASIGNACIÓN DE CLAVES DE APERTURAS";
                $det = "Por este medio envió a Usted el formato de autorización de asignación de claves de apertura de servicios, en atención a la solicitud con número de memorándum $munidad.";

            break;
            case 'ARC-02':
                $fecha = $reg_cursos[0]->fecha_modificacion;
                $memo = $reg_cursos[0]->nmacademico;
                $asunto = "REPROGRAMACIÓN, MODIFICACIÓN O CANCELACIÓN DE APERTURAS";
                $det = "Por este medio envió a Usted el formato de autorización de reprogramación, modificación o cancelación de aperturas de servicios, en atención a la solicitud con número de memorándum $nmunidad.";
            break;
        }
        //CONVERSION DE FECHA                
        $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
        $mes = $meses[date('m',strtotime($fecha))];
        $fecha = date('d',strtotime($fecha)).' de '.$mes.' del '.date('Y',strtotime($fecha));
    @endphp
    <div id="titulo">
        <h3>AUTORIZACIÓN DE {{$opt}}</h3>
        <br/>
        <table width="100%">
            <tr>        
                <td style='text-align:right;font-size:10px;'>
                    DIRECCIÓN TÉCNICA ACADÉMICA<br/>                    
                    Memorándum No. {{ $memo}} <br/>
                    Tuxtla Gutiérrez, Chiapas; {{$fecha}}.<br/>
                </td>                    
            </tr>   
        </table>
    </div>    
@endsection
@section('content')
    <div id="para"> 
        PARA: {{ $reg_unidad->dunidad }}.- {{$reg_unidad->pdunidad}}<br/>
        DE: {{ $reg_unidad->dacademico }}.- {{$reg_unidad->pdacademico}}<br/>
        ASUNTO: {{ $asunto }}<br/><br/>        
    </div>  
    
    <p style='text-align:justify; font-size: 10px;'>{{ $det }}</p>    
     <table class="tablas">
         <thead>
             <tr>
                 <th style="padding: 0px;" rowspan="2" >SERVICIO</th>
                 <th style="padding: 0px;" rowspan="2" >UNIDAD DE CAPACITACIÓN</th>
                 <th style="padding: 0px;" rowspan="2" >ESPECIALIDAD</th>
                 <th style="padding: 0px;" rowspan="2" width="8%" >NOMBRE</th>
                 <th style="padding: 0px;" rowspan="2" >CLAVE</th>
                 <th style="padding: 0px;" rowspan="2" >MOD</th>
                 <th style="padding: 0px;" colspan="2" >TIPO DE<br>CAPACI<BR/>TACIÓN</th>
                 <th style="padding: 0px;" rowspan="2" >D<br>U<br>R<br>A</th>
                 <th style="padding: 0px;" rowspan="2" >FECHA DE<br>INICIO</th>
                 <th style="padding: 0px;" rowspan="2" >FECHA DE<br>TÉRMINO</th>
                 <th style="padding: 0px;" rowspan="2" >HORARIO</th>
                 <th style="padding: 0px;" rowspan="2" >DIAS</th>
                 <th style="padding: 0px;" rowspan="2" >C<br>U<br>P<br>O</th>
                 <th style="padding: 0px;" rowspan="2" width="8%" >INSTRUCTOR</th>
                 <th style="padding: 0px;" rowspan="2" >CRITE<br>RIO<br>DE<br>PAGO</th>
                 <th style="padding: 0px;" rowspan="2" >MUNICIPIO</th>
                 <th style="padding: 0px;" rowspan="2" width="10%" >ESPACIO FISICO<br>MEDIO VIRTUAL</th>
                 <th style="padding: 0px;" rowspan="2" >ZON<br>A<br>ECO<br>NOM<br>ICA</th>
                 <th style="padding: 0px;" colspan="3" >TIPO DE <BR/> CUOTA</th>
                 <th style="padding: 0px;" rowspan="2" width='15%'>OBSERVACIONES</th>
             </tr>
             <tr>
                 <th >PRES<br>EN</th>
                 <th >DISTA<br>NCIA</th>
                 <th >ORD</th>
                 <th >EXO</th>
                 <th >RED</th>
             </tr>
         </thead>
         <tbody>
             @foreach($reg_cursos as $a)
             @php
                if(strpos($reg_unidad->unidad.$a->cct, "07EIC0")) 
                    $nom_unidad = "UNIDAD DE CAPACITACIÓN ";
                else
                    $nom_unidad = "ACCIÓN MÓVIL ";
             @endphp
             <tr>
                 <th>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION EXTRAORDINARIA';} @endphp</th>
                 <th>{{$nom_unidad}} {{ $a->unidad }}</th>
                 <th>{{ $a->espe }}</th>
                 <th>{{ $a->curso }}</th>
                 <th>{{ $a->clave }}</th>
                 <th>{{ $a->mod }}</th>
                 <th>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</th>
                 <th>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</th>
                 <th>{{ $a->dura }}</th>
                 <th>{{ $a->inicio }}</th>
                 <th>{{ $a->termino }}</th>
                 <th>{{ $a->horario }}</th>
                 <th>{{ $a->dia }}</th>
                 <th>{{ $a->mujer + $a->hombre }}</th>
                 <tH>{{ $a->nombre }}</tH>
                 <th>{{ $a->cp }}</th>
                 <th>{{ $a->muni }}</th>
                 <th>{{ $a->efisico }}</th>
                 <th>{{ $a->ze }}</th>
                 <th>@if($a->tipo=="PINS"){{ "X" }}@endif</th>
                 <th>@if($a->tipo=="EXO"){{ "X" }}@endif</th>
                 <th>@if($a->tipo=="EPAR"){{ "X" }}@endif</th>
                 <th>@if($opt == "ARC-01"){{ $a->nota }} @else {{ $a->observaciones}}@endif</th>
             </tr>             
             @endforeach
         </tbody>
     </table>
     <br/>
     <div style="page-break-inside: avoid; padding:0px; margin:0px;font-size: 7px;"><b>&nbsp;&nbsp;&nbsp;CRITERIO DE CONTRATACION Y PAGO</b><br/>
        <table class="tablad">
            <tr>
                <td colspan="4"><br/><b>1. PRIMARIA INCONCLUSA 2. PRIMARIA 3. SECUNDARIA 4. BACHILLERATO / PREPARATORIA O CARRERA TECNICA </b></td>
            </tr> 
            <tr>
                <td colspan="4"><b>5. PROFESIONAL TRUNCA 6. PROFESIONAL PASANTE 7. PROFESIONAL(TITULO Y/O CEDULA) 8. MAESTRIA (PASANTE) </b></td>
            </tr>
            <tr>
                <td colspan="4"><b>9. MAESTRIA (TITULO Y/O CEDULA) 10. DOCTORADO (PASANTE) 11. DOCTORADO(TITULO Y/O CEDULA) </b><br/>&nbsp;</td>
            </tr>
        </table>    
    </div>
     <table style='with:100%; border-spacing: 0px;' class="tablaf" >
         <tr>
             <td align="center" style='padding:50px 0 2px 0;  text-alingn:center; width:240px;border: gray 1px solid;'>
                 <b>{{ $realizo }}</b><br/>
                 ________________________________________________<br/><br/>
                 <b>{{ $puesto }} </b><br/><br/><br/>
                 <b>ELABORÓ</b>
             </td>
             <td>&nbsp; &nbsp; </td>
             <td align="center" style='padding:50px 0 2px 0; text-alingn:center; width:240px;border: gray 1px solid;'>
                 <b>{{ $reg_unidad->jcyc }}</b><br/>
                 ________________________________________________<br/><br/>
                 <b>{{ $reg_unidad->pjcyc }} </b><br/><br/><br/>
                 <b>REVISÓ</b>
             </td>
             <td>&nbsp; &nbsp; </td>
             <td align="center" style='padding:50px 0 2px 0; text-alingn:center; width:240px;border: gray 1px solid;'>
                 <b>{{ $reg_unidad->dacademico }}</b><br/>
                 ________________________________________________<br/><br/>
                 <b>{{ $reg_unidad->pdacademico}} </b><br/><br/><br/>
                 <b>AUTORIZÓ</b>
             </td>
             <td>&nbsp; &nbsp; </td>
             <td align="center" style='padding-top:100px; text-alingn:center; width:230px;border: gray 1px solid;'><b>SELLO DE LA DIRECCIÓN</b></td>
         </tr>
     </table>
     <p style="font-size: 7px;">CCP. {{ $reg_unidad->academico}}.-{{ $reg_unidad->pacademico}} DE LA UNIDAD DE CAPACITACIÓN {{ $reg_unidad->ubicacion}}.<br/>
     ARCHIVO/MINUTARIO<BR/>
     </p>
@endsection
@section('js')
<script type="text/php">
    if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 538, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);               
            ');
        }
</script>
@endsection