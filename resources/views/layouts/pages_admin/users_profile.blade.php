<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERFIL DE USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <style>
        .custom-control-label {
            margin-left: 9%; /* Ajusta según sea necesario */
        }

        /* Cambia el color del switch cuando esté desactivado */
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #28a745; /* Rojo (desactivado) */
        }

        .custom-control-input:not(:checked) ~ .custom-control-label::before {
            background-color: #dc3545; /* Verde (activado) */
        }

        /* Cambiar el color de la palanca en sí */
        .custom-control-input:checked ~ .custom-control-label::after {
            background-color: #f8d7da; /* Color de la palanca cuando esté desactivado */
        }

        .custom-control-input:not(:checked) ~ .custom-control-label::after {
            background-color: #d4edda; /* Color de la palanca cuando esté activado */
        }

        /* Aumentar el tamaño del switch */
        .custom-switch .custom-control-label::before {
            width: 3rem; /* Ancho del switch */
            height: 1.5rem; /* Altura del switch */
        }

        .custom-switch .custom-control-label::after {
            width: 1rem; /* Ancho de la palanca */
            height: 1rem; /* Altura de la palanca */
            top: -15; /* Ajuste vertical */
        }

        /* Ajustar la posición de la palanca cuando el switch está activado */
        .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
            transform: translateX(1.5rem); /* Desplazamiento horizontal de la palanca al activarse */
        }
    </style>
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
                        {{ $usuario->name }}
                      </h5>
                      <div class="h5 font-weight-300">
                        <i class="ni location_pin mr-2"></i>{{ $usuario->email }}
                      </div>
                      <div class="h5 mt-4">
                        <i class="ni business_briefcase-24 mr-2"></i>CARGO:
                      </div>
                      <div>
                        <i class="ni education_hat mr-2"></i>{{ $usuario->puesto }}
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="col-xl-8 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h3 class="mb-0">EDITAR PERFIL</h3>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{ route('usuario_permisos.index') }}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <form method="POST" action="{{ route('usuarios_permisos.update', ['id' => base64_encode($usuario->id) ]) }}" name="formProfileEdit" id="formProfileEdit">
                    @csrf
                    @method('PUT')
                      <h6 class="heading-small text-muted mb-4">Información del usuario</h6>
                      <div class="pl-lg-4">
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputEmailUpdate">Correo Electrónico</label>
                              <input type="email" id="inputEmailUpdate" name="inputEmailUpdate" class="form-control" readonly value="{{ $usuario->email }}">
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputNameUpdate">Nombre</label>
                              <input type="text" id="inputNameUpdate" name="inputNameUpdate" class="form-control" value="{{ $usuario->name }}">
                            </div>
                          </div>

                        </div>
                        {{-- teléfono y curp --}}
                        <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group">
                                <label class="form-control-label" for="inputCurpUpdate">CURP</label>
                                <input type="text" id="inputCurpUpdate" name="inputCurpUpdate" class="form-control" value="{{ $usuario->curp }}">
                              </div>
                            </div>

                            <div class="col-lg-6">
                              <div class="form-group">
                                <label class="form-control-label" for="inputTelUpdate">Teléfono</label>
                                <input type="text" id="inputTelUpdate" name="inputTelUpdate" class="form-control" value="{{ $usuario->telefono }}">
                              </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputPasswordUpdate">Contraseña</label>
                              <input type="password" id="inputPasswordUpdate" name="inputPasswordUpdate" class="form-control">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label class="form-control-label" for="inputPasswordRepeatInput">Repetir Contraseña</label>
                              <input type="password" id="inputPasswordRepeatInput" name="inputPasswordRepeatInput" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                       <!-- Switch de estatus -->
                       <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="statusSwitch">Estatus</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" @if($usuario->activo) checked @endif class="custom-control-input" id="statusSwitch">
                                        <label class="custom-control-label" for="statusSwitch" id="labelSwitch">@if($usuario->activo) Activo @endif</label>
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
                              <label class="form-control-label" for="inputPuestoUpdate">PUESTO</label>
                              <input id="inputPuestoUpdate" name="inputPuestoUpdate" class="form-control" value="{{ $usuario->puesto }}" type="text">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputUnidadUpdate">UNIDAD</label>
                                    <select name="inputUbicacionUpdate" id="inputUbicacionUpdate" class="form-control">
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($ubicaciones as $itemUbicacion)
                                            <option value="{{$itemUbicacion->ubicacion}}" @if($itemUbicacion->ubicacion == $ubicacion->ubicacion) selected @endif>{{$itemUbicacion->ubicacion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--modificacion de las unidades-->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputCapacitacionUpdate">Unidades de capacitación</label>
                                    <select name="inputCapacitacionUpdate" id="inputCapacitacionUpdate" class="form-control">
                                        {{-- <option value="">--SELECCIONAR--</option> --}}
                                        @foreach ($unidades as $unidad)
                                            <option value="{{$unidad->id}}" @if($unidad->unidad == $ubicacion->unidad) selected @endif>{{$unidad->unidad}}</option>
                                        @endforeach
                                    </select>
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
                        minlength: 3
                    },
                    inputCurpUpdate: {
                        required: true,
                        minlength: 18
                    },
                    inputTelUpdate: {
                        required: true,
                        minlength: 10
                    },
                    // inputPasswordUpdate: {
                    //     required: true,
                    // },
                    // inputPasswordRepeatInput: {
                    //     equalTo : "#inputPasswordUpdate"
                    // },
                    inputPuestoUpdate: {
                        required: true
                    },
                    inputUbicacionUpdate: {
                        required: true
                    }
                },
                messages: {
                    inputNameUpdate: {
                        required: 'Por favor ingrese el nombre completo'
                    },
                    inputCurpUpdate: {
                        required: 'Por favor ingrese una CURP valida'
                    },
                    inputTelUpdate: {
                        required: 'Por favor ingrese un número de télefono valido'
                    },
                    // inputPasswordUpdate: {
                    //     required: 'Por favor Ingresé la contraseña',
                    // },
                    inputPuestoUpdate: {
                        required: 'Por favor ingrese el puesto'
                    },
                    inputUbicacionUpdate: {
                        required: 'Por favor, seleccione la ubicación.',
                    },
                    // inputPasswordRepeatInput: {
                    //     equalTo: 'Las contraseñas no coinciden'
                    // }
                }
            });


            /**
            * cambios select dependientes de tbl_unidades
            */
            $('#inputUbicacionUpdate').on("change", () => {
                $("#inputUbicacionUpdate option:selected").each( () => {
                    var ubicacion = $('#inputUbicacionUpdate').val();
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
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#inputCapacitacionUpdate").empty();
                                $("#inputCapacitacionUpdate").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#inputCapacitacionUpdate').append('<option value="' + v.id + '">' + v.unidad + '</option>');
                                });
                                $("#inputCapacitacionUpdate").focus();
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

            document.getElementById('statusSwitch').addEventListener('change', function() {
                let label = document.getElementById('labelSwitch');
                let status = this.checked ? 'Activo' : 'Baja';
                let isActive = this.checked;
                label.textContent = status;

                fetch('{{ route("update.activo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_id: {{ $usuario->id }},
                        is_active: isActive
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Estado actualizado en la base de datos.');
                    } else {
                        console.log('Error al actualizar el estado.');
                    }
                })
                .catch(error => console.log('Error:', error));
            });
        });
    </script>
@endsection
