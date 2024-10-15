<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'SUPRE | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
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
    <div class="card-header">
        Suficiencia Presupuestal
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if ($message =  Session::get('warning'))
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Solicitudes para Suficiencia Presupuestal</h2>
                    {!! Form::open(['route' => 'supre-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="ejercicio" class="form-control mr-sm-2" id="ejercicio">
                            @foreach ($array_ejercicio as $cad)
                                <option value="{{$cad}}" @if($año_pointer == $cad) selected @endif>{{$cad}}</option>
                            @endforeach
                        </select>
                        <select name="tipo_suficiencia" class="form-control mr-sm-2" id="tipo_suficiencia">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="no_memorandum">N° MEMORANDUM</option>
                            <option value="unidad_capacitacion">UNIDAD CAPACITACIÓN</option>
                            <option value="fecha">FECHA</option>
                        </select>
                        <Div id="divunidades" name="divunidades" class="d-none d-print-none">
                            <select name="unidad" class="form-control mr-sm-2" id="unidad">
                                <option value="">SELECCIONE UNIDAD</option>
                                @foreach ($unidades as $cadwell)
                                    <option value="{{$cadwell->unidad}}">{{$cadwell->unidad}}</option>
                                @endforeach
                            </select>
                        </Div>
                        <div id="divcampo" name="divcampo">
                            {!! Form::text('busquedaporSuficiencia', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                        </div>
                        <Div id="divstat" name="divstat">
                            <select name="tipo_status" class="form-control mr-sm-2" id="tipo_status">
                                <option value="">BUSQUEDA POR STATUS</option>
                                <option value="En_Proceso">EN PROCESO</option>
                                <option value="Validado">VALIDADO</option>
                                <option value="Contratado">CONTRATADO</option>
                                <option value="Rechazado">RECHAZADO</option>
                            </select>
                        </Div>
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
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
                    <th scope="col">Status</th>
                    <th scope="col">Ultima Modificación de Status</th>
                    <th scope="col">E.Firma</th>
                    <th width="180px">Accion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    @php $idh = base64_encode($itemData->id); @endphp
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
                            {{$itemData->fecha_status}}
                        </td>
                        <td> Solicitud:
                            @switch($itemData->efirma_status_supre)
                                @case('EnFirma')
                                    En Firma
                                @break
                                @case('VALIDADO')
                                    Sellado
                                @break
                                @case('CANCELADO')
                                    Cancelado
                                @break
                                @case('CANCELADO ICTI')
                                    Cancelado
                                @break
                                @default
                                    No Generado
                                @break
                            @endswitch
                            @if(!is_null($itemData->folio_validacion))
                            <br>Validación:
                                @switch($itemData->efirma_status_valsupre)
                                    @case('EnFirma')
                                        En Firma
                                    @break
                                    @case('VALIDADO')
                                        Sellado
                                    @break
                                    @case('CANCELADO')
                                        Cancelado
                                    @break
                                    @case('CANCELADO ICTI')
                                        Cancelado
                                    @break
                                    @default
                                        No Generado
                                    @break
                                @endswitch
                            @endif
                        </td>
                        <td>
                            @if ( $itemData->status == 'En_Proceso')
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#supreModal" data-id='["{{$idh}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->doc_supre}}"]'>
                                    <i class="fa fa-file" aria-hidden="true"></i>
                                </a>
                                {{-- @if ($itemData->doc_supre != NULL) --}}
                                @if ($itemData->doc_supre != NULL || $itemData->supre_sellado)
                                    @can('supre.validar')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Validar" href="{{route('supre-validacion', ['id' => $idh])}}">
                                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                    @if ($itemData->doc_supre != NULL)
                                        @can('supre.upload_supre')
                                            <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                                data-toggle="modal" data-placement="top"
                                                data-target="#DocSupreModal2"
                                                data-id='{{$itemData->id}}'
                                                title="Reemplazar Suficiencia Presupuestal Firmada">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        @endcan
                                    @endif
                                @else
                                {{-- @elseif (!$itemData->supre_sellado) --}}
                                    @can('supre.edit')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Editar" href="{{route('modificar_supre', ['id' => $idh])}}">
                                            <i class="fa fa-wrench" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                    @can('supre.upload_supre')
                                        <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#DocSupreModal"
                                            data-id='{{$itemData->id}}'
                                            title="Cargar Validación de Suficiencia Presupuestal Firmada">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    @endcan
                                @endif
                                <input hidden value={{$itemData->id}} id='pdfp'>
                            @endif
                            @if ($itemData->status == 'Validado')
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#supreModal" data-id='["{{$idh}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->doc_supre}}"]'>
                                    <i class="fa fa-file" aria-hidden="true"></i>
                                </a>
                                {{-- @if ($itemData->doc_supre != NULL || $itemData->supre_sellado) --}}
                                @if($itemData->doc_validado != NULL || $itemData->valsupre_sellado)
                                    @if($itemData->edicion_pago || is_null($itemData->status_recepcion) || $itemData->status_recepcion == 'Rechazado')
                                            @can('supre.upload_valsupre')
                                                <button type="button" class="btn btn-warning btn-circle m-1 btn-circle-sm"
                                                    data-toggle="modal" data-placement="top"
                                                    data-target="#DocModal2"
                                                    data-id='{{$itemData->id}}'
                                                    title="Reemplazar Validación de Suficiencia Presupuestal Firmada {{$itemData->status_recepcion}}">
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                            @endcan
                                    @endif
                                @else
                                    @if(!in_array($itemData->status_recepcion, ['VALIDADO', 'En Espera']) || $itemData->permiso_editar)
                                        @can('supre.restart')
                                            <button type="button" class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                                data-toggle="modal" data-placement="top"
                                                data-target="#restartModal"
                                                data-id='{{$itemData->id}}'
                                                title="Rechazar Solicitud y Validación de Suficiencia Presupuestal">
                                                <i class="fa fa-history"></i>
                                            </button>
                                        @endcan
                                        @can('folio.modificar')
                                        <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#modfolioModal"
                                            data-id='{{$itemData->id}}'
                                            title="Permiso de Reemplazo de solicitud autografo">
                                            <i class="fa fa-history"></i>
                                        </button>
                                    @endcan
                                    @endif
                                    @can('supre.upload_valsupre')
                                    <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                        data-toggle="modal" data-placement="top"
                                        data-target="#DocModal"
                                        data-id='{{$itemData->id}}'
                                        title="Cargar Validación de Suficiencia Presupuestal Firmada">
                                        <i class="fa fa-upload"></i>
                                    </button>
                                @endcan
                                @endif
                                @if(!is_null($itemData->folio_validacion) && $itemData->efirma_status_valsupre != 'VALIDADO')
                                    @can('supre.validar')
                                            <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Editar Validación de Suficiencia Presupuestal" href="{{route('valsupre-mod', ['id' => $idh])}}">
                                                <i class="fa fa-wrench" aria-hidden="true"></i>
                                            </a>
                                    @endcan
                                @endif
                                @if ($itemData->permiso_editar)
                                    @can('supre.upload_supre')
                                        <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#DocSupreModal2"
                                            data-id='{{$itemData->id}}'
                                            title="Reemplazar Suficiencia Presupuestal Firmada">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    @endcan
                                @endif
                            @endif
                            @if ($itemData->status == 'Rechazado')
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#supreModal" data-id='["{{$idh}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->doc_supre}}"]'>
                                    <i class="fa fa-file" aria-hidden="true"></i>
                                </a>
                                {{-- @if(!$itemData->supre_sellado) --}}
                                    @can('supre.edit')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Editar" href="{{route('modificar_supre', ['id' => $idh])}}">
                                            <i class="fa fa-wrench" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                {{-- @endif --}}
                                @if ($itemData->doc_supre != NULL)
                                    @can('supre.upload_supre')
                                        <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm"
                                            data-toggle="modal" data-placement="top"
                                            data-target="#DocSupreModal2"
                                            data-id='{{$itemData->id}}'
                                            title="Reemplazar Suficiencia Presupuestal Firmada">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    @endcan
                                @endif
                                @can('supre.validar')
                                    <button type="button" class="btn btn-danger btn-circle m-1 btn-circle-sm"
                                        data-toggle="modal" data-placement="top"
                                        data-target="#cancelModal"
                                        data-id='{{$itemData->id}}'
                                        title="Cancelar Suficiencia Presupuestal">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        {{ $data->appends(request()->query())->links() }}
                    </td>
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
                            {{-- @can('supre.create') --}}
                                <div style="text-align:center" class="form-group">
                                    <a class="btn btn-danger" id="supre_pdf" name="supre_pdf" href="#" target="_blank">Solicitud de Suficiencia Presupuestal</a><br>
                                </div>
                            {{-- @endcan --}}
                            <div style="text-align:center" class="form-group">
                                <a class="btn btn-danger" id="valsupre_pdf" name="valsupre_pdf" href="#" target="_blank">Validación de Suficiencia Presupuestal</a><br>
                            </div>
                            <div style="text-align:center" class="form-group">
                                <a class="btn btn-danger" id="supre2_pdf" name="supre2_pdf" href="#" target="_blank" download>Suficiencia Presupuestal Autografa</a><br>
                            </div>
                            <div style="text-align:center" class="form-group">
                                <a class="btn btn-danger" id="valsupre2_pdf" name="valsupre2_pdf" href="#" target="_blank" download>Validación de Suficiencia Presupuestal Autografa</a><br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- END -->
        <!-- Modal -->
            <div class="modal fade" id="DocModal" role="dialog">
                <div class="modal-dialog">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('doc-valsupre-guardar') }}" id="doc_valsupre">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Cargar Validación de Suficiencia Presupuestal Firmada</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="text-align:center">
                                <div style="text-align:center" class="form-group">
                                    <input type="file" accept="application/pdf" class="form-control" id="doc_validado" name="doc_validado" placeholder="Archivo PDF">
                                    <input id="idinsmod" name="idinsmod" hidden>
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
        <!-- Modal -->
            <div class="modal fade" id="DocModal2" role="dialog">
                <div class="modal-dialog">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('doc-valsupre-guardar') }}" id="doc_valsupre">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Reemplazar Validación de Suficiencia Presupuestal Firmada</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="text-align:center">
                                <div style="text-align:center" class="form-group">
                                    <input type="file" accept="application/pdf" class="form-control" id="doc_validado" name="doc_validado" placeholder="Archivo PDF">
                                    <input id="idinsmod2" name="idinsmod2" hidden>
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
        <!-- Modal -->
            <div class="modal fade" id="DocSupreModal" role="dialog">
                <div class="modal-dialog">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('doc-supre-guardar') }}" id="doc_supre">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Cargar Suficiencia Presupuestal Firmada</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="text-align:center">
                                <div style="text-align:center" class="form-group">
                                    <input type="file" accept="application/pdf" class="form-control" id="doc_supre" name="doc_supre" placeholder="Archivo PDF">
                                    <input id="idsupmod" name="idsupmod" hidden>
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
        <!-- Modal -->
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
    <!-- END -->
        <!-- Modal -->
            <div class="modal fade" id="restartModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><b>¿Esta seguro de rechazar este proceso?</b></h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <br>
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
         <!-- END -->
        <!-- Modal -->
            <div class="modal fade" id="cancelModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><b>¿Esta seguro de cancelar este proceso?</b></h5>
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
                                <a class="btn btn-success" id="confirm_cancel" name="confirm_cancel" href="#">Aceptar</a>
                            </div>
                            <div class="form-group col-md-2"></div>
                        </div>
                    </div>
                </div>
            </div>
    <!-- END -->
        <!-- Modal -->
            <div class="modal fade" id="modfolioModal" role="dialog">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('folio-permiso-mod') }}" id="mod_folio">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><b>Seleccione el número de Suficiencia</b></h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-4"></div>
                                <div class="form-group col-md-4">
                                    <label for="unidad" class="control-label">Folio de Validación </label>
                                    <select name="folios" id="folios" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                </select>
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
                        </div>
                    </form>
                </div>
            </div>
        <!-- END -->
        <!-- Modal -->
        <div class="modal fade" id="modchangevalmodal" role="dialog">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('folio-permiso-mod') }}" id="mod_folio">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">¿Esta seguro de regresar a planeación la validación?<b></b></h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="form-row">
                        <div class="form-group col-md-3"></div>
                            <div class="form-group col-md-6">
                                <label for="unidad" class="control-label">Esto Regresara la Validación Suficiencia Presupuestal a planeación para su correción</label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-4">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            </div>
                            <div class="form-group col-md-4">
                                <a id="valsupre_confirm" href="#" class="btn btn-primary" >Confirmar</a>
                            </div>
                            <div class="form-group col-md-1"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <!-- END -->
    </div>
    <br>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/modals.js") }}"></script>
<script src="{{ asset("js/validate/filtersupre.js") }}"></script>
@endsection
