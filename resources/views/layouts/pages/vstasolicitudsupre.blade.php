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
                    <a class="btn btn-success btn-lg" href="{{route('frm-supre')}}"> Nuevo</a>
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
                        <td>{{$itemData->status}}</td>
                        <td>
                            @if ($itemData->status == 'Validado' || $itemData->status == 'En Proceso')
                                <a class="btn btn-warning" href="{{route('supre-pdf', ['id' => $itemData->id])}}" target="_blank">Solicitud PDF</a>
                                @if ($itemData->status == 'Validado')
                                    <a class="btn btn-danger" href="{{route('solicitudsuficiencia', ['id' => $itemData->id])}}" target="_blank">Validacion PDF</a>
                                @endif
                                @if ($itemData->status == 'En Proceso')
                                <a class="btn btn-success" href="{{route('supre-validacion', ['id' => $itemData->id])}}">Validar</a>
                            @endif
                            @endif
                            @if ($itemData->status == 'En Proceso' || $itemData->status == 'Rechazado')
                                <a class="btn btn-info" href="{{route('modificar_supre', ['id' => $itemData->id])}}">Editar</a>
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
        <hr style="border-color:dimgray">
        <h2>Status de Folios de Suficiencia</h2>
        <table  id="table-folios" class="table table-bordered">
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
                @foreach ($data2 as $itemData)
                    <tr>
                        <th scope="row">{{$itemData->no_memo}}</th>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->fecha}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>{{$itemData->folio_validacion}}</td>
                        <td>
                            @if ($itemData->status == 'Validado')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación PDF</a>
                                    <a class="btn btn-success" href="{{route('contratos.create', ['id' => $itemData->id_folios])}}">Crear Contrato</a>
                            @endif
                            @if ($itemData->status == 'Contratado')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación PDF</a>
                                    <a class="btn btn-info" href="{{route('contrato-pdf', ['id' => $itemData->id_folios])}}" target="_blank">Contrato PDF</a>
                                    <a class="btn btn-success" href="{{route('solicitud-pago', ['id' => $itemData->id_folios])}}">Solicitar Pago</a>
                            @endif
                            @if ($itemData->status == 'Pago Rechazado')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación PDF</a>
                                    <a class="btn btn-info" href="{{route('solicitudsuficiencia', ['id' => $itemData->id])}}" >Modificar</a>
                            @endif
                            @if ($itemData->status == 'Verificando_Pago')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación PDF</a>
                                    <a class="btn btn-info" href="{{route('solicitudsuficiencia', ['id' => $itemData->id])}}" target="_blank">Contrato PDF</a>
                                    <a class="btn btn-danger" href="{{$itemData->docs}}" target="_blank">Sol. Pago PDF</a>
                            @endif
                            @if ($itemData->status == 'Pago en Proceso')
                                    <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación PDF</a>
                                    <a class="btn btn-info" href="{{route('solicitudsuficiencia', ['id' => $itemData->id])}}" target="_blank">Contrato PDF</a>
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
