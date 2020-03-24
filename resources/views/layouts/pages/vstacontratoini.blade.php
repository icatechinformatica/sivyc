<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Contratos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <style>
        * {
        box-sizing: border-box;
        }

        br {
            line-height:70px;
        }
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

        br {
            line-height:50px;
        }
        }
    </style>
    <div class="container g-pt-50">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Contratos</h2>
                </div>
                <br>
                <div class="pull-right">
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
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
                @foreach ($data as $key=>$itemData)
                    <tr>
                        <th scope="row">{{$itemData->no_memo}}</th>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->fecha}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>{{$itemData->folio_validacion}}</td>
                        <td>
                            @if ($itemData->status == 'Validado')
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                                @can('contratos.create')
                                    <a class="btn btn-success" href="{{route('contratos.create', ['id' => $itemData->id_folios])}}">Crear Contrato</a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Contratado')
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                                @can('solicitud_pago.create')
                                    <a class="btn btn-success" href="{{route('solicitud-pago', ['id' => $itemData->id_folios])}}">Solicitar Pago</a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Pago_Rechazado')
                                <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación</a>
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                                @can(contratos.edit)
                                    <a class="btn btn-info" href="{{route('contrato-mod', ['id' => $itemData->id_contrato])}}" >Modificar</a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Verificando_Pago')
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
                            @endif
                            @if ($itemData->status == 'Pago_Verificado')
                                <a class="btn btn-danger" href="{{route('valsupre-pdf', ['id' => $itemData->id])}}" target="_blank">Validación</a>
                                <a class="btn btn-danger" href="{{$itemData->docs}}" target="_blank">Docs. para Pago</a><br/>
                                <a class="btn btn-info" href="{{route('contrato-pdf', ['id' => $itemData->id_contrato])}}" target="_blank">Contrato</a>
                                <a class="btn btn-info" href="{{route('solpa-pdf', ['id' => $itemData->id_folios])}}" target="_blank">Solicitud</a>
                            @endif
                            @if ($itemData->status == 'Finalizado')
                                <a class="btn btn-danger" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}"]'>PDF</a>
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

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
      <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Archivos PDF Generables</h4>
                </div>
            <div class="modal-body" style="text-align:center">
                <form action="" id="pdfForm" method="get">
                    @csrf
                <div style="text-align:center" class="form-group">
                    <a class="btn btn-danger" id="sol_pdf" name="sol_pdf" href="#" target="_blank">Solicitud de Pago</a><br>
                </div>
                <div style="text-align:center" class="form-group">
                    <a class="btn btn-danger" id="contrato_pdf" name="contrato_pdf" href="#" target="_blank">Contrato de Instructor</a>
                </div>
                <div style="text-align:center" class="form-group">
                    <a class="btn btn-danger" id="docs_pdf" name="docs_pdf" href="#" target="_blank">Documentos para solicitud de pago</a>
                </div>
                <div style="text-align:center" class="form-group">
                    <a class="btn btn-danger" id="valsupre_pdf" name="valsupre_pdf" href="#" target="_blank">Validación de Suficiencia Presupuestal</a><br>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>
<br>
@endsection
