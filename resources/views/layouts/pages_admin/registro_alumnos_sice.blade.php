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

                    {!! Form::open(['route' => 'alumnos_registrados_sice.inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}

                        {!! Form::text('busqueda_curp', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
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
                        <th scope="col">CURP</th>
                        <th scope="col">N° DE CONTROL</th>
                        <th scope="col">MODIFICAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnosRegistradosSice as $itemRegistradoSice)
                        <tr {{ ($itemRegistradoSice->estado_modificado == true) ? class="table-success" : "" }}>
                            <td>{{$itemRegistradoSice->curp}}</td>
                            <td>{{$itemRegistradoSice->no_control}}</td>
                            <td>
                                <a href="{{route('registro_alumnos_sice.modificar.show', ['id' => base64_encode($itemRegistradoSice->id)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                    <i class="fa fa-database" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">{{ $alumnosRegistradosSice->appends(request()->query())->links() }}</td>
                    </tr>
                </tfoot>
            </table>
        <br>
    </div>
    <br>
@endsection
