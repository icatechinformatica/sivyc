@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Alumnos Matriculados | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        <div class="form-row">
            <!--nombre aspirante-->
            <div class="form-group col-md-6">
                <label for="nombre " class="control-label">Nombre: {{$alumnos[0]->nombrealumno}} {{$alumnos[0]->apellidoPaterno}} {{$alumnos[0]->apellidoMaterno}}</label>
            </div>
            <!--nombre aspirante END-->
            <div class="form-group col-md-6">
                <label for="sexo" class="control-label">Genero: {{$alumnos[0]->sexo}} </label>
            </div>
        </div>
        <div class="form-row">

            <div class="form-group col-md-3">
                <label for="curp" class="control-label">CURP: {{$alumnos[0]->curp_alumno}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento: {{$alumnos[0]->fecha_nacimiento}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="telefono" class="control-label">Teléfono: {{$alumnos[0]->telefono}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="cp" class="control-label">C.P. {{$alumnos[0]->cp}} </label>
            </div>
        </div>
        <div class="form-row">

            <div class="form-group col-md-3">
                <label for="estado" class="control-label">Estado: {{$alumnos[0]->estado}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="municipio" class="control-label">Municipio: {{$alumnos[0]->municipio}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="estado_civil" class="control-label">Estado Civil: {{$alumnos[0]->estado_civil}} </label>
            </div>
            <!---->
            <div class="form-group col-md-3">
                <label for="discapacidad" class="control-label">Discapacidad que presenta: {{$alumnos[0]->discapacidad}}</label>
            </div>
        </div>
        <div class="form-row">
            <!-- domicilio -->
            <div class="form-group col-md-6">
                <label for="domicilio" class="control-label">Domicilio: {{$alumnos[0]->domicilio}}</label>
            </div>
            <!-- domicilio END -->
            <div class="form-group col-md-6">
                <label for="colonia" class="control-label">Colonia: {{$alumnos[0]->colonia}} </label>
            </div>
        </div>
        <!---->
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS GENERALES</b></h4>
        </div>
        <!---->
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="especialidad" class="control-label">ESPECIALIDAD: {{$alumnos[0]->especialidad}}</label>
            </div>
            <div class="form-group col-md-6">
                <label for="cursos" class="control-label">CURSO: {{$alumnos[0]->nombre_curso}} </label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="horario" class="control-label">HORARIO: {{$alumnos[0]->horario}} </label>
            </div>
            <div class="form-group col-md-4">
                <label for="grupo" class="control-label">GRUPO: {{$alumnos[0]->grupo}} </label>
            </div>
            <div class="form-group col-md-4">
                <label for="grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS: {{$alumnos[0]->ultimo_grado_estudios}} </label>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DOCUMENTACIÓN ENTREGADA</b></h4>
        </div>
        <div class="form-row">
            <div class="form-group col-md-8">
                <h5><b>ANEXAR EXTRANJEROS:</b></h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="comprobante_migratorio" name="comprobante_migratorio">
                    <label class="form-check-label" for="comprobante_migratorio">
                        COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL
                    </label>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="empresa" class="control-label">EMPRESA DONDE TRABAJA:</label>
                {{$alumnos[0]->empresa_trabaja}}
            </div>
            <div class="form-group col-md-6">
                <label for="puesto_empresa" class="control-label">PUESTO:</label>
                {{$alumnos[0]->puesto_empresa}}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="antiguedad" class="control-label">ANTIGUEDAD:</label>
                {{$alumnos[0]->antiguedad}}
            </div>
            <div class="form-group col-md-8">
                <label for="direccion_empresa" class="control-label">DIRECCIÓN:</label>
                {{$alumnos[0]->direccion_empresa}}
            </div>
        </div>
        <!---->
        <div class="form-row">
            <div class="row row-cols-1 row-cols-md-3">
                <div class="col mb-3 col-md-3">
                  @if ($alumnos[0]->chk_acta_nacimiento == true)
                    <div class="card">
                        <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                        <div class="card-body">
                        <h5 class="card-title">Acta de Nacimiento</h5>
                        <p class="card-text"></p>
                        </div>
                    </div>
                  @else
                    <div class="card">
                        <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                        <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text"></p>
                        </div>
                    </div>
                  @endif

                </div>
                <div class="col mb-3 col-md-3">
                  @if ($alumnos[0]->chk_curp == true)
                    <div class="card">
                        <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                        <div class="card-body">
                        <h5 class="card-title">CURP</h5>
                        <p class="card-text"></p>
                        </div>
                    </div>
                  @else
                    <div class="card">
                        <img src="http://fakeimg.pl/200x200/" class="img-thumbnail" alt="...">
                        <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text"></p>
                        </div>
                    </div>
                  @endif

                </div>
                <div class="col mb-3 col-md-3">
                    @if ($alumnos[0]->chk_comprobante_domicilio == true)
                        <div class="card">
                            <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                            <div class="card-body">
                            <h5 class="card-title">C. DOMICILIO</h5>
                            <p class="card-text"></p>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <img src="http://fakeimg.pl/200x200/" class="img-thumbnail" alt="...">
                            <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text"></p>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="col mb-3 col-md-3">
                  @if ($alumnos[0]->chk_fotografia == true)
                    <div class="card">
                        <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                        <div class="card-body">
                        <h5 class="card-title">FOTOGRAFÍA</h5>
                        <p class="card-text"></p>
                        </div>
                    </div>
                  @else
                    <div class="card">
                        <img src="http://fakeimg.pl/200x200/" class="img-thumbnail" alt="...">
                        <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text"></p>
                        </div>
                    </div>
                  @endif
                </div>
                <div class="col mb-3 col-md-3">
                    @if ($alumnos[0]->chk_ine == true)
                      <div class="card">
                          <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                          <div class="card-body">
                          <h5 class="card-title">INE</h5>
                          <p class="card-text"></p>
                          </div>
                      </div>
                    @else
                      <div class="card">
                          <img src="http://fakeimg.pl/200x200/" class="img-thumbnail" alt="...">
                          <div class="card-body">
                          <h5 class="card-title">INE</h5>
                          <p class="card-text"></p>
                          </div>
                      </div>
                    @endif
                </div>
                <div class="col mb-3 col-md-3">
                    @if ($alumnos[0]->chk_pasaporte_licencia == true)
                      <div class="card">
                          <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                          <div class="card-body">
                          <h5 class="card-title">LICENCIA O PASAPORTE</h5>
                          <p class="card-text"></p>
                          </div>
                      </div>
                    @else
                      <div class="card">
                          <img src="http://fakeimg.pl/200x200/" class="img-thumbnail" alt="...">
                          <div class="card-body">
                          <h5 class="card-title">LICENCIA O PASAPORTE</h5>
                          <p class="card-text"></p>
                          </div>
                      </div>
                    @endif
                </div>
                <div class="col mb-3 col-md-3">
                    @if ($alumnos[0]->chk_comprobante_ultimo_grado == true)
                      <div class="card">
                          <img src="{{asset("img/pdf.png") }}" class="img-thumbnail" alt="...">
                          <div class="card-body">
                          <h5 class="card-title">COMRPOBRANTE ÚLTIMO GRADO</h5>
                          <p class="card-text"></p>
                          </div>
                      </div>
                    @else
                      <div class="card">
                          <img src="http://fakeimg.pl/200x200/" class="img-thumbnail" alt="...">
                          <div class="card-body">
                          <h5 class="card-title">COMRPOBRANTE ÚLTIMO GRADO</h5>
                          <p class="card-text"></p>
                          </div>
                      </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
