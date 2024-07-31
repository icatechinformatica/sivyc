<!--Creado por Orlando Chavez -->
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
            line-height:50px;
        }
    </style>
    <div class="container g-pt-50">
        @if ($message =  Session::get('info'))
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Contratos</h2>
                    {!! Form::open(['route' => 'contrato-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="ejercicio" class="form-control mr-sm-2" id="ejercicio">
                            @foreach ($array_ejercicio as $cad)
                                <option value="{{$cad}}" @if($año_pointer == $cad) selected @endif>{{$cad}}</option>
                            @endforeach
                        </select>
                        <select name="tipo_contrato" class="form-control mr-sm-2" id="tipo_contrato">
                            <option value="">BUSQUEDA POR TIPO</option>
                            <option value="no_memorandum">N° MEMORANDUM</option>
                            <option value="unidad_capacitacion">UNIDAD CAPACITACIÓN</option>
                            <option value="fecha">FECHA</option>
                            <option value="folio_validacion">FOLIO DE VALIDACIÓN</option>
                            <option value="mes">MES</option>
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
                        <div id="divcampo" name="divcampo">
                            {!! Form::text('busquedaPorContrato', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                        </div>
                        <Div id="divstat" name="divstat">
                            <select name="tipo_status" class="form-control mr-sm-2" id="tipo_status">
                                <option value="">BUSQUEDA POR STATUS</option>
                                <option value="Validado">SUFICIENCIA VALIDADA</option>
                                <option value="Capturando">CAPTURANDO</option>
                                {{-- <option value="Validando_Contrato">CONTRATO EN REVISION</option>
                                <option value="Contratado">CONTRATO VALIDADO</option>
                                <option value="Contrato_Rechazado">CONTRATO RECHAZADO</option>
                                <option value="Verificando_Pago">VERIFICANDO PAGO</option> --}}
                                <option value="Pago_Verificado">PAGO VERIFICADO</option>
                                {{-- <option value="Pago_Rechazado">PAGO RECHAZADO</option> --}}
                                <option value="Finalizado">FINALIZADO</option>
                            </select>
                        </Div>
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-info  m-1" title="Está a tiempo" data-toggle="modal" data-target="#mymodalSemaforo">
                        Información sobre Semaforo
                    </a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered Datatables table-responsive-md">
            <caption>Catalogo de Suficiencias Presupuestales Validadas</caption>
            <thead>
                <tr>
                    <th scope="col">No. de Memoramdum</th>
                    <th scope="col">Unidad de Capacitación</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ultima Modificación de Status</th>
                    <th scope="col">Folio de Validación</th>
                    <th scope="col">Observación</th>
                    @can('contratos.create')
                        <th scope="col">Fecha de Validación de Recepción Fisica</th>
                    @endcan
                    <th scope="col">Acción</th>
                    <th scope="col">semaforo</th>
                </tr>
            </thead>
            @if (count($querySupre) > 0)
                <tbody>
                    @foreach ($querySupre as $itemData)
                        <tr>
                            <th scope="row">{{$itemData->no_memo}}</th>
                            <td>{{$itemData->unidad_capacitacion}}</td>
                            <td>
                                @if($itemData->created_at != NULL)
                                    <?php $d = $itemData->created_at->format('d'); $m = $itemData->created_at->format('m'); $y = $itemData->created_at->format('y'); ?>
                                    {{$d}}/{{$m}}/{{$y}}
                                @endif
                            </td>
                            <td>

                                @switch($itemData->status)
                                    @case('Validado')
                                        Suficiencia Validada
                                        @break
                                    @case('Capturando')
                                        Capturando
                                        @break
                                    @case('Validando_Contrato')
                                        Contrato en Revision
                                        @break
                                    @default
                                    @if($itemData->status == 'Pago_Verificado' && ($itemData->status_recepcion == 'Recibido' || $itemData->status_recepcion == 'recepcion tradicional')) Para Pago @elseif($itemData->status == 'Pago_Verificado') Para Envío @else{{$itemData->status}} @endif
                                        @break
                                @endswitch
                            </td>
                            <td>{{$itemData->fecha_status}}</td>
                            <td>{{$itemData->folio_validacion}}</td>
                            <td>{{$itemData->observacion}}</td>
                            @can('contratos.create')
                                @if($itemData->recepcion != NULL)
                                    <td>{{$itemData->recepcion}}</td>
                                @else
                                    <td>N/A</td>
                                @endif
                            @endcan
                            <td>
                                @if ($itemData->status == 'Validado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Documento pdf" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                    @can('contratos.create')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Crear Contrato y Solicitud de Pago" href="{{route('contratos.create', ['id' => $itemData->id_folios])}}">
                                            <i class="fa fa-file-text" aria-hidden="true"></i>
                                        </a>
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
                                        @can('supre.upload_supre')
                                            <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                                data-toggle="modal" data-placement="top"
                                                data-target="#DocSupreModal2"
                                                data-id='{{$itemData->id_supre}}'
                                                title="Reemplazar Suficiencia Presupuestal Firmada">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        @endcan
                                    @endif
                                @endif
                                @if ($itemData->status == 'Capturando' || $itemData->status_recepcion == 'Rechazado' || ($itemData->status_recepcion == null && $itemData->id_contrato != null))
                                    @can('contratos.edit')
                                        {{-- <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Firma Electronica" href="{{route('contrato-validado-historial', ['id' => $itemData->id_contrato])}}">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a> --}}
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Modificar Contrato y Pago" href="{{route('contrato-mod', ['id' => $itemData->id_contrato])}}">
                                            <i class="fa fa-file-text" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                    {{-- <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Documento pdf" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a> --}}
                                    @if(!is_null($itemData->status_recepcion))
                                        @can('contrato.restart')
                                            <button type="button" class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                                data-toggle="modal" data-placement="top"
                                                data-target="#restartModalContrato"
                                                data-id='{{$itemData->id_folios}}'
                                                title="Reiniciar Contrato">
                                                <i class="fa fa-history"></i>
                                            </button>
                                        @endcan
                                    @endif
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
                                        @can('supre.upload_supre')
                                            <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                                data-toggle="modal" data-placement="top"
                                                data-target="#DocSupreModal2"
                                                data-id='{{$itemData->id_supre}}'
                                                title="Reemplazar Suficiencia Presupuestal Firmada">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        @endcan
                                    @endif
                                @endif
                                @if ($itemData->status == 'Pago_Verificado' || ($itemData->status_recepcion == null && $itemData->id_contrato != null))
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
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
                                        @can('supre.upload_supre')
                                            <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                                data-toggle="modal" data-placement="top"
                                                data-target="#DocSupreModal2"
                                                data-id='{{$itemData->id_supre}}'
                                                title="Reemplazar Suficiencia Presupuestal Firmada">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        @endcan
                                    @endif
                                @endif
                                @if ($itemData->status == 'Finalizado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    {{-- <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('contrato-validado-historial', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a> --}}
                                    @can('folio.cancel')
                                        <button type="button" class="btn btn-warning btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#cancelModalFolio"
                                            data-id='{{$itemData->id_folios}}'
                                            title="Cancelar Folio">
                                            <i class="fa fa-window-close"></i>
                                        </button>
                                    @endcan
                                @endif
                            </td>
                            <td>
                                @if ($itemData->fecha_dif > 0)
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" data-fecha-id="{{$itemData->fecha_dif }}" title="La fecha de vencimiento ha pasado por {{$itemData->fecha_dif }} días">
                                        <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                                    </a>
                                @elseif ($itemData->fecha_dif >= -3)
                                    <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-fecha-id="{{$itemData->fecha_dif }}" title="La fecha de vencimiento está cerca faltán {{$itemData->fecha_dif }} días">
                                        <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Está a tiempo">
                                        <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8">
                            {{ $querySupre->appends(request()->query())->links() }}
                        </td>
                    </tr>
                </tfoot>
            @else
                <tbody>
                    <tr>
                        <td colspan="8">
                            <h4>
                                <center>
                                    <b>NO HAY REGISTROS DISPONIBLES</b>
                                </center>
                            </h4>
                        </td>
                    </tr>
                </tbody>
            @endif

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
                            <a class="btn btn-danger" id="valsupre_pdf" name="valsupre_pdf" href="#" target="_blank" download>Validación de Suficiencia Presupuestal</a><br>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!--Modal Ends-->
    <!--Modal Semaforo-->
    <div class="modal fade" id="mymodalSemaforo" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">INFORMACIÓN SOBRE EL SEMAFORO</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="LA FECHA DEL CURSO YA HA FINALIZADO O TERMINADO">
                    <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                </a>
                    <b>LA FECHA DEL CURSO YA HA FINALIZADO O TERMINADO</b>
                <br>
                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" title="EL CURSO TIENE TRES DÍAS PARA FINALIZAR">
                    <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                </a>
                    <b>EL CURSO TIENE TRES DÍAS PARA FINALIZAR</b>
                <br>
                <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="LAS FECHAS DE FINALIZACIÓN DEL CURSO ESTÁN EN TIEMPO Y FORMA">
                    <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
                </a>
                    <b>LAS FECHAS DE FINALIZACIÓN DEL CURSO ESTÁN EN TIEMPO Y FORMA</b>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
    </div>
    <!--Modal Semaforo Ends-->
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
<!-- Modal -->
<div class="modal fade" id="recepcionModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>¿Esta seguro de confirmar la recepción de documentos?</b></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('recepcion') }}" method="post" id="recepcion">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-3"></div>
                    <div class="form-group col-md-5">
                        <label for="fecha_recepcion" class="control-label">Fecha de Recepción</label>
                    <input type="date" class="form-control" name="fecha_recepcion" id="fecha_recepcion" required>
                    <input name="idf" id="idf" type="text" class="form-control" hidden>
                    </div>
                    <div class="form-group col-md-2"></div>
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
<div class="modal fade" id="DocSupreModal2" role="dialog">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" action="{{ route('doc-supre-guardar') }}" id="doc_supre">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reemplazar Suficiencia Presupuestal Firmada</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <input type="file" accept="application/pdf" class="form-control" id="doc_supre" name="doc_supre" placeholder="Archivo PDF">
                        <input id="idsupmod2" name="idsupmod2" hidden>
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
<br>
@endsection
@section('script_content_js')
    <script src="{{ asset("js/validate/modals.js") }}"></script>
    <script src="{{ asset("js/validate/statuschangefilter.js") }}"></script>
@endsection
