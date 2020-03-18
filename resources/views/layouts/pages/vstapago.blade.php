@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Pagos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <style>
        * {
        box-sizing: border-box;
        }

        #myInput {
        background-image: url('img/search.png');
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        font-size: 16px;
        padding: 12px 20px 12px 40px;
        border: 1px solid #ddd;
        margin-bottom: 12px;
        }
    </style>
    <div class="container g-pt-50">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Registro de Pagos</h2>
                </div>
            </div>
        </div>
        <div class="pull-left">
        </div>
        <hr style="border-color:dimgray">
        <br>
        <h2>Solicitudes de Pago</h2>
        <table  id="table-instructor" class="table table-bordered">
            <caption>Lista de Contratos en Espera</caption>
            <thead>
                <tr>
                    <th scope="col">N°. Contrato</th>
                    <th scope="col">N°. de Circular</th>
                    <th scope="col">Unidad de Capacitación</th>
                    <th scope="col">Estado</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contratos_folios as $itemData)
                    <tr>
                        <td>{{$itemData->numero_contrato}}</td>
                        <td>{{$itemData->numero_circular}}</td>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>
                            @switch($itemData->status)
                                @case('Verificando_Pago')
                                <a class="btn btn-primary" href="{{route('pago.verificando', ['idfolio' => $itemData->id_folios])}}">Verificar</a>
                                    @break
                                @case('Pago_Verificado')
                                <a class="btn btn-success" href="{{route('alumnos.inscripcion-paso1')}}">Verificar Pago</a>
                                    @break
                                @case('Finalizado')
                                <a class="btn btn-danger" href="{{route('alumnos.inscripcion-paso1')}}">Finalizar</a>
                                    @break
                            @endswitch
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                </tr>
            </tfoot>
        </table>
        <br>
    </div>
    <br>
@endsection
