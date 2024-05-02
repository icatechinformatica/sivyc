<?php
if($data->tipo_curso=='CERTIFICACION'){
    $tipo='DE LA CERTIFICACIÓN EXTRAORDINARIA';
}else{
    $tipo='DEL CURSO';
}
?>
<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-wfSDFE50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            body{
                font-family: sans-serif;
                font-size: 1.3em;
                margin: 10px;
            }
            @page {
                margin: 110px 40px 110px;
            }
            header { position: fixed;
                left: 0px;
                top: -100px;
                padding-left: 45px;
                height: 70px;
                width: 85%;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: -90px;
                right: 0px;
                height: 100px;
                width: 85%;
                padding-left: 45px;
                background-color: white;
                color: black;
                text-align: center;
            }
            img.izquierda {
                float: left;
                width: 100%;
                height: 100%;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 100%;
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
            .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 5px;
                left: 65px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }

        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <br><h6>{{$distintivo}}</h6>
        </header>
        <div class= "container g-pt-30">
            <div id="content">
                <div align=right>
                    <b>Unidad de Capacitación {{$data->unidad_capacitacion}}.</b>
                </div>
                <div align=right>
                    <b>Memorandum No. {{$data->no_memo}}.</b>
                </div>
                <div align=right>
                    <b>{{$data->unidad_capacitacion}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b>
                </div>
                <br><br><b>{{$para->nombre}} {{$para->apellidoPaterno}} {{$para->apellidoMaterno}}.</b>
                <br>{{$para->puesto}}.
                <br><br>Presente.
                <br><p class="text-justify">En virtud de haber cumplido con los requisitos de apertura <font style="text-transform:lowercase;"> {{$tipo}}</font> y validación de instructor externo, solicito de la manera más atenta gire sus apreciables instrucciones a fin de que proceda el pago correspondiente, que se detalla a continuación:</p>
                <div align=center>
                    <FONT SIZE=2><b>DATOS {{$tipo}}</b></FONT>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td><small> {{$data->curso}}</small></td>
                            <td><small>Clave: {{$data->clave}}</small></td>
                        </tr>
                        <tr>
                            <td><small>Especialidad: {{$data->espe}}</small></td>
                            <td><small>Modalidad: {{$data->mod}}</small></td>
                        </tr>
                        <tr>
                            <td><small>Fecha de Inicio y Término: {{$data->inicio}} AL {{$data->termino}}</small></td>
                            <td><small>Horario: {{$data->hini}} A {{$data->hfin}}</small></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <div align=center>
                    <FONT SIZE=2> <b>DATOS DEL INSTRUCTOR EXTERNO</b></FONT>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td><small>Nombre: {{$data->nombre}} {{$data->apellidoPaterno}} {{$data->apellidoMaterno}}</small></td>
                            <td><small>Número de Contrato: {{$data->numero_contrato}}</small></td>
                        </tr>
                        <tr>
                            <td><small>Registro STPS: NO APLICA</small></td>
                            <td><small>Memorándum de Validación: {{$data->instructor_mespecialidad}}</small></td>
                        </tr>
                        <tr>
                            <td><small>RFC: {{$data->rfc}}</small></td>
                            <td><small>Importe: {{$data->liquido}}</small></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <div align=center>
                    <FONT SIZE=2> <b>DATOS DE LA CUENTA PARA DEPOSITO O TRANSFERENCIA INTERBANCARIA</b></FONT>
                </div>
                <table>
                    <tbody>
                        @if($data->modinstructor == 'HONORARIOS')
                                <tr>
                                    <td><small>Banco: {{$data->banco}}</small></td>
                                </tr>
                                <tr>
                                    <td><small>Número de Cuenta: {{$data->no_cuenta}}</small></td>
                                </tr>
                                <tr>
                                    <td><small>Clabe Interbancaria: {{$data->interbancaria}}</small></td>
                                </tr>
                        @endif
                        @if($data->modinstructor == 'ASIMILADOS A SALARIOS')
                            @if($data->banco == NULL)
                                <tr>
                                    <td><small>Banco: NO APLICA</small></td>
                                </tr>
                                <tr>
                                    <td><small>Número de Cuenta: NO APLICA</small></td>
                                </tr>
                                <tr>
                                    <td><small>Clabe Interbancaria: NO APLICA</small></td>
                                </tr>
                            @else
                                <tr>
                                    <td><small>Banco: {{$data->banco}}</small></td>
                                </tr>
                                <tr>
                                    <td><small>Número de Cuenta: {{$data->no_cuenta}}</small></td>
                                </tr>
                                <tr>
                                    <td><small>Clabe Interbancaria: {{$data->interbancaria}}</small></td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
                <br><p class="text-left"><p>Nota: El Expediente Único soporte documental <font style="text-transform:lowercase;"> {{$tipo}}</font>, obra en poder de la Unidad de Capacitación.</p></p>
                <br><br>
                <table class="table1">
                    <tr>
                        <td><p align="center">Atentamente</p></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td><div align="center">{{$director->nombre}}</td></div>
                    </tr>
                    <tr>
                        <td><div align="center">{{$director->cargo}}</td></div>
                    </tr>
                </table>
                <p style="line-height:0.8em;">
                    <b><small>C.c.p.{{$ccp1->nombre}}.- {{$ccp1->cargo}}.-Para su conocimiento.</small></b><br/>
                    <b><small>C.c.p.{{$ccp2->nombre}}.- {{$ccp2->cargo}}.-Mismo fin.</small></b><br/>
                    <b><small>C.c.p.{{$ccp3->nombre}}.- {{$ccp3->cargo}}.-Mismo fin.</small></b><br/>
                    <b><small>Archivo<small></b><br/>
                    <b><small>Validó: {{$ccp3->nombre}}.- {{$ccp3->cargo}}.</small></b><br/>
                    <b><small>Elaboró: {{$ccp3->nombre}}.- {{$ccp3->cargo}}.</small></b>
                </p>
            </div>
        </div>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </footer>
    </body>
</html>
