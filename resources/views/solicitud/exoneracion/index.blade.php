{{--  AGC  --}}
@extends('theme.sivyc.layout')
@section('title', 'Exoneración y/o Reducción de Cuota | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />    
    <div class="card-header">
        Solicitud / Exoneración y/o Reducción de Cuota
    </div>
    @php
        $fecha_memorandum = date('Y-m-d');
        if (count($cursos)>0){
            $nrevision = $cursos[0]->nrevision;
            if($cursos[0]->fecha_memorandum) $fecha_memorandum = $cursos[0]->fecha_memorandum;
            $no_memorandum = $cursos[0]->no_memorandum;
        }else{
            
            $nrevision  = $no_memorandum = null;
        }
    @endphp
    <div class="card card-body" style=" min-height:450px;">              
    {{ Form::open(['route' => 'solicitud.exoneracion', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
        <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. REVISIÓN/ MEMORÁNDUM', 'title' => 'No. REVISIÓN/ MEMORÁNDUM', 'aria-label' => 'NUMERO', 'size' => 25]) }}
            </div>
            <div class="form-group col-md-2">
                {{ form::date('fecha', $fecha_memorandum, ['id' => 'fecha','class'=>'form-control','readonly'=>'readonly']) }}    
            </div>
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div>           
            
        </div>
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
       
        <div class="row">
            <div class="col-md-2"><h4><b>DE LA EXONERACIÓN  </b> </h4> </div>            
            <div class="col-md-7 text-right ">
                <h4>
                    @if($no_memorandum) No. Memorándum: {{ $no_memorandum }}  &nbsp;&nbsp;/&nbsp;&nbsp;@endif
                    @if ($nrevision)No. Revisión:  No. {{ $nrevision}} @endif 
                </h4>                 
            </div>
            <div class="col-md-3 text-right ">                
                @if($pdf)                
                    <div class="dropdown show justify-content-end">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print  text-white" title="Imprimir Memorándum"> PDF DESCARGAR SOPORTES</i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{$pdf}}" target="_blank">
                            @if ($no_memorandum)
                            {{ $no_memorandum."."."PDF" }} 
                            @else
                            {{ $nrevision."."."PDF" }} 
                            @endif
                            </a>
                        </div>
                    </div>             
                @endif  
            </div>
        </div>
        <hr />
        @if ($agregar)
            <div class="row">
                <div class="form-group col-md-2">                    
                    {{ Form::text('grupo', '', ['id'=>'grupo', 'class' => 'form-control', 'placeholder' => 'No. GRUPO', 'title' => 'No. GRUPO', 'size' => 25]) }}
                </div> 
                {{-- <div class="form-group col-md-3">                    
                    {{ Form::text('revision', $nrevision, ['id'=>'revision', 'class' => 'form-control', 'placeholder' => 'No. REVISION', 'title' => 'No. REVISION', 'size' => 25]) }}
                </div>  --}}
                <div class="form-group col-md-3">                    
                    {{ Form::select('opt', $razon,'', ['id'=>'opt','class' => 'form-control','placeholder' =>'- RAZÓN DE LA EXONERACIÓN -'] ) }}
                </div>  
                <div class="form-group col-md-3">                    
                    {{ Form::text('oficio', '', ['id'=>'oficio', 'class' => 'form-control', 'placeholder' => 'No OFICIO SOPORTE', 'title' => 'No OFICIO SOPORTE', 'size' => 25]) }}
                </div>
                <div class="form-group col-md-2">                    
                    {{ form::date('foficio', '', ['id' => 'foficio','class'=>'form-control', 'placeholder' =>'FECHA OFICIO SOPORTE']) }}    
                </div>        
            </div>
            <div class="row">
                <div class="form-group col-md-10">                    
                    <textarea name='observaciones' id='observaciones'  class="form-control" rows="3" placeholder="OBSERVACIONES"></textarea>
                </div>                
                <div class="form-group col-md-2">
                    <br/>
                    {{ Form::button('AGREGAR', ['id'=>'agregar','class' => 'btn btn-success']) }}
                </div>
                
            </div>
        @endif
        <h4><b>GRUPOS</b></h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped"> 
                    <thead>
                        <tr>
                            @if ($agregar)
                            <th rowspan="2" class="text-center">QUITAR</th>
                            @endif                            
                            <th rowspan="2" class="text-center">No.GRUPO</th>
                            <th rowspan="2" class="text-center">SERVICIO</th>
                            <th rowspan="2" class="text-center">UNIDAD/ACCIÓN MÓVIL</th> 
                            <th rowspan="2" class="text-center">CURSO</th>
                            <th rowspan="2" class="text-center">COSTO</th>
                            <th rowspan="2" class="text-center">HORAS</th>
                            <th rowspan="2" class="text-center">INICIO</th>       
                            <th rowspan="2" class="text-center">TERMINO</th>
                            <th rowspan="2" class="text-center">CUPO</th>
                            <th colspan="2" class="text-center">SEXO</th>
                            <th colspan="2" class="text-center">FOLIAJE</th>
                            <th rowspan="2" class="text-center">INSTRUCTOR</th> 
                            <th rowspan="2" class="text-center">EXO.</th>
                            <th rowspan="2" class="text-center">RED.</th>                            
                            <th rowspan="2" class="text-center">ESTATUS</th>
                            <th rowspan="2" class="text-center">TURNADO</th>
                            <th rowspan="2" class="text-center">No.CONVENIO</th>
                            <th colspan="4" class="text-center">DEPENDENCIA/GRUPO BENEFICIADO</th>
                            <th rowspan="2" class="text-center">AVISO</th>
                        </tr>
                        <tr>
                            <th class="text-center">F</th>
                            <th class="text-center">M</th>
                            <th class="text-center">DEL</th>
                            <th class="text-center">AL</th>
                            <th class="text-center">ORGANISMO</th>
                            <th class="text-center">OFICIO</th>
                            <th class="text-center">RAZÓN</th>
                            <th class="text-center">OBSERVACIONES</th>
                        </tr>
                    </thead>
                    @if (count($cursos)>0)
                    <tbody>
                        @foreach ($cursos as $item)
                            @php
                                $aviso = null;
                                $total = $item->hombre + $item->mujer;
                                if ($item->pobservacion) {
                                    $aviso = $item->pobservacion;
                                }
                            @endphp
                            <tr id="{{$item->id}}">
                                @if ($agregar)
                                <td class="text-center">
                                    <a class="nav-link" ><i class="fa fa-remove  fa-2x fa-lg text-danger" onclick="eliminar({{$item->id}},'{{ route('solicitud.exoneracion.eliminar') }}');" title="Eliminar"></i></a>
                                </td>
                                @endif                                
                                <td class="text-center">{{$item->folio_grupo}}</td>
                                <td class="text-center">{{$item->tipo_curso}}</td>
                                <td class="text-center">{{$item->unidad}}</td>
                                <td class="text-center">{{$item->curso}}</td>
                                <td class="text-center">{{$item->costo}}</td>
                                <td class="text-center">{{$item->dura}}</td>
                                <td class="text-center">{{$item->inicio}}</td>
                                <td class="text-center">{{$item->termino}}</td>
                                <td class="text-center">{{$total}}</td>
                                <td class="text-center">{{$item->mujer}}</td>
                                <td class="text-center">{{$item->hombre}}</td>
                                <td class="text-center">{{$item->fini}}</td>
                                <td class="text-center">{{$item->ffin}}</td>
                                <td class="text-center">{{$item->instructor}}</td>
                                <td class="text-center">@if ($item->tipo_exoneracion == 'EXO') {{"X"}}  @endif</td>
                                <td class="text-center">@if ($item->tipo_exoneracion == 'EPAR') {{"X"}} @endif</td>                                
                                <td class="text-center">{{$item->status}}</td>
                                <td class="text-center">{{$item->turnado}}</td>
                                <td class="text-center">{{$item->no_convenio}}</td>
                                <td class="text-center">{{$item->depen}}</td>
                                <td class="text-center">{{$item->noficio}}<br>{{$item->foficio}}</td>
                                <td class="text-center">{{$razon[$item->razon_exoneracion]}}</td>
                                <td class="text-left"><div style="width: 400px;">{{$item->observaciones}}</div></td>
                                <td><div style="width:200px;">{{$aviso}}</div></td>
                            </tr> 
                        @endforeach  
                    </tbody>     
                    @endif              
                </table>
            </div>
        @if (count($cursos)>0)  
            <br/>
            <div class="row justify-content-end"> 
                @if ($nrevision AND $agregar)                                          
                    {{ Form::button('GENERAR BORRADOR', ['id'=>'borrador','class' => 'btn  col-md-2 m-1']) }}
                @endif
                @if($movimientos)
                    {{ Form::select('movimiento', $movimientos, '', ['id'=>'movimiento','class' => 'form-control  col-md-3 m-1', 'placeholder'=>'- MOVIMIENTOS -'] ) }}
                @endif
                @if ($activar)
                    @if ($cursos[0]->status =='CAPTURA')
                        <div class="custom-file input-group col-md-3 m-2">
                            <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" />
                            <label for="file_autorizacion" class="custom-file-label">SUBIR ANEXOS PDF</label>
                        </div>                        
                        {{ Form::button('ENVIAR PREVIO >>', ['id'=>'preliminar','class' => 'col-md-2 btn  bg-danger m-1']) }}
                    @else                    
                        {{ Form::textarea('motivo', '', ['id' => 'motivo' ,'class' => 'form-controlcol-md-2 m-1', 'style'=>'display:none', 'placeholder' => 'MOTIVO','rows' =>'1']) }}

                        {{ Form::text('memo', $no_memorandum, ['id'=>'memo', 'class' => 'form-control col-md-2 m-1', 'placeholder' => 'No. MEMORÁNDUM', 'aria-label' => 'No. Memorándum', 'style'=>'display:none']) }}
                        {{ Form::button('GENERAR FOLIOS Y MEMORANDUM', ['id'=>'generar','class' => 'form-control col-md-2 btn m-1', 'style'=>'display:none']) }}                        
                        <div class="custom-file input-group col-md-2 m-2 " id="file" style="display: none;">
                            <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" />
                            <label for="file_autorizacion" class="custom-file-label">SUBIR ARCHIVO PDF</label>
                        </div>                                                                        
                    @endif
                @else 
                    @if ($cursos[0]->status == 'AUTORIZADO')
                        {{Form::textarea('motivo', '', ['id' => 'motivo' ,'class' => 'form-control col-md-4 m-1', 'style'=>'display:none', 'placeholder' => 'MOTIVO','rows' =>'1']) }}                        
                    @endif
                @endif 
                {{ Form::button('ENVIAR A LA DTA >>', ['id'=>'enviar','class' => 'btn  bg-danger m-1', 'style'=>'display:none']) }}                   
                {{ Form::button('NUEVA SOLICITUD', ['id'=>'nuevo','class' => 'btn m-1']) }}
            </div>
            </div>
        @endif
    {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">      
            $(document).ready(function(){                
                $("#movimiento" ).change(function(){
                    $("#motivo").hide();
                    $("#memo").hide();
                    $("#generar").hide();
                    $("#file").hide();
                    $("#enviar").hide();
                    switch($("#movimiento" ).val()){
                        case "REINICIAR":case "CANCELAR": case "SOPORTES":case "EDITAR":
                            $("#motivo").show("slow");
                            $("#enviar").show("slow");
                        break;
                        case "GENERAR": 
                            if($("#movimiento option:selected").text()=='CAMBIAR MEMORANDUM')
                                var etiqueta = "GUARDAR Y GENERAR";
                            else if($("#movimiento option:selected").text()=='GENERAR MEMORANDUM')
                                var etiqueta = "GENERAR MEMORANDUM";
                            else  var etiqueta = "GENERAR FOLIOS Y MEMORANDUM";                            
                            $("#memo").show("slow");
                            $("#generar").text(etiqueta);
                            $("#generar").show("slow");                            
                        break;
                        case "TURNAR":                            
                            $("#file").show("slow");
                            $("#enviar").show("slow");
                        break;
                    }                    
                });

                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.exoneracion')}}"); $('#frm').attr('target', '_self').submit();});
                $("#enviar" ).click(function(){ 
                     if (confirm("¿Esta seguro de ejecutar la acción?")==true) {
                        if($("#movimiento" ).val()=='TURNAR'){
                            $('#frm').attr('action', "{{route('solicitud.exoneracion.enviar')}}"); $('#frm').attr('target', '_self').submit();
                        }else{
                            $('#frm').validate({
                            rules: {
                                movimiento: { required: true},
                                motivo: { required: true}
                            },
                            messages:{
                                movimiento: { required: 'Seleccione un movimiento'},
                                motivo: { required: 'Escriba el motivo de la petición'}          
                            }
                            });
                            $('#frm').attr('action', "{{route('solicitud.exoneracion.edicion')}}"); $('#frm').attr('target', '_self').submit();
                        }
                    }
                });
                $("#agregar" ).click(function(){ 
                    if ($("#grupo").val()==''||$("#opt").val()==''||$("#observaciones").val()==''||$("#oficio").val()=='') {
                        alert("Todos los campos deben ser llenados!! "); 
                    }else{
                        if(confirm("¿Esta seguro de ejecutar la acción?")==true){ $('#frm').attr('action', "{{route('solicitud.exoneracion.agregar')}}"); $('#frm').attr('target', '_self').submit();}
                    }
                });
                $("#preliminar").click(function (){
                    if(confirm("¿Esta seguro de ejecutar la acción?")==true){ 
                        $('#frm').attr('action', "{{route('solicitud.exoneracion.preliminar')}}"); $('#frm').attr('target', '_self').submit();
                    }
                });
                $("#generar" ).click(function(){ 
                    if (confirm("¿Esta seguro de ejecutar la acción?")==true) {
                        $('#frm').validate({
                            rules: {
                                memo: { required: true}
                            },
                            messages:{
                                memo: { required: 'Ingrese el memorándum de exoneración'}          
                            }
                        });                            
                        $('#frm').attr('action', "{{route('solicitud.exoneracion.generar')}}"); $('#frm').attr('target', '_blank').submit();
                        if($("#movimiento option:selected").text()=='GENERAR FOLIOS Y MEMORANDUM' || $("#movimiento option:selected").text()=='CAMBIAR MEMORANDUM'){
                            $('#frm').attr('action', "{{route('solicitud.exoneracion')}}"); $('#frm').attr('target', '_self').submit();               
                        }
                    }
                });
                $("#nuevo" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.exoneracion.nuevo')}}"); $('#frm').attr('target', '_self').submit();});
                $('#borrador').click(function(){$('#frm').attr('action', "{{route('solicitudes.exoneracion.borrador')}}"); $('#frm').attr('target', '_blank').submit();});
            }); 
            
            function  eliminar(id, route){
                    var fila = "#"+id;
                    if(confirm("Est\u00E1 seguro de ejecutar la acci\u00F3n?")==true){        
                        $.ajax({
                                url: route,
                                data: {id : id},         
                                type:  'GET',
                                dataType : 'text',
                                success: function(response) {
                                console.log(response);                            
                                    if(response==true){                                      
                                        alert("La eliminaci\u00F3n ha sido efectuada!!") ;
                                        $(fila).remove(); 
                                        //document.getElementById('tblAlumnos').render();
                                    }else{
                                        
                                        alert("Error, instrucci\u00F3n no valida.");
                                    }   
                                },
                                statusCode: {
                                    404: function() {
                                    alert('Página no encontrada');
                                    }
                                },
                                error:function(x,xs,xt){                            
                                    alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                                }
                        });
                    } else{
                        $('#textURL').val("OPERACI\u00D3N CANCELADA");
                    }
                }
        </script>
    @endsection 
@endsection