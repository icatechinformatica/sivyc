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

                <div class="pull-right">
                    {!! Form::open(['route' => 'registrado_consecutivo.index', 'method' => 'POST', 'class' => 'form-inline' ]) !!}

                        {!! Form::text('busquedaConsecutivo', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">

            <table  id="table-instructor" class="table table-bordered Datatables">
                <thead>
                    <tr>
                        <th scope="col">N° CONTROL</th>
                        <th scope="col">AÑO</th>
                        <th scope="col">CONSECUTIVO</th>
                        <th scope="col">UNIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consecutivos_unidad as $itemconsetuvitos)
                        <tr>
                            <td>{{$itemconsetuvitos->no_control}}</td>
                            <td>{{$itemconsetuvitos->anio}}</td>
                            <td>{{$itemconsetuvitos->consecutivo}}</td>
                            <td>{{ $itemconsetuvitos->unidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">{{ $consecutivos_unidad->appends(request()->query())->links() }}</td>
                    </tr>
                </tfoot>
            </table>
        <br>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection
