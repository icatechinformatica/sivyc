{{--  AGC  --}}
@extends('theme.sivyc.layout')
@section('title', 'Exoneración y/o Reducción de Cuota | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
    
    <div class="card-header">
        Solicitudes / Exoneración y/o Reducción de Cuota
    </div>
    <div class="card card-body" style=" min-height:450px;">              
    {{ Form::open(['route' => 'solicitud.exoneracion', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
        <div class="row">
            <div class="form-group col-md-3">
                    {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control', 'placeholder' => 'No. Revisión / No. Memorándum', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 25]) }}
            </div>
            <div class="form-group col-md-2">
                    {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div> 
            @if (count($cursos)>0)
                <div class="form-group col-md-4 text-right">
                    <div class="dropdown show">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print  text-white" title="Imprimir Memorándum"> PDF DESCARGAR SOPORTES</i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{$file}}" target="_blank">
                            @if ($status!='AUTORIZADO')
                            {{ $cursos[0]->nrevision."."."PDF" }} 
                            @else
                            {{ $cursos[0]->no_memorandum."."."PDF" }} 
                            @endif
                            </a>
                        </div>
                    </div>
                </div> 
                @if ($status=='PREVALIDACION')
                    <div class="form-group col-md-3 text-right">
                        {{ Form::button('GENERAR MEMORÁNDUM BORRADOR', ['id'=>'borrador','class' => 'btn mt-1']) }}
                    </div> 
                @endif
            @endif   
        </div>
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        @if (count($cursos)>0)
            <div class="table-responsive ">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                        <th rowspan="2" class="text-center">No.GRUPO</th>
                            @if (($status=='PREVALIDACION') OR ($status=='SOLICITADO') OR ($status=='VALIDADO'))
                              <th rowspan="2" class="text-center">OBSERVACIONES</th>  
                            @endif                            
                            <th rowspan="2" class="text-center">SERVICIO</th>
                            <th rowspan="2" class="text-center">UNIDAD /ACCIÓN MÓVIL</th> 
                            <th rowspan="2" class="text-center">CURSO</th>
                            <th rowspan="2" class="text-center">COSTO</th>
                            <th rowspan="2" class="text-center">HORAS</th>
                            <th rowspan="2" class="text-center">INICIO</th>       
                            <th rowspan="2" class="text-center">TERMINO</th>
                            <th rowspan="2" class="text-center">HORARIO</th>
                            <th rowspan="2" class="text-center">CUPO</th>
                            <th colspan="2" class="text-center">SEXO</th>
                            <th colspan="2" class="text-center">FOLIAJE</th>
                            <th rowspan="2" class="text-center">INSTRUCTOR</th> 
                            <th rowspan="2" class="text-center">EXO</th>
                            <th rowspan="2" class="text-center">RED</th>                            
                            <th rowspan="2" class="text-center">ESTATUS</th>
                            <th rowspan="2" class="text-center">TURNADO</th>
                            <th rowspan="2" class="text-center">No.CONVENIO</th>
                            <th rowspan="2" class="text-center">ORGANISMO</th>
                            <th colspan="3" class="text-center">DEPENDENCIA/GRUPO BENEFICIADO</th>
                        </tr>
                        <tr>
                            <th class="text-center">F</th>
                            <th class="text-center">M</th>
                            <th class="text-center">DEL</th>
                            <th class="text-center">AL</th>
                            <th class="text-center">OFICIO</th>
                            <th class="text-center">RAZÓN</th>
                            <th class="text-center">OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cursos as $item)
                            <tr>
                                <td class="text-center">{{$item->folio_grupo}}</td>
                                @if (($status=='PREVALIDACION') OR ($status=='SOLICITADO') OR ($status=='VALIDADO'))
                                <td class="text-center">
                                    <div style="width: 250px;">
                                        {{ Form::textarea('respuesta['.$item->folio_grupo.']', $item->pobservacion, ['id' => 'respuesta['.$item->folio_grupo.']' ,'class' => 'form-control', 'placeholder' => 'OBSERVACIONES','rows' =>'2']) }}
                                    </div>
                                </td>
                                @endif
                                @php
                                    $total = $item->hombre + $item->mujer;                                   
                                @endphp                                
                                <td class="text-center">{{$item->tipo_curso}}</td>
                                <td class="text-center">{{$item->unidad}}</td>
                                <td><div style="width: 280px;">{{$item->curso}}</div></td>
                                <td class="text-center">{{$item->costo}}</td>
                                <td class="text-center">{{$item->dura}}</td>
                                <td class="text-center">{{$item->inicio}}</td>
                                <td class="text-center">{{$item->termino}}</td>
                                <td class="text-center"><div style="width: 80px;">{{$item->hini}} A {{$item->hfin}} </div></td>
                                <td class="text-center">{{$total}}</td>
                                <td class="text-center">{{$item->mujer}}</td>
                                <td class="text-center">{{$item->hombre}}</td>
                                <td class="text-center">{{$item->fini}}</td>
                                <td class="text-center">{{$item->ffin}}</td>
                                <td class="text-center"><div style="width: 120px;">{{$item->instructor}}</div></td>
                                <td class="text-center">@if ($item->tipo_exoneracion == 'EXO') {{"X"}}  @endif</td>
                                <td class="text-center">@if ($item->tipo_exoneracion == 'EPAR') {{"X"}} @endif</td>                                
                                <td class="text-center">{{$item->status}}</td>
                                <td class="text-center">{{$item->turnado}}</td>
                                <td class="text-center">{{$item->no_convenio}}</div></td>
                                <td class="text-center"><div style="width: 200px;">{{$item->depen}}</td>
                                <td class="text-center">{{$item->noficio}} <br> {{$item->foficio}}</td>
                                <td class="text-center">{{$razon[$item->razon_exoneracion]}}</td>
                                
                                <td><div style="width: 300px;">{{$item->observaciones}}</div></td>
                            </tr>
                        @endforeach 
                    </tbody>                   
                </table>
            </div>
            <br/>
            <div class="row justify-content-end">
            @if (($status=='PREVALIDACION') OR ($status=='SOLICITADO'))                
                {{ Form::select('movimiento', $movimientos, '', ['id'=>'movimiento','class' => 'form-control col-md-2 m-2', 'placeholder'=>'- MOVIMIENTOS -'] ) }}
                <div class="custom-file form-group col-md-3 text-center m-2" id="file" style="display: none;">
                    <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
                    <label for="file_autorizacion" class="custom-file-label">AUTORIZACIÓN FIRMADA PDF</label>
                </div>
                 {{ Form::textarea('motivo', '', ['id' => 'motivo' ,'class' => 'form-control col-md-3 m-2', 'placeholder' => 'MOTIVO DE LA CANCELACIÓN','rows' =>'1' , 'style'=>'display:none']) }}                
                {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn  bg-danger m-2']) }}
            @endif
            @if(in_array($status, ['REINICIAR','CANCELAR','EDITAR','SOPORTES']))              
                {{ Form::label('SOLICITUD DE:','',['class' =>'mt-3']) }}
                {{ Form::text('movimiento', $movimientos[$status], ['id'=>'movimiento','class' => 'form-control col-md-2 m-2','readonly'=>'readonly']) }}
                {{ Form::label('MOTIVO:','',['class' =>'mt-3']) }}
                {{ Form::textarea('motivo', $motivo, ['id' => 'motivo' ,'class' => 'form-control col-md-4 m-2', 'placeholder' => '','rows' =>'1','readonly'=>'readonly']) }}
                {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn bg-danger m-2']) }}
                {{ Form::button('RECHAZAR', ['id'=>'denegar','class' => 'btn bg-warning m-2']) }}
            @endif 
            </div>  
        @endif
    {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">              
            $(document).ready(function(){
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitudes.exoneracion')}}"); $('#frm').attr('target', '_self').submit();});
                
                $("#denegar" ).click(function(){                    
                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.denegar')}}"); $('#frm').attr('target', '_self').submit();
                });

                $("#aceptar" ).click(function(){
                    if(confirm("Esta seguro de ejecutar la acción?")==true){
                       if($("#movimiento").val()){
                            if($("#movimiento" ).val()=='CANCELAR' && $("#motivo").val()==''){
                                alert("POR FAVOR, ESCRIBA EL MOTIVO.");
                            }else{
                                $('#frm').attr('action', "{{route('solicitudes.exoneracion.aceptar')}}");
                                $('#frm').attr('target', '_self').submit();
                            }
                        }else alert("POR FAVOR, SELECCIONE UN MOVIMIENTO.");
                    }                    
                });
                $('#borrador').click(function(){$('#frm').attr('action', "{{route('solicitudes.exoneracion.borrador')}}"); $('#frm').attr('target', '_blank').submit();});
            });   

            $("#movimiento" ).change(function(){
                    $("#motivo").hide();
                    $("#file").hide();                    
                    switch($("#movimiento" ).val()){
                        case "CANCELAR": case "SOPORTES":
                            $("#motivo").show("slow");                            
                        break;
                        case "AUTORIZAR":
                            $("#file").show("slow");
                        break;                        
                    }                    
                });
               
        </script>
    @endsection 
@endsection