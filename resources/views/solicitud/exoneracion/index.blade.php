{{--  AGC  --}}
@extends('theme.sivyc.layout')
@section('title', 'Exoneración y/o Reducción de Cuota | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
    
    <div class="card-header">
        Solicitud / Exoneración y/o Reducción de Cuota
    </div>
    <div class="card card-body" style=" min-height:450px;">              
    {{ Form::open(['route' => 'solicitud.exoneracion', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
        <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. Revisión o No. Memorándum', 'aria-label' => 'CLAVE DEL CURSO', 'size' => 25]) }}
            </div>
            <div class="form-group col-md-2">
                {{ form::date('fecha', date('Y-m-d'), ['id' => 'fecha','class'=>'form-control','readonly'=>'readonly']) }}    
            </div>
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div>  
            @if($pdf)  
                <div class="form-group col-md-3">
                    <div class="dropdown show">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print  text-white" title="Imprimir Memorándum"> Memorándum Unidad</i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{$pdf}}" target="_blank">
                            @if ($memo)
                            {{ $memo."."."PDF" }} 
                            @else
                            {{ $nrevision."."."PDF" }} 
                            @endif
                            </a>
                        </div>
                    </div>
                </div> 
            @endif   
        </div>
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <h4><b>DE LA EXONERACIÓN</b></h4>
        <hr />
        <div class="row">  
            @if ($memo)
               <div class="form-group col-md-6">
                    <h5 ><b>No. Memorándum {{ $memo}}</b></h5>
                </div> 
            @endif
            @if ($nrevision)
               <div class="form-group col-md-6">
                    <h5 ><b>Revisión No. {{ $nrevision}}</b></h5>
                </div> 
            @endif  
        </div>
        @if ($agregar)
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="grupo">No. Grupo:</label>
                    {{ Form::text('grupo', '', ['id'=>'grupo', 'class' => 'form-control', 'placeholder' => 'No. GRUPO', 'aria-label' => 'No. GRUPO', 'size' => 25]) }}
                </div> 
                {{-- <div class="form-group col-md-3">
                    <label for="revision">No. Revisión:</label>
                    {{ Form::text('revision', $nrevision, ['id'=>'revision', 'class' => 'form-control', 'placeholder' => 'No. REVISION', 'aria-label' => 'No. REVISION', 'size' => 25]) }}
                </div>  --}}
                <div class="form-group col-md-3">
                    <label for="opt">Razón de la Exoneración:</label>
                    {{ Form::select('opt', ['MS'=>'MADRES SOLTERAS','AM'=>'ADULTOS MAYORES', 'BR'=>'BAJOS RECURSOS', 'D'=>'DISCAPACITADOS', 'PPL'=>'PERSONAS PRIVADAS DE LA LIBERTAD',
                        'GRS'=>'GRUPOS DE REINSERCION SOCIAL', 'O'=>'OTRO'], 
                        '', ['id'=>'opt','class' => 'form-control','placeholder' =>'- SELECCIONAR -'] ) }}
                </div>  
                <div class="form-group col-md-3">
                    <label for="oficio">Oficio de solicitud:</label>
                    {{ Form::text('oficio', '', ['id'=>'oficio', 'class' => 'form-control', 'placeholder' => 'Oficio de solicitud', 'aria-label' => 'Oficio de solicitud', 'size' => 25]) }}
                </div>
                <div class="form-group col-md-2">
                    <label for="foficio">Fecha Oficio de Solicitud</label>
                    {{ form::date('foficio', '', ['id' => 'foficio','class'=>'form-control']) }}    
                </div>        
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Observaciones:</label>
                    <textarea name='observaciones' id='observaciones'  class="form-control" rows="5"></textarea>
                </div>
                <div class="form-group col-md-11">

                </div>
                <div class="form-group col-md-1">
                    {{ Form::button('AGREGAR', ['id'=>'agregar','class' => 'btn btn-success']) }}
                </div>
                
            </div>
        @endif
        <hr />
        @if (count($cursos)>0)
            <h4><b>GRUPOS</b></h4>
            <div class="table-responsive ">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @if ($agregar)
                            <th style="padding: 1px;" rowspan="2" class="text-center">ELIMINAR</th>
                            @endif
                            <th style="padding: 1px;" rowspan="2" class="text-center">ID</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">No. GRUPO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">SERVICIO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">UNIDAD DE CAPACITACIÓN Y/O ACCIÓN MÓVIL</th> 
                            <th style="padding: 1px;" rowspan="2" class="text-center">NOMBRE DEL CURSO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">COSTO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">HORAS</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">FECHA INICIO</th>       
                            <th style="padding: 1px;" rowspan="2" class="text-center">FECHA TERMINO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">CUPO</th>
                            <th style="padding: 1px;" colspan="2" class="text-center">SEXO</th>
                            <th style="padding: 1px;" colspan="2" class="text-center">FOLIAJE EXONERACION</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">INSTRUCTOR</th> 
                            <th style="padding: 1px;" rowspan="2" class="text-center">EXO.</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">REDU.</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">TURNADO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">STATUS</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">No. CONVENIO</th>
                            <th style="padding: 1px;" colspan="4" class="text-center">DEPENDENCIA O GRUPO BENEFICIADO</th>
                            <th style="padding: 1px;" rowspan="2" class="text-center">AVISO</th>
                        </tr>
                        <tr>
                            <th class="text-center">F</th>
                            <th class="text-center">M</th>
                            <th class="text-center">DEL</th>
                            <th class="text-center">AL</th>
                            <th class="text-center">ORGANISMO</th>
                            <th class="text-center">OFICIO DE SOLICITUD</th>
                            <th class="text-center">RAZÓN DE LA EXONERACIÓN</th>
                            <th class="text-center">OBSERVACIONES</th>
                        </tr>
                    </thead>
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
                                <td class="text-center">{{$item->id}}</td>
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
                                <td class="text-center">{{$item->turnado}}</td>
                                <td class="text-center"> @if($item->status) {{$item->status}} @else {{"EN CAPTURA" }} @endif </td>
                                <td class="text-center">{{$item->no_convenio}}</td>
                                <td class="text-center">{{$item->depen}}</td>
                                <td class="text-center">{{$item->noficio}}<br>{{$item->foficio}}</td>
                                <td class="text-center">
                                    <div style="width: 300px;">
                                        {{ Form::select('', ['MS'=>'MADRES SOLTERAS','AM'=>'ADULTOS MAYORES', 'BR'=>'BAJOS RECURSOS', 'D'=>'DISCAPACITADOS', 'PPL'=>'PERSONAS PRIVADAS DE LA LIBERTAD',
                                        'GRS'=>'GRUPOS DE REINSERCION SOCIAL', 'O'=>'OTRO'], 
                                        $item->razon_exoneracion, ['id'=>'','class' => 'form-control','placeholder' =>'- SELECCIONAR -','disabled'=>'disabled'] ) }}
                                    </div>
                                </td>
                                <td class="text-left"><div style="width: 400px;">{{$item->observaciones}}</div></td>
                                <td><div style="width:200px;">{{$aviso}}</div></td>
                            </tr> 
                        @endforeach  
                    </tbody>                   
                </table>
            </div>
            <div class="btn-toolbar">
                @if ($activar)
                    @if (!$cursos[0]->status OR ($cursos[0]->status == 'RETORNADO'))
                        <div class="input-group col-md-5 my-2">
                        </div>
                        <div class="custom-file input-group col-md-3 my-2">
                            <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" />
                            <label for="file_autorizacion" class="custom-file-label">ANEXOS PDF</label>
                        </div>
                        <div class="btn-group col-md-2 my-2">
                            {{ Form::button('ENVIAR PRELIMINAR >>', ['id'=>'preliminar','class' => 'btn  bg-danger']) }}
                        </div>
                    @else
                        <div class="input-group col-md-2 my-2">
                            {{ Form::text('memo', $memo, ['id'=>'memo', 'class' => 'form-control', 'placeholder' => 'No. MEMORÁNDUM', 'aria-label' => 'No. Memorándum', 'size' => 25]) }}
                        </div>
                        <div class="btn-group col-md-3 my-2">
                            {{ Form::button('GENERAR FOLIAJE Y MEMORÁNDUM PDF', ['id'=>'generar','class' => 'btn']) }}
                        </div>
                        <div class="custom-file input-group col-md-3 my-2">
                            <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" />
                            <label for="file_autorizacion" class="custom-file-label">SOLICITUD FIRMADA, ANEXOS PDF</label>
                        </div>
                        <div class="btn-group col-md-2 my-2">
                            {{ Form::button('ENVIAR >>', ['id'=>'enviar','class' => 'btn  bg-danger']) }}
                        </div>
                    @endif
                @else 
                    @if ($cursos[0]->status == 'AUTORIZADO')
                        <div class="input-group col-md-3 my-2">
                            {{ Form::select('movimiento', $movimientos, '', ['id'=>'movimiento','class' => 'form-control', 'placeholder'=>'- SELECCIONAR MOVIMIENTO -'] ) }}
                        </div>
                        <div class="input-group col-md-3 my-2">
                            {{ Form::textarea('motivo', '', ['id' => 'motivo' ,'class' => 'form-control', 'placeholder' => 'MOTIVO DE LA EDICIÓN','rows' =>'1']) }}
                        </div>
                        <div class="btn-group col-md-2 my-2"> 
                            {{ Form::button('ENVIAR >>', ['id'=>'edicion','class' => 'btn  bg-danger']) }} 
                        </div>
                    @else
                        <div class="input-group col-md-10 my-2"></div>
                    @endif
                @endif
                <div class="btn-group col-md-2 my-2">
                    {{ Form::button('NUEVA SOLICITUD', ['id'=>'nuevo','class' => 'btn']) }}
                </div>
            </div>
        @endif
    {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">      
            $(document).ready(function(){
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.exoneracion')}}"); $('#frm').attr('target', '_self').submit();});
                $("#enviar" ).click(function(){ 
                    if (confirm("¿Esta seguro de ejecutar la acción?")==true) {
                        $('#frm').attr('action', "{{route('solicitud.exoneracion.enviar')}}"); $('#frm').attr('target', '_self').submit();
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
                    $('#frm').validate({
                        rules: {
                            memo: { required: true}
                        },
                        messages:{
                            memo: { required: 'Ingrese el memorándum de exoneración'}          
                        }
                    });
                    $('#frm').attr('action', "{{route('solicitud.exoneracion.generar')}}"); $('#frm').attr('target', '_blank').submit();
                });
                $("#nuevo" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.exoneracion.nuevo')}}"); $('#frm').attr('target', '_self').submit();});
                $("#edicion" ).click(function(){ 
                    if (confirm("¿Esta seguro de ejecutar la acción?")==true) {
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
                });
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