@extends('theme.sivyc.layout')  {{--AGC--}}
@section('title', 'Consultas | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />    
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
@endsection
@section('content')       
    <div class="card-header">
        Consultas / Instructores Asignados
    </div>
    <div class="card card-body" >
        <br />
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        {{ Form::open(['route' => 'consultas.instructor','method' => 'post','id'=>'frm', 'enctype' => 'multipart/form-data']) }}
            @csrf
            <div class="row form-inline">                
                    {{ Form::select('unidad', $unidades, $request->unidad ,['id'=>'unidad','class' => 'form-control mr-sm-2','title' => 'UNIDADES','placeholder' => 'UNIDADES']) }}                
                    {{ Form::select('tipo', ['instructor'=>'INSTRUCTOR','curp'=>'CURP','curso'=>'CURSO','clave'=>'CLAVE'], $request->tipo ,['id'=>'tipo','class' => 'form-control mr-sm-2','title' => 'BUSCAR POR','placeholder' => 'BUSCAR POR']) }}
                    {{ Form::text('busqueda', $request->busqueda, ['id'=>'busqueda','class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'title' => 'BUSCAR','size' => 38]) }}                    
                    {{ Form::date('fecha_inicio', $request->fecha_inicio , ['id'=>'fecha_inicio', 'class' => 'form-control datepicker mr-sm-2', 'placeholder' => 'FECHA INICIO', 'title' => 'FECHA INICIO']) }}                    
                    {{ Form::date('fecha_termino', $request->fecha_termino , ['id'=>'fecha_termino', 'class' => 'form-control datepicker mr-sm-2', 'placeholder' => 'FECHA TERMINO', 'title' => 'FECHA TERMINO']) }}
                    {{ Form::button('FILTRAR', ['id' => 'botonFILTRAR', 'name'=> 'boton', 'value' => 'FILTRAR', 'class' => 'btn mr-sm-1']) }}
                    {{ Form::button('XLS', ['id' => 'botonXLS', 'value' => 'XLS', 'class' => 'btn']) }}                                  
            </div>
            {{csrf_field()}}
        {!! Form::close() !!}
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>INSTRUCTOR</td>
                        <td>UNIDAD</td>
                        <td>GRUPO</td>
                        <td>CLAVE</td>
                        <td>MEMORÁNDUM</td>
                        <td>CURSO</td>
                        <td>ESPECIALIDAD</td>
                        <td>CURSO/CERTIFICACION</td>
                        <td>DURA</td>
                        <td>CAPACITACIÓN</td>
                        <td>ESTATUS</td>
                        <td>INICIO</td>
                        <td>TERMINO</td>
                        <td>HINI</td>
                        <td>HFIN</td>
                        <td>DIAS</td>
                        <td>LABORADOS</td>
                        <td>LUGAR O ESPACIO FISICO</td>
                        <td>OBSERVACIONES</td>
                    </tr>
                    @isset($consulta)
                    @foreach ($consulta as $item)
                    <tr>
                        <td>{{$item->nombre}}</td>
                        <td>{{$item->unidad}}</td>
                        <td><div style="width: 70px;">{{$item->folio_grupo}}</div></td>
                        <td><div style="width: 75px;">{{$item->clave}}</div></td>
                        <td><div style="width: 80px;">{{$item->munidad}}</div></td>
                        <td><div style="width: 150px;">{{$item->curso}}</div></td>
                        <td>{{$item->espe}}</td>
                        <td>{{$item->tipo_curso}}</td>
                        <td>{{$item->dura}}</td>
                        <td>{{$item->tcapacitacion}}</td>
                        <td>{{$item->status_curso}}</td>
                        <td>{{$item->inicio}}</td>
                        <td>{{$item->termino}}</td>
                        <td>{{$item->hini}}</td>
                        <td>{{$item->hfin}}</td>
                        <td>{{$item->dia}}</td>
                        <td>@if($item->tdias>0) {{ $item->tdias }} @else {{ $item->dias }}@endif DIAS</td>
                        <td><div style="width: 250px;">{{$item->efisico }}</div></td>
                        <td><div style="width: 450px;">{{$item->nota}}</div></td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="18">
                            {{$consulta->appends(request()->query())->links() }}
                        </td>
                    </tr>
                    @endisset
                </table>
            </div>
        </div>
    </div>
    @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                $("#botonFILTRAR" ).click(function(){ $('#frm').attr('action', "{{route('consultas.instructor')}}"); $("#frm").attr("target", '_self'); $('#frm').submit(); });
                $("#botonXLS" ).click(function(){ $('#frm').attr('action', "{{route('consultas.instructor.xls')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});
            });
            $(function() {
                $( ".datepicker" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });
            });
        </script>
    @endsection
@endsection
