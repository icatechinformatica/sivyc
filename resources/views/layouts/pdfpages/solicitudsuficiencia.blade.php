<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 120px 40px 80px;
            }
            header { position: fixed;
                left: 0px;
                top: -90px;
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
                bottom: -10px;
                right: 0px;
                height: 60px;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 35px;
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
            <br><h6>"2020, Año de Leona Vicario Benemérita Madre de la Patria"</h6>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derechabot" src="{{ public_path('img/icatech-imagen.png') }}">
        </footer>
        <div id="wrapper">
            <div align=center><b><h6> DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
                <br>DIRECCIÓN DE PLANEACIÓN
                <br>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO
                <br>FORMATO DE SOLICITUD DE SUFICIENCIA PRESUPUESTAL
                <br>UNIDAD DE CAPACITACIÓN {{$data2->unidad_capacitacion}} ANEXO DE MEMORÁNDUM No.{{$data2->no_memo}}</h6></b> </div>
            </div>
            <div class="form-row">
                <table width="700" class="table table-bordered" id="table-one">
                    <thead>
                        <tr>
                            <td scope="col"><small>No. DE SUFICIENCIA</small></td>
                            <td scope="col" ><small>FECHA</small></td>
                            <td scope="col" ><small>INSTRUCTOR</small></td>
                            <td width="10px"><small>UNIDAD/ A.M. DE CAP.</small></td>
                            <td scope="col" ><small>CURSO</small></td>
                            <td scope="col"><small>CLAVE DEL GRUPO</small></td>
                            <td scope="col" ><small>ZONA ECÓNOMICA</small></td>
                            <td scope="col"><small>HSM (horas)</small></td>
                            <td scope="col" ><small>IMPORTE POR HORA</small></td>
                            <td scope="col"><small>IVA 16%</small></td>
                            <td scope="col" ><small>PARTIDA/ CONCEPTO</small></td>
                            <td scope="col"><small>IMPORTE</small></td>
                            <td scope="col" ><small>OBSERVACION</small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <td scope="col" class="text-center"><small>{{$item->folio_validacion}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->fecha}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->unidad}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->curso_nombre}}</td>
                                <td scope="col" class="text-center"><small>{{$item->clave}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->ze}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->dura}}</small></td>
                                <td scope="col" class="text-center"><small>{{$item->importe_hora}}</td>
                                <td scope="col" class="text-center"><small>{{$item->iva}}</td>
                                <td scope="col" class="text-center"><small>12101 Honorarios</td>
                                <td scope="col" class="text-center"><small>{{$item->importe_total}}</td>
                                <td scope="col" class="text-center"><small>{{$item->comentario}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br><br>
            </div>
            <div align=center> <b>SOLICITA
                <br>
                <br>
                <br><small>{{$getremitente->nombre}} {{$getremitente->apellidoPaterno}} {{$getremitente->apellidoMaterno}}</small>
                <br>________________________________________
                <br><small>{{$getremitente->puesto}} DE {{$data2->unidad_capacitacion}}</small></b>
            </div>
        </div>
    </body>
</html>
