<!--creado por Daniel Méndez -->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Financieros | SIVyC Icatech')
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
                    <h2>Convenios Financieros</h2>
                </div>
                <br>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered Datatables">
            <caption>Catalogo de Suficiencias Presupuestales Validadas</caption>
            <thead>
                <tr>
                    <th scope="col">No. de Memorandum</th>
                    <th scope="col">Unidad de Capacitación</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Estatus</th>
                    <th scope="col">Folio de Validación</th>
                    <th scope="col">Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataSupre as $itemData)
                    <tr>
                        <th scope="row">{{$itemData->no_memo}}</th>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->fecha}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>{{$itemData->folio_validacion}}</td>
                        <td>
                            @if ($itemData->status == 'verificando_Pago')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Autorización de Pago</a>
                                    <a class="btn btn-danger" href="{{$itemData->docs}}" target="_blank">Sol. Pago PDF</a>
                            @endif
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
@endsection
<!--seccion-->
