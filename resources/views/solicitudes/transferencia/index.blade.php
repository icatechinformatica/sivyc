<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Financieros Transferencia | SIVyC Icatech')
@section('content_script_css')            
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .form-check-input{
            width:22px;
            height:22px;
        }        
    </style> 
@endsection
@section('content')   
    <div class="card-header">
        Solicitudes / Transferencia BANCARIA
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif        
        {{ Form::open(['method' => 'post', 'id'=>'frmfiltrar', 'enctype' => 'multipart/form-data']) }}
            @csrf
            <div class="row form-inline">
                {{ Form::select('ejercicio', $ejercicio, $request->ejercicio ,array('id'=>'ejericio','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('unidad', $unidades, $request->unidad ,array('id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('status_transferencia', ['PENDIENTE'=>'1. PENDIENTES','MARCADO'=>'2. MARCADOS','GENERADO'=>'3. GENERADOS','PAGADO'=>' 4. PAGADOS'], $request->status_transferencia ,['id'=>'status_transferencia','placeholder' => '- ESTATUS -','class' => 'form-control  mr-sm-3']) }}
                {{ Form::text('valor',$request->valor, ['id'=>'valor', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT/#CONTRATO/#SOLIC. PAGO/INSTRUCTOR', 'title' => 'DATO DE BUSQUEDA','size' => 45]) }}        
                {{ Form::submit('FILTRAR', ['id'=>'filtrar','class' => 'btn', 'value'=>'filtrar']) }}                
            </div>
        {!! Form::close() !!}
            <div class="col-md-12table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">UNIDAD</th>
                            <th class="text-center">CONTRATO / SOL. PAGO</th>
                            <th class="text-center">CLAVE / CURSO</th>
                            <th class="text-center">INSTRUCTOR / RFC</th>                            
                            <th class="text-center">FOLIO FISCAL</th>                            
                            <th class="text-center">BANCO / CUENTA/CLABE</th>                            
                            <th class="text-center">IMPORTE</th>
                            <th class="text-center">FACTURA</th>                
                            <th class="text-center">#LAYOUT</th>                      
                            <th class="text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data)>0)
                            @php 
                                $consec = 1;                            
                            @endphp
                            @foreach ($data as $item)                
                                <tr>    
                                    <td>{{ $consec++ }}</td>
                                    <td>{{$item->id}}-{{ $item->unidad_capacitacion }}</td>
                                    <td>{{ $item->numero_contrato }} <br/> {{ $item->no_memo }}</td>
                                    <td>{{ $item->clave }} {{ $item->curso }}</td>
                                    <td>{{ $item->instructor }} <br/>  RFC: {{ $item->rfc }} </td>                                
                                    <td>{{ $item->folio_fiscal }}</td>                                
                                    <td>{{ $item->banco }}: {{ $item->cuenta }} <br/> {{ $item->clabe }}</td>
                                    <td>{{ $item->importe_neto }}</td>
                                    <td> 
                                        <a id="botonRIAC-ACRED" class="nav-link" href="{{ $item->factura}}" target="_blank">
                                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                                        </a>
                                    </td>
                                    <td>{{ $item->num_layout }}</td>
                                    <td class="text-center">
                                        @if($item->status=='PENDIENTE' || $item->status=='MARCADO')                                                                          
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $item->id }}" name="status"   onchange="marcar({{$item->id}},$(this).prop('checked'),$(this))"  @if($item->status=='MARCADO'){{'checked'}} @endif >                                
                                            </div>                                                                     
                                        @else 
                                            {{ $item->status}}
                                        @endif                                                          
                                    </td>
                                </tr>
                            @endforeach                                
                        @else
                            <tr>
                                <td class="text-center" colspan="11" >
                                    <b>NO SE ENCONTRARON REGISTROS</b>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                    </tfoot>
            </table>
        </div>
        @if(count($data)>0 AND ($request->status_transferencia == "PENDIENTE" OR $request->status_transferencia == "MARCADO" OR $request->status_transferencia == "GENERADO" ))
            {{ Form::open(['method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data','accept-charset'=>'UTF-8']) }}
                @csrf
                <div class="row form-inline justify-content-end">
                    {{ Form::select('cuenta_retiro', $cuentas_retiro, null ,array('id'=>'cuenta_retiro','class' => 'form-control  mr-sm-3')) }}
                    {{ Form::text('num_layout',$request->num_layout, ['id'=>'num_layout', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT', 'title' => '#LAYOUT','size' => 25]) }}        
                    @if($request->status_transferencia == "GENERADO")
                        {{ Form::select('movimiento', ['DESHACER'=>'DESHACER GENERADOS', 'PAGADO' =>'SUBIR PAGADOS'], '', ['id'=>'movimiento','class' => 'form-control  col-md-3 m-1', 'placeholder'=>'- MOVIMIENTOS -'] ) }} 
                        {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn btn-danger']) }}
                        {{ Form::button('GENERAR', ['id'=>'generar','class' => 'btn']) }}
                    @else
                        {{ Form::button('GENERAR', ['id'=>'generar','class' => 'btn']) }}
                    @endif                    
                </div>
            {!! Form::close() !!}
        @endif
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
        function marcar(id, status, obj){ 
            if( $("#num_layout").val()){
                $.ajax({
                    method: "POST", 
                    url: "marcar", 
                    data: { 
                        id: id,
                        estado: status,
                        num_layout: $("#num_layout").val()
                    }
                })
                //.done(function( msg ) { alert(msg); });   
            }else{
                alert("Por favor, ingrese el NÚMERO DE LAYOUT, la opción se localiza en la parte inferior de la pantalla.");
                if(obj.prop('checked')) obj.prop('checked', false); 
                else obj.prop('checked', true); 
            }
        }

        $(document).ready(function(){
            $("#aceptar" ).click(function(){ 
                if(confirm("Esta seguro de deshacer GENERADO?")==true){
                    $("#status_transferencia").prop("selectedIndex", 2);
                    $('#frm').attr('target', '_self');
                    $('#frm').attr('action', "{{route('solicitudes.transferencia.deshacer')}}"); $('#frm').submit();                
                }
            });


            $("#generar" ).click(function(){ 
                if(confirm("Esta seguro de generar layout?")==true){                    
                    $("#status_transferencia").prop("selectedIndex", 3);
                    $('#frm').attr('target', '_blank');
                    $('#frm').attr('action', "{{route('solicitudes.transferencia.generar')}}"); $('#frm').submit(); 
                    setTimeout(function() {                
                        $('#frm').attr('action', "{{route('solicitudes.transferencia.index')}}"); $('#frmfiltrar').submit(); 
                    }, 1000);
                }
            });                                
        });       
    </script>
@endsection
