
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
            <div align=right> <b>Unidad de Capacitación {{$data[0]->unidad_solicita}}</b> </div>
            <div align=right> <b>Memorandum No. @if($nomemosol != null){{$nomemosol}} @else BORRADOR @endif</b></div>
            <div align=right> <b>{{$data[0]->unidad_solicita}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

            <br><b>{{$data_unidad->dacademico}}.</b>
            <br>{{$data_unidad->pdacademico}}.
            <br>Presente.<br>

            <br><p class="text-justify">Por este medio solicito la <b> @if($tipo_doc == 'REVALIDACION') actualización @elseif($tipo_doc == 'REACTIVACION') reactivación @else validación @endif  </b> como Instructor Externo, en función a que cumple con todos y cada uno de los requisitos establecidos en el manual de procedimientos del departamento de Gestión Académica.</p>
            <div class="table table-responsive">
                <table class="tablad" style="border-color: black">
                    <thead>
                        <tr>
                            <th style="border-color: black; width: 90px;">INSTRUCTOR</th>
                            <th style="border-color: black; width: 100px;">ESPECIALIDAD</th>
                            <th style="border-color: black">CURSOS A IMPARTIR</th>
                            <th style="border-color: black; width: 120px">OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cursos AS $key => $cold)
                            <tr>
                                <td><small>{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</small></td>
                                <td><small>{{$data[$key]->especialidad}}</small></td>
                                <td style="text-align:left; font-size: 10px; padding:5px;">
                                    @if($porcentaje[$key] > 50)
                                        @if($porcentaje[$key] == 100)
                                            <small>Todos los cursos relacionados a la especialidad.</small>
                                        @else
                                            <small>Todos los cursos relacionados a la especialidad excepto:</small>
                                        @endif
                                    @endif
                                    @if($porcentaje[$key] <= 50)
                                        @foreach($cold as $cadwell)
                                            <ul style="margin-left: -30px; line-height:80%;">
                                                <small><li style="text-align:left;">{{$cadwell->nombre_curso}}.</small></li>
                                            </ul>
                                        @endforeach
                                    @elseif($porcentaje[$key] >= 51 && $porcentaje[$key] < 100)
                                        @foreach($cursosnoav[$key] AS $cadwell)
                                        <ul style="margin-left: -30px; line-height:80%;">
                                                <small><li>{{$cadwell->nombre_curso}}</small></li>
                                        </ul>
                                        @endforeach
                                    @endif
                                </td>
                                <td><small>{{$data[$key]->observacion}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-left">Sin más por el momento le envío un cordial saludo.</p>
            <br><p class="text-left"><p>Atentamente.</p></p>
            <b>{{$data_unidad->dunidad}}.</b>
            <br><b>{{$data_unidad->pdunidad}} DE {{$data[0]->unidad_solicita}}.
            <br><br><small><b>"La documentación presentada por esta Unidad de Capacitación ha sido previamente cotejada con el ejemplar original".</b></small>
            <br><h6><small><b>C.c.p. {{$data_unidad->jcyc}}.- {{$data_unidad->pjcyc}}.-Mismo Fin</b></small></h6>
            <h6><small><b>Archivo/Minutario<b></small></h6>
            <small><small><b>Valido: {{$data_unidad->dunidad}}.- {{$data_unidad->pdunidad}} DE {{$data[0]->unidad_solicita}}.</b></small></small>
            <br><small><small><b>Elaboró: {{$solicito->name}}.- {{$solicito->puesto}}.</b></small></small>
            @foreach ($data as $altmer)
                <div class="page-break"></div>
                <div align=right> <b>{{$data[0]->unidad_solicita}}, Chiapas; {{$D}} de {{$M}} del {{$Y}}.</b></div>
                <br><br><b>{{$data_unidad->dacademico}}.</b>
                <br>{{$data_unidad->pdacademico}}.
                <br>Presente.<br>
                <br><p class="text-justify">Por este medio hago constar que el (la) {{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}} fue entrevistado (a) y evaluado (a) por el Departamento Tecnico de esta Unidad de Capacitación a mi cargo, concluyendo que la persona es idónea para otorgar cursos de capacitación dentro de la Especialidad de {{$altmer->especialidad}}, así mismo cabe señalar que cumple con todos y cada uno de los requisitos establecidos en el manual de procedimientos del departamento de Gestión Académica vigente de la Dirección Técnica Académica del Instituto.</p>
                <br><p class="text-justify">Mucho agradeceré que el (la) C. {{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}} sea integrado al "Padrón de Instructores de Capacitación para y en el Trabajo".</p>
                <br><p class="text-justify">Sin otro particular, se emite la presente Constancia de Selección, a los {{$fecha_letra}} días del mes de {{$M}} del {{$Y}}, en la Ciudad de {{$data[0]->unidad_solicita}}, Chiapas.</p>
                <br><p class="text-left"><p>Atentamente.</p></p>
                <table class="table1">
                    <tr>
                        <td colspan="2"><p align="center">Elaboró</p></td>
                        <td colspan="2"><p align="center">Atentamente</p></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center">{{$solicito->name}}.</td></div>
                        <td colspan="2"><div align="center">{{$data_unidad->dunidad}}.</td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center">{{$solicito->puesto}} DE LA UNIDAD DE CAPACITACIÓN DE {{$data[0]->unidad_solicita}}.</td></div>
                        <td colspan="2"><div align="center">{{$data_unidad->pdunidad}} DE {{$data[0]->unidad_solicita}}.</td>
                    </tr>
                </table>
                <h6><small><b>Archivo/Minutario<b></small></h6>
                <small><small><b>Valido: {{$data_unidad->dunidad}}.- {{$data_unidad->pdunidad}} DE {{$data[0]->unidad_solicita}}.</b></small></small>
                <br><small><small><b>Elaboró: {{$solicito->name}}.- {{$solicito->puesto}}.</b></small></small>
            @endforeach
    </body>
</html>
