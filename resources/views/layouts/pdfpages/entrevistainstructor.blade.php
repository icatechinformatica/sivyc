@extends('theme.formatos.vlayout'.$layout_año)
@section('title', 'ENTREVISTA INSTRUCTOR| SIVyC Icatech')
@section('content_script_css')
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            /* *.{border: 2px solid red;} */
            th, td {
            border-style:solid;
            border-color: black;
            }
            /* div.content
            {
                margin-bottom: 750%;
                margin-right: -25%;
                margin-left: 0%;
            } */
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
        header {left: 25px;}

        </style>
@endsection
@section('content')
        <div>
            <div style="text-align: center;"><b>Formato de Entrevista para Candidatos a Instructores</b></div>
            <p style="margin-right:-250px; text-align:right;"><b>{{$D}} de {{$M}} del {{$Y}}</b></p>
            <b>Nombre del entrevistado: {{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</b>
            <br><b>Unidad de capacitación: {{$userunidad->ubicacion}}</b>
            <div class="table table-responsive">
                <table class="tablad" style="border-color: black">
                    <thead>
                        <tr>
                            <td width="10%"><b>PREGUNTA</b></td>
                            <td width="10%"><b>RESPUESTA</b></td>
                        </tr>
                    </thead>
                    <tbody style="font-size:8;">
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
            <div align=center style="-10px;"><b>DECLARO BAJO PROTESTA DE DECIR VERDAD QUE LOS DATOS AQUÍ ASENTADOS SON CIERTOS</b></div>
            <table class="table1" style="font-size:8; text-align:center; margin-top: -10px;">
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
                    <td colspan="2"><b>_________________________________________________</b></td>
                    <td colspan="2"><b>_________________________________________________</b></td>
                </tr>
                <tr>
                    <td colspan="2"><div align="center">{{$data->apellidoPaterno}} {{$data->apellidoMaterno}} {{$data->nombre}}</td></div>
                    <td colspan="2"><div align="center">{{$funcionarios['elabora']['nombre']}}</td></div>
                </tr>
                <tr>
                    <td colspan="2"><div align="center">ENTREVISTADO</td>
                    <td colspan="2"><div align="center">{{$funcionarios['elabora']['puesto']}}</td></div>
                </tr>
            </table>
        </div>
        @endsection
        @section('script_content_js')
        @endsection
