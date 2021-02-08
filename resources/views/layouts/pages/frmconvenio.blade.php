@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('title', 'Formulario de Convenio | Sivyc Icatech')

@section('content_script_css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection

{{-- contenido --}}
@section('content')

    <div class="container g-pt-10">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <br />
        @endif

        <form method="POST" action="{{ route('convenios.store') }}" id="frmConvenio" enctype="multipart/form-data"
            autocomplete="off">
            @csrf

            {{-- titulo --}}
            <div style="text-align: center">
                <label for="tituloagregar_convenio">
                    <h1>NUEVO CONVENIO</h1>
                </label>
            </div>

            <div class="form-row">
                {{-- no convenio --}}
                <div class="form-group col-md-6">
                    <label for="no_convenio" class="control-label">N° CONVENIO</label>
                    <input type="text" class="form-control" id="no_convenio" name="no_convenio" placeholder="N° Convenio">
                </div>
                <!-- Organismo -->
                <div class="form-group col-md-6">
                    <label for="institucion" class="control-label">INSTITUCIÓN</label>
                    <input type="text" class="form-control" id="institucion" name="institucion" placeholder="Institución">
                </div>
            </div>
            <div class="form-row">
                <!--nombre_titular-->
                <div class="form-group col">
                    <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label>
                    <input type='text' id="nombre_titular" name="nombre_titular" class="form-control"
                        placeholder="nombre del titular">
                </div>
                <!-- Telefono -->
                <div class="form-group col">
                    <label for="telefono" class="control-label">TELÉFONO</label>
                    <input type="text" class="form-control" onkeypress="return solonumeros(event)" id="telefono"
                        name="telefono" placeholder="telefono">
                </div>
            </div>
            <div class="form-row">
                {{-- direccion --}}
                <div class="form-group col">
                    <label for="direccion" class="control-label">DIRECCIÓN</label>
                    <input type="text" class="form-control" name="direccion" id="direccion" placeholder="dirección">
                </div>
                <!--municipio-->
                <div class="form-group col-md-4">
                    <label for="area" class="control-label">MUNICIPIO</label>
                    <select name="municipio" id="municipio" class="custom-select">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id }}">{{ $municipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
                <!--localidad-->
                <div class="form-group col">
                    <label for="poblacion" class="control-label">LOCALIDAD</label>
                    <input type='text' id="poblacion" name="poblacion" class="form-control">
                </div>
            </div>

            <hr>

            <div class="form-row">
                <!--tipo convenio-->
                <div class="form-group col">
                    <label for="tipo_convenio" class="control-label">TIPO DE CONVENIO</label>
                    <select name="tipo_convenio" id="tipo_convenio" class="custom-select">
                        <option value="">--SELECCIONAR--</option>
                        <option value="GENERAL">GENERAL</option>
                        <option value="ESPECIFICO">ESPECIFICO</option>
                    </select>
                </div>
                <!-- NOMBRE DE FIRMA -->
                <div class="form-group col">
                    <label for="nombre_firma" class="control-label">NOMBRE DE FIRMA</label>
                    <input type='text' id="nombre_firma" name="nombre_firma" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <!-- fecha inicial -->
                <div class="form-group col">
                    <label for="fecha_firma" class="control-label">FECHA DE LA FIRMA</label>
                    <input type='text' id="fecha_firma" autocomplete="off" readonly="readonly" name="fecha_firma"
                        class="form-control datepicker">
                </div>
                <!-- Fecha conclusion -->
                <div class="form-group col">
                    <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label>
                    <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino"
                        class="form-control datepicker">
                </div>
            </div>
            <div class="form-row">
                <!--nombre_enlace-->
                <div class="form-group col">
                    <label for="nombre_enlace" class="control-label">NOMBRE DEL ENLACE</label>
                    <input type='text' id="nombre_enlace" name="nombre_enlace" class="form-control">
                </div>

                <!--telefono del enlace-->
                <div class="form-group col">
                    <label for="telefono_enlace" class="control-label">TELEFONO DEL ENLACE</label>
                    <input type='text' id="telefono_enlace" name="telefono_enlace" class="form-control">
                </div>
            </div>

            <hr>

            <div class="form-row">
                {{-- archivo --}}
                <div class="form-group col">
                    <label for="status">ARCHIVO DE CONVENIO</label>
                    <div class="custom-file">
                        <input type="file" id="archivo_convenio" name="archivo_convenio" accept="application/pdf"
                            class="custom-file-input">
                        <label for="archivo_convenio" class="custom-file-label">ARCHIVO CONVENIO</label>
                    </div>
                </div>
                <!-- Tipo de sector -->
                <div class="form-group col">
                    <label for="sector">SECTOR</label>
                    <select class="form-control" id="sector" name="sector">
                        <option value="">--SELECCIONAR--</option>
                        <option value="PUBLICO">PUBLICO</option>
                        <option value="PRIVADO">PRIVADO</option>
                        <option value="SOCIAL">SOCIAL</option>
                    </select>
                </div>
                {{-- btn publicar --}}
                <div class="col text-center pt-4">
                    <label for="publicar">PUBLICAR</label>
                    <input type="checkbox" id="publicar" name="publicar" checked data-toggle="toggle" data-on="Si"
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
                                <input type="checkbox" value="{{ $unidad->unidad }}" class="custom-control-input settings"
                                    name="unidades[]" id="check + {{ $unidad->id }}">
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
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
    </div>

@endsection

@section('script_content_js')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

    <script type="text/javascript">
        $(function() {
            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A {0} bytes.');
            // validaciones
            $('#frmConvenio').validate({
                rules: {
                    no_convenio: {
                        required: true
                    },
                    institucion: {
                        required: true
                    },
                    nombre_titular: {
                        required: true
                    },
                    telefono: {
                        required: true
                    },
                    direccion: {
                        required: true
                    },
                    poblacion: {
                        required: true
                    },
                    tipo_convenio: {
                        required: true
                    },
                    nombre_firma: {
                        required: true
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
                    nombre_enlace: {
                        required: true
                    },
                    telefono_enlace: {
                        required: true
                    },
                    municipio: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    archivo_convenio: {
                        extension: "pdf",
                        filesize: 2000000, //max size 2mb
                    }
                },
                messages: {
                    no_convenio: {
                        required: "Campo requerido"
                    },
                    institucion: {
                        required: "Campo requerido"
                    },
                    telefono: {
                        required: "Campo requerido"
                    },
                    sector: {
                        required: "Campo requerido"
                    },
                    fecha_firma: {
                        required: "Campo requerido"
                    },
                    fecha_termino: {
                        required: "Campo requerido"
                    },
                    poblacion: {
                        required: "Campo requerido"
                    },
                    municipio: {
                        required: "Campo requerido"
                    },
                    tipo_convenio: {
                        required: "Campo requerido"
                    },
                    nombre_firma: {
                        required: "Campo requerido"
                    },
                    nombre_titular: {
                        required: "Campo requerido"
                    },
                    nombre_enlace: {
                        required: "Campo requerido"
                    },
                    telefono_enlace: {
                        required: "Campo requerido"
                    },
                    status: {
                        required: "Campo requerido"
                    },
                    direccion: {
                        required: "Campo requerido"
                    },
                    archivo_convenio: {
                        accept: "Sólo se permiten pdf",
                        filesize: "El archivo debe ser menor de 2 MB",
                    }
                }
            });
            var dateFormat = "dd-mm-yy",
                from = $("#fecha_firma")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: 'dd-mm-yy'
                })
                .on("change", function() {
                    to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#fecha_termino").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: 'dd-mm-yy'
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
            // switch
            $('#chkToggle2').bootstrapToggle();
            // checksBox
            var checked = true;
            $('.settings').prop('checked', true);
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
    </script>
@endsection