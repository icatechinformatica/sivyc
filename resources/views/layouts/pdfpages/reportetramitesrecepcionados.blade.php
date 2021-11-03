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
                <br>CURSOS PARA PAGO DE {{$tipo}} - MODALIDAD: {{$modalidad}}
                <br>"TRÁMITES DE LA UNIDAD DE CAPACITACIÓN {{$unidad}} - RECEPCIONADOS EN EL DEPARTAMENTO DE RECURSOS FINANCIEROS - EJERCICIO 2021"
            </div>
            <div class="form-row">
                <table width="700" class="table table-bordered">
                    <thead>
                        <tr>
                            <td style="background-color:#DADADA; border: 1px solid #070707;" scope="col" rowspan="2" class="text-center"><small style="font-size: 12px;"><br>CONS.</small></td>
                            <td style="background-color:#DADADA; border: 1px solid #070707" scope="col" colspan="4" class="text-center"><small style="font-size: 12px;">VALIDACIÓN DE CONTRATO</small></td>
                            <td style="background-color:#FFEEBA; border: 1px solid #070707" scope="col" rowspan="2" class="text-center"><small style="font-size: 12px;"><br>FECHA FIRMA<br>DE CONTRATO</small></td>
                            <td style="background-color:#DADADA; border: 1px solid #070707" scope="col" rowspan="2" class="text-center"><small style="font-size: 12px;"><br>NOMBRE DEL INSTRUCTOR</small></td>
                            <td style="background-color:#DADADA; border: 1px solid #070707" scope="col" colspan="4" class="text-center"><small style="font-size: 12px;">VALIDACIÓN POSETRIOR</small></td>
                            <td style="background-color:#FFEEBA; border: 1px solid #070707" scope="col" rowspan="2" class="text-center"><small style="font-size: 12px;"><br>FECHA DE<br>RECEPCIÓN</small></td>
                            {{--<td width="8px"><small style="font-size: 8px;">FECHA</small></td> --}}
                        </tr>
                        <tr>
                            <td style="background-color:#FFEEBA; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">FECHA</small></td>
                            <td style="background-color:#B8DAFF; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">NÚM.</small></td>
                            <td style="background-color:#C3E6CB; border: 1px solid #070707"scope="col" class="text-center"><small style="font-size: 12px;">CLAVE CURSO</small></td>
                            <td style="background-color:#C3E6CB; border: 1px solid #070707"scope="col" class="text-center"><small style="font-size: 12px;">ESTATUS</small></td>
                            <td style="background-color:#FFEEBA; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">FECHA</small></td>
                            <td style="background-color:#C3E6CB; border: 1px solid #070707"scope="col" class="text-center"><small style="font-size: 12px;">ESTATUS</small></td>
                            <td style="background-color:#FFEEBA; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">FECHA</small></td>
                            <td style="background-color:#C3E6CB; border: 1px solid #070707"scope="col" class="text-center"><small style="font-size: 12px;">ESTATUS</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                        @php $cons = $key + 1; $num = explode('/', $item->numero_contrato); @endphp
                            @if ($item->chk_rechazado == TRUE)
                                @php $conteo = round(count($item->fecha_rechazo)/2); $float = is_float(count($item->fecha_rechazo)/2); @endphp
                                <tr>
                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 10px;">{{$cons}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 12px;">{{$item->fecha_rechazo['0']['fecha']}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center" width="8px"><small style="font-size: 12px;">{{$num['3']}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 10px;">{{$item->clave}}</small></td>
                                    <td style="background-color:#F5C6CB; border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 10px;">RECHAZADO</small></td>
                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 12px;">{{$item->inicio}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 10px;">{{$item->nombre}}</small></td>
                                    @foreach ($item->fecha_rechazo as $gap=>$cadwell)
                                        @if ($gap != 0)
                                            <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">{{$cadwell['fecha']}}</small></td>
                                            <td style="background-color:#F5C6CB; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">RECHAZADO</small></td>
                                        @endif
                                        @if ($gap == 2 || $gap == 4 || $gap == 6)
                                            @if ($gap == 2)
                                                @if ($item->recepcion == '')
                                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 10px;">-</small></td>
                                                @else
                                                    <td style="border: 1px solid #070707" scope="col" rowspan={{$conteo}} class="text-center"><small style="font-size: 10px;">{{$item->recepcion}}</small></td>
                                                @endif
                                            @endif
                                            </tr>
                                            <tr>
                                        @endif
                                    @endforeach
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">{{$item->fecha_status}}</small></td>
                                    <td style="background-color:#BEE5EB; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">VALIDADO</small></td>
                                    @if ($float == TRUE)
                                        <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">-</small></td>
                                        <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">-</small></td>
                                    @endif
                                </tr>
                            @else
                                <tr>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">{{$cons}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">{{$item->fecha_status}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center" width="8px"><small style="font-size: 12px;">{{$num['3']}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">{{$item->clave}}</small></td>
                                    <td style="background-color:#BEE5EB; border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">VALIDADO</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">{{$item->inicio}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">{{$item->nombre}}</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">-</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">-</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 12px;">-</small></td>
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">-</small></td>
                                    @if ($item->recepcion == '')
                                    <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">-</small></td>
                                    @else
                                        <td style="border: 1px solid #070707" scope="col" class="text-center"><small style="font-size: 10px;">{{$item->recepcion}}</small></td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </body>
</html>
