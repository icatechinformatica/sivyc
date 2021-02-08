@extends("theme.sivyc.layout")
<!--llamar la plantilla -->

@section('content_script_css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection

@section('content')
    <!--empieza aquí-->

    <div class="container g-pt-30">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif

        <form method="POST" action="{{ route('convenios.update', ['id' => base64_encode($convenios->id)]) }}"
            id="conveniosForm" enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')

            <div style="text-align: center">
                <label for="tituloagregar_convenio">
                    <h1>EDITAR CONVENIO</h1>
                </label>
            </div>

            <hr>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="no_convenio" class="control-label">N° CONVENIO</label>
                    <input type="text" class="form-control" id="no_convenio" name="no_convenio" placeholder="N° CONVENIO"
                        value="{{ $convenios->no_convenio }}">
                </div>
                <!-- Organismo -->
                <div class="form-group col-md-6">
                    <label for="institucion" class="control-label">INSTITUCIÓN</label>
                    <input type="text" class="form-control" id="institucion" name="institucion" placeholder="Institución"
                        value="{{ $convenios->institucion }}">
                </div>
                <!--Organismo Fin-->
            </div>

            <div class="form-row">
                <!--nombre_titular-->
                <div class="form-group col">
                    <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label>
                    <input type='text' id="nombre_titular" name="nombre_titular" class="form-control"
                        value="{{ $convenios->nombre_titular }}" />
                </div>
                <!-- Telefono -->
                <div class="form-group col">
                    <label for="telefono" class="control-label">TELÉFONO</label>
                    <input type="text" class="form-control" onkeypress="return solonumeros(event)" id="telefono"
                        name="telefono" placeholder="telefono" value="{{ $convenios->telefono }}">
                </div>
            </div>

            <div class="form-row">
                {{-- direccion --}}
                <div class="form-group col">
                    <label for="direccion" class="control-label">DIRECCIÓN</label>
                    <textarea name="direccion" class="form-control" id="direccion">{{ $convenios->direccion }}</textarea>
                </div>
                <!--poblacion-->
                <div class="form-group col">
                    <label for="poblacion" class="control-label">POBLACIÓN</label>
                    <input type='text' id="poblacion" name="poblacion" class="form-control"
                        value="{{ $convenios->poblacion }}" />
                </div>
                <!--municipio-->
                <div class="form-group col">
                    <label for="municipio" class="control-label">MUNICIPIO</label>
                    <select name="municipio" id="municipio" class="custom-select">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $municipio)
                            <option {{ $municipio->id == $convenios->municipio ? 'selected' : '' }}
                                value="{{ $municipio->id }}">{{ $municipio->muni }}</option>
                        @endforeach
                    </select>
                    {{-- <label for="municipio" class="control-label">MUNICIPIO</label> --}}
                    {{-- <input type='text' id="municipio" name="municipio" class="form-control" --}}
                    {{-- value="{{ $convenios->municipio }}" /> --}}
                </div>
            </div>

            <hr>

            <div class="form-row">
                <!--tipo convenio-->
                @if ($convenios->tipo_convenio == null)
                    <div class="form-group col">
                        <label for="tipo_convenio" class="control-label">TIPO DE CONVENIO</label>
                        <select name="tipo_convenio" id="tipo_convenio" class="custom-select">
                            <option value="">--SELECCIONAR--</option>
                            <option value="GENERAL">GENERAL</option>
                            <option value="ESPECIFICO">ESPECIFICO</option>
                        </select>
                    </div>
                @else
                    @if ($convenios->tipo_convenio == 'GENERAL')
                        <div class="form-group col">
                            <label for="tipo_convenio" class="control-label">TIPO DE CONVENIO</label>
                            <select name="tipo_convenio" id="tipo_convenio" class="custom-select">
                                <option selected value="GENERAL">GENERAL</option>
                                <option value="ESPECIFICO">ESPECIFICO</option>
                            </select>
                        </div>
                    @else
                        <div class="form-group col">
                            <label for="tipo_convenio" class="control-label">TIPO DE CONVENIO</label>
                            <select name="tipo_convenio" id="tipo_convenio" class="custom-select">
                                <option value="GENERAL">GENERAL</option>
                                <option selected value="ESPECIFICO">ESPECIFICO</option>
                            </select>
                        </div>
                    @endif
                @endif
                <!-- NOMBRE DE FIRMA -->
                <div class="form-group col">
                    <label for="nombre_firma" class="control-label">NOMBRE DE FIRMA</label>
                    <input type='text' id="nombre_firma" name="nombre_firma" class="form-control"
                        value="{{ $convenios->nombre_firma == null ? 'NO DEFINIDO' : $convenios->nombre_firma }}" />
                </div>
            </div>

            <div class="form-row">
                <!-- fecha inicial -->
                <div class="form-group col">
                    <label for="fecha_firma" class="control-label">FECHA DE LA FIRMA</label>
                    <input type='text' id="fecha_firma" autocomplete="off" readonly="readonly" name="fecha_firma"
                        value="{{ $convenios->fecha_firma }}" class="form-control datepicker" />
                </div>
                <!-- Fecha conclusion -->
                <div class="form-group col">
                    <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label>
                    <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino"
                        value="{{ $convenios->fecha_vigencia }}" class="form-control datepicker" />
                </div>
            </div>

            <div class="form-row">
                <!--nombre_enlace-->
                <div class="form-group col">
                    <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label>
                    <input type='text' id="nombre_enlace" name="nombre_enlace" class="form-control"
                        value="{{ $convenios->nombre_enlace }}" />
                </div>
                <!--telefono del enlace-->
                <div class="form-group col">
                    <label for="telefono_enlace" class="control-label">TELEFONO DEL ENLACE</label>
                    <input type='text' id="telefono_enlace" name="telefono_enlace" class="form-control"
                        value="{{ $convenios->telefono_enlace == null ? 'NO DEFINIDO' : $convenios->telefono_enlace }}" />
                </div>
            </div>

            <hr>

            <div class="form-row">
                {{-- archivo --}}
                <div class="col">
                    <div class="form-group col">
                        <label for="status">ARCHIVO DE CONVENIO</label>
                        <div class="custom-file">
                            <input type="file" id="archivo_convenio" name="archivo_convenio" accept="application/pdf"
                                class="custom-file-input">
                            <label for="archivo_convenio" class="custom-file-label">NUEVO ARCHIVO CONVENIO</label>
                        </div>
                    </div>

                    <div class="col">
                        <div class="custom-file">
                            @if (isset($convenios->archivo_convenio))
                                <a href="{{ $convenios->archivo_convenio }}" target="_blank"
                                    rel="{{ $convenios->archivo_convenio }}">
                                    <img src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px"
                                        height="50px">
                                </a>
                            @else
                                <strong class="ml-5">NO ADJUNTADO</strong>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Tipo de sector -->
                <div class="form-group col">
                    {{-- sector --}}
                    <label for="sector">TIPO SECTOR</label>
                    <select class="form-control" id="tipo" name="tipo">
                        <option value="">--SELECCIONAR--</option>
                        <option {{ trim($convenios->sector) == 'PUBLICO' ? 'selected' : '' }} value="PUBLICO">PUBLICO
                        </option>
                        <option {{ trim($convenios->sector) == 'PRIVADO' ? 'selected' : '' }} value="PRIVADO">PRIVADO
                        </option>
                        <option {{ trim($convenios->sector) == 'SOCIAL' ? 'selected' : '' }} value="SOCIAL">SOCIAL
                        </option>
                    </select>
                </div>
                {{-- publlicar --}}
                <div class="form-group col text-center pt-4">
                    <label for="publicar">PUBLICAR</label>
                    <input type="checkbox" id="publicar" name="publicar"
                        {{ $convenios->activo == 'true' ? 'checked' : '' }} data-toggle="toggle" data-on="Si"
                        data-off="No" data-onstyle="success" data-offstyle="danger" data-width="100" data-height="30">
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col d-flex justify-content-center">
                    <h1>Unidades</h1>
                    <div class="col d-flex justify-content-end">
                        <button id="btnMarcar" type="button" class="btn btn-link check-all">Marcar todos</button>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($unidades as $unidad)
                    <div class="col-4">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" value="{{ $unidad->unidad }}"
                                    class="custom-control-input settings"
                                        @if (json_decode($convenios->unidades) != null)
                                            @foreach (json_decode($convenios->unidades, true) as $unity)
                                                @if ($unidad->unidad == $unity)
                                                    checked
                                                @endif
                                            @endforeach
                                        @endif 
                                    name="unidades[]"
                                    id="check + {{ $unidad->id }}">
                                <label class="custom-control-label"
                                    for="check + {{ $unidad->id }}">{{ $unidad->unidad }}</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!--botones de enviar y retroceder-->
            <div class="row mt-5">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{ URL::previous() }}">Regresar</a>
                    </div>
                    @can('convenios.update')
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">Modificar</button>
                        </div>
                    @endcan
                </div>
            </div>
        </form>
        <br>
    </div>

    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}

@endsection

@section('script_content_js')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script>
        $(function() {
            var dateFormat = "mm/dd/yy",
                from = $("#fecha_firma")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1
                })
                .on("change", function() {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#fecha_termino").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1
                })
                .on("change", function() {
                    from.datepicker("option", "maxDate", getDate(this));
                });

            function getDate(element) {
                var date;
                try {
                    date = $.datepicker.parseDate(dateFormat, element.value);
                } catch (error) {
                    date = null;
                }

                return date;
            }

            // valildaciones
            $.validator.addMethod("phoneMX", function(phone_number, element) {
                phone_number = phone_number.replace(/\s+/g, "");
                return this.optional(element) || phone_number.length > 9 &&
                    phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})?[2-9]\d{2}?\d{4}$/);
            }, "Por favor especifique un número valido de teléfono");

            $.validator.addMethod("formatoFecha", function(value, element) {
                return value.match('')
            });

            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            });


            $('#conveniosForm').validate({
                rules: {
                    no_convenio: {
                        required: true
                    },
                    institucion: {
                        required: true
                    },
                    tipo: {
                        required: true
                    },
                    telefono: {
                        required: true,
                        phoneMX: true
                    },
                    sector: {
                        required: true
                    },
                    fecha_firma: {
                        required: true
                    },
                    fecha_termino: {
                        required: true
                    },
                    archivo_convenio: {
                        extension: "pdf",
                        filesize: 2000000
                    },
                    tipo_convenio: {
                        required: true
                    },
                    nombre_firma: {
                        required: true
                    },
                    telefono_enlace: {
                        required: true
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    no_convenio: {
                        required: 'El Número de convenio es requerido.'
                    },
                    institucion: {
                        required: 'El campo institución es requerido.'
                    },
                    tipo: {
                        required: 'el campo tipo es requerido.'
                    },
                    telefono: {
                        required: 'el telefono es requerido',
                        phoneMX: 'no es un número telefonico dado.'
                    },
                    sector: {
                        required: 'seleccione el tipo de sector.'
                    },
                    fecha_firma: {
                        required: 'la fecha de la firma es requerida'
                    },
                    fecha_termino: {
                        required: 'La fecha de termino es requerida.'
                    },
                    archivo_convenio: {
                        accept: "No es una extensión valida, son aceptado pdf.",
                        filesize: "El tamaño del archivo debe de ser menor a 2 Mb."
                    },
                    tipo_convenio: {
                        required: 'El tipo de convenio es requerido'
                    },
                    nombre_firma: {
                        required: 'El nombre de firma es requrido'
                    },
                    telefono_enlace: {
                        required: 'El telefono del enlace es requerido'
                    },
                    status: {
                        required: 'El status es requerido'
                    }
                }
            });

            var checked = true;
            $('#btnMarcar').html('Desmarcar todos');

            $('.check-all').on('click', function() {
                if (checked == false) {
                    $('.settings').prop('checked', true);
                    checked = true;
                    $('#btnMarcar').html('Desmarcar todos');
                } else {
                    $('.settings').prop('checked', false);
                    checked = false;
                    $('#btnMarcar').html('Marcar todos');
                }
            });
        });

        // switch
        $('#chkToggle2').bootstrapToggle();

    </script>

@endsection
