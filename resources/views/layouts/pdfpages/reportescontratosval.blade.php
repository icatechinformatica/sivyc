<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="{{ public_path('\BootstrapCustomized\bootstrap-onlytables.min.css') }}">
        <style>
            * {
            box-sizing: border-box;
            }

            .row {
            margin-left:-5px;
            margin-right:-5px;
            }

            .column {
            float: left;
            width: 25%;
            padding: 0px;
            }

            /* Clearfix (clear floats) */
            .row::after {
            content: "";
            clear: left;
            display: table;
            }

            table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #ddd;
            }

            th, td {
            text-align: center;
            padding: 16px;
            }

            tr:nth-child(even) {
            background-color: #f2f2f2;
            }
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 170px 40px 220px;
            }
            header { position: fixed;
                left: 0px;
                top: -160px;
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
            #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            }

            #customers td, #customers th {
            padding: 8px;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}

            #customers tr:hover {background-color: #ddd;}

            #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
            }
        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
            <br><h6>"2021, Año de la Independencia"</h6>
            <div align=center><b><h6>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
                <br>CURSOS PARA PAGO DE HONORARIOS
                <br>TRAMITES VALIDADOS EN EL SIVYC Y RECEPCIONADOS EN EL DEPARTAMENTO DE RECURSOS FINANCIEROS - EJERCICIO 2021
                <br>REPORTE AL {{$now->day}} DE {{$monthnow}} {{$now->year}}
            </div>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
        <div id="wrapper">
            <div class="row">
                <div class="column">
                    <table id="customers">
                        <thead>
                            <tr>
                                <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: {{$nombremesini}}</b></small></td>
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
                                $column = 1;
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
                </div>
                @if($mes2 != NULL)
                    @if($nombremesini != 'FEBRERO')
                        <div class="column">
                            <?php $column++; ?>
                            <table id="customers">
                                <thead>
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: FEBRERO</b></small></td>
                                    </tr>
                                    <tr>
                                        <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                        <td scope="col"><small style="font-size: 8px;">TRAMITES<br> PRESENTADOS POR UNIDAD</small></td>
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
                        </div>
                    @endif
                @endif
                @if($mes3 != NULL)
                    @if($nombremesini != 'MARZO')
                        <div class="column">
                            <?php $column++; ?>
                            <table id="customers">
                                <thead>
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: MARZO</b></small></td>
                                    </tr>
                                    <tr>
                                        <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                        <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
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
                        </div>
                    @endif
                @endif
                @if($mes4 != NULL)
                    @if($nombremesini != 'ABRIL')
                        <div class="column">
                            <?php $column++; ?>
                            <table id="customers">
                                <thead>
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: ABRIL</b></small></td>
                                    </tr>
                                    <tr>
                                        <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                        <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
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
                        </div>
                    @endif
                @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes5 != NULL)
                @if($nombremesini != 'MAYO')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: MAYO</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: MAYO</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes5 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes6 != NULL)
                @if($nombremesini != 'JUNIO')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: JUNIO</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: JUNIO</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes6 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes7 != NULL)
                @if($nombremesini != 'JULIO')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: JULIO</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: JULIO</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes7 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes8 != NULL)
                @if($nombremesini != 'AGOSTO')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: JULIO</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: JULIO</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes8 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes9 != NULL)
                @if($nombremesini != 'SEPTIEMBRE')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: SEPTIEMBRE</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: SEPTIEMBRE</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes9 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes10 != NULL)
                @if($nombremesini != 'OCTUBRE')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: OCTUBRE</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: OCTUBRE</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes10 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes11 != NULL)
                @if($nombremesini != 'NOVIEMBRE')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: NOVIEMBRE</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: NOVIEMBRE</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes11 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            @if($mes12 != NULL)
                @if($nombremesini != 'DICIEMBRE')
                    <div class="column">
                        <?php $column++; ?>
                        <table id="customers">
                            <thead>
                                @if($column == 1)
                                    <tr>
                                        <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: DICIEMBRE</b></small></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td scope="col" colspan="3"><small style="font-size: 12px;"><b>MES: DICIEMBRE</b></small></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                                    <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mes12 as $item)
                                    <tr>
                                        @if($column == 1)
                                            <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                        @endif
                                        <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                        <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            <div class="column">
                <?php $column++; ?>
                <table id="customers">
                    <thead>
                        @if($column == 1)
                            <tr>
                                <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                <td scope="col" colspan="3"><small style="font-size: 12px;"><b>ACUMULADO: {{$nombremesini}} - {{$nombremesfin}}</b></small></td>
                            </tr>
                        @else
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 12px;"><b>ACUMULADO: {{$nombremesini}} - {{$nombremesfin}}</b></small></td>
                            </tr>
                        @endif
                        <tr>
                            <td scope="col"><small style="font-size: 8px;">VALIDADOS EN EL SIVYC</small></td>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES <br>PRESENTADOS POR UNIDAD</small></td>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES POR ENTREGAR</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                @if($column == 1)
                                    <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                @endif
                                <td scope="col"><small style="font-size: 8px;">{{$item->sivyc}}</small></td>
                                <td scope="col"><small style="font-size: 8px;">{{$item->fisico}}</small></td>
                                <td scope="col"><small style="font-size: 8px;">{{$item->porentregar}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($column == 4)
                </div>  <!--este div cierra el row si ya hay 4 columnas-->
                <div class="row">
                <?php $column = 0;?>
            @endif
            <div class="column">
                <?php $column++; ?>
                <table id="customers">
                    <thead>
                        @if($column == 1)
                            <tr>
                                <td scope="col" rowspan="2"><small style="font-size: 8px;">UNIDADES DE CAPACITACION</small></td>
                                <td scope="col" colspan="3"><small style="font-size: 12px;"><b>ESTATUS POR UNIDAD</b></small></td>
                            </tr>
                        @else
                            <tr>
                                <td scope="col" colspan="3"><small style="font-size: 12px;"><b>ESTATUS POR UNIDAD</b></small></td>
                            </tr>
                        @endif
                        <tr>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES PAGADOS</small></td>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES <br> POR <br> PAGAR</small></td>
                            <td scope="col"><small style="font-size: 8px;">TRAMITES OBSERVADOS</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                @if($column == 1)
                                    <td scope="col"><small style="font-size: 8px;">{{$item->ubicacion}}</small></td>
                                @endif
                                <td scope="col"><small style="font-size: 8px;">{{$item->pagado}}</small></td>
                                <td scope="col"><small style="font-size: 8px;">{{$item->porpagar}}</small></td>
                                <td scope="col"><small style="font-size: 8px;">{{$item->observados}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <br>
        </div>
    </body>
</html>
