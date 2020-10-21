<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Supervisión de Instructores | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <div class="card-header">
        Supervisi&oacute;n Escolar {{ $fecha }}
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
            
                {{ Form::open(['route' => 'supervision.escolar', 'method' => 'post', 'class' => 'form-inline', 'enctype' => 'multipart/form-data' ]) }}                                                        
                    {{ Form::date('fecha', null , ['class' => 'form-control datepicker mr-sm-1', 'placeholder' => 'FECHA', 'readonly' =>'readonly']) }}                        
                    {{ Form::select('tipo_busqueda', array( 'nombre_instructor' => 'INSTRUCTOR','clave_curso' => 'CLAVE CURSO', 'nombre_curso' => 'CURSO' ), null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'SELECIONAR'] ) }}                        
                    {{ Form::text('valor_busqueda', null, ['class' => 'form-control mr-sm-6', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) }}
                    {{ Form::button('FILTRAR', array('class' => 'btn', 'type' => 'submit')) }}
                {!! Form::close() !!}
                                 
                
        </div>
        <div class="row">                    
            @include('supervision.escolar.table')                    
        </div>
    </div>    
    <br>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>    
    <script src="{{ asset('js/supervisiones/datepicker.js') }}"></script>
@endsection
