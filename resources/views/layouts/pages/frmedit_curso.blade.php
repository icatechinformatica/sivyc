<!--plantilla trabajada por DANIEL MENDEZ CRUZ-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Editar Cursos | Sivyc Icatech')
<!--seccion-->
<head>
    <style>
        .switch {
          position: relative;
          display: inline-block;
          width: 90px;
          height: 34px;
        }

        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }

        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }

        input:checked + .slider {
          background-color: #2196F3;
        }

        input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
          -webkit-transform: translateX(50px);
          -ms-transform: translateX(50px);
          transform: translateX(50px);
        }

        /* Rounded sliders */
        .slider.round {
          border-radius: 34px;
        }

        .slider.round:before {
          border-radius: 50%;
        }
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
    <form method="POST" action="{{ route('cursos-catalogo.update', ['id' => $cursos[0]->id ])}}" method="post" id="frmcursoscatalogo" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="text-align: right;width:65%">
            <label for="tituloformulariocurso"><h1>Formulario de Cursos</h1></label>
        </div>
        <hr style="border-color:dimgray">
        <label><h2>Vista de Documentos</h2></label>
        <div class="form-row">
            @if ($cursos[0]->documento_memo_validacion) 
            <a class="btn btn-warning" href="{{$cursos[0]->documento_memo_validacion}}" target="_blank">
                MEMORÁNDUM VALIDACIÓN
            </a>
            @endif
            @if ($cursos[0]->documento_memo_actualizacion)
            <a class="btn btn-warning" href="{{$cursos[0]->documento_memo_actualizacion}}" target="_blank">
                MEMORÁNDUM ACTUALIZACIÓN
            </a>
            @endif
            @if ($cursos[0]->documento_solicitud_autorizacion)
            <a class="btn btn-warning" href="{{$cursos[0]->documento_solicitud_autorizacion}}" target="_blank">
                MEMORÁNDUM ACTUALIZACIÓN
            </a>
            @endif
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">
          <!-- Unidad -->
          <div class="form-group col-md-6">
            <label for="areaCursos" class="control-label">CAMPO</label>
            <select class="form-control" id="areaCursos" name="areaCursos">
                <option value="">--SELECCIONAR--</option>
                @foreach ($areas as $itemareas)
                    <option {{( $cursos[0]->area == $itemareas->id) ? "selected" : ""}} value="{{$itemareas->id}}">{{$itemareas->formacion_profesional}}</option>
                @endforeach
            </select>
          </div>
          <!--Unidad Fin-->
          <!-- nombre curso -->
          <div class="form-group col-md-6">
            <label for="especialidadCurso" class="control-label">ESPECIALIDAD</label>
            <select class="form-control" id="especialidadCurso" name="especialidadCurso">
                <option value="">--SELECCIONAR--</option>
                @foreach ($especialidades as $itemespecialidades)
                    <option {{( $cursos[0]->id_especialidad == $itemespecialidades->id) ? "selected" : ""}} value="{{$itemespecialidades->id}}">{{$itemespecialidades->nombre}}</option>
                @endforeach
            </select>
          </div>
          <!-- nombre curso FIN-->
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="nombrecurso" class="control-label">NOMBRE DEL CURSO</label>
                <input type="text" class="form-control" id="nombrecurso" name="nombrecurso" value="{{$cursos[0]->nombre_curso}}">
            </div>
            <div class="form-group col-md-3">
                <label for="unidad_accion_movil" class="control-label">UNIDAD ACCIÓN MÓVIL</label>
                <select class="form-control" name="unidad_accion_movil" id="unidad_accion_movil">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($unidadesMoviles as $itemUnidaMovil)
                        <option {{( $cursos[0]->unidad_amovil == $itemUnidaMovil->ubicacion) ? "selected" : ""}} value="{{$itemUnidaMovil->ubicacion}}">{{$itemUnidaMovil->ubicacion}}</option>
                    @endforeach
                    <option value="0" @if($otrauni == TRUE) selected @endif>OTRO</option>
                </select>
            </div>
            <div class="form-group col-md-5">
                <div class="unidad_especificar" @if($otrauni == FALSE) style="display: none" @endif>
                    <label for="unidad_ubicacion_especificar" class="control-label">ESPECIFIQUE</label>
                    <input type="text" class="form-control" name="unidad_ubicacion_especificar"
                        id="unidad_ubicacion_especificar" @if($otrauni == TRUE) value="{{$cursos[0]->unidad_amovil}}" @endif>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="modalidad  " class="control-label">Modalidad</label>
                <select class="form-control" id="modalidad" name="modalidad">
                  <option value="">--SELECCIONAR--</option>
                  <option {{ trim($cursos[0]->modalidad) == "CAE" ? "selected" : "" }} value="CAE">CAE</option>
                  <option {{ trim($cursos[0]->modalidad) == "EXT" ? "selected" : "" }} value="EXT">EXT</option>
                  <option {{ trim($cursos[0]->modalidad) == "EMP" ? "selected" : "" }} value="EMP">EMP</option>
                  <option {{ trim($cursos[0]->modalidad) == "CAE Y EXT" ? "selected" : "" }} value="CAE Y EXT">CAE Y EXT</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="clasificacion  " class="control-label">CLASIFICACIÓN</label>
                <select class="form-control" id="clasificacion" name="clasificacion">
                    <option value="">--SELECCIONAR--</option>
                    <option {{ trim($cursos[0]->clasificacion) == "A-BASICO" ? "selected" : "" }} value="A-BASICO">A-BÁSICO</option>
                    <option {{ trim($cursos[0]->clasificacion) == "B-INTERMEDIO" ? "selected" : "" }} value="B-INTERMEDIO">B-INTERMEDIO</option>
                    <option {{ trim($cursos[0]->clasificacion) == "C-AVANZADO" ? "selected" : "" }} value="C-AVANZADO">C-AVANZADO</option>
                </select>
            </div>
            <!--clasificacion END-->
            <!-- Puesto-->
            <div class="form-group col-md-3">
                <label for="costo" class="control-label">COSTO</label>
                <input type="text" class="form-control" id="costo_curso" name="costo" placeholder="COSTO" value="{{$cursos[0]->costo}}">
            </div>
            <!-- Puesto END-->
            <!-- Duracion -->
            <div class="form-group col-md-3">
              <label for="duracion" class="control-label">DURACIÓN EN HORAS</label>
              <input type="text" class="form-control" id="duracion" name="duracion" placeholder="DURACIÓN EN HORAS" value="{{$cursos[0]->horas}}">
            </div>
            <!-- Duracion END -->
        </div>
        <div class="form-row">
            <!-- Perfil-->
            <div class="form-group col-md-6">
              <label for="perfil" class="control-label">PERFIL</label>
              <input type="text" class="form-control" id="perfil" name="perfil" placeholder="perfil" value="{{$cursos[0]->perfil}}">
            </div>
            <!-- Perfil END-->
            <div class="form-group col-md-6">
                <label for="nivel_estudio" class="control-label">NIVEL DE ESTUDIO DEL INSTRUCTOR</label>
                <input type="text" name="nivel_estudio" id="nivel_estudio" class="form-control" value="{{$cursos[0]->nivel_estudio}}">
            </div>
        </div>
        <div class="form-row">
            <!-- Objetivo -->
            <div class="form-group col-md-6">
              <label for="objetivo" class="control-label">OBJETIVO DEL CURSO</label>
              <textarea name="objetivo" id="objetivo" class="form-control" cols="15" rows="5" placeholder="OBJETIVO DEL CURSO">
                  {{ $cursos[0]->objetivo }}
              </textarea>
            </div>
            <!-- Objetivo END -->
            <!-- Accion Movil-->
            <div class="form-group col-md-6">
                <label for="descripcionCurso" class="control-label">OBSERVACIONES</label>
                <textarea name="descripcionCurso" id="descripcionCurso" class="form-control" cols="15" rows="5" placeholder="DESCRIPCIÓN">
                    {{ $cursos[0]->descripcion }}
                </textarea>
            </div>
            <!-- Accion Movil END-->
        </div>
        <hr>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="solicitud_autorizacion" class="control-label">SOLICITUD DE AUTORIZACIÓN</label>
                <select class="form-control" id="solicitud_autorizacion" name="solicitud_autorizacion">
                    <option value="">--SELECCIONAR--</option>
                    <option {{$cursos[0]->solicitud_autorizacion == true ? "selected" : "" }} value="true">SI</option>
                    <option {{$cursos[0]->solicitud_autorizacion == false ? "selected" : "" }} value="false">NO</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="memo_actualizacion" class="control-label">MEMO DE ACTUALIZACIÓN</label>
                <input type="text" class="form-control" id="memo_actualizacion" name="memo_actualizacion" value="{{$cursos[0]->memo_actualizacion}}">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-4">
                <label for="memo_validacion" class="control-label">MEMO DE VALIDACIÓN</label>
                <input type="text" class="form-control" id="memo_validacion" name="memo_validacion" value="{{$cursos[0]->memo_validacion}}">
            </div>
        </div>
        <div class="form-row">
            <!-- Solicitud -->
            <div class="form-group col-md-4">
                <label for="documento_solicitud_autorizacion" class="control-label">DOCUMENTO SOLICITUD DE AUTORIZACIÓN</label>
                <input type="file" class="form-control" id="documento_solicitud_autorizacion" name="documento_solicitud_autorizacion">
            </div>
            <!-- Solicitud END -->
            <!-- memo_actualizacion -->
            <div class="form-group col-md-4">
                <label for="documento_memo_actualizacion" class="control-label">Documento Memo Actualización</label>
                <input type="file" class="form-control" id="documento_memo_actualizacion" name="documento_memo_actualizacion">
            </div>
            <!-- fecha_validacion END -->
            <div class="form-group col-md-4">
                <label for="documento_memo_validacion" class="control-label">Documento Memo Validación</label>
                <input type="file" class="form-control" id="documento_memo_validacion" name="documento_memo_validacion">
            </div>
        </div>
        <div class="form-row">
            <!-- fecha_validacion -->
            <div class="form-group col-md-6">
              <label for="fecha_validacion" class="control-label">Fecha Validación</label>
              <input type="text" class="form-control" id="fecha_validacion" name="fecha_validacion" value="{{$fechaVal}}">
            </div>
            <!-- memo_actualizacion END -->
            <div class="form-group col-md-6">
                <label for="fecha_actualizacion" class="control-label">Fecha Actualización</label>
                <input type="text" class="form-control" id="fecha_actualizacion" name="fecha_actualizacion" value="{{$fechaAct}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="cambios_especialidad" class="control-label">CAMBIOS DE ESPECIALIDAD</label>
                <input type="text" name="cambios_especialidad" id="cambios_especialidad" class="form-control" value="{{$cursos[0]->cambios_especialidad}}">
            </div>
            <div class="form-group col-md-4">
                <label for="nivel_estudio" class="control-label">NIVEL DE ESTUDIO</label>
                <input type="text" name="nivel_estudio" id="nivel_estudio" class="form-control" value="{{$cursos[0]->nivel_estudio}}">
            </div>
            <div class="form-group col-md-4">
                <label for="categoria" class="control-label">CATEGORIA</label>
                <input type="text" name="categoria" id="categoria" class="form-control" value="{{$cursos[0]->categoria}}">
            </div>
            <input type="hidden" name="idCursos" id="idCursos" value="{{$cursos[0]->id}}">
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="categoria" class="control-label">TIPO DE CURSO</label>
                <select class="form-control" id="tipo_curso" name="tipo_curso">
                    <option {{$cursos[0]->tipo_curso == 'PRESENCIAL' ? "selected" : "" }} value="PRESENCIAL">PRESENCIAL</option>
                    <option {{$cursos[0]->tipo_curso == 'A DISTANCIA' ? "selected" : "" }} value="A DISTANCIA">A DISTANCIA</option>
                    <option {{$cursos[0]->tipo_curso == 'A DISTANCIA Y PRESENCIAL' ? "selected" : "" }} value="A DISTANCIA Y PRESENCIAL">A DISTANCIA Y PRESENCIAL</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="criterio_pago_minimo_edit" class="control-label">CRITERIO DE PAGO MINIMO</label>
                <select class="form-control" id="criterio_pago_minimo_edit" name="criterio_pago_minimo_edit">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($criterio_pago as $item_criterio_pago_minimo)
                        <option {{($cursos[0]->rango_criterio_pago_minimo == $item_criterio_pago_minimo->id) ? 'selected' : ''}}
                            value="{{$item_criterio_pago_minimo->id}}">
                                {{$item_criterio_pago_minimo->perfil_profesional}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="criterio_pago_maximo_edit" class="control-label">CRITERIO DE PAGO MÁXIMO</label>
                <select class="form-control" id="criterio_pago_maximo_edit" name="criterio_pago_maximo_edit">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($criterio_pago as $item_criterio_pago_maximo)
                        <option {{ ($cursos[0]->rango_criterio_pago_maximo == $item_criterio_pago_maximo->id) ? 'selected' : ''}}
                            value="{{$item_criterio_pago_maximo->id}}">
                                {{$item_criterio_pago_maximo->perfil_profesional}}
                        </option>
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
                            <input type="checkbox" id="{{$cadwell->id}}" name="a[{{$cadwell->id}}]" value="{{$cadwell->grupo}}" @if($gv != NULL) @foreach ($gv as $data) @if($data == $cadwell->grupo) checked @endif @endforeach @endif/> {{$cadwell->grupo}}
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
                            <input type="checkbox" id="b{{$cadwell->id}}" name="b[{{$cadwell->id}}]" value="{{$cadwell->organismo}}" @if($dp != NULL) @foreach($dp as $dpdata2) @if($dpdata2 == $cadwell->organismo) checked @endif @endforeach @endif/> {{$cadwell->organismo}}
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
        <hr style="border-color:dimgray">
        <label><h2>Estado General del Curso</h2></label>
        <div class="form-group col-md-4">
            @if ($cursos[0]->estado == true)
                <label class="switch">
                    <input id="estado" name="estado" type="checkbox" checked onclick="leyenda()">
                    <span class="slider round"></span>
                </label>
                <h5><p id="text1">Curso Activo</p><p id="text2" style="display:none">Curso Inactivo</p></h5>
            @else
                <label class="switch">
                    <input id="estado" name="estado" type="checkbox" onclick="leyenda()">
                    <span class="slider round"></span>
                </label>
                <h5><p id="text1" style="display:none">Curso Activo</p><p id="text2">Curso Inactivo</p></h5>
            @endif
        </div>
        <label><h2>Alta/Baja al Curso</h2></label>
        <div class="form-group col-md-8">
            <a class="btn btn-danger" href="{{ route('curso-alta_baja', ['id' => $cursos[0]->id]) }}" >Alta/Baja</a>
            <footer>El curso dado de baja puede ser dado de alta de nuevo en cualquier momento necesario y viceversa.</footer>
        </div>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
                <div class="pull-right">
                    @can('cursos.update')
                        <button type="submit" class="btn btn-primary" >Actualizar</button>
                    @endcan
                </div>
            </div>
        </div>
    </form>
    <br>
</div>
<script>
    function leyenda() {
      var checkBox = document.getElementById("estado");
      var text1 = document.getElementById("text1");
      var text2 = document.getElementById("text2");
      if (checkBox.checked == true){
        text1.style.display = "block";
        text2.style.display = "none";
      } else {
         text1.style.display = "none";
         text2.style.display = "block";
      }
    }
</script>
@endsection
@section('script_content_js')
    <script type="text/javascript">
        var expanded = false;
        var expanded2 = false;
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

<!--a-->
