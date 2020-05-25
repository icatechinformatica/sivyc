@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Editar Cursos | Sivyc Icatech')
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
        </div><br />
    @endif
    <form method="POST" action="{{ route('cursos-catalogo.update')}}" method="post" id="frmcursoscatalogo" enctype="multipart/form-data">
        @csrf
        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso"><h1>Formulario de Cursos</h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
          <!-- Unidad -->
          <div class="form-group col-md-6">
            <label for="areaCursos" class="control-label">ÁREA</label>
            <select class="form-control" id="areaCursos" name="areaCursos">
                <option value="">--SELECCIONAR--</option>
                @foreach ($areas as $itemareas)
                    <option value="{{$itemareas->id}}">{{$itemareas->formacion_profesional}}</option>
                @endforeach
            </select>
          </div>
          <!--Unidad Fin-->
          <!-- nombre curso -->
          <div class="form-group col-md-6">
            <label for="especialidadCurso" class="control-label">Especialidad</label>
            <select class="form-control" id="especialidadCurso" name="especialidadCurso">
                <option value="">--SELECCIONAR--</option>
            </select>
          </div>
          <!-- nombre curso FIN-->
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nombrecurso" class="control-label">Nombre del Curso</label>
                <input type="text" class="form-control" id="nombrecurso" name="nombrecurso" value="{{$cursos->nombre_curso}}">
            </div>
            <div class="form-group col-md-6">
                <label for="unidad_accion_movil" class="control-label">Unidad Acción Móvil</label>
                <input type="text" class="form-control" id="unidad_accion_movil" name="unidad_accion_movil" value="{{ $cursos->unidad_amovil }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="modalidad  " class="control-label">Modalidad</label>
                <select class="form-control" id="modalidad" name="modalidad">
                  <option value="">--SELECCIONAR--</option>
                  <option value="CAE">CAE</option>
                  <option value="EXT">EXT</option>
                </select>
            </div>
            <!--clasificacion-->
            <div class="form-group col-md-6">
                <label for="clasificacion  " class="control-label">Clasificación</label>
                <select class="form-control" id="clasificacion" name="clasificacion">
                    <option value="">--SELECCIONAR--</option>
                    <option value="A-BASICO">A-BASICO</option>
                    <option value="B-INTERMEDIO">B-INTERMEDIO</option>
                    <option value="C-AVANZADO">C-AVANZADO</option>
                </select>
            </div>
            <!--clasificacion END-->
        </div>
        <div class="form-row">
          <!-- Destinatario -->
          <div class="form-group col-md-6">
            <label for="horas" class="control-label">Horas</label>
            <input type="number" class="form-control" id="horas" name="horas" placeholder="Horas" value="{{ $cursos->horas }}">
          </div>
          <!-- Destinatario END -->
          <!-- Puesto-->
          <div class="form-group col-md-6">
            <label for="costo" class="control-label">Costo</label>
            <input type="text" class="form-control" id="costo_curso" name="costo" placeholder="costo" value="{{ $cursos->costo }}">
          </div>
          <!-- Puesto END-->

        </div>
        <div class="form-row">
            <!-- Duracion -->
            <div class="form-group col-md-6">
              <label for="duracion" class="control-label">Duración</label>
              <input type="text" class="form-control" id="duracion" name="duracion" placeholder="duracion" value="{{ $cursos->duracion }}">
            </div>
            <!-- Duracion END -->
            <!-- Perfil-->
            <div class="form-group col-md-6">
              <label for="perfil" class="control-label">Perfil</label>
              <input type="text" class="form-control" id="perfil" name="perfil" placeholder="perfil" value="{{ $cursos->perfil }}">
            </div>
            <!-- Perfil END-->
        </div>
        <div class="form-row">
            <!-- Objetivo -->
            <div class="form-group col-md-6">
              <label for="objetivo" class="control-label">OBJECTIVO</label>
              <textarea name="objetivo" id="objetivo" class="form-control" cols="15" rows="5" placeholder="OBJETIVO">
                  {{ $cursos->objetivo }}
              </textarea>
            </div>
            <!-- Objetivo END -->
            <!-- Accion Movil-->
            <div class="form-group col-md-6">
                <label for="descripcionCurso" class="control-label">DESCRIPCIÓN</label>
                <textarea name="descripcionCurso" id="descripcionCurso" class="form-control" cols="15" rows="5" placeholder="DESCRIPCIÓN">
                    {{ $cursos->objetivo }}
                </textarea>
            </div>
            <!-- Accion Movil END-->
        </div>
        <hr>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="solicitud_autorizacion" class="control-label">SOLICITUD DE AUTORIZACIÓN</label>
                <div class="col-sm-10">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="solicitud_autorizacion" name="solicitud_autorizacion" value="{{ $cursos->solicitud_autorizacion }}">
                    <label class="form-check-label" for="solicitud_autorizacion">
                        AUTORIZACIÓN
                    </label>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="memo_actualizacion" class="control-label">MEMO DE ACTUALIZACIÓN</label>
                <input type="text" class="form-control" id="memo_actualizacion" name="memo_actualizacion" value="{{ $cursos->memo_actualizacion}}">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-4">
                <label for="memo_validacion" class="control-label">MEMO DE VALIDACIÓN</label>
                <input type="text" class="form-control" id="memo_validacion" name="memo_validacion" value="{{ $cursos->memo_validacion }}">
            </div>
        </div>
        <div class="form-row">
            <!-- Solicitud -->
            <div class="form-group col-md-4">
              <label for="documento_solicitud_autorizacion" class="control-label">Documento Solicitud de Autorización</label>
              <input type="file" class="form-control" id="documento_solicitud_autorizacion" name="documento_solicitud_autorizacion">
            </div>
            <!-- Solicitud END -->
            <!-- memo_actualizacion -->
            <div class="form-group col-md-4">
              <label for="documento_memo_actualizacion" class="control-label">Documento Memo Actualización</label>
              <input type="file" class="form-control" id="documento_memo_actualizacion" name="documento_memo_actualizacion">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-4">
                <label for="documento_memo_validacion" class="control-label">Documento Memo Validación</label>
                <input type="file" class="form-control" id="documento_memo_validacion" name="documento_memo_validacion">
            </div>
        </div>
        <div class="form-row">
            <!-- fecha_validacion -->
            <div class="form-group col-md-6">
              <label for="fecha_validacion" class="control-label">Fecha Validación</label>
              <input type="date" class="form-control" id="fecha_validacion" name="fecha_validacion" value="{{ $cursos->fecha_validacion }}">
            </div>
            <!-- memo_actualizacion END -->
            <div class="form-group col-md-6">
                <label for="fecha_actualizacion" class="control-label">Fecha Actualización</label>
                <input type="date" class="form-control" id="fecha_actualizacion" name="fecha_actualizacion" value="{{ $cursos->fecha_actualizacion }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="cambios_especialidad" class="control-label">CAMBIOS DE ESPECIALIDAD</label>
                <input type="text" name="cambios_especialidad" id="cambios_especialidad" class="form-control" value="{{ $cursos->cambios_especialidad }}">
            </div>
            <div class="form-group col-md-4">
                <label for="nivel_estudio" class="control-label">NIVEL DE ESTUDIO</label>
                <input type="text" name="nivel_estudio" id="nivel_estudio" class="form-control" value="{{ $cursos->nivel_estudio }}">
            </div>
            <div class="form-group col-md-4">
                <label for="categoria" class="control-label">CATEGORIA</label>
                <input type="text" name="categoria" id="categoria" class="form-control" value="{{ $cursos->categoria }}">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
                </div>
            </div>
        </div>
    </form>
    <br>
</div>
@endsection
