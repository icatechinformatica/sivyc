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
    <div class="card card-body" style=" min-height:450px;">
        @if(count($message)>0)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif
        {{ Form::open(['route' => 'grupos.recibos.buscar', 'method' => 'post', 'id'=>'frm',  'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            @csrf
            {{ Form::hidden('ID', null, ['id'=>'ID']) }}
            {{ Form::hidden('idconcepto', null, ['id'=>'idconcepto']) }}
            <div class="row form-inline pl-4">  
                {{ Form::select('ejercicio', $anios, $request->ejercicio ,['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder' => 'EJERCICIO']) }}
                {{ Form::select('unidad', $unidades, $request->unidad ,['id'=>'unidad','placeholder' => '- UNIDAD -','class' => 'form-control  mr-sm-2']) }}
            </div>
            <div class="row form-inline pl-4"> 
                {{ Form::select('id_concepto', $conceptos, $request->id_concepto ,['id'=>'id_concepto','placeholder' => '- CONCEPTO -','class' => 'form-control  mr-sm-2']) }}
                {{ Form::select('status', ['PENDIENTE'=>'1. PENDIENTES','ASIGNADO'=>'2. ASIGNADOS','ENVIADO'=>'3. ENVIADOS','PAGADO'=>'4. PAGADOS','POR COBRAR'=>'5. POR COBRAR','CANCELADO'=>'6. CANCELADOS'], $request->status ,['id'=>'status','placeholder' => '- ESTATUS -','class' => 'form-control  mr-sm-2']) }}
                {{ Form::text('folio_grupo', $request->folio_grupo, ['id'=>'folio_grupo', 'class' => 'form-control mr-2', 'placeholder' => 'NO.RECIBO / GRUPO / CLAVE', 'title' => 'NO. RECIBO / FOLIO DE GRUPO / CLAVE ','size' => 25]) }}
                {{ Form::submit('BUSCAR', ['id' => 'buscar','name' => 'BUSCAR', 'class' => 'btn mr-5']) }}
                {{ Form::button('NUEVA ASIGNACIÓN', ['id' => 'nuevo','name' => 'nuevo', 'value'=>'nuevo','class' => 'btn']) }}                
            </div>    
        {!! Form::close() !!}    
        @if(count($data)>0)
            @if($data[0]->id_concepto==1)
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
                                    <td>
                                        @if($item->vb_dg==true or $item->clave!='0')
                                            {{ $item->nombre }}
                                        @endif
                                    </td>
                                    <td>{{ $item->hombre+$item->mujer }}</td>
                                    <td>{{number_format($item->costo, 2, '.', ',') }}</td>
                                    <td>{{ date('d/m/Y', strtotime($item->inicio)) }} - {{ date('d/m/Y', strtotime($item->termino)) }}</td>
                                    <td>{{ $item->hini }} - {{ $item->hfin }}</td>
                                    <td>{{ $item->status_folio }} 
                                        <span @if($item->status_recibo=="POR COBRAR")  class="text-danger"  @endif>
                                        @if($item->status_recibo)({{ $item->status_recibo }})@endif
                                        </span>
                                    
                                    </td>
                                    <td class="text-center">                                
                                        @if($item->file_pdf)
                                            <a class="nav-link pt-0"  href="{{$item->file_pdf}}" target="_blank">
                                                <i class="far fa-file-pdf fa-3x text-danger" title="DESCARGAR RECIBO DE PAGO OFICIALIZADO."></i>
                                            </a>
                                        @else
                                            <i  class="far fa-file-pdf  fa-3x text-muted pt-0"  title='ARCHIVO NO DISPONIBLE.'></i>
                                        @endif

                                    </td>
                                    <td class="text-center">
                                        <a class="nav-link pt-0"  onclick="ver('{{ ($item->folio_recibo AND $item->folio_recibo!= 'NO DISPONIBLE') ? $item->folio_recibo : $item->folio_grupo}}','{{ $item->id_concepto}}')">
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
                <div class="table-responsive p-0 m-0">                
                    <table class="table table-hover table-responsive-md  p-0 m-0 w-100">
                        <thead >
                            <tr >
                                <th scope="col">NRECIBO</th>
                                <th scope="col">UNIDAD</th>                            
                                <th scope="col">GRUPO</th>                                
                                <th scope="col">CONCEPTO</th>                                
                                <th scope="col">CUOTA</th>       
                                <th scope="col">DESCRIPCIÓN</th>
                                <th scope="col">FECHA</th>
                                <th scope="col">ESTATUS</th>
                                <th scope="col">RECIBO</th>
                                <th scope="col">VER</th>
                            </tr>
                        </thead>
                        <tbody class="w-100">
                            @foreach($data as $item)                                                      
                                <tr>
                                    <th scope="row">{{ $item->folio_recibo }}</th>
                                    <td>{{ $item->unidad}}</td>                                
                                    <td>{{ $item->folio_grupo }}</td>
                                    <td>{{ $item->concepto }}</td>
                                    <td>{{number_format($item->importe, 2, '.', ',') }}</td>                                
                                    <td>{{ $item->descripcion}}</td>
                                    <td>{{ date('d/M/Y', strtotime($item->fecha_expedicion)) }}</td>
                                    <td>{{ $item->status_folio }}</td>
                                    <td class="text-center">                                
                                        @if($item->file_pdf)
                                            <a class="nav-link pt-0"  href="{{$item->file_pdf}}" target="_blank">
                                                <i class="far fa-file-pdf fa-3x text-danger" title="DESCARGAR RECIBO DE PAGO OFICIALIZADO."></i>
                                            </a>                                           
                                        @else
                                            <i  class="far fa-file-pdf  fa-3x text-muted pt-0"  title='ARCHIVO NO DISPONIBLE.'></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a class="nav-link pt-0"  onclick="ver('{{ $item->folio_recibo }}','{{ $item->id_concepto}}')">
                                            @if($item->editar)                                                
                                                <i  class="fa fa-edit  fa-3x fa-lg text-success" title='EDITAR REGISTRO DE RECIBO DE PAGO.'></i>
                                            @else                                                
                                                <i  class="fa fa-search  fa-3x fa-lg" style="color: #826E19"  title='VER REGISTRO DE RECIBO DE PAGO.'></i>
                                            @endif
                                        </a>
                                    </td>
                                </tr>     
                            @endforeach      
                        
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='8'>
                                    {{ $data->links() }}              
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        @else                            
            <div class="text-center p-5 bg-light"><h5> <b>NO SE ENCONTRARON REGISTROS</b></h5></div>
        @endif        
    </div>
    @section('script_content_js') 
        <script language="javascript">
            
            $("#buscar" ).click(function(){                     
                $('#frm').attr('action', "{{route('grupos.recibos.buscar')}}"); 
                $('#frm').attr('target', '_self');
                $('#frm').submit();                 
            }); 
            
            $("#nuevo").click(function(){
               /* if($("#id_concepto").val()>1) $("#ID" ).val("NUEVO");                
                else $("#ID" ).val("");
                */
                $("#ID" ).val("NUEVO");  
                $('#frm').attr('action', "{{route('grupos.recibos')}}"); 
                $('#frm').attr('target', '_blank');
                $('#frm').submit();         
            });   
            function ver(ID,IDCONCEPTO){
                $('#ID').val(ID);
                $('#idconcepto').val(IDCONCEPTO);
                $('#frm').attr('action', "{{route('grupos.recibos')}}");
                $('#frm').attr('target', '_blank');
                $('#frm').submit();                
            }
        </script>  
    @endsection
@endsection