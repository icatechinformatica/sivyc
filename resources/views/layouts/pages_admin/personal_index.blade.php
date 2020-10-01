<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERSONAL RECURSOS HUMANOS | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">

                    <div class="card-header border-0">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="row align-items-center">
                            <div class="col-8">
                              <h3 class="mb-0">PERSONAL ICATECH</h3>
                                {!! Form::open(['route' => 'personal.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                                    <select name="tipo_busqueda_personal" class="form-control mr-sm-2" id="tipo_busqueda_personal">
                                        <option value="">BUSCAR POR TIPO</option>
                                        <option value="numero_enlace">NÚMERO DE ENLACE</option>
                                        <option value="nombres">NOMBRE COMPLETO</option>
                                    </select>

                                    {!! Form::text('busquedaPersonal', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                                {!! Form::close() !!}
                            </div>
                            <div class="col-4 text-right">
                              <a href="{{route('personal.crear')}}" class="btn btn-sm btn-success">NUEVO PERSONAL</a>
                            </div>
                        </div>
                    </div>
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">N° ENLACE</th>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">DETALLES</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($directorio as $directorioItem)
                                    <tr>
                                        <td scope="row">{{ $directorioItem->numero_enlace }}</td>
                                        <td scope="row">
                                            {{ $directorioItem->apellidoPaterno}} {{ $directorioItem->apellidoMaterno}} {{$directorioItem->nombre}}
                                        </td>
                                        <td>
                                            <a href="{{route('personal.edit', ['id' => base64_encode($directorioItem->id)])}}"
                                                class="btn btn-info btn-circle m-1 btn-circle-sm"
                                                data-toggle="tooltip" data-placement="top"
                                                title="MODIFICAR REGISTRO">

                                                <i class="fa fa-users" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Card footer -->
                    <div class="card-footer py-4">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item">
                                    {{ $directorio->appends(request()->query())->links() }}
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc_admin.footer")
        <!-- FOOTER PORTAL DE GOBIERNO END-->
    </div>
@endsection
