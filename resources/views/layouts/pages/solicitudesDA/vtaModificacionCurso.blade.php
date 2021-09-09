@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Modificación de un curso | SIVyC Icatech')

@section('content')

    <div class="container-fluid px-5 mt-4">
        {{-- titulo --}}
        <div class="row pb-2">
            <div class="col text-center">
                <h2>SOLICITUD MODIFICACIÓN DE CURSO</h2>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        {{-- {{$data}} --}}

        {{-- formulario --}}
        {{-- <div class="row"> --}}
            {{-- <div class="col"> --}}
                <form id="formDataGenerales" action="{{route('solicitudesDA.inicio')}}" method="GET">
                    @csrf
                    <div class="form-group row d-flex align-items-end">
                        <div class="form-group col">
                            <label for="num_solicitud" class="control-label">NÚMERO DE SOLICITUD</label>
                            <input type="text" class="form-control" id="num_solicitud" name="num_solicitud"
                                placeholder="NÚMERO DE SOLICITUD">
                        </div>
                        <div class="form-group col">
                            <label for="fecha_solicitud" class="control-label">FECHA DE SOLICITUD</label>
                            <input type='text' id="fecha_solicitud" autocomplete="off" readonly="readonly"
                                name="fecha_solicitud" class="form-control datepicker" placeholder="FECHA SOLICITUD">
                        </div>
                        <div class="form-group col">
                            <label for="busqueda_curso" class="control-label">CLAVE DEL CURSO</label>
                            <input type="text" class="form-control" id="busqueda_curso" name="busqueda_curso"
                                placeholder="CLAVE DEL CURSO">
                        </div>

                        <div class="form-group col d-flex justify-content-center">
                            <button type="submit" id="btnBuscarCurso" class="btn btn-primary">BUSCAR</button>
                        </div>
                    </div>
                </form>
            {{-- </div> --}}
            {{-- <div class="col-6 mt-4">
                {!! Form::open(['route' => 'solicitudesDA.inicio', 'method' => 'GET', 'class' => 'form-inline']) !!}
                {!! Form::text('busqueda_curso', null, ['class' => 'form-control col mr-sm-2', 'placeholder' => 'CLAVE DEL CURSO', 'aria-label' => 'BUSCAR']) !!}
                {!! Form::text('txtTipo', 'searchCurso', ['class' => 'd-none']) !!}
                <button type="submit" id="btnBuscarCurso" class="btn btn-primary">BUSCAR</button>
                {!! Form::close() !!}
            </div> --}}
        {{-- </div> --}}

        {{-- curso --}}
        @if ($curso != null && !$curso->isEmpty())

            @if ($curso[0]->status == 'REPORTADO')
                <div class="alert alert-success">
                    <p class="text-center"> <strong>NO SE PUEDE AGREGAR EL CURSO DEBIDO A QUE YA HA SIDO REPORTADO</strong></p>
                </div>
            @elseif($curso[0]->status == 'CANCELADO')
                <div class="alert alert-success">
                    <p class="text-center"> <strong>NO SE PUEDE AGREGAR EL CURSO DEBIDO A QUE HA SIDO CANCELADO</strong></p>
                </div>
            @elseif($curso[0]->status == 'NINGUNO')
                <div class="alert alert-success">
                    <p class="text-center"> <strong>NO SE PUEDE AGREGAR EL CURSO DEBIDO A NINGUNO</strong></p>
                </div>
            @elseif($curso[0]->status != 'REPORTADO' && $curso[0]->turnado == 'UNIDAD')
                <table class="table table-bordered table-striped mt-2">
                    <thead>
                        <tr>
                            <th scope="col">CLAVE</th>
                            <th scope="col">UNIDAD</th>
                            <th scope="col">AREA</th>
                            <th scope="col">CURSO</th>
                            <th scope="col">ESPECIALIDAD</th>
                            <th scope="col">MOTIVO</th>
                            <th scope="col">DESCRIPCIÓN</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($curso as $course)
                            <input type="text" name="txtId" id="txtId" class="d-none" value="{{ $course->id }}">
                            <tr>
                                <td>{{ $course->clave }}</td>
                                <td>{{ $course->unidad }}</td>
                                <td>{{ $course->area }}</td>
                                <td>{{ $course->curso }}</td>
                                <td>{{ $course->espe }}</td>
                                <td>
                                    <select name="motivo" class="form-control mr-sm-2" id="motivo">
                                        <option value="">SELECCIONE EL MOTIVO</option>
                                        <option>REPROGRAMACIÓN FECHA/HORA</option>
                                        <option>CAMBIO DE INSTRUCTOR</option>
                                        <option>CANCELACIÓN DE CURSO</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-group my-0">
                                        <textarea class="form-control" name="descripcion" id="descripcion"
                                            placeholder="DESCRIPCIÓN" rows="2"></textarea>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="row pr-2 d-flex justify-content-end">
                    <button id="btnAgregarCurso" type="button" class="btn btn-success">AGREGAR CURSO</button>
                </div>
            @endif
        @endif

        {{-- <hr> --}}
        {{-- form solicitud --}}
        {{-- <form id="formSearchSolicitud" action="{{route('solicitudesDA.inicio')}}" method="GET"> 
            @csrf
            <div class="form-group row d-flex align-items-center">
                <input type="text" name="txtTipo" id="txtTipo" class="d-none" value="searchSolicitud">
                <div class="form-group col-3">
                    <input type="text" class="form-control" id="num_solicitudS" name="num_solicitudS"
                        placeholder="NÚMERO DE SOLICITUD">
                </div>
                <div class="form-group col-3">
                    <button type="submit" class="btn btn-primary">BUSCAR SOLICITUD</button>
                </div>
            </div>
        </form> --}}

        
        @if ($solicitud != null && !$solicitud->isEmpty())

            @if ($solicitud[0]->archivo_solicitud != null)
                <div class="alert alert-success">
                    <p class="text-center"> <strong>SOLICITUD ENVIADA A DTA</strong></p>
                </div>
            @endif

            <table class="table table-bordered table-striped mt-2">
                <thead>
                    <tr>
                        <th scope="col">CLAVE</th>
                        <th scope="col">UNIDAD</th>
                        <th scope="col">AREA</th>
                        <th scope="col">CURSO</th>
                        <th scope="col">ESPECIALIDAD</th>
                        <th scope="col">MOTIVO</th>
                        <th scope="col">DESCRIPCIÓN</th>
                        @if ($solicitud[0]->archivo_solicitud == null)
                            <th scope="col">ELIMINAR</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach ($solicitud as $item)
                        <tr>
                            <td>{{ $item->clave }}</td>
                            <td>{{ $item->unidad }}</td>
                            <td>{{ $item->area }}</td>
                            <td>{{ $item->curso }}</td>
                            <td>{{ $item->espe }}</td>
                            <td>{{ $item->opcion_solicitud }}</td>
                            <td>{{ $item->obs_solicitud }}</td>
                            @if ($item->archivo_solicitud == null)
                                <td class="text-center">
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Eliminar"
                                        href="{{ route('solicitudesDA.destroy', ['id' => $item->id_solicitud]) }}">
                                        <i class="fa fa-trash fa-2x pt-2" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($solicitud[0]->archivo_solicitud == null)
                <div class="row pr-2 d-flex justify-content-end">
                    <button id="btnTernarDTA" type="button" class="btn btn-success">TURNAR A DTA</button>
                    <a href="{{route('tablaSolicitud.pdf', ['id' => $solicitud[0]->num_solicitud])}}">
                        <button type="button" class="btn btn-primary">GENERAR PDF</button>
                    </a>
                </div>
            @endif
        @endif
        <hr>

        {{-- toast --}}
        <div aria-live="assertive" aria-atomic="true" style="position: absolute; top: 15%; right: 0;" role="alert"
            class="toast mt-3 mr-3" data-autohide="false">
            <div class="toast-header bg-primary">
                <strong id="titleToast" class="mr-auto text-white"></strong>
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="msgVolumen" class="toast-body">
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalSolicitudes" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4"><strong id="modalBody"></strong></div>
                <div class="modal-footer">
                    <button id="btnAceptarAdd" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal solicitar archivo -->
    <div class="modal fade" id="modalArchivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                

                <form id="formCargarArchivo" enctype="multipart/form-data" action="{{route('solicitudesDA.guardarSolicitud')}}" method="post">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">CARGAR SOLICITUD</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- archivo --}}
                        <input class="d-none" type="text" id="idsolicitud" name="idSolicitud" value="{{($solicitud != null && !$solicitud->isEmpty()) ? $solicitud[0]->id_solicitud : ''}}">
                        <input class="d-none" type="text" class="form-control" id="num_solicitud" name="num_solicitud" value="{{($solicitud != null && !$solicitud->isEmpty()) ? $solicitud[0]->num_solicitud : ''}}">
                        <div class="form-group col">
                            <label for="status">ARCHIVO DE SOLICITUD</label>
                            <div class="custom-file">
                                <input type="file" id="archivo_solicitud" name="archivo_solicitud" accept="application/pdf"
                                    class="custom-file-input">
                                <label for="archivo_solicitud" class="custom-file-label">ARCHIVO SOLICITUD</label>
                            </div>
                            <small>La solicitud debera estar firmada y sellada por la unidad</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">TURNAR A DTA</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection

@section('script_content_js')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        /*$('#formDataGenerales').validate({
            rules: {
                num_solicitud: {
                    required: true
                },
                fecha_solicitud: {
                    required: true
                }
            },
            messages: {
                num_solicitud: {
                    required: 'Número de solicitud requerido'
                },
                fecha_solicitud: {
                    required: 'Fecha de solicitud requerida'
                }
            }
        }); */

        $('#formBuscarCurso').validate({
            rules: {
                searchCurso: {
                    required: true
                }
            },
            messages: {
                searchCurso: {
                    required: 'Campo curso requerido'
                }
            }

        });

        $('#formCargarArchivo').validate({
            rules: {
                archivo_solicitud: {
                    required: true
                }
            }, 
            messages: {
                archivo_solicitud: {
                    required: 'Debe cargar la solicitud en formato pdf'
                }
            }
        });

        $("#fecha_solicitud").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });

        var objCurso;
        $('#btnAgregarCurso').click(function() {
            if ($('#num_solicitud').val() != '') {
                if ($('#fecha_solicitud').val() != '') {
                    if ($('#motivo').val() != '') {
                        if ($('#descripcion').val() != '') {
                            objCurso = recolectarDatos('POST');
                            EnviarInformacion(objCurso, 'insert');
                        } else {
                            $('#titleToast').html('Descripción de la solicitud');
                            $("#msgVolumen").html("El campo descripción de solicitud es requerido");
                            $(".toast").toast("show");
                        }
                    } else {
                        $('#titleToast').html('Motivo de la solicitud');
                        $("#msgVolumen").html("El campo motivo de solicitud es requerido");
                        $(".toast").toast("show");
                    }
                } else {
                    $('#titleToast').html('Fecha de solicitud');
                    $("#msgVolumen").html("El campo fecha de solicitud es requerido");
                    $(".toast").toast("show");
                }
            } else {
                $('#titleToast').html('Número de solicitud');
                $("#msgVolumen").html("El campo número de solicitud es requerido");
                $(".toast").toast("show");
            }
        });

        function EnviarInformacion(objCurso, tipo) {
            $.ajax({
                type: 'POST',
                url: '/solicitudesDA/guardar',
                data: objCurso,
                success: function(msg) {
                    
                    if (msg != 'duplicado') {
                        $('#modalTitle').html('Operación exitosa');
                        $('#modalBody').html('El curso se agrego a la solicitud exitosamente');
                        $('#modalSolicitudes').modal('show');
                    } else {
                        $('#modalTitle').html('Curso repetido');
                        $('#modalBody').html('El curso ya esta agregado al número de solicitud');
                        $('#modalSolicitudes').modal('show');
                    }
                },
                error: function(jqXHR, textStatus) {
                    console.log(jqXHR);
                    console.log(textStatus);
                }
            });
        }

        function recolectarDatos(method) {
            nuevoCurso = [];
            nuevoCurso = {
                'num_solicitud': $('#num_solicitud').val(),
                'fecha_solicitud': $('#fecha_solicitud').val(),
                'id_curso': $('#txtId').val(),
                'opcion_solicitud': $('#motivo').val(),
                'obs_solicitud': $('#descripcion').val(),
                '_token': $("meta[name='csrf-token']").attr("content"),
                '_method': method
            }
            return nuevoCurso;
        }

        $('#btnAceptarAdd').click(function () {
            $('#btnBuscarCurso').click();
        });

        $('#btnTernarDTA').click(function () {
            $('#modalArchivo').modal('show');
        });

    </script>
@endsection