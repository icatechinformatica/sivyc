<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="{{ public_path('\BootstrapCustomized\bootstrap-onlytables.min.css') }}">
        <style>
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 110px 40px 110px;
            }
            header { position: fixed;
                left: 0px;
                top: -100px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
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
                left: 700px;
                width: 350px;
                height: 60px;

            }

            img.derecha {
                float: right;
                width: 200px;
                height: 60px;
            }
            div.content
            {
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
            <br><h6>"2021, Año de la Independencia"</h6>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
        <div id="wrapper">
            <div align=center><b><h6>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
                <br>CURSOS PARA PAGO DE HONORARIOS
                <br>TRAMITES VALIDADOS EN EL SIVYC Y RECEPCIONADOS EN EL DEPARTAMENTO DE RECURSOS FINANCIEROS - EJERCICIO 2021
            </div>
            <div class="form-row">
                <table width="700" class="table table-striped" id="table-one">
                    <thead>
                        <tr>
                            <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                            <td scope="col" colspan="3"><small style="font-size: 8px;">MES: {{$nombremesini}}</small></td>
                        </tr>
                        <tr>
                            <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $x = 1;
                            do
                            {
                                $a = 'mes' . $x;
                                $x++;
                            }while($$a == NULL);
                        ?>
                        @foreach ($$a as $item)
                        <tr>
                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                            <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                            <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                            <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($mes2 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: FEBRERO</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes2 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes3 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: MARZO</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes3 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes4 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: ABRIL</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes4 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes5 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: MAYO</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes5 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes6 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: JUNIO</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes6 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes7 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: JULIO</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes7 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes8 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: AGOSTO</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes8 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes9 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: SEPTIEMBRE</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes9 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes10 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: OCTUBRE</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes10 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes11 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: NOVIEMBRE</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes11 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if($mes12 != NULL)
                    <table width="700" class="table table-striped" id="table-one">
                        <thead>
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 8px;">MES: DICIEMBRE</small></td>
                            </tr>
                            <tr>
                                <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES PRESENTADOS POR UNIDAD</small></td>
                                <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mes12 as $item)
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                    <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <br>
            </div>
        </div>
    </body>
</html>
