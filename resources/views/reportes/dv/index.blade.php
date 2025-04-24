<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        thead { position: sticky; top: 0; z-index: 10; background-color: #ffffff; }
        .table-responsive { height:550px; overflow:scroll;  width: 100%}

        .table tr th{ background-color: #ccc;  border: 1px solid #fff; text-align: center; font-size: 10px; margin:0px; padding:3px; font-weight:bold; vertical-align: middle;}
        .table tr td{ font-size: 11px; margin:0px; padding:0px; text-align: center;}
        .tb_texto{ font-size:10px;}
    </style>
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Reportes | DV - Operación con | SIVyC Icatech')
@section('content')
    <div class="card-header">
        Reportes / DV - Operación con Convenios Generales Vigentes
    </div>
    <div class="card card-body">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif
        {{ Form::open(['method' => 'post', 'id'=>'frm',  'enctype' => 'multipart/form-data']) }}
            @csrf
            <div class="row form-inline">
                {{ Form::date('fecha1', $fecha1 ?? '' , ['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA INICIAL', 'title' => 'FECHA INICIAL', 'required' => 'required']) }}
                {{ Form::date('fecha2', $fecha2 ?? '', ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA FINAL', 'title' => 'FECHA FINAL', 'required' => 'required']) }}
                {{ Form::button('FILTRAR', ['class' => 'btn', 'onclick' => "filtrar('FILTRAR')"]) }}
                {{ Form::button('XLS', ['class' => 'btn', 'onclick' => "filtrar('XLS')"]) }}
            </div>
            {{ Form::hidden('opcion', null, ['id'=>'opcion']) }}
        {!! Form::close() !!}
                <div class="table-responsive p-0">
                    <table class="table table-hover p-0 table-bordered">
                    @if(count($data)>0)
                        <thead>
                            <tr>
                                <th rowspan="2"><p style="width: 80px;">No. Convenio</p></th>
                                <th rowspan="2"><p style="width: 150px;">Institución</p></th>
                                <th rowspan="2"><p>Tipo</p></th>
                                <th rowspan="2"><p style="width: 60px;">Inicio de Vigencia</p></th>
                                <th rowspan="2"><p style="width: 60px;">Termino de Vigencia</p></th>
                                <th rowspan="2"><p style="width: 100px;">Unidad</p></th>

                                @foreach ($anios as $anio)
                                    <th rowspan="2">Total Cursos {{$anio}}</th>
                                    <th colspan="3">Egresados Acumulados {{$anio}}</th>
                                @endforeach

                                @foreach ($meses as $mes)
                                @php
                                    $partes = explode('-', $mes);
                                    $res_anio = $partes[0];
                                    $res_mes = $array_meses[$partes[1]];
                                @endphp
                                    <th colspan="5">Mes de {{$res_mes.' '.$res_anio}}</th>
                                @endforeach
                            </tr>

                            <tr>
                                @foreach ($anios as $anio)
                                    <th>M</th>
                                    <th>H</th>
                                    <th>Total</th>
                                @endforeach

                                @foreach ($meses as $mes)
                                    <th>No.Cursos</th>
                                    <th>M</th>
                                    <th>H</th>
                                    <th>Total</th>
                                    <th><p style="width: 300px;">Cursos</p></th>
                                @endforeach
                            </tr>

                        </thead>
                            <tbody>
                                @foreach($data as $item)
                                    <tr>
                                        <td class="tb_texto">{{ $item->no_convenio }}</td>
                                        <td class="tb_texto">{{ $item->institucion}}</td>
                                        <td class="tb_texto">{{ $item->tipo_sector}}</td>
                                        <td class="tb_texto">{{ $item->fecha_firma}}</td>
                                        <td class="tb_texto">{{ $item->fecha_vigencia}}</td>
                                        <td class="tb_texto">{{ $item->unidad}}</td>

                                        {{-- anio --}}
                                        @foreach ($anios as $anio)
                                            <td class="tb_texto">{{ $item->{'total_cursos_' . $anio} }}</td>
                                            <td class="tb_texto">{{ $item->{'total_mujeres_' . $anio} }}</td>
                                            <td class="tb_texto">{{ $item->{'total_hombres_' . $anio} }}</td>
                                            <td class="tb_texto">{{ $item->{'total_alumnos_' . $anio} }}</td>
                                        @endforeach

                                        {{-- meses del anio en rango --}}
                                        {{-- Enero --}}
                                        @foreach ($meses as $mes)
                                        @php $mes_alias = str_replace('-', '_', $mes); @endphp
                                            <td class="tb_texto">{{ $item->{'total_cursos_' . $mes_alias} }}</td>
                                            <td class="tb_texto">{{ $item->{'total_mujeres_'. $mes_alias} }}</td>
                                            <td class="tb_texto">{{ $item->{'total_hombres_'. $mes_alias} }}</td>
                                            <td class="tb_texto">{{ $item->{'total_alumnos_'. $mes_alias} }}</td>
                                            <td class="tb_texto">{{ $item->{'cursos_'. $mes_alias} }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='13'>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>


    </div>
    @section('script_content_js')
        <script>
             function filtrar(opt) {
                $('#opcion').val(opt)
                $('#frm').attr('action', "{{route('reportes.dv.generar')}}");
                if(opt=="FILTRAR")$('#frm').attr('target', '_self');
                else $('#frm').attr('target', '_blank');
                $('#frm').submit();
             }
        </script>
    @endsection
@endsection
