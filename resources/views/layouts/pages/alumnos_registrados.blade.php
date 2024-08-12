<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<div class="card-header">
    Preinscripción / Alumnos Matriculados
</div>
<div class="card card-body" style=" min-height:450px;">
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        {!! Form::open(['route' => 'alumnos.inscritos', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
            <div class="row form-inline">
                {{ Form::text('busquedapor', '', ['id'=>'busquedapor', 'class' => 'form-control mr-sm-2', 'placeholder' => 'CURP / NOMBRE / No.CONTROL/ CURSO / No.GRUPO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 48]) }}
                {{ Form::submit('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}
            </div>
        {!! Form::close() !!}
            <table class="table table-bordered">
                <caption>Catalogo de Alumnos</caption>
                <thead>
                    <tr>
                        <th scope="col">N°CONTROL</th>
                        <th width="85px">FOLIO</th>
                        <th scope="col">NOMBRE</th>
                        <th width="85px">N°GRUPO</th>
                        <th width="85px">CLAVE</th>
                        <th scope="col">CURSO</th>
                        <th scope="col">FECHAS</th>
                        <th scope="col">HORARIO</th>
                        <th width="100px">ACCIONES</th>
                        @can('alumno.inscrito.edit')
                            <th scope="col">MODIFICAR</th>
                        @endcan
                        <th scope="col">SID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $itemData)
                        <tr>
                            <td>{{$itemData->no_control}}</td>
                            <td>{{$itemData->folio}}</td>
                            <td scope="row">{{$itemData->apellido_paterno}} {{$itemData->apellido_materno}} {{$itemData->nombre}}</td>
                            <td>{{$itemData->folio_grupo}}</td>
                            <td scope="row">{{ $itemData->clave }}</td>
                            <td scope="row">{{ $itemData->nombre_curso }}</td>
                            <td scope="row">{{$itemData->inicio}} AL {{$itemData->termino}}</td>
                            <td scope="row">{{$itemData->horario}}</td>
                            @can('alumno.inscrito.show')
                                <td>
                                    <a href="{{route('alumnos.inscritos.detail', ['id' => base64_encode($itemData->id_registro)])}}" class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="VER REGISTRO">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('alumno.inscrito.edit')
                                <td>
                                    <a href="{{route('alumnos.update.registro', ['id' => base64_encode($itemData->id_registro)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                        <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endcan
                            <td>
                                {{-- @if ($itemData->es_cereso == true)
                                    <a href="{{route('documento.sid_cerrs', ['nocontrol' => base64_encode($itemData->id_registro)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" view="documento_sid_{{ $itemData->no_control }}.pdf" data-placement="top" title="DESCARGAR SID DE CERSS" target="_blank">
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                @else --}}
                                    <a href="{{route('documento.sid', ['nocontrol' => base64_encode($itemData->id_registro)])}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" view="documento_sid_{{ $itemData->no_control }}.pdf" data-placement="top" title="DESCARGAR SID" target="_blank">
                                        <i class="far fa-file-pdf" aria-hidden="true"></i>
                                    </a>
                                {{-- @endif --}}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            {{ $alumnos->appends(request()->query())->links() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        <br>
    </div>
    <br>
@endsection
