
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body{
                font-family: sans-serif;
                /* border: 1px solid black; */
                font-size: 1.3em;
                /* margin: 10px; */
            }
            @page {
                margin: 20px 30px 40px;

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
            bottom: 70px;
            /* right: 0px; */
            /* height: 60px; */
            /* text-align: center; */
            /* line-height: 60px; */
            border: 1px solid white;
            }
            img.izquierda {
                float: left;
                width: 100%;
                height: 80px;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 90px;
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
            .tablas{border-collapse: collapse;width: 990px;}
        .tablas tr{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablas th{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablaf { border-collapse: collapse; width: 100%;border: gray 1px solid; }
        .tablaf tr td { font-size: 7px; text-align: center; padding: 0px;}
        .tablad { border-collapse: collapse;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px;}
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}
        .tablag tr td{ font-size: 8px; padding: 1px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 15px;
                left: 15px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
        </style>
    </head>
    <body style="margin-top:90px; margin-bottom:70px;">
        <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <br><h6>{{$distintivo}}</h6>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </footer>
        <div class= "container">
            <div align=right> <b>Unidad de Capacitación {{$data_unidad->unidad}}</b> </div>
            <div align=right> <b>Memorandum No. {{$especialidades[0]->memorandum_solicitud}}</b></div>
            <div align=right> <b>{{$data_unidad->municipio}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

            <br><br><b>{{$data_unidad->dacademico}}.</b>
            <br>{{$data_unidad->pdacademico}}.
            <br>Presente.<br>

            <br><p class="text-justify">Por medio de la presente, me dirijo a usted para solicitar la baja operativa del instructor externo de la unidad {{$data_unidad->unidad}} que a continuación se menciona:</p>
            <div class="table table-responsive">
                <table class="tablad" style="border-color: black">
                    <thead>
                        <tr>
                            <th style="border-color: black; width: 110px;">INSTRUCTOR</th>
                            <th style="border-color: black; width: 150px;">NO. MEMORANDUM</th>
                            <th style="border-color: black; width: 310px;">ESPECIALIDAD</th>720
                            <th style="border-color: black; width: 150px">MOTIVO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @php foreach ($especialidades AS $cc => $watt){} $cc++; @endphp
                            <td rowspan="{{$cc}}"><small>{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</small></td>
                        @foreach($especialidades AS $key => $cold)
                            @if($key != 0)
                                <tr>
                            @endif
                                @php if(isset($cold->hvalidacion)){$lastkey = array_key_last($cold->hvalidacion);};@endphp
                                <td><small>
                                    @if(isset($cold->hvalidacion))
                                        @if(isset($cold->hvalidacion[$lastkey]['memo_val']))
                                            {{$cold->hvalidacion[$lastkey]['memo_val']}}
                                        @else
                                            {{$cold->hvalidacion[$lastkey]['memo_baja']}}
                                        @endif
                                    @else
                                        {{$cold->memorandum_validacion}}
                                    @endif
                                </small></td>

                                <td><small>{{$cold->especialidad}}</small></td>
                                <td><small>{{$instructor->motivo}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-left">Sin otro particular, aprovecho la ocasión para saludarlo.</p>
            <br><p class="text-left"><p>Atentamente.</p></p>
            <br><br><br><br><b>{{$data_unidad->dunidad}}.</b>
            <br><b>{{$data_unidad->pdunidad}} DE {{$data_unidad->unidad}}.
            <br><br><h6><small><b>C.c.p. {{$data_unidad->jcyc}}.- {{$data_unidad->pjcyc}}.-  Para su conocimiento</b></small></h6>
            <h6><small><b>Archivo/Minutario<b></small></h6>
            <small><small><b>Elaboró y Validó: {{$data_unidad->academico}}.- {{$data_unidad->pacademico}} DE {{$data_unidad->unidad}}.</b></small></small>
        </div>
    </body>
</html>
