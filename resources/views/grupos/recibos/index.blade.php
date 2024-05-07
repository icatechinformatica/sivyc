<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <style>
        .custom-file-label::after {
            content: "Examinar";
        }
        .fixed-width-label {
            max-width: 200px; /* Adjust the value as needed */
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            margin-bottom:-1px;
        }
    </style>
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Grupos- Recibos de Pago | SIVyC Icatech')
@section('content')       
    <div class="card-header">
        Grupos / Recibos de Pago        
    </div>
    <div class="card card-body">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif        
        {{ Form::open(['method' => 'post', 'id'=>'frm',  'enctype' => 'multipart/form-data']) }}
            @csrf                   
            <div class="row form-inline">                
                {{ Form::select('id_concepto', $conceptos, $request->id_concepto ,['id'=>'id_concepto','placeholder' => '- CONCEPTO -','class' => 'form-control  mr-sm-2']) }}
                {{ Form::text('folio_grupo', $request->folio_grupo, ['id'=>'folio_grupo', 'class' => 'form-control mr-2', 'placeholder' => 'FOLIO DE GRUPO/NO. RECIBO', 'aria-label' => 'FOLIO DE GRUPO', 'required' => 'required', 'size' => 30]) }}
                {{ Form::button('BUSCAR', ['id' => 'buscar','name' => 'BUSCAR', 'class' => 'btn', 'value'=>route('grupos.recibos')]) }}                
                {{ Form::button('NUEVA ASIGNACIÓN', ['id' => 'nuevo','name' => 'nuevo', 'value'=>'nuevo','class' => 'btn']) }}    
            </div>        
            {{ Form::hidden('ID', null, ['id'=>'ID']) }}            
            @if($data)                        
                {{ Form::hidden('idconcepto', $data->id_concepto, ['id'=>'idconcepto']) }}
                {{ Form::hidden('id_recibo', $data->id_recibo , ['id'=>'id_recibo']) }} 
                {{ Form::hidden('precio_unitario', $data->precio_unitario , ['id'=>'precio_unitario']) }}            
                <div class="row form-inline"> 
                        <div class="form-group col-md-6"> <h4>  @if($data->id_concepto==1)DEL CURSO @else DEL CONCEPTO DE PAGO @endif</h4> </div>    
                        <div class="form-group col-md-6 justify-content-end ">                        
                            <h4 class="bg-light p-2">&nbsp; RECIBO No. &nbsp;<span class="bg-white p-1">&nbsp;<b>{{$data->uc}}</b> <b class="text-danger">{{ str_pad($data->num_recibo, 4, "0", STR_PAD_LEFT) }}</b>&nbsp;</span> &nbsp;</h4>
                            @if($data->status_folio == 'DISPONIBLE') 
                                <h4 class="text-center text-white p-2" style="background-color: #33A731;">&nbsp;{{$data->status_folio}} &nbsp;</h4>
                            @elseif(in_array($data->status_folio, ['IMPRENTA','ENVIADO','CANCELADO'])) 
                                <h4 class="text-center text-white bg-danger p-2" >&nbsp;{{$data->status_folio}} &nbsp;</h4>
                            @else
                                <h4 class="bg-warning text-center p-2">&nbsp;{{$data->status_folio}} &nbsp;</h4>
                            @endif
                            @if($data->file_pdf)
                                <a class="nav-link pt-0" href="{{$path_files}}{{ $data->file_pdf}}" target="_blank">
                                    <i  class="far fa-file-pdf  fa-3x text-danger"  title='DESCARGAR RECIBO DE PAGO OFICIALIZADO.'></i>
                                </a>
                            @endif
                        </div>                    
                </div>       
                @if($data->id_concepto==1)
                    <div class="row bg-light" style="padding:35px; line-height: 1.5em;">                        
                        <div class="form-group col-md-6">    FOLIO GRUPO: <b>{{$data->folio_grupo}}</b></div>
                        <div class="form-group col-md-6">    
                            UNIDAD/ACCIÓN MÓVIL:            
                            <b>@if($data->unidad == $data->ubicacion){{ $data->unidad }} @else {{ $data->ubicacion }} / {{ $data->unidad }} @endif</b>        
                        </div>
                        <div class="form-group col-md-6">CURSO: <b>{{ $data->curso }}</b></div>
                        <div class="form-group col-md-6">
                            CLAVE: 
                            @if($data->clave==0) <b class="text-danger">{{$data->status_clave}} &nbsp;</b> @else <b>{{$data->clave}} &nbsp;</b> @endif
                            @if($data->status_curso) ESTATUS: <b class="text-danger"> {{$data->status_curso}} </b> @endif 
                        </div>
                        <div class="form-group col-md-6">INSTRUCTOR: <b>{{ $data->nombre }}</b></div>
                        <div class="form-group col-md-6">ARC-01: <b>{{ $data->munidad }}</b></div>
                        <div class="form-group col-md-6">TOTAL BENEFICIADOS: <b>{{ $data->hombre+$data->mujer }}</b></div>
                        <div class="form-group col-md-6">FECHAS: <b>{{ $data->inicio }} AL {{ $data->termino }}</b></div> 
                        <div class="form-group col-md-6">TOTAL CUOTA DE RECUPERACIÓN: <b>$ {{ number_format($data->costo, 2, '.', ',') }}</b></div>                    
                        <div class="form-group col-md-6">HORARIO: <b>DE {{ $data->hini }} A {{ $data->hfin }} </b></div>
                        @if($data->status_recibo )<div class="form-group col-md-6">ESTATUS RECIBO: <b>{{ $data->status_recibo }}</b></div>@endif
                        <div class="form-group col-md-6">TIPO DE PAGO: <b>{{ $data->tpago }}</b></div>                        
                    </div> 
                @elseif($data->status_folio == 'ENVIADO')                             
                    <div class="row bg-light" style="padding:35px; line-height: 1.5em;">   
                        <div class="form-group col-md-6">    
                            UNIDAD/ACCIÓN MÓVIL:            
                            <b>@if($data->unidad == $data->ubicacion){{ $data->unidad }} @else {{ $data->ubicacion }} / {{ $data->unidad }} @endif</b>        
                        </div>                        
                        <div class="form-group col-md-6"> FECHA EXPEDICIÓN: <b>{{ date('d/M/Y', strtotime($data->fecha_expedicion)) }}</b></div>
                                                
                        @if(isset($data->descripcion))<div class="form-group col-md-6"> INFORMACIÓN GENERAL: <b>{{$data->descripcion}}</b></div>@endif
                        @if(isset($data->constancias))<div class="form-group col-md-6"> FOLIO(S) DE CONSTANCIA(S): <b>{{$data->constancias}}</b></div>@endif
                        @if(isset($data->importe))<div class="form-group col-md-6">CANTIDAD: <b>$ {{ number_format($data->importe, 2, '.', ',') }}</b></div>@endif                       
                        
                        @if($data->folio_grupo) <div class="form-group col-md-6"> FOLIO GRUPO: <b>{{$data->folio_grupo}}</b></div> @endif
                        @if($data->clave) <div class="form-group col-md-6">CLAVE: <b>{{$data->clave}} &nbsp;</b></div> @endif                                       
                        @if($data->curso) <div class="form-group col-md-6"> CURSO: <b>{{ $data->curso }}</b></div> @endif
                        
                        
                        <div class="form-group col-md-6">ESTATUS RECIBO: <b>{{ $data->status_recibo }}</b></div>
                    </div> 
                @else                     
                    <div class="form-row bg-light p-5">
                        @if($data->id_concepto==2 OR $data->id_concepto==4)
                            <div class="form-group col-md-3 mr-1 ">
                                <label class="fixed-width-label">FOLIO GRUPO/CLAVE:</label>
                                {{ Form::text('clave', $data->clave, ['id'=>'clave', 'class' => 'form-control']) }}                                
                            </div>                           
                            <div class="form-group col-md-2 mr-1 ">
                                <label>MATRÍCULA:</label>
                                {{ Form::text('matricula',  $data->matricula, ['id'=>'matricula', 'class' => 'form-control']) }}
                            </div>
                            @if($data->calificacion)
                                <div class="form-group col-md-2 mr-1 ">
                                    <label>CALIFICACIÓN:</label>
                                    {{ Form::text('calificacion',  $data->calificacion, ['id'=>'calificacion', 'class' => 'form-control', 'disabled'=>'true']) }}
                                </div>
                            @endif    
                        @endif
                        @if($data->id_concepto==3)
                            <div class="form-group col-md-6 mr-1 ">
                                <label>FOLIOS DE CONSTANCIAS: (FOLIO INICIAL- FOLIO FINAL, FOLIO A, FOLIO D)</label>
                                {{ Form::text('constancias', $data->constancias, ['id'=>'constancias', 'class' => 'form-control']) }}
                            </div>
                            <div class="form-group col-md-2 mr-1 ">
                                <label>CANTIDAD:</label>
                                {{ Form::text('cantidad', $data->cantidad, ['id'=>'cantidad', 'class' => 'form-control']) }}
                            </div>
                        @endif
                        <div class="form-group col-md-2 mr-1 ">
                            <label>IMPORTE:</label>
                            {{ Form::text('importe', $data->importe, ['id'=>'importe', 'class' => 'form-control']) }}
                        </div>
                        <div class="form-group col-md-12">
                            <label>DESCRIPCIÓN:</label>
                            {{ Form::textarea('descripcion', $data->descripcion, ['id'=>'descripcion', 'class' => 'form-control' , 'rows' => '4']) }}
                        </div>                        
                    </div>                     
                @endif
                @if($data->editar)
                    <h4 class="pt-2 pb-2">DEL RECIBO</h4>                     
                    <div class="form-row bg-light p-5">                        
                        <div class="form-group col-md-12">
                            <label>FOLIO Y FECHA DEL COMPROBANTE DEPOSITO:</label>                           
                            <div class="row form-inline ml-1">  
                                <div id="text-box-container" class="form-inline">    
                                    @if(isset($data->depositos))
                                        @php
                                            $i=1;
                                        @endphp
                                        @foreach($data->depositos as $item)
                                            <input type="text" class="form-control" id="folio_deposito{{$i}}" name="folio_deposito[{{$i}}]" value="{{$item['folio']}}" placeholder="No.FOLIO">
                                            <input type="text" class="form-control" id="importe_deposito{{$i}}" name="importe_deposito[{{$i}}]" value="{{$item['importe']}}" placeholder="IMPORTE">
                                            <input type="date" class="form-control mr-3" id="fecha_deposito{{$i}}" name="fecha_deposito[{{$i}}]" value="{{$item['fecha']}}" placeholder ="DIA/MES/AÑO">
                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <input type="text" class="form-control" id="folio_deposito1" name="folio_deposito[1]" placeholder="No.FOLIO">
                                        <input type="text" class="form-control" id="importe_deposito1" name="importe_deposito[1]" value="{{ isset($data->importe) ? $data->importe : ''}}" placeholder="IMPORTE">
                                        <input type="date" class="form-control mr-3" id="fecha_deposito1" name="fecha_deposito[1]" placeholder ="DIA/MES/AÑO">
                                    @endif
                                </div>
                                <button type="button" class="btn form-inline bg-info" onclick="addTextBox()" title="MÁS" style="font-size: 20px; padding:0 5px 0 5px;">+</button>                            
                            </div>
                            <hr/>
                        </div>                        
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
                            {{ Form::text('recibio', $data->recibio, ['id'=>'recibio', 'class' => 'form-control', 'placeholder' => 'RECIBIÓ', 'title' => 'RECIBIÓ', 'disabled'=>'true']) }}
                        </div>
                        <div class="form-group col-md-2 m-1 "> <br/>
                        @if($data->status_folio != 'CANCELADO')
                             {{ Form::button('GUARDAR CAMBIOS', ['id'=>'modificar','class' => 'btn', 'value'=> route('grupos.recibos.modificar')]) }}
                        @endif
                        </div>
                    </div>                     
                @endif
                <div class="row w-100 form-inline justify-content-end mt-4">                    
                    <h5 class="bg-light p-2">RECIBO No. <span class="bg-white p-1">&nbsp;<b>{{$data->uc}}</b> <b class="text-danger">{{ str_pad($data->num_recibo, 4, "0", STR_PAD_LEFT) }}</b>&nbsp;</span></h5>
                    @if($data->file_pdf)
                        <a class="nav-link pt-0" href="{{$path_files}}{{ $data->file_pdf}}" target="_blank">
                            <i  class="far fa-file-pdf  fa-3x text-danger"  title='DESCARGAR RECIBO DE PAGO OFICIALIZADO.'></i>
                        </a>
                    @endif
                        
                    @if($movimientos)
                            {{ Form::select('movimiento', $movimientos, '', ['id'=>'movimiento','class' => 'form-control  col-md-3 m-1', 'placeholder'=>'- MOVIMIENTOS -'] ) }}
                    @endif
                    <div class="custom-file col-md-2" id="inputFile" style="display:none">
                        <input id="file_recibo" type="file" name="file_recibo" class="custom-file-input" accept=".pdf" >
                        <label class="custom-file-label" for="file_recibo">&nbsp;&nbsp;</label>
                    </div>                    
                    {{ Form::select('status_recibo', $status_recibo, '', ['id'=>'status_recibo','class' => 'form-control', 'title'=>'ESTATUS','style'=>'display:none'] ) }}                    
                    {{ Form::text('motivo', '', ['id'=>'motivo', 'class' => 'form-control col-md-4 m-1 ', 'placeholder' => 'MOTIVO', 'title' => 'MOTIVO', 'style'=>'display:none']) }}
                    @if($data->status_folio == 'DENEGADO')                     
                            Observaciones:
                            {{ Form::text('movimiento', 'DENEGADO', ['id'=>'movimiento', 'class' => 'form-control col-md-4 m-1 ','style'=>'display:none']) }}
                            {{ Form::textarea('observaciones', $data->observaciones, ['id'=>'observaciones', 'class' => 'form-control col-md-6 m-1', 'disabled'=>'true', 'row'=>2, 'style' => 'height: 4em;']) }}
                            {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn btn-danger', 'value'=>route('grupos.recibos.aceptar')]) }}
                    @else
                            {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn btn-danger', 'style'=>'display:none', 'value'=>route('grupos.recibos.aceptar')]) }}
                    @endif
                        
                    @if($data->status_folio == 'DISPONIBLE')                
                            {{ Form::text('recibide', $data->recibide, ['id'=>'recibide', 'class' => 'form-control col-md-3 m-1 ', 'placeholder' => 'RECIBÍ DE', 'title' => 'RECICÍ DE']) }}
                            {{ Form::date('fecha', $data->fecha_expedicion, ['id'=>'fecha', 'class' => 'form-control col-md-1 m-1 ', 'placeholder' => 'DIA/MES/AÑO',  'title'=>'FECHA DE EXPEDICIÓN']) }}                        
                            {{ Form::text('recibio', $data->recibio, ['id'=>'recibio', 'class' => 'form-control col-md-3 m-1 ', 'placeholder' => 'RECIBIÓ', 'title' => 'RECIBIÓ', 'disabled'=>'true' ]) }}
                            {{ Form::select('status_recibo', $status_recibo, '', ['id'=>'status_recibo','class' => 'form-control', 'title'=>'ESTATUS'] ) }}
                            {{ Form::button('ASIGNAR', ['id'=>'asignar','class' => 'btn btn-danger', 'value'=> route('grupos.recibos.asignar')]) }}
                    @else                        
                        @if(!in_array($data->status_folio, ['IMPRENTA','DISPONIBLE','ENVIADO','CANCELADO']) OR (!$data->status_curso AND $data->id_concepto==1)) 
                                {{ Form::button('GENERAR RECIBO', ['id'=>'pdfRecibo','class' => 'btn', 'value' => route('grupos.recibos.pdf')]) }}
                        @endif
                            @if($data->status_folio == "CARGADO") 
                                {{ Form::button('ENVIAR', ['id'=>'enviar','class' => 'btn btn-danger', 'value'=> route('grupos.recibos.enviar')]) }}
                            @endif
                    @endif
                        
                </div>
            @endif
        {!! Form::close() !!}
    </div>
    @section('script_content_js') 
        <script src="{{ asset('js/grupos/recibos.js') }}"></script>               
        <script>            
            let textBoxCounter = $('#text-box-container input[name^="folio_deposito"]').length;
            function addTextBox() {                
                textBoxCounter++;
                const newTextBox = document.createElement("div");
                newTextBox.innerHTML = `
                        <input type="text" class="form-control" id="folio_deposito${textBoxCounter}" name="folio_deposito[${textBoxCounter}]" placeholder="No.FOLIO">
                        <input type="text" class="form-control" id="importe_deposito${textBoxCounter}" name="importe_deposito[${textBoxCounter}]" placeholder="IMPORTE">
                        <input type="date" class="form-control mr-3" id="fecha_deposito${textBoxCounter}" name="fecha_deposito[${textBoxCounter}]" placeholder ="DIA/MES/AÑO">
                        `;
                document.getElementById("text-box-container").appendChild(newTextBox);
            }

            $("#nuevo").click(function(){
                $("#folio_grupo" ).val("");
                $("#ID" ).val("NUEVO");
                $('#frm').attr('action', "{{route('grupos.recibos')}}"); 
                $('#frm').attr('target', '_self');
                $('#frm').submit();         
            });

            
            function validarConstancias() {
                var valor = $('#constancias').val();
                var expresionRegular = /^[A-Za-z0-9,\-]+$/;
                if (!expresionRegular.test(valor)) alert("SOLO SE PERMITE CARACTERES ALFANUMÉRICOS, COMAS Y GUIONES MEDIOS")

                var valorLimpio = valor.replace(/[^A-Za-z0-9,\-]/g, '');                
                $('#constancias').val(valorLimpio);   
            }

            $('#constancias').on('blur', validarConstancias);  
            $('#constancias').keypress(validarConstancias);          
        </script>
    @endsection
@endsection