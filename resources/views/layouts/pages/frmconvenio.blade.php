@extends("theme.sivyc.layout")
<!--llamar la plantilla -->
@section('title', 'Nuevo de Convenio | Sivyc Icatech')

@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection
@section('content')       
    <div class="card-header">
        Catálogos / Nuevo Convenio
    </div>
    <div class="card card-body">
    <?php
        $id_organismo = null;
    ?>
    
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
            <div class="form-row">
                {{-- no convenio --}}
                <div class="form-group col-md-6">
                    <label for="no_convenio" class="control-label">N° CONVENIO</label>
                    <input type="text" class="form-control" id="no_convenio" name="no_convenio" placeholder="N° Convenio">
                </div>
                <!-- Organismo -->
                <div class="form-group col-md-6">
                    <label for="institucion" class="control-label">INSTITUCIÓN</label>
                    {{ Form::select('institucion', $organismos,$id_organismo, ['id'=>'institucion','class' => 'form-control mr-sm-2', 'placeholder' => '- SELECCIONAR -'] ) }}
                </div>
            </div>
            <div class="form-row">
                <!--nombre_titular-->
                <div class="form-group col">
                    <label for="nombre_titular" class="control-label">NOMBRE DEL TITULAR</label>
                    <input type='text' id="nombre_titular" name="nombre_titular" class="form-control"
                        placeholder="nombre del titular" readonly>
                </div>
                <!-- Telefono -->
                <div class="form-group col">
                    <label for="telefono" class="control-label">TELÉFONO</label>
                    <input type="text" class="form-control" onkeypress="return solonumeros(event)" id="telefono"
                        name="telefono" placeholder="telefono" readonly>
                </div>
                <!-- email -->
                <div class="form-group col">
                    <label for="correo_ins" class="control-label">CORREO DE LA INSTITUCIÓN</label>
                    <input type="email" class="form-control" onkeypress="return solonumeros(event)" id="correo_ins"
                        name="correo_ins" placeholder="CORREO DE LA INSTITUCIÓN" readonly>
                </div>
            </div>
            <div class="form-row">
                {{-- direccion --}}
                <div class="form-group col">
                    <label for="direccion" class="control-label">DIRECCIÓN</label>
                    <input type="text" class="form-control" name="direccion" id="direccion" placeholder="dirección" readonly>
                </div>
                <!--localidad-->
                <div class="form-group col">
                    <label for="poblacion" class="control-label">LOCALIDAD</label>
                    <input type='text' id="poblacion" name="poblacion"
                    placeholder="LOCALIDAD" class="form-control" readonly>
                </div>
            </div>

            <div class="form-row">
                <!--estados-->
                <div class="form-group col">
                    <label for="estadoG" class="control-label">ESTADO</label>
                    <input type='text' id="estadoG" name="estadoG" placeholder="ESTADO" class="form-control" readonly>
                </div>
                <!--municipio-->
                <div class="form-group col">
                    <label for="municipio" class="control-label">MUNICIPIO</label>
                    <input type='text' id="municipio" name="municipio" placeholder="MUNICIPIO" class="form-control" readonly>
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
                    <input type='text' id="nombre_firma" name="nombre_firma"
                    placeholder="NOMBRE DE FIRMA" class="form-control">
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
                    <input type='text' id="nombre_enlace" name="nombre_enlace" 
                    placeholder="NOMBRE DEL ENLACE" class="form-control">
                </div>
                <!--telefono del enlace-->
                <div class="form-group col">
                    <label for="telefono_enlace" class="control-label">TELEFONO DEL ENLACE</label>
                    <input type='text' id="telefono_enlace" name="telefono_enlace"
                        placeholder="TELEFONO DEL ENLACE" class="form-control">
                </div>
                <!-- email enlace -->
                <div class="form-group col">
                    <label for="correo_en" class="control-label">CORREO DEL ENLACE</label>
                    <input type="email" class="form-control" onkeypress="return solonumeros(event)" id="correo_en"
                        name="correo_en" placeholder="CORREO DEL ENLACE">
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
                <a class="btn " href="{{ URL::previous() }}">< Regresar</a>
                <button type="submit" class="btn btn-danger">Guardar</button>
                
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
                    nombre_enlace: {
                        required: true
                    },
                    telefono_enlace: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    archivo_convenio: {
                        extension: "pdf",
                        filesize: 3000000, //max size 2mb
                    }
                },
                messages: {
                    no_convenio: {
                        required: "Campo requerido"
                    },
                    institucion: {
                        required: "Campo requerido"
                    },
                    sector: {
                        required: "Campo requerido"
                    },
                    fecha_firma: {
                        required: "Campo requerido"
                    },
                    tipo_convenio: {
                        required: "Campo requerido"
                    },
                    nombre_firma: {
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
                    archivo_convenio: {
                        accept: "Sólo se permiten pdf",
                        filesize: "El archivo debe ser menor de 3 MB",
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
                    // to.datepicker("option", "minDate", getDate(this));
                }),
                to = $("#fecha_termino").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    dateFormat: 'dd-mm-yy'
                })
                .on("change", function() {
                    // from.datepicker("option", "maxDate", getDate(this));
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

            /* $('#estadoG').on("change", () => {
                var IdEstado = $('#estadoG').val();
                $('#estadoG option:selected').each(() => {
                    var datos = {idEst: IdEstado, _token: "{{ csrf_token() }}"};
                    var url = '/convenios/sid/municipios';

                    var request = $.ajax
                    ({
                        url: url,
                        method: 'POST',
                        data: datos,
                        dataType: 'json'
                    });

                    request.done((respuesta) => {
                        if (respuesta.length < 1) {
                            $("#municipio").empty();
                            $("#municipio").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#municipio").empty();
                                $("#municipio").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#municipio').append('<option value="' + v.id + '">' + v.muni + '</option>');
                                });
                                $("#municipio").focus();
                            }
                        }
                    });

                    request.fail(( jqXHR, textStatus) => {
                        alert( "Ocurrio un error: " + textStatus );
                    });
                });
            }); */

            $('#institucion').change(function(){
                console.log('print');
                var organismo_id=$(this).val();
                if($(organismo_id != '')){
                    $.ajax({
                        type: "GET",
                        url: "/convenios/organismo",
                        data:{organismo_id:organismo_id, _token:"{{csrf_token()}}"},
                        contentType: "application/json",              
                        dataType: "json",
                        success: function (data) { 
                            $('#nombre_titular').val(data['titular']);
                            $('#telefono').val(data['telefono']);
                            $('#correo_ins').val(data['correo']);
                            $('#direccion').val(data['direccion']);
                            $('#poblacion').val(data['localidad']); 
                            $('#estadoG').val(data['estado']); 
                            $('#municipio').val(data['municipio']); 
                        }
                    });
                }else{
                    $('#nombre_titular').val('');
                    $('#telefono').val('');
                    $('#correo_ins').val('');
                    $('#direccion').val('');
                    $('#poblacion').val('');
                    $('#estadoG').val(''); 
                    $('#municipio').val(''); 
                }
            });

        });
    </script>
@endsection
