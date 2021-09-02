@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}

    </style>
    <div class="card-header">
        Consulta de Solicitados Mediante Suficiencias Presupuestales

    </div>
    <div class="card card-body" >
        <br />
        <form action="{{route('reporte-solicitados')}}" method="GET" id="cacahuate">
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputccp"><h3>Filtrar Por Fechas</h3></label>
                    </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <input type="date" name="fecha_inicio" class="form-control" id="fecha_inicio" placeholder="Fecha inicio">
                </div>
                <div class="form-group col-md-2">
                    <input type="date" name="fecha_termino" class="form-control" id="fecha_termino" placeholder="Fecha termino">
                </div>
                <div class="form-group col-md-1">
                    <input type="submit" value="BUSCAR" class="btn btn-green">
                </div>
            </div>
            {{csrf_field()}}
        </form>
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover fixed_header">
                    <thead>
                        <tr>
                            <th rowspan="3" style="text-align: center;"><br><br>UNIDADES</th>
                            <th colspan="3" align="center">SUFICIENCIAS PRESUPUESTALES</th>
                            <th colspan="3" align="center">CONTRATOS</th>
                            <th colspan="4" align="center">PAGOS</th>
                        </tr>
                        <tr>
                            <th rowspan="2" align="center"><br>Solicitado</th>
                            <th colspan="2" align="center">Validación</th>
                            <th rowspan="2" align="center"><br>Solicitado</th>
                            <th colspan="2" align="center">Validación</th>
                            <th rowspan="2" align="center"><br>Solicitado</th>
                            <th colspan="3" align="center">Validación</th>
                        </tr>
                        <tr>
                            <th align="center">Aceptado</th>
                            <th align="center">Rechazado</th>
                            <th align="center">Aceptado</th>
                            <th align="center">Rechazado</th>
                            <th align="center">Aceptado</th>
                            <th align="center">Finalizado</th>
                            <th align="center">Rechazado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($consulta1 != NULL)
                            @if($consulta2 != NULL)
                                @foreach ($unidades as $unidad)
                                    <?php $findit = FALSE; ?>
                                    @foreach($consulta1 as $key => $supre)
                                        @if ($unidad->unidad == $supre->unidad)
                                            <?php
                                                $findit = TRUE; $key2 = $key + 1;
                                                $dif = round($supre->supre_validados * 100 / $supre->supre_total, 2);
                                                if($dif == 100)
                                                {
                                                    $color = '#c6efce';
                                                }
                                                else if($dif >= 50)
                                                {
                                                    $color = '#ffeb9c';
                                                }
                                                else
                                                {
                                                    $color = '#ffc7ce';
                                                }
                                            ?>
                                            <tr bgcolor= {{$color}}>

                                                <td align="center" >{{$supre->unidad}}</td>
                                                <td align="center" >{{$supre->supre_proceso}}</td>
                                                <td align="center" >{{$supre->supre_validados}}</td>
                                                <td align="center" >{{$supre->supre_rechazados}}</td>
                                                <td align="center" >{{$consulta2[$key]->contrato_proceso}}</td>
                                                <td align="center" >{{$consulta2[$key]->contrato_validados}}</td>
                                                <td align="center" >{{$consulta2[$key]->contrato_rechazados}}</td>
                                                <td align="center" >{{$consulta2[$key]->pago_proceso}}</td>
                                                <td align="center" >{{$consulta2[$key]->pago_validados}}</td>
                                                <td align="center" >{{$consulta2[$key]->pago_finalizados}}</td>
                                                <td align="center" >{{$consulta2[$key]->pago_rechazados}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if($findit == FALSE)
                                        <tr>
                                            <td align="center">{{$unidad->unidad}}</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                            <td align="center">0</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
