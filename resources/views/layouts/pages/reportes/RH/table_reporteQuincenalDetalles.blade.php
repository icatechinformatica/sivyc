{{-- tabla_resultados.blade.php --}}
<caption>Tarjeta de Tiempo</caption>
@php $id_registro = null; @endphp
    <thead>
        <tr>
            <th scope="col" width="110px">FECHA</th>
            <th scope="col" width="110px">DÍA</th>
            <th scope="col" width="250px">ENTRADA-SALIDA</th>
            <th width="80px">ACCION</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dates as $key => $date)
            <tr>
                @php $fecha_registro = $date; @endphp
                <td style="text-align: center;">{{$date}}</td>
                <td>{{$days[$key]}}</td>
                <td>
                    @if (in_array($days[$key], ['SÁBADO','DOMINGO']) && !in_array($data[0]->horario_checador, ['4','5']))  {{-- Se verifica si la fecha es sabado o domingo y si no es velador. los veladores trabajan fin de semana tambien --}}
                        <b>{{$days[$key]}}</b>
                    @else {{-- Aqui entra cuando es entresemana --}}
                        @php $falta = true @endphp {{-- Checador de inasistencia por falta --}}
                        @foreach($data as $registro) {{-- foreach de los registros del usuario --}}
                            @if($registro->fecha2 == $date)
                                @if(collect($dias_inhabiles)->contains('fecha', $date) && !in_array($data[0]->horario_checador, ['4','5'])) {{-- Verifica si hay dias inabiles en el rango de fechas y que no sea velador. los veladores trabajan en dias inhabiles --}}
                                    {{$dias_inhabiles[array_search($date, array_column($dias_inhabiles, 'fecha'))]->numero_memorandum}}
                                @elseif(!$registro->justificante)
                                    @if(!is_null($registro->entrada))
                                        @if($registro->entrada > '08:31:00') {{-- revisa si el checado es despues de l ahora correcta--}}
                                            <b>INASISTENCIA</b>
                                        @else
                                            @if($registro->retardo) <b> R @endif
                                            {{$registro->entrada}} </b>
                                        @endif
                                    @else
                                        <b>OMISIÓN DE ENTRADA</b>
                                    @endif
                                    @if(!is_null($registro->salida))
                                        @if($registro->salida < '16:00:00' && in_array($data[0]->horario_checador, ['1','3'])) @else - {{$registro->salida}} @endif
                                    @elseif(!in_array($data[0]->horario_checador, ['4','5'])) - <b>OMISIÓN DE SALIDA</b> @endif
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
                <td>
                    @if (!in_array($days[$key], ['SÁBADO','DOMINGO']) || in_array($data[0]->horario_checador, ['4','5']))
                        <a data-toggle="modal" data-placement="top" data-target="#JustificanteModal" data-id='["{{$id_registro}}", "{{$fecha_registro}}", "{{$numero_enlace}}"]'>
                            <i class="fa fa-edit fa-2x fa-lg text-success" title="Agregar Justificante"></i>
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
