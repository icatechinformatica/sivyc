<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Grupos- Recibos de Pago | SIVyC Icatech')
@section('content')       
    <div class="card-header">
        Recibos de Pago        
    </div>
    <div class="card card-body">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif
        {{ Form::open(['method' => 'post', 'id'=>'frm']) }}
            @csrf
            <div class="row form-inline">                 
                {{ Form::text('folio_grupo', $request->folio_grupo, ['id'=>'folio_grupo', 'class' => 'form-control mr-2', 'placeholder' => 'FOLIO DE GRUPO', 'aria-label' => 'FOLIO DE GRUPO', 'required' => 'required', 'size' => 30]) }}
                {{ Form::button('BUSCAR', ['id' => 'buscar','name' => 'BUSCAR', 'class' => 'btn', 'value' => 'BUSCAR']) }}
                
            </div>        
            @if($data)  
                <div class="row form-inline"> 
                    <div class="form-group col-md-6"> <h4>DEL CURSO</h4> </div>    
                    <div class="form-group col-md-6 justify-content-end ">                        
                        <h4 class="bg-light p-2">&nbsp; RECIBO No. &nbsp;<span class="bg-white p-1">&nbsp;<b>{{$data->uc}}</b> <b class="text-danger">{{$data->folio_recibo}}</b>&nbsp;</span> &nbsp;</h4>
                        @if($data->status_recibo == 'DISPONIBLE') 
                            <h4 class="text-center text-white p-2" style="background-color: #33A731;">&nbsp;DISPONIBLE &nbsp;</h4>
                        @else
                            <h4 class="bg-warning text-center p-2">&nbsp;ASIGNADO &nbsp;</h4>
                        @endif
                    </div>                    
                </div>                
                <div class="row bg-light" style="padding:35px; line-height: 1.5em;">
                    <div class="form-group col-md-12"><b> </b></div>
                    <div class="form-group col-md-12">    FOLIO GRUPO: <b>{{$data->folio_grupo}}</b></div>
                    <div class="form-group col-md-6">CLAVE: <b>{{$data->clave}}</b></div>
                    <div class="form-group col-md-6">    
                        UNIDAD/ACCIÓN MÓVIL:            
                        <b>@if($data->unidad == $data->ubicacion){{ $data->unidad }} @else {{ $data->ubicacion }}/{{ $data->unidad }} @endif</b>        
                    </div>                
                    <div class="form-group col-md-6">CURSO: <b>{{ $data->curso }}</b></div>
                    <div class="form-group col-md-6">INSTRUCTOR: <b>{{ $data->nombre }}</b></div>
                    <div class="form-group col-md-6">TIPO DE PAGO: <b>{{ $data->tpago }}</b></div>
                    <div class="form-group col-md-6">FECHAS: <b>{{ $data->inicio }} AL {{ $data->termino }}</b></div>                
                    <div class="form-group col-md-6">TOTAL BENEFICIADOS: <b>{{ $data->hombre+$data->mujer }}</b></div>
                    <div class="form-group col-md-6">HORARIO: <b>DE {{ $data->hini }} A {{ $data->hfin }} </b></div>                                
                    <div class="form-group col-md-6">TOTAL CUOTA DE RECUPERACIÓN: <b>$ {{ number_format($data->costo, 2, '.', ',') }}</b></div>
                    <div class="form-group col-md-6">ESTATUS: <b>{{ $data->status_curso }}</b></div>                    
                </div>                
                @if($data->status_recibo == 'ASIGNANDO')                    
                    <h4 class="pt-2 pb-2">DEL RECIBO DE PAGO</h4>                     
                    <div class="form-row bg-light p-5">
                        <div class="form-group col-md-3 m-1 ">
                            <label>RECIBÍ DE:</label>
                            {{ Form::text('recibide', $data->recibide, ['id'=>'recibide', 'class' => 'form-control', 'placeholder' => 'RECIBÍ DE', 'title' => 'RECICÍ DE']) }}
                        </div>
                        <div class="form-group col-md-3 m-1 ">
                            <label>EXPEDICIÓN:</label>
                            {{ Form::date('fecha', $data->fecha_expedicion, ['id'=>'fecha', 'class' => 'form-control', 'placeholder' => 'DIA/MES/AÑO',  'title'=>'FECHA DE EXPEDICIÓN']) }}
                        </div>
                        <div class="form-group col-md-3 m-1 ">
                            <label>RECIBIÓ:</label>
                            {{ Form::text('recibio', $data->recibio, ['id'=>'recibio', 'class' => 'form-control', 'placeholder' => 'RECIBIÓ', 'title' => 'RECIBIÓ']) }}
                        </div>
                        <div class="form-group col-md-2 m-1 "> <br/>
                            {{ Form::button('GUARDAR CAMBIOS', ['id'=>'modificar','class' => 'btn']) }}                                      
                        </div>
                    </div> 
                    <hr/> 
                @endif                 
                <div class="row w-100 form-inline justify-content-end">                    
                    <h5 class="bg-light p-2">RECIBO No. <span class="bg-white p-1">&nbsp;<b>{{$data->uc}}</b> <b class="text-danger">{{$data->folio_recibo}}</b>&nbsp;</span></h5>
                    @if($data->status_recibo == 'DISPONIBLE')                        
                        {{ Form::text('recibide', $data->recibide, ['id'=>'recibide', 'class' => 'form-control col-md-3 m-1 ', 'placeholder' => 'RECIBÍ DE', 'size' => 150, 'title' => 'RECICÍ DE']) }}
                        {{ Form::date('fecha', $data->fecha_expedicion, ['id'=>'fecha', 'class' => 'form-control col-md-2 m-2 ', 'placeholder' => 'DIA/MES/AÑO', 'size' => 50, 'title'=>'FECHA DE EXPEDICIÓN']) }}                        
                        {{ Form::text('recibio', $data->recibio, ['id'=>'recibio', 'class' => 'form-control col-md-3 m-1 ', 'placeholder' => 'RECIBIÓ', 'size' => 150, 'title' => 'RECIBIÓ' ]) }}
                        {{ Form::button('ASIGNAR', ['id'=>'asignar','class' => 'btn btn-danger']) }}
                    @else
                        {{ Form::button('GENERAR RECIBO', ['id'=>'pdfRecibo','class' => 'btn']) }}
                        @if(!$data->deshacer) 
                            {{ Form::button('ENVIAR', ['id'=>'enviar','class' => 'btn btn-danger']) }}
                        @endif
                    @endif
                    @if($data->deshacer)
                        {{ Form::select('movimiento', [ 'SUBIR' => 'SUBIR ARCHIVO PDF', 'DESHACER'=>'DESHACER ASIGNACION'], '', ['id'=>'movimiento','class' => 'form-control  col-md-3 m-1 custom-select-mov', 'placeholder'=>'- MOVIMIENTOS -'] ) }}
                        {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn btn-danger']) }}
                    @endif
                    
                </div>            
            @endif
        {!! Form::close() !!}
    </div>
    @section('script_content_js') 
        <script language="javascript">
             $(document).ready(function(){                
                $("#asignar" ).click(function(){ 
                    if(confirm("Esta seguro de ejecutar la acción?")==true){ 
                        $('#frm').attr('action', "{{route('grupos.recibos.asignar')}}"); 
                        $('#frm').attr('target', '_self');
                        $('#frm').submit(); 
                    }
                }); 

                $("#modificar" ).click(function(){ 
                    if(confirm("Esta seguro de ejecutar la acción?")==true){ 
                        $('#frm').attr('action', "{{route('grupos.recibos.modificar')}}");
                        $('#frm').attr('target', '_self');
                        $('#frm').submit(); 
                    }
                }); 
                $("#buscar").click(function(){ 
                    $('#frm').attr('action', "{{route('grupos.recibos')}}");
                    $('#frm').attr('target', '_self');
                    $('#frm').submit(); 
                });
                $("#pdfRecibo").click(function(){ 
                    $('#frm').attr('action', "{{route('grupos.recibos.pdf')}}"); 
                    $('#frm').attr('target', '_blank');
                    $('#frm').submit();
                 });
            });       
            
        </script>  
    @endsection
@endsection