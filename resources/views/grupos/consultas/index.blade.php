<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />

    <div class="card-header">
        Grupos / Búsqueda
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
        {{ Form::open(['route' => 'grupos.consultas', 'method' => 'post', 'class' => 'form-inline', 'id'=>'frm']) }}
            <div class="row">                
                {{ Form::select('ejercicio', $anios, $ejercicio ??'' ,['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder' => 'EJERCICIO']) }}
                {{ Form::text('clave', $clave, ['id'=>'clave', 'class' => 'form-control mr-sm-2', 'placeholder' => 'FOLIO GRUPO / CLAVE / INSTRUCTOR', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 50]) }}                
                {{ Form::button('BUSCAR', ['class' => 'btn', 'type' => 'submit']) }}                
            </div>
            <div class="row">
                @include('grupos.consultas.table')
            </div>
        {!! Form::close() !!}
    </div>
    @section('script_content_js')
        <script language="javascript">
            function arc01(clave){
                $("#clave").val(clave);
                $('#frm').attr('action', "{{route('solicitud.apertura')}}"); $('#frm').submit();
            }
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
