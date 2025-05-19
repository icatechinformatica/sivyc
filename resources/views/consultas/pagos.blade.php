<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consulta de Pagos | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr td { font-size: 11px; }
    </style>
@endsection
@section('content')
    <div class="card-header">
        Consultas / Pagos de Instructores
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
            <div class="row form-inline">  
                <div class="d-flex flex-lg-row flex-column col-12 col-md-9 col-sm-12 justify-content-left">              
                    {{ Form::select('ejercicio', $anios, $request->ejercicio??null ,array('id'=>'ejercicio','class' => 'form-control  mr-sm-3')) }}
                    {{ Form::select('unidad', $unidades, $request->unidad??null,array('id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-3')) }}                
                    {{ Form::text('valor',$request->valor??null, ['id'=>'valor', 'class' => 'form-control mr-sm-3', 'placeholder' => 'INSTRUCTOR', 'title' => 'DATO DE BUSQUEDA','size' => 45]) }}
                    {{ Form::button('BUSCAR', ['id'=>'filtrar','class' => 'btn', 'value'=>'filtrar']) }}
                </div>
                <div class="d-flex flex-lg-row flex-column col-12 col-md-3 col-sm-12 justify-content-end">
                    @foreach ($estatus as $key => $value)
                        <div class="form-check d-flex mt-2 mr-4">
                            <input type="radio" class="form-check-input col-md-6" name="status" value="{{ $key }}" {{ $key == $status ? 'checked' : '' }}>
                            <label class="form-check-label col-md-6 mt-1" for="estatus{{ $key }}">
                                {{ $value }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="table-responsive p-0 m-0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th> 
                            <th class="small align-middle">CURSO</th>
                            <th class=" small">INSTRUCTOR</th>
                            <th class="text-center small align-middle" style="width: 180px">UNIDAD/AM</th>
                            <th class="text-right small align-middle">IMPORTE</th>                          
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
                                    <td class="p-2">{{ $item->curso }}</td>
                                    <td class="p-2">{{ $item->instructor }}</td>
                                    <td class="text-center p-2">{{ $item->unidad_capacitacion }}</td>      
                                    <td class=" text-right p-2">{{ number_format($item->importe_neto, 2, '.', ',') }}</td>                              
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
                            <td colspan="3"></td>
                            <td class=" text-right p-2" > TOTAL</td>
                            <td class=" text-right p-2">{{ number_format($subtotal, 2, '.', ',') }} </td>
                        </tr>                       
                        <tr>
                            <td colspan="5">
                                {{ $data->links() }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
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
         if ($('#num_layout').length){
            const miCajaDeTexto = document.getElementById('num_layout');
            const valorGuardado = localStorage.getItem('num_layout');
            if (valorGuardado) miCajaDeTexto.value = valorGuardado;
            miCajaDeTexto.addEventListener('input', function () {
                localStorage.setItem('num_layout', miCajaDeTexto.value);
            });
        }
        if($('#fecha_pago').length){
            const fecha_pago = document.getElementById('fecha_pago');
            const fechaGuardado = localStorage.getItem('fecha_pago');
            if (fechaGuardado) fecha_pago.value = fechaGuardado;
            fecha_pago.addEventListener('input', function () {
                localStorage.setItem('fecha_pago', fecha_pago.value);
            });
        }

        $(document).ready(function(){ 
            $('input[type=radio][name=status]').on('change', function() {
                $('#frm').attr('target', '_self').submit();
            });

            $('#unidad, #ejercicio, #valor').on('change', function() {
                if($('input[name="status"]:checked').length > 0){
                    $('#frm').attr('target', '_self').submit();  
                }                
            });            

            $("#excel" ).click(function(){
                 $('#frm').attr('action', "{{route('consultas.pagos.excel')}}");
                 $('#frm').attr('target', '_blank');
                 $('#frm').submit();
            });

            $("#filtrar" ).click(function(){
                 $('#frm').attr('action', "{{route('consultas.pagos')}}");
                 $('#frm').attr('target', '_self');
                 $('#frm').submit();
            });
        });
    </script>
@endsection
