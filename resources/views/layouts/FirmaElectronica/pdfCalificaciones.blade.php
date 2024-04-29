<html>

<head>
    <title>LISTA DE CALIFICACIONES</title>
    <style>
        body {
            font-family: sans-serif;
        }

        @page {
            margin: 200px 25px 0px 25px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -185px;
            right: 0px;
            text-align: center;
        }

        header h6 {
            height: 0;
            line-height: 14px;
            padding: 8px;
            margin: 0;
        }

        header #curso {
            margin-top: 20px;
            font-size: 8px;
            border: 1px solid gray;
            padding: 12px;
            line-height: 18px;
            text-align: justify;
        }

        main {
            padding: 0;
            margin: 0;
            margin-top: 0px;
        }

        .tabla {
            border-collapse: collapse;
            width: 100%;
        }

        .tabla tr td,
        .tabla tr th {
            font-size: 8px;
            border: gray 1px solid;
            text-align: center;
            padding: 3px;
        }

        .tab {
            margin-left: 10px;
            margin-right: 50px;
        }

        .tab1 {
            margin-left: 15px;
            margin-right: 50px;
        }

        .tab2 {
            margin-left: 5px;
            margin-right: 20px;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: -100px;
            height: 150px;
            width: 100%;
            font-size: 10px;
        }
        footer .page:after {
            content: counter(page, sans-serif);
        }

        .tablaf {
            border-collapse: collapse;
            width: 100%;
        }

        .tablaf tr td {
            font-size: 9px;
            text-align: center;
            padding: 3px;
        }

        .tab {
            margin-left: 20px;
            margin-right: 50px;
        }

        .tab1 {
            margin-left: 3px;
            margin-right: 18px;
        }

        .tab2 {
            margin-left: 10px;
            margin-right: 60px;
        }
        .page-break {
            page-break-after: always;
        }
        .page-break-non {
            page-break-after: avoid;
        }

    </style>
</head>

<body>

    <header>
        <img src="img/reportes/sep.png" alt='sep' width="16%"
            style='position:fixed; left:0; margin: -170px 0 0 20px;' />
        <h6>SUBSECRETAR&Iacute;A DE EDUCACI&Oacute;N E INVESTIGACI&Oacute;N TECNOL&Oacute;GICAS</h6>
        <h6>DIRECCI&Oacute;N GENERAL DE CENTROS DE FORMACI&Oacute;N PARA EL TRABAJO</h6>
        <h6>REGISTRO DE EVALUACI&Oacute;N POR SUBOBJETIVOS</h6>
        <h6>(RESD-05)</h6>
        <div id="curso">
            UNIDAD DE CAPACITACI&Oacute;N: <span class="tab">{{ $curso->plantel }} {{ $curso->unidad }}</span>
            CLAVE CCT: <span class="tab">{{ $curso->cct }}</span>
            AREA: <span class="tab">{{ $curso->area }}</span>
            ESPECIALIDAD: &nbsp;&nbsp;{{ $curso->espe }}
            <br />
            CURSO: <span class="tab1">{{ $curso->curso }}</span>
            CLAVE: <span class="tab1">{{ $curso->clave }}</span>
            CICLO ESCOLAR: <span class="tab1">{{ $curso->ciclo }}</span>
            FECHA INICIO: <span class="tab1"> {{ $curso->fechaini }}</span>
            FECHA TERMINO: &nbsp;&nbsp; {{ $curso->fechafin }}
            <br />
            GRUPO: <span class="tab2">{{ $curso->grupo }}</span>
            HORARIO: <span class="tab2"> {{ $curso->dia }} DE {{ $curso->hini }} A {{ $curso->hfin }}</span>
            CURP: &nbsp;&nbsp;{{ $curso->curp }}
        </div>
    </header>
    @if(!is_null($uuid))
        <footer>
            {{-- <div style="display: inline-block; width: 50%;"></div> --}}
            <div class="page-number"><small class="link">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} <br> Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas </small></div>
        </footer>
    @endif

    <main>
        <table class="tabla">
            <thead>
                <tr>
                    <th width="15px" rowspan="2">N<br />U<br />M</th>
                    <th width="90px" rowspan="2">N&Uacute;MERO DE <br />CONTROL</th>
                    <th width="300px">NOMBRE DEL ALUMNO</th>
                    <th colspan="17" width="380"><b>CLAVE DE CADA SUBOBJETIVO</b></th>
                    <th rowspan="2"><b>RESULTADO FINAL</b></th>

                </tr>
                <tr>
                    <th>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th>
                    <th colspan="17">RESULTADO</th>
                </tr>
            </thead>
            @php $i = 16; @endphp
            <tbody>
                @foreach ($alumnos as $a)
                    <tr width="10 px;">
                        <td>{{ $consec++ }}</td>
                        <td>{{ $a->matricula }}</td>
                        <td>{{ $a->alumno }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $a->calificacion }}</td>
                    </tr>
                    @if($consec > $i && isset($alumnos[$consec]->alumno))
                        </tbody>
                        </table>
                        <br><br><br>
                        @if(!is_null($objeto))
                            <div style="display: inline-block; width: 85%;">
                                <table style="width: 100%; font-size: 5px;">
                                    @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                                        <tr>
                                            <td style="width: 10%; font-size: 7px;"><b>Nombre del firmante:</b></td>
                                            <td style="width: 90%; font-size: 7px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: top; font-size: 7px;"><b>Firma Electrónica:</b></td>
                                            <td style="font-size: 7px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 7px;"><b>Puesto:</b></td>
                                            @if ($dataFirmante->curp == $moist['_attributes']['curp_firmante'])
                                                <td style="font-size: 7px; height: 25px;">{{ $dataFirmante->cargo }}</td>
                                            @else
                                                <td style="font-size: 7px; height: 25px;">Instructor externo</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td style="font-size: 7px;"><b>Fecha de Firma:</b></td>
                                            <td style="font-size: 7px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 7px;"><b>Número de Serie:</b></td>
                                            <td style="font-size: 7px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div style="display: inline-block; width: 15%;">
                                <img style="position: fixed; width: 15%; top: 45%; left: 79%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                            </div>
                        @endif
                        <div class="page-break"></div>
                        @php $i = $i+15; @endphp
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th width="15px" rowspan="2">N<br />U<br />M</th>
                                    <th width="90px" rowspan="2">N&Uacute;MERO DE <br />CONTROL</th>
                                    <th width="300px">NOMBRE DEL ALUMNO</th>
                                    <th colspan="17" width="380"><b>CLAVE DE CADA SUBOBJETIVO</b></th>
                                    <th rowspan="2"><b>RESULTADO FINAL</b></th>

                                </tr>
                                <tr>
                                    <th>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th>
                                    <th colspan="17">RESULTADO</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
        </table>
        <br><br><br>
        @if(!is_null($objeto))
            <div style="display: inline-block; width: 85%;">
                <table style="width: 100%; font-size: 5px;">
                    @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                        <tr>
                            <td style="width: 10%; font-size: 7px;"><b>Nombre del firmante:</b></td>
                            <td style="width: 90%; font-size: 7px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; font-size: 7px;"><b>Firma Electrónica:</b></td>
                            <td style="font-size: 7px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 7px;"><b>Puesto:</b></td>
                            @if ($dataFirmante->curp == $moist['_attributes']['curp_firmante'])
                                <td style="font-size: 7px; height: 25px;">{{ $dataFirmante->cargo }}</td>
                            @else
                                <td style="font-size: 7px; height: 25px;">Instructor externo</td>
                            @endif
                        </tr>
                        <tr>
                            <td style="font-size: 7px;"><b>Fecha de Firma:</b></td>
                            <td style="font-size: 7px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 7px;"><b>Número de Serie:</b></td>
                            <td style="font-size: 7px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div style="display: inline-block; width: 15%;">
                <img style="position: fixed; width: 15%; top: 45%; left: 79%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        @endif
    </main>
</body>

</html>
