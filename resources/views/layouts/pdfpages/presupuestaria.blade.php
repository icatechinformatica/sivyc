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
            @else
                <br><br><b> C. {{$funcionarios['director']}}</b> <!-- now -->
                <br><b>{{$funcionarios['directorp']}}</b>
                <!--<br><b>Unidad de Capacitación {$unidad->ubicacion}}.</b>-->
                @if ($unidad->cct != '07EI')
                    <br><b>Acción Movil {{$data_supre->unidad_capacitacion}}.</b>
                @else
                @endif
            @endif
            <br><br><br><h6><small><b>C.c.p. {{$funcionarios['ccp1']}}.- {{$funcionarios['ccp1p']}}.-Mismo Fin</b></small></h6>
            <h6><small><b>C.c.p. {{$funcionarios['ccp2']}}.- {{$funcionarios['ccp2p']}}.-Mismo Fin</b></small></h6>
            <h6><small><b>Archivo.<b></small></h6>
            <br><br><small><b>Valido: {{$funcionarios['delegado']}}.- {{$funcionarios['delegadop']}}</b></small></h6>
            <br><small><b>Elaboró: @if(!is_null($data_supre->elabora)){{strtoupper($data_supre->elabora['nombre'])}}.- {{strtoupper($data_supre->elabora['puesto'])}}@else{{$funcionarios['elabora']}}.- {{$funcionarios['elaborap']}}@endif</b></small></h6>
        </div>
    </body>
</html>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
