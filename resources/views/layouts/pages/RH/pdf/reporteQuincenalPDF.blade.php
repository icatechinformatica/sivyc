@extends('theme.formatos.vlayout2025')
@section('title', 'Tarjeta de Tiempo | SIVyC Icatech')
@section('content_script_css')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-wfSDFE50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            /* div.content
            {
                margin-bottom: 750%;
                margin-right: 0%;
                margin-left: 0%;
            } */
            .landscape {
                page: landscape;
                size: landscape;
            }
            .page-break {
                page-break-after: always;
            }
            .page-break-non {
                page-break-after: avoid;
            }
            .contenedor {
                position:RELATIVE;
                top:-50px;
                width:100%;
                margin:auto;
                font-size: 10px;
            }
            .table1, .table1 td {
                border:0px ;
                text-align: center;
            }
            .table1 td {
                padding:5px;
            }
            .content {
                margin-bottom: -50px;
            }
            body {
                margin-bottom: -10px;
            }
        </style>
@endsection
@section('content')
@php $months = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE']; $id_registro = null; @endphp
    <div class= "contenedor">
        <p style="text-align: center; line-height: 1; margin-bottom: -5px;">Dirección Administrativa <br> Departamento de Recursos Humanos</p>
        <div style=" margin-bottom: -20px;">
            <div style="display: inline-block; width: 49%; text-align: left;">
                <b>TARJETA DE TIEMPO</b>
            </div>
            <div style="display: inline-block; width: 49%; text-align:right;">
                <b>{{$hoy}}</b>
            </div>
        </div>
        <hr style="border-color:dimgray; margin-bottom: 10px;">
        <div style=" margin: 0% 2% -2% 2%;">
            <div style="width: 49%; display: inline-block;">
                <p>Número: <b>{{$numero_enlace}}</b></p>
                <p style="line-height: 0;">Empleado: <b>{{$data[0]->nombre_trabajador}}</b></p>
            </div>
            <div style="text-align: right; width: 49%; display: inline-block;">
                <p style="text-align: right;">Grupo: <b>OFICINAS ADMINISTRATIVAS</b></p>
                {{-- <p style="text-align: right;">Grupo: <b>{{$data[0]->nombre_adscripcion}}</b></p> --}}
            </div>
        </div>
        <br>
        <p style="text-align: center;">Hago constar que la presente Tarjeta de Tiempo ha sido marcada personalmente por mi en las horas de entrada y salidas mostradas, y responde al registro de mi asistencia durante el periodo señalado.</p>
        <div style="margin-bottom: -35px; margin-top: 0px;">
            <div style="width: 38%; display: inline-block;">
                <p>Fechas del Periodo:</p>
            </div>
            <div style="width: 49%; display: inline-block; text-align: center;">
                <p>{{$days[0]}} {{$dates[0]}} - {{end($days)}} {{end($dates)}}</p>
            </div>
        </div>
        <hr style="border-color:dimgray; margin-bottom: -15px;">
        <table id="tablaResultados" class="table table-bordered" style='text-align:center; font-size:0.9em;'>
            @php $id_registro = null; @endphp
            <thead>
                <tr>
                    <th scope="col" width="110px">FECHA</th>
                    <th scope="col" width="110px">DÍA</th>
                    <th scope="col" width="250px">ENTRADA-SALIDA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dates as $key => $date)
                    <tr>
                        @php $fecha_registro = $date; @endphp
                        <td style="text-align: center;">{{$date}}</td>
                        <td>{{$days[$key]}}</td>
                        <td>
                            @if (in_array($days[$key], ['SÁBADO','DOMINGO']))  {{-- Se verifica si la fecha es sabado o domingo --}}
                                <b>{{$days[$key]}}</b>
                            @else {{-- Aqui entra cuando es entresemana --}}
                                @php $falta = true @endphp {{-- Checador de inasistencia por falta --}}
                                @foreach($data as $registro) {{-- foreach de los registros del usuario --}}
                                    @if($registro->fecha2 == $date)
                                        @if(collect($dias_inhabiles)->contains('fecha', $date))
                                            {{$dias_inhabiles[array_search($date, array_column($dias_inhabiles, 'fecha'))]->numero_memorandum}}
                                        @elseif(!$registro->justificante)
                                            @if(!is_null($registro->entrada)) {{$registro->entrada}} @else <b>INASISTENCIA</b> @endif -  @if(!is_null($registro->salida)) {{$registro->salida}} @else <b>INASISTENCIA</b> @endif
                                        @else
                                            INCAPACIDAD {{$registro->observaciones}}
                                        @endif
                                        @php $falta = false; $id_registro = $registro->id;@endphp
                                    @endif
                                @endforeach
                                @if($falta)
                                    @if(collect($dias_inhabiles)->contains('fecha', $date))
                                        {{$dias_inhabiles[array_search($date, array_column($dias_inhabiles, 'fecha'))]->numero_memorandum}}
                                    @else
                                        <b>INASISTENCIA</b>
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="width: 49%; display: inline-block; margin-top: -20px;">
            <table class="table1">
                <tr>
                    <td><p align="center"><b>Firma del supervisor</b></p></td>
                </tr>
                <tr>
                    <td><h3>_____________________</h3></td>
                </tr>
                <tr>
                    <td><div align="center"> C.P. Alberto de Jesús Pérez León</td></div>
                </tr>
            </table>
        </div>
        <div style="width: 48%; display: inline-block;">
            <table class="table1">
                <tr>
                    <td><p align="center"><b>Firma del empleado</b></p></td>
                </tr>
                <tr>
                    <td><h3>_____________________</h3></td>
                </tr>
                <tr>
                    <td><div align="center"> C. {{$data[0]->nombre_trabajador}}</td></div>
                </tr>
            </table>
        </div>
    </div>
@endsection
@section('script_content_js')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
@endsection
