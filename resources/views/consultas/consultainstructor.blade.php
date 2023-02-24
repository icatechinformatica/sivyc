@extends('theme.sivyc.layout')  {{--AGC--}}
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
    <div class="card-header">
        Consulta de Instructores Asignados
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
        {{ Form::open(['route' => 'consultas.instructor','method' => 'get','id'=>'frm', 'enctype' => 'multipart/form-data']) }}
            @csrf
            <div class="form-row">
                <div class="from-group col-md-2">
                    {{ Form::select('unidad', $unidades, $request->unidad ,['id'=>'unidad','class' => 'form-control','title' => 'UNIDADES','placeholder' => 'UNIDADES']) }}    
                </div>
                <div class="form-group col-md-2">
                    {{ Form::select('tipo', ['curp'=>'CURP DEL INSTRUCTOR','instructor'=>'NOMBRE DEL INSTRUCTOR','curso'=>'NOMBRE DEL CURSO','clave'=>'CLAVE DE CURSO'], $request->tipo ,['id'=>'tipo','class' => 'form-control','title' => 'BUSCAR POR','placeholder' => 'BUSCAR POR']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::text('busqueda', $request->busqueda, ['id'=>'busqueda','class' => 'form-control', 'placeholder' => 'BUSCAR', 'title' => 'BUSCAR','size' => 38]) }}                    
                </div>
                <div class="form-group col-md-2">
                    {{ Form::date('fecha_inicio', $request->fecha_inicio , ['id'=>'fecha_inicio', 'class' => 'form-control datepicker', 'placeholder' => 'FECHA INICIO', 'title' => 'FECHA INICIO']) }}                    
                </div>
                <div class="form-group col-md-2">
                    {{ Form::date('fecha_termino', $request->fecha_termino , ['id'=>'fecha_termino', 'class' => 'form-control datepicker ', 'placeholder' => 'FECHA TERMINO', 'title' => 'FECHA TERMINO']) }}                    
                </div>
                <div class="form-group col-md-1">
                    <input type="submit" value="BUSCAR" class="btn btn-green">
                </div>
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
                        <td>SERVICIO</td>
                        <td>DURA</td>
                        <td>CAPACITACIÓN</td>
                        <td>ESTATUS</td>
                        <td>INICIO</td>
                        <td>TERMINO</td>
                        <td>HINI</td>
                        <td>HFIN</td>
                        <td>DIAS</td>
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
                        <td><div style="width: 250px;">{{$item->efisico }}</div></td>
                        <td><div style="width: 350px;">{{$item->nota}}</div></td>
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
@endsection
