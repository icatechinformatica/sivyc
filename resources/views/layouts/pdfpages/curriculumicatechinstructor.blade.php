
@extends('theme.formatos.vlayout2025')
@section('title', 'CURRICULUM ICATECH| SIVyC Icatech')
@section('content_script_css')
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
        /* *{border: 1px solid red;} */
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

            /* div.content
            {
                margin-bottom: 750%;
                margin-right: -25%;
                margin-left: 0%;
            } */
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

            img.derechaf { float: right; width: 2.5cm; height: 3.0cm;}

        .tablad { border-collapse: collapse;font-size: 10px; width: 100%; border-color: black; border: black 1px solid; border-bottom-color: black; border-top-color: black; text-align: center; padding:0.5px;}
        .tablaz { border-collapse: collapse;font-size: 10px;border: black 1px solid; text-align: center; padding:0.5px; margin-right: 0px; margin-left: auto;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        header {left: 25px;}
        </style>
 @endsection
 @section('content')
        <div>
            @if($data->archivo_fotografia != FALSE)
                {{-- <img class="derechaf img-thumbnail mb-3" src="{{ asset($data->archivo_fotografia) }}"> --}}
                <img style="border: 2px solid black; margin-top: 0px; margin-right: 30px;" class="pull-right"  src="{{ asset($data->archivo_fotografia) }}" alt="foto" width="75" height="75">
            @endif
            <br><br><br><br>
            <table class="tablaz" style="border-color: black">
                <thead>
                    <tr>
                        <td>
                            <b>FECHA DE SOLICITUD:</b>
                        </td>
                        <td>
                            {{$D}} de {{$M}} del {{$Y}}
                        </td>
                    </tr>
                </thead>
            </table>
            <p style="margin-top: -12px;"><b><small>I. DATOS PERSONALES</small></b></p>
            <br>
            <div class="table table-responsive" style="margin-top: -30px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>APELIDO PATERNO</b></td>
                            <td width="10%;"><b>APELLIDO MATERNO</b></td>
                            <td width="10%;"><b>NOMBRE(S)</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$data->apellidoPaterno}}</td>
                            <td>{{$data->apellidoMaterno}}</td>
                            <td>{{$data->nombre}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table table-responsive" style="margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>CURP</b></td>
                            <td width="10%;"><b>RFC CON HOMOCLAVE</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$data->curp}}</td>
                            <td>{{$data->rfc}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table table-responsive" style="margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>SEXO</b></td>
                            <td width="10%;"><b>ESTADO CIVIL</b></td>
                            <td width="10%;"><b>FECHA DE NACIMIENTO</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$data->sexo}}</td>
                            <td>{{$data->estado_civil}}</td>
                            <td>{{$data->fecha_nacimiento}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table table-responsive" style="margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>LUGAR DE NACIMIENTO</b></td>
                            <td width="10%;"><b>NACIONALIDAD</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$data->municipio_nacimiento}}, {{$data->entidad_municipio_nacimiento}}</td>
                            <td>MEXICANO</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table table-responsive" style="margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>LUGAR DE RESIDENCIA</b></td>
                            <td width="10%;"><b>CODIGO POSTAL</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$data->localidad}}. {{$data->municipio}}, {{$data->entidad}}</td>
                            <td>{{$data->codigo_postal}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table table-responsive" style="margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td colspan="2" width="10%;"><b>NUMERO DE TELEFONO</b></td>
                            <td width="10%;"><b>CORREO ELECTRONICO PERSONAL</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CASA</td>
                            <td>{{$data->telefono_casa}}</td>
                            <td rowspan="2">{{$data->correo}}</td>
                        </tr>
                        <tr>
                            <td>MOVIL</td>
                            <td>{{$data->telefono}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p style="margin-top: -12px;"><b><small>II. FORMACIÓN ACADÉMICA Y EN CURSO</small></b></p>
            <br>
            <div class="table table-responsive" style="margin-top: -30px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><small><small><b>NIVEL EDUCATIVO</b></small></small></td>
                            <td width="10%;"><small><small><b>INSTITUCIÓN</b></small></small></td>
                            <td width="10%;"><small><small><b>ÁREA DE LA CARRERA</b></small></small></td>
                            <td width="10%;"><small><small><b>CARRERA</b></small></small></td>
                            <td width="10%;"><small><small><b>PERIODO</b></small></small></td>
                            <td width="10%;"><small><small><b>REALIZADO EN</b></small></small></td>
                            <td width="10%;"><small><small><b>DOCUMENTO</b></small></small></td>
                            <td width="10%;"><small><small><b>FECHA DE EXPEDICIÓN</b></small></small></td>
                            <td width="10%;"><small><small><b>FOLIO DOCUMENTO</b></small></small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($perfiles))
                            @foreach($perfiles as $cadwell)
                                <tr>
                                    <td><small><small>{{$cadwell->grado_profesional}}</small></small></td>
                                    <td><small><small>{{$cadwell->nombre_institucion}}</small></small></td>
                                    <td><small><small>{{$cadwell->area_carrera}}</small></small></td>
                                    <td><small><small>{{$cadwell->carrera}}</small></small></td>
                                    <td><small><small>{{$cadwell->periodo}}</small></small></td>
                                    <td><small><small>{{$cadwell->entidad_institucion}}</small></small></td>
                                    <td><small><small>{{$cadwell->estatus}}</small></small></td>
                                    <td><small><small>{{$cadwell->fecha_expedicion_documento}}</small></small></td>
                                    <td><small><small>{{$cadwell->folio_documento}}</small></small></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <p style="margin-top: -12px;"><b><small>III. EXPERIENCIA DOCENTE (ANTERIOR Y ACTUAL)</small></b></p>
            <div class="table table-responsive" style="margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>ASIGNATURA</b></td>
                            <td width="10%;"><b>INSTITUCIÓN</b></td>
                            <td width="10%;"><b>FUNCIÓN</b></td>
                            <td width="10%;"><b>PERIODO</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data->exp_docente))
                            @foreach($data->exp_docente AS $cadwell)
                                <tr>
                                    <td>{{$cadwell['asignatura']}}</td>
                                    <td>{{$cadwell['institucion']}}</td>
                                    <td>{{$cadwell['funcion']}}</td>
                                    <td>{{$cadwell['periodo']}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <p style="margin-top: -12px;"><b><small>IV. EXPERIENCIA LABORAL (ANTERIOR Y ACTUAL)</small></b></p>
            <div class="table table-responsive" style="margin-bottom: 2px; margin-top: -12px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="10%;"><b>PUESTO</b></td>
                            <td width="10%;"><b>PERIODO</b></td>
                            <td width="10%;"><b>INSTITUCIÓN</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data->exp_laboral))
                            @foreach($data->exp_laboral AS $cadwell)
                                <tr>
                                    <td style="font-size: 9px;">{{$cadwell['puesto']}}</td>
                                    <td style="font-size: 9px;">{{$cadwell['periodo']}}</td>
                                    <td style="font-size: 9px;">{{$cadwell['institucion']}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div style="d-block text-align: center;">
                <div style="text-align: center; padding-left: 0%; padding-right: 0%; margin-bottom:30px"><b><small>DECLARO BAJO PROTESTA DE DECIR VERDAD QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS</small></b></div>
                <div style="text-align: center; padding-left: 0%; padding-right: 0%"><b><small>{{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</small></b></div>
                <div style="text-align: center; padding-left: 0%; padding-right: 0%"><b><small><small>
                Se informa que no se realizarán tranferencias de datos personales,
                 salvo aquéllas que sean necesarias para atender requerimientos de información de una autoridad
                 competente, que estén debidamente fundados y motivados. En ese caso se atenderá a lo dispuesto
                 en el Art. 18 de la Ley de Protección de Datos Personales en Posesión de Sujetos Obligados
                 del Estado de Chiapas.
            </small></small></b></div>
            </div>

        </div>
        @endsection
        @section('script_content_js')
        @endsection
