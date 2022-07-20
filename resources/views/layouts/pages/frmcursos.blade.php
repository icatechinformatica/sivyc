@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Cursos | Sivyc Icatech')
<head>
    <style>
        .multiselect {
            width: 200px;
            }

        .selectBox {
            position: relative;
            width: 363px;
        }

        .selectBox select {
            width: 100%;
            font-weight: bold;
        }
        .selectBox2 {
            position: relative;
            width: 730px;
        }

        .selectBox2 select {
            width: 100%;
            font-weight: bold;
        }

        .overSelect {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }

        #checkboxes {
            display: none;
            border: 1px #dadada solid;
            width: 363px;
            height: 110px;
            overflow: auto;
        }

        #checkboxes label {
            display: block;
        }

        #checkboxes label:hover {
            background-color: #1e90ff;
        }

        #checkboxes2 {
            display: none;
            border: 1px #dadada solid;
            width: 730px;
            height: 110px;
            overflow: auto;
        }

        #checkboxes2 label {
            display: block;
        }

        #checkboxes2 label:hover {
            background-color: #1e90ff;
        }
    </style>
</head>
@section('content')
<div class="container g-pt-50">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br />
    @endif
    <form method="POST" action="{{ route('cursos.guardar-catalogo') }}" method="post" id="frmcursoscatalogo" enctype="multipart/form-data">
        @csrf
        <div style="text-align: right;width:60%">
            <label for="tituloformulariocurso"><h1>NUEVO CURSO</h1></label>
         </div>
         <hr style="border-color:dimgray">
        <div class="form-row">
          <!-- Unidad -->
          <div class="form-group col-md-6">
            <label for="areaCursos" class="control-label">CAMPO</label>
            <select class="form-control" id="areaCursos" name="areaCursos">
                <option value="">--SELECCIONAR--</option>
                @foreach ($areas as $itemareas)
                    <option value="{{$itemareas->id}}">{{$itemareas->formacion_profesional}}</option>
                @endforeach
            </select>
          </div>
          <!--Unidad Fin-->
          <!-- nombre curso -->
          <div class="form-group col-md-6">
            <label for="especialidadCurso" class="control-label">ESPECIALIDAD</label>
            <select class="form-control" id="especialidadCurso" name="especialidadCurso">
                <option value="">--SELECCIONAR--</option>
            </select>
          </div>
          <!-- nombre curso FIN-->
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="nombrecurso" class="control-label">NOMBRE DEL CURSO</label>
                <input type="text" class="form-control" id="nombrecurso" name="nombrecurso">
            </div>
            <div class="form-group col-md-3">
                <label for="unidad_accion_movil" class="control-label">UNIDAD ACCIÓN MOVIL</label>
                <select class="form-control" name="unidad_accion_movil" id="unidad_accion_movil">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($unidadesMoviles as $itemUnidaMovil)
                        <option value="{{$itemUnidaMovil->ubicacion}}">{{$itemUnidaMovil->ubicacion}}</option>
                    @endforeach
                    <option value="0">OTRO</option>
                </select>
            </div>
            <div class="form-group col-md-5">
                <div class="unidad_especificar" style="display: none">
                    <label for="unidad_ubicacion_especificar" class="control-label">ESPECIFIQUE</label>
                    <input type="text" class="form-control" name="unidad_ubicacion_especificar"
                        id="unidad_ubicacion_especificar">
                </div>
            </div>
        </div>
        <div class="form-row">
            <!--modalidad-->
            <div class="form-group col-md-3">
                <label for="modalidad" class="control-label">MODALIDAD</label>
                <select class="form-control" id="modalidad" name="modalidad">
                  <option value="">--SELECCIONAR--</option>
                  <option value="CAE">CAE</option>
                  <option value="EXT">EXT</option>
                  <option value="CAE Y EXT">CAE Y EXT</option>
                </select>
            </div>
            <!--clasificacion-->
            <div class="form-group col-md-3">
                <label for="clasificacion  " class="control-label">CLASIFICACIÓN</label>
                <select class="form-control" id="clasificacion" name="clasificacion">
                    <option value="">--SELECCIONAR--</option>
                    <option value="A-BASICO">A-BÁSICO</option>
                    <option value="B-INTERMEDIO">B-INTERMEDIO</option>
                    <option value="C-AVANZADO">C-AVANZADO</option>
                </select>
            </div>
            <!--clasificacion END-->
            <!-- Puesto-->
            <div class="form-group col-md-3">
                <label for="costo" class="control-label">COSTO</label>
                <input type="text" class="form-control" id="costo_curso" name="costo" placeholder="costo">
            </div>
            <!-- Puesto END-->
            <!-- Duracion -->
            <div class="form-group col-md-3">
              <label for="duracion" class="control-label">DURACIÓN EN HORAS</label>
              <input type="text" class="form-control" id="duracion" name="duracion" placeholder="duracion">
            </div>
            <!-- Duracion END -->
        </div>
        <div class="form-row">

            <!-- Perfil-->
            <div class="form-group col-md-6">
              <label for="perfil" class="control-label">PERFIL</label>
              <input type="text" class="form-control" id="perfil" name="perfil" placeholder="perfil">
            </div>
            <!-- Perfil END-->
            <div class="form-group col-md-6">
                <label for="nivel_estudio" class="control-label">NIVEL DE ESTUDIO DEL INSTRUCTOR</label>
                <input type="text" name="nivel_estudio" id="nivel_estudio" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <!-- Objetivo -->
            <div class="form-group col-md-6">
              <label for="objetivo" class="control-label">OBJETIVO DEL CURSO</label>
              <textarea name="objetivo" id="objetivo" class="form-control" cols="15" rows="5" placeholder="OBJETIVO DEL CURSO"></textarea>
            </div>
            <!-- Objetivo END -->
            <!-- Accion Movil-->
            <div class="form-group col-md-6">
                <label for="descripcionCurso" class="control-label">OBSERVACION</label>
                <textarea name="descripcionCurso" id="descripcionCurso" class="form-control" cols="15" rows="5" placeholder="DESCRIPCIÓN"></textarea>
            </div>
            <!-- Accion Movil END-->
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="solicitud_autorizacion" class="control-label">SOLICITUD DE AUTORIZACIÓN</label>
                <select class="form-control" id="solicitud_autorizacion" name="solicitud_autorizacion">
                    <option value="">--SELECCIONAR--</option>
                    <option value="true">SI</option>
                    <option value="false">NO</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="memo_actualizacion" class="control-label">MEMO DE ACTUALIZACIÓN</label>
                <input type="text" class="form-control" id="memo_actualizacion" name="memo_actualizacion">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-4">
                <label for="memo_validacion" class="control-label">MEMO DE VALIDACIÓN</label>
                <input type="text" class="form-control" id="memo_validacion" name="memo_validacion">
            </div>
        </div>
        <div class="form-row">
            <!-- Solicitud -->
            <div class="form-group col-md-4">
              <label for="documento_solicitud_autorizacion" class="control-label">DOCUMENTO SOLICITUD AUTORIZACIÓN</label>
              <input type="file" class="form-control" id="documento_solicitud_autorizacion" name="documento_solicitud_autorizacion">
            </div>
            <!-- Solicitud END -->
            <!-- memo_actualizacion -->
            <div class="form-group col-md-4">
              <label for="documento_memo_actualizacion" class="control-label">DOCUMENTO MEMO ACTUALIZACIÓN</label>
              <input type="file" class="form-control" id="documento_memo_actualizacion" name="documento_memo_actualizacion">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-4">
                <label for="documento_memo_validacion" class="control-label">DOCUMENTO MEMO VALIDACIÓN</label>
                <input type="file" class="form-control" id="documento_memo_validacion" name="documento_memo_validacion">
            </div>
        </div>
        <div class="form-row">
            <!-- fecha_validacion -->
            <div class="form-group col-md-6">
              <label for="fecha_validacion" class="control-label">FECHA VALIDACIÓN</label>
              <input type="text" class="form-control" id="fecha_validacion" name="fecha_validacion" autocomplete="off">
            </div>
            <!-- memo_actualizacion END -->
            <div class="form-group col-md-6">
                <label for="fecha_actualizacion" class="control-label">FECHA AUTORIZACIÓN</label>
                <input type="text" class="form-control" id="fecha_actualizacion" name="fecha_actualizacion" autocomplete="off">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="cambios_especialidad" class="control-label">CAMBIOS DE ESPECIALIDAD</label>
                <input type="text" name="cambios_especialidad" id="cambios_especialidad" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="categoria" class="control-label">CATEGORIA</label>
                <input type="text" name="categoria" id="categoria" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="categoria" class="control-label">TIPO DE CURSO</label>
                <select class="form-control" id="tipo_curso" name="tipo_curso">
                    <option value="">--SELECCIONAR--</option>
                    <option value="PRESENCIAL">PRESENCIAL</option>
                    <option value="A DISTANCIA">A DISTANCIA</option>
                    <option value="PRESENCIAL Y A DISTANCIA">PRESENCIAL Y A DISTANCIA</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="criterio_pago_minimo" class="control-label">CRITERIO DE PAGO MINIMO</label>
                <select class="form-control" id="criterio_pago_minimo" name="criterio_pago_minimo">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($cp as $criterioPagoMin)
                        <option value="{{$criterioPagoMin->id}}">{{$criterioPagoMin->perfil_profesional}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="criterio_pago_maximo" class="control-label">CRITERIO DE PAGO MÁXIMO</label>
                <select class="form-control" id="criterio_pago_maximo" name="criterio_pago_maximo">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($cp as $criterioPagoMax)
                        <option value="{{$criterioPagoMax->id}}">{{$criterioPagoMax->perfil_profesional}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <div class="multiselect">
                    <div class="selectBox" onclick="showCheckboxes()">
                        <label for="a" class="control-label">GRUPOS VULNERABLES</label>
                        <select class="form-control">
                            <option>-SELECCIONAR-</option>
                        </select>
                        <div class="overSelect"></div>
                    </div>
                    <div id="checkboxes">
                        @foreach ($gruposvulnerables as $cadwell)
                        <label for="{{$cadwell->id}}">
                            <input type="checkbox" id="{{$cadwell->id}}" name="a[{{$cadwell->id}}]" value="{{$cadwell->grupo}}"/> {{$cadwell->grupo}}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="form-group col-md-8">
                <div class="multiselect">
                    <div class="selectBox2" onclick="showCheckboxes2()">
                        <label for="b" class="control-label">DEPENDENCIAS</label>
                        <select class="form-control">
                            <option>-SELECCIONAR-</option>
                        </select>
                        <div class="overSelect"></div>
                    </div>
                    <div id="checkboxes2">
                        @foreach ($dependencias as $cadwell)
                        <label for="b{{$cadwell->id}}">
                            <input type="checkbox" id="b{{$cadwell->id}}" name="b[{{$cadwell->id}}]" value="{{$cadwell->organismo}}"/> {{$cadwell->organismo}}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="form-row">
            <div class="form-group col-md-6">
                <label for="observaciones">OBSERVACIONES</label>
                <textarea name="observaciones" id="observaciones" cols="30" rows="5" class="form-control"></textarea>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                <div class="pull-right">
                    @can('cursos.store')
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    @endcan
                </div>
            </div>
        </div>
    </form>
    <br>
</div>
@endsection
@section('script_content_js')
    <script type="text/javascript">
        var expanded = false;
        var expanded2 = false;
        $(function(){
            $.validator.addMethod('filesize', function (value, element, param) {
                return this.optional(element) || (element.files[0].size <= param)
            }, 'El TAMAÑO DEL ARCHIVO DEBE SER MENOR A 2 MB.');
            /**
             * Modificacion de cursos, validación
            */
            $('#frmcursoscatalogo').validate({
                rules: {
                    nombrecurso: {
                        required: true
                    },
                    modalidad: {
                        required: true
                    },
                    clasificacion: {
                        required: true
                    },
                    documento_solicitud_autorizacion: {
                        extension: "pdf",
                        filesize: 2000000   //max size 2mb
                    },
                    documento_memo_actualizacion: {
                        extension: "pdf",
                        filesize: 2000000   //max size 2mb
                    },
                    documento_memo_validacion: {
                        extension: "pdf",
                        filesize: 2000000   //max size 2mb
                    },
                    fecha_validacion: {
                        required: true
                    },
                    areaCursos: {
                        required: true
                    },
                    especialidadCurso: {
                        required: true
                    },
                    duracion: {
                        number: true
                    },
                    tipo_curso: {
                            required: true,
                            valueNotEquals: "default"
                    },
                },
                messages: {
                    nombrecurso: {
                        required: "Por favor, Escriba nombre del curso"
                    },
                    modalidad: {
                        required: "Por favor, Seleccione la modalidad"
                    },
                    clasificacion: {
                        required: "Por favor, Seleccione la clasificación"
                    },
                    documento_solicitud_autorizacion: {
                        extension: "Sólo se permiten pdf",
                    },
                    documento_memo_actualizacion: {
                        extension: "Sólo se permiten pdf",
                    },
                    documento_memo_validacion: {
                        extension: "Sólo se permiten pdf",
                    },
                    fecha_validacion: {
                        required: "la fecha de validación es requerido"
                    },
                    especialidadCurso: {
                        required: "Por favor, Seleccione la especialidad"
                    },
                    areaCursos: {
                        required: "Por favor, Seleccione el campo"
                    },
                    duracion: {
                        number: 'Acepta sólo números'
                    },
                    tipo_curso: {
                        required: "Por favor ingrese el tipo de curso",
                        valueNotEquals: "Por favor ingrese el tipo de curso"
                }
                }
            });
        });
        $('#unidad_accion_movil').on("change", () => {
            $("#unidad_accion_movil option:selected").each( () => {
                var medioEntero = $('#unidad_accion_movil').val();
                if (!medioEntero) {
                    $("#unidad_ubicacion_especificar").css("display", "none");
                    $('#unidad_ubicacion_especificar').rules('remove', 'required');
                    $('.unidad_especificar').css("display", "none");
                } else {
                    if (medioEntero == 0) {
                        $("#unidad_ubicacion_especificar").css("display", "block");
                        $('#unidad_ubicacion_especificar').rules('add', {required: true,
                            messages: {
                                required: "Campo Requerido"
                            }
                        });
                        $('.unidad_especificar').css("display", "block");
                    } else {
                        $("#unidad_ubicacion_especificar").css("display", "none");
                        $('#unidad_ubicacion_especificar').rules('remove', 'required');
                        $('.unidad_especificar').css("display", "none");
                    }
                }
            });
        });
        function showCheckboxes() {
            var checkboxes = document.getElementById("checkboxes");
            if (!expanded) {
                checkboxes.style.display = "block";
                expanded = true;
            } else {
                checkboxes.style.display = "none";
                expanded = false;
            }
        }
        function showCheckboxes2() {
            var checkboxes = document.getElementById("checkboxes2");
            if (!expanded2) {
                checkboxes.style.display = "block";
                expanded2 = true;
            } else {
                checkboxes.style.display = "none";
                expanded2 = false;
            }
        }
    </script>
@endsection
