@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'AGREGAR EXONERACIÓN | Sivyc Icatech')

@section('content_script_css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection

@section('content')

    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <h1>AGREGAR EXONERACIÓN</h1>
            </div>
        </div>

        <hr>
        <form id="formAddExoneracion" action="{{ route('exoneraciones.guardar') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group row">
                <div class="form-group col">
                    <label for="unidad" class="control-label">UNIDAD DE CAPACITACIÓN</label>
                    <select name="unidad" id="unidad" class="custom-select">
                        <option value="">SELECCIONE UNA UNIDAD</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->unidad }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col">
                    <label for="no_memorandum" class="control-label">N° DE MEMORANDUM</label>
                    <input type="text" class="form-control" id="no_memorandum" name="no_memorandum"
                        placeholder="N° DE MEMORANDUM">
                </div>
            </div>

            <div class="form-group row">
                {{-- estado --}}
                <div class="form-group col">
                    <label for="estadoE" class="control-label">ESTADO</label>
                    <select name="estadoE" id="estadoE" class="custom-select">
                        <option value="">SELECCIONE UN ESTADO</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <!--municipio-->
                <div class="form-group col">
                    <label for="municipioE" class="control-label">MUNICIPIO</label>
                    <select name="municipioE" id="municipioE" class="custom-select">
                        <option value="">SELECCIONE UN MUNICIPIO</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="localidad" class="control-label">LOCALIDAD</label>
                    <input type="text" class="form-control" id="localidad" name="localidad" placeholder="LOCALIDAD">
                </div>
                <div class="form-group col">
                    <label for="fecha_memorandum" class="control-label">FECHA MEMORANDUM</label>
                    <input type='text' id="fecha_memorandum" autocomplete="off" readonly="readonly" name="fecha_memorandum"
                        class="form-control datepicker" placeholder="FECHA MEMORANDUM">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="tipo_exoneracion" class="control-label">TIPO DE EXONERACIÓN</label>
                    <select name="tipo_exoneracion" id="tipo_exoneracion" class="custom-select">
                        <option value="">--SELECCIONE--</option>
                        <option value="TOTAL">TOTAL</option>
                        <option value="PARCIAL">PARCIAL</option>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="porcentaje" class="control-label">PORCENTAJE DE LA EXONERACION</label>
                    <input type="number" class="form-control" id="porcentaje" name="porcentaje"
                        placeholder="PORCENTAJE DE LA EXONERACIÓN">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="razon_exoneracion" class="control-label">RAZON DE LA EXONERACIÓN</label>
                    <textarea class="form-control" name="razon_exoneracion" id="razon_exoneracion"
                        placeholder="RAZON DE LA EXONERACIÓN" rows="2"></textarea>
                </div>
                <div class="form-group col">
                    <label for="observaciones" class="control-label">OBSERVACIONES</label>
                    <textarea class="form-control" name="observaciones" id="observaciones" placeholder="OBSERVACIONES"
                        rows="2"></textarea>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="numero_convenio" class="control-label">NÚMERO DE CONVENIO</label>
                    <input type="text" class="form-control" id="numero_convenio" name="numero_convenio"
                        placeholder="NÚMERO DE CONVENIO">
                </div>
                {{-- archivo --}}
                <div class="form-group col">
                    <label for="status">MEMORANDUM SOPORTE</label>
                    <div class="custom-file">
                        <input type="file" id="memo_soporte" name="memo_soporte" accept="application/pdf"
                            class="custom-file-input">
                        <label for="memo_soporte" class="custom-file-label"></label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="status" class="control-label">ESTADO DE LA EXONERACIÓN</label>
                    <select name="status" id="status" class="custom-select">
                        <option value="">--SELECCIONE--</option>
                        <option value="EN PROCESO">EN PROCESO</option>
                        <option value="AUTORIZADO">AUTORIZADO</option>
                    </select>
                </div>

                {{-- publlicar --}}
                <div class="form-group col text-center pt-4">
                    <label for="activo">PUBLICAR</label>
                    <input type="checkbox" id="activo" name="activo"
                         data-toggle="toggle" data-on="Si"
                        data-off="No" data-onstyle="success" data-offstyle="danger" data-width="100" data-height="30">
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script_content_js')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

    <script>
        $(function() {
            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A {0} bytes.');
        });

        $('#formAddExoneracion').validate({
            rules: {
                unidad: {
                    required: true
                },
                no_memorandum: {
                    required: true
                },
                estadoE: {
                    required: true
                },
                municipioE: {
                    required: true
                },
                localidad: {
                    required: true
                },
                fecha_memorandum: {
                    required: true
                },
                tipo_exoneracion: {
                    required: true
                },
                porcentaje: {
                    required: true
                },
                razon_exoneracion: {
                    required: true
                },
                numero_convenio: {
                    required: true
                },
                status: {
                    required: true
                }
            },
            messages: {
                unidad: {
                    required: 'La unidad de capacitación es requerida'
                },
                no_memorandum: { 
                    required: 'El número de memorandum es requerido'
                },
                estadoE: {
                    required: 'El estado es requerido'
                },
                municipioE: {
                    required: 'El municipio es requerido'
                },
                localidad: {
                    required: 'La localidad es requerida'
                },
                fecha_memorandum: {
                    required: 'La fecha del memorandum es requerida'
                },
                tipo_exoneracion: {
                    required: 'El tipo de exoneración es requerido'
                },
                porcentaje: {
                    required: 'El porcentaje de exoneración es requerido'
                },
                razon_exoneracion: {
                    required: 'La razon de la exoneración es requerida'
                },
                numero_convenio: {
                    required: 'El número de exoneración es requerido'
                },
                status: {
                    required: 'El status de exoneración es requerido'
                }
            }
        });

        $('#estadoE').on("change", () => {
            var IdEstado = $('#estadoE').val();
            $('#estadoE option:selected').each(() => {
                var datos = {idEst: IdEstado, _token: "{{ csrf_token() }}"};
                var url = '/exoneraciones/sid/municipios';

                var request = $.ajax({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request.done((respuesta) => {
                    if (respuesta.length < 1) {
                        $("#municipioE").empty();
                        $("#municipioE").append(
                            '<option value="" selected="selected">--SELECCIONAR--</option>');
                    } else {
                        if (!respuesta.hasOwnProperty('error')) {
                            $("#municipioE").empty();
                            $("#municipioE").append(
                                '<option value="" selected="selected">--SELECCIONAR--</option>');
                            $.each(respuesta, (k, v) => {
                                $('#municipioE').append('<option value="' + v.id + '">' + v.muni + '</option>');
                            });
                            $("#municipioE").focus();
                        }
                    }
                });
                request.fail((jqXHR, textStatus) => {
                    alert("Ocurrio un error: " + textStatus);
                });
            });
        });

        $("#fecha_memorandum").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        })

    </script>
@endsection
