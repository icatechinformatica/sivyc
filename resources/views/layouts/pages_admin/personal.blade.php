<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'CREAR PERSONAL | Sivyc Icatech')
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

                        </div>
                      </div>
                    </div>
                    <div class="text-center">
                      <h5 class="h3">

                      </h5>
                      <div class="h5 font-weight-300">
                        <i class="ni location_pin mr-2"></i>
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
                        <h3 class="mb-0">NUEVO PERSONAL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('personal.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('personal.store') }}" name="formPersonalStore" id="formPersonalStore">
                    @csrf
                      <h6 class="heading-small text-muted mb-4">Información personal</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputnumeroControl">N° DE ENLACE</label>
                              <input type="text" id="inputnumeroControl" name="inputnumeroControl" class="form-control" >
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputNombre">NOMBRE</label>
                              <input type="text" id="inputNombre" name="inputNombre" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputApellidoPaterno">APELLIDO PATERNO</label>
                              <input type="text" id="inputApellidoPaterno" name="inputApellidoPaterno" class="form-control">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputApellidoMaterno">APELLIDO MATERNO</label>
                              <input type="text" id="inputApellidoMaterno" name="inputApellidoMaterno" class="form-control">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputCurp">CURP</label>
                              <input type="text" id="inputCurp" name="inputCurp" class="form-control">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputEmail">EMAIL</label>
                              <input type="text" id="inputEmail" name="inputEmail" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">Información del personal</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputPuesto">PUESTO</label>
                              <input id="inputPuesto" name="inputPuesto" class="form-control" type="text">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputCategoria">CATEGORIA</label>
                              <input id="inputCategoria" name="inputCategoria" class="form-control" type="text">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <!--modificacion de las unidades-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputOrganoAdministrativo">ORGANO ADMINISTRATIVO</label>
                                    <select name="inputOrganoAdministrativo" id="inputOrganoAdministrativo" class="form-control">
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($oA as $itemOrganoAdministrativo)
                                            <option value="{{ $itemOrganoAdministrativo->id }}">{{ $itemOrganoAdministrativo->organo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputAreaAdscripcion">ÁREA DE ADSCRIPCIÓN</label>
                                    <select name="inputAreaAdscripcion" id="inputAreaAdscripcion" class="form-control">
                                        <option value="">--SELECCIONAR--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="CREAR" class="btn btn-sm btn-success">
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

            $('#formPersonalStore').validate({
                rules: {
                    inputnumeroControl: {
                        required: true,
                        minlength: 2
                    },
                    inputNombre: {
                        required: true,
                    },
                    inputPuesto: {
                        required: true
                    },
                    inputCategoria: {
                        required: true
                    },
                    inputOrganoAdministrativo: {
                        required: true
                    },
                    inputAreaAdscripcion: {
                        required: true
                    }
                },
                messages: {
                    inputnumeroControl: {
                        required: 'Por favor ingrese el nombre completo',
                        minlength: 'Se requiere minimo dos caracteres'
                    },
                    inputNombre: {
                        required: 'Por favor Ingresé el nombre',
                    },
                    inputPuesto: {
                        required: 'Por favor ingrese el puesto'
                    },
                    inputCategoria: {
                        required: 'Por favor, ingresa la categoría.',
                    },
                    inputOrganoAdministrativo: {
                        required: 'por favor, seleccione organo administrativo'
                    },
                    inputAreaAdscripcion: {
                        required: 'Por favor, seleccione el área de adscripción'
                    }
                }
            });


            /**
            * cambios select dependientes de tbl_unidades
            */
            $('#inputOrganoAdministrativo').on("change", () => {
                $("#inputOrganoAdministrativo option:selected").each( () => {
                    var oAdministrativo = $('#inputOrganoAdministrativo').val();
                    var url = '/organo/organo_administrativo/'+ oAdministrativo;

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
                            $("#inputAreaAdscripcion").empty();
                            $("#inputAreaAdscripcion").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#inputAreaAdscripcion").empty();
                                $("#inputAreaAdscripcion").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#inputAreaAdscripcion').append('<option value="' + v.id + '">' + v.area + '</option>');
                                });
                                $("#inputAreaAdscripcion").focus();
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
            $("#inputnumeroControl").keypress(function (e) {
                //if the letter is not digit then display error and don't type anything
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                   //retornar falso
                   return false;
               }
            });

        });
    </script>
@endsection
