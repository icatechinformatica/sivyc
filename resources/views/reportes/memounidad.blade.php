@extends('theme.formatos.vlayout2025')
@section('title', 'Formato T | SIVyC Icatech')
@section('content_script_css')
    <style>
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        .contenedor {
        position:RELATIVE;
        top:10px;
        width:100%;
        margin:auto;

        /* Propiedad que ha sido agreda*/

        }
    </style>
@endsection
@section('content')
    <div class= "contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCION TECNICA ACADEMICA</b></div>
        <div align=right style="font-size:11px;"><b>MEMORANDUM NO. {{ $nume_memo }}</b></div>
        <div align=right style="font-size:11px;"><b>TUXTLA GUTIERREZ, CHIAPAS; {{ $fecha_nueva }}</b></div>
        <br>
        <div align=left style="font-size:12px;"><b>{{ $funcionarios['dunidad']['titulo'] }} {{ $funcionarios['dunidad']['nombre'] }}, {{ $funcionarios['dunidad']['puesto'] }}</b></div>
        <div align=left style="font-size:11px;"><b>PRESENTE.</b></div>
        <br>
            @php
                $num=1;
            @endphp
            <div class="table-responsive-sm">
                <div align="justify" style="font-size:11px;">
                    En seguimiento a la integración del Formato T del día {{substr($fecha_envio,8,2)}} con mes de {{@strtolower($mesReportado2)}} del presente
                    año de su Unidad de Capacitación, le informo que fueron recibidos los formatos RIACD-02 INSCRIPCION,
                    RIACD-02 ACREDITACION, LAD-04 LISTA DE ASISTENCIA, RESD-05 CALIFICACIONES
                    digitalizados con firmas y sellos de {{ $sum_total }} cursos pertenecientes a la Unidad {{ $reg_unidad->unidad }}. De lo anterior,
                    hago de su conocimiento que, una vez revisada la informacion en comento, se reportaron a la Dirección
                    de Planeación de este Instituto {{ $totalReportados }} cursos y {{ $total }} no se reportaron de acuerdo a las siguientes observaciones:
                </div>
                <br>
                <table class="tablas">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>MES</th>
                            <th>UNIDAD/ACCION MOVIL</th>
                            <th>ESPECIALIDAD</th>
                            <th>CURSO</th>
                            <th>CLAVE</th>
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reg_cursos as $index => $a)
                            <tr>
                                <th>{{ $num }}</th>
                                <th>{{ $a->mes }}</th>
                                <th>{{ $a->unidad }}</th>
                                <th>{{ $a->espe }}</th>
                                <th>{{ $a->curso }}</th>
                                <th>{{ $a->clave }}</th>
                                <th>{{ json_decode($a->comentario_enlaces_retorno, JSON_UNESCAPED_SLASHES) }}</th>
                            </tr>
                                @php
                                    $num=$num+1;
                                @endphp
                        @endforeach
                    </tbody>
                </table>
                <br>
                <div align="justify" style="font-size:11px;">
                    {{-- No omito manifestar sobre la información antes mencionada, que se encuentran pendientes los documentos
                    originales de las entregas realizadas por su Unidad de Capacitación para el reporte estadístico una vez
                    reintegrados al trabajo presencial. --}}
                </div>
                <br>
                <div style="font-size:11px;">Sin más por el momento, agradezco su atención y le envío un cordial saludo.</div>
                <br>
                <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>
                <br><br><br><br>
                <div style="font-size:11px;"> <b>{{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }}</b> </div>
                <div style="font-size:11px;"> <b>{{ $funcionarios['dacademico']['puesto'] }}</b> </div>
                <br>
                {{-- <div style="font-size:9px;"> <b>C.C.P LIC. YESENIA FUSIKO. KOMUKAI HORITA, JEFA DEL DEPARTAMENTO ACADÉMICO. PARA SU CONOCIMIENTO, CIUDAD.</b> </div> --}}
                <div style="font-size:9px;"> <b>C.c.p. {{ $funcionarios['dacademico_unidad']['titulo'] }} {{ $funcionarios['dacademico_unidad']['nombre'] }}. {{ $funcionarios['dacademico_unidad']['puesto'] }}.</b> </div>
                <div style="font-size:9px;"> <b>ARCHIVO</b> </div>
                <div style="font-size:7px;"> <b>VALIDÓ: {{ $funcionarios['certificacion']['titulo'] }} {{ $funcionarios['certificacion']['nombre'] }}. {{ $funcionarios['certificacion']['puesto'] }}</b> </div>
                <div style="font-size:7px;"> <b>ELABORÓ: {{ $funcionarios['elabora']['nombre'] }}. {{ $funcionarios['elabora']['puesto'] }}</b> </div>
            </div><br>
    </div>
@endsection
@section('script_content_js')
@endsection


