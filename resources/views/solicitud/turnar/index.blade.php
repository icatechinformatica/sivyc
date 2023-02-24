<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Turnar Apertura | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
    
    <div class="card-header">
        Solicitud / Turnar ARC
    </div>
    <div class="card card-body" style=" min-height:450px;">              
    {{ Form::open(['route' => 'solicitud.apertura.enviar', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
        <div class="row">
            <div class="form-group col-md-2">
                <label for="">OPCIÓN:</label>
                {{ Form::select('opt', ['ARC01'=>'ARC01','ARC02'=>'ARC02'], $opt, ['id'=>'opt','class' => 'form-control mr-sm-2'] ) }}
            </div>
            <div class="form-group col-md-3">
                <label for="">NO. REVISIÓN O MEMORÁNDUM ARC:</label>    
                {{ Form::text('memo', $memo, ['id'=>'memo', 'class' => 'form-control', 'placeholder' => 'MEMORÁNDUM ARC', 'aria-label' => 'MEMORÁNDUM ARC', 'required' => 'required', 'size' => 25]) }}
            </div>
            <div class="form-group col-md-2">
                    <label for="">FECHA:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" value="{{date('Y-m-d')}}" readonly/>
                </div>  
            <div class="form-group col-md-1 mt-4">
                {{ Form::button('FILTRAR', ['id'=>'buscar','class' => 'btn']) }}
            </div>               
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
                @include('solicitud.turnar.table')
            </div>
        @endif

    {!! Form::close() !!}    
    </div>
    @section('script_content_js') 
        <script language="javascript">      
            $(document).ready(function(){
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('solicitud.apertura.turnar')}}"); $('#frm').attr('target', '_self').submit();});
                $("#mcambiar" ).click(function(){ if(confirm("Esta seguro de ejecutar la acción?")==true) { $('#frm').attr('action', "{{route('solicitud.apertura.cmemo')}}"); $('#frm').attr('target', '_self').submit();}});
                $("#enviar" ).click(function(){ if(confirm("Esta seguro de ejecutar la acción?")==true) { $('#frm').attr('action', "{{route('solicitud.apertura.enviar')}}"); $('#frm').attr('target', '_self').submit();}});
                $("#preliminar" ).click(function(){ if(confirm("Esta seguro de ejecutar la acción?")==true) {$('#frm').attr('action', "{{route('solicitud.apertura.preliminar')}}"); $('#frm').attr('target', '_self').submit();}});
                $("#generar" ).click(function(){
                    if (confirm("Continua si has guardado el memorándum ARC")==true) {
                        if($("#opt" ).val() == "ARC01"){
                            $('#frm').attr('action', "{{route('solicitud.generar.arc01')}}"); $('#frm').attr('target', '_blank').submit();
                        }else if($("#opt" ).val() == "ARC02"){
                            $('#frm').attr('action', "{{route('solicitud.generar.arc02')}}"); $('#frm').attr('target', '_blank').submit();
                        }
                    }
                });
                
            });        
        </script>
    @endsection 
@endsection