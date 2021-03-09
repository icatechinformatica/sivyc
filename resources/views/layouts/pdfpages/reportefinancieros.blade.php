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
                <br>DIRECCIÓN ADMINISTRATIVA
                <br>DEPARTAMENTO DE RECURSOS FINANCIEROS
                <br>FORMATO DE CONTROL DE ESTADOS DE CONTRATOS Y PAGOS
            </div>
            <div class="form-row">
                <table width="700" class="table table-striped" id="table-one">
                    <thead>
                        <tr>
                            <td scope="col" class="text-center"><small style="font-size: 10px;"><b>Sec. de Solic.</b></small></td>
                            <td scope="col" class="text-center" ><small style="font-size: 10px;"><b>MEMO. SOLICITADO</b></small></td>
                            <td scope="col" class="text-center" ><small style="font-size: 10px;"><b>NO. DE SUFICIENCIA</b></small></td>
                            <td scope="col" class="text-center" ><small style="font-size: 10px;"><b>ESTAD</b>O<small></td>
                            <td width="8px" class="text-center"><small style="font-size: 10px;"><b>FECHA</b></small></td>
                            <td scope="col" class="text-center" ><small style="font-size: 10px;"><b>UNIDAD/A.M</b>.<small></td>
                            <td scope="col" class="text-center" ><small style="font-size: 10px;"><b>INSTRUCTOR</b></small></td>
                            <td scope="col" class="text-center" ><small style="font-size: 10px;"><b>CURSO</b></small></td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;"><b>CLAVE DEL GRUPO</b></small></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key=>$item)
                            <tr>
                                <?php $count = $count+1;?>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$count}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->no_memo}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->suf}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->status}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->fecha}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->unidad_capacitacion}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->nombre}} {{$item->apellidoPaterno}} {{$item->apellidoMaterno}}</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->curso}}</small></td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">{{$item->clave}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </body>
</html>
