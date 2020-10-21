<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.globals.layout')
@section('title', 'Supervisión de Unidades | SIVyC Icatech')
@section('content')
    <div class="card-header">
        Supervisi&oacute;n de Unidades del {{ $fecha }}
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
            {{ Form::open(['route' => 'supervision.unidades', 'method' => 'post', 'class' => 'form-inline', 'enctype' => 'multipart/form-data' ]) }}                                                        
                {{ Form::date('fecha', null , ['class' => 'form-control datepicker mr-sm-1', 'placeholder' => 'FECHA', 'readonly' =>'readonly']) }}                        
                {{ Form::button('FILTRAR', array('class' => 'btn', 'type' => 'submit')) }}
            {!! Form::close() !!}
                                 
                
        </div>
        
        <div class="row">                    
            @include('supervision.unidades.tabla_unidades')                    
        </div>
    </div>    
    <br>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>    
    <script src="{{ asset('js/supervision/datepicker.js') }}"></script>
@endsection
