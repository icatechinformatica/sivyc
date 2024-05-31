<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <style>           
        thead { position: sticky; top: 0; z-index: 10; background-color: #ffffff; }
        .table-responsive { height:550px; overflow:scroll;  width: 100%}        
        
        .table tr th{ background-color: #ccc;  border: 1px solid #fff; text-align: center; font-size: 10px; margin:0px; padding:3px; font-weight:bold; vertical-align: middle;}                
        .table tr td{ font-size: 11px; margin:0px; padding:0px; text-align: center;}
    </style>
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Reportes- PAT Concentrado | SIVyC Icatech')
@section('content')       
    <div class="card-header">
        Reportes / PAT Concentrado
    </div>
    <div class="card card-body p-5">
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
                {{ Form::select('organismo', $organismos, $organismo ,['id'=>'organismo','class' => 'form-control mr-sm-2 ml-4','title' => 'ORGANISMOS ADMINISTRATIVOS','placeholder' => '- SELECCIONAR ORGANISMO -']) }}
                {{ Form::select('mes', $meses, $mes ,['id'=>'mes','class' => 'form-control mr-sm-2','title' => 'MES','placeholder' => 'MES']) }}
                {{ Form::select('ejercicio', $anios, $ejercicio ,['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder' => 'EJERCICIO']) }}
                {{ Form::button('FILTRAR', ['class' => 'btn', 'onclick' => "filtrar('FILTRAR')"]) }}
                {{ Form::button('XLS', ['class' => 'btn', 'onclick' => "filtrar('XLS')"]) }}
                {{ Form::button('PDF', ['class' => 'btn', 'onclick' => "filtrar('PDF')"]) }}
            </div>        
            {{ Form::hidden('opcion', null, ['id'=>'opcion']) }}
        {!! Form::close() !!}          
                <div class="table-responsive p-0">                
                    <table class="table table-hover p-0">
                    @if(count($data)>0) 
                        @php 
                            $m = 1;
                        @endphp
                        <thead>
                            <tr>
                                <th rowspan="2">NÚM.</th>
                                <th rowspan="2">
                                    <div style="width: 120px;">FUNCIONES</div>
                                </th>
                                <th rowspan="2">
                                    <div style="width: 180px;">ACTIVIDADES</div>    
                                </th>
                                <th class="col-1" rowspan="2">UNIDAD DE MEDIDA</th> 
                                <th rowspan="2"> TIPO U.M.</th>
                                <th colspan="2">META ANUAL</th>                                
                                @foreach($data[1] as $idorg => $org )
                                    <th colspan="2">{{$m++}}<br/> {{ $org }}</th>
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
                                $cont = $i = 1;
                                $funcion = $itemfuncion = null;
                            @endphp
                            <tbody>
                                @foreach($data[0] as $item) 
                                                              
                                    <tr>
                                        
                                        @if($itemfuncion <> $item->funcion)                                            
                                            @php                                        
                                                $funcion = $itemfuncion = $item->funcion;
                                                $cont = 1;                                        
                                            @endphp
                                            <td rowspan="{{$item->rowspan}}" style="font-size: 12px; vertical-align: top;"><b > @if($funcion){{ $i++ }}@endif </b></td>
                                            <td rowspan="{{$item->rowspan}}" style="font-size: 12px; vertical-align: top;">{{ $funcion }}</td>
                                        @else 
                                            @php
                                                $funcion = null;   
                                            @endphp                                     
                                        @endif

                                        <td style="font-size: 12px; text-align: left; padding:5px;">{{$cont++}}.- {{ $item->procedimiento}}</td>
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
        </script>
    @endsection
@endsection