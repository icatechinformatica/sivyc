@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Pagos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrapcustomizer.css") }}">
    <link href="{{ asset("vendor/toggle/bootstrap-toggle.css") }}" rel="stylesheet">
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

        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }
        .switch {
          position: relative;
          display: inline-block;
          width: 90px;
          height: 34px;
        }

        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(50px);
          -ms-transform: translateX(50px);
          transform: translateX(50px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
    </style>
    <div class="container g-pt-50">
        @if ($message =  Session::get('info'))
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Solicitudes de Pagos</h2>
                    {!! Form::open(['route' => 'pago-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="ejercicio" class="form-control mr-sm-2" id="ejercicio">
                            @foreach ($array_ejercicio as $cad)
                                <option value="{{$cad}}" @if($año_pointer == $cad) selected @endif>{{$cad}}</option>
                            @endforeach
                        </select>
                        <select name="tipo_pago" class="form-control mr-sm-2" id="tipo_pago">
                            <option value="">BUSQUEDA POR TIPO</option>
                            <option value="no_contrato">N° DE CONTRATO</option>
                            <option value="unidad_capacitacion">UNIDAD CAPACITACIÓN</option>
                            <option value="fecha_firma">FECHA</option>
                            <option value="mes">MES</option>
                            <option value="agendar_fecha" @if($tipoPago == 'agendar_fecha') selected @endif>LISTOS PARA ENTREGA FISICA</option>
                        </select>
                        <Div id="divmes" name="divmes" class="d-none d-print-none">
                            <select name="mes" class="form-control mr-sm-2" id="mes">
                                <option value="">SELECCIONE MES</option>
                                <option value="01">ENERO</option>
                                <option value="02">FEBRERO</option>
                                <option value="03">MARZO</option>
                                <option value="04">ABRIL</option>
                                <option value="05">MAYO</option>
                                <option value="06">JUNIO</option>
                                <option value="07">JULIO</option>
                                <option value="08">AGOSTO</option>
                                <option value="09">SEPTIEMBRE</option>
                                <option value="10">OCTUBRE</option>
                                <option value="11">NOVIEMBRE</option>
                                <option value="12">DICIEMBRE</option>
                            </select>
                        </Div>
                        <Div id="divunidades" name="divunidades" class="d-none d-print-none">
                            <select name="unidad" class="form-control mr-sm-2" id="unidad">
                                <option value="">SELECCIONE UNIDAD</option>
                                @foreach ($unidades as $cadwell)
                                    <option value="{{$cadwell->unidad}}">{{$cadwell->unidad}}</option>
                                @endforeach
                            </select>
                        </Div>
                        <div id="divcampo" name="divcampo" @if($tipoPago == 'agendar_fecha') class="d-none d-print-none" @endif>
                        {!! Form::text('busquedaPorPago', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                        </div>
                        <Div id="divstat" name="divstat" @if($tipoPago == 'agendar_fecha') class="d-none d-print-none" @endif>
                            <select name="tipo_status" class="form-control mr-sm-2" id="tipo_status">
                                <option value="">BUSQUEDA POR STATUS</option>
                                <option value="Verificando_Pago">VERIFICANDO PAGO</option>
                                <option value="Pago_Verificado">PAGO VERIFICADO</option>
                                <option value="Pago_Rechazado">PAGO RECHAZADO</option>
                                <option value="Finalizado">FINALIZADO</option>
                            </select>
                        </Div>
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="pull-left">
        </div>
        <hr style="border-color:dimgray">
        {{-- @if($tipoPago == 'agendar_fecha')
            <form action="{{ route('agendar-entrega-pago') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                    </div>
                    <div class="form-group col-md-2" style="text-align: right;">
                        <h5>Fecha a Agendar</h5>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="date" class="form-control" id="agendar_date" name="agendar_date" required>
                    </div>
                    <div class="form-group col-md-2">
                        <h5 for="agendar_date"></h5>
                        <button type="submit" class="btn btn-primary" style="bottom: 25%;" >Agendar</button>
                    </div>
                </div>
                <div style="text-align: right;">
                    <label style="color: black">Seleccionar Todo</label>
                    <input  type="checkbox" id="ckbCheckAll"
                            data-toggle="toggle"
                            data-style="ios"
                            data-on= " "
                            data-off= " "
                            data-onstyle="success"
                            data-offstyle="danger"
                            onchange="toggleOnOff()"/>
                </div>
        @endif --}}
        <table  id="table-instructor" class="table table-bordered table-responsive-md Datatables">
            <caption>Lista de Contratos en Espera</caption>
            <thead>
                <tr>
                    <th scope="col">N°. Contrato</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Unidad de Capacitación</th>
                    <th scope="col">Status</th>
                    {{-- <th scope="col" style="width: 150px;">Ultima Modificación de Status</th> --}}
                    {{-- @can('contratos.create')
                            <th scope="col" style="width: 150px;">Fecha de Validación de Recepción Fisica</th>
                    @endcan --}}
                    <th width="160px">Acciones</th>
                    <th scope="col" width="200px">Fecha de Entrega Fisica</th>
                    <th scope="col" width="130px">Factura</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contratos_folios as $itemData)
                    <tr @if($itemData->alerta == TRUE && is_null($itemData->fecha_agenda)) style='background-color: #621032;;' @endif>
                        <td>{{$itemData->numero_contrato}}</td>
                        <td>
                            @if($itemData->created_at != NULL)
                                <?php $d = $itemData->created_at->format('d'); $m = $itemData->created_at->format('m'); $y = $itemData->created_at->format('y'); ?>
                                {{$d}}/{{$m}}/{{$y}}
                            @endif
                        </td>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->status}}</td>
                        {{-- <td>{{$itemData->fecha_status}}</td> --}}
                        {{-- @can('contratos.create')
                            @if($itemData->recepcion != NULL)
                                <td>{{$itemData->recepcion}}</td>
                            @else
                                <td>N/A</td>
                            @endif
                        @endcan --}}
                        <td>
                            @switch($itemData->status)
                                @case('Verificando_Pago')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->arch_pago}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    @can('verificar_pago.create')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Verificar Pago" href="{{route('pago.verificarpago', ['id' => $itemData->id_contrato])}}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                    @can('contrato.restart')
                                        <button type="button" class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#restartModalContrato"
                                            data-id='{{$itemData->id_folios}}'
                                            title="Reiniciar Contrato">
                                            <i class="fa fa-history"></i>
                                        </button>
                                    @endcan
                                    @can('folio.cancel')
                                        <button type="button" class="btn btn-warning btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#cancelModalFolio"
                                            data-id='{{$itemData->id_folios}}'
                                            title="Cancelar Folio">
                                            <i class="fa fa-window-close"></i>
                                        </button>
                                    @endcan
                                    @if($itemData->permiso_editar == TRUE)
                                        @can('folio.especialedit')
                                            <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Editar Folio" href="{{route('folio_especialedit', ['id' => $itemData->id_folios])}}">
                                                <i class="fa fa-wrench" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                    @endif
                                @break
                                @case('Pago_Verificado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->arch_pago}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    @can('pagos.create')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Confirmar Pago" href="{{route('pago-crear', ['id' => $itemData->id_contrato])}}">
                                            <i class="fa fa-money" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @can('pago.restart')
                                        <button type="button" class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#restartModalPago"
                                            data-id='{{$itemData->id_folios}}'
                                            title="Reiniciar Solicitud de Pago">
                                            <i class="fa fa-history"></i>
                                        </button>
                                    @endcan
                                    @if($itemData->permiso_editar == TRUE)
                                        @can('folio.especialedit')
                                            <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Editar Folio" href="{{route('folio_especialedit', ['id' => $itemData->id_folios])}}">
                                                <i class="fa fa-wrench" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                    @endif
                                @break
                                @case('Pago_Rechazado')
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @can('contratos.edit')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Modificar Solicitud de Pago" href="{{route('pago-mod', ['id' => $itemData->id_folios])}}" >
                                            <i class="fa fa-wrench" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                    @can('contrato.restart')
                                        <button type="button" class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#restartModalContrato"
                                            data-id='{{$itemData->id_folios}}'
                                            title="Reiniciar Contrato">
                                            <i class="fa fa-history"></i>
                                        </button>
                                    @endcan
                                    @can('folio.cancel')
                                        <button type="button" class="btn btn-warning btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#cancelModalFolio"
                                            data-id='{{$itemData->id_folios}}'
                                            title="Cancelar Folio">
                                            <i class="fa fa-window-close"></i>
                                        </button>
                                    @endcan
                                    @if($itemData->permiso_editar == TRUE)
                                        @can('folio.especialedit')
                                            <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Editar Folio" href="{{route('folio_especialedit', ['id' => $itemData->id_folios])}}">
                                                <i class="fa fa-wrench" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                    @endif
                                @break
                                @case('Finalizado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->arch_pago}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Resumen de Pago" href="{{route('mostrar-pago', ['id' => $itemData->id_contrato])}}" target="_blank">
                                        <i class="fa fa-money" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Subir Solicitud de Pago Autorizada" id="pago_upload" name="pago_upload" data-toggle="modal" data-target="#Modaluploadpago" data-id='{{$itemData->id_folios}}'>
                                        <i class="fa fa-upload" aria-hidden="true"></i>
                                    </a>
                                @break
                            @endswitch
                        </td>
                        <td>
                            @if(isset($itemData->recepcion))
                                Entregado: {{$itemData->recepcion}}
                            @else
                                Fecha Actual: {{$itemData->fecha_agenda}}
                                @can('contratos.create')
                                    <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-target="#agendarModal" data-id='["{{$itemData->id_contrato}}"]'>
                                        AGENDAR ENTREGA
                                    </a>
                                    {{-- <div @if($tipoPago == 'agendar_fecha') class="form-control-plaintext text-truncate" @else class="d-none d-print-none" @endif >
                                        <label>AÑADIR</label>
                                        <input type="checkbox" class="checkBoxClass"
                                            data-toggle="toggle"
                                            data-style="ios"
                                            data-on=" "
                                            data-off=" "
                                            data-onstyle="success"
                                            data-offstyle="danger"
                                            name="agendar[{{$itemData->id_contrato}}]"
                                            value="{{$itemData->id_contrato}}">
                                    </div> --}}
                                @endcan
                                @if(isset($itemData->fecha_agenda))
                                    @can('contrato.validate')
                                        <a class="btn btn-info" id="recepcionar" name="recepcionar" data-toggle="modal" data-target="#recepcionarModal" data-id='["{{$itemData->id_folios}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->arch_contrato}}"]'>
                                            Confirmar Entrega
                                        </a>
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td>
                            @if (isset($itemData->arch_factura))
                                <a class="btn btn-info" href="{{$itemData->arch_factura}}" target="_blank">PDF</a>
                            @endif
                            @if (isset($itemData->arch_factura_xml))
                                <a class="btn btn-info" href="{{$itemData->arch_factura_xml}}" target="_blank">XML</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        {{ $contratos_folios->appends(request()->query())->links() }}
                    </td>
                </tr>
            </tfoot>
        </table>
        {{-- @if($tipoPago == 'agendar_fecha')
            </form>
        @endif --}}
        <br>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
      <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Archivos PDF Generables</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                            <a class="btn btn-danger" id="pagoautorizado_pdf" name="pagoautorizado_pdf" href="#" target="_blank" download>Solicitud de Pago Autorizado</a><br>
                        </div>
                        <div style="text-align:center" class="form-group">
                            <a class="btn btn-danger" id="valsupre_pdf" name="valsupre_pdf" href="#" target="_blank" download>Validación de Suficiencia Presupuestal</a><br>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="restartModalPago" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>¿Esta seguro de reiniciar este proceso?</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2"></div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                    <div class="form-group col-md-4">
                        <a class="btn btn-success" id="confirm_restart2" name="confirm_restart2" href="#">Aceptar</a>
                    </div>
                    <div class="form-group col-md-2"></div>
                </div>
            </div>
        </div>
    </div>
<!-- END -->
    <br>
<!-- Modal -->
    <div class="modal fade" id="restartModalContrato" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>¿Esta seguro de reiniciar este proceso?</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2"></div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                    <div class="form-group col-md-4">
                        <a class="btn btn-success" id="confirm_restart" name="confirm_restart" href="#">Aceptar</a>
                    </div>
                    <div class="form-group col-md-2"></div>
                </div>
            </div>
        </div>
    </div>
<!-- END -->
<!-- Modal Cancel Folio -->
<div class="modal fade" id="cancelModalFolio" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>¿Esta seguro de cancelar este proceso?</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('folio-cancel') }}" method="post" id="cancelfolio">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-2"></div>
                    <div class="form-group col-md-8">
                        <label for="observaciones"><b>Describa el motivo de cancelación</b></label>
                        <textarea name="observaciones" id="observaciones" cols="8" rows="6" class="form-control" required></textarea>
                        <input name="idf" id="idf" type="text" class="form-control" hidden>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2"></div>
                    <div class="form-group col-md-4">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-primary" >Aceptar</button>
                    </div>
                    <div class="form-group col-md-2"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Subir Pago-->
<div class="modal fade" id="Modaluploadpago" role="dialog">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" action="{{ route('doc-pago-guardar') }}" id="doc_pago">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cargar Solicitud de Pago Autorizada</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <input type="file" accept="application/pdf" class="form-control" id="doc_validado" name="doc_validado" placeholder="Archivo PDF">
                        <input id="idfolpa" name="idfolpa" hidden>
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal Confirmar Entrega de Documentacion-->
<div class="modal fade" id="recepcionarModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('confirmar-entrega-fisica') }}" id="confirmar_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Confirmar Entrega Fisica?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <p st>Vista de Documentos</p>
                    <div style="text-align:left" class="form-row">
                        <div class="form-group col-md-1"></div>
                        <div class="form-group col-md-4" style="margin-left: 3%;">
                            <label for="inputfactura_pdf">Contrato Firmado</label>
                            <a class="btn btn-info" id="archivo_contrato_firmado" name="archivo_contrato_firmado" href="#" target="_blank">Contrato</a>
                        </div>
                        <div class="form-group col-md-1"></div>
                        <div class="form-group col-md-5">
                            <label for="inputfactura_xml">Solicitud de Pago Firmada</label>
                            <a class="btn btn-info" id="archivo_pago_firmado" name="archivo_contrato_firmado" href="#" target="_blank">Solicitud de Pago</a>
                        </div>
                    </div>
                    <div style="text-align:center" class="form-group">
                        <p>Si confirmas la entrega fisica se hara el cambio de manera permanente.</p>
                        <input id="id_folio_entrega" name="id_folio_entrega" hidden>
                        <button style="text-align: left; font-size: 10px;" type="button" class="btn btn-danger" data-dismiss="modal">No, Mantener Pendiente la Entrega</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-primary" >Sí, Confirmar Entrega</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal Agendar Entrega de Documentacion-->
<div class="modal fade" id="agendarModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('agendar-entrega-pago') }}" id="agendar_entrega" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Agendar Entrega Fisica?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center;">
                    <div>
                        <p style="text-align:center">Fecha a Agendar</p>
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
                        <input type="date" id="agendar_date" name="agendar_date" class="form-control" style="text-align: center; width: 33%;" required>
                    </div>
                    <p style="text-align:center; margin-bottom: -3%; margin-top: 1%;">Documentación Necesaria para agendar</p><br>
                    <div style="text-align:left" class="form-row">
                        <div class="form-group col-md-1"></div>
                        <div class="form-group col-md-4">
                            <label for="inputfactura_pdf">Factura PDF</label>
                            <input type="file" accept="application/pdf" name="factura_pdf" id="factura_pdf" style="text-align: left; font-size: 12px;" class="form-control">
                        </div>
                        <div class="form-group col-md-1"></div>
                        <div class="form-group col-md-5">
                            <label for="inputfactura_xml">Factura XML</label>
                            <input type="file" accept="application/xml" name="factura_xml" id="factura_xml" style="text-align: right; font-size: 12px;" class="form-control">
                        </div>
                    </div>
                    <div style="text-align:left" class="form-row">
                        <div class="form-group col-md-1"></div>
                        <div class="form-group col-md-4">
                            <label for="inputfactura_pdf">Contrato Firmado</label>
                            <input type="file" accept="application/pdf" name="contrato_pdf" id="contrato_pdf" style="text-align: left; font-size: 12px;" class="form-control" required>
                        </div>
                        <div class="form-group col-md-1"></div>
                        <div class="form-group col-md-5">
                            <label for="inputfactura_xml">Solicitud de Pago Firmada</label>
                            <input type="file" accept="application/pdf" name="solpa_pdf" id="solpa_pdf" style="text-align: right; font-size: 12px;" class="form-control" required>
                        </div>
                    </div>
                    <div style="text-align:center" class="form-group">
                        <p>Si confirmas se asignara la fecha deseada a este registro.</p>
                        <input id="id_contrato_agenda" name="id_contrato_agenda" hidden>
                        <button style="text-align: left; font-size: 10px;" type="button" class="btn btn-danger" data-dismiss="modal">No, Mantener Pendiente la Entrega</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-primary" >Sí, Confirmar</button>
                    </div>
                </div>
                <div class="modal-footer"><div class="form-group"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END -->
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/modals.js") }}"></script>
<script src="{{ asset("js/scripts/bootstrap-toggle.js") }}"></script>
<script>
    $(function(){
    //metodo
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

      document.getElementById('tipo_pago').onchange = function() {
        var index = this.selectedIndex;
        var inputText = this.children[index].innerHTML.trim();

        if(inputText == 'FOLIO DE VALIDACIÓN')
        {
            $('#divstat').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "")
        }
        else
        {
            $('#divstat').prop("class", "")
        }
        if(inputText == 'UNIDAD CAPACITACIÓN')
        {
            console.log('hola');
            $('#divunidades').prop("class", "")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
        }
        if(inputText == 'MES')
        {
            $('#divmes').prop("class", "")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
        }
        if(inputText == 'FECHA')
        {
            $('#divstat').prop("class", "")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "")
        }
        if(inputText == 'N° DE CONTRATO')
        {
            $('#divstat').prop("class", "")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "")
        }
        if(inputText == 'LISTOS PARA ENTREGA FISICA')
        {
            $('#divstat').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
        }
      }

    $('#Modaluploadpago').on('show.bs.modal', function(event){
        // console.log('hola');
        var button = $(event.relatedTarget);
        var id = button.data('id');
        document.getElementById('idfolpa').value = id;
    });

    $('#recepcionarModal').on('show.bs.modal', function(event){
        // console.log('hola');
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id[1]);
        document.getElementById('id_folio_entrega').value = id[0];
        $('#archivo_pago_firmado').attr("href", id[1]);
        $('#archivo_contrato_firmado').attr("href", id[2]);
    });

    $('#agendarModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        // console.log(id);
        document.getElementById('id_contrato_agenda').value = id;
    });

});

function toggleOnOff() {
        var checkBox = document.getElementById("ckbCheckAll");
        if (checkBox.checked == true){
            $('.checkBoxClass').prop('checked', true).change()
        } else {
            $('.checkBoxClass').prop('checked', false).change()
        }
    }
</script>
@endsection
