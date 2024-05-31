<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <style>           
        table tr { height:10px; }
        table tr td, table tr th{ font-size: 11px; margin:0px; padding:0px;}
        table tr td{ font-size: 12px; margin:0px; padding:0px;}
    </style>
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Reportes- PAT Concentrado | SIVyC Icatech')
@section('content')       
    <div class="card-header">
        Reportes / PAT Concentrado
    </div>
    <div class="card card-body">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif        
        {{ Form::open(['method' => 'post', 'id'=>'frm',  'enctype' => 'multipart/form-data']) }}
            @csrf                   
            <div class="row form-inline">                     
                {{ Form::select('organismo', $organismos, $organismo ,['id'=>'organismo','class' => 'form-control mr-sm-2','title' => 'ORGANISMOS ADMINISTRATIVOS','placeholder' => '- SELECCIONAR ORGANISMO -']) }}
                {{ Form::select('mes', $meses, $mes ,['id'=>'mes','class' => 'form-control mr-sm-2','title' => 'MES','placeholder' => 'MES']) }}
                {{ Form::select('ejercicio', $anios, $ejercicio ,['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder' => 'EJERCICIO']) }}
                {{ Form::button('FILTRAR', ['class' => 'btn', 'onclick' => "filtrar('FILTRAR')"]) }}
                {{ Form::button('XLS', ['class' => 'btn', 'onclick' => "filtrar('XLS')"]) }}
                {{ Form::button('PDF', ['class' => 'btn', 'onclick' => "filtrar('PDF')"]) }}
            </div>        
            {{ Form::hidden('opcion', null, ['id'=>'opcion']) }}
        {!! Form::close() !!}          
                <div class="table-responsive p-0 m-0 w-100">                
                    <table class="table table-hover">
                    @if(count($data)>0) 
                        <thead>
                            <tr>
                                <th rowspan="2">NÚM</th>
                                <th rowspan="2">
                                    <div style="width: 350px;">FUNCIONES</div>
                                </th>
                                <th rowspan="2">
                                    <div style="width: 350px;">ACTIVIDADES</div>    
                                </th>
                                <th class="col-1" rowspan="2">UM</th> 
                                <th rowspan="2"> TIPO UM</th>
                                <th colspan="2">META ANUAL</th>
                                @php 
                                    $m = 1;
                                @endphp
                                @foreach($data[1] as $idorg => $org )
                                    <th colspan="2" class="text-center">{{$m++}}<br/> {{ $org }}</th>
                                @endforeach
                                
                            </tr>
                            <tr>                                
                                <th>PROG</th>
                                <th>ALC</th>
                                @foreach($data[1] as $org )
                                    <th>PROG</th>
                                    <th>ALC</th>
                                @endforeach
                            </tr>
                        </thead>
                       
                            @php
                                $i = 1;
                                $funcion = $itemfuncion = null;
                            @endphp
                            <tbody>
                                @foreach($data[0] as $item) 
                                    @php
                                        if($itemfuncion <> $item->funcion)$funcion = $itemfuncion = $item->funcion;
                                        else $funcion = null;                                        
                                    @endphp                              
                                    <tr>
                                        <th scope="row"> @if($funcion){{ $i++ }}@endif</th>
                                        <td>{{ $funcion }}</td>
                                        <td>{{ $item->procedimiento}}</td>
                                        <td>{{ $item->unidadm}}</td>
                                        <td>{{ $item->tipo_unidadm}}</td>
                                        <td>{{ $item->programada}}</td>
                                        <td>{{ $item->alcanzada}}</td>
                                        @foreach($data[1] as $idorg => $org )
                                            @php
                                                $prog = "prog_".$idorg;
                                                $alc = "alc_".$idorg;                                                
                                            @endphp
                                            <td>{{ $item->$prog}}</td>
                                            <td>{{ $item->$alc}}</td>                                            
                                        @endforeach
                                    </tr>     
                                @endforeach    
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='14'>
                                                    
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                
           
    </div>
    @section('script_content_js') 
        <script>
             function filtrar(opt) {
                $('#opcion').val(opt)
                $('#frm').attr('action', "{{route('reportes.pat.generar')}}"); 
                if(opt=="FILTRAR")$('#frm').attr('target', '_self');
                else $('#frm').attr('target', '_blank');
                $('#frm').submit();  
             }
             /*
             $("#botonFILTRAR").click(function(){                
                $('#frm').attr('action', "{{route('reportes.pat.generar')}}"); 
                $('#frm').attr('target', '_self');
                $('#frm').submit();         
            });*/
        </script>
    @endsection
@endsection