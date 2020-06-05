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
                    <h2>Registro de Alumnos</h2>
                </div>
                <br>

                <div class="pull-right">
                    @can('alumnos.inscripcion-paso1')
                        <a class="btn btn-success btn-lg" href="{{route('alumnos.preinscripcion')}}"> Nuevo</a>
                    @endcan
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        @if ($contador > 0)
            <table  id="table-instructor" class="table table-bordered Datatables">
                <caption>Catalogo de Alumnos</caption>
                <thead>
                    <tr>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">CURP</th>
                        <th scope="col">DOCUMENTOS</th>
                        <th scope="col">ACCIONES</th>
                        <th scope="col">MODIFICAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retrieveAlumnos as $itemData)
                        <tr>
                            <td scope="row">{{$itemData->nombre}} {{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}}</td>
                            <td>{{$itemData->curp}}</td>
                            <td>
                                <a href="{{route('alumnos.preinscripcion.paso2',['id' => base64_encode($itemData->id)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="ANEXAR DOCUMENTOS">
                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                @can('alumnos.inscripcion-paso2')
                                    <a href="{{route('alumnos.presincripcion-paso2', ['id' => base64_encode($itemData->id)])}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="INSCRIBIR">
                                        <i class="fa fa-gears" aria-hidden="true"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>
                                @can('alumnos.inscripcion-paso2')
                                    <a href="{{route('alumnos.presincripcion-modificar', ['id' => base64_encode($itemData->id)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="alert alert-warning" role="alert">
                <h2>NO HAY ALUMNOS REGISTRADOS!</h2>
            </div>
        @endif
        <br>
    </div>
    <br>
@endsection
