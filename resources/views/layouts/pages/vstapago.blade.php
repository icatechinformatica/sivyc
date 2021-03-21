@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Pagos | SIVyC Icatech')
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
        @if ($message =  Session::get('info'))
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Solicitudes de Pagos</h2>
                    {!! Form::open(['route' => 'pago-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_pago" class="form-control mr-sm-2" id="tipo_pago">
                            <option value="">BUSQUEDA POR TIPO</option>
                            <option value="no_contrato">N° DE CONTRATO</option>
                            <option value="unidad_capacitacion">UNIDAD CAPACITACIÓN</option>
                            <option value="fecha_firma">FECHA</option>
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
                        {!! Form::text('busquedaPorPago', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                        </div>
                        <Div id="divstat" name="divstat">
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
        <table  id="table-instructor" class="table table-bordered table-responsive-md Datatables">
            <caption>Lista de Contratos en Espera</caption>
            <thead>
                <tr>
                    <th scope="col">N°. Contrato</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Unidad de Capacitación</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ultima Modificación de Status</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contratos_folios as $itemData)
                    <tr>
                        <td>{{$itemData->numero_contrato}}</td>
                        <td>
                            @if($itemData->created_at != NULL)
                                <?php $d = $itemData->created_at->format('d'); $m = $itemData->created_at->format('m'); $y = $itemData->created_at->format('y'); ?>
                                {{$d}}/{{$m}}/{{$y}}
                            @endif
                        </td>
                        <td>{{$itemData->unidad_capacitacion}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>{{$itemData->fecha_status}}</td>
                        <td>
                            @switch($itemData->status)
                                @case('Verificando_Pago')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    @can('verificar_pago.create')
                                        <a class="btn btn-success btn-circle m-1 btn-circle-sm" title="Verificar Pago" href="{{route('pago.verificarpago', ['id' => $itemData->id_contrato])}}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                    @endcan
                                @break
                                @case('Pago_Verificado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
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
                                @break
                                @case('Pago_Rechazado')
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @break
                                @case('Finalizado')
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="PDF" id="show_pdf" name="show_pdf" data-toggle="modal" data-target="#myModal" data-id='["{{$itemData->id_folios}}","{{$itemData->id_contrato}}","{{$itemData->docs}}","{{$itemData->id_supre}}","{{$itemData->status}}","{{$itemData->doc_validado}}"]'>
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Resumen de Pago" href="{{route('mostrar-pago', ['id' => $itemData->id_contrato])}}" target="_blank">
                                        <i class="fa fa-money" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Consulta de Validación" href="{{route('pago.historial-verificarpago', ['id' => $itemData->id_contrato])}}">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @break
                            @endswitch
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
                        <a class="btn btn-success" id="confirm_restart" name="confirm_restart" href="#">Aceptar</a>
                    </div>
                    <div class="form-group col-md-2"></div>
                </div>
            </div>
        </div>
    </div>
<!-- END -->
    <br>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/modals.js") }}"></script>
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
        if(inputText == 'N° DE CONTRATO')
        {
            $('#divstat').prop("class", "form-row d-none d-print-none")
        }
        else
        {
            $('#divstat').prop("class", "")
        }
        if(inputText == 'UNIDAD CAPACITACIÓN')
        {
            $('#divunidades').prop("class", "")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
        }
        else
        {
            $('#divcampo').prop("class", "")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
        }
        if(inputText == 'MES')
        {
            $('#divmes').prop("class", "")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divcampo').prop("class", "form-row d-none d-print-none")
        }
        else
        {
            $('#divcampo').prop("class", "")
            $('#divunidades').prop("class", "form-row d-none d-print-none")
            $('#divmes').prop("class", "form-row d-none d-print-none")
        }
      }

});
</script>
@endsection
