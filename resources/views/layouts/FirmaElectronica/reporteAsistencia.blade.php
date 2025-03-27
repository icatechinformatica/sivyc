<html>

<head>
    <title>LISTA DE ASISTENCIA</title>
    <style>
        body {
            font-family: sans-serif;
        }

        @page {
            margin: 100px 25px 0px 25px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -80px;
            right: 0px;
            text-align: center;
        }

        header h6 {
            height: 0;
            line-height: 14px;
            padding: 8px;
            margin: 0;
        }

        table #curso {
            font-size: 8px;
            padding: 10px;
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
            bottom: 100px;
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
            margin-left: 10px;
            margin-right: 20px;
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
    {!! $header !!}
    @if(!is_null($uuid))
        <footer>
            {{-- <div style="display: inline-block; width: 50%;"></div> --}}
            <div style="display: inline-block; width: 85%; margin-top: 25px;">
                <table style="width: 100%; font-size: 5px; line-height: 1;">
                    @foreach ($objeto['firmantes']['firmante'][0] as $keys=>$moist)
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
                                @if(!is_null($firmantes))
                                    <td style="font-size: 7px; height: 25px;">{{ $firmantes->cargo }}</td>
                                 @else
                                    <td style="font-size: 7px; height: 25px;">{{ $dataFirmante->cargo }}</td>
                                @endif
                            @else
                                <td style="font-size: 7px; height: 25px;">INSTRUCTOR EXTERNO</td>
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
                {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                <img style="position: fixed; width: 15%; top: 5%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
            <div class="page-number"><small class="link">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} <br> Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas </small></div>
        </footer>
    @endif
    {!! $body_html !!}
</body>

</html>
