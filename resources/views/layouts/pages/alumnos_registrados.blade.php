<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>ALUMNOS MATRICULADOS</h2>
                </div>

                <div class="pull-right">
                    {!! Form::open(['route' => 'alumnos.inscritos', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_busqueda" class="form-control mr-sm-2" id="tipo_busqueda">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="no_control">N° DE CONTROL</option>
                            <option value="curso">CURSO</option>
                            <option value="nombres">NOMBRE</option>
                        </select>

                        {!! Form::text('busquedapor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">

            <table  id="table-instructor" class="table table-bordered Datatables">
                <caption>Catalogo de Alumnos</caption>
                <thead>
                    <tr>
                        <th scope="col">N° CONTROL</th>
                        <th scope="col">CURSOS</th>
                        <th scope="col">NOMBRE</th>
                        <th width="160px">ACCIONES</th>
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
                            <td scope="row">{{ $itemData->nombre_curso }}</td>
                            <td scope="row">{{$itemData->apellido_paterno}} {{$itemData->apellido_materno}} {{$itemData->nombre}}</td>
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
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endcan

                            <td>
                                @if ($itemData->es_cereso == true)
                                    <a href="{{route('documento.sid_cerrs', ['nocontrol' => base64_encode($itemData->id_registro)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" view="documento_sid_{{ $itemData->no_control }}.pdf" data-placement="top" title="DESCARGAR SID DE CERSS" target="_blank">
                                        <i class="fa fa-file" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <a href="{{route('documento.sid', ['nocontrol' => base64_encode($itemData->id_registro)])}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" view="documento_sid_{{ $itemData->no_control }}.pdf" data-placement="top" title="DESCARGAR SID" target="_blank">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                    </a>
                                @endif
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
