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
                <br>DIRECCIÓN DE PLANEACIÓN
                <br>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO
                <br>FORMATO DE CONTROL DE SUFICIENCIA PRESUPUESTAL
            </div>
            <div class="form-row">
                <table width="700" class="table table-striped" id="table-one">
                    <thead>
                        <tr>
                            <td scope="col"><small style="font-size: 8px;">Sec. de Solic.</small></td>
                            <td scope="col" ><small style="font-size: 8px;">MEMO. SOLICITADO</small></td>
                            <td scope="col" ><small style="font-size: 8px;">NO. DE SUFICIENCIA</small></td>
                            <td width="8px"><small style="font-size: 8px;">FECHA</small></td>
                            <td scope="col" ><small style="font-size: 8px;">INSTRUCTOR</small></td>
                            <td scope="col"><small style="font-size: 8px;">UNIDAD/A.M. DE CAP.</small></td>
                            <td scope="col" ><small style="font-size: 8px;">CURSO</small></td>
                            <td scope="col"><small style="font-size: 8px;">CLAVE DEL GRUPO</small></td>
                            <td scope="col" ><small style="font-size: 8px;">Z.E.</small></td>
                            <td scope="col"><small style="font-size: 8px;">HSM</small></td>
                            <td scope="col" ><small style="font-size: 8px;">IMPORTE POR HORA</small></td>
                            <td scope="col"><small style="font-size: 8px;">IVA 16%</small></td>
                            <td scope="col" ><small style="font-size: 8px;">PARTIDA/ CONCEPTO<small></td>
                            <td scope="col" ><small style="font-size: 8px;">IMPORTE TOTAL FEDERAL<small></td>
                            <td scope="col" ><small style="font-size: 8px;">IMPORTE TOTAL ESTATAL<small></td>
                            <td scope="col" ><small style="font-size: 8px;">RETENCIÓN ISR<small></td>
                            <td scope="col" ><small style="font-size: 8px;">RETENCIÓN IVA<small></td>
                            <td width="6px" ><small style="font-size: 8px;">MEMO. RESPUESTA<small></td>
                            <td scope="col" ><small style="font-size: 8px;">FECHA REGISTRO<small></td>
                            <td scope="col" ><small style="font-size: 8px;">OBSERVACIONES<small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$key}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->no_memo}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->suf}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->fecha}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->unidad_capacitacion}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->curso}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->clave}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->ze}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->dura}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->importe_hora}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$iva[$key]}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">12101 Honorarios</td>
                                @if ($recursos[$key] == "Federal")
                                    <td scope="col" class="text-center"><small style="font-size: 8px;">{{$cantidad[$key]}}</td>
                                    <td scope="col" class="text-center"><small style="font-size: 8px;"></td>
                                @else
                                    <td scope="col" class="text-center"><small style="font-size: 8px;"></td>
                                    <td scope="col" class="text-center"><small style="font-size: 8px;">{{$cantidad[$key]}}</td>
                                @endif
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$risr[$key]}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$riva[$key]}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->folio_validacion}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->fecha_validacion}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 8px;">{{$item->comentario}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </body>
</html>
