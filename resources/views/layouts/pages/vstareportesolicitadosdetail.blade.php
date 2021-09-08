@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
        td a{
            display: block;
            box-sizing:border-box;
            height: 100%;
            width: 100%;
        }
        thead tr th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
        }
        .table-responsive {
            height:400px;
            overflow:auto;
        }
    </style>
    <div class="card-header">
        Consulta de Solicitados Mediante Suficiencias Presupuestales Unidad: {{$un}}

    </div>
    <div class="card card-body" >
        <br />
        <form action="{{route('reporte-solicitados')}}" method="GET" id="cacahuate">
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputccp"><h3>Filtrado Por Fechas</h3></label>
                    </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <h4>{{$ini}} AL {{$fin}}</h4>
                </div>
            </div>
            {{csrf_field()}}
        </form>
        <br>
        <div class="row justify-content-center">
            <H2>Rechazados</H2>
        </div>
        <div class="row">
            <div class="table-responsive" style="width:50%;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="text-align: center;">No. Memorándum</th>
                            <th style="text-align: center;">Fecha de Rechazo</th>
                            <th style="text-align: center;">Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consulta1->supre_memo_rechazo as $key => $rechazo)
                                <tr>
                                    <td align="center">{{$rechazo}}</td>
                                    @if($consulta1->supre_fecha_rechazo[$key] != 'NULL')
                                        <td align="center">{{$consulta1->supre_fecha_rechazo[$key]}}</td>
                                    @else
                                        <td align="center">{{$consulta1->supre_updated_rechazo[$key]}}</td>
                                    @endif
                                    @if($consulta1->supre_observaciones[$key] != 'NULL')
                                        <td align="center">{{$consulta1->supre_observaciones[$key]}}</td>
                                    @else
                                        <td align="center">N/A</td>
                                    @endif
                                </tr>
                        @endforeach
                        @foreach($consulta1->supre_memo_rechazo as $key => $rechazo)
                                <tr>
                                    <td align="center">{{$rechazo}}</td>
                                    @if($consulta1->supre_fecha_rechazo[$key] != 'NULL')
                                        <td align="center">{{$consulta1->supre_fecha_rechazo[$key]}}</td>
                                    @else
                                        <td align="center">{{$consulta1->supre_updated_rechazo[$key]}}</td>
                                    @endif
                                    @if($consulta1->supre_observaciones[$key] != 'NULL')
                                        <td align="center">{{$consulta1->supre_observaciones[$key]}}</td>
                                    @else
                                        <td align="center">N/A</td>
                                    @endif
                                </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="chart" class="table-responsive" style="width:50%;">
            </div>
        </div>
        <script src="https://unpkg.com/@chartisan/chartjs@^2.1.0/dist/chartisan_chartjs.umd.js"></script>
        <script src="https://unpkg.com/chart.js@2.9.3/dist/Chart.min.js"></script>
        <script>
                const chart = new Chartisan({
  el: '#chart',
  url: 'https://chartisan.dev/chart/example.json',
  hooks: new ChartisanHooks()
    .datasets('doughnut')
    .pieColors(),
})
            </script>
    </div>
@endsection
<!--a-->
