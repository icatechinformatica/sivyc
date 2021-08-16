<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
   
    <div class="card-header">
        Consulta de Cursos Aperturados       
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if ($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <?php
            if(isset($curso)) $clave = $curso->clave;
            else $clave = null;
        ?>
        {{ Form::open(['route' => 'grupos.consultas', 'method' => 'post', 'id'=>'frm']) }}              

            <div class="row">
                <div class="form-group col-md-3">
                        {{ Form::text('clave', $clave, ['id'=>'clave', 'class' => 'form-control', 'placeholder' => 'CLAVE DEL CURSO O INSTRUCTOR', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 100]) }}
                </div>
                <div class="form-group col-md-2">
                        {{ Form::button('FILTRAR', ['class' => 'btn', 'type' => 'submit']) }}
                </div>                    
            </div>
            <div class="row">
                @include('grupos.consultas.table')
            </div>             
        {!! Form::close() !!}    
    </div>    
    @section('script_content_js')
        <script language="javascript">        
            function editar(clave){
                $("#clave").val(clave);
                $('#frm').attr('action', "{{route('grupos.consultas.calificaciones')}}"); $('#frm').submit();                 
            }
            function signar(clave){
                $("#clave").val(clave);
                $('#frm').attr('action', "{{route('grupos.consultas.folios')}}"); $('#frm').submit();                 
            }
            function cancelar(clave){
                $("#clave").val(clave);
                $('#frm').attr('action', "{{route('grupos.consultas.cancelarfolios')}}"); $('#frm').submit();                 
            }
        </script>  
    @endsection
@endsection
