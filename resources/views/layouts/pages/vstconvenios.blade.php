<!--Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Convenios | SIVyC Icatech')
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
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Convenios</h2>
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{route('convenio.create')}}"> Agregar Convenio</a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered Datatables">
            <caption>Catalogo de Solcitudes</caption>
            <thead>
                <tr>
                    <th scope="col">No. de Convenio</th>
                    <th scope="col">Institución</th>
                    <th scope="col">Fecha de Firma</th>
                    <th scope="col">Fecha de Vigencia</th>
                    <th scope="col">Estado</th>
                    <th width="160px">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->no_convenio}}</th>
                        <td>{{$itemData->institucion}}</td>
                        <td>{{$itemData->fecha_firma}}</td>
                        <td>{{$itemData->fecha_vigencia}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>
                            <a class="btn btn-warning" href="{{route('convenios.edit', ['id' => $itemData->id])}}">
                                Mostrar
                            </a>
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
