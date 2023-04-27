
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
                padding: 5px;
                /* border: 1px solid red; */

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
            top: -10px;
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
                width: 100%;
                height: 80px;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 90px;
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
        .tablad { border-collapse: collapse;font-size: 10px;border: black 1px solid; text-align: center; padding:0.5px; }
        /* .tablad tr {page-break-after: always; page-break-before: always;} */
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}
        .tablag tr td{ font-size: 8px; padding: 1px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
        .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 15px;
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
            <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        </footer>
        <div class= "container">
            <div align=right> <b>Unidad de Capacitación {{$daesp}}</b> </div>
            <div align=right> <b>Memorandum No. @if($nomemosol != null){{$nomemosol}} @else BORRADOR @endif</b></div>
            <div align=right> <b>{{$data_unidad->municipio}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

            <br><b>{{$data_unidad->dacademico}}.</b>
            <br>{{$data_unidad->pdacademico}}.
            <br>Presente.<br>

            <br><p class="text-justify">Por este medio solicito la <b> @if($tipo_doc == 'REVALIDACION') actualización @elseif($tipo_doc == 'REACTIVACION') reactivación @else validación @endif  </b> como Instructor Externo, en función a que cumple con todos y cada uno de los requisitos establecidos en el manual de procedimientos del departamento de Gestión Académica.</p>
            {{-- <div class="table table-responsive"> --}}
                <table class="tablad">
                    <thead>
                        <tr>
                            <th style="border-color: black; width: 90px;">INSTRUCTOR</th>
                            <th style="border-color: black; width: 100px;">ESPECIALIDAD</th>
                            <th style="border-color: black">CURSOS A IMPARTIR</th>
                            <th style="border-color: black; width: 120px">OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 30; @endphp
                        @foreach($cursos AS $key => $cold)
                            <tr>
                                <td><small>{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</small></td>
                                <td><small>{{$data[$key]->especialidad}}</small></td>
                                <td style="text-align:left; padding:5px;">
                                @if($data[$key]->status == 'BAJA EN FIRMA' || $data[$key]->status == 'BAJA EN PREVALIDACION')<small>BAJA EN TODOS LOS CURSOS DE LA ESPECIALIDAD</small></td>
                                @else
                                    @foreach($cold as $moist => $cadwell)
                                        <ul style="margin-left: -30px; line-height:80%;">
                                            <small><li style="text-align:left; margin: -1; padding: -0.2em;">{{$cadwell->nombre_curso}}.</small></li>
                                        </ul>
                                        @if($moist == $i)
                                            </td>
                                            <td><small>{{$data[$key]->observacion}}</small></td>
                                            </tr>
                                            </tbody>
                                            </table>
                                            <div class="page-break"></div>
                                            <table class="tablad">
                                                <thead>
                                                    <tr>
                                                        <th style="border-color: black; width: 90px;">INSTRUCTOR</th>
                                                        <th style="border-color: black; width: 100px;">ESPECIALIDAD</th>
                                                        <th style="border-color: black">CURSOS A IMPARTIR</th>
                                                        <th style="border-color: black; width: 120px">OBSERVACIONES</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                            <tr>
                                                <td><small>{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</small></td>
                                                <td><small>{{$data[$key]->especialidad}}</small></td>
                                                <td style="text-align:left; padding:5px;">
                                        @php $i = $i+39; @endphp
                                        @endif
                                    @endforeach
                                @endif
                                </td>
                                {{-- @if($data[$key]->status == 'BAJA EN FIRMA')
                                <td><small>BAJA EN TODOS LOS CURSOS DE LA ESPECIALIDAD</small></td>
                                @endif --}}
                                <td><small>@if($data[$key]->status != 'BAJA EN FIRMA' && $data[$key]->status != 'BAJA EN PREVALIDACION'){{$data[$key]->observacion}} @else BAJA DE ESPECIALIDAD @endif</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            {{-- </div> --}}
            <small><b>"La documentación presentada por esta Unidad de Capacitación ha sido previamente cotejada con el ejemplar original".</b></small>
            <br><p class="text-left">Sin más por el momento le envío un cordial saludo.</p>
            <br><p class="text-left"><p>Atentamente.</p></p>
            <b>{{$data_unidad->dunidad}}.</b>
            <br><b>{{$data_unidad->pdunidad}} DE {{$data_unidad->unidad}}.
            <br><h6><small><b>C.c.p. {{$data_unidad->jcyc}}.- {{$data_unidad->pjcyc}}.-Mismo Fin</b></small></h6>
            <h6><small><b>Archivo/Minutario<b></small></h6>
            <small><small><b>Valido: {{$data_unidad->dunidad}}.- {{$data_unidad->pdunidad}} DE {{$data_unidad->unidad}}.</b></small></small>
            <br><small><small><b>Elaboró: {{$solicito->name}}.- {{$solicito->puesto}}.</b></small></small>
            @foreach ($data as $altmer)
                @if($altmer->status != 'BAJA EN FIRMA' && $altmer->status!= 'BAJA EN PREVALIDACION')
                    <div class="page-break"></div>
                    <div align=right> <b>{{$data_unidad->municipio}}, Chiapas; {{$D}} de {{$M}} del {{$Y}}.</b></div>
                    <br><br><b>{{$data_unidad->dacademico}}.</b>
                    <br>{{$data_unidad->pdacademico}}.
                    <br>Presente.<br>
                    <br><p class="text-justify">Por este medio hago constar que el (la) C. {{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}} fue entrevistado (a) y evaluado (a) por el Departamento Tecnico de esta Unidad de Capacitación a mi cargo, concluyendo que la persona es idónea para otorgar cursos de capacitación dentro de la Especialidad de {{$altmer->especialidad}}, así mismo cabe señalar que cumple con todos y cada uno de los requisitos establecidos en el manual de procedimientos del departamento de Gestión Académica vigente de la Dirección Técnica Académica del Instituto.</p>
                    <br><p class="text-justify">Mucho agradeceré que el (la) C. {{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}} sea integrado al "Padrón de Instructores de Capacitación para y en el Trabajo".</p>
                    <br><p class="text-justify">Sin otro particular, se emite la presente Constancia de Selección, a los {{$fecha_letra}} días del mes de {{$M}} del {{$Y}}, en la Ciudad de {{$data_unidad->municipio}}, Chiapas.</p>
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
                            <td colspan="2"><div align="center">{{$data_unidad->academico}}.</td></div>
                            <td colspan="2"><div align="center">{{$data_unidad->dunidad}}.</td></div>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="center">{{$data_unidad->pacademico}} DE LA UNIDAD DE CAPACITACIÓN DE {{$daesp}}.</td></div>
                            <td colspan="2"><div align="center">{{$data_unidad->pdunidad}} DE {{$data_unidad->unidad}}.</div></td>
                        </tr>
                    </table>
                    <h6><small><b>Archivo/Minutario<b></small></h6>
                    <small><small><b>Valido: {{$data_unidad->dunidad}}.- {{$data_unidad->pdunidad}} DE {{$data_unidad->unidad}}.</b></small></small>
                    <br><small><small><b>Elaboró: {{$solicito->name}}.- {{$solicito->puesto}}.</b></small></small>
                @endif
            @endforeach
        </div>
    </body>
</html>
