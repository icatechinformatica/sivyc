<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>ALUMNOS MATRICULADOS</h2>

                    {!! Form::open(['route' => 'alumno_registrado.modificar.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_busqueda_por_alumno_registrado" class="form-control mr-sm-2" id="tipo_busqueda_por_alumno_registrado">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="no_control_busqueda">N° DE CONTROL</option>
                            <option value="nombres">NOMBRE</option>
                            <option value="curso">CURSO INSCRITO</option>
                            <option value="curp">CURP</option>
                        </select>

                        {!! Form::text('busquedaporAlumnoRegistrado', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
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
                        <th scope="col">NOMBRE</th>
                        <th scope="col">CURP</th>
                        <th scope="col">CURSO INSCRITO</th>
                        <th scope="col">MODIFICAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $itemAlumnos)
                        <tr {{ ($itemAlumnos->estatus_modificacion == true) ? "class='table-warning'" : "" }}>
                            <td>{{$itemAlumnos->no_control}}</td>
                            <td scope="row">{{$itemAlumnos->apellido_paterno}} {{$itemAlumnos->apellido_materno}} {{$itemAlumnos->nombrealumno}}</td>
                            <td>{{$itemAlumnos->curp_alumno}}</td>
                            <td>{{$itemAlumnos->nombre_curso}}</td>
                            <td>
                                <a href="{{route('alumno_registrado.modificar.show', ['id' => base64_encode($itemAlumnos->preiscripcion)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                    <i class="fa fa-database" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">{{ $alumnos->appends(request()->query())->links() }}</td>
                    </tr>
                </tfoot>
            </table>
        <br>
    </div>
    <br>
@endsection
