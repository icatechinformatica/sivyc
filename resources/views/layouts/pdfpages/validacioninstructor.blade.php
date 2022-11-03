
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
                font-size: 1.3em;
                /* margin: 10px; */
            }
            @page {
                margin: 20px 30px 40px;

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
            bottom: 70px;
            /* right: 0px; */
            /* height: 60px; */
            /* text-align: center; */
            /* line-height: 60px; */
            border: 1px solid white;
            }
            img.izquierda {
                float: left;
                width: 300px;
                height: 60px;
            }

            img.izquierdabot {
                float: inline-end;
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
        .tablad { border-collapse: collapse;font-size: 12px;border: black 1px solid; text-align: center; padding:0.5px;}
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}
        .tablag tr td{ font-size: 8px; padding: 1px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        </style>
    </head>
    <body style="margin-top:90px; margin-bottom:70px;">
        <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <img class="derecha" src="{{ public_path('img/chiapas.png') }}">
            <div style="clear:both;">
                <h6>{{$distintivo}}</h6>
            </div>
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/franja.png') }}">
            <img class="derecha" src="{{ public_path('img/icatech-imagen.png') }}">
            <div class="page-break-non"></div>
        </footer>
        <div class= "container">
            @foreach ($especialidades AS $wort => $cadwell)
                @if($wort != 0)
                    <div class="page-break"></div>
                @endif
                <div align=right> <b>Dirección Técnica Académica</b></div>
                <div align=right> <b>Memorandum No. {{$especialidades[0]->memorandum_validacion}}</b></div>
                <div align=right> <b>{{$especialidades[0]->unidad_solicita}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

                <br><br><b>{{$unidad->dunidad}}.</b>
                <br>{{$unidad->pdunidad}} {{$unidad->unidad}}.
                <br>Presente.
                <br><br>Con relación a la solicitud de @if($cadwell->status == 'EN FIRMA') validación @elseif($cadwell->status == 'REACTIVACION EN FIRMA') reactivación @else revalidación @endif del instructor, realizada mediante memorándum núm. {{$cadwell->memorandum_solicitud}}, me permito indicarle que el siguiente docente ha quedado @if($cadwell->status == 'EN FIRMA') validado @elseif($cadwell->status == 'REACTIVACION EN FIRMA') reactivado @else revalidado @endif en la especialidad que se indica.
                <br><br>
                <div class="table table-responsive">
                    <table class="tablad" style="border-color: black">
                        {{-- <thead>
                            <tr>
                                <th style="border-color: black; width: 90px;">INSTRUCTOR</th>
                                <th style="border-color: black; width: 100px;">ESPECIALIDAD</th>
                                <th style="border-color: black">CURSOS A IMPARTIR</th>
                                <th style="border-color: black; width: 120px">OBSERVACIONES</th>
                            </tr>
                        </thead> --}}
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
                                <td style='width: 180px;'>Especialidad y Clave de la Especialidad:</td>
                                <td style='width: 360px;'>{{$cadwell->nombre}}</td>
                                <td style='width: 180px;'>{{$cadwell->clave}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Instructor:</td>
                                <td style='width: 360px;' colspan="2">{{$instructor->tipo_honorario}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Nivel de Estudios que Cubre para la Especialidad:</td>
                                <td style='width: 360px;' colspan="2">{{$cadwell->perfil_profesional}}</td>
                            </tr>
                            <tr>
                                <td style='width: 180px;'>Observaciones:</td>
                                <td style='width: 360px;' colspan="2">{{$cadwell->observacion_validacion}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                Es preciso señalar que, en su expediente consta que cumple con los requisitos y documentos que requiere el perfil de la especialidad solicitada.
                <br><br>
                Agradeciendo de antemano su atención, envío un cordial saludo.
                <br><br>
                <div class="row">
                    <div class="column">
                        <table class="tablad">
                            <thead>
                                <tr>
                                    <td>
                                        <br><b>ELABORÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{$elaboro->name}}</small></small></small>
                                        <br><small><small><small>{{$elaboro->puesto}}</small></small></small>
                                        <br><br><br>
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
                                        <br><b>REVISÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{$unidad->jcyc}}</small></small></small>
                                        <br><small><small><small>{{$unidad->pjcyc}}</small></small></small>
                                        <br><br>
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
                                        <br><b>AUTORIZÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br><small><small><small>{{$unidad->dacademico}}</small></small></small>
                                        <br><small><small><small>{{$unidad->pdacademico}}</small></small></small>
                                        <br><br>
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
                                        <br><b>REVISÓ</b><br><br>
                                        <b><br>_________________________</b>
                                        <br>alguien1<br>
                                        <b>111</b>
                                        <br><br><br>
                                    </td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                “Este documento es de uso interno y no tiene validez jurídica ni contractual, se extiende únicamente con fines académicos. Estos datos y resultados son considerados confidenciales por lo que se prohíbe su reproducción parcial o total para fines distintos al uso interno de la Dirección Técnica Académica y las Unidades de Capacitación”.
                <br><br>
                <small><small>C.c.p. {{$unidad->jcyc}} .- {{$unidad->pjcyc}}. – Para su conocimiento - Edificio.</small></small>
                <br><small><small>C.c.p. {{$unidad->academico}}.- {{$unidad->pacademico}} DE LA UNIDAD {{$unidad->unidad}}. – Para su conocimiento - Edificio.</small></small>
                <br><small><small>Archivo /Minutario.</small></small>
            @endforeach
        </div>
    </body>
</html>
