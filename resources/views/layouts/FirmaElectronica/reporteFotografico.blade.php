<html>

<head>
    <title>REPORTE FOTOGRAFICO</title>
    <style>
        body {
            font-family: sans-serif;
            margin-top: 15%;
            margin-bottom: 4%;
        }

        @page {
            /* margin: 100px 25px 170px 25px; */
            /* margin: 35px 30px 40px 30px; */
            margin: 35px 30px 150px 30px;
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
            /* padding: 3px; */
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
        .estilo_tabla {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* text-align: center; */
            /* margin-top: 20px; */
        }

        .estilo_colum {
            border: 1px solid #ddd;
            padding: 3px;
            /* text-align: left; */
            font-size: 12px;
        }

        .direccion {
            top: 1.3cm;
            text-align: left;
            position: absolute;
            bottom: 60px;
            left: 20px;
            font-size: 8px;
            color:#FFF;
            font-weight: bold;
            line-height: 1;
        }

    </style>
</head>


<body>
    <header>
        <img src="img/instituto_oficial.png" alt="Logo Izquierdo" width="30%" style="position:fixed; left:0; top:0;" />
        <img src="img/chiapas.png" alt="Logo Derecho" width="25%" style="position:fixed; right:0; top:0;" />
    </header>
    <footer>
        @if(!is_null($uuid))
            <div style="position: absolute; top: -35px; left: 15px; font-size:10px; text-align:justify">
                <span style="">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas.</span>
            </div>
        @endif
        {{-- position: relative; top:-76% --}}
        <div style="position: absolute; top: 5px;">
            <img style="" src="img/formatos/footer_vertical.jpeg" width="100%">
            @if ($cursopdf)
                @php $direccion = explode("*", $cursopdf->direccion);  @endphp
                <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
            @endif
        </div>
    </footer>
    <div style="margin-top: -9%; margin-bottom: 4%;">
        <h6 style="text-align: center;">{{isset($leyenda) ? $leyenda : ''}}</h6>
    </div>
    <div style="text-align:center;">
        <span style="text-align: center;">REPORTE FOTOGRÁFICO DEL INSTRUCTOR</span>
    </div>
    {{-- <h6 style="text-align: center;">{{isset($leyenda) ? $leyenda : ''}}</h6> --}}
    {{-- tabla --}}
    @if ($cursopdf)
        {{-- Lugar y fecha --}}
        <div style="text-align: right;">
            <p style="margin-bottom: 5px;">Unidad de Capacitación {{ucfirst(strtolower($cursopdf->ubicacion))}}
            @if ($cursopdf->ubicacion != $cursopdf->unidad)
            , Accion Movil {{ucfirst(strtolower($cursopdf->unidad))}}.
            @else

            @endif
            </p>
            <p style="margin-top: 0px; margin-bottom: 20px;">{{ucfirst(strtolower($cursopdf->unidad))}}, Chiapas. A {{$fechapdf}}.</p>
        </div>

        <table border="1" class="estilo_tabla" width="100%">
            <tbody>
                <tr class="estilo_colum">
                    <td class="estilo_colum" colspan="2"><b>CURSO: </b>{{$cursopdf->curso}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum" colspan="2"><b>TIPO: </b>{{$cursopdf->tcapacitacion}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>FECHA DE INICIO: </b>{{$cursopdf->inicio}}</td>
                    <td class="estilo_colum"><b>FECHA DE TÉRMINO: </b>{{$cursopdf->termino}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>CLAVE: </b>{{$cursopdf->clave}}</td>
                    <td class="estilo_colum"><b>HORARIO: </b>{{$cursopdf->hini. ' A '. $cursopdf->hfin}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>NOMBRE DEL TITULAR DE LA U.C: </b>{{$cursopdf->dunidad}}</td>
                    <td class="estilo_colum"><b>NOMBRE DEL INSTRUCTOR: </b>{{$cursopdf->nombre}}</td>
                </tr>
            </tbody>
        </table>

    @endif
    <br><br><br><br>
     {{-- Mostrar imagenes --}}
     @if (count($base64Images) > 0)
     <div class="" style="text-align: center;">
         @foreach($base64Images as $base64)
             <img style="width: 320px; height: 320px; margin-right: 3px; margin-left: 3px;" src="data:image/jpeg;base64,{{$base64}}" alt="Foto">
         @endforeach
     </div>
     @endif

    {{-- prueba de imagen --}}
    {{-- <img src="https://sivyc.icatech.gob.mx/storage/uploadFiles/UNIDAD/revision_exoneracion/1_ICATECH-800-2040-2023_230613104402_23.pdf" alt=""> --}}
    {{-- <embed src="https://sivyc.icatech.gob.mx/storage/uploadFiles/UNIDAD/revision_exoneracion/1_ICATECH-800-2040-2023_230613104402_23.pdf"/> --}}


    {{-- Apartado de mostrar firmas --}}
    <div>
        @if(!is_null($objeto))
            <div style="display: inline-block; width: 85%; margin-left: 12px; margin-top: 3%;">
                <table style="width: 100%;">
                    @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                        <tr style="">
                            <td style="font-size: 10px;"><b>Nombre del firmante:</b></td>
                            <td style="font-size: 10px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; font-size: 10px;"><b>Firma Electrónica:</b></td>
                            <td style="font-size: 10px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px;"><b>Puesto:</b></td>
                            @if ($dataFirmante->curp == $moist['_attributes']['curp_firmante'])
                                <td style="font-size: 10px;">{{ $dataFirmante->cargo }}</td>
                            @else
                                <td style="font-size: 10px;">Instructor</td>
                            @endif
                        </tr>
                        <tr>
                            <td style="font-size: 10px;"><b>Fecha de Firma:</b></td>
                            <td style="font-size: 10px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px;"><b>Número de Serie:</b></td>
                            <td style="font-size: 10px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                        </tr>
                    @endforeach
                </table>
                <div style="display: inline-block; width: 10%;">
                    <img style="position: fixed; width: 16%; top: 62%; left: 77%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                </div>
            </div>

        @endif
    </div>
        {{-- <img style="position: fixed; width: 18%; top: 30%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
</body>

</html>
