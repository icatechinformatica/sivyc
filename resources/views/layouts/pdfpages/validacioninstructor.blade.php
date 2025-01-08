@extends('theme.formatos.hlayout2025')
@section('title', 'SOLICITUD DE VALIDACIÓN DE INSTRUCTOR | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
            .row {
            margin-left:-10px;
            margin-right:30px;
            }

            .column {
            float: left;
            width: 25%;
            padding: 5px;
            }

            /* Clearfix (clear floats) */
            .row::after {
            content: "";
            clear: both;
            display: table;
            }
            .ftr{
                position: fixed;
                top: 85%;
                bottom: 0;
                left: 0;
                height: 60px;
            }
            th, td {
            border-style:solid;
            border-color: black;
            }
            div.content
            {
                margin-bottom: 750%;
                margin-right: -25%;
                margin-left: 0%;
            }
            .floatleft {
                float:left;
            }
            .page-break {
                page-break-after: always;
            }
            .page-break-non {
                page-break-after: avoid;
            }
            .table1, .table1 td {
                border:0px ;
            }
            .table1 td {
                padding:5px;
            }
            .tablas{border-collapse: collapse;width: 990px;}
        .tablas tr{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablas th{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablaf { border-collapse: collapse; width: 100%;border: gray 1px solid; }
        .tablaf tr td { font-size: 7px; text-align: center; padding: 0px;}
        .tablad { border-collapse: collapse;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px; width: 100%;}
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}
        .tablag tr td{ font-size: 8px; padding: 1px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        </style>
    @endsection
    @section('content')
        <div>
            @php $cont=0; foreach($especialidades AS $ari){if($ari->status != 'BAJA EN FIRMA'){$cont++;}} @endphp
                <div align=right> <b>Dirección Técnica Académica</b></div>
                <div align=right> <b>Memorandum No. @if($especialidades[0]->status != 'BAJA EN FIRMA') {{$especialidades[0]->memorandum_validacion}} @else {{$especialidades[0]->memorandum_baja}} @endif</b></div>
                <div align=right> <b>Tuxtla Gutiérrez, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>
                <b>{{ $funcionarios['dunidad']['titulo'] }} {{ $funcionarios['dunidad']['nombre'] }}.</b>
                <br>{{ $funcionarios['dunidad']['puesto'] }}.
                <br>Presente.
                <br><br>Con relación a la solicitud de @if($especialidades[0]->status == 'EN FIRMA') validación @elseif($especialidades[0]->status == 'REACTIVACION EN FIRMA') reactivación @else actualización @endif del instructor, realizada mediante memorándum núm. {{$especialidades[0]->memorandum_solicitud}}, me permito indicarle que el siguiente docente ha quedado @if($especialidades[0]->status == 'EN FIRMA') validado @elseif($especialidades[0]->status == 'REACTIVACION EN FIRMA') reactivado @else actualizado @endif en @if($cont == 1) la especialidad @else las especialidades @endif que se indica.
                <div class="table table-responsive">
                    <table class="tablad" style="border-color: black; font-size: 7;">
                        <tbody>
                            <tr>
                                <td style='width: 180px;'>Nombre del Instructor:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Numero de Control:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->numero_control}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;' rowspan="{{$cont}}">Especialidad y Clave de la Especialidad:</td>
                                @foreach ($especialidades AS $wort => $cadwell)
                                    @if ($cadwell->status != 'BAJA EN FIRMA')
                                        <td style='width: 360px;'>{{$cadwell->nombre}}</td>
                                        <td style='width: 180px;'>{{$cadwell->clave}}</td>
                                    </tr>
                                    <tr>
                                    @endif
                                @endforeach
                                <td style='width: 180px;'>Instructor:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->tipo_honorario}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Nivel de Estudios que Cubre para la Especialidad:</td>
                                <td style='width: 360px;' colspan="2"> @if($especialidades[0]->status != 'BAJA EN FIRMA'){{$especialidades[0]->perfil_profesional}} @else {{$especialidades[1]->perfil_profesional}}@endif</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Observaciones:</td>
                                <td style='width: 360px;' colspan="2"> @if($especialidades[0]->status != 'BAJA EN FIRMA'){{$especialidades[0]->observacion_validacion}} @else {{$especialidades[1]->observacion_validacion}}@endif</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                Es preciso señalar que, en su expediente consta que cumple con los requisitos y documentos que requiere el perfil de la especialidad solicitada.
                <br>
                Agradeciendo de antemano su atención, envío un cordial saludo.
                <div>
                    <div class="column">
                        <table class="tablad">
                            <thead>
                                <tr>
                                    <td>
                                        <b>ELABORÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>C. {{ $funcionarios['elabora']['nombre'] }}</small></small></small>
                                        <br><small><small><small>{{ $funcionarios['elabora']['puesto'] }}</small></small></small>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="column">
                        <table style="border-collapse:initial;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td>
                                        <b>REVISÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }}</small></small></small>
                                        <br><small><small><small>{{ $funcionarios['gestionacademica']['puesto'] }}</small></small></small>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="column">
                        <table style="border-collapse:initial;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td>
                                        <b>AUTORIZÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }}</small></small></small>
                                        <br><small><small><small>{{ $funcionarios['dacademico']['puesto'] }}</small></small></small>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="column">
                        <table style="border-collapse:initial;font-size: 12px; color:white; border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td>
                                        <b>REVISÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br>alguien1<br>
                                        <b>111</b>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <div>
                    “Este documento es de uso interno y no tiene validez jurídica ni contractual, se extiende únicamente con fines académicos. Estos datos y resultados son considerados confidenciales por lo que se prohíbe su reproducción parcial o total para fines distintos al uso interno de la Dirección Técnica Académica y las Unidades de Capacitación”.
                    <br><br>
                    <small><small>C.c.p. {{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }} .- {{ $funcionarios['gestionacademica']['puesto'] }}. – Para su conocimiento - Edificio.</small></small>
                    <br><small><small>Archivo</small></small>
                </div>
        </div>
        @endsection
        @section('script_content_js')
        @endsection
