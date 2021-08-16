<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Preinscripción | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
     <div class="card-header">
        Preinscripci&oacute;n / Buscar Grupo 
    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
            <div class="row">
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <div class="row">
                {{ Form::open(['route' => 'preinscripcion.buscar', 'method' => 'post', 'id' => 'frm','class' => 'form-inline', 'enctype' => 'multipart/form-data' ]) }}
                    {{ Form::text('valor_buscar', null, ['size'=>50, 'class' => 'form-control', 'placeholder' => 'GRUPO / CURSO', 'aria-label' => 'BUSCAR']) }}
                    {{ Form::submit('BUSCAR', array('class' => 'btn', 'type' => 'submit')) }}
                    {{ Form::hidden('folio_grupo',NULL, ['id'=>'folio_grupo']) }}
                {!! Form::close() !!}
        </div>
        <div class="row">
            @include('preinscripcion.buscar.table')
        </div>
    </div>
    <br>  

    @section('script_content_js') 
         <script language="javascript">
                              
                function  show(id){
                    //if(id>0){
                        $('#folio_grupo').val(id);
                        $('#frm').attr('action', "{{route('preinscripcion.show')}}"); $('#frm').submit(); 
                    //}alert(id);
                }
                    
        </script>
    @endsection
@endsection
