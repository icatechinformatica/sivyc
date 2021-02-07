@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Matricular Alumno | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif
        <div style="text-align: center;">
            <h3><b>MODIFICACIÓN (SID - {{ $alumnos->no_control }})</b></h3>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS DEL CURSO</b></h4>
        </div>
        <form method="POST" id="form_sid_registro" action="{{ route('alumnos-cursos.update', ['idregistrado' => base64_encode($alumnos->id) ]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="horario" class="control-label">TIPO DE CURSO</label>
                    <select class="form-control" id="tipo_curso" name="tipo_curso_mod" required>
                        <option value="">--SELECCIONAR--</option>
                        <option {{ ($alumnos->tipo_curso == "PRESENCIAL") ? "selected" : "" }} value="PRESENCIAL">PRESENCIAL</option>
                        <option {{ ($alumnos->tipo_curso == "A DISTANCIA") ? "selected" : "" }} value="A DISTANCIA">A DISTANCIA</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="especialidad" class="control-label">ESPECIALIDAD:</label>
                    <select class="form-control" id="especialidad_sid_mod" name="especialidad_sid_mod">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($especialidades as $itemEspecialidad)
                            <option {{ ($alumnos->id_especialidad == $itemEspecialidad->id) ? "selected" : "" }} value="{{$itemEspecialidad->id}}">{{ $itemEspecialidad->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="cursos" class="control-label">CURSO:</label>
                    <select class="form-control" id="curso_sid_mod" name="curso_sid_mod">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($cursos as $itemCursos)
                            <option {{ ($alumnos->id_curso == $itemCursos->id) ? "selected" : "" }} value="{{$itemCursos->id}}">{{ $itemCursos->nombre_curso }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="horario" class="control-label">HORARIO:</label>
                    <input type="text" name="horario_mod" id="horario_mod" value="{{$alumnos->horario}}" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="grupo_mod" class="control-label">GRUPO:</label>
                    <input type="text" name="grupo_mod" id="grupo_mod" value="{{$alumnos->grupo}}" class="form-control" autocomplete="off">
                </div>
            </div>
            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    @can('alumno.inscrito.update')
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary" >Modificar</button>
                        </div>
                    @endcan
                </div>
            </div>
            <input type="hidden" name="no_control_update" id="no_control_update" value="{{$alumnos->no_control}}">
        </form>
    </div>
@endsection
