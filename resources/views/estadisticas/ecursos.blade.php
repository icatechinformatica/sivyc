<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
   
    <div class="card-header">
        Estadísticas de Cursos
    </div>
    <div class="card card-body" style=" min-height:450px;">
        @if($message)
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
       {{ Form::open(['method' => 'post','id'=>'frm']) }} 
            <div class="row form-inline">
                    {{ Form::select('unidad', $unidades, $unidad ,['id'=>'unidad','class' => 'form-control  mr-sm-4 mt-3','placeholder' => 'TODAS LAS UNIDADES','title' => 'TODAS LAS UNIDADES']) }}
                    {{ Form::select('tcapacitacion', ['A DISTANCIA'=>'A DISTANCIA','PRESENCIAL'=>'PRESENCIAL','TODOS'=>'TODOS'], $tcapacitacion ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3')) }}                    
                    {{ Form::date('finicial', $finicial , ['id'=>'finicial', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA INICAL', 'title' => 'FECHA INICIAL']) }}                                        
                    {{ Form::date('ffinal', $ffinal , ['id'=>'ffinal', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA FINAL', 'title' => 'FECHA FINAL']) }}                                       
                    {{ Form::text('tregistros', $tregistros, ['id'=>'tregistros', 'class' => 'form-control mr-sm-4 mt-3 numero', 'placeholder' => 'TOTAL REGISTROS', 'title' => 'TOTAL REGISTROS','size' => 5]) }}
                    {{ Form::select('status_curso', ['AUTORIZADO'=>'AUTORIZADO','EN FIRMA'=>'EN FIRMA','VALIDADO'=>'VALIDADO','CANCELADO'=>'CANCELADO'], $status_curso ,array('id'=>'mod','class' => 'form-control  mr-sm-4 mt-3','title'=>'ESTATUS')) }} 
                    {{ Form::select('categoria', $categorias, $categoria ,array('id'=>'categoria','class' => 'form-control  mr-sm-4 mt-3','title'=>'CATEGORIA')) }} 
                    
                    <div class="form-check mt-3 mr-sm-4">UNIDAD &nbsp;
                        <input class="form-check-input custom-checkbox checkbox-lg " type="checkbox" value="1"  name="uni" @if($uni){{'checked'}} @endif>
                    </div>
                    <div class="form-check mt-3 mr-sm-4">CURSO &nbsp;
                        <input class="form-check-input custom-checkbox checkbox-lg " type="checkbox" value="1"  name="curso" @if($curso){{'checked'}} @endif>
                    </div>
                    <div class="form-check mt-3 mr-sm-4">INSTRUCTOR &nbsp;
                        <input class="form-check-input custom-checkbox checkbox-lg" type="checkbox" value="1"  name="instructor" @if($instructor){{'checked'}} @endif>
                    </div>
                {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4 mt-3']) }}
                {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4 mt-3']) }}
           
            </div>
        {!! Form::close() !!}
        
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @if($uni)<th scope="col" class="text-center" width="2%">UNIDAD</th>@endif    
                             @if($curso)
                                <th scope="col" class="text-center" width="2%">CATEGORIA</th>
                                <th scope="col" class="text-center" width="5%">CURSO</th>
                             @endif
                            @if($instructor)<th scope="col" class="text-center" width="5%">INSTRUCTOR</th>@endif
                            <th scope="col" class="text-center" width="2%">TOTAL CURSO</th>                            
                        </tr>
                    </thead>
                    @if(isset($cursos))
                    <?php $n=1;   ?>
                    <tbody>                        
                        @foreach($cursos as $c)
                            <tr>   
                            @if($uni) <td> {{ $c->unidad }}</td>@endif                     
                            @if($curso)
                                <td> {{ $c->categoria }} </td>
                                <td> {{ $c->curso }} </td>
                            @endif
                            @if($instructor) <td> {{ $c->instructor }}</td>@endif
                                <td class="text-center"> {{ $c->total}} </td>                                                                                                                                  
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
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('estadisticas.ecursos')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('estadisticas.ecursos.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});                                
            });
        </script>  
    @endsection
@endsection
