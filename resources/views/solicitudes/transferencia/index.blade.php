<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Financieros Transferencia | SIVyC Icatech')
@section('content_script_css')            
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
@endsection
@section('content')   
    <div class="card-header">
        Solicitudes / Transferencia BANCARIA
    </div>
    <div class="card card-body p-4 " style=" min-height:450px;">
        @if($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif        
        {{ Form::open(['method' => 'post', 'id'=>'frmfiltrar', 'enctype' => 'multipart/form-data']) }}
            @csrf
            <div class="row form-inline pl-4">
                {{ Form::select('ejercicio', $ejercicio, $request->ejercicio ,array('id'=>'ejericio','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('unidad', $unidades, $request->unidad ,array('id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('status_transferencia', ['PENDIENTE'=>'1. PENDIENTES','MARCADO'=>'2. MARCADOS','GENERADO'=>'3. GENERADOS','PAGADO'=>' 4. PAGADOS', 'FINALIZADO'=>' 5. FINALIZADO'], $request->status_transferencia ,['id'=>'status_transferencia','placeholder' => '- ESTATUS -','class' => 'form-control  mr-sm-3']) }}
                {{ Form::text('valor',$request->valor, ['id'=>'valor', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT/#CONTRATO/#SOLIC. PAGO/INSTRUCTOR', 'title' => 'DATO DE BUSQUEDA','size' => 45]) }}        
                {{ Form::submit('FILTRAR', ['id'=>'filtrar','class' => 'btn', 'value'=>'filtrar']) }}                
            </div>
        {!! Form::close() !!}
            <div class="table-responsive p-0 m-0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>UNIDAD</th>
                            <th class="text-center small">CONTRATO / SOL. PAGO</th>
                            <th class="text-center small">CLAVE / CURSO</th>
                            <th class="text-center small">INSTRUCTOR / RFC</th>                            
                            <th class="text-center small">FOLIO FISCAL</th>                            
                            <th class="text-center small">BANCO / CUENTA/CLABE</th>                            
                            <th class="text-center small">IMPORTE</th>
                            <th class="text-center small">FACTURA</th> 
                            @can('transferencia.layout')
                                <th class="text-center small">#LAYOUT</th>                     
                                <th class="text-center small">ESTATUS</th>    
                            @endcan
                            @can('transferencia.pagado')
                                <th class="text-center small">CONTRATO</th> 
                                <th class="text-center small">PAGADO</th>                            
                            @endcan
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
                                    <td class="text-center"> 
                                        @if($item->factura)
                                            <a class="nav-link" href="{{ $item->factura}}" target="_blank">
                                                <i  class="far fa-file-pdf  fa-2x text-danger"></i>
                                            </a>
                                        @else
                                            NO DISPONIBLE
                                        @endif
                                        
                                    </td>
                                    @can('transferencia.layout')
                                        <td>{{ $item->num_layout }}</td>
                                        <td class="text-center">
                                            @if($item->status=='PENDIENTE' || $item->status=='MARCADO')                                                                          
                                                <div class="form-check">
                                                    <input class="custom-check" type="checkbox" value="{{ $item->id }}" name="status"   onchange=marcar({{$item->id}},$(this).prop("checked"),$(this),"MARCADO")  @if($item->status=='MARCADO'){{'checked'}} @endif >                                
                                                </div>                                                                     
                                            @else 
                                                {{ $item->status}}
                                            @endif                                                          
                                        </td>        
                                    @endcan
                                    @can('transferencia.pagado')                           
                                        <td class="text-center"> 
                                            @if( $item->contrato)
                                                <a class="nav-link" href="{{$item->contrato}}" target="_blank">
                                                    <i  class="far fa-file-pdf  fa-2x text-danger"></i>
                                                </a>
                                            @else
                                                NO DISPONIBLE
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->status == "PENDIENTE" OR ($item->status == "PAGADO" AND !$request->num_layout))
                                                <div class="form-check">
                                                    <input class="custom-check" type="checkbox" value="{{ $item->id }}" name="status"   onchange=marcar({{$item->id}},$(this).prop("checked"),$(this),"PAGADO")  @if($item->status=='PAGADO'){{'checked'}} @endif >
                                                </div>                                                                     
                                            @else 
                                                {{ $item->status}}
                                            @endif                                                          
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach                                
                        @else
                            <tr>
                                <td class="text-center" colspan="13" >
                                    <b>NO SE ENCONTRARON REGISTROS</b>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                    </tfoot>
            </table>
        </div>
        @can('transferencia.layout')
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
        @endcan
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
        function marcar(id, check, obj, status){ 
            if( $("#num_layout").val() || status==="PAGADO"){ 
                $.ajax({
                    method: "POST", 
                    url: "marcar", 
                    data: { 
                        id: id,
                        check: check,
                        estado: status,
                        num_layout: $("#num_layout").val()
                        
                    }
                })
                .done(function( msg ) { alert(msg); });   
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
