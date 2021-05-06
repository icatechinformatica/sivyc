<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Solicitudes | SIVyC Icatech')
@section('content_script_css')    
        <link rel="stylesheet" href="{{asset('css/bootstrap4-toggle.min.css') }}"/>
        <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')   
    <div class="card-header">
        Cancelación de Folios       
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
        {{ Form::open(['route' => 'grupos.cancelacionfolios', 'method' => 'post','id'=>'frmbuscar', 'enctype' => 'multipart/form-data' ]) }}              
            @csrf
            <h5>BÚSQUEDA POR MATRÍCULA DEL ALUMNO O CLAVE DEL CURSO</h5>
            <hr/>
            <div class="row form-inline">           
                {{ Form::text('clave', $clave, ['id'=>'buscar', 'class' => 'form-control mr-sm-2 mt-3', 'placeholder' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 30]) }}
                {{ Form::text('matricula', NULL, ['id'=>'buscar', 'class' => 'form-control mr-sm-2 mt-3', 'placeholder' => 'MATRICULA', 'size' => 20]) }}
                {{ Form::button('BUSCAR', ['class' => 'form-control mr-sm-2 mt-3 btn', 'type' => 'submit']) }}                
            </div>
        {!! Form::close() !!}
        {{ Form::open(['route' => 'grupos.cancelacionfolios.guardar', 'method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data' ]) }} 
        @csrf
            <div class="row">
                @include('grupos.cancelacionfolios.table')
            </div>
            
            <hr/>            
            <div class="row form-inline justify-content-end">
                {{ Form::text('num_autorizacion',NULL, ['id'=>'num_autorizacion', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => 'NUM. AUTORIZACI&Oacute;N', 'title' => 'NUM. AUTORIZACI&Oacute;N', 'required' => 'required', 'size' => 25]) }}
                <div class="input-group mr-sm-4 mt-3">
                    <div class="custom-file">
                        <input type="file" id="file_autorizacion" name="file_autorizacion" accept="application/pdf" class="custom-file-input" required />
                        <label for="file_autorizacion" class="custom-file-label">PDF AUTORIZACI&Oacute;N</label>
                    </div>
                </div> 
                {{ Form::label('MOTIVO:','',['class' => 'mr-sm-4 mt-3'])}}                                                 
                {{ Form::select('motivo', $motivo, '' ,array('id'=>'motivo','class' => 'form-control  mr-sm-4 mt-3','title' => 'MOTIVO', 'required' => 'required')) }}
                {{ Form::button('CANCELAR FOLIO(S)', ['class' => 'btn mr-sm-4 mt-3 bg-danger', 'type' => 'submit']) }}                                    
            </div>
        {!! Form::close() !!}
    </div>
@endsection
