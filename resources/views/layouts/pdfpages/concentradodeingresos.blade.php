<!DOCTYPE HTML>
@php $total = 0.00; $totalpag = 0; @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-wfSDFE50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            *{
                box-sizing: border-box;
            }
            @page {
                margin: 160px 40px 80px;
            }

            body{
                font-family: sans-serif;
                font-size: 1.3em;
                margin: 10px;
            }
            .content{
                width: 100%;
                /* height:fit-content; */
                border: 1px solid green;
            }
            header { position: fixed;
                left: 0px;
                top: -130px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: -60px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            img.izquierda {
                /* display: inline; */
                float: left;
                width: 160px;
                height: 60px;
            }

            img.izquierdabot {
                float: inline-end;
                width: 350px;
                height: 60px;
            }

            img.derechabot {
                position: absolute;
                left: 450px;
                width: 250px;
                height: 60px;

            }

            img.derecha {
                /* display: inline; */
                float: right;
                width: 160px;
                height: 60px;
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
            .cent{
                float: left;
                width: 408px;
                height: 60px;
                vertical-align: top;
                font-size: 9px;
                line-height: 14px;
            }
            .cent p{
                height: auto;
            }
            .righty{
                float: right;
                width: 408px;
                height: 60px;
                vertical-align: top;
                font-size: 9px;
                line-height: 14px;
            }
            .contador{
                /* display:initial; */
                float: right;
                vertical-align: middle;
                text-align: justify;
                font-size: 10px;
            }
            /* .page-number:before {
                counter-increment: section -1;
                content: "HOJA " counter(page) " DE nb"
            } */

            /* #pageCounter {
                counter-reset: pageTotal;
            } */
            #pageCounter span {
                counter-increment: pageTotal;
            }
            #pageNumbers {
                /* counter-reset: currentPage; */
                float: right;
                vertical-align: middle;
                text-align: justify;
                font-size: 10px;
            }
            #pageNumbers p:before {
                counter-increment: currentPage;
                content: "Página " counter(currentPage);
            }
            /* #pageNumbers p:after {
                content: counter(pageTotal);
            } */

        </style>
    </head>
    <body>
        <header>
            @php $totalpag++; @endphp
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
                <div class="cent">
                    <p>
                        FORMA RF-001
                        <br>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS
                        <br>UNIDAD DE CAPACITACIÓN {{$data['0']->ubicacion}}
                        <br>CONCENTRADO DE INGRESOS PROPIOS
                    </p>
                </div>
            <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
            <br><h6>{{$distintivo}}</h6>
            <div class="righty">
                <div id="pageNumbers">
                    <p class="page-number" style="border:1px solid black; vertical-align: middle; padding-left: 25px; padding-right: 25px;"></p>
                </div>
            </div>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
        <div class= "form-row">
            <div style="text-align: center">
                <div style="display: inline; width: 50%;">
                    <table style="border: 0px; text-align: center; font-size: 10px;">
                        <tbody>
                            <tr>
                                <td style="width:33%;">FECHA DE ELABORACIÓN</td>
                                <td style="border: 0px;"></td>
                                <td style="width:33%;">{{$fecha['inicio']}}</td>
                            </tr>
                            <tr>
                                <td>{{$fecha['hoy']}}</td>
                                <td style="border: 0px;"></td>
                                <td>{{$fecha['termino']}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div style="text-align: center">
                <div style="display: inline; width: 50%;">
                    <table style="font-size: 10px;">
                        <tbody>
                            <tr>
                                <td style="text-align: center;">DESPOSITOS(S) EFECTUADO(S) A LA CUENTA BANCARIA:</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 2px;">BBVA BANCOMER NO. {{$data[0]->cuenta}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
                    <table style="text-align: center; border: 0px;">
                        <tbody>
                            <tr style="font-size: 8px;">
                                <td style="width: 15%;"><b>MOVTO BANCARIO</b></td>
                                <td style="width: 15%;"><b>NO. DE RECIBO Y/O FACTURA</b></td>
                                <td style="width: 55%;"><b>CONCEPTO DE COBRO</b></td>
                                <td style="width: 15%;"><b>IMPORTE</b></td>
                            </tr>
                            @foreach($data as $cadwell)
                                <tr style="font-size: 12px;">
                                    <td>{{$cadwell->movimiento_bancario}}</td>
                                    <td>@if($cadwell->folio_pago != '') {{$cadwell->folio_pago}} @else N/A @endif</td>
                                    <td>{{$cadwell->curso}}</td>
                                    <td>$ {{$cadwell->costo}}</td>
                                </tr>
                                @php $total = $total + $cadwell->costo;@endphp
                            @endforeach
                            <tr>
                                <td style="height: 8px;"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan='3' style="text-align: right; padding-right: 40px; border: 0px;">TOTAL</td>
                                @php $total = number_format((float)$total,2,'.', '') @endphp
                                <td>$ {{$total}}</td>
                            </tr>
                        </tbody>
                    </table>
            <br>
            <div style="text-align: center">
                <div style="display: inline; width: 50%;">
                    <table style="font-size: 10px;">
                        <tbody>
                            <tr>
                                <td style="padding-left: 2px;">OBSERVACIONES:
                                    <br>SE ENVÍA FICHAS DE DEPOSITO CON NO. DE MOVIMIENTO BANCARIO:
                                    @foreach ($data as $key => $moist)
                                        @if($key != 0),&nbsp; @endif
                                        {{$moist->movimiento_bancario}}
                                    @endforeach
                                </td>
                            </tr>
                            {{-- <tr>
                                <td style="padding-left: 2px;">NOTA: (NOTA)</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div style="text-align: center">
                <div style="display: inline; width: 50%;">
                    <table style="font-size: 10px; text-align: center;">
                        <tbody>
                            <tr>
                                <td style="width: 33%">REALIZA</td>
                                <td style="width: 33%">REVISA</td>
                                <td style="width: 33%">AUTORIZA</td>
                            </tr>
                            <tr>
                                <td style="border-bottom: 0px;">{{$realiza->name}}</td>
                                <td style="border-bottom: 0px;">{{$data[0]->delegado_administrativo}}</td>
                                <td style="border-bottom: 0px;">{{$data[0]->dunidad}}</td>
                            </tr>
                            <tr>
                                <td style="border-top: 0px;">{{$realiza->puesto}}</td>
                                <td style="border-top: 0px;">{{$data[0]->pdelegado_administrativo}}</td>
                                <td style="border-top: 0px;">{{$data[0]->pdunidad}} {{$data[0]->ubicacion}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="text-align:justify;"><small>DECLARO BAJO PROTESTA DE DECIR VERDAD, QUE LOS DATOS CONTENIDOS EN ESTE CONCENTRADO SON VERÍDICOS Y MANIFIESTO TENER CONOCIMIENTO DE LAS SANCIONES QUE SE APLICARÁN EN CASO CONTRARIO</small></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
