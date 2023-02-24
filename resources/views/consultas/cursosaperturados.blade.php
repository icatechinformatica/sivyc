<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />

    <div class="card-header">
        Consulta de Cursos Aperturados

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
        {{ Form::open(['method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data']) }}
            <div class="row form-inline">
                    {{ Form::text('valor', $valor, ['id'=>'valor', 'class' => 'form-control mr-sm-4 mt-3', 'placeholder' => 'CLAVE APERTURA / MEMORÁNDUM ARC-01', 'size' => 38]) }}
                    {{ Form::select('unidad', $unidades, $unidad ,['id'=>'unidad','class' => 'form-control  mr-sm-4 mt-3','title' => 'UNIDAD','placeholder' => '-SELECCIONAR-']) }}
                    {{ Form::select('opcion', ['INICIADOS'=>'CURSOS INICIADOS','TERMINADOS'=>'CURSOS TERMINADOS'], $opcion, ['id'=>'opcion', 'class' => 'form-control mr-sm-4 mt-3'] ) }}
                    {{ Form::date('fecha1', $fecha1 , ['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA INICIAL', 'title' => 'FECHA INICIAL', 'required' => 'required']) }}
                    {{ Form::date('fecha2', $fecha2, ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-4 mt-3', 'placeholder' => 'FECHA FINAL', 'title' => 'FECHA FINAL', 'required' => 'required']) }}
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
                            <th scope="col" class="text-center" width="8%">UNIDAD</th>
                            <th scope="col" class="text-center" width="8%">CLAVE</th>
                            <th scope="col" class="text-center" width="8%">ESCPECIALIDAD</th>
                            <th scope="col" class="text-center" width="12%">CURSO</th>
                            <th scope="col" class="text-center" width="4%">TIPO</th>
                            <th scope="col" class="text-center" width="2%">MOD</th>
                            <th scope="col" class="text-center" width="2%">DURA</th>
                            <th scope="col" class="text-center" width="2%">HOMBRES</th>
                            <th scope="col" class="text-center" width="2%">MUJERES</th>
                            <th scope="col" class="text-center" width="5%">INICIO</th>
                            <th scope="col" class="text-center" width="5%">TERMINO</th>
                            <th scope="col" class="text-center" width="5%">HORA_INI</th>
                            <th scope="col" class="text-center" width="5%">HORA_FIN</th>
                            <th scope="col" class="text-center" width="6%">ESTATUS</th>
                        </tr>
                    </thead>
                    @if(isset($data))
                    <?php $i=1;   ?>
                    <tbody>
                        @foreach($data as $d)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $d->unidad }}</td>
                                <td>{{ $d->clave }}</td>
                                <td>{{ $d->espe }}</td>
                                <td>{{ $d->curso }}</td>
                                <td class="text-center" >{{ $d->tcapacitacion }}</td>
                                <td class="text-center" >{{ $d->mod }}</td>
                                <td class="text-center" >{{ $d->dura }}</td>
                                <td class="text-center" >{{ $d->hombre }}</td>
                                <td class="text-center" >{{ $d->mujer }}</td>
                                <td class="text-center" >{{ $d->inicio }}</td>
                                <td class="text-center" >{{ $d->termino }}</td>
                                <td class="text-center" >{{ $d->hini }}</td>
                                <td class="text-center" >{{ $d->hfin }}</td>
                                <td class="text-center" >{{ $d->status }}</td>
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
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.cursosaperturados')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.cursosaperturados.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});
            });
            $(function() {
                $( ".datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });
            });
        </script>
    @endsection
@endsection
