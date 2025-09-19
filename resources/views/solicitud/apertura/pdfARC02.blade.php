@extends('theme.formatos.hlayout')
@section('title', 'Solicitud ARC-02 | SIVyC Icatech')
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

        #titulo{ position: fixed; top: 80px; width:100%; text-align: center; }
        #titulo h4{padding:0px; margin:0px 0px 2px 0px; font-size: 11px; font-weight:bold;}        
        #titulo span{position: fixed; top: 120px; width:93%; display: block; text-align: right; font-weight: bold;}
        #para { font-size: 8px; font-weight:bold; position: fixed; top: 140px;}        
        
        .agenda-list {list-style: none; padding: 0; margin: 0;}
        ul.inline-list { list-style: none; padding: 0; margin-top:150px;}
        ul.inline-list li { margin-right: 10px;}
        .obs {padding: 0; margin: 0; }
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
        <h4>ARC-02</h4>

        <table width="100%" table width="100%" style="margin-left: -65px;">
            <tr>
                <td style='text-align:right;font-size: 9px;'>
                    {{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}<br/>
                    MEMORÁNDUM NO. {{ $memo_apertura }} <br/>
                    {{ $reg_unidad->municipio }}, Chiapas; {{$fecha_memo }}.<br/>
                </td>
            </tr>
        </table>
    </div>
    <div id="para">
        PARA: {{ $reg_unidad->dacademico }}, {{$reg_unidad->pdacademico}}<br/>
        DE: {{ $reg_unidad->dunidad }}, {{$reg_unidad->pdunidad}} {{$reg_unidad->ubicacion}}<br/>
        ASUNTO: SOLICITUD DE REPROGRAMACION, CANCELACION O CORRECCIÓN<br/><br/>
        CC. ARCHIVO
    </div>
    <br><br>
    <table class="tablas">
            <tbody>
                <tr>
                    <th>CURSO/CERTIFICACIÓN</th>
                    <th>NOMBRE</th>
                    <th>MOD</th>
                    <th>TIPO DE<br>CAPACI<BR/>TACIÓN</th>
                    <th>HORAS</th>                    
                    <th>CLAVE</th>
                    <th>NUM. DE <br> MEMORANDUM DE <br> AUT. DE CLAVE</th>
                    <th>INSTRUCTOR<br/>EXTERNO</th>
                    <th>FECHAS/HORARIOS<br/>(HORAS)</th>
                    <th>ESPACIO FISICO</th>
                    <th>MOTIVO</th>
                    <th>SOLICITA</th>
                    <th>OBSERVACIONES</th>
                </tr>                               
                @foreach($reg_cursos as $a)
                    <tr>
                        <td>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION';} @endphp</td>
                        <td style="width: 8%;">{{ $a->curso }}</td>
                        <td>{{ $a->mod }}</td>
                        <td>{{ $a->tcapacitacion}}</td>
                        <td>{{ $a->dura }}</td>
                        <td style="width:8%;">{{ $a->clave }}</td>
                        <td>@if ($a->mvalida) {{ substr($a->mvalida ,0,12)}} {{ substr($a->mvalida ,12,strlen($a->mvalida ))}} @endif</td>
                        <td style="width: 10%;">{{ $a->nombre }}</td>
                        <td> <div style="width: 50px">{!! $a->agenda !!}</div></td>
                        <td style="width: 15%;">{{ $a->efisico }}</td>
                        <td>@isset($a->motivo){{ $a->motivo }}@else {{$a->opcion}}@endisset</td>
                        <td style="width: 8%;">{{ $a->realizo }}</td>
                        <td style="width: 15%;">{{$a->observaciones}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br><br><br><br><br>
        <div>
            <table class="tablaf">
                <tr>
                    <td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><b>ELABORO</b><br><br><br><br></td>
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><b>Vo. Bo.</b><br><br><br><br></td>
                </tr>
                <tr>
                    <td> </td><td> </td><td> </td><td> </td>
                    <td align="center">_____________________________________________________<br><b>{{ $reg_unidad->academico }}</b></td>
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center">_____________________________________________________<br><b>{{ $reg_unidad->dunidad }}</b></td>
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><br><b>SELLO UNIDAD DE<br>CAPACITACION</b></td>
                </tr>
                <tr>
                    <td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><b>{{ $reg_unidad->pacademico }}</b></td>
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><b>{{ $reg_unidad->pdunidad }} {{$reg_unidad->ubicacion}}</b></td>
                </tr>
            </table>
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


