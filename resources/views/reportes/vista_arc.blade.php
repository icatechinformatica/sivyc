@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'APERTURAS | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50"> 
        <div class="row">
            <h4>Aperturas y Modificaciones</h4>  
        </div>
        <div class="row">
            <div class="pull-left">
                {{ Form::open(['route' => 'pdf.generar', 'method' => 'post', 'class' => 'form-inline', 'enctype' => 'multipart/form-data','target'=>'_blank' ]) }}
                {{ Form::text('memo_apertura', null , ['class' => 'form-control  mr-sm-1', 'placeholder' => 'MEMO APERTURA']) }}
                {{ Form::date('fecha_apertura', null , ['class' => 'form-control  mr-sm-1', 'placeholder' => 'FECHA APERTURA']) }}              
                {!! Form::submit( 'ARC01', ['id'=>'arc01', 'class' => 'btn btn-dark', 'name' => 'submitbutton'], 'ARC01')!!}
                {!! Form::submit( 'ARC02', ['id'=>'arc02', 'class' => 'btn btn-dark', 'name' => 'submitbutton'], 'ARC02')!!}
                {!! Form::close() !!}
            </div> 
        </div>
    </div>    
    <script src="{{ asset('js/scripts/datepicker-es.js') }}"></script>
@endsection