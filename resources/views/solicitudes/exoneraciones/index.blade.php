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
                <div class="form-group col-md-3">
                    <div class="dropdown show">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print  text-white" title="Imprimir Memorándum"> Memorándum Unidad</i>
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
                    <div class="form-group col-md-3">
                        {{ Form::button('MEMORÁNDUM BORRADOR', ['id'=>'borrador','class' => 'btn mt-1']) }}
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
                            <th style="padding: 1px;" rowspan="2" class="text-center">ID</th>
                            @if (($status=='PREVALIDACION') OR ($status=='SOLICITADO') OR ($status=='VALIDADO'))
                              <th style="padding: 1px;" rowspan="2" class="text-center">OBSERVACIÓN</th>  
                            @endif
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
                            <th style="padding: 1px;" rowspan="2" class="text-center">ORGANISMO</th>
                            <th style="padding: 1px;" colspan="3" class="text-center">DEPENDENCIA O GRUPO BENEFICIADO</th>
                        </tr>
                        <tr>
                            <th class="text-center">F</th>
                            <th class="text-center">M</th>
                            <th class="text-center">DEL</th>
                            <th class="text-center">AL</th>
                            <th class="text-center">OFICIO DE SOLICITUD</th>
                            <th class="text-center">RAZÓN DE LA EXONERACIÓN</th>
                            <th class="text-center">OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cursos as $item)
                            <tr>
                                <td class="text-center">{{$item->id}}</td>
                                @if (($status=='PREVALIDACION') OR ($status=='SOLICITADO') OR ($status=='VALIDADO'))
                                <td class="text-center">
                                    <div style="width: 300px;">
                                        {{ Form::textarea('respuesta['.$item->folio_grupo.']', $item->pobservacion, ['id' => 'respuesta['.$item->folio_grupo.']' ,'class' => 'form-control', 'placeholder' => 'OBSERVACIONES','rows' =>'2']) }}
                                    </div>
                                </td>
                                @endif
                                @php
                                    $total = $item->hombre + $item->mujer;
                                    $razon = $item->razon_exoneracion;
                                @endphp
                                <td class="text-center">{{$item->folio_grupo}}</td>
                                <td class="text-center">{{$item->tipo_curso}}</td>
                                <td class="text-center">{{$item->unidad}}</td>
                                <td class="text-center">{{$item->curso}}</td>
                                <td class="text-center">{{$item->costo}}</td>
                                <td class="text-center">{{$item->dura}}</td>
                                <td class="text-center">{{$item->inicio}}</td>
                                <td class="text-center">{{$item->termino}}</td>
                                <td class="text-center">{{$total}}</td>
                                <td class="text-center">{{$item->hombre}}</td>
                                <td class="text-center">{{$item->mujer}}</td>
                                <td class="text-center">{{$item->fini}}</td>
                                <td class="text-center">{{$item->ffin}}</td>
                                <td class="text-center">{{$item->instructor}}</td>
                                <td class="text-center">@if ($item->tipo_exoneracion == 'EXO') {{"X"}}  @endif</td>
                                <td class="text-center">@if ($item->tipo_exoneracion == 'EPAR') {{"X"}} @endif</td>
                                <td class="text-center">{{$item->turnado}}</td>
                                <td class="text-center">@if($item->status) {{$item->status}} @else {{"EN CAPTURA" }} @endif</td>
                                <td class="text-center">{{$item->no_convenio}}</td>
                                <td class="text-center">{{$item->depen}}</td>
                                <td class="text-center">{{$item->noficio}} <br> {{$item->foficio}}</td>
                                <td class="text-center">
                                    <div style="width: 300px;">
                                        {{ Form::select('opt',['MS'=>'MADRES SOLTERAS','AM'=>'ADULTOS MAYORES', 'BR'=>'BAJOS RECURSOS', 'D'=>'DISCAPACITADOS', 'PPL'=>'PERSONAS PRIVADAS DE LA LIBERTAD',
                                        'GRS'=>'GRUPOS DE REINSERCION SOCIAL', 'O'=>'OTRO'], 
                                        $razon, ['id'=>'opt','class' => 'form-control','placeholder' =>'- SELECCIONAR -','disabled'=>'disabled'] ) }}
                                    </div>
                                </td>
                                <td class="text-center"><div style="width: 400px;">{{$item->observaciones}}</div></td>
                            </tr>
                        @endforeach 
                    </tbody>                   
                </table>
            </div>
            @if (($status=='PREVALIDACION') OR ($status=='SOLICITADO'))
                <div class="row mt-2">
                    <div class="form-group col-md-4 my-2"></div>
                    <div class="form-group col-md-3 my-2" style="display: block;" id="espacio"></div>
                    <div class="form-group col-md-3 my-2">
                        {{ Form::select('movimiento', $movimientos, '', ['id'=>'movimiento','class' => 'form-control', 'placeholder'=>'- SELECCIONAR MOVIMIENTO -', 'onchange'=>'javascript:showContent()'] ) }}
                    </div>
                    <div class="custom-file form-group col-md-3 text-center my-2" id="file" style="display: none;">
                        <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
                        <label for="file_autorizacion" class="custom-file-label">AUTORIZACIÓN FIRMADA PDF</label>
                    </div>
                    <div class="form-group col-md-3 my-2" style="display: none;" id="content">
                        {{ Form::textarea('motivo', '', ['id' => 'motivo' ,'class' => 'form-control', 'placeholder' => 'MOTIVO DE LA CANCELACIÓN','rows' =>'1']) }}
                    </div>
                    <div class="form-group col-md-2"> 
                        {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn  bg-danger']) }} 
                    </div>
                </div>  
            @endif
            @if ($edicion)
                <div class="row mt-2">
                    <div class="form-group col-md-3">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">SOLICITUD DE:</label>
                        {!! Form::text('movimiento', $movimientos, ['id'=>'movimiento','class' => 'form-control','readonly'=>'readonly']) !!}
                        {{-- {{ Form::select('movimiento', $movimientos, $status, ['id'=>'movimiento','class' => 'form-control', 'placeholder'=>'- SELECCIONAR MOVIMIENTO -'] ) }} --}}
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">MOTIVO EDICIÓN:</label>
                        {{ Form::textarea('motivo', $edicion, ['id' => 'motivo' ,'class' => 'form-control', 'placeholder' => '','rows' =>'3','readonly'=>'readonly']) }}
                    </div>
                    <div class="form-group col-md-2 my-3"> 
                        {{ Form::button('ACEPTAR', ['id'=>'aceptar','class' => 'btn  bg-danger']) }} 
                    </div>
                </div>  
            @endif   
        @endif
        
    {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">      
            $(document).ready(function(){
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitudes.exoneracion')}}"); $('#frm').attr('target', '_self').submit();});
                $("#aceptar" ).click(function(){// alert($("#movimiento").val());
                    if(confirm("Esta seguro de ejecutar la acción?")==true){
                        switch($("#movimiento").val()){
                            case "RETORNAR":
                                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.retornar')}}");
                                    $('#frm').attr('target', '_self').submit();
                            break; 
                            case "VALIDAR":
                                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.validar')}}");
                                    $('#frm').attr('target', '_self').submit();
                            break; 
                            case "RETORNAR_VALIDADO":
                                $('#frm').attr('action', "{{route('solicitudes.exoneracion.rvalidado')}}");
                                $('#frm').attr('target', '_self').submit();
                            break;
                            case "AUTORIZAR":
                                $('#frm').attr('action', "{{route('solicitudes.exoneracion.autorizar')}}");
                                $('#frm').attr('target', '_self').submit();
                            break;
                            case "CANCELAR":
                                if (($("#motivo").val()=='') || ($("#motivo").val()==' ')) {
                                    alert("POR FAVOR ESCRIBA EL MOTIVO DE LA CANCELACIÓN.")
                                } else {
                                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.cancelar')}}");
                                    $('#frm').attr('target', '_self').submit();
                                }
                            break;
                            case "CANCELACION SOLICITUD DE EXONERACION":
                                if (($("#motivo").val()=='') || ($("#motivo").val()==' ')) {
                                    alert("POR FAVOR ESCRIBA EL MOTIVO DE LA CANCELACIÓN.")
                                } else {
                                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.cancelar')}}");
                                    $('#frm').attr('target', '_self').submit();
                                }
                            break;
                            case "EDICION LISTA DE ALUMNOS":
                                if (($("#motivo").val()=='') || ($("#motivo").val()==' ')) {
                                    alert("POR FAVOR ESCRIBA EL MOTIVO DE LA EDICIÓN.")
                                } else {
                                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.editar')}}");
                                    $('#frm').attr('target', '_self').submit();
                                }
                            break;
                            case "ACTUALIZACION SOPORTES":
                                    $('#frm').attr('action', "{{route('solicitudes.exoneracion.asoporte')}}");
                                    $('#frm').attr('target', '_self').submit();
                            break;
                            default:
                                alert("POR FAVOR SELECCIONE UN MOVIMIENTO.")
                            break;                   
                        }
                    }
                });
                $('#borrador').click(function(){$('#frm').attr('action', "{{route('solicitudes.exoneracion.borrador')}}"); $('#frm').attr('target', '_blank').submit();});
            });   
            function showContent(){
                element = document.getElementById("content");
                element1 = document.getElementById("file");
                element2 = document.getElementById("espacio");
                if ($("#movimiento").val()=='CANCELAR') {
                    element.style.display = 'block';
                    element1.style.display = 'none';
                    element2.style.display = 'none';
                } else if ($("#movimiento").val()=='AUTORIZAR') {
                    element.style.display = 'none';
                    element1.style.display = 'block';
                    element2.style.display = 'none';
                } else {
                    element.style.display = 'none';
                    element1.style.display = 'none';
                    element2.style.display = 'block';
                }
            }    
        </script>
    @endsection 
@endsection