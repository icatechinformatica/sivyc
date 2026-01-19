@extends('theme.formatos.vlayout'.$layout_año)
@section('title', 'VALIDACIÓN DE BAJA DE INSTRUCTOR | SIVyC Icatech')
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
    </head>
    @endsection
    @section('content')
        <div>
            <div align=right> <b>Dirección Técnica Académica</b> </div>
            <div align=right> <b>Memorandum No. {{$especialidades[0]->memorandum_baja}}</b></div>
            <div align=right> <b>Tuxtla Gutiérrez, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

            <br><br><b>{{ $funcionarios['dunidad']['titulo'] }} {{ $funcionarios['dunidad']['nombre'] }}.</b>
            <br>{{ $funcionarios['dunidad']['puesto'] }}
            <br>Presente.<br>

            <br><p class="text-justify">En relación a la solicitud de baja en el Registro del Padrón de Instructores mediante memorándum No. <b>{{$especialidades[0]->memorandum_solicitud}}</b> de fecha <b>{{$DS}} DE {{$MS}} DEL {{$YS}}</b>, me permito informarle que, a petición de la Unidad, procedió la baja del siguiente instructor por las causas que se describen a continuación:</p>
            <div class="table table-responsive">
                <table class="tablad" style="border-color: black">
                    <thead>
                        <tr>
                            <th style="border-color: black; width: 10%;">INSTRUCTOR</th>
                            <th style="border-color: black; width: 10%;">NO. MEMORANDUM</th>
                            <th style="border-color: black; width: 10%;">ESPECIALIDAD</th>720
                            <th style="border-color: black; width: 10%">MOTIVO</th>
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
                                @php $lastkey = array_key_last($cold->hvalidacion); if($lastkey != 0){$lastkey = $lastkey - 1;}@endphp
                                <td><small>
                                    @if(isset($cold->hvalidacion[$lastkey]['memo_val']))
                                        {{$cold->hvalidacion[$lastkey]['memo_val']}}
                                    @elseif(isset($cold->hvalidacion[$lastkey]['memo_baja']))
                                        {{$cold->hvalidacion[$lastkey]['memo_baja']}}
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
            <br><br><br><br><b>{{ $funcionarios['dacademico']['titulo'] }} {{ $funcionarios['dacademico']['nombre'] }}</b>
            <br><b>{{ $funcionarios['dacademico']['puesto'] }}.
            <br><br><small><b>C.c.p. {{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }}.- {{ $funcionarios['gestionacademica']['puesto'] }}.-  Para su conocimiento.</b></small>
            <br><small><b>C.c.p. {{ $funcionarios['dacademico_unidad']['titulo'] }} {{ $funcionarios['dacademico_unidad']['nombre'] }}.- {{ $funcionarios['dacademico_unidad']['puesto'] }}.-  Mismo fin.</b></small>
            <br><small><b>Archivo<b></small>
            <br><small><small><b>Validó: {{ $funcionarios['gestionacademica']['titulo'] }} {{ $funcionarios['gestionacademica']['nombre'] }}.- {{ $funcionarios['gestionacademica']['puesto'] }}.</b></small></small>
            <br><small><small><b>Elaboró: C. {{ $funcionarios['elabora']['nombre'] }}.- {{ $funcionarios['elabora']['puesto'] }}.</b></small></small>
        </div>
        @endsection
        @section('script_content_js')
        @endsection
