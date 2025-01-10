@extends('theme.formatos.hlayout2025')
@section('title', 'Solicitud ARC-01 | SIVyC Icatech')
@section('content_script_css')
    <style>
         /* body { margin-top: 140px;  margin-bottom: 36px;} */
        .tablas{border-collapse: collapse; width: 100%; margin-top:1px; }
        .tablas tr th {padding:1px;margin:0px;}
        .tablas th, .tablas td{font-size: 7px; border: gray 1px solid; text-align: center;font-weight:bold;}

        .tablaf { page-break-inside: avoid; border-collapse: collapse; width: 100%; white-space: nowrap; height: auto; margin-top:15px;}
        .tablaf tr td { font-size: 12px; text-align: center; padding: 0px 0px;}
        .tablad { page-break-inside: avoid; font-size: 8px;border: gray 1px solid; text-align: left; border-collapse: collapse; }
        .tablad tr td{padding: 1px 10px 0 10px;}

        #titulo{ position: fixed; top: 50px; width:100%; text-align: center;}
        #titulo h4{padding:0px; margin:0px 0px 2px 0px; font-size: 11px; font-weight:bold;}
        #titulo h3{padding:0px; margin:0px; font-size: 12px; font-weight:bold;}
        #titulo table{position: fixed; top: 100px;}
        #para {position: relative; top: -30px; height:auto; width:60%; font-size: 8px; font-weight:bold; margin-bottom:-40px;}
        .content {font-family: sans-serif; font-size: 9px; padding-top: 115px;}
    </style>

@endsection
@section('content')
    @php
        if(strpos($reg_unidad->unidad.$reg_unidad->cct, "07EIC0"))
            $nombre_unidad = "UNIDAD DE CAPACITACIÓN ";
        else
            $nombre_unidad = "CENTRO DE TRABAJO ACCIÓN MÓVIL ";
    @endphp
    <div id="titulo">
        <h4>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}</h4>
        <h4>DEPARTAMENTO ACADÉMICO</h4>
        <h4>ARC-01</h4>

        <table width="100%" table width="100%" style="margin-left: -65px;">
            <tr>
                <td style='text-align:right;font-size: 9px;'> <b>
                    {{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}<br/>
                    MEMORÁNDUM NO. {{ $memo_apertura }} <br/>
                    {{ $reg_unidad->municipio }}, Chiapas; {{$fecha_memo }}.</b><br/>
                </td>
            </tr>
        </table>
    </div>
    <div id="para">
        PARA: {{ $reg_unidad->dacademico }}, {{$reg_unidad->pdacademico}}<br/>
        DE: {{ $reg_unidad->dunidad }}, {{$reg_unidad->pdunidad}} {{$reg_unidad->ubicacion}}<br/>
        ASUNTO: SOLICITUD DE APERTURA<br/><br/>
        CC. ARCHIVO
    </div><br><br>

    <table class="tablas">
        <thead>
            <tr>
                <th rowspan="2">CURSO /<br/>CERTIFICACIÓN</th>
                <th rowspan="2">ESPECIALIDAD</th>
                <th rowspan="2">NOMBRE</th>
                <th rowspan="2">MOD</th>
                <th colspan="2">TIPO DE<br>CAPACITACIÓN</th>
                <th rowspan="2">D<br>U<br>R<br>A</th>
                <th rowspan="2">FECHA DE<br>INICIO</th>
                <th rowspan="2">FECHA DE<br>TERMINO</th>
                <th rowspan="2">HRS<br>DIA<br>RIAS</th>
                <th rowspan="2">HORARIO</th>
                <th rowspan="2">DIAS</th>
                <th rowspan="2">C<br>U<br>P<br>O</th>
                <th colspan="2">INSCRI TOS</th>
                <th rowspan="2">INSTRUCTOR <br/>EXTERNO</th>
                <th rowspan="2" >CRITE<br>RIO<br>DE<br>PAGO</th>
                <th rowspan="2">MUNICIPIO</th>
                <th rowspan="2">ZON<br>A<br>ECO<br>NOM<br>ICA</th>
                <th rowspan="2">DEPENDEN<br>CIA<br>BENEFICIA<br>DA</th>
                <th colspan="3">TIPO DE CUOTA</th>
                <th width="auto"  rowspan="2">ESPACIO FISICO<br>MEDIO VIRTUAL</th>
                <th width="auto" rowspan="2">OBSERVACIONES</th>
            </tr>
            <tr>
                <th >PRES<br>EN</th>
                <th >DISTA<br>NCIA</th>
                <th >FEM</th>
                <th >MAS</th>
                <th >ORD</th>
                <th >EXO</th>
                <th >RED</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reg_cursos as $a)
            <tr>
                <td>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION';} @endphp</td>
                <td>{{ $a->espe }}</td>
                <td>{{ $a->curso }}</td>
                <td>{{ $a->mod }}</td>
                <td>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</td>
                <td>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</td>
                <td>{{ $a->dura }}</td>
                <td>{{ $a->inicio }}</td>
                <td>{{ $a->termino }}</td>
                <td>{{ $a->horas }}</td>
                <td>{{ $a->horario }}</td>
                <td>{{ $a->dia }}</td>
                <td>{{ $a->mujer + $a->hombre }}</td>
                <td>{{ $a->mujer }}</td>
                <td>{{ $a->hombre }}</td>
                <td>{{ $a->nombre }}</td>
                <td>{{ $a->cp }}</td>
                <td>{{ $a->muni }}</td>
                <td>{{ $a->ze }}</td>
                <td>{{ $a->depen }}</td>
                <td>@if($a->tipo=="PINS"){{ "X" }}@endif</td>
                <td>@if($a->tipo=="EXO"){{ "X" }}@endif</td>
                <td>@if($a->tipo=="EPAR"){{ "X" }}@endif</td>
                <td >{{ $a->efisico }}</td>
                <td>{{ $a->nota }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br/>
    <div style="page-break-inside: avoid;"><b>&nbsp;&nbsp;&nbsp;CRITERIO DE CONTRATACION Y PAGO</b><br/>
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
        <table class="tablaf">
            <thead>
                <th>
                    <b>SOLICITA</b><br><br><br><br><br><br>
                    <b>{{ $reg_unidad->vinculacion }}</b><br>_____________________________________________________
                    <br>
                    <b>{{ $reg_unidad->pvinculacion }}</b>
                </th>
                <th>
                    <b>ELABORO</b><br><br><br><br><br><br>
                    <b>{{ $reg_unidad->academico }}</b><br>_____________________________________________________
                    <br>
                    <b>{{ $reg_unidad->pacademico }}</b>
                </th>
                <th>
                    <b>Vo. Bo.</b><br><br><br><br><br><br>
                    <b>{{ $reg_unidad->dunidad }}</b><br>_____________________________________________________
                    <br>
                    <b>{{ $reg_unidad->pdunidad }} {{$reg_unidad->ubicacion}}</b>
                </th>
                <th>
                    <br><br><br><b>SELLO UNIDAD DE<br>CAPACITACION</b><br>
                </th>
            </thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>

@endsection
@section('content_script_css')
<script type="text/php">
    if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 538, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
    }
</script>
@endsection

