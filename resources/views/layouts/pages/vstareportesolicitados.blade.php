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
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <td rowspan="3" style="text-align: center;"><br><br>UNIDADES</td>
                        <td colspan="3" align="center">SUFICIENCIAS PRESUPUESTALES</td>
                        <td colspan="3" align="center">CONTRATOS</td>
                        <td colspan="4" align="center">PAGOS</td>
                    </tr>
                    <tr>
                        <td rowspan="2" align="center"><br>Solicitado</td>
                        <td colspan="2" align="center">Validación</td>
                        <td rowspan="2" align="center"><br>Solicitado</td>
                        <td colspan="2" align="center">Validación</td>
                        <td rowspan="2" align="center"><br>Solicitado</td>
                        <td colspan="3" align="center">Validación</td>
                    </tr>
                    <tr>
                        <td align="center">Aceptado</td>
                        <td align="center">Rechazado</td>
                        <td align="center">Aceptado</td>
                        <td align="center">Rechazado</td>
                        <td align="center">Aceptado</td>
                        <td align="center">Finalizado</td>
                        <td align="center">Rechazado</td>
                    </tr>
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
                </table>
            </div>
        </div>
    </div>
@endsection
