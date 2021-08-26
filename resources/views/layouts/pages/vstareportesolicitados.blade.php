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
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>INSTRUCTOR</td>
                        <td>UNIDAD</td>
                        <td>CURSO</td>
                        <td>FECHA DE INICIO</td>
                        <td>FECHA DE TERMINO</td>
                        <td>DIAS</td>
                        <td>HORA INICIO</td>
                        <td>HORA TERMINO</td>

                    </tr>
                    @isset($consulta)
                    @foreach ($consulta as $item)
                    <tr>
                        <td>{{$item->nombre}}</td>
                        <td>{{$item->unidad}}</td>
                        <td>{{$item->curso}}</td>
                        <td>{{$item->inicio}}</td>
                        <td>{{$item->termino}}</td>
                        <td>{{$item->dia}}</td>
                        <td>{{$item->hini}}</td>
                        <td>{{$item->hfin}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8">
                            {{$consulta->appends(request()->query())->links()}}
                        </td>
                    </tr>
                    @endisset
                </table>
            </div>
        </div>
    </div>
@endsection
