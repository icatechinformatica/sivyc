@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'MODIFICAR EXONERACIÓN | Sivyc Icatech')

@section('content_script_css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection

@section('content')

    <div class="container mt-5">
        <div class="row">
            <div class="col">
                <h1>MODIFICAR EXONERACIÓN</h1>
            </div>
        </div>

        <hr>
        <form id="formEditExoneracion" action="{{ route('exoneraciones.update', ['id' => base64_encode($exoneracion->id)]) }}" method="POST" 
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group row">
                {{-- unidad de capacitacion --}}
                <div class="form-group col">
                    <label for="unidad" class="control-label">UNIDAD DE CAPACITACIÓN</label>
                    <select name="unidad" id="unidad" class="custom-select">
                        <option value="">SELECCIONE UNA UNIDAD</option>
                        @foreach ($unidades as $unidad)
                            <option {{$unidad->id == $exoneracion->id_unidad_capacitacion ? 'selected' : ''}} 
                                value="{{ $unidad->id }}">{{ $unidad->unidad }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- numero de memorandum --}}
                <div class="form-group col">
                    <label for="no_memorandum" class="control-label">N° DE MEMORANDUM</label>
                    <input type="text" class="form-control" id="no_memorandum" name="no_memorandum"
                        placeholder="N° DE MEMORANDUM" value="{{$exoneracion->no_memorandum}}">
                </div>
            </div>

            <div class="form-group row">
                {{-- estado --}}
                <div class="form-group col">
                    <label for="estadoEx" class="control-label">ESTADO</label>
                    <select name="estadoEx" id="estadoEx" class="custom-select">
                        <option value="">SELECCIONE UN ESTADO</option>
                        @foreach ($estados as $estado)
                            <option {{$estado->id == $exoneracion->id_estado ? 'selected' : ''}} 
                                value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <!--municipio-->
                <div class="form-group col">
                    <label for="municipioEx" class="control-label">MUNICIPIO</label>
                    <select name="municipioEx" id="municipioEx" class="custom-select">
                        <option value="">SELECCIONE UN MUNICIPIO</option>
                        @foreach ($municipios as $municipio)
                            <option {{ $municipio->id == $exoneracion->id_municipio ? 'selected' : '' }}
                                value="{{ $municipio->id }}">{{ $municipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="localidad" class="control-label">LOCALIDAD</label>
                    <input type="text" class="form-control" id="localidad" name="localidad"
                    placeholder="LOCALIDAD" value="{{$exoneracion->localidad}}">
                </div>
                <div class="form-group col">
                    <label for="fecha_memorandum" class="control-label">FECHA MEMORANDUM</label>
                    <input type='text' id="fecha_memorandum" autocomplete="off" readonly="readonly" 
                        name="fecha_memorandum" class="form-control datepicker" placeholder="FECHA MEMORANDUM"
                        value="{{$exoneracion->fecha_memorandum}}">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="tipo_exoneracion" class="control-label">TIPO DE EXONERACIÓN</label>
                    <select name="tipo_exoneracion" id="tipo_exoneracion" class="custom-select">
                        <option value="">--SELECCIONE--</option>
                        <option {{$exoneracion->tipo_exoneracion == 'TOTAL' ? 'selected' : ''}} value="TOTAL">TOTAL</option>
                        <option {{$exoneracion->tipo_exoneracion == 'PARCIAL' ? 'selected' : ''}} value="PARCIAL">PARCIAL</option>
                    </select>
                </div>
                <div class="form-group col">
                    <label for="porcentaje" class="control-label">PORCENTAJE DE LA EXONERACION</label>
                    <input type="number" class="form-control" id="porcentaje" name="porcentaje"
                        placeholder="PORCENTAJE DE LA EXONERACIÓN" value="{{$exoneracion->porcentaje}}">
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="razon_exoneracion" class="control-label">RAZON DE LA EXONERACIÓN</label>
                    <textarea class="form-control" name="razon_exoneracion" id="razon_exoneracion"
                        placeholder="RAZON DE LA EXONERACIÓN" rows="2">{{$exoneracion->razon_exoneracion}}</textarea>
                </div>
                <div class="form-group col">
                    <label for="observaciones" class="control-label">OBSERVACIONES</label>
                    <textarea class="form-control" name="observaciones" id="observaciones" placeholder="OBSERVACIONES"
                        rows="2">{{$exoneracion->observaciones}}</textarea>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col">
                    <label for="grupo_beneficiado" class="control-label">GRUPO BENEFICIADO</label>
                    <input type="text" class="form-control" id="grupo_beneficiado" name="grupo_beneficiado"
                        placeholder="GRUPO BENEFICIADO" value="{{$exoneracion->grupo_beneficiado}}">
                </div>
                <div class="form-group col">
                    <label for="numero_convenio" class="control-label">NÚMERO DE CONVENIO</label>
                    <input type="text" class="form-control" id="numero_convenio" name="numero_convenio"
                        placeholder="NÚMERO DE CONVENIO" value="{{$exoneracion->no_convenio}}">
                </div>
                {{-- archivo --}}
                {{-- <div class="form-group col">
                    <label for="status">MEMORANDUM SOPORTE</label>
                    <div class="custom-file">
                        <input type="file" id="memo_soporte" name="memo_soporte" accept="application/pdf"
                            class="custom-file-input">
                        <label for="memo_soporte" class="custom-file-label"></label>
                    </div>
                </div> --}}
                {{-- archivo --}}
                <div class="col">
                    <div class="form-group col">
                        <label for="status">ARCHIVO DE SOPORTE</label>
                        <div class="custom-file">
                            <input type="file" id="memo_soporte" name="memo_soporte" accept="application/pdf"
                                class="custom-file-input">
                            <label for="memo_soporte" class="custom-file-label">NUEVO ARCHIVO SOPORTE</label>
                        </div>
                    </div>

                    <div class="col">
                        <div class="custom-file d-flex flex-row-reverse">
                            @if (isset($exoneracion->memo_soporte_dependencia))
                                <a href="{{ $exoneracion->memo_soporte_dependencia }}" target="_blank"
                                    rel="{{ $exoneracion->memo_soporte_dependencia }}">
                                    <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px"
                                        height="50px">
                                </a>
                            @else
                                <strong class="ml-5">NO ADJUNTADO</strong>
                            @endif
                        </div>
                    </div>
                </div>


            </div>

            <div class="row mt-5">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">MODIFICAR</button>
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

        $('#formEditExoneracion').validate({
            rules: {
                unidad: {
                    required: true
                },
                no_memorandum: {
                    required: true
                },
                estadoEx: {
                    required: true
                },
                municipioEx: {
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
                grupo_beneficiado: {
                    required: true
                },
                numero_convenio: {
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
                estadoEx: {
                    required: 'El estado es requerido'
                },
                municipioEx: {
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
                grupo_beneficiado: {
                    required: 'El grupo beneficiado es requerido'
                },
                numero_convenio: {
                    required: 'El número de convenio es requerido'
                }
            }
        });

        $('#estadoEx').on("change", () => {
            var IdEstado = $('#estadoEx').val();
            $('#estadoEx option:selected').each(() => {
                var datos = {idEst: IdEstado};
                var url = '/exoneraciones/sid/municipios';

                var request = $.ajax({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request.done((respuesta) => {
                    if (respuesta.length < 1) {
                        $("#municipioEx").empty();
                        $("#municipioEx").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                    } else {
                        if (!respuesta.hasOwnProperty('error')) {
                            $("#municipioEx").empty();
                            $("#municipioEx").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                            $.each(respuesta, (k, v) => {
                                $('#municipioEx').append('<option value="' + v.id + '">' + v.muni + '</option>');
                            });
                            $("#municipioEx").focus();
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
