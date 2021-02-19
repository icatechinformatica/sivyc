@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Modificacón de un curso | SIVyC Icatech')

@section('content')

    <div class="container-fluid px-5 mt-4">
        {{-- titulo --}}
        <div class="row pb-2">
            <h2>SOLICITUD MODIFICACIÓN  DE CURSO</h2>
        </div>

        {{-- formulario --}}
        <form id="formDataGenerales" action="" method="post">
            @csrf

            <div class="form-group row">
                <div class="form-group col">
                    <label for="num_solicitud" class="control-label">NÚMERO DE SOLICITUD</label>
                    <input type="text" class="form-control" id="num_solicitud" name="num_solicitud"
                    placeholder="NÚMERO DE SOLICITUD">
                </div>
                <div class="form-group col">
                    <label for="fecha_solicitud" class="control-label">FECHA DE SOLICITUD</label>
                    <input type='text' id="fecha_solicitud" autocomplete="off" readonly="readonly" 
                        name="fecha_solicitud" class="form-control datepicker" placeholder="FECHA SOLICITUD">
                </div>
            </div>
        </form>

        <hr class="my-3">

        {{-- form busar curso --}}
        {{-- <form id="formBuscar Curso" action="" method="post" class="mb-5">
            @csrf
            <div class="form-group row d-flex align-items-center">
                <div class="form-group col-4">
                    <input type="text" class="form-control" id="searchCurso" name="searchCurso"
                    placeholder="CAMPO CURSO">
                </div>
                <div class="form-group col-2">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">BUSCAR</button>
                    </div>
                </div>
            </div>
        </form> --}}

        <div class="form-row">
            <div class="col">
                {!! Form::open(['route' => 'solicitudesDA.inicio', 'method' => 'GET', 'class' => 'form-inline']) !!}
                {{-- <select name="busqueda" class="form-control mr-sm-2" id="busqueda">
                    <option value="">BUSCAR POR TIPO</option>
                    <option value="no_convenio">N° DE CONVENIO</option>
                    <option value="institucion">INSTITUCIÓN</option>
                    <option value="tipo_convenio">TIPO DE CONVENIO</option>
                    <option value="sector">SECTOR</option>
                </select> --}}

                {!! Form::text('busqueda_curso', null, ['class' => 'form-control col-4 mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR']) !!}
                <button type="submit" class="btn btn-outline-primary">BUSCAR</button>
                {!! Form::close() !!}
            </div>
        </div>

        {{-- {{$curso}} --}}
        @if ($curso->isEmpty())
            vacio
        @else
            <table class="table table-bordered table-striped mt-5">
                <thead>
                    <tr>
                        <th scope="col">CLAVE</th>
                        <th scope="col">UNIDAD</th>
                        <th scope="col">AREA</th>
                        <th scope="col">CURSO</th>
                        <th scope="col">ESPECIALIDAD</th>
                        <th scope="col">MOTIVO</th>
                        <th scope="col">DESCRIPCIÓN</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($curso as $course)
                            <tr>
                            <td>{{$course->clave}}</td>
                            <td>{{$course->unidad}}</td>
                            <td>{{$course->area}}</td>
                            <td>{{$course->curso}}</td>
                            <td>{{$course->espe}}</td>
                            <td>
                                <select name="motivo" class="form-control mr-sm-2" id="motivo">
                                    <option value="">SELECCIONE EL MOTIVO</option>
                                    <option value="no_convenio">REPROGRAMACIÓN FECHA/HORA</option>
                                    <option value="institucion">CAMBIO DE INSTRUCTOR</option>
                                    <option value="tipo_convenio">CANCELACIÓN DE CURSO</option>
                                </select>
                            </td>
                            <td>
                                <div class="form-group col">
                                    <textarea class="form-control" name="razon_exoneracion" id="razon_exoneracion"
                                        placeholder="RAZON DE LA EXONERACIÓN" rows="2"></textarea>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
    
@endsection

@section('script_content_js')

    <script>
        $('#formDataGenerales').validate({
            rules: {
                num_solicitud: {
                    required: true
                },
                fecha_solicitud: {
                    required: true
                }
            },
            messages: {
                num_solicitud: {
                    required: 'Número de solicitud requerido'
                },
                fecha_solicitud: {
                    required: 'Fecha de solicitud requerida'
                }
            }
        });

        $('#formBuscarCurso').validate({
            rules: {
                searchCurso: {
                    required: true
                }
            },
            messages: {
                searchCurso: {
                    required: 'Campo curso requerido'
                }
            }
            
        });

        $("#fecha_solicitud").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });
    </script>
@endsection