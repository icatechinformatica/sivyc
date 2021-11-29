@extends("theme.sivyc.layout")
@section('title', 'Agregar Documento | Sivyc Icatech')

@section('content_script_css')
@endsection

@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col">
                @if ($message = Session::get('warning'))
                    <div class="alert alert-info">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                @if ($message = Session::get('danger'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Añadir documento para firmar electronicamente</div>
            <div class="card-body d-flex justify-content-center">
                <div class="col-12 col-md-6">
                    <form id="form" action="{{ route('addDocument.guardar') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        {{-- <div class="col my-2"> --}}
                            <div class="custom-file my-2">
                                <input type="file" id="doc" name="doc" class="custom-file-input" accept="application/pdf"
                                    lang="es">
                                <label class="custom-file-label" for="doc">Seleccionar Archivo</label>
                            </div>
                        {{-- </div> --}}

                        <div class="row py-3">
                            <div class="col">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="firmare" name="firmare">
                                    <label class="custom-control-label" for="firmare">¿Vas a firmar este documento?</label>
                                </div>
                            </div>
                            <div class="col">
                                <select class="custom-select" id="tipo_documento" name="tipo_documento">
                                    <option value="" selected>Tipo de Documento</option>
                                    {{-- <option value="Lista de asistencia">Lista de asistencia</option> --}}
                                    {{-- <option value="Lista de calificaciones">Lista de calificaciones</option> --}}
                                    <option value="Contrato">Contrato</option>
                                </select>
                            </div>
                        </div>

                        <div id="divQuienes" class="row"></div>

                        <div class="row mt-3">
                            <div class="col">
                                {{-- <input type="text" id="no_oficio" name="no_oficio" class="form-control" placeholder="N° de oficio o clave de curso"> --}}
                                <input type="text" id="no_oficio" name="no_oficio" class="form-control" placeholder="N° de contrato">
                            </div>
                        </div>

                        <div class="col text-center pt-2 pb-3"><strong>Añadir Firmantes</strong></div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <select class="custom-select" id="tipoUser" name="tipoUser">
                                    <option value="" selected>Tipo de usuario</option>
                                    <option value="1">Instructor</option>
                                    <option value="2">Funcionario</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="input-group flex-nowrap">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="addon-wrapping">@</span>
                                    </div>
                                    <input id="email" type="email" class="form-control" placeholder="Correo electronico"
                                        aria-label="Username" aria-describedby="addon-wrapping">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col text-center">
                                <button id="btnSearch" type="button" style="width: 200px"
                                    class="btn btn-outline-info">Buscar Firmante</button>
                            </div>
                        </div>

                        <div class="col text-center pt-3"><strong>Firmantes</strong></div>
                        <div id="firmantes" class="row mt-3"></div>

                        <div class="row pt-3">
                            <div class="col text-center">
                                <button id="btnSolicitar" style="width: 350px" type="button"
                                    class="btn btn-primary">Solicitar Firmas</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="loader">
            <div class="modal-dialog modal-dialog-centered text-center" role="document">
                <span id="spamLoader" class="fa fa-spinner text-white fa-spin fa-3x w-100"></span>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalMessages" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #541533">
                    <h5 class="modal-title text-white" id="titulo">cj</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong id="mensaje"></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Aceptar</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_content_js')
    <script>
        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });

        $('#btnSearch').click(function() {
            tipoUser = $('#tipoUser').val();
            email = $('#email').val();
            if (tipoUser != '' && email != '') {
                objFirmante = recolectarDatos('POST', tipoUser, email);
                enviarInformacion(objFirmante);
            } else {
                $('#titulo').html('Buscar Firmante');
                $('#mensaje').html(
                    'Debe seleccionar un tipo de usuario e ingresar un correo electronico antes de buscar a un firmante'
                );
                $('#modalMessages').modal('show');
            }
        });

        function recolectarDatos(method, tipoUser, email) {
            newFirmante = {
                tipo: tipoUser,
                email: email,
                '_token': $("meta[name='csrf-token']").attr("content"),
                '_method': method
            };
            return newFirmante;
        }

        function enviarInformacion(objFirmante) {
            $.ajax({
                type: 'POST',
                url: "{{ url('/addDocumento/buscar') }}",
                data: objFirmante,
                beforeSend: function() {
                    console.log('init');
                    $('#loader').modal('toggle')
                },
                success: function(result) {
                    $('#loader').modal('toggle')
                    if (result['id'] == null) {
                        sujeto = objFirmante['tipo'] == 1 ? 'el instructor' : 'el funcionario';
                        $('#titulo').html('Buscar Firmante');
                        $('#mensaje').html(
                            'No se encontraron coincidencias! Esto podria suceder por que el correo no esta escrito correctamente o porque ' +
                            sujeto + ' no se encuentra activo.');
                        $('#modalMessages').modal('show');
                    } else {
                        crearInputData(objFirmante['tipo'], result);
                    }
                },
                error: function(jqXHR, textStatus) {
                    $('#loader').modal('toggle')
                    console.log(jqXHR);
                    alert("Hubo un error: " + jqXHR.status);
                }
            }).done(function() {
                $('#loader').modal('toggle')
            });
        }

        function crearInputData(tipo, result) {
            tipo = tipo == 1 ? 'Instructor' : 'Funcionario';
            firmantes = document.getElementById('firmantes');
            firmantes.innerHTML += `
                    <div class="col-12">
                        <div class="alert alert-info" role="alert my-0 py-0">${result['nombre']} ${result['apellidoPaterno']} ${result['apellidoMaterno']}</div>
                        <input type="text" class="d-none" name="firmas[]" value="${tipo}-${result['id']}" input">
                    </div>
                `;
            $('#email').val('');
            $('#tipoUser').val('');
        }

        $('#form').validate({
            rules: {
                doc: {
                    required: true
                },
                tipo_documento: {
                    required: true
                },
                no_oficio: {
                    required: true
                }
            },
            messages: {
                doc: {
                    required: 'El documento a firmar es requerido'
                },
                tipo_documento: {
                    required: 'Campo requerido'
                },
                no_oficio: {
                    required: 'El número de oficio o clave de curso es requerido'
                }
            }
        });

        $('#btnSolicitar').click(function() {
            if (confirm("¿Está seguro de enviar a firma este documento?") == true) {
                $('#form').submit();
            }
        });

        $('#tipo_documento').change(function (e) {
            value = $('#tipo_documento').val();
            console.log(value);
            quienes = document.getElementById('divQuienes');
            if (value != '') {
                quienes.innerHTML = `
                    <div class="col">
                        <div class="alert alert-light text-center py-1" role="alert">
                            <p class="py-0 my-0"><strong>¿Quienes firman el ${value}?</strong></p> 
                            <p class="py-0 my-0">* Director(a) de la unidad</p>
                            <p class="py-0 my-0">* Instructor</p>
                            <p class="py-0 my-0">* Jefe de departamento academico</p>
                            <p class="py-0 my-0">* Jefe de departamento de vinculación</p>
                            <p class="py-0 my-0">* Delegado administrativo</p>
                        </div>
                    </div>
                `;
            } else {
                quienes.innerHTML = ``;
            }
            
        });

    </script>
@endsection