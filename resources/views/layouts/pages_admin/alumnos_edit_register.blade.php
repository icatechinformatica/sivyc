<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERFIL DE USUARIO | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">

            <div class="col-xl-12 order-xl-1">
                <div class="card">
                  <div class="card-header">
                    <div class="row align-items-center">
                      <div class="col-8">
                        <h2>ALUMNOS INSCRITOS A MODIFICAR NÚMERO DE CONTROL</h2>
                      </div>
                      <div class="col-4 text-right">
                        <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">NO CONTROL</th>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">CURSOS</th>
                                    <th scope="col">ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($alumnos_pre as $item_alumnos_pre)
                                    <tr>
                                        <td>{{$item_alumnos_pre->no_control}}</td>
                                        <td>{{$item_alumnos_pre->apellido_paterno}} {{$item_alumnos_pre->apellido_materno}} {{$item_alumnos_pre->nombrealumno}}</td>
                                        <td>{{$item_alumnos_pre->nombre_curso}}</td>
                                        <td>

                                            <a
                                                class="nav-link"
                                                data-toggle="modal" data-placement="top"
                                                title="ELIMINAR REGISTRO"
                                                data-target="#sideModalBRDangerDemo"
                                                data-id="{{ base64_encode($item_alumnos_pre->id) }}"
                                                data-pre-id="{{ $id_preinscripcion }}">
                                                <i class="fa fa-trash text-red"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                    </div>

                    <form action="{{ route('alumno_registrado.modificar.update', ['id' => $id_preinscripcion ])}}" id="frmalumno_registrado_modificar" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label for="numero_control_edit" class="control-label">NÚMERO DE CONTROL PARA MODIFICAR</label>
                                <input type="text" name="numero_control_edit" id="numero_control_edit" class="form-control" placeholder="NÚMERO DE CONTROL PARA MODIFICAR">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="codigo_verificacion_edit" class="control-label">CÓDIGO DE VERIFICACIÓN</label>
                                <input type="text" name="codigo_verificacion_edit" id="codigo_verificacion_edit" class="form-control" placeholder="CÓDIGO DE VERIFICACIÓN">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 margin-tb">
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-success" >Modificar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>

        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc_admin.footer")
        <!-- FOOTER PORTAL DE GOBIERNO END-->

        <!--modal para la derecha-->
        <div class="modal fade right show" id="sideModalBRDangerDemo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-modal="true">
            <div class="modal-dialog modal-side modal-bottom-right modal-notify modal-danger" role="document">
                <!--Content-->
                <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header">
                        <p class="heading">ELIMINAR REGISTRO DEL SISTEMA</p>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="white-text">×</span>
                        </button>
                    </div>

                    <!--Body-->
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-9 white-text">
                            <p>¿ESTÁ SEGUR@ QUE QUIERE ELIMINAR EL REGISTRO SELECCIONADO DEL SISTEMA?</p>
                            </div>
                        </div>
                    </div>

                    <!--Footer-->
                    <div class="modal-footer justify-content-center">
                        <a type="button" class="btn btn-success waves-effect waves-light" id="deleteitemtype">ELIMINAR</a>
                        <a type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">CANCELAR</a>
                    </div>
                </div>
                <!--/.Content-->
            </div>
        </div>
        <!--END modal para la derecha-->

    </div>
@stop
@section('scripts_content')
    <script type="text/javascript">
        $(function(){
            /***
            * modificaciones de funciones de flecha
            */
            var getdataFromAlumnos = (id, idpre) =>
            {
                    $.ajax({
                        type: 'GET',
                        url: '/alumnos_registrados/modificar/delete/'+id+'/'+idpre,
                        data: {status: status, name: name},
                        dataType: 'json',
                        success: (response) => {
                            var contenidoModal = $("#contextoModalBody");
                            var myModalLabel = $("#myModalLabel");
                            myModalLabel.append(
                                response[0].nombre_curso
                            );
                            contenidoModal.append(
                            );
                        },
                        error: () => {
                            console.log("No se ha podido obtener la información")
                        }
                    });
            }

            /**
            *  modificación de modal bootsrap
            */
            $('#sideModalBRDangerDemo').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var idPre = button.data('pre-id');
                console.log(id);
                //getdataFromAlumnos(id);
            });

            $('#sideModalBRDangerDemo').on('hidden.bs.modal', function (e) {
                var contenidoModal = $("#contextoModalBody");
                var myModalLabel = $("#myModalLabel");
                contenidoModal.empty();
                myModalLabel.empty();
            });

            $('#deleteitemtype').on('click', (evt) => {
                    console.log('triggered');
                }
            );

        });
    </script>
@stop

