<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REPORTE INGRESOS PROPIOS</title>
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: sans-serif;
        }

        @page {
            margin: 110px 10px 110px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -100px;
            right: 0px;
            height: 60px;
            background-color: white;
            color: black;
            text-align: center;
            line-height: 60px;
        }

        header h1 {
            margin: 10px 0;
        }

        header h2 {
            margin: 0 0 10px 0;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: -90px;
            right: 0px;
            height: 60px;
            background-color: white;
            color: black;
            text-align: center;
        }

        footer .page:after {
            content: counter(page);
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
            width: 300px;
            height: 60px;
        }

        img.izquierdabot {
            float: inline-end;
            width: 450px;
            height: 60px;
        }

        img.derechabot {
            position: absolute;
            left: 400px;
            width: 350px;
            height: 60px;

        }

        img.derecha {
            float: right;
            width: 200px;
            height: 60px;
        }

        div.content {
            margin-top: 60%;
            margin-bottom: 70%;
            margin-right: -25%;
            margin-left: 0%;
        }

    </style>
</head>

<body>
    <header>
        <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
        <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
        {{-- <br> --}}
        <div id="wrapper">
            <div >
                <b>
                    <h6>
                        <br><br><br><br>
                        <br>REPORTE INGRESOS PROPIOS
                    </h6>
                    <h6>"2021, Año de la Independencia"</h6>
                </b>
            </div>
        </div>
        <br>
    </header>
    <footer>
        {{-- <img class="izquierdabot" src="{{ public_path('img/franja.png') }}"> --}}
        <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
    </footer>

    <br><br>
    <table>
        <tbody>
            <tr>
                <td>Rango de Fechas:</td>
                <td> </td>
                <td>{{$fechaInicio}} - {{$fechaTermino}}</td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Unidad</th>
                <th>{{$yearAnt}}</th>
                <th>{{$year}}</th>
                <th>Diferencia vs {{$yearAnt}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($totalesPeriodoActual as $key => $item)
                <tr>
                    <td>{{$item->ubicacion}}</td>
                    <td>
                        @if (isset($totalesPeriodoAnterior[$key]->total))
                            {{$totalesPeriodoAnterior[$key]->total}}
                        @else
                            0.0
                        @endif
                    </td>
                    <td>{{$item->total}}</td>
                    <td>
                        @if (isset($totalesPeriodoAnterior[$key]->total))
                            {{$item->total - $totalesPeriodoAnterior[$key]->total}}
                        @else
                            {{$item->total}}
                        @endif
                    </td>
                </tr>
            @endforeach

            <tr>
                <td><strong>Total</strong></td>
                <td>
                    <div style="display: none">{{$totalPeriodoAnt = 0}}</div>
                    @foreach ($totalesPeriodoAnterior as $item1)
                        <div style="display: none">{{$totalPeriodoAnt += $item1->total}}</div>
                    @endforeach
                    <strong>{{$totalPeriodoAnt}}</strong>
                </td>
                <td>
                    <div style="display: none">{{$totalPeriodoAct = 0}}</div>
                    @foreach ($totalesPeriodoActual as $item2)
                        <div style="display: none">{{$totalPeriodoAct += $item2->total}}</div>
                    @endforeach
                    <strong>{{$totalPeriodoAct}}</strong>
                </td>
                <td>
                    <div style="display: none">{{$totalDiferencia = 0}}</div>
                    @foreach ($totalesPeriodoActual as $key => $item3)
                        @if (isset($totalesPeriodoAnterior[$key]->total))
                            <div style="display: none">{{$totalDiferencia += ($item3->total - $totalesPeriodoAnterior[$key]->total)}}</div>
                        @else
                            <div style="display: none">{{$totalDiferencia += $item3->total}}</div>
                        @endif
                    @endforeach
                    <strong>{{$totalDiferencia}}</strong>
                </td>
            </tr>
        </tbody>
    </table>
</body>