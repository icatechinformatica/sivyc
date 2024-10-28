<html>
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
                margin: 110px 20px 60px;
            }
            header {
            position: fixed;
            left: 0px;
            top: -110px;
            right: 0px;
            color: black;
            text-align: center;
            line-height: 30px;
            height: 100px;
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
            bottom: -30px;
            right: 0px;
            height: 100px;
            text-align: center;
            line-height: 60px;
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
            .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 0px;
                left: 15px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
            .landscape {
                page: landscape;
                size: landscape;
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
            <img class="izquierda" src="{{ public_path('img/formatos/bannervertical.jpeg') }}">
            <h6><i>{{$distintivo}}<i></h6>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </footer>
        <div class= "container g-pt-30">
            {!!$bodySupre!!}
            @if(!is_null($uuid))
                <br><b> C. {{$funcionarios['director']}}</b> <!-- now -->
                <br><b>{{$funcionarios['directorp']}}</b>
                <br><br><br><div style="display: inline-block; width: 85%;">
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
                                <td style="font-size: 7px; height: 25px;">{{$puestos[$key]}}</td>
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
                    <img style="position: fixed; width: 15%; top: 50%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                </div>
            @else
                <br><br><b> C. {{$funcionarios['director']}}</b> <!-- now -->
                <br><b>{{$funcionarios['directorp']}}</b>
                <!--<br><b>Unidad de Capacitación {$unidad->ubicacion}}.</b>-->
                @if ($unidad->cct != '07EI')
                    <br><b>Acción Movil {{$data_supre->unidad_capacitacion}}.</b>
                @else
                @endif
            @endif
            {{-- aqui usar el fi --}}
            @if(!is_null($bodyCcp))
                {!!$bodyCcp!!}
            @else
                <br><br><small><b>C.c.p. {{$funcionarios['ccp1']}}.- {{$funcionarios['ccp1p']}}.-Para su conocimiento</b></small>
                <br><small><b>C.c.p. {{$funcionarios['ccp2']}}.- {{$funcionarios['ccp2p']}}.-Mismo Fin</b></small>
                <br><small><b>Archivo.<b></small>
                <br><br><small><small><b>Validó: {{$funcionarios['director']}}.- {{$funcionarios['directorp']}}</b></small></small>
                <br><small><small><b>Elaboró: {{$funcionarios['delegado']}}.- {{$funcionarios['delegadop']}}</b></small></small>
            @endif
        </div>
    </body>
</html>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
