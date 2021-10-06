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
    {{ Form::open(['method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
         <div class="row">
            <div class="form-group col-md-2 mt-2">
                {{ Form::select('opt', ['ARC01'=>'ARC01','ARC02'=>'ARC02'], $opt, ['id'=>'opt','class' => 'form-control mr-sm-2'] ) }}
            </div>
            <div class="form-group col-md-2 mt-2">
                    {{ Form::text('memo', $memo, ['id'=>'memo', 'class' => 'form-control', 'placeholder' => 'MEMORÁNDUM ARC', 'aria-label' => 'MEMORÁNDUM ARC', 'required' => 'required', 'size' => 25]) }}
            </div>            
            <div class="form-group col-md-2">
                {{ Form::button('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div> 
            @if(count($grupos)>0) 
                @if($file)
                    <div class="form-group col-md-3">
                        <div class="dropdown show">
                            <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-print  text-white" title="Imprimir Memorándum"> Memorándum Unidad</i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="{{$file}}" target="_blank">
                                {{ $grupos[0]->munidad."PDF" }}
                                </a>
                            </div>
                        </div>
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
        
        @if(count($grupos)>0)
            <hr/> 
            <h4><b>GRUPOS</b></h4>
            <div class="row">
                @include('solicitudes.aperturas.table')
            </div>
        @endif

    {!! Form::close() !!} 
</div>
    @section('script_content_js') 
        <script language="javascript">      
            $(document).ready(function(){
                $("#opt").change(function(){                    
                    $("#memo").val("") ;
                    $('#frm').attr('action', "{{route('solicitudes.aperturas')}}"); $('#frm').attr('target', '_self').submit();
                });
                
                //MOSTRAR BOTONES CONFORME AL MOVIMIENTO
                $("#mrespuesta").hide();
                $("#fecha").hide();                    
                $("#file").hide();
                
                $("#movimiento").change(function(){ 
                    switch($("#movimiento").val()){
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
                        case "AUTORIZADO":
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


                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitudes.aperturas')}}"); $('#frm').attr('target', '_self').submit();});
                $("#aceptar" ).click(function(){// alert($("#movimiento").val());
                    if(confirm("Esta seguro de ejecutar la acción?")==true){
                        switch($("#movimiento").val()){
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
                            default:
                                alert("POR FAVOR SELECCIONE UN MOVIMIENTO.")
                            break;                   
                        }
                    }
                });
                $("#generar" ).click(function(){                   
                    $('#frm').attr('action', "{{route('solicitudes.generar.autoriza')}}"); $('#frm').attr('target', '_blank').submit();                   
                });
                
            });        
        </script>
    @endsection 
@endsection