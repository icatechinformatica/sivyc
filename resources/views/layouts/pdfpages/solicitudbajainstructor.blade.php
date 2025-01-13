
@extends('theme.formatos.vlayout2025')
@section('title', 'SOLICITUD DE BAJA DE INSTRUCTOR | SIVyC Icatech')
@section('content_script_css')
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            .ftr{
                position: fixed;
                top: 85%;
                bottom: 0;
                left: 0;
                height: 60px;
            }
            th, td {
            border-style:solid;
            border-color: black;
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
@endsection
@section('content')
        <div>&nbsp;
            <div align=right> <b>Unidad de Capacitación {{$data_unidad->ubicacion}}</b> </div>
            <div align=right> <b>Memorandum No. {{$especialidades[0]->memorandum_solicitud}}</b></div>
            <div align=right> <b>{{$data_unidad->municipio}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

            <br><br><b>{{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }}.</b>
            <br>{{ $funcionarios['dacademico']['puesto'] }}.
            <br>Presente.<br>

            <br><p class="text-justify">Por medio de la presente, me dirijo a usted para solicitar la baja operativa del instructor externo de la unidad {{$data_unidad->ubicacion}} que a continuación se menciona:</p>
            <div class="table table-responsive">
                <table class="tablad" style="border-color: black">
                    <thead>
                        <tr>
                            <th style="border-color: black; width: 110px;">INSTRUCTOR</th>
                            <th style="border-color: black; width: 150px;">NO. MEMORANDUM</th>
                            <th style="border-color: black; width: 310px;">ESPECIALIDAD</th>720
                            <th style="border-color: black; width: 150px">MOTIVO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @php foreach ($especialidades AS $cc => $watt){} $cc++; @endphp
                            <td rowspan="{{$cc}}"><small>{{$instructor->apellidoPaterno}} {{$instructor->apellidoMaterno}} {{$instructor->nombre}}</small></td>
                        @foreach($especialidades AS $key => $cold)
                            @if($key != 0)
                                <tr>
                            @endif
                                @php if(isset($cold->hvalidacion)){$lastkey = array_key_last($cold->hvalidacion);};@endphp
                                <td><small>
                                    @if(isset($cold->hvalidacion))
                                        @if(isset($cold->hvalidacion[$lastkey]['memo_val']))
                                            {{$cold->hvalidacion[$lastkey]['memo_val']}}
                                        @else
                                            {{$cold->hvalidacion[$lastkey]['memo_baja']}}
                                        @endif
                                    @else
                                        {{$cold->memorandum_validacion}}
                                    @endif
                                </small></td>

                                <td><small>{{$cold->especialidad}}</small></td>
                                <td><small>{{$instructor->motivo}}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-left">Sin otro particular, aprovecho la ocasión para saludarlo.</p>
            <br><p class="text-left"><p>Atentamente.</p></p>
            <br><br><br><br><b>{{ $funcionarios['dunidad']['titulo'] }} {{ $funcionarios['dunidad']['nombre'] }}.</b>
            <br><b>{{ $funcionarios['dunidad']['puesto'] }}.
            <br><br><h6><small><b>C.c.p. {{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }}.- {{ $funcionarios['gestionacademica']['puesto'] }}.-  Para su conocimiento</b></small></h6>
            <h6><small><b>Archivo<b></small></h6>
            <small><small><b>Elaboró y Validó: {{ $funcionarios['dacademico_unidad']['titulo'] }} {{ $funcionarios['dacademico_unidad']['nombre'] }}.- {{ $funcionarios['dacademico_unidad']['puesto'] }}.</b></small></small>
        </div>
        @endsection
        @section('script_content_js')
        @endsection
