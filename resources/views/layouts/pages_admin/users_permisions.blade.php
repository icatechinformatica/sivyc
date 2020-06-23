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
                    <h2>Alumnos Matriculados</h2>
                </div>
                <div class="pull-right">
                    <a href="{{route('permisos_roles.index')}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="ROLES Y PERMISOS">
                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">

            <table  id="table-instructor" class="table table-bordered Datatables">
                <caption>Catalogo de Alumnos</caption>
                <thead>
                    <tr>
                        <th scope="col">CORREO ELECTRÓNICO</th>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">PERMISOS</th>
                        <th scope="col">MODIFICAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $itemUsuarios)
                        <tr>
                            <td>{{$itemUsuarios->email}}</td>
                            <td scope="row">{{$itemUsuarios->name}}</td>
                            <td></td>
                            <td>
                                <a href="{{route('usuarios_permisos.show', ['id' => base64_encode($itemUsuarios->id)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                    <i class="fa fa-wrench" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                    </tr>
                </tfoot>
            </table>

        <br>
    </div>
    <br>
@endsection
