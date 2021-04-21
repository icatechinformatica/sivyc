<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'DTA Folios | SIVyC Icatech')
@section('content_script_css')    
        <link rel="stylesheet" href="{{asset('css/bootstrap4-toggle.min.css') }}"/>
        <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')   
    <div class="card-header">
        Consulta de Actas de Folios       
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
        {{ Form::open(['method' => 'post','id'=>'frm']) }} 
            <div class="row form-inline">
                    {{ Form::select('unidad', $unidades, $unidad ,['id'=>'unidad','class' => 'form-control  mr-sm-4 mt-3','title' => 'UNIDAD', 'placeholder' => 'TODO']) }}
                    {{ Form::select('mod', ['EXT'=>'EXT','CAE'=>'CAE','GRAL'=>'GENERAL'], $mod ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3','title' => 'MODALIDAD', 'placeholder' => 'TODO')) }}                    
                    {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4 mt-3']) }}
                    {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4 mt-3']) }}
            </div>
        {!! Form::close() !!}
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">UNIDAD</th>
                            <th scope="col">MODALIDAD</th>
                            <th scope="col">FOLIO INICIAL</th>
                            <th scope="col">FOLIO FINAL</th>                
                            <th scope="col">TOTAL</th>
                            <th scope="col" class="text-center">ASIGNADOS</th>
                            <th scope="col">NUM. ACTA</th>
                            <th scope="col">FECHA ACTA</th>
                            <th scope="col">PUBLICADO</th>
                            <th scope="col">PDF ACTA</th>                                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    @foreach ($data as $item)
                        <?php $pendientes = $item->total-$item->contador; ?>
                        <tr>     
                             <td>{{ $i++ }}</td>          
                             <td>{{ $item->unidad }}</td>
                             <td>{{ $item->mod }}</td>
                             <td>{{ $item->finicial }}</td>
                             <td>{{ $item->ffinal }}</td>
                             <td>{{ $item->total }}</td>
                             <td class="text-center">{{ $item->contador }}</td>
                             <td>{{ $item->num_acta }}</td>
                             <td>{{ $item->facta }}</td>                 
                             <td class="text-center"> 
                             @if($item->activo==true)
                                <b>SI</b>
                             @else 
                                <p class="text-danger"><b>NO</b></p>            
                             @endif
                             </td>
                             <td>
                                @if($item->file_acta)
                                    <a class="nav-link"  href="{{ $path_file.$item->file_acta }}" target="_blank">
                                        <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                                    </a>  
                                @else 
                                    {{ "NO ADJUNTADO"}}
                                @endif
                             </td> 
                          
                        </tr>
                    @endforeach
                        <tr>
                            <td colspan="12" >
                               {{ $data->render() }}
                             </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
     @section('script_content_js') 
        <script language="javascript">           
            $(document).ready(function(){ 
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.lotes')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.lotes.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});                                
            });
        </script>  
    @endsection
@endsection

