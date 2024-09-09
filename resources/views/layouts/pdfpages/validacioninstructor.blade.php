<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body{
                font-family: sans-serif;
                /* border: 1px solid black; */
                font-size: 1.2em;
                /* margin: 10px; */
            }
            @page {
                margin: 20px 30px;

            }
            .row {
            margin-left:-10px;
            margin-right:30px;
            }

            .column {
            float: left;
            width: 25%;
            padding: 5px;
            }

            /* Clearfix (clear floats) */
            .row::after {
            content: "";
            clear: both;
            display: table;
            }
            .ftr{
                position: fixed;
                top: 85%;
                bottom: 0;
                left: 0;
                height: 60px;
            }
            header {
            position: fixed;
            left: 0px;
            top: 0px;
            right: 0px;
            color: black;
            text-align: center;
            line-height: 60px;
            height: 60px;
            }
            header h1{
            margin: 10px 0;
            }
            header h2{
            margin: 0 0 10px 0;
            }
            th, td {
            border-style:solid;
            border-color: black;
            }
            footer {
            position: fixed;
            /* left: 0px; */
            bottom: 80px;
            /* right: 0px; */
            /* height: 60px; */
            /* text-align: center; */
            /* line-height: 60px; */
            border: 1px solid white;
            }
            img.izquierda {
                float: left;
                width: 100%;
                height: 90px;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 80px;
            }

            img.derecha {
                float: right;
                width: 200px;
                height: 60px;
            }
            div.content
            {
                margin-bottom: 750%;
                margin-right: -25%;
                margin-left: 0%;
            }
            .floatleft {
                float:left;
            }
            .page-break {
                page-break-after: always;
            }
            .page-break-non {
                page-break-after: avoid;
            }
            .table1, .table1 td {
                border:0px ;
            }
            .table1 td {
                padding:5px;
            }
            .tablas{border-collapse: collapse;width: 990px;}
        .tablas tr{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablas th{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablaf { border-collapse: collapse; width: 100%;border: gray 1px solid; }
        .tablaf tr td { font-size: 7px; text-align: center; padding: 0px;}
        .tablad { border-collapse: collapse;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px; width: 100%;}
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}
        .tablag tr td{ font-size: 8px; padding: 1px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 10px;
                left: 15px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
        </style>
    </head>
    <body style="margin-top:90px; margin-bottom:70px;">
        <header>
            <img class="izquierda" src="{{ public_path('img/formatos/bannerhorizontal.jpeg') }}">
            <br><h6>{{$distintivo}}</h6>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
            <p class='direccion'><b>
                @php $direccion = explode("*",$funcionarios['dacademico']['direccion']) @endphp
                @foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}} @endforeach
                <br>
                {{-- @if(!is_null($funcionarios['dunidad']['telefono']))Teléfono: {{$funcionarios['dunidad']['telefono']}} @endif  --}}
                @if(!is_null($funcionarios['dacademico']['correo'])) Correo: {{$funcionarios['dacademico']['correo']}} @endif
            </b></p>
        </footer>
        <div>
            @php $cont=0; foreach($especialidades AS $ari){if($ari->status != 'BAJA EN FIRMA'){$cont++;}} @endphp
                <div align=right> <b>Dirección Técnica Académica</b></div>
                <div align=right> <b>Memorandum No. @if($especialidades[0]->status != 'BAJA EN FIRMA') {{$especialidades[0]->memorandum_validacion}} @else {{$especialidades[0]->memorandum_baja}} @endif</b></div>
                <div align=right> <b>Tuxtla Gutiérrez, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>
                <b>{{ $funcionarios['dunidad']['titulo'] }} {{ $funcionarios['dunidad']['nombre'] }}.</b>
                <br>{{ $funcionarios['dunidad']['puesto'] }}.
                <br>Presente.
                <br><br>Con relación a la solicitud de @if($especialidades[0]->status == 'EN FIRMA') validación @elseif($especialidades[0]->status == 'REACTIVACION EN FIRMA') reactivación @else actualización @endif del instructor, realizada mediante memorándum núm. {{$especialidades[0]->memorandum_solicitud}}, me permito indicarle que el siguiente docente ha quedado @if($especialidades[0]->status == 'EN FIRMA') validado @elseif($especialidades[0]->status == 'REACTIVACION EN FIRMA') reactivado @else actualizado @endif en @if($cont == 1) la especialidad @else las especialidades @endif que se indica.
                <div class="table table-responsive">
                    <table class="tablad" style="border-color: black; font-size: 7;">
                        <tbody>
                            <tr>
                                <td style='width: 180px;'>Nombre del Instructor:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Numero de Control:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->numero_control}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;' rowspan="{{$cont}}">Especialidad y Clave de la Especialidad:</td>
                                @foreach ($especialidades AS $wort => $cadwell)
                                    @if ($cadwell->status != 'BAJA EN FIRMA')
                                        <td style='width: 360px;'>{{$cadwell->nombre}}</td>
                                        <td style='width: 180px;'>{{$cadwell->clave}}</td>
                                    </tr>
                                    <tr>
                                    @endif
                                @endforeach
                                <td style='width: 180px;'>Instructor:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->tipo_honorario}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Nivel de Estudios que Cubre para la Especialidad:</td>
                                <td style='width: 360px;' colspan="2"> @if($especialidades[0]->status != 'BAJA EN FIRMA'){{$especialidades[0]->perfil_profesional}} @else {{$especialidades[1]->perfil_profesional}}@endif</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Observaciones:</td>
                                <td style='width: 360px;' colspan="2"> @if($especialidades[0]->status != 'BAJA EN FIRMA'){{$especialidades[0]->observacion_validacion}} @else {{$especialidades[1]->observacion_validacion}}@endif</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                Es preciso señalar que, en su expediente consta que cumple con los requisitos y documentos que requiere el perfil de la especialidad solicitada.
                <br>
                Agradeciendo de antemano su atención, envío un cordial saludo.
                <div>
                    <div class="column">
                        <table class="tablad">
                            <thead>
                                <tr>
                                    <td>
                                        <b>ELABORÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>C. {{ $funcionarios['elabora']['nombre'] }}</small></small></small>
                                        <br><small><small><small>{{ $funcionarios['elabora']['puesto'] }}</small></small></small>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="column">
                        <table style="border-collapse:initial;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td>
                                        <b>REVISÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }}</small></small></small>
                                        <br><small><small><small>{{ $funcionarios['gestionacademica']['puesto'] }}</small></small></small>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="column">
                        <table style="border-collapse:initial;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td>
                                        <b>AUTORIZÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }}</small></small></small>
                                        <br><small><small><small>{{ $funcionarios['dacademico']['puesto'] }}</small></small></small>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    <div class="column">
                        <table style="border-collapse:initial;font-size: 12px; color:white; border: black 1px solid; text-align: center; padding:0.5px;">
                            <thead>
                                <tr>
                                    <td>
                                        <b>REVISÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br>alguien1<br>
                                        <b>111</b>
                                        <br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <div>
                    “Este documento es de uso interno y no tiene validez jurídica ni contractual, se extiende únicamente con fines académicos. Estos datos y resultados son considerados confidenciales por lo que se prohíbe su reproducción parcial o total para fines distintos al uso interno de la Dirección Técnica Académica y las Unidades de Capacitación”.
                    <br><br>
                    <small><small>C.c.p. {{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }} .- {{ $funcionarios['gestionacademica']['puesto'] }}. – Para su conocimiento - Edificio.</small></small>
                    <br><small><small>Archivo</small></small>
                </div>
        </div>
    </body>
</html>
