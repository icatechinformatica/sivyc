<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />  
    <style>   
        table tr td, table tr th{ font-size: 12px;}
    </style>
@endsection
@extends('theme.sivyc.layout')
@section('title', 'Grupos- Recibos de Pago | SIVyC Icatech')
@section('content')       
    <div class="card-header">
        Grupos / Buscar Recibos de Pago        
    </div>
    <div class="card card-body  p-5 style=" min-height:450px;"">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif
        {{ Form::open(['route' => 'grupos.recibos.buscar', 'method' => 'post', 'id'=>'frm',  'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            @csrf            
            <div class="row form-inline pl-4">  
                {{ Form::select('ejercicio', $anios, $request->ejercicio ,['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder' => 'EJERCICIO']) }}
                {{ Form::select('unidad', $unidades, $request->unidad ,['id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-2']) }}
                {{ Form::select('status', ['PENDIENTES'=>'1. PENDIENTES','ASIGNADOS'=>'2. ASIGNADOS','ENVIADOS'=>'3. ENVIADOS','PAGADOS'=>'4. PAGADOS','POR COBRAR'=>'5. POR COBRAR','CANCELADOS'=>'6. CANCELADOS'], $request->status ,['id'=>'status','placeholder' => '- ESTATUS -','class' => 'form-control  mr-sm-2']) }}
                {{ Form::text('folio_grupo', $request->valor, ['id'=>'folio_grupo', 'class' => 'form-control mr-2', 'placeholder' => 'NO.RECIBO / GRUPO / CLAVE', 'title' => 'NO. RECIBO / FOLIO DE GRUPO / CLAVE ','size' => 25]) }}
                {{ Form::submit('BUSCAR', ['id' => 'buscar','name' => 'BUSCAR', 'class' => 'btn mr-5']) }}
                {{ Form::button('NUEVA ASIGNACIÓN', ['id' => 'asignar','name' => 'ASIGNAR', 'class' => 'btn']) }}                
            </div>    
        {!! Form::close() !!}    
        @if(count($data)>0)
            <div class="table-responsive p-0 m-0">                
                <table class="table table-hover table-responsive" id="tabla">
                    <thead>
                        <tr>
                            <th scope="col">NRECIBO</th>
                            <th scope="col">UNIDAD</th>                            
                            <th scope="col">GRUPO</th>
                            <th scope="col" class="col-1">CLAVE</th> 
                            <th scope="col">CURSO</th>
                            <th scope="col">INSTRUCTOR</th>
                            <th scope="col">BENEF</th>
                            <th scope="col">CUOTA</th>
                            <th scope="col">FECHAS</th>
                            <th scope="col">HORARIO</th>
                            <th scope="col">ESTATUS</th>
                            <th scope="col">RECIBO</th>
                            <th scope="col">VER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $item)
                            <tr>
                                <th scope="row">{{ $item->folio_recibo }}</th>
                                <td>{{ $item->unidad}}</td>                                
                                <td>{{ $item->folio_grupo }}</td>
                                <td>{{ $item->clave }}</td>
                                <td>{{ $item->curso }}</td>
                                <td>{{ $item->nombre }}</td>
                                <td>{{ $item->hombre+$item->mujer }}</td>
                                <td>{{number_format($item->costo, 2, '.', ',') }}</td>
                                <td>{{ date('d/m/Y', strtotime($item->inicio)) }} - {{ date('d/m/Y', strtotime($item->termino)) }}</td>
                                <td>{{ $item->hini }} - {{ $item->hfin }}</td>
                                <td>{{ $item->status_folio }}</td>
                                <td class="text-center">                                
                                    @if($item->file_pdf)
                                        <a class="nav-link pt-0" href="{{$path_files}}{{ $item->file_pdf}}" target="_blank">
                                            <i  class="far fa-file-pdf  fa-3x text-danger"  title='DESCARGAR RECIBO DE PAGO OFICIALIZADO.'></i>
                                        </a>
                                    @else
                                        <i  class="far fa-file-pdf  fa-3x text-muted pt-0"  title='ARCHIVO NO DISPONIBLE.'></i>
                                    @endif

                                </td>
                                <td class="text-center">
                                    <a class="nav-link pt-0"  onclick="ver('{{ $item->folio_grupo }}')">
                                        <i  class="fa fa-search  fa-3x fa-lg" style="color: #826E19"  title='VER REGISTRO DE RECIBO DE PAGO.'></i>
                                    </a>
                                </td>
                            </tr>     
                        @endforeach     
                       
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='14'>
                                {{ $data->links() }}              
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>
                @else                            
                    <div class="text-center p-5 bg-light">
                    <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                    </div>
                @endif        
        </div>
    @section('script_content_js') 
        <script language="javascript">
            
            $("#buscar" ).click(function(){                     
                $('#frm').attr('action', "{{route('grupos.recibos.buscar')}}"); 
                $('#frm').attr('target', '_self');
                $('#frm').submit();                 
            }); 
            
            $("#asignar" ).click(function(){                     
                $('#frm').attr('action', "{{route('grupos.recibos')}}"); 
                $('#frm').attr('target', '_blank');
                $('#frm').submit();         
            });   
            function ver(folio){
                $('#folio_grupo').val(folio);
                $('#frm').attr('action', "{{route('grupos.recibos')}}");
                $('#frm').attr('target', '_blank');
                $('#frm').submit();                
            }
        </script>  
    @endsection
@endsection