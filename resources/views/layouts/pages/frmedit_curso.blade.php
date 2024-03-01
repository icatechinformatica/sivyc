<!--plantilla trabajada por DANIEL MENDEZ CRUZ-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Editar Cursos | Sivyc Icatech')
<!--seccion-->
<head>
    <style>

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
            width: 600px;
            
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
            height: 180px;
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
            width: 600px;
            height: 180px;
            overflow: auto;
        }

        #checkboxes2 label {
            display: block;
        }

        #checkboxes2 label:hover {
            background-color: #1e90ff;
        }
        .form-check-input{
            width:22px;
            height:22px;
        }
        .form-check-label{
            padding: 8px 0 8px 15px;
        }
    </style>
</head>
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <div class="card-header">
        Catálogos / Cursos / Editar
    </div>
    <div class="card card-body" style=" min-height:450px;"> 
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br />
            @endif        
       <div class="col-lg-12 margin-tb">
        <form method="POST" action="{{ route('cursos-catalogo.update', ['id' => $cursos[0]->id ])}}" method="post" id="frmcursoscatalogo" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label><h1>Datos del Curso</h1></label>
        </div>
        <div class="form-row">
            @if ($cursos[0]->documento_solicitud_autorizacion)
            <a class="btn btn-warning text-dark" href="{{env('APP_URL').'/'.'storage'.$cursos[0]->documento_solicitud_autorizacion}}" target="_blank">
                MEMORÁNDUM SOLICITUD
            </a>
            @endif
            @if ($cursos[0]->documento_memo_validacion)
            <a class="btn btn-warning text-dark" href="{{env('APP_URL').'/'.'storage'.$cursos[0]->documento_memo_validacion}}" target="_blank">
                MEMORÁNDUM VALIDACIÓN
            </a>
            @endif
            @if ($cursos[0]->documento_memo_actualizacion)
            <a class="btn btn-warning text-dark" href="{{env('APP_URL').'/'.'storage'.$cursos[0]->documento_memo_actualizacion}}" target="_blank">
                MEMORÁNDUM ACTUALIZACIÓN
            </a>
            @endif
            
            @if ($cursos[0]->file_carta_descriptiva)
            <a class="btn btn-warning text-dark" href="{{env('APP_URL').'/'.'storage'.$cursos[0]->file_carta_descriptiva}}" target="_blank">
                CARTA DESCRIPTIVA
            </a>
            @endif
        </div>
        <hr />        
        <div class="form-row">
            <div class="form-group col-md-7">
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
            <div class="form-group col-md-2">
                <div class="unidad_especificar" @if($otrauni == false) style="display: none" @endif>
                    <label for="unidad_ubicacion_especificar" class="control-label">ESPECIFIQUE</label>
                    <input type="text" class="form-control" name="unidad_ubicacion_especificar" id="unidad_ubicacion_especificar" @if($otrauni == TRUE) value="{{$cursos[0]->unidad_amovil}}" @endif>
                </div>
            </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="areaCursos" class="control-label">CAMPO</label>
            <select class="form-control" id="areaCursos" name="areaCursos">
                <option value="">--SELECCIONAR--</option>
                @foreach ($areas as $itemareas)
                    <option {{( $cursos[0]->area == $itemareas->id) ? "selected" : ""}} value="{{$itemareas->id}}">{{$itemareas->formacion_profesional}}</option>
                @endforeach
            </select>
          </div>
          <div class="form-group col-md-8">
            <label for="especialidadCurso" class="control-label">ESPECIALIDAD</label>
            <select class="form-control" id="especialidadCurso" name="especialidadCurso">
                <option value="">--SELECCIONAR--</option>
                @foreach ($especialidades as $itemespecialidades)
                    <option {{( $cursos[0]->id_especialidad == $itemespecialidades->id) ? "selected" : ""}} value="{{$itemespecialidades->id}}">{{$itemespecialidades->nombre}}</option>
                @endforeach
            </select>
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
            <div class="form-group col-md-3">
                <label for="costo" class="control-label">COSTO</label>
                <input type="text" class="form-control" id="costo_curso" name="costo" placeholder="COSTO" value="{{$cursos[0]->costo}}">
            </div>          
            <div class="form-group col-md-3">
              <label for="duracion" class="control-label">DURACIÓN EN HORAS</label>
              <input type="text" class="form-control" id="duracion" name="duracion" placeholder="DURACIÓN EN HORAS" value="{{$cursos[0]->horas}}">
            </div>            
        </div>
        <div class="form-row">            
            <div class="form-group col-md-4">
              <label for="perfil" class="control-label">PERFIL DE INGRESO SUGERIDO</label>
              
              <select class="form-control" id="perfil" name="perfil">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($perfil as $p)                        
                        <option value="{{$p}}" {{$cursos[0]->perfil == $p ? "selected" : "" }} >{{$p}}</option>
                    @endforeach
                </select>
            </div>            
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
              <label for="objetivo" class="control-label">OBJETIVO DEL CURSO</label>
              <textarea name="objetivo" id="objetivo" class="form-control" cols="15" rows="4" placeholder="OBJETIVO DEL CURSO">{{ $cursos[0]->objetivo }}</textarea>
            </div>          
            <div class="form-group col-md-6">
                <label for="descripcionCurso" class="control-label">OBSERVACIONES</label>
                <textarea name="descripcionCurso" id="descripcionCurso" class="form-control" cols="15" rows="4" placeholder="DESCRIPCIÓN">{{ $cursos[0]->descripcion }}</textarea>
            </div>
        </div><br/>
        <hr>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="solicitud_autorizacion" class="control-label">SOLICITUD DE AUTORIZACIÓN DE RIESGO</label>
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
            <div class="form-group col-md-4">
                <label for="memo_validacion" class="control-label">MEMO DE VALIDACIÓN</label>
                <input type="text" class="form-control" id="memo_validacion" name="memo_validacion" value="{{$cursos[0]->memo_validacion}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="documento_solicitud_autorizacion" class="control-label">DOCUMENTO SOLICITUD DE AUTORIZACIÓN</label>
                <input type="file" class="form-control" id="documento_solicitud_autorizacion" name="documento_solicitud_autorizacion">
            </div>
            <div class="form-group col-md-4">
                <label for="documento_memo_actualizacion" class="control-label">Documento Memo Actualización</label>
                <input type="file" class="form-control" id="documento_memo_actualizacion" name="documento_memo_actualizacion">
            </div>           
            <div class="form-group col-md-4">
                <label for="documento_memo_validacion" class="control-label">Documento Memo Validación</label>
                <input type="file" class="form-control" id="documento_memo_validacion" name="documento_memo_validacion">
            </div>
        </div>
        <div class="form-row">         
            <div class="form-group col-md-6">
              <label for="fecha_validacion" class="control-label">Fecha Validación</label>
              <input type="text" class="form-control" id="fecha_validacion" name="fecha_validacion" value="{{$fechaVal}}">
            </div>
            <div class="form-group col-md-6">
                <label for="fecha_actualizacion" class="control-label">Fecha Actualización</label>
                <input type="text" class="form-control" id="fecha_actualizacion" name="fecha_actualizacion" value="{{$fechaAct}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="cambios_especialidad" class="control-label">CAMBIOS DE ESPECIALIDAD</label>
                <input type="text" name="cambios_especialidad" id="cambios_especialidad" class="form-control" value="{{$cursos[0]->cambios_especialidad}}">
            </div>            
            <div class="form-group col-md-3">
                <label for="categoria" class="control-label">CATEGORIA</label>                
                <select class="form-control" id="categoria" name="categoria">
                    <option value="">--SELECCIONAR--</option>
                    @foreach ($categorias as $c)
                        <option value="{{$c}}" {{$cursos[0]->categoria == $c ? "selected" : "" }} >{{$c}}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="idCursos" id="idCursos" value="{{$cursos[0]->id}}">
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="categoria" class="control-label">TIPO DE CAPACITACIÓN</label>
                <select class="form-control" id="tipo_curso" name="tipo_curso">
                    <option {{$cursos[0]->tipo_curso == 'PRESENCIAL' ? "selected" : "" }} value="PRESENCIAL">PRESENCIAL</option>
                    <option {{$cursos[0]->tipo_curso == 'A DISTANCIA' ? "selected" : "" }} value="A DISTANCIA">A DISTANCIA</option>
                    <option {{$cursos[0]->tipo_curso == 'PRESENCIAL Y A DISTANCIA' ? "selected" : "" }} value="PRESENCIAL Y A DISTANCIA">PRESENCIAL Y A DISTANCIA</option>
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
            <div class="form-group col-md-5">
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
            <div class="form-group col-md-6">
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
        <div class="form-row">                        
            <div class="form-group col-md-6">
                <label for="file_carta_descriptiva" class="control-label">SUBIR CARTA DESCRIPTIVA</label>
                <input type="file" class="form-control" id="file_carta_descriptiva" name="file_carta_descriptiva">
            </div>
            <div class="form-group col-md-1"></div>
            <div class="form-group col-md-4  col-md-offset-2">
                <label for="documento_solicitud_autorizacion" class="control-label">&nbsp;</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="true" name="proyecto" id="proyecto" @if($cursos[0]->proyecto==true){{"checked"}} @endif />                    
                    <label class="form-check-label H6" for="flexCheckChecked">
                        PROYECTO
                    </label>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="categoria" class="control-label h6">CURSO/CERTIFICACIÓN</label>    
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="CURSO" name="servicio[]" @if(in_array("CURSO",json_decode($cursos[0]->servicio))){{"checked"}} @endif >
                    <label class="form-check-label H6" for="flexCheckChecked">
                        CURSO
                    </label>
                </div>  
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="CERTIFICACION" name="servicio[]"  @if(in_array("CERTIFICACION",json_decode($cursos[0]->servicio))){{"checked"}} @endif>                    
                    <label class="form-check-label H6" for="flexCheckChecked">
                        CERTIFICACIÓN
                    </label>
                </div>      
            </div>
            <div class="form-group col-md-2">
                <label for="categoria" class="control-label h6">ESTATUS DEL CURSO</label>                
                <select class="form-control" aria-label="estado" name="estado" id="estado">
                    <option value='1' @if($cursos[0]->estado==true){{"selected"}} @endif >ACTIVO</option>
                    <option value='2' @if($cursos[0]->estado==false){{"selected"}} @endif >INACTIVO</option>
                    <option value='3' @if(is_null($cursos[0]->estado)){{"selected"}} @endif >BAJA</option>
                </select>
            </div>            
            <div class="form-group col-md-8">
                <label for="motivo" class="control-label h6">MOTIVO</label>
                <textarea name="motivo" id="motivo" class="form-control"  rows="1" placeholder="MOTIVO">{{ trim($cursos[0]->motivo) }}</textarea>
            </div>
        </div> <br/><br/>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn" href="{{URL::previous()}}"><< Regresar</a>
                </div>
                <div class="pull-center">
                    <a class="btn" href="{{ route('curso-alta_baja', ['id' => $cursos[0]->id]) }}" >Activar por Unidad</a>                </div>
                <div class="pull-right">                    
                    @can('cursos.update')
                        <button type="submit" class="btn btn-danger" >Guardar Cambios</button>
                    @endcan
                </div>
            </div>
        </div>
    </form>
    <br>
    </div>
</div>
@endsection
@section('script_content_js')
    <script src="{{ asset('js/catalogos/cursos.js') }}"></script>
    <script type="text/javascript">
        var expanded = false;
        var expanded2 = false;
        $('#unidad_accion_movil').on("change", () => {
            if($('#unidad_accion_movil').val()==0){
                $('.unidad_especificar').css("display", "block");
            }else{
                $('.unidad_especificar').css("display", "none");
            }
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