<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Financieros Transferencia | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr td { font-size: 11px; }
    </style>
@endsection
@section('content')
    <div class="card-header">
        Solicitudes / Transferencia BANCARIA
    </div>
    <div class="card card-body p-4 " style=" min-height:450px;">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif       
        {{ Form::open(['method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data','accept-charset'=>'UTF-8']) }}
            @csrf
            <div class="row form-inline pl-4">
                {{ Form::select('ejercicio', $anios, $request->ejercicio ,array('id'=>'ejercicio','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('unidad', $unidades, $request->unidad ,array('id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('status', ['PENDIENTE'=>'1. PENDIENTES','MARCADO'=>'2. MARCADOS','GENERADO'=>'3. GENERADOS','PAGADO'=>' 4. PAGADOS', 'FINALIZADO'=>' 5. FINALIZADO'], $request->status ,['id'=>'status','placeholder' => '- ESTATUS -','class' => 'form-control  mr-sm-3']) }}
                {{ Form::text('valor',$request->valor, ['id'=>'valor', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT/#CONTRATO/#SOLIC. PAGO/INSTRUCTOR', 'title' => 'DATO DE BUSQUEDA','size' => 45]) }}
                {{ Form::button('FILTRAR', ['id'=>'filtrar','class' => 'btn', 'value'=>'filtrar']) }}
                {{ Form::button('EXCEL', ['id'=>'excel','class' => 'btn', 'value'=>'excel']) }}
            </div>


            <div class="table-responsive p-0 m-0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center small align-middle">UNIDAD</th>
                            <th class="text-center small">CONTRATO / SOL. PAGO/ <br/>FEC. SOL /FEC. PAGO</th>
                            <th class="text-center small align-middle" style="width: 180px">CLAVE / CURSO</th>
                            <th class="text-center small align-middle" style="width: 180px">INSTRUCTOR / RFC</th>
                            <th class="text-center small">BANCO / CUENTA/CLABE</th>
                            <th class="text-center small align-middle">IMPORTE</th>
                            <th class="text-center small align-middle">FOLIO FISCAL</th>
                            @can('transferencia.pagado')
                                <th class="text-center small">NÚM. TRANSF.</th>
                            @endcan
                            @can('transferencia.layout')
                                <th class="text-center small align-middle">FACTURA</th>
                                <th class="text-center small align-middle">#LAYOUT</th>
                                <th class="text-center small align-middle">ESTATUS</th>
                            @endcan
                            @can('transferencia.pagado')
                                <th class="text-center small align-middle">CONTRATO</th>
                                <th class="text-center small align-middle">ESTATUS</th>
                            @endcan

                            @can('transferencia.pagado')
                                <th class="text-center small align-middle">COMPROBANTE</th>
                                <th class="text-center small align-middle">EDITAR</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data)>0)
                            @php
                                $consec = 1;
                            @endphp
                            @foreach ($data as $item)                           
                                <tr>
                                    <td class="p-2">{{ $consec++ }}</td>
                                    <td class="p-2">{{$item->id}}-{{ $item->unidad_capacitacion }}</td>
                                    <td class="p-2">{{ $item->numero_contrato }} <br/> {{ $item->no_memo }} <br/>
                                        SOLICITADO: {{ date('d/m/Y', strtotime($item->solicitud_fecha)) }},
                                        @if($item->fecha_pago) PAGADO:{{ date('d/m/Y', strtotime($item->fecha_pago)) }} @endif
                                    </td>
                                    <td class="p-2">{{ $item->clave }} <br/> {{ $item->curso }}</td>
                                    <td class="p-2">{{ $item->instructor }} <br/>  RFC: {{ $item->rfc }} </td>
                                    <td class="p-2">{{ $item->banco }}: {{ $item->cuenta }} <br/> {{ $item->clabe }}</td>
                                    <td class=" text-right p-2">{{ number_format($item->importe_neto, 2, '.', ',') }}</td>
                                    <td class="p-2">{{ $item->folio_fiscal }}</td>
                                    @can('transferencia.pagado')
                                            <td class="p-2">{{ $item->no_pago }}</td>
                                    @endif
                                    @can('transferencia.layout')
                                        <td class="text-center p-2">
                                            @if($item->factura)
                                                <a class="nav-link" href="{{ $item->factura}}" target="_blank">
                                                    <i  class="far fa-file-pdf  fa-2x text-danger"></i>
                                                </a>
                                            @else
                                                <i  class="far fa-file-pdf  fa-2x text-muted pt-2"  title='ARCHIVO NO DISPONIBLE.'></i>
                                            @endif

                                        </td>
                                        <td class="text-center p-2">
                                            {{ $item->num_layout }}
                                        </td>
                                        <td class="text-center p-2">
                                            @if($item->status=='PENDIENTE' || $item->status=='MARCADO')
                                                <div class="form-check p-0 pt-2">
                                                    <input class="custom-check" type="checkbox" value="{{ $item->id }}" name="ids"   onchange=marcar({{$item->id}},$(this).prop("checked"),$(this),"MARCADO")  @if($item->status=='MARCADO'){{'checked'}} @endif >
                                                </div>
                                            @else
                                                {{ $item->status}}
                                            @endif
                                        </td>
                                    @endcan

                                    @can('transferencia.pagado')
                                        <td class="text-center p-2">
                                            @if( $item->contrato)
                                                <a class="nav-link" href="{{$item->contrato}}" target="_blank">
                                                    <i  class="far fa-file-pdf  fa-2x text-danger"></i>
                                                </a>
                                            @else
                                                <i  class="far fa-file-pdf  fa-2x text-muted pt-2"  title='ARCHIVO NO DISPONIBLE.'></i>
                                            @endif
                                        </td>
                                        <td class="text-center p-2">
                                            {{ $item->status}}
                                        </td>

                                    @endcan

                                    @can('transferencia.pagado')
                                            <td class="text-center p-2">
                                                @if( $item->arch_pago)
                                                    <a class="nav-link" href="{{$item->arch_pago}}" target="_blank">
                                                        <i  class="far fa-file-pdf  fa-2x text-danger"></i>
                                                    </a>
                                                @else
                                                    <i  class="far fa-file-pdf  fa-2x text-muted pt-2"  title='ARCHIVO NO DISPONIBLE.'></i>
                                                @endif
                                            </td>
                                            <td class="text-center p-2">
                                                <div class="form-check p-0 pt-2">
                                                    <input class="custom-check" type="checkbox" value="{{ $item->id }}" name="ids[]"   >
                                                </div>
                                            </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="16" >
                                    <b>NO SE ENCONTRARON REGISTROS</b>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    @if(count($data)>0)
                    <tfoot>
                        <tr>
                            <td colspan="16">
                                {{ $data->links() }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
        <div class="row form-inline justify-content-end">
                @csrf
                @can('transferencia.pagado')
                        {{ Form::button('REGISTRAR PAGOS', ['id'=>'registrar_pagos','class' => 'btn btn-danger']) }}
                @endcan
                @can('transferencia.layout')
                    @if (count($data) > 0 && in_array($request->status, ["PENDIENTE", "MARCADO", "GENERADO"]))
                        {{ Form::select('cuenta_retiro', $cuentas_retiro, null ,array('id'=>'cuenta_retiro','class' => 'form-control  mr-sm-3')) }}
                        {{ Form::label('numero_layout', 'Núm. Layout Sugerido:', ['class' => 'mr-2']) }}
                        @if($request->status == "GENERADO")
                            {{ Form::select('num_layout', $numeros_layouts, $folio??null ,array('id'=>'num_layout','class' => 'form-control  mr-sm-3')) }}
                        @else
                            {{ Form::text('num_layout',$folio??null, ['id'=>'num_layout', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT', 'title' => '#ROME','size' => 25]) }}
                        @endif

                        @if($request->status == "GENERADO")
                                {{ Form::select('movimiento', ['DESHACER'=>'DESHACER GENERADOS', 'PAGADO' =>'SUBIR PAGADOS'], '', ['id'=>'movimiento','class' => 'form-control  col-md-3 m-1', 'placeholder'=>'- MOVIMIENTOS -'] ) }}
                                {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn btn-danger']) }}                                    
                         @endif
                         {{ Form::button('GENERAR', ['id'=>'generar','class' => 'btn']) }}
                    @endif
                @endcan
           </div>


            {{-- Modal REGISTRAR PAGOS --}}
            <div class="modal fade" id="modalRegistrarPagos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                {{-- color en el modal --}}
                <div class="modal-dialog modal-sm modal-notify modal-danger" id="" role="document">
                <!--Content-->
                <div class="modal-content text-center">
                    <!--Header-->
                    <div class="modal-header d-flex justify-content-center">
                        {{-- Mensaje para el modal --}}
                    <p class="heading font-weight-bold">REGISTRAR PAGOS</p>
                    </div>
                    <!--Body-->
                    <div class="modal-body">
                        <div class="alert alert-danger alert-dismissible fade show pl-2 text-left" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>Alerta! </strong><span> Si las solicitudes tienen registro de pago, se efectuará el reemplazo.</span>
                        </div>
                        <div class="form-group">
                            {{ Form::text('numero_pago',old('numero_pago'), ['id'=>'numero_pago', 'class' => 'form-control mt-2', 'placeholder' => '#TRANSFERENCIA', 'title' => '#TRANSFERENCIA']) }}
                            {{ Form::text('folio_fiscal',old('folio_fiscal'), ['id'=>'folio_fiscal', 'class' => 'form-control mt-2', 'placeholder' => 'FOLIO FISCAL', 'title' => 'FOLIO FISCAL']) }}
                            {{ Form::date('fecha_pago',old('fecha_pago'), ['id'=>'fecha_pago', 'class' => 'form-control mt-2', 'placeholder' => 'dd/mm/aaaa', 'title' => 'FECHA DE PAGO']) }}
                            {{ Form::textarea('descripcion',old('descripcion'), ['id'=>'descripcion', 'class' => 'form-control mt-2', 'placeholder' => 'DESCRIPCION', 'title' => '#LAYOUT','rows' => 3]) }}
                            <div class="mt-4" id="formUpPdf">
                                <input type="file" name="arch_pago" accept=".pdf" id="pdfInputDoc" style="display: none;" onchange="checkIcon('iconCheck', 'pdfInputDoc')">
                                <button class="btn-outline-danger btn-sm" onclick="event.preventDefault(); document.getElementById('pdfInputDoc').click();">
                                SUBIR ARCHIVO PDF <i class="fas fa-cloud-upload-alt fa-2x text-danger" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--Footer-->
                    <div class="modal-footer flex-center">
                        <button class="btn btn-danger" id="guardar" onclick="guardar()">GUARDAR</button>
                        <a type="button" class="btn btn-outline-danger waves-effect" id="" data-dismiss="modal">
                        <i class="fa fa-times fa-sm text-danger" aria-hidden="true"> </i>CANCELAR</a>
                    </div>
                </div>
                <!--/.Content-->
                </div>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
@section('script_content_js')
    <script>
         $(function(){
            //metodo
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
         });

        function checkIcon(idIcon, inputPdfId) {
                let iconIndic = document.getElementById(idIcon);
                let pdfInput = document.getElementById(inputPdfId);
                if (pdfInput.files.length > 0) {iconIndic.style.display = 'inline-block';}
      					else {iconIndic.style.display = 'none';}
        }

         $("#registrar_pagos").click(function () {
                $('#modalRegistrarPagos').modal('show');
            });

         //MANTENER VALORES DURANTE LA PAGINACION
         /*
         if ($('#num_layout').length){
            const miCajaDeTexto = document.getElementById('num_layout');
            const valorGuardado = localStorage.getItem('num_layout');
            if (valorGuardado) miCajaDeTexto.value = valorGuardado;
            miCajaDeTexto.addEventListener('input', function () {
                localStorage.setItem('num_layout', miCajaDeTexto.value);
            });
        }
            */
        if($('#fecha_pago').length){
            const fecha_pago = document.getElementById('fecha_pago');
            const fechaGuardado = localStorage.getItem('fecha_pago');
            if (fechaGuardado) fecha_pago.value = fechaGuardado;
            fecha_pago.addEventListener('input', function () {
                localStorage.setItem('fecha_pago', fecha_pago.value);
            });
        }


        function marcar(id, check, obj, status){
            if( $("#num_layout").val() || (status==="PAGADO" && $("#fecha_pago").val()!="")){
                $.ajax({
                    method: "POST",
                    url: "marcar",
                    data: {
                        id: id,
                        check: check,
                        estado: status,
                        num_layout: $("#num_layout").val(),
                        fecha_pago: $("#fecha_pago").val()

                    }
                })
                .done(function( msg ) { alert(msg); });
            }else{
                if(status=="PAGADO") alert("POR FAVOR, INGRESE LA FECHA DE PAGO.");
                else alert("Por favor, ingrese el NÚMERO DE LAYOUT, la opción se localiza en la parte inferior de la pantalla.");
                if(obj.prop('checked')) obj.prop('checked', false);
                else obj.prop('checked', true);
            }
        }

        $(document).ready(function(){
            $("#guardar" ).click(function(){
                if(confirm("Esta seguro de GUARDAR?")==true){
                    $('#frm').attr('target', '_self');
                    $('#frm').attr('action', "{{route('solicitudes.transferencia.pagado')}}"); $('#frm').submit();
                }
            });

            $("#aceptar" ).click(function(){
                if(confirm("Esta seguro de deshacer GENERADO?")==true){
                    $("#status").prop("selectedIndex", 2);
                    $('#frm').attr('target', '_self');
                    $('#frm').attr('action', "{{route('solicitudes.transferencia.deshacer')}}"); $('#frm').submit();
                }
            });


            $("#generar" ).click(function(){
                if(confirm("Esta seguro de generar layout?")==true){                    
                    $('#frm').attr('target', '_blank');
                    $('#frm').attr('action', "{{route('solicitudes.transferencia.generar')}}"); $('#frm').submit();
                
                    setTimeout(function () {
                        window.location = "{{ route('solicitudes.transferencia.index') }}?status=GENERADO&valor="+$("#valor").val();
                    }, 1000);

                }
            });

            $("#excel" ).click(function(){
                 $('#frm').attr('action', "{{route('solicitudes.transferencia.excel')}}");
                 $('#frm').attr('target', '_blank');
                 $('#frm').submit();
            });

            $("#filtrar" ).click(function(){
                 $('#frm').attr('action', "{{route('solicitudes.transferencia.index')}}");
                 $('#frm').attr('target', '_self');
                 $('#frm').submit();
            });
        });
    </script>
@endsection
