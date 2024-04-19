@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Pagos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrapcustomizer.css") }}">
    <link href="{{ asset("vendor/toggle/bootstrap-toggle.css") }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        * {
        box-sizing: border-box;
        font-size: 14px !important;
        }
        .btn i.fa-cloud-upload {
            font-size: 1.4em !important; /* Ajusta el tamaño como desees */
        }

        .nav-link i.fa-file-pdf-o {
            font-size: 1.8em !important; /* Ajusta el tamaño como desees */
        }

        td span {
            white-space: nowrap;
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
            width: 20px;
            text-align: left;
        }

        input[type=file]::file-selector-button {
            margin-right: 20px;
            border: none;
            background: #084cdf;
            padding: 10px 20px;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            transition: background .2s ease-in-out;
            width: 70px;
            text-align: left;
            content: "Subir";
        }

        input[type=file]::file-selector-button:hover {
            background: #0d45a5;
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
                            {{-- <option value="agendar_fecha" @if($tipoPago == 'agendar_fecha') selected @endif>LISTOS PARA ENTREGA FISICA</option>
                            <option value="entregado_fisicamente" @if($tipoPago == 'entregado_fisicamente') selected @endif>ENTREGADO FISICAMENTE</option> --}}
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
                                    <option value="{{$cadwell->unidad}}" @if($cadwell->unidad == $unidad) selected @endif>{{$cadwell->unidad}}</option>
                                @endforeach
                            </select>
                        </Div>
                        <div id="divcampo" name="divcampo" @if($tipoPago == 'agendar_fecha') class="d-none d-print-none" @endif>
                        {!! Form::text('busquedaPorPago', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                        </div>
                        <Div id="divstat" name="divstat" @if($tipoPago == 'agendar_fecha') class="d-none d-print-none" @endif>
                            <select name="tipo_status" class="form-control mr-sm-2" id="tipo_status">
                                <option value="">BUSQUEDA POR STATUS</option>
                                {{-- <option value="Verificando_Pago">VERIFICANDO PAGO</option> --}}
                                <option value="Pago_Verificado">PAGO VERIFICADO</option>
                                {{-- <option value="Pago_Rechazado">PAGO RECHAZADO</option> --}}
                                <option value="Finalizado">PAGADO</option>
                                <option value="En Espera">PARA VALIDAR DIGITAL</option>
                                <option value="VALIDADO">VALIDADO</option>
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
        <table  id="table-instructor" class="table table-bordered table-responsive-md Datatables">
            <caption>Lista de Contratos en Espera</caption>
            <thead>
                <tr>
                    <th scope="col" style="font-size: 15px">N°. Contrato</th>
                    <th scope="col" style="font-size: 15px">Fecha</th>
                    <th scope="col" style="font-size: 15px">Unidad de Capacitación</th>
                    <th scope="col" style="font-size: 15px">Instructor</th>
                    <th scope="col" style="font-size: 15px">Status</th>
                    {{-- <th scope="col" style="width: 150px;">Ultima Modificación de Status</th> --}}
                    {{-- @can('contratos.create')
                            <th scope="col" style="width: 150px;">Fecha de Validación de Recepción Fisica</th>
                    @endcan --}}
                    <th width="160px" style="font-size: 15px">Acciones</th>
                    <th scope="col" width="200px" style="font-size: 15px">Fecha de Entrega Fisica</th>
                    <th scope="col" width="130px" style="font-size: 15px">ZIP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contratos_folios as $itemData)
                    <tr @can('contratos.create')@if($itemData->alerta == TRUE && (is_null($itemData->status_recepcion) || $itemData->status_recepcion == 'Rechazado')) style='background-color: #621032; color: white;' @endif @endcan @can('contrato.validate')@if($itemData->alerta_financieros == TRUE && $itemData->status_recepcion == 'En Espera') style='background-color: #621032; color: white;' @endif @endcan>
                        <td style="font-size: 13px">{{$itemData->numero_contrato}}</td>
                        <td style="font-size: 13px">
                            @if($itemData->created_at != NULL)
                                <?php $d = $itemData->created_at->format('d'); $m = $itemData->created_at->format('m'); $y = $itemData->created_at->format('y'); ?>
                                {{$d}}/{{$m}}/{{$y}}
                            @endif
                        </td>
                        <td style="font-size: 13px">{{$itemData->unidad_capacitacion}}</td>
                        <td style="font-size: 13px">{{$itemData->nombre}}</td>
                        <td style="font-size: 13px">@if($itemData->status == 'Finalizado') Pagado @elseif($itemData->status == 'Pago_Verificado' && ($itemData->status_recepcion == 'Recibido' || $itemData->status_recepcion == 'VALIDADO' || $itemData->status_recepcion == 'recepcion tradicional')) Pago En Proceso @elseif($itemData->status = 'Pago_Verificado' && $itemData->status_recepcion == 'En Espera') Enviado @elseif($itemData->status = 'Pago_Verificado') Para Envío @else {{$itemData->status}}@endif</td>
                        <td style="font-size: 13px">
                            @switch($itemData->status)
                                @case('Verificando_Pago')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->arch_pago}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    {{-- @can('contratos.edit')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Modificar Solicitud de Pago" href="{{route('pago-mod', ['id' => $itemData->id_folios])}}" >
                                            <i class="fa fa-wrench" aria-hidden="true"></i>
                                        </a>
                                    @endcan --}}
                                    @can('verificar_pago.create')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Verificar Pago" href="{{route('pago.verificarpago', ['id' => $itemData->id_contrato])}}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
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
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Formulario" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
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
                                    {{-- @can('contratos.edit')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Modificar Solicitud de Pago" href="{{route('pago-mod', ['id' => $itemData->id_folios])}}" >
                                            <i class="fa fa-wrench" aria-hidden="true"></i>
                                        </a>
                                    @endcan --}}
                                    @if($itemData->permiso_editar == TRUE)
                                        @can('folio.especialedit')
                                            <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Editar Folio" href="{{route('folio_especialedit', ['id' => $itemData->id_folios])}}">
                                                <i class="fa fa-wrench" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                    @endif
                                @break
                                @case('Finalizado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}","{{$itemData->arch_pago}}","jeje"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Resumen de Pago" href="{{route('mostrar-pago', ['id' => $itemData->id_contrato])}}" target="_blank">
                                        <i class="fa fa-money" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @if(is_null($itemData->arch_pago))
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Subir Solicitud de Pago Autorizada" id="pago_upload" name="pago_upload" data-toggle="modal" data-target="#Modaluploadpago" data-id='{{$itemData->id_folios}}'>
                                            <i class="fa fa-upload" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                @break
                            @endswitch
                        </td>
                        <td style="font-size: 13px">
                            @php
                                if(isset($itemData->soportes_instructor)){
                                    $itemData->soportes_instructor = json_decode($itemData->soportes_instructor);
                                    $itemData->archivo_ine = $itemData->soportes_instructor->archivo_ine;
                                    $itemData->archivo_rfc = $itemData->soportes_instructor->archivo_rfc;
                                    $itemData->archivo_bancario = $itemData->soportes_instructor->archivo_bancario;
                                    $itemData->archivo_domicilio = $itemData->soportes_instructor->archivo_domicilio;
                                }
                            @endphp
                            @if($itemData->status_recepcion == 'VALIDADO' && isset($itemData->recepcion))
                                Documentacion Validada
                                Digitalmente: {{$itemData->recepcion}}
                                @if(is_null($itemData->status_transferencia))
                                    @if($itemData->edicion_pago == TRUE)
                                        @can('contratos.create')
                                            <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-placement="top" @if($itemData->tipo_curso == 'CURSO') data-target="#agendarModalOrdinaria" @else data-target="#agendarModalCertificacion" @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$itemData->modinstructor}}","{{$itemData->edicion_pago}}"]'>
                                                EDITAR ENTREGA
                                            </a>
                                        @endcan
                                    @else
                                        @can('contrato.validate')
                                            <a class="btn btn-danger" id="retornar_fisico" name="retornar_fisico" data-toggle="modal" data-placement="top" data-target="#retornarRecepcionModal" data-id='{{$itemData->id_contrato}}'>
                                                Retorno o Edición
                                            </a>
                                        @endcan
                                    @endif
                                @else
                                    <br>En Layout de Pago
                                @endif
                            @else
                                @switch($itemData->status_recepcion)
                                    @case('En Espera')
                                        En Espera de Revisión Digital<br> enviada el: @if(isset($itemData->fecha_envio)){{$itemData->fecha_envio}} @else {{$itemData->updated_at}} @endif
                                        @can('contratos.create')
                                            {{-- <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-placement="top" @if($itemData->tipo_curso == 'CURSO') data-target="#agendarModalOrdinaria" @else data-target="#agendarModalCertificacion" @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","EN ESPERA"]'>
                                                AGENDAR ENTREGA
                                            </a> --}}
                                        @endcan
                                        @can('contrato.validate')
                                            <a class="btn btn-info" id="recepcionar" name="recepcionar" data-toggle="modal" @if($itemData->tipo_curso == 'CURSO') data-target="#validarRecepcionModalOrdinaria" @else data-target="#validarRecepcionModalCertificacion"  @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$calendario_entrega}}","{{$itemData->modinstructor}}"]'>
                                                Revisar Entrega Digital
                                            </a>
                                        @endcan
                                    @break
                                    @case('Rechazado')
                                        @can('contratos.create')
                                            <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-placement="top" @if($itemData->tipo_curso == 'CURSO') data-target="#agendarModalOrdinaria" @else data-target="#agendarModalCertificacion" @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$itemData->modinstructor}}"]'>
                                                AGENDAR ENTREGA
                                            </a>
                                        @endcan
                                            <p style="color: red;">{{$itemData->observacion_rechazo_recepcion}}</p>
                                    @break
                                    @case('VALIDADO')
                                        Documentación Validada
                                        Fecha de Cita: {{$itemData->fecha_agenda}}
                                        @can('contrato.validate')
                                            <a class="btn btn-info" id="recepcionar_fisico" name="recepcionar_fisico" data-toggle="modal" data-placement="top" data-target="#recepcionModal" data-id='{{$itemData->id_contrato}}'>
                                                Confirmar Recepción
                                            </a>
                                        @endcan
                                    @break
                                    @case('No Recibido')
                                        No Recibido Fisicamente
                                        @can('contratos.create')
                                            <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-placement="top" @if($itemData->tipo_curso == 'CURSO') data-target="#agendarModalOrdinaria" @else data-target="#agendarModalCertificacion" @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$itemData->modinstructor}}"]'>
                                                AGENDAR ENTREGA
                                            </a>
                                        @endcan
                                    @break
                                    @case('recepcion tradicional')
                                        LA ENTREGA SERA POR RECEPCION TRADICIONAL
                                        @if(is_null($itemData->arch_contrato))
                                            @can('contratos.create')
                                                <a class="btn btn-info" id="subir_contrato_rezagado" name="subir_contrato_rezagado" data-toggle="modal" data-placement="top" data-target="#subirContratoRezagadoModal" data-id='["{{$itemData->id_contrato}}"]'>
                                                    SUBIR CONTRATO
                                                </a>
                                            @endcan
                                        @endif
                                        @if($itemData->edicion_pago == TRUE)
                                            @can('contratos.create')
                                                <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-placement="top" @if($itemData->tipo_curso == 'CURSO') data-target="#agendarModalOrdinaria" @else data-target="#agendarModalCertificacion" @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$itemData->modinstructor}}","{{$itemData->edicion_pago}}"]'>
                                                    EDITAR ENTREGA
                                                </a>
                                            @endcan
                                        @endif
                                    @break
                                    @default
                                        @can('contratos.create')
                                            <a class="btn btn-info" id="agendar_recep" name="agendar_recep" data-toggle="modal" data-placement="top" @if($itemData->tipo_curso == 'CURSO') data-target="#agendarModalOrdinaria" @else data-target="#agendarModalCertificacion" @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$itemData->modinstructor}}"]'>
                                                AGENDAR ENTREGA
                                            </a>
                                        @endcan
                                    @break
                                @endswitch
                            @endif
                            @if ($itemData->status_recepcion == 'recepcion tradicional')
                                @can('contrato.validate')
                                    <a class="btn btn-danger" id="retornar_fisico" name="retornar_fisico" data-toggle="modal" data-placement="top" data-target="#retornarRecepcionModal" data-id='{{$itemData->id_contrato}}'>
                                        Retorno o Edición
                                    </a>
                                @endcan
                            @endif
                        </td>
                        @if (!is_null($itemData->status_recepcion))
                            <td style="font-size: 13px">
                                <a class="btn btn-success" id="verdocs" name="verdocs" data-toggle="modal" @if($itemData->tipo_curso == 'CURSO') data-target="#validarRecepcionModalOrdinaria" @else data-target="#validarRecepcionModalCertificacion"  @endif data-id='["{{$itemData->id_contrato}}","{{$itemData->arch_solicitud_pago}}","{{$itemData->archivo_bancario}}","{{$itemData->arch_mespecialidad}}","{{$itemData->pdf_curso}}","{{$itemData->doc_validado}}","{{$itemData->arch_factura}}","{{$itemData->arch_factura_xml}}","{{$itemData->arch_contrato}}","{{$itemData->archivo_ine}}","{{$itemData->arch_asistencia}}","{{$itemData->arch_calificaciones}}","{{$itemData->arch_evidencia}}","{{$calendario_entrega}}","{{$itemData->status_recepcion}}","{{$itemData->modinstructor}}"]'>
                                    Ver
                                </a>
                                <a class="btn btn-success" title="Descargar Documentación" href="{{route('downloadRarPagos', ['id_contrato' => $itemData->id_contrato])}}">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        @endif
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
<!-- Modal validar Documentacion ordinaria-->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade" id="validarRecepcionModalOrdinaria">
    <div class="modal-dialog modal-xl" style="width: 90%;">
        <form method="POST" action="{{ route('confirmar-entrega-fisica') }}" id="confirmar_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@can('contrato.validate')¿Confirmar Documentación digital para Entrega Fisica? @endcan @can('contratos.create')Vista de Documentación @endcan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <p><b>Validación de Documentos Digitales</b></p><br>
                    <table  class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i id="solpa_iconxv" class="fas fa-minus-circle" style="vertical-align:middle;"></i></td>
                                <td id="td1v" style="text-align: left; vertical-align: left; font-size: 12px;">1.- Solicitud de Pago</td>
                                <td><a class="nav-link" target="_blanks" title="Solicitud de Pago Firmada" id="show_solpav" name="show_solpav">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check-circle text-success" style="vertical-align:middle;"></i></td>
                                <td id="td5v" style="text-align: left; vertical-align: left; font-size: 12px;">5.- Validación de Suficiencia Presupuestal</td>
                                <td><a class="nav-link" target="_blanks" title="Validación de Suficiencia Presupuestal" id="show_validacion_suprev" name="show_validacion_suprev">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle; background-color:white;" width='10px;'><i class="fas fa-check-circle text-success" style="vertical-align:middle;"></i></td>
                                <td id="td9v" style="text-align: left; vertical-align: left; font-size: 12px;">9.- Identificación de Instructor</td>
                                <td><a class="nav-link" target="_blanks" title="Identificación de Instructor" id="show_identificacionv" name="show_identificacionv">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check-circle text-success" style="vertical-align:middle;"></i></td>
                                <td id="td2v" style="text-align: left; vertical-align: left; font-size: 12px;">2.- Cuenta Bancaria del Instructor</td>
                                <td><a class="nav-link" target="_blanks" title="Cuenta Bancaria del Instructor" id="show_cuenta_bancariav" name="show_cuenta_bancariav">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle;" width='10px;'><i id="asistencias_iconxv" class="fas fa-minus-circle" style="vertical-align:middle;"></i></td>
                                <td id="td10v" style="text-align: left; vertical-align: left; font-size: 12px;">6.- Lista de Asistencias</td>
                                <td><a class="nav-link" target="_blanks" title="Lista de Asistencias" id="show_asistenciasv" name="show_asistenciasv">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle;" width='10px;' class="fac_pdf_ord">
                                    <i id="factura_iconxv" class="fas fa-minus-circle" style="vertical-align:middle;"></i>
                                </td>
                                <td id="td6v" style="text-align: left; vertical-align: left; font-size: 12px;" class="fac_pdf_ord">
                                    6.- Factura PDF
                                </td>
                                <td class="fac_pdf_ord">
                                    <a class="nav-link" target="_blanks" title="Factura PDF" id="show_fact_pdfv" name="show_fact_pdfv">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check-circle text-success" style="vertical-align:middle;"></i></td>
                                <td id="td3v" style="text-align: left; vertical-align: left; font-size: 12px;">3.- Validación de Instructor</td>
                                <td><a class="nav-link" target="_blanks" title="Validación de Instructor" id="show_validacion_instructorv" name="show_validacion_instructorv">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle;" width='10px;'><i id="evidencia_iconxv" class="fas fa-minus-circle" style="vertical-align:middle;"></i></td>
                                <td id="td11v" style="text-align: left; vertical-align: left; font-size: 12px;">7.- Reporte de Evidencias Fotográficas</td>
                                <td><a class="nav-link" target="_blanks" title="Reporte de Evidencias Fotográficas" id="show_evidencia_fotograficav" name="show_evidencia_fotograficav">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle;" width='10px;' class="fac_xml_ord">
                                    <i id="facturaxml_iconxv" class="fas fa-minus-circle" style="vertical-align:middle;"></i>
                                </td>
                                <td id="td7v" style="text-align: left; vertical-align: left; font-size: 12px;" class="fac_xml_ord">
                                    7.- Factura XML
                                </td>
                                <td class="fac_xml_ord">
                                    <a class="nav-link" target="_blanks" title="Factura XML" id="show_fact_xmlv" name="show_fact_xmlv">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check-circle text-success" style="vertical-align:middle;"></i></td>
                                <td id="td4v" style="text-align: left; vertical-align: left; font-size: 12px;">4.- ARC-01/02</td>
                                <td><a class="nav-link" target="_blanks" title="ARC-01" id="show_arc01v" name="show_arc01v">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>

                                <td style="vertical-align:middle;" width='10px;'><i id="contrato_iconxv" class="fas fa-minus-circle" style="vertical-align:middle;"></i></td>
                                <td id="td8v" style="text-align: left; vertical-align: left; font-size: 12px;">8.- Contrato</td>
                                <td><a class="nav-link" target="_blanks" title="Contrato Firmada" id="show_contratov" name="show_contratov">
                                    <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                </a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <input id="id_contrato_agendav" name="id_contrato_agendav" hidden>
                @can('contrato.validate')
                    <div style="text-align:center" class="form-group" id="div_val_ordinario">
                        <p>Si validas podras asignar la fecha deseada a este registro.</p>
                        <button id="rechazar_recepcion" style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="button" class="btn" data-toggle="modal" data-target="#rechazar_entregaModal">Rechazar Entrega Digital</button>
                        <button id="validar_cita" style="text-align: right; font-size: 10px;" type="button" class="btn btn-danger" data-toggle="modal" data-target="#validarCitaModal">Validar Entrega Digital</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="background-color: #12322B; color: white;" class="btn" data-dismiss="modal">Cerrar</button>
                    </div>
                @endcan
            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal validar Documentacion certificacion-->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade" id="validarRecepcionModalCertificacion">
    <div class="modal-dialog modal-xl" style="width: 90%;">
        <form method="POST" action="{{ route('confirmar-entrega-fisica') }}" id="confirmar_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@can('contrato.validate')¿Confirmar Documentación digital para Entrega Fisica? @endcan @can('contratos.create')Vista de Documentación @endcan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <p st>Vista de Documentos</p>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Documentación</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <table  class="table table-bordered">
                                <caption>Documentos para Recepción</caption>
                                <tbody>
                                    <tr>
                                        <td id="td1vc" style="text-align: left; vertical-align: left; font-size: 12px;">1.- Solicitud de Pago</td>
                                        <td><a class="nav-link" target="_blanks" title="Solicitud de Pago Firmada" id="show_solpavc" name="show_solpavc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>
                                        <td id="td5vc" style="text-align: left; vertical-align: left; font-size: 12px;">5.- Validación de Suficiencia Presupuestal</td>
                                        <td><a class="nav-link" target="_blanks" title="Validación de Suficiencia Presupuestal" id="show_validacion_suprevc" name="show_validacion_suprevc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>

                                        <td id="td7vc" style="text-align: left; vertical-align: left; font-size: 12px;" class="fac_xml_cer">
                                            9.- Factura XML
                                        </td>
                                        <td class="fac_xml_cer">
                                            <a class="nav-link" target="_blanks" title="Factura XML" id="show_fact_xmlvc" name="show_fact_xmlvc">
                                                <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="td2vc" style="text-align: left; vertical-align: left; font-size: 12px;">2.- Cuenta Bancaria del Instructor</td>
                                        <td><a class="nav-link" target="_blanks" title="Cuenta Bancaria del Instructor" id="show_cuenta_bancariavc" name="show_cuenta_bancariavc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>
                                        <td id='td10vc' style="text-align: left; vertical-align: left; font-size: 12px;">6.- Lista de Calificaciones</td>
                                        <td><a class="nav-link" target="_blanks" title="Lista de Calificaciones" id="show_calificacionesvc" name="show_calificacionesvc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>
                                        <td id="td6vc" style="text-align: left; vertical-align: left; font-size: 12px;" class="fac_pdf_cer">
                                            10.- Factura PDF
                                        </td>
                                        <td class="fac_pdf_cer">
                                            <a class="nav-link" target="_blanks" title="Factura PDF" id="show_fact_pdfvc" name="show_fact_pdfvc">
                                                <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="td3vc" style="text-align: left; vertical-align: left; font-size: 12px;">3.- Validación de Instructor</td>
                                        <td><a class="nav-link" target="_blanks" title="Validación de Instructor" id="show_validacion_instructorvc" name="show_validacion_instructorvc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>

                                        <td id="td9vc" style="text-align: left; vertical-align: left; font-size: 12px;">7.- Identificación de Instructor</td>
                                        <td><a class="nav-link" target="_blanks" title="Identificación de Instructor" id="show_identificacionvc" name="show_identificacionvc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>
                                    </tr>
                                    <tr>
                                        <td id="td4vc" style="text-align: left; vertical-align: left; font-size: 12px;">4.- ARC-01/02</td>
                                        <td><a class="nav-link" target="_blanks" title="ARC-01" id="show_arc01vc" name="show_arc01vc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>
                                        <td id="td8vc" style="text-align: left; vertical-align: left; font-size: 12px;">8.- Contrato</td>
                                        <td><a class="nav-link" target="_blanks" title="Contrato Firmada" id="show_contratovc" name="show_contratovc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div style="text-align:center" class="form-group" id="div_val_certificacion">
                    <input id="id_contrato_agendavc" name="id_contrato_agendavc" hidden>
                    @can('contrato.validate')
                        <p>Si validas podras asignar la fecha deseada a este registro.</p>
                        <button id="rechazar_recepcionvc" style="text-align: left; font-size: 10px;" type="button" class="btn btn-danger" data-toggle="modal" data-target="#rechazar_entregaModal">Rechazar Entrega</button>
                        <button id="validar_citavc" style="text-align: right; font-size: 10px;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#validarCitaModal">Agendar Entrega Fisica</button>
                    @endcan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal Rechazar Entrega de Documentacion-->
<div class="modal fade" id="rechazar_entregaModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('rechazar-entrega-fisica') }}" id="rechazar_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Rechazar Entrega Fisica?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <p>Si confirmas el rechazo se hara el cambio de manera permanente.</p>
                        <p style="text-align: left; padding-left: 15%;"><small>Observacion de Rechazo</small></p>
                        <textarea name="observacion_rechazo" id="observacion_rechazo" cols="50" rows="5" required></textarea><br>
                        <input id="id_contrato_entrega_rechazo" name="id_contrato_entrega_rechazo" hidden>
                        <button style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="button" class="btn" data-dismiss="modal">Cancelar Rechazo</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" >Rechazar</button>
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
<!-- Modal Confirmar Fecha de Entrega de Documentacion-->
<div class="modal fade" id="validarCitaModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('validar-cita-fisica') }}" id="validar_cita">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Agendar Cita de Entrega Fisica?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <p>Si confirmas la fecha de cita se hara el cambio de manera permanente.</p>
                        {{-- <p style="text-align: center; padding-left: 0%;"><small>Fecha de Entrega Fisica</small></p>
                        <input type="date" name="fecha_confirmada" id="fecha_confirmada" hidden> --}}
                        {{-- <input style="text-align: center" type="text" name="mostrar_fecha" id="mostrar_fecha" readonly><br><br><br> --}}
                        <input id="id_contrato_cita" name="id_contrato_cita" hidden>
                        <button style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="button" class="btn" data-dismiss="modal">Cancelar</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" onclick="cerrarValidarCitaModal()" >Confirmar</button>
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
<!-- Modal Agendar Entrega de Documentacion ordinaria-->
<div class="modal fade bs-example-modal-lg" id="agendarModalOrdinaria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="width: 90%;">
        <form method="POST" action="{{ route('agendar-entrega-pago') }}" id="agendar_entregaOrdinaria" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-body" style="text-align:center;">
                    <h1 class="modal-title"><b>Carga de Documentación Digital</b></h1><br>
                    <table  class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="vertical-align:middle;" width='5%;'><i id="solpa_icon" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td1" style="text-align: left; vertical-align: middley; font-size: 12px; width: 30%;">1.- Solicitud de Pago</td>
                                <td style="width: 15%; text-align: left;">
                                    <div style="display: inline-block">
                                        <a class="nav-link" target="_blanks" title="Solicitud de Pago Firmada" id="show_solpa" name="show_solpa">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" style="margin: 0px !important;" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="solpa_pdf" name="solpa_pdf" hidden>
                                        <label for="solpa_pdf" id="solpa_pdf_label">
                                            <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                                &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                            </a>
                                            <span id="solpa_name">Subir</span>
                                        </label>
                                    </div>
                                </td>
                                <td style="vertical-align:middle;" width='5%'><i id="factura_icon" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td6" style="text-align: left; vertical-align: middley; font-size: 12px; width: 30%;">
                                    6.- Factura PDF
                                </td>
                                <td style="width: 15%; text-align: left;">
                                    <div style="display: inline-block;">
                                        <a class="nav-link" target="_blanks" title="Factura PDF" id="show_fact_pdf" name="show_fact_pdf">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block;">
                                        <input style='display:none;' type="file" accept="application/pdf" id="factura_pdf" name="factura_pdf" hidden>
                                        <label for="factura_pdf">
                                        <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                            &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                        </a>
                                        <span id="factura_name">Subir</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td2" style="text-align: left; vertical-align: middley; font-size: 12px;">2.- Cuenta Bancaria</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Cuenta Bancaria del Instructor" id="show_cuenta_bancaria" name="show_cuenta_bancaria">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;"><i id="contratof_icon" class="fas fa-times text-danger"></td>
                                <td id="td8" style="text-align: left; vertical-align: middley; font-size: 12px;">8.- Contrato</td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block">
                                        <a class="nav-link" target="_blanks" title="Contrato Firmada" id="show_contrato" name="show_contrato">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="contratof_pdf" name="contratof_pdf" hidden>
                                        <label for="contratof_pdf" id=contratof_pdf_label>
                                        <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                            &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                        </a>
                                        <span id="contratof_name">Subir</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td3" style="text-align: left; vertical-align: middley; font-size: 12px;">3.- Validación de Instructor</td>
                                <td style="text-align: left;">
                                        <a class="nav-link" target="_blanks" title="Validación de Instructor" id="show_validacion_instructor" name="show_validacion_instructor">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td9" style="text-align: left; vertical-align: middley; font-size: 12px;">9.- Identificación Oficial</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Identificación de Instructor" id="show_identificacion" name="show_identificacion">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td4" style="text-align: left; vertical-align: middley; font-size: 12px;">4.- ARC-01/02</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="ARC-01" id="show_arc01" name="show_arc01">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'><i id="asistencias_icon" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td10" style="text-align: left; vertical-align: middley; font-size: 12px;">10.- Lista de Asistencias</td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block">
                                        <a class="nav-link" target="_blanks" title="Lista de Asistencias" id="show_asistencias" name="show_asistencias">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="asistencias_pdf" name="asistencias_pdf" hidden>
                                        <label for="asistencias_pdf" id="asistencias_pdf_label">
                                        <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                            &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                        </a>
                                        <span id="asistencias_name">Subir</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td5" style="text-align: left; vertical-align: middley; font-size: 12px;">5.- Suficiencia Presupuestal</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Validación de Suficiencia Presupuestal" id="show_validacion_supre" name="show_validacion_supre">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'><i id="evidencia_fotografica_icon" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td11" style="text-align: left; font-size: 12px;">11.- Reporte de Evidencias</td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block">
                                        <a class="nav-link" target="_blanks" title="Reporte de Evidencias Fotográficas" id="show_evidencia_fotografica" name="show_evidencia_fotografica">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="evidencia_fotografica_pdf" name="evidencia_fotografica_pdf" hidden>
                                        <label for="evidencia_fotografica_pdf" id="evidencia_fotografica_pdf_label">
                                        <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                            &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                        </a>
                                        <span id="evidencia_fotografica_name">Subir</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i id="facturaxml_icon" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id="td7" style="text-align: left; vertical-align: middley; font-size: 12px;">
                                    7.- Factura XML
                                </td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block">
                                        <a class="nav-link" target="_blanks" title="Factura XML" id="show_fact_xml" name="show_fact_xml">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/xml" id="factura_xml" name="factura_xml" hidden>
                                        <label for="factura_xml">
                                        <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                            &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                        </a>
                                        <span id="factura_xml_name">Subir</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="text-align:right; display:inline-block; width: 60%;" class="form-group">
                        <input id="id_contrato_agenda" name="id_contrato_agenda" hidden>
                        <input id="tipo_envio" name="tipo_envio" hidden>
                        <button id='guardar' style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="submit" class="btn" value="guardar" onclick="guardado(this.value)">Guardar</button>
                        <button id='guardarenviar' style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" value="guardar_enviar" onclick="guardado(this.value)">Guardar y Enviar</button>
                    </div>
                    <div style="text-align:right; display:inline-block; width:30%;">
                        <button type="button" class="btn" style="text-align: right; background-color: #12322B; color: white;" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal Agendar Entrega de Documentacion certificacion-->
<div class="modal fade bs-example-modal-lg" id="agendarModalCertificacion" tabindex="-1" role="dialog" aria-hidden="true">v
    <div class="modal-dialog modal-xl" style="width: 90%;">
        <form method="POST" action="{{ route('agendar-entrega-pago') }}" id="agendar_entrega" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-body" style="text-align:center;">
                    <h1 class="modal-title"><b>Carga de Documentación Digital</b></h1><br>
                    <table  class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i id="solpa_iconc" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td1c' style="text-align: left; vertical-align: middle; font-size: 12px;">1.- Solicitud de Pago</td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block;">
                                        <a class="nav-link" target="_blanks" title="Solicitud de Pago Firmada" id="show_solpac" name="show_solpac">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block;">
                                        <input style='display:none;' type="file" accept="application/pdf" id="solpa_pdfc" name="solpa_pdfc" hidden>
                                        <label for="solpa_pdfc" id="solpa_pdfc_label">
                                            <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                                &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                            </a>
                                            <span id="solpa_namec">Subir</span>
                                        </label>
                                    </div>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'>
                                    {{-- <i id="factura_iconc" class="fas fa-check text-success" style="vertical-align:middle;"></i> --}}
                                </td>
                                <td id='td6c' style="text-align: left; vertical-align: middle; font-size: 12px;">
                                    {{-- 6.- Factura PDF --}}
                                </td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block;">
                                        {{-- <a class="nav-link" target="_blanks" title="Factura PDF" id="show_fact_pdfc" name="show_fact_pdfc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a> --}}
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="factura_pdfc" name="factura_pdfc" hidden>
                                        <label for="factura_pdfc">
                                            {{-- <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                                &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                            </a> --}}
                                            {{-- <span id="factura_namec">Subir</span> --}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td2c' style="text-align: left; vertical-align: middle; font-size: 12px;">2.- Cuenta Bancaria del Instructor</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Cuenta Bancaria del Instructor" id="show_cuenta_bancariac" name="show_cuenta_bancariac">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'>
                                    {{-- <i id="facturaxml_iconc" class="fas fa-check text-success" style="vertical-align:middle;"></i> --}}
                                </td>
                                <td id='td7c' style="text-align: left; vertical-align: middle; font-size: 12px;">
                                    {{-- 7.- Factura XML --}}
                                </td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block;">
                                        <a class="nav-link" target="_blanks" title="Factura XML" id="show_fact_xmlc" name="show_fact_xmlc">
                                            {{-- <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i> --}}
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="factura_xmlc" name="factura_xmlc" hidden>
                                        <label for="factura_xmlc">
                                            {{-- <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                                &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                            </a> --}}
                                            {{-- <span id="factura_xml_namec">Subir</span> --}}
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td3c' style="text-align: left; vertical-align: middle; font-size: 12px;">3.- Validación de Instructor</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Validación de Instructor" id="show_validacion_instructorc" name="show_validacion_instructorc">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'><i id="contratof_iconc" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td8c' style="text-align: left; vertical-align: middle; font-size: 12px;">8.- Contrato</td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block;">
                                        <a class="nav-link" target="_blanks" title="Contrato Firmada" id="show_contratoc" name="show_contratoc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="contratof_pdfc" name="contratof_pdfc" hidden>
                                        <label for="contratof_pdfc" id="contratof_pdfc_label">
                                            <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                                &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                            </a>
                                            <span id="contratof_namec">Subir</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td4c' style="text-align: left; vertical-align: middle; font-size: 12px;">4.- ARC-01/02</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="ARC-01" id="show_arc01c" name="show_arc01c">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td9c' style="text-align: left; vertical-align: middle; font-size: 12px;">9.- Identificación de Instructor</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Identificación de Instructor" id="show_identificacionc" name="show_identificacionc">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;" width='10px;'><i class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td5c' style="text-align: left; vertical-align: middle; font-size: 12px;">5.- Validación de Suficiencia Presupuestal</td>
                                <td style="text-align: left;">
                                    <a class="nav-link" target="_blanks" title="Validación de Suficiencia Presupuestal" id="show_validacion_suprec" name="show_validacion_suprec">
                                        <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                                <td style="vertical-align:middle;" width='10px;'><i id="calificaciones_iconc" class="fas fa-check text-success" style="vertical-align:middle;"></i></td>
                                <td id='td10c' style="text-align: left; vertical-align: middle; font-size: 12px;">10.- Lista de Calificaciones</td>
                                <td style="text-align: left;">
                                    <div style="display: inline-block;">
                                        <a class="nav-link" target="_blanks" title="Lista de Calificaciones" id="show_calificacionesc" name="show_calificacionesc">
                                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div style="display: inline-block">
                                        <input style='display:none;' type="file" accept="application/pdf" id="calificaciones_pdfc" name="calificaciones_pdfc" hidden>
                                        <label for="calificaciones_pdfc" id="calififcaciones_pdfc_label">
                                            <a class="btn px-1 py-1 mr-0" style="background-color: #12322B; color: white;">
                                                &nbsp; <i class="fa fa-cloud-upload fa-3x"></i> &nbsp;
                                            </a>
                                            <span id="calificaciones_namec">Subir</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="text-align:right; display:inline-block; width: 60%;" class="form-group">
                        <input id="id_contrato_agendac" name="id_contrato_agendac" hidden>
                        <input id="tipo_envioc" name="tipo_envioc" hidden>
                        <button id='guardar' style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="submit" class="btn" value="guardar" onclick="guardado(this.value)">Guardar</button>
                        <button id='guardarenviar' style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" value="guardar_enviar" onclick="guardado(this.value)">Guardar y Enviar</button>
                    </div>
                    <div style="text-align:right; display:inline-block; width:30%;">
                        <button type="button" class="btn" style="text-align: right; background-color: #12322B; color: white;" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal Entrega Fisica de Documentacion-->
<div class="modal fade" id="recepcionModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Confirmar Recepción de Documentos Fisicos?</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="text-align:center">
                <div style="text-align:center" class="form-group">
                    <p>Si confirmas o rechazas la recepcion se hara el cambio de manera permanente.</p>
                    {{-- <input id="id_contrato_cita" name="id_contrato_cita" hidden> --}}
                    <button id="rechazar_recepcion_fisica" style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="button" class="btn" data-toggle="modal" data-target="#rechazar_RecepcionModal">No Recibido</button>
                    <button id="confirmar_recepcion_fisica" style="text-align: right; font-size: 10px;" type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmar_RecepcionModal">Recibido</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- END -->
<!-- Modal Confirmar Entrega Fisica de Documentacion-->
<div class="modal fade" id="confirmar_RecepcionModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('recibido-entrega-fisica') }}" id="norecibir_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Recibir Entrega Fisica?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <p>¿Esta Seguro de la Recepción de Documentos?</p>
                        <input id="id_contrato_entrega" name="id_contrato_entrega" hidden>
                        <button style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="button" class="btn btn" data-dismiss="modal">Cancelar</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" >Confirmar Recepción</button>
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
<!-- Modal Rechazar Entrega Fisica de Documentacion-->
<div class="modal fade" id="rechazar_RecepcionModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('norecibido-entrega-fisica') }}" id="norecibir_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿No Recibir Entrega Fisica?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <p>¿Esta Seguro de la NO Recepción de Documentos?</p>
                        <input id="id_contrato_noentrega" name="id_contrato_noentrega" hidden>
                        <button style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="button" class="btn" data-dismiss="modal">Cancelar</button>
                        <button style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger" >Confirmar No Recepción</button>
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
<!-- Modal Retornar Entrega Fisica de Documentacion-->
<div class="modal fade" id="retornarRecepcionModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('edicion-entrega-fisica') }}" id="editar_entrega">
            @csrf
            <div class="modal-content">
                <div class="modal-body" style="text-align:center">
                    <div style="text-align:center" class="form-group">
                        <p>Retornar o Editar Documentación</p>
                        <p style="text-align: left; padding-left: 15%;"><small>Observación</small></p>
                        <textarea name="observacion_retorno" id="observacion_retorno" cols="50" rows="5" required></textarea><br>
                        <br>
                        <p><b>Movimiento a Realizar</b></p>
                        <button id="editar_btn" style="text-align: left; font-size: 10px; background-color: #12322B; color: white;" type="submit" class="btn">EDITAR</button>
                        <input id="id_contrato_retorno_recepcion" name="id_retorno_recepcion" hidden>
                        <button id="retornar_btn" style="text-align: right; font-size: 10px;" type="submit" class="btn btn-danger">RETORNAR</button>
                        <button type="button" class="btn"  style="background-color: #12322B; color: white;" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
            {{-- retorno-entrega-fisica --}}
            <div class="modal-footer">

            </div>
        </form>
    </div>
</div>
<!-- END -->
<!-- Modal Subir Contrato Rezagado-->
<div class="modal fade" id="subirContratoRezagadoModal" role="dialog">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" action="{{ route('subir-contrato-rezagado') }}" id="doc_pago">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Contrato</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="text-align:center">
                    <p>Subir archivo de contrato firmado:</p>
                    <div class="form-group col-md-2"></div>
                    <div style="text-align:center" class="form-group col-md-10">
                        <input type="file" accept="application/pdf" id="contrato_rezagado_doc" name="contrato_rezagado_doc" placeholder="Archivo PDF">
                        <input id="idcontrato_rezagado" name="idcontrato_rezagado" hidden>

                    </div>
                    <button type="submit" class="btn btn-danger" >Subir Archivo</button>
                    <br>
                </div>
                <div class="modal-footer">
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
<script src="{{ asset("js/solicitud/pagoIndex.js")}}"></script>
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
            $('#divunidades').prop("class", "")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
        }
        if(inputText == 'ENTREGADO FISICAMENTE')
        {
            $('#divstat').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
            $('#divunidades').prop("class", "")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
        }
      }

    $('#Modaluploadpago').on('show.bs.modal', function(event){
        // console.log('hola');
        var button = $(event.relatedTarget);
        var id = button.data('id');
        document.getElementById('idfolpa').value = id;
    });

    $('#validarRecepcionModalOrdinaria').on('show.bs.modal', function(event){
        // console.log('test');
        var button = $(event.relatedTarget);
        var attrid = button.attr('data-id');
        var id = JSON.parse(attrid)
        if(typeof id[13] !== 'undefined'){
            var fecha = id[13];
            var fechaTexto = fecha.toString();
        }

        $('#div_val_ordinario').removeClass('d-none d-print-none');
        $('#div_val_ordinario').addClass('form-group');

        document.getElementById('id_contrato_agendav').value = id[0];
        setAnchorHrefs(id, true, true);
        $('#rechazar_recepcion').data('id', id[0]);
        $('#validar_cita').data('id', [id[0], fechaTexto]);
        // if(typeof id[13] !== 'undefined'){console.log('a')
        //     $('#div_val_ordinario').removeClass('form-group');
        //     $('#div_val_ordinario').addClass('d-none d-print-none');
        // }
        // else{

        // }

        //By Jose Luis Valid Reporte fotografico / Mostrar documento firmado electronicamente en caso de que exisitiera
        //de lo contrario mostrar el documento de manera tradicional
        let data = { "_token": $("meta[name='csrf-token']").attr("content"), "id": id['0'] }
        $.ajax({
            type:"post",
            url: "{{ route('supre.busqueda.reportefoto') }}",
            data: data,
            dataType: "json",
            success: function (response) {
                if(response.id_curso != ''){
                    const repoFotograficoLink = document.getElementById('show_evidencia_fotograficav');
                    const repoFotUrl = "/reportefoto/pdf/" + response.id_curso;
                    repoFotograficoLink.href = repoFotUrl;
                    document.getElementById('td11v').style.color = "black";
                }
            }
        });

        var datos = {valor: id['0']};
        var url = '/efirma/busqueda';
        var request = $.ajax ({
            url: url,
            method: 'POST',
            data: datos,
            dataType: 'json'
        });
            request.done(( respuesta) =>
        {
            respuesta.forEach(element => {
                console.log(element);
                switch (element['tipo_archivo']) {
                    case 'Contrato':
                        // Obtener el elemento <a> por su id
                        const contratovLink = document.getElementById('show_contratov');
                        // Construir la URL dinámica utilizando la ruta y el ID correspondiente
                        const contratovUrl = "/contrato/" + element['id_contrato'];
                        // Asignar la URL al atributo href
                        contratovLink.href = contratovUrl;
                        contratovLink.hidden = false;
                        document.getElementById('td8v').style.color = "black";
                    break;
                    case 'Lista de asistencia':
                        const asistenciavLink = document.getElementById('show_asistenciasv');
                        const asistenciavUrl = "/lista/asistencia/" + element['id_curso'];
                        asistenciavLink.href = asistenciavUrl;
                        asistenciavLink.hidden = false;
                        document.getElementById('td10v').style.color = "black";
                    break;
                    case 'Reporte fotografico':
                        const reportevLink = document.getElementById('show_evidencia_fotograficav');
                        const reportevUrl = "/reportefoto/pdf/" + element['id_curso'];
                        reportevLink.href = reportevUrl;
                        reportevLink.hidden = false;
                        document.getElementById('td11v').style.color = "black";
                    break;
                    case 'Solicitud Pago':
                        const solpavLink = document.getElementById('show_solpav');
                        const solpavUrl = "/contrato/solicitud-pago/pdf/" + element['id_folios'];
                        solpavLink.href = solpavUrl;
                        solpavLink.hidden = false;
                        document.getElementById('td1v').style.color = "black";
                    break;
                    default:
                    break;
                }

            });
            // console.log(respuesta); show_contrato contratof_pdf(para subir)
        });

        if(id[14] != 'HONORARIOS') {
            var e1 = document.getElementsByClassName("fac_pdf_ord");
            var e2 = document.getElementsByClassName("fac_xml_ord");
            // Itera sobre los elementos y ocúltalos
            for (var i = 0; i < e1.length; i++) {
                e1[i].style.display = "none"; // Oculta el elemento
                e2[i].style.display = "none"; // Oculta el elemento

            }
        }

    });

    $('#validarRecepcionModalCertificacion').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var attrid = button.attr('data-id');
        var id = JSON.parse(attrid)
        if(typeof id[13] !== 'undefined'){
            var fecha = id[13];
            var fechaTexto = fecha.toString();
        }
        // console.log(fecha);


        $('#div_val_certificacion').removeClass('d-none d-print-none');
        $('#div_val_certificacion').addClass('form-group');

        // var fechaTexto = fecha.toString();
        // console.log(id);
        document.getElementById('id_contrato_agendavc').value = id[0];
        setAnchorHrefs(id, false, true);
        $('#rechazar_recepcionvc').data('id', id[0]);
        $('#validar_citavc').data('id', [id[0],fechaTexto]);
        console.log(id[12])
        if(typeof id[13] !== 'undefined'){
            $('#div_val_certificacion').removeClass('form-group');
            $('#div_val_certificacion').addClass('d-none d-print-none');
        }

        var datos = {valor: id['0']};
        var url = '/efirma/busqueda';
        var request = $.ajax ({
            url: url,
            method: 'POST',
            data: datos,
            dataType: 'json'
        });
            request.done(( respuesta) =>
        {
            respuesta.forEach(element => {
                // console.log(element);
                switch (element['tipo_archivo']) {
                    case 'Contrato':
                        // Obtener el elemento <a> por su id
                        const contratovcLink = document.getElementById('show_contratovc');
                        // Construir la URL dinámica utilizando la ruta y el ID correspondiente
                        const contratovcUrl = "/contrato/" + element['id_contrato'];
                        // Asignar la URL al atributo href
                        contratovcLink.href = contratovcUrl;
                        contratovcLink.hidden = false;
                        document.getElementById('td8vc').style.color = "black";
                    break;
                    case 'Lista de calificaciones':
                        const calificacionesvcLink = document.getElementById('show_calificacionesvc');
                        const calificacionevcsUrl = "/lista/calificacion/" + element['id_curso'];
                        calificacionesvcLink.href = calificacionevcsUrl;
                        calificacionesvcLink.hidden = false;
                        document.getElementById('td10vc').style.color = "black";
                    break;
                    case 'Solicitud Pago':
                        const solpavcLink = document.getElementById('show_solpavc');
                        const solpavcUrl = "/contrato/solicitud-pago/pdf/" + element['id_folios'];
                        solpavcLink.href = solpavcUrl;
                        solpavcLink.hidden = false;
                        document.getElementById('td1vc').style.color = "black";
                    break;
                    default:
                    break;
                }

            });
            // console.log(respuesta); show_contrato contratof_pdf(para subir)
        });

        if(id[13] != 'HONORARIOS') {
            var e1 = document.getElementsByClassName("fac_pdf_cer");
            var e2 = document.getElementsByClassName("fac_xml_cer");
            // Itera sobre los elementos y ocúltalos
            for (var i = 0; i < e1.length; i++) {
                e1[i].style.display = "none"; // Oculta el elemento
                e2[i].style.display = "none"; // Oculta el elemento

            }
        }

    });

    $('#rechazar_entregaModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        // console.log(id)
        document.getElementById('id_contrato_entrega_rechazo').value = id;
    });

    $('#validarCitaModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id[0])
        document.getElementById('id_contrato_cita').value = id[0];
        $('#fecha_confirmada').val(id[1]);
        $('#mostrar_fecha').val(id[1]);
    });

    $('#agendarModalOrdinaria').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id);
        document.getElementById('id_contrato_agenda').value = id[0];
        if(id[14] == 1) {
            $('#guardarenviar').hide();
        }
        setAnchorHrefs(id, true, false);

        var datos = {valor: id['0']};
        var url = '/efirma/busqueda';
        var request = $.ajax ({
            url: url,
            method: 'POST',
            data: datos,
            dataType: 'json'
        });
            request.done(( respuesta) =>
        {
            respuesta.forEach(element => {
                // console.log(element);
                switch (element['tipo_archivo']) {
                    case 'Contrato':
                        // Obtener el elemento <a> por su id
                        const contratoLink = document.getElementById('show_contrato');
                        // Construir la URL dinámica utilizando la ruta y el ID correspondiente
                        const contratoUrl = "/contrato/" + element['id_contrato'];
                        // Asignar la URL al atributo href
                        contratoLink.href = contratoUrl;
                        contratoLink.hidden = false;
                        $('#contratof_pdf_label').attr('hidden', true);
                        $('#contratof_pdf').prop('required', false);
                        $('#contratof_icon').attr('class', "fas fa-check text-success");
                        document.getElementById('td8').style.color = "black";
                    break;
                    case 'Lista de asistencia':
                        const asistenciaLink = document.getElementById('show_asistencias');
                        const asistenciaUrl = "/lista/asistencia/" + element['id_curso'];
                        asistenciaLink.href = asistenciaUrl;
                        asistenciaLink.hidden = false;
                        $('#asistencias_pdf_label').attr('hidden', true);
                        $('#asistencias_pdf').prop('required', false);
                        $('#asistencias_icon').attr('class', "fas fa-check text-success");
                        document.getElementById('td10').style.color = "black";
                    break;
                    case 'Reporte fotografico':
                        const reporteLink = document.getElementById('show_evidencia_fotografica');
                        const reporteUrl = "/reportefoto/pdf/" + element['id_curso'];
                        reporteLink.href = reporteUrl;
                        reporteLink.hidden = false;
                        $('#evidencia_fotografica_pdf_label').attr('hidden', true);
                        $('#evidencia_fotografica_pdf').prop('required', false);
                        $('#evidencia_fotografica_icon').attr('class', "fas fa-check text-success");
                        document.getElementById('td11').style.color = "black";
                    break;
                    case 'Solicitud Pago':
                        // console.log('aaa');
                        const solpaLink = document.getElementById('show_solpa');
                        const solpaUrl = "/contrato/solicitud-pago/pdf/" + element['id_folios'];
                        solpaLink.href = solpaUrl;
                        solpaLink.hidden = false;
                        $('#solpa_pdf_label').attr('hidden', true);
                        $('#solpa_pdf').prop('required', false);
                        $('#solpa_icon').attr('class', "fas fa-check text-success");
                        document.getElementById('td1').style.color = "black";
                    break;
                    default:
                    break;
                }

            });
            // console.log(respuesta); show_contrato contratof_pdf(para subir)
        });
    });

    $('#agendarModalCertificacion').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        // console.log(id);
        document.getElementById('id_contrato_agendac').value = id[0];
        setAnchorHrefs(id, false, false);

        if(id[14] == 1) {
            $('#guardarenviar').hide();
        }

        var datos = {valor: id['0']};
        var url = '/efirma/busqueda';
        var request = $.ajax ({
            url: url,
            method: 'POST',
            data: datos,
            dataType: 'json'
        });
            request.done(( respuesta) =>
        {
            respuesta.forEach(element => {
                console.log(element);
                switch (element['tipo_archivo']) {
                    case 'Contrato':
                        // Obtener el elemento <a> por su id
                        const contratocLink = document.getElementById('show_contratoc');
                        // Construir la URL dinámica utilizando la ruta y el ID correspondiente
                        const contratocUrl = "/contrato/" + element['id_contrato'];
                        // Asignar la URL al atributo href
                        contratocLink.href = contratocUrl;
                        contratocLink.hidden = false;
                        $('#contratof_pdfc_label').attr('hidden', true);
                        $('#contratof_pdfc').prop('required', false);
                        $('#contratof_iconc').attr('class', "fas fa-check text-success");
                        document.getElementById('td8c').style.color = "black";
                    break;
                    case 'Lista de calificaciones':
                        const calificacionesLink = document.getElementById('show_calificacionesc');
                        const calificacionesUrl = "/lista/calificacion/" + element['id_curso'];
                        calificacionesLink.href = calificacionesUrl;
                        calificacionesLink.hidden = false;
                        $('#asistencias_pdfc_label').attr('hidden', true);
                        $('#asistencias_pdfc').prop('required', false);
                        $('#calificaciones_iconc').attr('class', "fas fa-check text-success");
                        document.getElementById('td10c').style.color = "black";
                    break;
                    case 'Solicitud Pago':
                        const solpacLink = document.getElementById('show_solpac');
                        const solpacUrl = "/contrato/solicitud-pago/pdf/" + element['id_folios'];
                        solpacLink.href = solpacUrl;
                        solpacLink.hidden = false;
                        $('#solpa_pdfc_label').attr('hidden', true);
                        $('#solpa_pdfc').prop('required', false);
                        $('#solpa_iconc').attr('class', "fas fa-check text-success");
                        document.getElementById('td1c').style.color = "black";
                    break;
                    default:
                    break;
                }

            });
            // console.log(respuesta); show_contrato contratof_pdf(para subir)
        });
    });

    $('#recepcionModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id)
        $('#rechazar_recepcion_fisica').data('id', id);
        $('#confirmar_recepcion_fisica').data('id', id);
    });

    $('#confirmar_RecepcionModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        console.log(id)
        document.getElementById('id_contrato_entrega').value = id;
    });

    $('#rechazar_RecepcionModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        // console.log(id)
        document.getElementById('id_contrato_noentrega').value = id;
    });

    $('#retornarRecepcionModal').on('show.bs.modal', function(event){
        var button = $(event.relatedTarget);
        var id = button.data('id');
        // console.log(id)
        document.getElementById('id_contrato_retorno_recepcion').value = id;
    });
    $('#subirContratoRezagadoModal').on('show.bs.modal', function(event){
        // console.log('hola');
        var button = $(event.relatedTarget);
        var id = button.data('id');
        document.getElementById('idcontrato_rezagado').value = id;
    });

    function setAnchorHrefs(idx, ordinario, validacion) {
        let anchors = null;
        let variables = null;
        let icons = null;
        let td = null;

        if(ordinario) {
            if(validacion) {
                anchors = ['#show_solpav', '#show_cuenta_bancariav', '#show_validacion_instructorv', '#show_arc01v',
                        '#show_validacion_suprev', '#show_fact_pdfv', '#show_fact_xmlv',  '#show_contratov',
                        '#show_identificacionv', '#show_asistenciasv', '#show_calificacionesv', '#show_evidencia_fotograficav'];

                variables = ['#solpa_pdfv', , , , , '#factura_pdfv', '#factura_xmlv', '#contratof_pdfv', , '#asistencias_pdfv',
                        '#calificaciones_pdfv', '#evidencia_fotografica_pdfv'];
                icons = ['#solpa_iconv', , , , , '#factura_iconv', '#facturaxml_iconv', '#contratof_iconv', , '#asistencias_iconv',
                        '#calificaciones_iconv', '#evidencia_iconv'];
                td = 'v';
            } else {
                anchors = ['#show_solpa', '#show_cuenta_bancaria', '#show_validacion_instructor', '#show_arc01',
                        '#show_validacion_supre', '#show_fact_pdf', '#show_fact_xml',  '#show_contrato',
                        '#show_identificacion', '#show_asistencias', '#show_calificaciones', '#show_evidencia_fotografica'];

                variables = ['#solpa_pdf', , , , , '#factura_pdf', '#factura_xml', '#contratof_pdf', , '#asistencias_pdf',
                        '#calificaciones_pdf', '#evidencia_fotografica_pdf'];
                icons = ['#solpa_icon', , , , , '#factura_icon', '#facturaxml_icon', '#contratof_icon', , '#asistencias_icon',
                        '#calificaciones_icon', '#evidencia_fotografica_icon'];
                td = '';
            }
        } else {
            if(validacion) {
                anchors = ['#show_solpavc', '#show_cuenta_bancariavc', '#show_validacion_instructorvc', '#show_arc01vc',
                        '#show_validacion_suprevc', '#show_fact_pdfvc', '#show_fact_xmlvc',  '#show_contratovc',
                        '#show_identificacionvc', '#show_asistenciasvc', '#show_calificacionesvc', '#show_evidencia_fotograficavc'];

                variables = ['#solpa_pdfvc', , , , , '#factura_pdfvc', '#factura_xmlvc', '#contratof_pdfvc', , '#asistencias_pdfvc',
                        '#calificaciones_pdfvc', '#evidencia_fotografica_pdfvc'];

                icons = ['#solpa_iconvc', , , , , '#factura_iconvc', '#facturaxml_iconvc', '#contratof_iconvc', , '#asistencias_iconvc',
                        '#calificaciones_iconvc', '#evidencia_iconvc'];
                td = 'vc';
            } else {
                anchors = ['#show_solpac', '#show_cuenta_bancariac', '#show_validacion_instructorc', '#show_arc01c',
                        '#show_validacion_suprec', '#show_fact_pdfc', '#show_fact_xmlc',  '#show_contratoc',
                        '#show_identificacionc','#show_asistenciasc', '#show_calificacionesc', '#show_evidencia_fotograficac'];

                //variables = ['#solpa_pdfc', , , , , '#factura_pdfc', '#factura_xmlc', '#contratof_pdfc', , '#asistencias_pdfc',
                        //'#calificaciones_pdfc', '#evidencia_fotografica_pdfc'];
                //By Jose Luis / Quitar factura pdf y xml
                variables = ['#solpa_pdfc', , , , , '#contratof_pdfc', , '#asistencias_pdfc',
                        '#calificaciones_pdfc', '#evidencia_fotografica_pdfc'];

                icons = ['#solpa_iconc', , , , , '#factura_iconc', '#facturaxml_iconc', '#contratof_iconc', , '#asistencias_iconc',
                        '#calificaciones_iconc', '#evidencia_iconc'];
                td = 'c';
            }
        }

        if (ordinario) {
            anchors.splice(10, 1); // Remove #show_calificaciones from the anchors array
            idx.splice(11, 1); // Remove #show_calificaciones link from the id array
            variables.splice(10, 1);
            icons.splice(10, 1);
        } else {
            anchors.splice(9, 1); // Remove #show_asistencias from the anchors array
            idx.splice(10, 1); // Remove #show_asistencias link from the id array
            variables.splice(9, 1);
            icons.splice(9, 1);
            anchors.splice(10, 1); // Remove #show_evidencia_fotografica from the anchors array
            idx.splice(11, 1); // Remove #show_evidencia_fotografica link from the aidarray
            variables.splice(10, 1);
            icons.splice(10, 1);
        }

        // if(idx[12] == 'ASIMILADOS A SALARIOS') {
        //     anchors.splice(5,1);
        //     anchors.splice(5,1);
        //     idx.splice(6,1);
        //     idx.splice(6,1);
        //     variables.splice(5,1);
        //     variables.splice(5,1);
        //     icons.splice(5,1);
        //     icons.splice(5,1);
        // }

        for (let i = 0; i < anchors.length; i++) {
            const href = idx[i+1];
            if (idx[i+1] != "") {
                    $(anchors[i]).attr('href', href);
                    $(anchors[i]).attr('hidden', false);
                    document.getElementById('td'+[i+1]+td).style.color = "black";

                    if (idx[idx.length - 1] == "EN ESPERA"){
                        $(variables[i]+'_label').attr('hidden', true);
                        dnd = anchors[i].substring(1);
                        // $(anchors[i]).parent().parent().css("background-color", "lightgray");
                    } else {
                        // $(anchors[i]).parent().parent().css("background-color", "white");
                        $(variables[i]+'_label').attr('hidden', false);
                        $(variables[i]).prop('required', false);
                    }

                    $(icons[i]).attr('class', "fas fa-check text-success");
            } else {
                $(variables[i]).prop('required', true);

                if((anchors[i] == '#show_fact_pdf' || anchors[i] == '#show_fact_xml' || anchors[i] == '#show_fact_pdfc' || anchors[i] == '#show_fact_xmlc') && (idx[12] == 'ASIMILADOS A SALARIOS' || idx[13] == 'ASIMILADOS A SALARIOS' || idx[11] == 'ASIMILADOS A SALARIOS')) {
                    $(variables[i]).prop('required', false);
                    document.getElementById('td'+[i+1]+td).style.display = 'none';
                    document.getElementById('td'+[i+1]+td).previousElementSibling.style.display = 'none';
                    document.getElementById('td'+[i+1]+td).nextElementSibling.style.display = 'none';
                }

                $(anchors[i]).attr('hidden', true);
                document.getElementById('td'+[i+1]+td).style.color = "red";
                $(variables[i]+'_label').attr('hidden', false);
                $(icons[i]).attr('class', "fas fa-times text-danger");
                // $(anchors[i]).parent().parent().css("background-color", "white");

            }
        }
    }

    const fileInputs = document.querySelectorAll('input[type="file"]');

    // Agrega un escucha de eventos para cada input de tipo archivo
    fileInputs.forEach(function(fileInput) {
        fileInput.addEventListener("change", function() {
            let icon_changer;
            if(fileInput.id == "factura_xml" || fileInput.id == "factura_xmlc")
            {
                // Verifica si el id contiene "c" después de "xml"
                if (fileInput.id.indexOf("xmlc") != -1) {
                    icon_changer = "facturaxml_iconc";
                } else {
                    icon_changer = "facturaxml_icon";
                }
            }
            else
            {
                icon_changer = fileInput.id.replace("pdf", "icon");
            }
            document.getElementById(fileInput.parentElement.parentElement.previousElementSibling.id).style.color = "black";
            document.getElementById(icon_changer).className = "fas fa-check text-success";
        });
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

function guardado(buttonValue) {
    // Código de la función aquí
    // console.log(buttonValue);
    var confirmacion = confirm("¿Está seguro de continuar?");
    document.getElementById('tipo_envio').value = buttonValue;
    document.getElementById('tipo_envioc').value = buttonValue;

    // Si el usuario hace clic en "Aceptar", continuar con el envío del formulario
    if (confirmacion) {
        return true;
    }
    // Si el usuario hace clic en "Cancelar", detener el envío del formulario
    else {
        return false;
    }
}

function validando(event) {
    // Seleccionar el botón de envío del formulario
    var submitBtn = document.getElementById('submit-btn');

    // Cambiar el icono del botón a "fa-check-circle" y "text-success"
    var icon = document.getElementById('solpa_iconxv');
    icon.classList.remove('fa-minus');
    icon.classList.add('fa-check', 'text-success');

    // Esperar 6 segundos antes de enviar el formulario
    setTimeout(function() {
        // Enviar el formulario
        return true;
    }, 2000);
}

function cerrarValidarCitaModal() {
    // Cerrar el modal "validarCitaModal"
    $('#validarCitaModal').modal('hide');

    // Llamar a la función "validando()" después de cerrar el modal
    setTimeout(validando(event), 500);
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editar_entrega');
    const editarBtn = document.getElementById('editar_btn');
    const retornarBtn = document.getElementById('retornar_btn');

    editarBtn.addEventListener('click', function() {
        form.action = '{{ route("edicion-entrega-fisica") }}';
    });

    retornarBtn.addEventListener('click', function() {
        form.action = '{{ route("retorno-entrega-fisica") }}';
    });
});

</script>
@endsection
