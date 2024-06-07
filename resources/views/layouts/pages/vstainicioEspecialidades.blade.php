@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Especialidades | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
@endsection
@section('content')
    <div class="card-header">
        Catálogos / Especialidades
    </div>
    <div class="card card-body">
        <div class="row">
            <div class="col">
                {!! Form::open(['route' => 'especialidades.inicio', 'method' => 'GET', 'id'=>'frm', 'class' => 'form-inline' ]) !!}
                    {{ Form::text('busqueda', $busqueda, ['class' => 'form-control mr-sm-2 col-6', 'placeholder' => 'CLAVE/NOMBRE/PREFIJO/CAMPO', 'aria-label' => 'BUSCAR']) }}
                    {{ Form::submit('BUSCAR', ['id'=>'buscar','class' => 'btn']) }}                                        
                {!! Form::close() !!}
            </div>
            <div class="col">
                <div class="pull-right">                    
                    <a class="btn" href="{{route('especialidades.agregar')}}">NUEVA ESPECIALIDAD</a>
                    {{ Form::button('XLS', ['class' => 'btn', 'onclick' => "generar_xls()"]) }}
                </div>

            </div>
        </div>

        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th scope="col" class="w-2">Clave</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Campo de Formación</th>
                    <th scope="col">Creado</th>
                    <th scope="col">Actualizado</th>
                    <th scope="col">Creado por</th>
                    <th scope="col">Actualizado por</th>
                    
                    <th scope="col">Estado</th>
                    <th scope="col">Prefijo</th>
                    <th scope="col">Modificar</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($especialidades as $especialidad)
                    <tr>
                        <td>{{ $especialidad->clave }}</td>
                        <td>{{ $especialidad->nombre }}</td>
                        <td>{{ $especialidad->nameArea }}</td>
                        <td>{{ $especialidad->created_at !=null ? $especialidad->created_at->format('d/m/y') : '' }}</td>
                        <td>{{ $especialidad->updated_at != null ? $especialidad->updated_at->format('d/m/y') : '' }}</td>
                        <td>{{ $especialidad->nameCreated }}</td>
                        <td>{{ $especialidad->nameCreated != null ? $especialidad->nameCreated : $especialidad->nameUpdated }}</td>                        
                        @if ($especialidad->activo == 'true')
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
                        <td>{{ $especialidad->prefijo }}</td>
                        <td>
                            <a class="nav-link" alt="Editar Registro" href="{{ route('especialidades.modificar', ['id' => $especialidad->id]) }}">
                                <i  class="fa fa-edit  fa-2x fa-lg text-success"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{! $especialidades->links() }}
                {{ $especialidades->appends(Request::only(['busqueda']))->render() }}
            </div>
        </div>
    

</div>
@endsection
@section('script_content_js') 
    <script>
        $('#frm').attr('target', '_self');
        function generar_xls() {           //alert("pasa");
            $('#frm').attr('action', "{{route('catalogos.especialidades.xls')}}");             
            $('#frm').attr('target', '_blank');
            $('#frm').submit();  
        }             
    </script>
@endsection