@extends('layouts.pages.layout')

@section('content')
<section class="container g-py-40 g-pt-40 g-pb-0">
<form action="{{ url('/instructor-save') }}" method="post" id="registerinstructor" enctype="multipart/form-data">
    @csrf
        <div>
            <label><h2>Datos Personales</h2></label>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="inputnombre">Nombre</label>
            <input name='nombre' id='nombre' type="text" class="form-control">
          </div>
          <div class="form-group col-md-4">
            <label for="inputapellido_paterno">Apellido Paterno</label>
            <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control">
          </div>
          <div class="form-group col-md-4">
            <label for="inputapellido_materno">Apellido Materno</label>
            <input name='apellido_materno' id='apellido_materno' type="text" class="form-control">
          </div>
        </div>
         <div class="form-row">
         <div class="form-group col-md-6">
           <label for="inputCURP">CURP</label>
           <input name='curp' id='curp' type="text" class="form-control">
         </div>
         <div class="form-group col-md-6">
           <label for="inputcorreo">Correo Electronico</label>
           <input name='correo' id="correo" type="text" class="form-control" placeholder="correo_electronico@ejemplo.com">
         </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="inputacta_nacimiento">Acta de Nacimiento</label>
            <input id="acta_nacimiento" name="acta_nacimiento" type="file" accept="application/pdf" class="form-control" aria-required="true">
          </div>
          <div class="form-group col-md-4">
            <label for="inputine">Credencial del INE</label>
            <input id="ine" name="ine" type="file" accept="application/pdf" class="form-control" aria-required="true">
          </div>
          <div class="form-group col-md-4">
            <label for="inputcomprobante_domicilio">Comprobante de Domicilio</label>
            <input id="comprobante_domicilio" name="comprobante_domicilio" type="file" accept="application/pdf" class="form-control" aria-required="true">
          </div>
        </div>
        <hr style="border-color:dimgray">
        <div>
            <label><h2>Datos Academicos</h2></label>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputcurriculum">Curriculum Vitae</label>
              <input id="curriculum" name="curriculum" type="file" accept="application/pdf" class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-6">
              <label for="inputcertificado_estudios">Certificado de Ultimo Grado de Estudios</label>
              <input id="certificado_estudios" name="certificado_estudios" type="file" accept="application/pdf" class="form-control" aria-required="true">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-8">
              <label for="inputconstancia_cursos">Constancias de cursos</label> <!-- PENDIENTE! MULTIPLES ARCHIVOS PDF CON ARRAY -->
              <input id="constancia_cursos" name="constancia_cursos" type="file" accept="application/pdf" class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-4">
              <label for="inputespecialidad">Especialidad</label>
              <input id="especialidad" name="especialidad" type="text" class="form-control" aria-required="true">
            </div>
          </div>
          <hr style="border-color:dimgray">
          <div>
              <label><h2>Datos Institucionales</h2></label>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="inputconstancia_agente">Constancia de Agente Capacitador Externo </label>
              <input id="constancia_agente" name="constancia_agente" type="file" accept="application/pdf" class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-4">
              <label for="inputseleccion_firmada">Constancia de Seleccion Firmada</label>
              <input id="seleccion_firmada" name="seleccion_firmada" type="file" accept="application/pdf" class="form-control" aria-required="true">
            </div>
            <div class="form-group col-md-4">
                <label for="inputformato_entrevista">Formato de Entrevista para Candidatos</label>
                <input id="formato_entrevista" name="formato_entrevista" type="file" accept="application/pdf" class="form-control" aria-required="true">
            </div>
          </div>
          <div>
              <label for="inputobservacion">Observaciones</label>
              <textarea id="observacion" name="observacion" cols="30" rows="7" class="form-control" aria-required="false">
              </textarea>
          </div>
          <br>
          <div style="text-align: right;width:100%">
              <button type="submit" class="btn btn-primary" >Agregar</button>
          </div>
    </form>
</section>
<br>
@stop
