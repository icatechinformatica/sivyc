<html>

<head>
    <title>REPORTE FOTOGRAFICO</title>
    <style>
        body {
            font-family: sans-serif;
            top: 20px;
        }

        @page {
            /* margin: 100px 25px 170px 25px; */
            /* margin: 25px 100px 25px 170px; */
            margin: 35px 30px 40px 30px;
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

        /* table #curso {
            font-size: 8px;
            padding: 10px;
            line-height: 18px;
            text-align: justify;
        } */

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
            bottom: -170px;
            height: 150px;
            width: 100%;
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

        /* by Jose Luis Moreno */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* text-align: center; */
            /* margin-top: 20px; */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            /* text-align: left; */
            font-size: 12px;
        }

    </style>
</head>


<body>
    <header>
        <img src="img/instituto_oficial.png" alt="Logo Izquierdo" width="30%" style="position:fixed; left:0; top:0;" />
        <img src="img/chiapas.png" alt="Logo Derecho" width="25%" style="position:fixed; right:20%; top:0;" />
    </header>
    @if(!is_null($uuid))
        {{-- <footer> --}}
            {{-- <div style="display: inline-block; width: 50%;"></div> --}}
            <div class="page-number"><small class="link">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} <br> Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas </small></div>
        {{-- </footer> --}}
    @endif
    <br>
    <br>
    <br>
    <h6 style="text-align: center;">{{isset($leyenda) ? $leyenda : ''}}</h6>
    {{-- tabla --}}
    @if ($cursopdf)
        <table border="1" class="" width="100%">
            <tbody>
                <tr>
                    <td colspan="2"><b>CURSO: </b>{{$cursopdf->curso}}</td>
                </tr>
                <tr>
                    <td colspan="2"><b>TIPO: </b>{{$cursopdf->tcapacitacion}}</td>
                </tr>
                <tr>
                    <td><b>FECHA DE INICIO: </b>{{$cursopdf->inicio}}</td>
                    <td><b>FECHA DE TERMINO: </b>{{$cursopdf->termino}}</td>
                </tr>
                <tr>
                    <td><b>CLAVE: </b>{{$cursopdf->clave}}</td>
                    <td><b>HORARIO: </b>{{$cursopdf->hini. ' A '. $cursopdf->hfin}}</td>
                </tr>
                <tr>
                    <td><b>TITULAR DE LA U.C: </b>{{$cursopdf->dunidad}}</td>
                    <td><b>INSTRUCTOR: </b>{{$cursopdf->nombre}}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: right;">
            <p style="">Unidad de Capacitación {{ucfirst(strtolower($cursopdf->ubicacion))}}
            @if ($cursopdf->ubicacion != $cursopdf->unidad)
            , Accion Movil {{ucfirst(strtolower($cursopdf->unidad))}}.
            @else

            @endif
            </p>
            <p>{{ucfirst(strtolower($cursopdf->unidad))}}, Chiapas. A {{$fecha_gen}}.</p>
        </div>
    @endif
    <br><br>
     {{-- Mostrar imagenes --}}
     @if (count($base64Images) > 0)
     <div class="" style="text-align: center;">
         @foreach($base64Images as $base64)
             <img style="width: 350px; height: 350px; margin: 5px;" src="data:image/jpeg;base64,{{$base64}}" alt="Foto">
         @endforeach
     </div>
     @endif

    {{-- prueba de imagen --}}
    {{-- <img src="https://sivyc.icatech.gob.mx/storage/uploadFiles/UNIDAD/revision_exoneracion/1_ICATECH-800-2040-2023_230613104402_23.pdf" alt=""> --}}
    {{-- <embed src="https://sivyc.icatech.gob.mx/storage/uploadFiles/UNIDAD/revision_exoneracion/1_ICATECH-800-2040-2023_230613104402_23.pdf"/> --}}


    {{-- Apartado de mostrar firmas --}}
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
                            <td style="font-size: 7px; height: 25px;">Instructor</td>
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
            <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            {{-- <img style="position: fixed; width: 18%; top: 30%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
        </div>
    @endif

</body>

</html>
