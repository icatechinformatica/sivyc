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
                {{ Form::select('ejercicio', $anios, $request->ejercicio ,array('id'=>'ejercicio','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('unidad', $unidades, $request->unidad ,array('id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-3')) }}
                {{ Form::select('status_transferencia', ['PENDIENTE'=>'1. PENDIENTES','MARCADO'=>'2. MARCADOS','GENERADO'=>'3. GENERADOS','PAGADO'=>' 4. PAGADOS', 'FINALIZADO'=>' 5. FINALIZADO'], $request->status_transferencia ,['id'=>'status_transferencia','placeholder' => '- ESTATUS -','class' => 'form-control  mr-sm-3']) }}
                {{ Form::text('valor',$request->valor, ['id'=>'valor', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT/#CONTRATO/#SOLIC. PAGO/INSTRUCTOR', 'title' => 'DATO DE BUSQUEDA','size' => 45]) }}        
                {{ Form::button('FILTRAR', ['id'=>'filtrar','class' => 'btn', 'value'=>'filtrar']) }}
                {{ Form::button('EXCEL', ['id'=>'excel','class' => 'btn', 'value'=>'excel']) }}
            </div>
        {!! Form::close() !!}
            <div class="table-responsive p-0 m-0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>                            
                            <th class="text-center small align-middle">UNIDAD</th>
                            <th class="text-center small">CONTRATO / SOL. PAGO/ FEC. SOL /FEC. PAGO</th>
                            <th class="text-center small align-middle" style="width: 180px">CLAVE / CURSO</th>
                            <th class="text-center small align-middle" style="width: 180px">INSTRUCTOR / RFC</th>                            
                            <th class="text-center small align-middle">FOLIO FISCAL</th>                            
                            <th class="text-center small">BANCO / CUENTA/CLABE</th>                            
                            <th class="text-center small align-middle">IMPORTE</th>
                            <th class="text-center small align-middle">FACTURA</th> 
                            @can('transferencia.layout')
                                <th class="text-center small align-middle">#LAYOUT</th>
                                <th class="text-center small align-middle">ESTATUS</th>
                            @endcan
                            @can('transferencia.pagado')
                                <th class="text-center small align-middle">CONTRATO</th> 
                                <th class="text-center small align-middle">PAGADO</th>                            
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
                                    <td class="p-2">{{ $consec++ }}</td>                                    
                                    <td class="p-2">{{$item->id}}-{{ $item->unidad_capacitacion }}</td>
                                    <td class="p-2">{{ $item->numero_contrato }} <br/> {{ $item->no_memo }} <br/> 
                                        SOLICITADO: {{ date('d/m/Y', strtotime($item->solicitud_fecha)) }}, 
                                        @if($item->fecha_pago) PAGADO:{{ date('d/m/Y', strtotime($item->fecha_pago)) }} @endif
                                    </td>
                                    <td class="p-2">{{ $item->clave }} <br/> {{ $item->curso }}</td>
                                    <td class="p-2">{{ $item->instructor }} <br/>  RFC: {{ $item->rfc }} </td>                                
                                    <td class="p-2">{{ $item->folio_fiscal }}</td>                                
                                    <td class="p-2">{{ $item->banco }}: {{ $item->cuenta }} <br/> {{ $item->clabe }}</td>
                                    <td class=" text-right p-2">{{ $item->importe_neto }}</td>
                                    <td class="text-center p-2"> 
                                        @if($item->factura)
                                            <a class="nav-link" href="{{ $item->factura}}" target="_blank">
                                                <i  class="far fa-file-pdf  fa-2x text-danger"></i>
                                            </a>
                                        @else
                                            <i  class="far fa-file-pdf  fa-2x text-muted pt-2"  title='ARCHIVO NO DISPONIBLE.'></i>
                                        @endif
                                        
                                    </td>
                                    @can('transferencia.layout')                                        
                                        <td class="text-center p-2">
                                            {{ $item->num_layout }}
                                        </td>
                                        <td class="text-center p-2">
                                            @if($item->status_layout=='PENDIENTE' || $item->status_layout=='MARCADO')                                                                          
                                                <div class="form-check p-0 pt-2">
                                                    <input class="custom-check" type="checkbox" value="{{ $item->id }}" name="status"   onchange=marcar({{$item->id}},$(this).prop("checked"),$(this),"MARCADO")  @if($item->status_layout=='MARCADO'){{'checked'}} @endif >                                
                                                </div>                                                                     
                                            @else                                                 
                                                {{ $item->status_layout}}
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
                                            @if($item->status == "PENDIENTE" OR ($item->status == "PAGADO" AND !$request->num_layout))
                                                <div class="form-check p-0 pt-2">
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
                    @if(count($data)>0)
                    <tfoot>
                        <tr>
                            <td colspan='14'>
                                {{ $data->links() }}              
                            </td>
                        </tr>
                    </tfoot>
                    @endif
            </table>
        </div>
        <div class="row form-inline justify-content-end">
            {{ Form::open(['method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data','accept-charset'=>'UTF-8']) }}
                @csrf                          
                @can('transferencia.pagado') 
                    @if($request->status_transferencia == "PENDIENTE" )
                        {{ Form::date('fecha_pago',old('fecha_pago'), ['id'=>'fecha_pago', 'class' => 'form-control mr-sm-3', 'placeholder' => 'dd/mm/aaaa', 'title' => 'FECHA DE PAGO']) }} 
                    @endif
                @endcan
                @can('transferencia.layout')
                    @if(count($data)>0 AND ($request->status_transferencia == "PENDIENTE" OR $request->status_transferencia == "MARCADO" OR $request->status_transferencia == "GENERADO" ))
                    
                        {{ Form::select('cuenta_retiro', $cuentas_retiro, null ,array('id'=>'cuenta_retiro','class' => 'form-control  mr-sm-3')) }}
                        {{ Form::text('num_layout',old('num_layout'), ['id'=>'num_layout', 'class' => 'form-control mr-sm-3', 'placeholder' => '#LAYOUT', 'title' => '#LAYOUT','size' => 25]) }}        
                        @if($request->status_transferencia == "GENERADO")
                                {{ Form::select('movimiento', ['DESHACER'=>'DESHACER GENERADOS', 'PAGADO' =>'SUBIR PAGADOS'], '', ['id'=>'movimiento','class' => 'form-control  col-md-3 m-1', 'placeholder'=>'- MOVIMIENTOS -'] ) }} 
                                {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn btn-danger']) }}
                                {{ Form::button('GENERAR', ['id'=>'generar','class' => 'btn']) }}
                        @else
                                {{ Form::button('GENERAR', ['id'=>'generar','class' => 'btn']) }}
                         @endif                    
                
                    
                    @endif
                @endcan    
            {!! Form::close() !!}   
        </div>
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
            
            $("#excel" ).click(function(){
                 $('#frmfiltrar').attr('action', "{{route('solicitudes.transferencia.excel')}}"); 
                 $('#frmfiltrar').attr('target', '_blank');
                 $('#frmfiltrar').submit(); 
            });

            $("#filtrar" ).click(function(){
                 $('#frmfiltrar').attr('action', "{{route('solicitudes.transferencia.index')}}"); 
                 $('#frmfiltrar').attr('target', '_self');
                 $('#frmfiltrar').submit(); 
            });
        });       
    </script>
@endsection
