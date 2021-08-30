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
                    <!--isset(consulta1)
                    foreach (consulta1 as key => item)
                    <tr>
                        <td>{item->nombre}}</td>
                        <td>{item->unidad}}</td>
                        <td>{item->curso}}</td>
                        <td>{item->inicio}}</td>
                        <td>{item->termino}}</td>
                        <td>{item->dia}}</td>
                        <td>{item->hini}}</td>
                        <td>{item->hfin}}</td>
                    </tr>
                    endforeach
                    endisset-->
                </table>
            </div>
        </div>
    </div>
@endsection
