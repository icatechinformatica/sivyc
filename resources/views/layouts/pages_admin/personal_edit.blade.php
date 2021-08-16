<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'EDITAR PERSONAL | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">

            <div class="col-xl-4 order-xl-2">
                <div class="card card-profile">
                  <div class="row justify-content-center">
                    <div class="col-lg-3 order-lg-2">
                      <div class="card-profile-image">
                        <a href="#">
                          <img src="{{asset("img/blade_icons/nophoto.png")}}" class="rounded-circle">
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                    <div class="d-flex justify-content-between">
                    </div>
                  </div>
                  <div class="card-body pt-0">
                    <div class="row">
                      <div class="col">
                        <div class="card-profile-stats d-flex justify-content-center">
                            <h5 class="h3">
                                N° Enlace:
                            </h5>
                            {{ $directorioPersonal->numero_enlace }}
                        </div>
                      </div>
                    </div>
                    <div class="text-center">
                      <h5 class="h3">
                          {{ $directorioPersonal->puesto }}
                      </h5>
                      <div class="h5 font-weight-300">
                        <i class="ni location_pin mr-2"></i>
                      </div>
                      <div class="h5 mt-4">
                        <i class="ni business_briefcase-24 mr-2"></i>CARGO: {{ $directorioPersonal->cargo }}
                      </div>
                      <div>
                        <i class="ni education_hat mr-2"></i>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="col-xl-8 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">EDITAR PERFIL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('personal.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('personal.update', ['id' => base64_encode($directorioPersonal->id) ]) }}" name="formProfileEdit" id="formProfileEdit">
                    @csrf
                    @method('PUT')
                      <h6 class="heading-small text-muted mb-4">Información del usuario</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputNumeroEnlaceUpdate">N° DE ENLACE</label>
                              <input type="text" id="inputNumeroEnlaceUpdate" name="inputNumeroEnlaceUpdate" class="form-control" value="{{ $directorioPersonal->numero_enlace }}">
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputNameUpdate">NOMBRE</label>
                              <input type="text" id="inputNameUpdate" name="inputNameUpdate" class="form-control" value="{{ $directorioPersonal->nombre }}">
                            </div>
                          </div>

                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputPaternoUpdate">APELLIDO PATERNO</label>
                              <input type="text" id="inputPaternoUpdate" name="inputPaternoUpdate" class="form-control" value="{{ $directorioPersonal->apellidoPaterno }}">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputMaternoUpdate">APELLIDO MATERNO</label>
                              <input type="text" id="inputMaternoUpdate" name="inputMaternoUpdate" class="form-control" value="{{ $directorioPersonal->apellidoMaterno }}">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputCurp">CURP</label>
                              <input type="text" id="inputCurp" name="inputCurp" class="form-control" value="{{ $directorioPersonal->curp}}">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputEmail">EMAIL</label>
                              <input type="text" id="inputEmail" name="inputEmail" class="form-control" value="{{ $directorioPersonal->email}}">
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">INFORMACIÓN DEL PERSONAL</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputCategoria">CATEGORIA</label>
                                    <input id="inputCategoria" name="inputCategoria"  value="{{ $directorioPersonal->categoria }}" class="form-control" type="text">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label class="form-control-label" for="inputPuestoUpdate">PUESTO</label>
                                <input id="inputPuestoUpdate" name="inputPuestoUpdate" class="form-control" value="{{ $directorioPersonal->puesto }}" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputOrganoUpdate">ORGANO ADMINISTRATIVO</label>
                                    <select name="inputOrganoUpdate" id="inputOrganoUpdate" class="form-control">
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($oAdministrativo as $itemOrganoAdministrativo)
                                            <option value="{{ $itemOrganoAdministrativo->id }}"
                                                {{( $itemOrganoAdministrativo->id == $directorioPersonal->idOrgano ) ? 'selected': ''}}>
                                                {{ $itemOrganoAdministrativo->organo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--modificacion de las unidades-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputAdscripcionUpdate">ÁREA ADSCRIPCIÓN</label>
                                    <select name="inputAdscripcionUpdate" id="inputAdscripcionUpdate" class="form-control">
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($adscripcion as $itemAdscripcion)
                                            <option value="{{ $itemAdscripcion->id }}"
                                                {{( $itemAdscripcion->id == $directorioPersonal->idadscripcion ) ? 'selected': ''}}>
                                                {{ $itemAdscripcion->area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="custom-control custom-control-alternative custom-checkbox">
                                    <input class="custom-control-input" id="activo" name="activos"
                                            type="checkbox" value="false" {{( $directorioPersonal->activo == false ) ? 'checked': ''}}>
                                        <label class="custom-control-label" for="activo">
                                        <span class="text-muted">DESACTIVAR</span>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="Modificar" class="btn btn-sm btn-warning">
                      </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>

        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc_admin.footer")
        <!-- FOOTER PORTAL DE GOBIERNO END-->
    </div>
@endsection
@section('scripts_content')
    <script type="text/javascript">
        $(function(){

            $('#formProfileEdit').validate({
                rules: {
                    inputNameUpdate: {
                        required: true,
                        minlength: 2
                    },
                    inputPasswordUpdate: {
                        required: true,
                    },
                    inputPuestoUpdate: {
                        required: true
                    },
                    inputUbicacionUpdate: {
                        required: true
                    },
                    inputNumeroEnlaceUpdate: {
                        required: true
                    }
                },
                messages: {
                    inputNameUpdate: {
                        required: 'Por favor ingrese el nombre completo',
                        minlength: 'Longitud minima de 2 caracteres'
                    },
                    inputPuestoUpdate: {
                        required: 'Por favor ingrese el puesto'
                    },
                    inputUbicacionUpdate: {
                        required: 'Por favor, seleccione la ubicación.',
                    },
                    inputPasswordRepeatInput: {
                        equalTo: 'Las contraseñas no coinciden'
                    }
                }
            });


            /**
            * cambios select dependientes de tbl_unidades
            */
            $('#inputOrganoUpdate').on("change", () => {
                $("#inputOrganoUpdate option:selected").each( () => {
                    var organoAdmin = $('#inputOrganoUpdate').val();
                    var url = '/organo/organo_administrativo/'+ organoAdmin;

                    var request = $.ajax
                    ({
                        url: url,
                        method: 'GET',
                        dataType: 'json'
                    });

                    /*
                        *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                        *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                        *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                        *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
                    */

                    request.done(( respuesta ) => {
                        if (respuesta.length < 1) {
                            $("#inputAdscripcionUpdate").empty();
                            $("#inputAdscripcionUpdate").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#inputAdscripcionUpdate").empty();
                                $("#inputAdscripcionUpdate").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#inputAdscripcionUpdate').append('<option value="' + v.id + '">' + v.area + '</option>');
                                });
                                $("#inputAdscripcionUpdate").focus();
                            }else{

                                //Puedes mostrar un mensaje de error en algún div del DOM
                            }
                        }
                    });

                    request.fail(( jqXHR, textStatus ) =>
                    {
                            alert( "Hubo un error: " + textStatus );
                    });
                });
            });

            /**
            * ESCRIBIR SÓLO NÚMEROS
            */
            $("#inputNumeroEnlaceUpdate").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                   //retornar falso
                   return false;
               }
            });

        });
    </script>
@endsection
