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
                    <th scope="col">No. Contrato</th>
                    <th scope="col">Lugar de Expedicion</th>
                    <th scope="col">Fecha de Firma</th>
                    <th scope="col">Municipio</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataCont as $itemData)
                    <tr>
                        <th scope="row">{{$itemData->numero_contrato}}</th>
                        <td>{{$itemData->lugar_expedicion}}</td>
                        <td>{{$itemData->fecha_firma}}</td>
                        <td>{{$itemData->municipio}}</td>
                        <td><a class="btn btn-success" href="">Validar</a></td>
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
