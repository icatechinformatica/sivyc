<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'NUEVO PERFIL DE USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">

            <div class="col-xl-12 order-xl-1">
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
                        <h3 class="mb-0">NUEVO USUARIO</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('usuarios.perfil.store') }}" id="userCreateForm" name="userCreateForm">
                    @csrf
                      <h6 class="heading-small text-muted mb-4">Información del usuario</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="emailInput">Correo Electrónico</label>
                              <input type="email" id="emailInput" name="emailInput" class="form-control">
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="nameInput">Nombre completo</label>
                              <input type="text" id="nameInput" name="nameInput" class="form-control" >
                            </div>
                          </div>

                        </div>
                        {{-- Campos telefono y curp --}}
                        <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group">
                                <label class="form-control-label" for="curpInput">CURP</label>
                                <input type="text" id="curpInput" name="curpInput" class="form-control">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group">
                                <label class="form-control-label" for="telInput">Teléfono</label>
                                <input type="text" id="telInput" name="telInput" class="form-control" >
                              </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="passwordInput">Contraseña</label>
                              <input type="password" id="passwordInput" name="passwordInput" class="form-control">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="confirmtPasswordInput">Confirmar Contraseña</label>
                              <input type="password" id="confirmtPasswordInput" name="confirmtPasswordInput" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr class="my-4" />
                      <!-- Address -->
                      <h6 class="heading-small text-muted mb-4">Información del contacto</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-md-8">
                            <div class="form-group">
                              <label class="form-control-label" for="input-address">PUESTO</label>
                              <input id="puestoInput" name="puestoInput" class="form-control" type="text">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="ubicacionInput">Ubicacion</label>
                                    <select name="ubicacionInput" id="ubicacionInput" class="form-control">
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($ubicacion as $itemUbicacion)
                                            <option value="{{$itemUbicacion->ubicacion}}">{{$itemUbicacion->ubicacion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--modificacion de las unidades-->
                            <div class="col-md-6">
                                <label for="form-control-label">Unidades de capacitación</label>
                                <select name="capacitacionInput" id="capacitacionInput" class="form-control">
                                    <option value="">--SELECCIONAR--</option>
                                </select>
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

            $('#userCreateForm').validate({
                rules: {
                    emailInput: {
                        required: true,
                        email: true
                    },
                    nameInput: {
                        required: true,
                        minlength: 3
                    },
                    curpInput: {
                        required: true,
                        minlength: 18
                    },
                    telInput: {
                        required: true,
                        minlength: 10
                    },
                    passwordInput: {
                        required: true,
                    },
                    confirmtPasswordInput: {
                        equalTo : "#passwordInput"
                    },
                    puestoInput: {
                        required: true
                    },
                    ubicacionInput: {
                        required: true
                    }
                },
                messages: {
                    emailInput: {
                        required: 'Por favor ingrese su correo electrónico',
                        email: 'agregar un correo electrónico valido'
                    },
                    nameInput: {
                        required: 'Por favor ingrese el nombre completo'
                    },
                    curpInput: {
                        required: 'Por favor ingrese una curp valida'
                    },
                    telInput: {
                        required: 'Por favor ingrese un número de teléfono valido'
                    },
                    passwordInput: {
                        required: 'Por favor Ingresé la contraseña',
                    },
                    puestoInput: {
                        required: 'Por favor ingrese el puesto'
                    },
                    ubicacionInput: {
                        required: 'Por favor, seleccione la ubicación.',
                    },
                    confirmtPasswordInput: {
                        equalTo: 'Las contraseñas no coinciden'
                    }
                }
            });


            /**
            * cambios select dependientes de tbl_unidades
            */
            $('#ubicacionInput').on("change", () => {
                $("#ubicacionInput option:selected").each( () => {
                    var ubicacion = $('#ubicacionInput').val();
                    var url = '/unidades/unidades_ubicacion/'+ ubicacion;

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
                            $("#capacitacionInput").empty();
                            $("#capacitacionInput").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#capacitacionInput").empty();
                                $("#capacitacionInput").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#capacitacionInput').append('<option value="' + v.id + '">' + v.unidad + '</option>');
                                });
                                $("#capacitacionInput").focus();
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

        });
    </script>
@endsection
