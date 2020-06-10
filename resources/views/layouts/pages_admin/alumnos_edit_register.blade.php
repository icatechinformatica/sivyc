<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>ALUMNOS INSCRITOS A MODIFICAR NÚMERO DE CONTROL</h2>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">NO CONTROL</th>
                        <th scope="col">NOMBRE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos_pre as $item_alumnos_pre)
                    <tr>
                        <td>{{$item_alumnos_pre->no_control}}</td>
                        <td>{{$item_alumnos_pre->apellidoPaterno}} {{$item_alumnos_pre->apellidoMaterno}} {{$item_alumnos_pre->nombrealumno}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        <br>
        <form action="{{ route('alumno_registrado.modificar.update', ['id' => $id_preinscripcion ])}}" id="frmalumno_registrado_modificar" method="post">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="numero_control_edit" class="control-label">NÚMERO DE CONTROL PARA MODIFICAR</label>
                    <input type="text" name="numero_control_edit" id="numero_control_edit" class="form-control" placeholder="NÚMERO DE CONTROL PARA MODIFICAR">
                </div>
                <div class="form-group col-md-8">
                    <label for="codigo_verificacion_edit" class="control-label">CÓDIGO DE VERIFICACIÓN</label>
                    <input type="text" name="codigo_verificacion_edit" id="codigo_verificacion_edit" class="form-control" placeholder="CÓDIGO DE VERIFICACIÓN">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success" >Modificar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br>
@endsection
