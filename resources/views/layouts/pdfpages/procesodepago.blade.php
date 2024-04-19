<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-wfSDFE50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            body{
                font-family: sans-serif;
                font-size: 1.3em;
                margin: 10px;
            }
            @page {
                margin: 110px 40px 110px;
            }
            header { position: fixed;
                left: 0px;
                top: -100px;
                padding-left: 45px;
                height: 70px;
                width: 85%;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: -90px;
                right: 0px;
                height: 100px;
                width: 85%;
                padding-left: 45px;
                background-color: white;
                color: black;
                text-align: center;
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
            table, td {
              border:1px solid black;
            }
            table {
              border-collapse:collapse;
              width:100%;
            }
            td {
              padding:px;
            }

            .table1, .table1 td {
                border:0px ;
            }
            .table1 td {
                padding:5px;
            }
            small {
                font-size: .7em
            }
            .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 15px;
                left: 65px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }

        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <br><h6>{{$distintivo}}</h6>
        </header>
        <div class= "container g-pt-30">
            <div id="content">
                {!! $body_html !!}
                <table class="table1">
                    <tr>
                        <td><p align="center">Atentamente</p></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td><div align="center">{{$director->nombre}}</td></div>
                    </tr>
                    <tr>
                        <td><div align="center">{{$director->cargo}}</td></div>
                    </tr>
                </table>
                @if(!is_null($objeto))
                    {{-- <div style="display: inline-block; width: 85%;"> --}}
                        <table style="width: 85%; font-size: 8px; border-collapse: collapse; border: none;">
                            @foreach ($objeto['firmantes']['firmante'][0] as $keys=>$moist)
                            <tr>
                                <td style="width: 10%; font-size: 8px; border: none;"><b>Nombre del firmante:</b></td>
                                <td style="width: 90%; font-size: 8px; border: none;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; font-size: 8px; border: none;"><b>Firma Electrónica:</b></td>
                                <td style="font-size: 8px; border: none;">{{ wordwrap($moist['_attributes']['firma_firmante'], 105, "\n", true) }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8px; border: none;"><b>Puesto:</b></td>
                                <td style="font-size: 8px; height: 25px; border: none;">{{ $puesto->cargo }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8px; border: none;"><b>Fecha de Firma:</b></td>
                                <td style="font-size: 8px; border: none;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8px; border: none;"><b>Número de Serie:</b></td>
                                <td style="font-size: 8px; border: none;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                            </tr>
                            @endforeach
                        </table>
                    {{-- </div> --}}
                    <div style="display: inline-block; width: 15%;">
                        {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                        <img style="position: fixed; width: 100%; top: 52%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                    </div>
                @endif
                <p style="line-height:0.8em;">
                    <b><small>C.c.p.{{$ccp1->nombre}}.- {{$ccp1->cargo}}.-Para su conocimiento.</small></b><br/>
                    <b><small>C.c.p.{{$ccp2->nombre}}.- {{$ccp2->cargo}}.-Mismo fin.</small></b><br/>
                    <b><small>C.c.p.{{$ccp3->nombre}}.- {{$ccp3->cargo}}.-Mismo fin.</small></b><br/>
                    <b><small>Archivo/ Minutario<small></b><br/>
                    <b><small>Validó: {{$ccp3->nombre}}.- {{$ccp3->cargo}}.</small></b><br/>
                    <b><small>Elaboró: {{$ccp3->nombre}}.- {{$ccp3->cargo}}.</small></b>
                </p>
            </div>
        </div>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </footer>
    </body>
</html>
