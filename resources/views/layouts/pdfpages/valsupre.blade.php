<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style type="text/css">
            *{
                box-sizing: border-box;
            }

            @page {
                margin: 100px 60px 60px -5px;
            }
            header {
                position: fixed;
                left: 0px;
                top: -90px;
                padding-left: 45px;
                height: 70px;
                width: 100%;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
                margin-bottom: -10px;
            }
            body{
                font-family: sans-serif;
                font-size: 1.3em;
                margin: 10px;
            }
            header h1{
                margin: 10px 0;
            }
            header h2{
                margin: 0 0 10px 0;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: -50px;
                right: 0px;
                height: 100px;
                width: 85%;
                padding-left: 45px;
                /* background-color: none; */
                /* color: black; */
                text-align: center;
            }
            footer table {
                width: 100%;
            }
            footer p {
                text-align: right;
            }
            footer .izq {
                text-align: left;
            }
            img.izquierda {
                float: left;
                width: 100%;
                height: 100%;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 100%;
            }
            .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 0px;
                left: 65px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
            #wrappertop {
                margin-top: -2%;
                background-position: 5px 10px;
                background-repeat: no-repeat;
                background-size: 32px;
                width: 100%;
                line-height: 60%;
                font-size: 16px;
                padding: 0px;
                border: 1px solid transparent;
                margin-bottom: 0px;
            }
            #wrapperbot {
                background-position: 5px 10px;
                background-repeat: no-repeat;
                background-size: 32px;
                width: 100%;
                line-height: 70%;
                font-size: 16px;
                padding: 12px 20px 12px 40px;
                border: 1px solid transparent;
                margin-bottom: 0px;
            }

            div.a {
                text-align: center;
            }

            div.b {
                text-align: left;
            }

            div.c {
                text-align: right;
            }

            div.d {
                text-align: justify;
            }
            header .distintivo {
                border: 1px solid transparent;
                position: absolute;
                top: 100%;
                left: 2%;
                width: 100%;
                height: 100%;
                /* display: flex; */
                vertical-align: top;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                /* background-color: rgba(255, 255, 255, 0.5); Opcional: para agregar un fondo semi-transparente */
            }
        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" style="margin-bottom: 0px;" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            {{-- <div class="distintivo"> --}}
                <p class="distintivo">{{$distintivo}}</p>
            {{-- </div><br> <br> <br> <br>  --}}
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>
                AV. BARRIO SAN JACINTO N° 154, ENTRE CALLE PÁJAROS Y CALLE CONEJOS,<br>FRACC. EL DIAMANTE C.P. 29059<br>TUXTLA GUTIÉRREZ, CHIAPAS. TELEFONO +52(961)6121621.<br>EMAIL: icatech@icatech.chiapas.gob.mx
            </br></p>
        </footer>
        <div id="wrapperbot">
            {!!$body_html!!}
            @if(!is_null($uuid))
                <br><div style="display: inline-block; width: 85%;">
                <table style="width: 100%; font-size: 9px;">
                    @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                        <tr>
                            <td style="width: 10%; font-size: 9px;"><b>Nombre del firmante:</b></td>
                            <td style="width: 90%; font-size: 9px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; font-size: 9px;"><b>Firma Electrónica:</b></td>
                            <td style="font-size: 9px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 110, "\n", true) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Puesto:</b></td>
                            <td style="font-size: 9px; height: 25px;">{{$puestos[$key]}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Fecha de Firma:</b></td>
                            <td style="font-size: 9px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Número de Serie:</b></td>
                            <td style="font-size: 9px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div style="display: inline-block; width: 15%;">
                {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                <img style="position: fixed; width: 13%; top: 47%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        @else
            <div align=center>
                <small><small>C. {{$funcionarios['remitente']}}</small></small>
                <br><small>________________________________________</small><br/>
                <br><small><small>{{$funcionarios['remitentep']}}</small></small></b>
            </div>
            <br><br><br><br><br><br>
        @endif
            {!!$ccp_html!!}
        </div>
    </body>
</html>
