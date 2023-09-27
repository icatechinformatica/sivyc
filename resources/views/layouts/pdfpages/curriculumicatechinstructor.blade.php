
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
        /* *{border: 1px solid red;} */
            body{
                font-family: sans-serif;
                /* border: 1px solid black; */
                font-size: 1.3em;
                /* margin: 10px; */
            }
            @page {
                margin: 20px 30px 20px;

            }
            .ftr{
                position: fixed;
                top: 85%;
                bottom: 0;
                left: 0;
                height: 60px;
            }
            header {
            position: fixed;
            left: 0px;
            top: 0px;
            right: 0px;
            color: black;
            text-align: center;
            line-height: 60px;
            height: 60px;
            }
            header h1{
            margin: 10px 0;
            }
            header h2{
            margin: 0 0 10px 0;
            }
            th, td {
            border-style:solid;
            border-color: black;
            }
            footer {
            position: fixed;
            /* left: 0px; */
            bottom: 20px;
            /* right: 0px; */
            /* height: 60px; */
            /* text-align: center; */
            /* line-height: 60px; */
            border: 1px solid white;
            }
            img.izquierda {
                float: left;
                width: 300px;
                height: 60px;
            }

            img.izquierdabot {
                float: inline-end;
                width: 330px;
                height: 60px;
            }

            img.derecha {
                float: right;
                width: 200px;
                height: 60px;
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

            img.derechaf { float: right; width: 2.5cm; height: 3.0cm;}

            .tablas{border-collapse: collapse;width: 990px;}
        .tablas tr{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablas th{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablaf { border-collapse: collapse; width: 100%;border: gray 1px solid; }
        .tablaf tr td { font-size: 7px; text-align: center; padding: 0px;}
        .tablad { border-collapse: collapse;font-size: 10px; width: 100%; border-color: black; border: black 1px solid; border-bottom-color: black; border-top-color: black; text-align: center; padding:0.5px;}
        .tablaz { border-collapse: collapse;font-size: 10px;border: black 1px solid; text-align: center; padding:0.5px; margin-right: 0px; margin-left: auto;}
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}
        .tablag tr td{ font-size: 8px; padding: 1px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .direccion {
            top: 0.20cm;
            text-align: left;
            position: absolute;
            bottom: 60px;
            left: 6px;
            font-size: 11px;
            color:#FFF;
            font-weight: bold;
            line-height: 1;
            width: 320px;
            margin-top: 5px;
            margin-left: 3px;
            }
        </style>
    </head>
    <body style="margin-top:90px; margin-bottom:70px; border: 1px">
        <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
            <div style="clear:both;">
                <h6>{{$distintivo}}</h6>
            </div>
        </header>
        <footer>
            <div style="position: relative; top:-40px;">
                <img class="izquierdabot" src="{{ public_path('img/franja2.png') }}">
                <div align="justify" class="direccion">
                    14 Poniente Norte No.239 Colonia Moctezuma <br>
                    Tuxtla Gutiérrez, CP 29030 Telefono: +52 (961) 61-2-16-21 <br>
                    email: icatech@icatech.chiapas.gob.mx
                </div>
                <img class="derecha" src="{{ public_path('img/icatech-imagen.png') }}">
            </div>
            <div class="page-break-non"></div>
        </footer>
        <div>
            @if($data->archivo_fotografia != FALSE)
                {{-- <img class="derechaf img-thumbnail mb-3" src="{{ asset($data->archivo_fotografia) }}"> --}}
                <img style="border: 2px solid black; margin-top: -5px; margin-right: -160px;" class="pull-right"  src="{{ asset($data->archivo_fotografia) }}" alt="foto" width="75" height="75">
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
            <b><small>I. DATOS PERSONALES</small></b>
            <br>
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="240px"><b>APELIDO PATERNO</b></td>
                            <td width="240px"><b>APELLIDO MATERNO</b></td>
                            <td width="240px"><b>NOMBRE(S)</b></td>
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
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="360px"><b>CURP</b></td>
                            <td width="360px"><b>RFC CON HOMOCLAVE</b></td>
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
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="240px"><b>SEXO</b></td>
                            <td width="240px"><b>ESTADO CIVIL</b></td>
                            <td width="240px"><b>FECHA DE NACIMIENTO</b></td>
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
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="360px"><b>LUGAR DE NACIMIENTO</b></td>
                            <td width="360px"><b>NACIONALIDAD</b></td>
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
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="360px"><b>LUGAR DE RESIDENCIA</b></td>
                            <td width="360px"><b>CODIGO POSTAL</b></td>
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
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td colspan="2" width="360px"><b>NUMERO DE TELEFONO</b></td>
                            <td width="360px"><b>CORREO ELECTRONICO PERSONAL</b></td>
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
            <b><small>II. FORMACIÓN ACADÉMICA Y EN CURSO</small></b>
            <br>
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="80px"><small><small><b>NIVEL EDUCATIVO</b></small></small></td>
                            <td width="80px"><small><small><b>INSTITUCIÓN</b></small></small></td>
                            <td width="80px"><small><small><b>ÁREA DE LA CARRERA</b></small></small></td>
                            <td width="80px"><small><small><b>CARRERA</b></small></small></td>
                            <td width="80px"><small><small><b>PERIODO</b></small></small></td>
                            <td width="80px"><small><small><b>REALIZADO EN</b></small></small></td>
                            <td width="80px"><small><small><b>DOCUMENTO</b></small></small></td>
                            <td width="80px"><small><small><b>FECHA DE EXPEDICIÓN</b></small></small></td>
                            <td width="80px"><small><small><b>FOLIO DOCUMENTO</b></small></small></td>
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
            <b><small>III. EXPERIENCIA DOCENTE (ANTERIOR Y ACTUAL)</small></b>
            <br>
            <div class="table table-responsive">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="180px"><b>ASIGNATURA</b></td>
                            <td width="180px"><b>INSTITUCIÓN</b></td>
                            <td width="180px"><b>FUNCIÓN</b></td>
                            <td width="180px"><b>PERIODO</b></td>
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
            <b><small>IV. EXPERIENCIA LABORAL (ANTERIOR Y ACTUAL)</small></b>
            <br>
            <div class="table table-responsive" style="margin-bottom: 2px;">
                <table class="tablad">
                    <thead>
                        <tr>
                            <td width="220px"><b>PUESTO</b></td>
                            <td width="160px"><b>PERIODO</b></td>
                            <td width="280px"><b>INSTITUCIÓN</b></td>
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
    </body>
</html>
