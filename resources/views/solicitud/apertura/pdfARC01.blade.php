@extends('theme.formatos.hlayout'.$layout_año)
@section('title', 'Solicitud ARC-01 | SIVyC Icatech')
@section('content_script_css')
    <style>
        .content {font-family: sans-serif; font-size: 9px; margin-top:30px;}

        .tablas{border-collapse: collapse; width: 100%; margin-top:70px; }
        .tablas tr th {padding:1px;margin:0px;}
        .tablas th, .tablas td{font-size: 8px; border: gray 1px solid; text-align: center;font-weight:bold;}

        .tablaf { page-break-inside: avoid; border-collapse: collapse; width: 100%; white-space: nowrap; height: auto; margin-top:15px;}
        .tablaf tr td { font-size: 12px; text-align: center; padding: 0px 0px;}
        .tablad { page-break-inside: avoid; font-size: 8px;border: gray 1px solid; text-align: left; border-collapse: collapse; }
        .tablad tr td{padding: 1px 10px 0 10px;}

        #titulo{ position: fixed; left:0%; top: 160px; width:100%; text-align: center; }
        #titulo h4{padding:0px; margin:0px 0px 2px 0px; font-size: 11px; font-weight:bold;}
        #titulo span{position: fixed; top: 210px; width:93%; display: block; text-align: right; font-weight: bold;}
        #para { font-size: 8px; font-weight:bold; position: fixed; top: 200px;}

        .agenda-list {list-style: none; padding: 0; margin: 0;}
        ul.inline-list { list-style: none; padding: 0; margin-top:150px;}
        ul.inline-list li { margin-right: 10px;}
        .obs {padding: 0; margin: 0; }
        body { padding-top: 12%; }
    </style>
@php $arc = true; @endphp
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
        <span>
            {{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}<br/>
            MEMORÁNDUM NO. {{ $memo_apertura }} <br/>
            {{ $reg_unidad->municipio }}, Chiapas; {{$fecha_memo }}.</b><br/>
        </span>
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
                <th rowspan="2">FOLIO /<br/>NOMBRE</th>
                <th rowspan="2">MOD</th>
                <th rowspan="2">TIPO DE<br>CAPACI<BR/>TACIÓN</th>
                <th rowspan="2">D<br>U<br>R<br>A</th>
                <th rowspan="2">FECHAS/HORARIOS<br/>(HORAS)</th>
                <th rowspan="2">C<br>U<br>P<br>O</th>
                <th colspan="2">INSCRI TOS</th>
                <th rowspan="2">INSTRUCTOR <br/>EXTERNO</th>
                <th rowspan="2">CRITE<br>RIO<br>DE<br>PAGO</th>
                <th rowspan="2">MUNICIPIO</th>
                <th rowspan="2">ZON<br>A<br>ECO<br>NOM<br>ICA</th>
                <th rowspan="2">DEPENDEN<br>CIA<br>BENEFICIA<br>DA</th>
                <th rowspan="2">TIPO DE CUOTA</th>
                <th rowspan="2" width="auto">ESPACIO FISICO /<br>MEDIO VIRTUAL</th>
                <th rowspan="2" width="auto">OBSERVACIONES</th>
            </tr>
            <tr>
                <th >FEM</th>
                <th >MAS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reg_cursos as $a)
                @php
                    $agendaArray = json_decode($a->agenda, true);
                @endphp
            <tr>
                <td>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION';} @endphp</td>
                <td>{{ $a->espe }}</td>
                <td><div style="width: 60px">{{ $a->folio_grupo }} /<br/> {{ $a->curso }}</div></td>
                <td>{{ $a->mod }}</td>
                <td>{{ $a->tcapacitacion}}</td>
                <td>{{ $a->dura }}</td>
                <td> <div style="width: 90px">{!! $a->agenda !!}</div></td>
                {{--<td>{{ $a->dia }}</td>--}}
                <td>{{ $a->mujer + $a->hombre }}</td>
                <td>{{ $a->mujer }}</td>
                <td>{{ $a->hombre }}</td>
                <td>
                    @if($a->vb_dg==true or $a->clave!='0')
                        {{ $a->nombre }}
                    @endif
                </td>
                <td>
                    @if($a->vb_dg==true or $a->clave!='0')
                        {{ $a->cp }}
                    @endif
                </td>
                <td>{{ $a->muni }}</td>
                <td>{{ $a->ze }}</td>
                <td>{{ $a->depen }}</td>
                <td>@if($a->tipo=="PINS"){{ "ORD" }} @elseif($a->tipo=="EPAR") {{ "RED" }} @else {{ $a->tipo }} @endif</td>
                <td >{{ $a->efisico }}</td>
                <td class="obs"> {!! $a->observaciones !!}</td>
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
