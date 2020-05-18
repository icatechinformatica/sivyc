@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Cursos | Sivyc Icatech')
<!--seccion-->
@section('content')
<div class="container g-pt-50">
    <form method="POST" action="{{ route('cursos.gurdar') }}" method="post" id="registercv" enctype="multipart/form-data">
        @csrf
        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso"><h1>Formulario de Cursos</h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
          <!-- Unidad -->
          <div class="form-group col-md-6">
            <label for="especialidad" class="control-label">Especialidad</label>
            <select class="form-control" id="especialidad" name="especialidad">
                <option value="">--SELECCIONAR--</option>
            @foreach ($especialidades as $item)
                <option value="{{$item->id}}">{{$item->nombre}}</option>
            @endforeach
            </select>
          </div>
          <!--Unidad Fin-->
          <!-- nombre curso -->
          <div class="form-group col-md-6">
            <label for="nombrecurso" class="control-label">Nombre del Curso</label>
            <input type="text" class="form-control" id="nombrecurso" name="nombrecurso">
          </div>
          <!-- nombre curso FIN-->
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="modalidad  " class="control-label">Modalidad</label>
                <select class="form-control" id="modalidad">
                  <option value="">--SELECCIONAR--</option>
                  <option value="CAE">CAE</option>
                  <option value="EXT">EXT</option>
                </select>
            </div>
            <!--clasificacion-->
            <div class="form-group col-md-6">
                <label for="clasificacion  " class="control-label">Clasificación</label>
                <select class="form-control" id="clasificacion">
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
            <input type="number" class="form-control" id="horas" name="horas" placeholder="Horas">
          </div>
          <!-- Destinatario END -->
          <!-- Puesto-->
          <div class="form-group col-md-6">
            <label for="costo" class="control-label">Costo</label>
            <input type="text" class="form-control" id="costo" name="costo" placeholder="costo">
          </div>
          <!-- Puesto END-->

        </div>
        <div class="form-row">
            <!-- Duracion -->
            <div class="form-group col-md-6">
              <label for="duracion" class="control-label">Duración</label>
              <input type="text" class="form-control" id="duracion" name="duracion" placeholder="duracion">
            </div>
            <!-- Duracion END -->
            <!-- Perfil-->
            <div class="form-group col-md-6">
              <label for="perfil" class="control-label">Perfil</label>
              <input type="text" class="form-control" id="perfil" name="perfil" placeholder="perfil">
            </div>
            <!-- Perfil END-->
        </div>
        <div class="form-row">
            <!-- Objetivo -->
            <div class="form-group col-md-6">
              <label for="objetivo" class="control-label">Objetivo</label>
              <input type="text" class="form-control" id="objetivo" name="objetivo" placeholder="objetivo">
            </div>
            <!-- Objetivo END -->
            <!-- Accion Movil-->
            <div class="form-group col-md-6">
              <label for="unidad_accion_movil" class="control-label">Unidad Acción Móvil</label>
              <input type="text" class="form-control" id="unidad_accion_movil" name="unidad_accion_movil">
            </div>
            <!-- Accion Movil END-->
        </div>
        <hr>
        <div class="form-row">
            <!-- Solicitud -->
            <div class="form-group col-md-12">
              <label for="solicitud_autorizacion" class="control-label">Solicitud de Autorización</label>
              <input type="file" class="form-control" id="solicitud_autorizacion" name="solicitud_autorizacion">
            </div>
            <!-- Solicitud END -->
        </div>
        <div class="form-row">
            <!-- fecha_validacion -->
            <div class="form-group col-md-6">
              <label for="fecha_validacion" class="control-label">Fecha Validación</label>
              <input type="date" class="form-control" id="fecha_validacion" name="fecha_validacion">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-6">
                <label for="memo_validacion" class="control-label">Memo Validación</label>
                <input type="file" class="form-control" id="memo_validacion" name="memo_validacion">
            </div>
        </div>
        <div class="form-row">
            <!-- memo_actualizacion -->
            <div class="form-group col-md-6">
              <label for="memo_actualizacion" class="control-label">Memo Actualización</label>
              <input type="file" class="form-control" id="memo_actualizacion" name="memo_actualizacion">
            </div>
            <!-- memo_actualizacion END -->
            <div class="form-group col-md-6">
                <label for="fecha_actualizacion" class="control-label">Fecha Actualización</label>
                <input type="date" class="form-control" id="fecha_actualizacion" name="fecha_actualizacion">
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
