@extends('theme.sivyc.layout')  {{--AGC--}}
@section('title', 'Consultas | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />    
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
@endsection
@section('content')       
    <div class="card-header">
        Consultas / Contratos Electrónicos y Autografos 
    </div>
    <div class="card card-body pt-4" >
         @if($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        
        {{ Form::open(['route' => 'consultas.contratosfirmados','method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data']) }}
            @csrf
            <div class="row form-inline">                    
                    {{ Form::select('unidad', $unidades, $req['unidad']??0 ,['id'=>'unidad','class' => 'form-control mr-sm-2','title' => 'UNIDADES','placeholder' => '- UNIDAD -']) }}
                    {{ Form::select('estatus', [''=>'- SELECCIONAR -','ELECTRONICOS'=>'ELECTRÓNICOS','AUTOGRAFOS'=>'AUTÓGRAFOS'], $req['estatus']??0 ,['id'=>'estatus','class' => 'form-control mr-sm-2']) }}
                    {{ Form::date('fecha_inicio', $req['fecha_inicio']??'' , ['id'=>'fecha_inicio', 'class' => 'form-control datepicker mr-sm-2', 'placeholder' => 'FECHA INICIO', 'title' => 'FECHA INICIO']) }}
                    {{ Form::date('fecha_termino', $req['fecha_termino']??'' , ['id'=>'fecha_termino', 'class' => 'form-control datepicker mr-sm-2', 'placeholder' => 'FECHA TERMINO', 'title' => 'FECHA TERMINO']) }}
                    {{ Form::text('busqueda', $req['busqueda']??'', ['id'=>'busqueda','class' => 'form-control mr-sm-2', 'placeholder' => 'CONTRATO / CLAVE / INSTRUCTOR ', 'title' => 'BUSCAR','size' => 25]) }}                  
                    {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-1']) }}
                    {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn']) }}                                  
            </div>
            {{csrf_field()}}
        {!! Form::close() !!}
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>#</td>
                        <td class="text-center">CONTRATO</td>
                        <td class="text-center">ARC01</td>
                        <td class="text-center">CLAVE</td>
                        <td >CURSO</td>
                        <td>INSTRUCTOR</td>
                        <td class="text-center">UNIDAD</td>                        
                        <td>DTA</td>
                        <td>FIRMADO</td>                     
                    </tr>
                    @isset($consulta)
                    @php
                        $n = 1;
                    @endphp
                    @foreach ($consulta as $item)
                    <tr>
                        <td> {{ $n++ }}</td>
                        <td>{{$item->numero_contrato}}</td>
                        <td>{{$item->munidad}}</td>    
                        <td><div style="width: 125px;">{{$item->clave}}</div></td>
                        <td>{{$item->curso}}</td>
                        <td>{{$item->instructor}}</td>
                        <td>{{$item->unidad}}</td>  
                        <td>{{$item->dta}}</td>                    
                        <td class="text-center">{{$item->firmado}}</td>                        
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="18">
                            {{$consulta->appends(request()->query())->links() }}
                        </td>
                    </tr>
                    @endisset
                </table>
            </div>
        </div>
    </div>
    @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.contratosfirmados')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.contratosfirmados.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});
            });
            $(function() {
                $( ".datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });
            });
        </script>
    @endsection
@endsection
