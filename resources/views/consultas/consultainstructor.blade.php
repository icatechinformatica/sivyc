@extends('theme.sivyc.layout')  {{--AGC--}}
@section('title', 'Consultas | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}
    </style>
    <div class="card-header">
        Consulta de Instructores
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
        <form action="{{route('consultas.instructor')}}" method="GET" id="cacahuate">
            <div class="form-row">
                <div class="from-group col-md-2">
                    <select name="unidad" id="unidad" class="form-control">
                        <option value=0 selected disabled>UNIDADES</option>
                        @foreach ($unidad as $item)
                            <option value="{{$item}}">{{$item}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <select name="tipo" class="form-control" placeholder=" " id="tipo">
                        <option value=0 selected disabled="">BUSCAR POR TIPO</option>
                        <option value="curp">Curp del Instructor</option>
                        <option value="nombre_instructor">Nombre del Instructor</option>
                        <option value="nombre_curso">Nombre del Curso</option>
                        <option value="curso">Clave del Curso</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <input type="text" name="busqueda" id="busqueda" placeholder="BUSCAR" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <input type="date" name="fecha_inicio" class="form-control" id="fecha_inicio" placeholder="FECHA INICIO">
                </div>
                <div class="form-group col-md-2">
                    <input type="date" name="fecha_termino" class="form-control" id="fecha_termino" placeholder="FECHA TERMINO">
                </div>
                <div class="form-group col-md-1">
                    <input type="submit" value="BUSCAR" class="btn btn-green">
                </div>
            </div>
            {{csrf_field()}}
        </form>
        <br>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>INSTRUCTOR</td>
                        <td>UNIDAD</td>
                        <td>CURSO</td>
                        <td>STATUS</td>
                        <td>FECHA DE INICIO</td>
                        <td>FECHA DE TERMINO</td>
                        <td>HORA INICIO</td>
                        <td>HORA TERMINO</td>
                        <td>DIAS</td>
                        
                    </tr>
                    @isset($consulta)
                    @foreach ($consulta as $item)
                    <tr>
                        <td>{{$item->nombre}}</td>
                        <td>{{$item->unidad}}</td>
                        <td>{{$item->curso}}</td>
                        <td>{{$item->status_curso}}</td>
                        <td>{{$item->inicio}}</td>
                        <td>{{$item->termino}}</td>
                        <td>{{$item->hini}}</td>
                        <td>{{$item->hfin}}</td>
                        <td>{{$item->dia}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="9">
                            {{$consulta->appends(request()->query())->links()}}
                        </td>
                    </tr>
                    @endisset
                </table>
            </div>
        </div>
    </div>
@endsection
