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
                <td>
                    @if (!in_array($days[$key], ['SÁBADO','DOMINGO']))
                        <a data-toggle="modal" data-placement="top" data-target="#JustificanteModal" data-id='["{{$id_registro}}", "{{$fecha_registro}}", "{{$numero_enlace}}"]'>
                            <i class="fa fa-edit fa-2x fa-lg text-success" title="Agregar Justificante"></i>
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
