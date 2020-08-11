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
                            @if ( $itemData->status == 'En_Proceso')
                            <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#supreModal" data-id='["{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                                @can('supre.validar')
                                    <a class="btn btn-success" href="{{route('supre-validacion', ['id' => $itemData->id])}}">Validar</a>
                                @endcan
                                @can('supre.edit')
                                    <a class="btn btn-info" href="{{route('modificar_supre', ['id' => $itemData->id])}}">Editar</a>
                                @endcan
                                @can('supre.delete')
                                    <a class="btn btn-warning" href="{{route('eliminar-supre', ['id' => $itemData->id])}}">Eliminar</a>
                                @endcan
                                <input hidden value={{$itemData->id}} id='pdfp'>
                            @endif
                            @if ($itemData->status == 'Validado')
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#supreModal" data-id='["{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                            @endif
                            @if ($itemData->status == 'Rechazado')
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#supreModal" data-id='["{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                                @can('supre.edit')
                                    <a class="btn btn-info" href="{{route('modificar_supre', ['id' => $itemData->id])}}">Editar</a>
                                @endcan
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
        <!-- Modal -->
        <div class="modal fade" id="supreModal" role="dialog">
            <div class="modal-dialog">
            <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archivos PDF Generables</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="text-align:center">
                        <div style="text-align:center" class="form-group">
                            <a class="btn btn-danger" id="supre_pdf" name="supre_pdf" href="#" target="_blank">Solicitud de Suficiencia Presupuestal</a><br>
                        </div>
                        <div style="text-align:center" class="form-group">
                            <a class="btn btn-danger" id="anexo_pdf" name="anexo_pdf" href="#" target="_blank">Anexo Solicitud de Suficiencia Presupuestal</a><br>
                        </div>
                        <div style="text-align:center" class="form-group">
                            <a class="btn btn-danger" id="valsupre_pdf" name="valsupre_pdf" href="#" target="_blank">Validación de Suficiencia Presupuestal</a><br>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection
