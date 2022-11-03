
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
            <div align=center> <b>Formato de Entrevista para Candidatos a Instructores</b></div>
            <div align=right> <b>{{$D}} de {{$M}} del {{$Y}}</b></div>
            <br><b>Nombre del entrevistado: {{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</b>
            <br><b>Unidad de capacitación: {{$userunidad->ubicacion}}
            <br>
            <div class="table table-responsive">
                <table class="tablad" style="border-color: black">
                    <thead>
                        <tr>
                            <td width="360px"><b>PREGUNTA</b></td>
                            <td width="360px"><b>RESPUESTA</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>¿Conoce Usted a que se dedica el ICATECH o ha escuchado de él? Indique</td>
                            <td><small>{{$data->entrevista['1']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Qué lo motivó a impartir capacitación?</td>
                            <td><small>{{$data->entrevista['2']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Ha impartido cursos de Capacitación? Si, ¿Cuáles?</td>
                            <td><small>{{$data->entrevista['3']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Cómo se llamó el último curso que dio, en qué fecha lo dio y para quién lo otorgó? Indique si cuenta con documento que lo acredite</td>
                            <td><small>{{$data->entrevista['4']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Considera estar lo suficientemente actualizado o contar con dominio sobre la especialidad en la cual impartirá cursos de capacitación? ¿Por qué?</td>
                            <td><small>{{$data->entrevista['5']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Con qué frecuencia busca temas actuales sobre la especialidad en la cual imparte cursos de capacitación? ¿Y qué medios utiliza?</td>
                            <td><small>{{$data->entrevista['6']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Ha elaborado guías pedagógicas?</td>
                            <td><small>{{$data->entrevista['7']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Qué técnicas de Enseñanza- Aprendizaje utiliza con los alumnos? Describa.</td>
                            <td><small>{{$data->entrevista['8']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Cómo comprueba que los alumnos entienden lo que Usted les enseña?</td>
                            <td><small>{{$data->entrevista['9']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Estaría dispuesto a recibir capacitación acerca de la especialidad en la cual se desarrollará?</td>
                            <td><small>{{$data->entrevista['10']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Cómo definiría su personalidad frente a grupo?</td>
                            <td><small>{{$data->entrevista['11']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿A qué dedica la mayoría de su tiempo?</td>
                            <td><small>{{$data->entrevista['12']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Estaría dispuesto a viajar a cualquier parte del Estado en el momento en que se le indique? En caso negativo ¿Por qué?</td>
                            <td><small>{{$data->entrevista['13']}}</small></td>
                        </tr>
                        <tr>
                            <td>¿Cuenta Usted con recibos de Honorarios? ¿O en su caso, estaría dispuesto a tramitarlos ante el SAT?</td>
                            <td><small>{{$data->entrevista['14']}}</small></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div align=center><b>DECLARO BAJO PROTESTA DE DECIR VERDAD QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS</b></div>
            <br>
            <table class="table1">
                <tr>
                    <td colspan="2" width="360px"><p align="center"></p></td>
                    <td colspan="2" width="360px"><p align="center"></p></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">_________________________________________________</td>
                    <td colspan="2">_________________________________________________</td>
                </tr>
                <tr>
                    <td colspan="2"><div align="center">{{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</td></div>
                    <td colspan="2"><div align="center">{{$usernombre}}</td></div>
                </tr>
                <tr>
                    <td colspan="2"><div align="center">ENTREVISTADO</td>
                    <td colspan="2"><div align="center">{{$userpuesto}}</td></div>
                </tr>
            </table>
        </div>
    </body>
</html>
