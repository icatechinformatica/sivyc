<!-- Creado por Orlando Chávez orlando@sidmac.com -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Perfil Profesional | Sivyc Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> hay algunos problemas con los campos.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <form action="{{ route('perfilinstructor-guardar') }}" method="post" id="registerperf_prof">
        @csrf
        <div class="card-header">
            <h1>CREACIÓN DE INSTRUCTOR</h1>
        </div>
        <div class="card card-body">
            <div>
                <label><h4>Perfiles Profesionales</h4></label>
                @if (count($validado) > 0)
                    <div class="pull-right">
                        @can('instructor.editar_fase2')
                            <a class="btn btn-info" href="{{route('instructor-perfil', ['id' => $id])}}">Agregar Perfil Profesional</a>
                        @endcan
                    </div>
                @endif
            </div>
            @if (count($perfil) > 0)
                <table class="table table-bordered table-responsive-md" >
                    <thead>
                        <tr>
                            <th scope="col">Grado Profesional</th>
                            <th scope="col">Area de la Carrera</th>
                            <th scope="col">Nivel de Estudio</th>
                            <th scope="col">Nombre de Institucion</th>
                            <th scope="col">Status</th>
                            <th width="85px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perfil as $item)
                            <tr>
                                <th scope="row">{{$item->grado_profesional}}</th>
                                <td>{{ $item->area_carrera }}</td>
                                <td>{{ $item->estatus }}</td>
                                <td>{{ $item->nombre_institucion }}</td>
                                <td>{{ $item->status }}</td>
                                <td>
                                    @can('instructor.editar_fase2')
                                        <a class="btn btn-info" href="{{route('instructor-perfilmod', ['id' => $item->id, 'idins' => $id])}}">Modificar</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="pull-left alert alert-warning">
                    <strong>Info!</strong> No hay Registros
                    <div class="pull-right">
                        @can('instructor.editar_fase2')
                            <a class="btn btn-info" href="{{route('instructor-perfil', ['id' => $id])}}">Agregar Perfil Profesional</a>
                        @endcan
                    </div>
                </div>
            @endif
            <br>
            <div>
                <label><h4>Especialidades a Impartir</h4></label>
                @if (count($validado) > 0)
                    <div class="pull-right">
                        @can('instructor.editar_fase2')
                            <a class="btn btn-info" href="{{ route('instructor-curso', ['id' => $id]) }}">Agregar Especialidad Validado para Impartir</a>
                        @endcan
                    </div>
                @endif
            </div>
                @if (count($validado) > 0)
                <table class="table table-bordered table-responsive-md" id="table-perfprof2">
                    <thead>
                        <tr>
                            <th scope="col">Especialidad</th>
                            <th scope="col">Memo. solicitud</th>
                            <th scope="col">Fecha de solicitud</th>
                            <th scope="col">Criterio Pago</th>
                            <th scope="col">Obsevaciones</th>
                            <th scope="col">Status</th>
                            <th width="85px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($validado as $item)
                            <tr>
                                <th scope="row">{{$item->nombre}}</th>
                                <td>{{ $item->memorandum_solicitud}}</td>
                                <td>{{ $item->fecha_solicitud}}</td>
                                <td style="text-align: center;">{{ $item->criterio_pago_id }}</td>
                                <td>{{ $item->observacion }}</td>
                                <td>{{ $item->status}}</td>
                                <td>
                                    @can('instructor.editar_fase2')
                                        <!--<a class="btn btn-info" href="{ route('instructor-editespectval', ['id' => item->especialidadinsid,'idins' => datains->id]) }}">Modificar</a>-->
                                        <a class="btn btn-info" href="{{ route('instructor-editespectval', ['id' => $item->espinid, 'idins' => $id]) }}">Modificar</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">
                    @if (count($perfil) > 0)
                        <strong>Info!</strong> No hay Registros
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <a class="btn btn-info" href="{{ route('instructor-curso', ['id' => $id]) }}">Agregar Especialidad Validado para Impartir</a>
                            @endcan
                        </div>
                    @else
                        <strong>Info!</strong> No hay Registros en Perfil Profesional, Añada Uno para Poder Agregar una Especialidad a Validar
                    @endif
                </div>
            @endif
        </div>
    </form>
@stop
@section('script_content_js')
    <script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
@endsection

