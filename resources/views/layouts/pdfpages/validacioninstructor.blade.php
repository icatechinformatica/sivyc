@extends('theme.formatos.hlayout2025')
@section('title', 'VALIDACIÓN DE INSTRUCTOR | SIVyC Icatech')
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
        .tablag { border-collapse: collapse;border: 1px solid rgb(23, 23, 23); font-size: 7; text-align: center; padding:0.5px; width: 100%;}
        .tablag tr td{ border: 1px solid rgb(23, 23, 23)}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .content { margin-top: 12%;}
        </style>
    @endsection
    @section('content')
        <div style="margin-top: -120px;">
            @php $cont=0; foreach($especialidades AS $ari){if($ari->status != 'BAJA EN FIRMA'){$cont++;}} @endphp
                <div align=right> <b>Dirección Técnica Académica</b></div>
                <div align=right> <b>Memorandum No. @if($especialidades[0]->status != 'BAJA EN FIRMA') {{$especialidades[0]->memorandum_validacion}} @else {{$especialidades[0]->memorandum_baja}} @endif</b></div>
                <div align=right> <b>Tuxtla Gutiérrez, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>
                <b>{{ $funcionarios['dunidad']['titulo'] }} {{ $funcionarios['dunidad']['nombre'] }}.</b>
                <br>{{ $funcionarios['dunidad']['puesto'] }}.
                <br>Presente.
                <br><br style="line-height: 1;"><p style="line-height: 1;">Con relación a la solicitud de @if($especialidades[0]->status == 'EN FIRMA') validación @elseif($especialidades[0]->status == 'REACTIVACION EN FIRMA') reactivación @else actualización @endif del instructor, realizada mediante memorándum núm. {{$especialidades[0]->memorandum_solicitud}}, me permito indicarle que el siguiente docente ha quedado @if($especialidades[0]->status == 'EN FIRMA') validado @elseif($especialidades[0]->status == 'REACTIVACION EN FIRMA') reactivado @else actualizado @endif en @if($cont == 1) la especialidad @else las especialidades @endif que se indica.</p>
                <div class="table table-responsive" style="margin-bottom: 0px; margin-top: -8px;">
                    <table class="tablag" style="">
                        <tbody>
                            <tr>
                                <td style='width: 180px;'><b>NOMBRE DEL INSTRUCTOR:</b></td>
                                <td style='width: 360px;' colspan="2">{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'><b>NUMERO DE CONTROL:</b></td>
                                <td style='width: 360px;' colspan="2">{{$instructor->numero_control}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'><b>REGIMEN FISCAL:</b></td>
                                <td style='width: 360px;' colspan="2">{{$instructor->tipo_honorario}}</td>
                            </tr>
                            <tr>
                                <td><b>CLAVE DE LA ESPECIALIDAD:</b></td>
                                <td><b>ESPECIALIDAD:</b></td>
                                <td><b>NIVEL ACADÉMICO:</b></td>
                            </tr>
                                @foreach ($especialidades AS $wort => $cadwell)
                                    @if ($cadwell->status != 'BAJA EN FIRMA')
                                    <tr>
                                        <td style='width: 180px;'>{{$cadwell->clave}}</td>
                                        <td style='width: 360px;'>{{$cadwell->nombre}}</td>
                                        <td>{{mb_strtoupper($cadwell->perfil_profesional, 'UTF-8')}}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            {{-- </tr> --}}
                            {{-- <tr>
                                <td style='width: 180px;'>NIVEL DE ESTUDIOS QUE CUBRE PARA LA ESPE:</td>
                                <td style='width: 360px;' colspan="2"> @if($especialidades[0]->status != 'BAJA EN FIRMA'){{$especialidades[0]->perfil_profesional}} @else {{$especialidades[1]->perfil_profesional}}@endif</td>
                            </tr> --}}
                            <tr>
                                <td style='width: 180px;'><b>OBSERVACIONES:</b></td>
                                <td style='width: 360px;' colspan="2"> @if($especialidades[0]->status != 'BAJA EN FIRMA'){{$especialidades[0]->observacion_validacion}} @else {{$especialidades[1]->observacion_validacion}}@endif</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="line-height: 1;">Es preciso señalar que, en su expediente consta que cumple con los requisitos y documentos que requiere el perfil de la especialidad solicitada.
                <br style="line-height: 0;">
                Agradeciendo de antemano su atención, envío un cordial saludo.</p>
                <div>
                    <div class="column">
                        <table class="tablad">
                            <thead>
                                <tr>
                                    <td>
                                        <b>ELABORÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{ $funcionarios['elabora']['nombre'] }}</small></small></small>
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
                        <table style="border-collapse:initial;font-size: 12px; border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td width="173px"  >
                                        <b>SELLO</b><br><br>
                                        <br><b></b>
                                        <br>&nbsp;<br>
                                        <b>&nbsp;</b>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <div style="font-size: 10px;">
                    “Este documento es de uso interno y no tiene validez jurídica ni contractual, se extiende únicamente con fines académicos. Estos datos y resultados son considerados confidenciales por lo que se prohíbe su reproducción parcial o total para fines distintos al uso interno de la Dirección Técnica Académica y las Unidades de Capacitación”.
                    <br>
                    <small><small>C.c.p. {{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }} .- {{ $funcionarios['gestionacademica']['puesto'] }}. – Para su conocimiento - Edificio.</small></small>
                    <br><small><small>Archivo</small></small>
                </div>
        </div>
        @endsection
        @section('script_content_js')
        @endsection
