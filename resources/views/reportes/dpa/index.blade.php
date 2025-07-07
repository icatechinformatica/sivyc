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
@section('title', 'Reportes- DPA | Nómina de Instructores | SIVyC Icatech')
@section('content')       
    <div class="card-header">
        Reportes / DPA- Nómina de Instructores
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
            <h4>Filtrar con Fecha de Reportado:</h4>
            <div class="row form-inline ml-1">   
                
                {{ Form::date('fecha1', $fecha1 ?? '' , ['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA INICIAL', 'title' => 'FECHA INICIAL', 'required' => 'required']) }}
                {{ Form::date('fecha2', $fecha2 ?? '', ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA FINAL', 'title' => 'FECHA FINAL', 'required' => 'required']) }}                  
                {{ Form::button('FILTRAR', ['class' => 'btn', 'onclick' => "filtrar('FILTRAR')"]) }}
                {{ Form::button('XLS', ['class' => 'btn', 'onclick' => "filtrar('XLS')"]) }}                
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
                                <th>#</th>
                                <th>N.Qna</th>
                                <th>Subsistema</th>
                                <th>Entidad</th>
                                <th>ZonaEconómica</th>
                                <th>RFC</th>
                                <th>CURP</th>
                                <th>Primer Apellido</th>
                                <th>Segundo Apellido</th>
                                <th>Nombres</th>                                
                                <th>Plaza</th>
                                <th>Categoría</th>
                                <th>Código</th>
                                <th>Horas</th>
                                <th>CCT</th>
                                <th>Reportado</th>
                                <th>Mes</th>
                            </tr>                            
                        </thead>                       
                            <tbody>
                                @foreach($data as $item) 
                                    <tr>
                                        <td>{{ $m++}}</td>
                                        <td>{{ $item->nqna}}</td>
                                        <td>{{ $item->subsistema}}</td>                                        
                                        <td>{{ $item->entidad}}</td>
                                        <td>{{ $item->ze}}</td>
                                        <td class="text-left">{{ $item->rfc}}</td>
                                        <td class="text-left">{{ $item->curp}}</td>                                        
                                        <td class="text-left">{{ $item->apaterno}}</td>
                                        <td class="text-left">{{ $item->amaterno}}</td>
                                        <td class="text-left">{{ $item->nombre}}</td>
                                        
                                        <td>{{ $item->tipo_plaza}}</td>
                                        <td>{{ $item->plaza}}</td>
                                        <td>{{ $item->codigo_plaza}}</td>                                        
                                        <td>{{ $item->horas}}</td>
                                        <td>{{ $item->cct}}</td>
                                        <td>{{ $item->fecha}}</td>
                                        <td>{{ $item->mes}}</td>
                                    </tr>     
                                @endforeach    
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='13'>                                                    
                                    </td>
                                </tr>
                            </tfoot>
                        @elseif(isset($fecha1))
                            <div class="row mt-4">
                                <div  class="col-md-12 alert alert-danger text-center" >
                                    {{ "NO SE ENCONTRARON REGISTROS." }}
                                </div>
                            </div>
                        @endif
                    </table>
                </div>
                
           
    </div>
    @section('script_content_js') 
        <script>
             function filtrar(opt) {
                $('#opcion').val(opt)
                $('#frm').attr('action', "{{route('reportes.dpa.generar')}}"); 
                if(opt=="FILTRAR")$('#frm').attr('target', '_self');
                else $('#frm').attr('target', '_blank');
                $('#frm').submit();  
             }             
        </script>
    @endsection
@endsection