<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
   
    <div class="card-header">
        Consulta de Folios Asignados
        
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if($message)
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
                    {{ Form::select('unidad', $unidades, $unidad ,['id'=>'unidad','class' => 'form-control  mr-sm-4 mt-3','title' => 'UNIDAD']) }}
                    {{ Form::select('mod', ['EXT'=>'EXT','CAE'=>'CAE','GRAL'=>'GENERAL'], $mod ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3','title' => 'MODALIDAD')) }}                    
                    {{ Form::text('finicial', $finicial, ['id'=>'finicial', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => ' FOLIO INICIAL', 'title' => ' FOLIO INICIAL','size' => 20]) }}
                    {{ Form::text('ffinal', $ffinal, ['id'=>'ffinal', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => 'FOLIO FINAL', 'title' => 'FOLIO FINAL', 'size' => 20]) }}                    
                    {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4 mt-3']) }}
                    {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4 mt-3']) }}
            </div>
        {!! Form::close() !!}
        
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                           <th scope="col" class="text-center" width="1%">#</th>
                            <th scope="col" class="text-center" width="5%">UNIDAD</th>
                            <th scope="col" class="text-center" width="5%">FOLIO</th>
                            <th scope="col" class="text-center" width="5%">EXPEDICI&Oacute;N</th>
                            <th scope="col" class="text-center" width="7%">ESTATUS</th>                            
                            <th scope="col" class="text-center" width="8%">MOTIVO</th>
                            <th scope="col" class="text-center" width="7%">MATR&Iacute;CULA</th>
                            <th scope="col"  width="15%">ALUMNOS</th>
                            <th scope="col" width="12%">CLAVE</th>
                            <th scope="col" width="20%">CURSO</th>
                            <th scope="col" class="text-center" width="5%">AUTORIZACI&Oacute;N</th>
                        </tr>
                    </thead>
                    @if(isset($folios))
                    <?php $n=1;   ?>
                    <tbody>                        
                        @foreach($folios as $f)
                            <tr>
                                <td class="text-center"> {{ $n++ }} </td>
                                <td class="text-center"> {{ $f->unidad }} </td>                            
                                <td class="text-center"> {{ $f->folio }} </td>
                                <td class="text-center"> @if($f->fecha_expedicion) {{ date('d/m/Y', strtotime($f->fecha_expedicion)) }} @endif</td>
                                <td class="text-center"> {{ $f->movimiento}} </td>
                                <td class="text-center"> {{ $f->motivo}} </td>
                                <td> {{ $f->matricula}}  </td>
                                <td>{{ $f->alumno}} </td>
                                <td class="text-center"> {{ $f->clave}} </td>
                                <td class="text-center"> {{ $f->curso}} </td>
                                <td class="text-center">
                                 @if($f->file_autorizacion)
                                    <a class="nav-link"  href="{{ $path_file.$f->file_autorizacion }}" target="_blank">
                                        <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                                    </a>  
                                @else 
                                    {{ "NO ADJUNTADO"}}
                                @endif
                                </td>                                                                                                      
                            </tr>                            
                        @endforeach                       
                    </tbody>                    
                    
                    <tfoot>                       
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
              
    </div>    
     @section('script_content_js') 
        <script language="javascript">           
            $(document).ready(function(){ 
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.folios')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.folios.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});                                
            });
        </script>  
    @endsection
@endsection
