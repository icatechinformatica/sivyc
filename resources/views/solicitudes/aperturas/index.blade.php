<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Aperturas | SIVyC Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />

<div class="card-header">
    Solicitudes / Aperturas ARC01 y ARC02
</div>
<div class="card card-body" style=" min-height:450px;">
     @if ($message)
    <div class="row ">
        <div class="col-md-12 alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    </div>
    @endif

    {{ Form::open(['method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
    @csrf
    
    <div class="row">
        <div class="form-group col-md-2 mt-1">
            {{ Form::select('opt', ['ARC01'=>'ARC01','ARC02'=>'ARC02'], $opt, ['id'=>'opt','class' => 'form-control mr-sm-2'] ) }}
        </div>
        <div class="form-group col-md-3 mt-1">
            {{ Form::text('memo', $memo, ['id'=>'memo', 'class' => 'form-control', 'placeholder' => 'MEMORÁNDUM ARC', 'aria-label' => 'MEMORÁNDUM ARC', 'required' => 'required', 'size' => 25]) }}
        </div>
        <div class="form-group col-md-2 mr-sm-1">
            {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
        </div>
        @if(count($grupos)>0)
            @php
                if($movimientos)$activar = true; 
                else $activar = false;
                $munidad = $grupos[0]->munidad; 
                $nmunidad = $grupos[0]->nmunidad; 
                $status_curso = $grupos[0]->status_curso; 
                $pdf_curso = $grupos[0]->pdf_curso;            
            @endphp
                          
                <div class="form-group col-md-2 mr-sm-1">
                    <div class="dropdown show">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-print  text-white" title="Imprimir Memorándum">&nbsp;SOLICITUD</i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{$file}}" target="_blank">
                                {{ $grupos[0]->munidad."PDF" }}
                            </a>
                        </div>
                    </div>
                </div>
            
            @if (($opt== "ARC01" AND $status_solicitud != "VALIDADO") OR ($opt== "ARC02" AND $status_solicitud != "VALIDADO"))
            <div class="form-group col-md-2 mr-sm-1">
                @if ($opt== "ARC01")
                {{ Form::button('ARC 01 BORRADOR', ['id'=>'arc','class' => 'btn']) }}
                @else
                {{ Form::button('ARC 02 BORRADOR', ['id'=>'arc','class' => 'btn']) }}
                @endif
            </div>
            @endif        
            @if($grupos[0]->pdf_curso AND $activar == false)
                <div class="form-group col-md-2"> 
                    <a href="{{ $grupos[0]->pdf_curso }}" target="_blank" class="btn bg-warning">PDF AUTORIZACIÓN</a> 
                </div>  
            @endif
        @endif
    </div>
   

    @if(count($grupos)>0)
        <hr />
        <h4><b>GRUPOS</b></h4>        
        @include('solicitudes.aperturas.table')        
    @endif

    {!! Form::close() !!}
</div>
@section('script_content_js')
<script language="javascript">
    $(document).ready(function() {       
        //MOSTRAR BOTONES CONFORME AL MOVIMIENTO
        $("#mrespuesta").hide();
        $("#fecha").hide();
        $("#file").hide();

        $("#movimiento").change(function() { 
            switch ($("#movimiento").val()) {
                case "RETORNADO":
                    $("#mrespuesta").hide();
                    $("#fecha").hide();
                    $("#file").hide();
                    $("#espacio").show();
                    break;
                case "EN FIRMA":
                    $("#mrespuesta").show();
                    $("#fecha").show();
                    $("#file").hide();
                    $("#espacio").hide();
                    break;
                case "AUTORIZADO": alert($("#movimiento").val());
                    $("#mrespuesta").hide();
                    $("#fecha").hide();
                    $("#file").show();
                    $("#espacio").hide();
                    break;
                case "CANCELADO":
                    $("#mrespuesta").hide();
                    $("#fecha").hide();
                    $("#file").show();
                    $("#espacio").hide();
                    break;
                case "DESHACER":
                    $("#mrespuesta").hide();
                    $("#fecha").hide();
                    $("#file").hide();
                    $("#espacio").show();
                    break;
                case "CAMBIAR":
                    $("#mrespuesta").show();
                    $("#fecha").show();
                    $("#file").hide();
                    $("#espacio").hide();
                    break;
            }
        });


        $("#buscar").click(function() {
            $('#frm').attr('action', "{{route('solicitudes.aperturas')}}");
            $('#frm').attr('target', '_self').submit();
        });
        $("#arc").click(function() {
            if ($("#opt").val() == "ARC01") {
                $('#frm').attr('action', "{{route('solicitudes.aperturas.barc')}}");
                $('#frm').attr('target', '_blank').submit();
            } else if ($("#opt").val() == "ARC02") {
                $('#frm').attr('action', "{{route('solicitudes.aperturas.barc')}}");
                $('#frm').attr('target', '_blank').submit();
            }
        });
        $("#aceptar").click(function() { // alert($("#movimiento").val());
            if (confirm("Esta seguro de ejecutar la acción?") == true) {
                switch ($("#movimiento").val()) {
                    case "RETORNADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.retornar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "EN FIRMA":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.asignar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "DESHACER":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.deshacer')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "CAMBIAR":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.cambiarmemo')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "AUTORIZADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.autorizar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "CANCELADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.autorizar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    default:
                        alert("POR FAVOR SELECCIONE UN MOVIMIENTO.")
                        break;
                }
            }
        });
        $("#generar").click(function() {
            $('#frm').attr('action', "{{route('solicitudes.generar.autoriza')}}");
            $('#frm').attr('target', '_blank').submit();
        });
        $("#aceptar_preliminar").click(function() { // alert($("#movimiento").val());
            if (confirm("Esta seguro de ejecutar la acción?") == true) {
                switch ($("#pmovimiento").val()) {
                    case "RETORNADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.pretornar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    case "VALIDADO":
                        $('#frm').attr('action', "{{route('solicitudes.aperturas.pvalidar')}}");
                        $('#frm').attr('target', '_self').submit();
                        break;
                    default:
                        alert("POR FAVOR SELECCIONE UN MOVIMIENTO.")
                        break;
                }
            }
        });
    });
</script>
@endsection
@endsection
