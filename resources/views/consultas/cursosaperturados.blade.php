<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Consultas | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .multiselect-all,
        .multiselect-option{ text-align: left; display: flex; align-items: center; }        
    </style>
@endsection
@section('content')   

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
        @php
            if(isset($curso)) $clave = $curso->clave;
            else $clave = null;            
            $opciones = [];            
            $opciones['INICIADOS'] = 'CURSOS INICIADOS';
            $opciones['TERMINADOS'] = 'CURSOS TERMINADOS';            
        @endphp
        @can('consultas.poa')
            @php
                $opciones['AUTORIZADOS'] = 'CURSOS AUTORIZADOS';
                $opciones['EXONERADOS'] = 'CURSOS EXONERADOS';
            @endphp
        @endcan

        {{ Form::open(['method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data']) }}
            <div class="row form-inline">                    
                    {{ Form::select('unidad[]', $unidades, $values['unidad'] ?? null ,['id'=>'unidad','class' => 'form-control  mr-sm-4 select2','title' => 'UNIDAD','placeholder' => '-SELECCIONAR-', 'multiple' => true, 'size' => 1]) }}
                    {{ Form::select('opcion', $opciones, $values['opcion'] ?? null, ['id'=>'opcion', 'class' => 'form-control mr-sm-4 ml-3'] ) }}
                    {{ Form::date('fecha1', $values['fecha1'] ?? null , ['id'=>'fecha1', 'class' => 'form-control datepicker  mr-sm-4', 'placeholder' => 'FECHA INICIAL', 'title' => 'FECHA INICIAL', 'required' => 'required']) }}
                    {{ Form::date('fecha2', $values['fecha2'] ?? null, ['id'=>'fecha2', 'class' => 'form-control datepicker  mr-sm-4', 'placeholder' => 'FECHA FINAL', 'title' => 'FECHA FINAL', 'required' => 'required']) }}
                    {{ Form::text('valor', $values['valor'] ?? null, ['id'=>'valor', 'class' => 'form-control mr-sm-3', 'placeholder' => 'CLAVE APERTURA / MEMO ARC-01', 'size' => 30]) }}
                    {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-4']) }}
                    {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn mr-sm-4']) }}
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/1.1.1/css/bootstrap-multiselect.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/1.1.1/js/bootstrap-multiselect.min.js"></script>

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
            $(document).ready(function() {
                $('#unidad').multiselect({                    
                    includeSelectAllOption: true,
                    selectAllText: '- SELECCIONAR TODO-',
                    allSelectedText: 'Todos seleccionados',
                    enableFiltering: true,
                    buttonWidth: '100%',
                    nonSelectedText: '-UNIDAD-',
                    numberDisplayed: 0,
                    maxHeight: 300,     
                    buttonText: function(options, select) {
                        if (options.length === 0) {
                            return '-SELECCIONAR-'; // solo aparece si está vacío
                        } else if (options.length === 1) {
                            return $(options[0]).text(); // si selecciona uno, muestra su nombre
                        } else {
                            return options.length-1 + ' SELECCIONADOS'; // si selecciona varios
                        }
                    }
                });
                
            });
        </script>
    @endsection
@endsection
