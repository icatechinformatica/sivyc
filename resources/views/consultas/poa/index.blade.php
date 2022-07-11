@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section("content_script_css")
<link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
@endsection
@section('content')    
    <div class="card-header">Consulta Cursos/Horas, Programado Anual  & Autorizados</div>
    <div class="card card-body" >        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        {{ Form::open(['method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data']) }} 
            <div class="row form-inline">
                {{ Form::select('opciones',['CURSOS'=>'CURSOS AUTORIZADOS','CUOTA'=>'CUOTA POR ALUMNO'], $request->opciones ,['id'=>'opciones','class' => 'form-control mr-sm-4','placeholder' => 'OPCIONES']) }}
                {{ Form::date('fecha1', $request->fecha1, ['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-3', 'placeholder' => 'FECHA INICIO', 'title' => 'FECHA INICO', 'required' => 'required']) }}
                {{ Form::date('fecha2', $request->fecha2, ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-3', 'placeholder' => 'FECHA TERMINO', 'title' => 'FECHA TERMINO', 'required' => 'required']) }}                                       
                {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4']) }}
                {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4']) }}
            </div>
        {!! Form::close() !!}        
        <div class="row">       
            @switch($request->opciones)
                @case ("CUOTA")
                    @include('consultas.poa.table_cuota')
                @break
                @default
                    @include('consultas.poa.table_poa')
                @breaK
            @endswitch
        </div>
    </div>
  
@endsection
@section('script_content_js')
    <script language="javascript">
        $(document).ready(function(){
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.poa')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.poa.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});
                $(function() {
                
            });
        });
    </script>
@endsection