@extends('theme.formatos.hlayout'.$layout_año)
@if($opt=="ARC-02")
    @section('title', 'AUTORIZACIÓN ARC-02 | SIVyC Icatech')
@else
    @section('title', 'AUTORIZACIÓN ARC-01 | SIVyC Icatech')
@endif
@section('content_script_css')
    <style>
        .content {font-family: sans-serif; font-size: 9px; margin-top:90px;}
        .tablas{border-collapse: collapse; width: 100%; margin-top:15px; }
        .tablas tr th {padding:1px;margin:0px;}
        .tablas th, .tablas td{font-size: 8px; border: gray 1px solid; text-align: center;font-weight:bold;}

        .tablaf { page-break-inside: avoid; border-collapse: collapse; width: 100%; white-space: nowrap; height: auto; margin-top:15px;}
        .tablaf tr td { font-size: 8px; text-align: center; padding: 0px 0px;}

        .tablad { page-break-inside: avoid; font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px 2px 2px 0; border-collapse: collapse; }
        .tablad tr td{padding: 1px 5px 0 5px; font-size: 7.5px;}

        #titulo{ position: fixed; top: 130px; width:100%; text-align: center; }
        #titulo h4{padding:0px; margin:0px 0px 2px 0px; font-size: 11px; font-weight:bold;}
        #titulo span{position: fixed; top: 120px; width:93%; display: block; text-align: right; font-weight: bold;}
        #para { font-size: 8px; font-weight:bold; position: fixed; top: 190px;}

        .agenda-list {list-style: none; padding: 0; margin: 0;}
        ul.inline-list { list-style: none; padding: 0; margin-top:150px;}
        ul.inline-list li { margin-right: 10px;}
        .obs {padding: 0; margin: 0; }
        body { padding-top: 12%; }

    </style>
@endsection
@section('content')
    @php
        if(strpos($reg_unidad->unidad.$reg_unidad->cct, "07EIC0"))
            $nombre_unidad = "UNIDAD DE CAPACITACIÓN ";
        else
            $nombre_unidad = "CENTRO DE TRABAJO ACCIÓN MÓVIL ";

        $valido= $reg_cursos[0]->valido;
        $munidad = $reg_cursos[0]->munidad;
        $nmunidad = $reg_cursos[0]->nmunidad;
        $fecha = $asunto = $det = $memo =  $obs = "";
        switch($opt){
            case 'ARC-01':
                $fecha = $fechaLayout = $reg_cursos[0]->fecha_apertura;
                $memo = $reg_cursos[0]->mvalida;
                $asunto = "AUTORIZACIÓN DE ASIGNACIÓN DE CLAVES DE APERTURAS";
                $det = "Por este medio envió a Usted el formato de autorización de asignación de claves de cursos de capacitación y/o certificación, en atención a la solicitud con número de memorándum $munidad.";

            break;
            case 'ARC-02':
                $fecha = $fechaLayout = $reg_cursos[0]->fecha_modificacion;
                $memo = $reg_cursos[0]->nmacademico;
                $asunto = "REPROGRAMACIÓN, MODIFICACIÓN O CANCELACIÓN DE APERTURAS";
                $det = "Por este medio envió a Usted el formato de autorización de reprogramación, modificación o cancelación de cursos de capacitación y/o certificación, en atención a la solicitud con número de memorándum $nmunidad.";
            break;
        }
        //CONVERSION DE FECHA
        $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
        $mes = $meses[date('m',strtotime($fecha))];
        $fecha = date('d',strtotime($fecha)).' de '.$mes.' del '.date('Y',strtotime($fecha));
    @endphp
    <div id="titulo">
        <h3 style="margin-left: -80px;">AUTORIZACIÓN DE {{$opt}}</h3>
        <br/>
        <table width="100%" style="margin-left: -65px;">
            <tr>
                <td style='text-align:right;font-size:10px;'><b>
                    DIRECCIÓN TÉCNICA ACADÉMICA<br/>
                    Memorándum No. {{ $memo}} <br/>
                    Tuxtla Gutiérrez, Chiapas; {{$fecha}}.</b><br/>
                </td>
            </tr>
        </table>
    </div>
    <div id="para">
        PARA: {{ $reg_unidad->dunidad }}.- {{$reg_unidad->pdunidad}} {{$reg_unidad->ubicacion}}<br/>
        DE: {{ $reg_unidad->dacademico }}.- {{$reg_unidad->pdacademico}}<br/>
        ASUNTO: {{ $asunto }}<br/><br/>
    </div>
    <div>
    <p style='text-align:justify; font-size: 10px;'>{{ $det }}</p>
     <table class="tablas">
         <thead>
             <tr>
                 <th>CURSO /<br/>CERTIFICACIÓN</th>
                 <th>UNIDAD DE CAPACITACIÓN</th>
                 <th>ESPECIALIDAD</th>
                 <th width="8%" >NOMBRE</th>
                 <th width="7%">CLAVE</th>
                 <th>MOD</th>
                 <th>TIPO DE<br>CAPACITA<BR/>CIÓN</th>
                 <th>D<br>U<br>R<br>A</th>
                 <th width="90px">FECHAS/HORARIOS<br/>(HORAS)</th>
                 <th>C<br>U<br>P<br>O</th>
                 <th width="8%" >INSTRUCTOR<br/>EXTERNO</th>
                 <th>CRITE<br>RIO<br>DE<br>PAGO</th>
                 <th>MUNICIPIO</th>
                 <th width="10%" >ESPACIO FÍSICO<br>MEDIO VIRTUAL</th>
                 <th>ZON<br>A<br>ECO<br>NOM<br>ICA</th>
                 <th>TIPO DE <BR/> CUOT<br/>A</th>
                 <th width='12%'>OBSERVACIONES</th>
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
                 <th>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION';} @endphp</th>
                 <th>{{$nom_unidad}} {{ $a->unidad }}</th>
                 <th>{{ $a->espe }}</th>
                 <th>{{ $a->curso }}</th>
                 <th>{{ $a->clave }}</th>
                 <th>{{ $a->mod }}</th>
                 <td>{{ $a->tcapacitacion}}</td>
                 <th>{{ $a->dura }}</th>
                 <td>{!! $a->agenda !!}</td>
                 <th>{{ $a->mujer + $a->hombre }}</th>
                 <tH>{{ $a->nombre }}</tH>
                 <th>{{ $a->cp }}</th>
                 <th>{{ $a->muni }}</th>
                 <th>{{ $a->efisico }}</th>
                 <th>{{ $a->ze }}</th><td>@if($a->tipo=="PINS"){{ "ORD" }} @elseif($a->tipo=="EPAR") {{ "RED" }} @else {{ $a->tipo }} @endif</td>
                 <td class="obs"> {!! $a->observaciones !!}</td>
             </tr>
             @endforeach
         </tbody>
     </table>
     <br/>
     <div style="page-break-inside: avoid; padding:0px; margin:0px;font-size: 8px;"><b>&nbsp;&nbsp;&nbsp;CRITERIO DE CONTRATACIÓN Y PAGO</b><br/>
        <table class="tablad">
            <tr>
                <td colspan="4"><br/><b>1. PRIMARIA INCONCLUSA &nbsp;2. PRIMARIA &nbsp;3. SECUNDARIA &nbsp;4. BACHILLERATO / PREPARATORIA O CARRERA TECNICA &nbsp;5. PROFESIONAL PASANTE </b></td>
            </tr>
            <tr>
                     <td colspan="4"><b>6. PROFESIONAL(TITULO Y/O CEDULA) &nbsp;7. MAESTRIA (PASANTE) &nbsp;8. MAESTRIA (TITULO Y/O CEDULA) 9. DOCTORADO (PASANTE) 10. DOCTORADO(TITULO Y/O CEDULA)</b></td>
            </tr>
            <tr>
                     <td colspan="4"><b>11. PROFESIONAL CERTIFICADO DE COMPETENCIA LABORAL EN CAPACITACIÓN ESPECIALIZADA Y TEMAS ESPECÍFICOS </b><br/>&nbsp;</td>
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
     ARCHIVO<BR/>
     </p>
    </div>
@endsection
@section('content_script_js')
<script type="text/php">
    if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 538, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
</script>
@endsection
