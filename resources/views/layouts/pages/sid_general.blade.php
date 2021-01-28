@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Inscripción Alumno | Sivyc Icatech')
<!--contenido-->
@section('content')
    <style>
        .modal
        {
            position: fixed;
            z-index: 999;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background-color: Black;
            filter: alpha(opacity=60);
            opacity: 0.6;
            -moz-opacity: 0.8;
        }
        .center
        {
            z-index: 1000;
            margin: 300px auto;
            padding: 10px;
            width: 150px;
            background-color: White;
            border-radius: 10px;
            filter: alpha(opacity=100);
            opacity: 1;
            -moz-opacity: 1;
        }
        .center img
        {
            height: 128px;
            width: 128px;
        }
    </style>
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
        <div style="text-align: center;">
            <h3><b>INSCRIPCIÓN (SID - 01)</b></h3>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS PERSONALES</b></h4>
        </div>
            <div class="form-row">
                <!--nombre aspirante-->
                <div class="form-group col-md-3">
                    <label for="nombre " class="control-label">Nombre: {{$Alumno->nombre}}</label>
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-3">
                    <label for="apellidoPaterno" class="control-label">Apellido Paterno: {{$Alumno->apellido_paterno}}</label>
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-3">
                    <label for="apellidoMaterno" class="control-label">Apellido Materno: {{$Alumno->apellido_materno}}</label>
                </div>
                <!-- apellido materno END-->
                <div class="form-group col-md-3">
                    <label for="sexo" class="control-label">Genero: {{$Alumno->sexo}}</label>
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="curp" class="control-label">CURP: {{$Alumno->curp}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento: {{$Alumno->fecha_nacimiento}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="telefono" class="control-label">Teléfono: {{$Alumno->telefono}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="cp" class="control-label">C.P. {{$Alumno->cp}}</label>
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="estado" class="control-label">Estado: {{$Alumno->estado}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">Municipio: {{$Alumno->municipio}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="estado_civil" class="control-label">Estado Civil: {{$Alumno->estado_civil}}</label>
                </div>
                <!---->
                <div class="form-group col-md-3">
                    <label for="discapacidad" class="control-label">Discapacidad que presenta: {{$Alumno->discapacidad}}</label>
                </div>
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio" class="control-label">Domicilio: {{$Alumno->domicilio}}</label>
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia" class="control-label">Colonia: {{$Alumno->colonia}}</label>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="es_cereso" class="control-label"><b>{{($Alumno->es_cereso == true) ? 'EN EL CERESO' : ''}}</b></label>
                </div>
            </div>
            @if ($Alumno->es_cereso == true)
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="es_cereso" class="control-label">EXPEDIENTE NO°: <b>{{$Alumno->numero_expediente}}</b></label>
                    </div>
                    <div class="form-row col-md-4">
                        <label for="es_cereso" class="control-label">NOMBRE DEL CERESO: <b>{{$Alumno->nombre_cerss}}</b> </label>
                    </div>
                    <div class="form-row col-md-4">
                        <label for="es_cereso" class="control-label">TITULAR DEL CERESO: <b>{{$Alumno->titular_cerss}}</b> </label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="direccion_cereso" class="control-label">DIRECCIÓN DEL CERESO: <b>{{$Alumno->direccion_cerss}}</b></label>
                    </div>
                </div>
            @endif
            <!---->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS GENERALES</b></h4>
            </div>
            <form method="POST" id="form_sid_registro" action="{{ route('alumnos.update-sid') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tblubicacion" class="control-label">UNIDADES</label>
                        <select class="form-control" id="tblubicacion" name="tblubicacion" required>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($tblUnidades as $itemTblUnidades)
                                <option value="{{$itemTblUnidades->ubicacion}}">{{$itemTblUnidades->ubicacion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tblunidades" class="control-label">UNIDAD O ACCIÓN MÓVIL A LA QUE SE DESEA INSCRIBIRSE</label>
                        <select class="form-control" id="tblunidades" name="tblunidades" required>
                            <option value="">--SELECCIONAR--</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="especialidad" class="control-label">ESPECIALIDAD A LA QUE DESEA INSCRIBIRSE:</label>
                        <select class="form-control" id="especialidad_sid" name="especialidad_sid" required>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($especialidades as $itemEspecialidad)
                                <option value="{{$itemEspecialidad->id}}">{{ $itemEspecialidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tipo_curso" class="control-label">TIPO DE CURSO</label>
                        <select class="form-control" id="tipo_curso" name="tipo_curso" required>
                            <option value="">--SELECCIONAR--</option>
                            <option value="PRESENCIAL">PRESENCIAL</option>
                            <option value="A DISTANCIA">A DISTANCIA</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="cursos" class="control-label">CURSO:</label>
                        <select class="form-control" id="cursos_sid" name="cursos_sid" required>
                            <option value="">--SELECCIONAR--</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="horario" class="control-label">HORARIO:</label>
                        <input type="text" name="horario" id="horario" class="form-control" autocomplete="off">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="grupo" class="control-label">GRUPO:</label>
                        <input type="text" name="grupo" id="grupo" class="form-control" autocomplete="off">
                    </div>
                </div>
                <!--botones de enviar y retroceder-->
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                        </div>
                        @can('alumnos.inscripcion.store')
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary" >Guardar</button>
                            </div>
                        @endcan
                    </div>
                </div>
                <input type="hidden" name="alumno_id" id="alumno_id" value="{{ $Alumno->id }}">
            </form>
            <!--display modal-->
            <div class="modal">
                <div class="center">
                    <img alt="" src="{{URL::asset('/img/cargando.gif')}}" />
                </div>
            </div>
    </div>
@endsection
@section('script_content_js')
<script type="text/javascript">
    $(function(){
        /***
         * validacion SID registro
        */
        $('#form_sid_registro').validate({
            rules: {
                especialidad_sid: {
                    required: true,
                },
                cursos_sid: {
                    required: true,
                },
                grupo: {
                    required: true
                },
                tipo_curso: {
                    required: true
                },
                horario: {
                    required: true
                },
                cerrs: {
                    required: true
                },
                tblubicacion: {
                    required: true
                },
                tblunidades: {
                    required: true
                }
            },
            messages: {
                especialidad_sid: {
                    required: "Por favor, Seleccione la especialidad"
                },
                cursos_sid: {
                    required: "Por favor, Seleccione el curso"
                },
                grupo: {
                    required: "Agregar el grupo"
                },
                tipo_curso: {
                    required: "Por favor, Seleccione tipo de curso"
                },
                horario: {
                    required: "Agregar Horario"
                },
                cerrs: {
                    required: "Por favor, Seleccione una opción"
                },
                tblubicacion: {
                    required: "Por favor, Seleccione una opción"
                },
                tblunidades: {
                    required: "Por favor, Seleccione una opción"
                }
            }
        });

        /**
        * cambios select dependientes de tbl_unidades
        */
        $('#tblubicacion').on("change", () => {
            $("#tblubicacion option:selected").each( () => {
                var ubicacion = $('#tblubicacion').val();
                var url = '/unidades/unidades_by_ubicacion/'+ ubicacion;

                $.ajax
                ({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function(){
                        $(".modal").show();
                    },
                    success: function(respuesta)
                    {
                        if (respuesta.length < 1) {
                            $("#tblunidades").empty();
                            $("#tblunidades").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#tblunidades").empty();
                                $("#tblunidades").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#tblunidades').append('<option value="' + v.unidad + '">' + v.unidad + '</option>');
                                });
                                $("#tblunidades").focus();
                            }else{

                                //Puedes mostrar un mensaje de error en algún div del DOM
                            }
                        }
                    },
                    complete:function(data){
                        // escondemos el modal
                        $(".modal").hide();
                    },
                    error: function(jqXHR, textStatus){
                        alert( "Hubo un error: " + textStatus );
                    }
                });
            });
        });

        /***
         * escuchará los cambios del select de especialidades y enviará una petición Ajax para buscar
         * los cursos de esa especialidad
        */
        $('#especialidad_sid' && '#tipo_curso').on("change", () => {

            $("#especialidad_sid option:selected").each( () => {
                var IdEsp = $('#especialidad_sid').val();
                var tipo = $('#tipo_curso').val();
                var unidad = $('#tblunidades').val();
                var datos = { idEsp: IdEsp, tipo: tipo, unidad: unidad};
                var url = "{{route('alumnos.sid.cursos')}}";

                var solicitud = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json',
                    beforeSend: function(){
                        $(".modal").show();
                    },
                    success: function(response){
                        /*
                            *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                            *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                            *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                            *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
                        */
                        if (response.length < 1) {
                            $("#cursos_sid").empty();
                            $("#cursos_sid").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!response.hasOwnProperty('error')){
                                $("#cursos_sid").empty();
                                $("#cursos_sid").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(response, (k ,v) => {
                                    $('#cursos_sid').append('<option value="' + v.id + '">' + v.nombre_curso + '</option>');
                                });

                                $("#cursos_sid").focus();
                            }
                        }
                    },
                    complete:function(data){
                        // escondemos el modal
                        $(".modal").hide();
                    },
                    error: function(jqXHR, textStatus){
                        jsonValue = jQuery.parseJSON( jqXHR.responseText );
                        console.log(jqXHR.status);
                        alert( "Hubo un error: " + jsonValue );
                    }
                });

                $.when(solicitud).then(function(data, textStatus, jqXHR ){
                    if (jqXHR.status === 200) {
                        $(".modal").hide();
                    }
                });
            });
        });
    });
</script>
@endsection
