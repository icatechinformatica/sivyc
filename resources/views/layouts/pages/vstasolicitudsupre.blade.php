<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'SUPRE | SIVyC Icatech')
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
                    <h2>Solicitudes para Suficiencia Presupuestal</h2>
                </div>
                <br>
                <div class="pull-right">
                    @can('supre.create')
                        <a class="btn btn-success btn-lg" href="{{route('frm-supre')}}"> Nuevo</a>
                    @endcan
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Catalogo de Solcitudes</caption>
            <thead>
                <tr>
                    <th scope="col">No. de Memorandum</th>
                    <th scope="col">Unidad de Capacitación</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Estatus</th>
                    <th width="180px">Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->no_memo}}</th>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->fecha}}</td>
                        <td>
                            @if ($itemData->status == 'En_Proceso')
                                En Proceso
                            @else
                                {{$itemData->status}}
                            @endif
                        </td>
                        <td>
                            @if ($itemData->status == 'Validado' || $itemData->status == 'En_Proceso')
                                <a class="btn btn-warning" href="{{route('supre-pdf', ['id' => $itemData->id])}}" target="_blank">Solicitud PDF</a>
                                <a class="btn btn-warning" href="{{route('tablasupre-pdf', ['id' => $itemData->id])}}" target="_blank">Anexo PDF</a>
                                <input hidden value={{$itemData->id}} id='pdfp'>
                                @if ($itemData->status == 'Validado')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validacion PDF</a>
                                @endif
                                @if ($itemData->status == 'En_Proceso')
                                @can('supre.validacion')
                                    <a class="btn btn-success" href="{{route('supre-validacion', ['id' => $itemData->id])}}">Validar</a>
                                @endcan
                            @endif
                            @endif
                            @if ($itemData->status == 'En_Proceso' || $itemData->status == 'Rechazado')
                                @can('supre.edit')
                                    <a class="btn btn-info" href="{{route('modificar_supre', ['id' => $itemData->id])}}">Editar</a>
                                @endcan()
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
    <br>
@endsection
