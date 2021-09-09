<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
                margin-right: 25%;
                margin-left: 0%;
            }
        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
            <br><h6>{{$distintivo}}</h6>
        </header>
        <div id="wrapper">
            <div align=center><b><h6>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
                <br>DIRECCIÓN DE PLANEACIÓN
                <br>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO
                <br>FORMATO DE SOLICITUD DE SUFICIENCIA PRESUPUESTAL
                <br>UNIDAD DE CAPACITACIÓN {{$data2->unidad_capacitacion}} ANEXO DE MEMORÁNDUM No. {{$data2->no_memo}}</h6></b> </div>
            </div>
            <div class="form-row">
                <table width="700" class="table table-striped" id="table-one">
                    <thead>
                        <tr class="active">
                            <td scope="col"><small style="font-size: 10px;">No. DE SUFICIENCIA</small></td>
                            <td scope="col" ><small style="font-size: 10px;">FECHA</small></td>
                            <td scope="col" ><small style="font-size: 10px;">INSTRUCTOR</small></td>
                            <td scope="col" width="10px"><small style="font-size: 10px;">UNIDAD/ A.M. DE CAP.</small></td>
                            <td scope="col" ><small style="font-size: 10px;">SERVICIO</small></td>
                            <td scope="col" ><small style="font-size: 10px;">NOMBRE</small></td>
                            <td scope="col"><small style="font-size: 10px;">CLAVE DEL GRUPO</small></td>
                            <td scope="col" ><small style="font-size: 10px;">ZONA ECÓNOMICA</small></td>
                            <td scope="col"><small style="font-size: 10px;">HSM (horas)</small></td>
                            <td scope="col" ><small style="font-size: 10px;">IMPORTE POR HORA</small></td>
                            <td scope="col"><small style="font-size: 10px;">IVA 16%</small></td>
                            <td scope="col" ><small style="font-size: 10px;">PARTIDA/ CONCEPTO</small></td>
                            <td scope="col"><small style="font-size: 10px;">IMPORTE</small></td>
                            <td scope="col" ><small style="font-size: 10px;">OBSERVACION<small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->folio_validacion}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->fecha}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->unidad}}</small></td>
                                @if ($item->tipo_curso=='CERTIFICACION')
                                    <td><small style="font-size: 10px;">CERTIFICACIÓN EXTRAORDINARIA</small></td>
                                @else
                                    <td><small style="font-size: 10px;">CURSO</small></td>
                                @endif
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->curso_nombre}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->clave}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->ze}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->dura}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->importe_hora}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->iva}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">12101 Honorarios</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->importe_total}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->comentario}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
            </div>
            <div align=center> <b>SOLICITA
                <br>
                <br><small>{{$getremitente->nombre}} {{$getremitente->apellidoPaterno}} {{$getremitente->apellidoMaterno}}</small>
                <br>________________________________________
                <br><small>{{$getremitente->puesto}} <br> <font style="text-transform: uppercase;">{{$getremitente->area}}</font></small></b>
            </div>
        </div>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
    </body>
</html>
