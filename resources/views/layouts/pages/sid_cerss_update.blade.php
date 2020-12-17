@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Solicitud de Inscripción CERRS | Sivyc Icatech')
<!--ÁREA EXTRA DONDE SE AGREGA CSS-->
@section('content_script_css')
    <style>
        .constancia_reclusion_tag{
            display: none;
        }
    </style>
@endsection
<!--ÁREA EXTRA DONDE SE AGREGA CSS ENDS-->
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        <div class="row">
            <div class="col-lg-8 margin-tb">
                <div>
                    <h3><b>Solicitud de Inscripción (SID) - CERSS</b></h3>
                </div>
            </div>
            <div class="col-lg-4 margin-tb">
                <div class="pull-right">
                    <a class="btn btn-warning btn-circle m-1 btn-circle-sm" href="#" data-toggle="modal" data-placement="top" title="INFORMACIÓN ACERCA DEL SID" data-target="#fullHeight">
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS GENERALES CERSS</b></h4>
        </div>
        <form method="POST" id="form_sid_cerss_update" action="{{ route('preinscripcion.cerss.modificar', ['idPreinscripcion' => base64_encode($idPrealumnoUpdate) ]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <!--NOMBRE CERSS-->
                <div class="form-group col-md-4">
                    <label for="nombre_cerss_update" class="control-label">NOMBRE DEL CERSS</label>
                    <input type="text" class="form-control" id="nombre_cerss_update" name="nombre_cerss_update" autocomplete="off" value="{{$alumnoPre_update->nombre_cerss}}">
                </div>
                <!--NOMBRE CERSS END-->
                <div class="form-group col-md-8">
                    <label for="direcciones_cerss_update_" class="control-label">DIRECCIÓN DEL CERSS</label>
                    <input type="text" class="form-control" id="direcciones_cerss_update_" name="direcciones_cerss_update_" autocomplete="off" value="{{$alumnoPre_update->direccion_cerss}}"/>
                </div>
            </div>
            <div class="form-row">
                <!--TITULAR DEL CERSS-->
                <div class="form-group col-md-8">
                    <label for="titular_cerss_update_ " class="control-label">TITULAR DEL CERSS</label>
                    <input type="text" class="form-control" id="titular_cerss_update_" name="titular_cerss_update_" autocomplete="off" value="{{$alumnoPre_update->titular_cerss}}"/>
                </div>
                <!--TITULAR DEL CERSS END-->
            </div>

            <!--PERSONALES-->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS PERSONALES CERSS</b></h4>
            </div>
            <!--PERSONALES END-->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numero_expediente_cerss_update" class="control-label">NÚMERO DE EXPEDIENTE</label>
                    <input type="text" class="form-control" id="numero_expediente_cerss_update" name="numero_expediente_cerss_update" autocomplete="off" value="{{$alumnoPre_update->numero_expediente}}"/>
                </div>
            </div>
            <div class="form-row">
                <!--nombre aspirante-->
                <div class="form-group col-md-4">
                    <label for="nombre_aspirante_cerss_update " class="control-label">NOMBRE</label>
                    <input type="text" class="form-control" id="nombre_aspirante_cerss_update" name="nombre_aspirante_cerss_update" autocomplete="off" value="{{$alumnoPre_update->nombre}}">
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-4">
                    <label for="apellidoPaterno_aspirante_cerss_update" class="control-label">APELLIDO PATERNO</label>
                    <input type="text" class="form-control" id="apellidoPaterno_aspirante_cerss_update" name="apellidoPaterno_aspirante_cerss_update" autocomplete="off" value="{{$alumnoPre_update->apellido_paterno}}">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-4">
                    <label for="apellidoMaterno_aspirante_cerss_update" class="control-label">APELLIDO MATERNO</label>
                    <input type="text" class="form-control" id="apellidoMaterno_aspirante_cerss_update" name="apellidoMaterno_aspirante_cerss_update" autocomplete="off" value="{{$alumnoPre_update->apellido_materno}}">
                </div>
                <!-- apellido materno END-->
            </div>
            <div class="form-row">
                <b><label for="fechanacimiento" class="control-label">FECHA DE NACIMIENTO</label></b>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="dia_cerss" class="control-label">DÍA</label>
                    <select class="form-control" id="dia_cerss" name="dia_cerss">
                        <option value="">--SELECCIONAR--</option>
                        @for ($i = 01; $i <= 31; $i++)
                            <option {{ ($dia_nac_cerss == $i) ? "selected" : ""  }}  value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="mes_cerss" class="control-label">MES</label>
                    <select class="form-control" id="mes_cerss" name="mes_cerss">
                        <option value="">--SELECCIONAR--</option>
                        <option {{ ($mes_nac_cerss == "01") ? "selected" : ""  }} value="01">ENERO</option>
                        <option {{ ($mes_nac_cerss == "02") ? "selected" : ""  }} value="02">FEBRERO</option>
                        <option {{ ($mes_nac_cerss == "03") ? "selected" : ""  }} value="03">MARZO</option>
                        <option {{ ($mes_nac_cerss == "04") ? "selected" : ""  }} value="04">ABRIL</option>
                        <option {{ ($mes_nac_cerss == "05") ? "selected" : ""  }} value="05">MAYO</option>
                        <option {{ ($mes_nac_cerss == "06") ? "selected" : ""  }} value="06">JUNIO</option>
                        <option {{ ($mes_nac_cerss == "07") ? "selected" : ""  }} value="07">JULIO</option>
                        <option {{ ($mes_nac_cerss == "08") ? "selected" : ""  }} value="08">AGOSTO</option>
                        <option {{ ($mes_nac_cerss == "09") ? "selected" : ""  }} value="09">SEPTIEMBRE</option>
                        <option {{ ($mes_nac_cerss == "10") ? "selected" : ""  }} value="10">OCTUBRE</option>
                        <option {{ ($mes_nac_cerss == "11") ? "selected" : ""  }} value="11">NOVIEMBRE</option>
                        <option {{ ($mes_nac_cerss == "12") ? "selected" : ""  }} value="12">DICIEMBRE</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="anio_cerss" class="control-label">AÑO</label>
                    <input type="text" class="form-control" id="anio_cerss" name="anio_cerss" placeholder="INGRESA EL AÑO EJ. 1943" value="{{ $anio_nac_cerss }}" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nacionalidad_cerss_update" class="control-label">NACIONALIDAD</label>
                    <input type="text" class="form-control" id="nacionalidad_cerss_update" name="nacionalidad_cerss_update" placeholder="NACIONALIDAD" autocomplete="off" value="{{$alumnoPre_update->nacionalidad}}">
                </div>
                <div class="form-group col-md-6">
                    <label for="genero_cerss_update" class="control-label">GENERO</label>
                    <select class="form-control" id="genero_cerss_update" name="genero_cerss_update">
                        <option value="">--SELECCIONAR--</option>
                        <option {{ trim($alumnoPre_update->sexo) == "FEMENINO" ? "selected" : ""  }} value="FEMENINO">MUJER</option>
                        <option {{ trim($alumnoPre_update->sexo) == "MASCULINO" ? "selected" : ""  }} value="MASCULINO">HOMBRE</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="cerss_estado_update" class="control-label">Estado</label>
                    <select class="form-control" id="cerss_estado_update" name="cerss_estado_update" required>
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($estados as $itemEstado)
                        <option {{ (trim($alumnoPre_update->estado) == trim($itemEstado->nombre)) ? "selected" : "" }} value="{{$itemEstado->id}}">{{ $itemEstado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="cerss_municipio_update" class="control-label">Municipio</label>
                    <select class="form-control" id="cerss_municipio_update" name="cerss_municipio_update">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $itemMunicipio)
                            <option {{ ($alumnoPre_update->municipio == $itemMunicipio->muni) ? "selected" : ""  }} value="{{$itemMunicipio->muni}}">{{ $itemMunicipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="curp_cerss_update" class="control-label">CURP ASPIRANTE</label>
                    <input type="text" class="form-control" id="curp_cerss_update" name="curp_cerss_update" placeholder="CURP" autocomplete="off" value="{{$alumnoPre_update->curp}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="rfc_cerss_update" class="control-label">RFC ASPIRANTE</label>
                    <input type="text" class="form-control" id="rfc_cerss_update" name="rfc_cerss_update" placeholder="RFC" autocomplete="off" value="{{$alumnoPre_update->rfc_cerss}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="discapacidad_cerss_update" class="control-label">Discapacidad que presenta</label>
                    <select class="form-control" id="discapacidad_cerss_update" name="discapacidad_cerss_update">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumnoPre_update->discapacidad == "VISUAL") ? "selected" : "" }} value="VISUAL">VISUAL</option>
                        <option {{( $alumnoPre_update->discapacidad == "AUDITIVA") ? "selected" : "" }} value="AUDITIVA">AUDITIVA</option>
                        <option {{( $alumnoPre_update->discapacidad == "DE COMUNICACIÓN") ? "selected" : "" }} value="DE COMUNICACIÓN">DE COMUNICACIÓN</option>
                        <option {{( $alumnoPre_update->discapacidad == "MOTRIZ") ? "selected" : "" }} value="MOTRIZ">MOTRIZ</option>
                        <option {{( $alumnoPre_update->discapacidad == "INTELECTUAL") ? "selected" : "" }} value="INTELECTUAL">INTELECTUAL</option>
                        <option {{( $alumnoPre_update->discapacidad == "NINGUNA") ? "selected" : "" }} value="NINGUNA">NINGUNA</option>
                    </select>
                </div>
            </div>
            <!---->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="ultimo_grado_estudios_cerss_update" class="control-label">ÚLTIMO GRADO DE ESTUDIOS</label>
                    <select class="form-control" id="ultimo_grado_estudios_cerss_update" name="ultimo_grado_estudios_cerss_update">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($grado_estudio_update as $itemGradoEstudio => $val)
                            <option {{( $alumnoPre_update->ultimo_grado_estudios == $val) ? "selected" : "" }} value="{{$val}}">{{$val}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <div class="custom-file">
                        <label for="file_upload " class="control-label">FICHA IDENTIFICACIÓN CERSS</label>
                        <input type="file" class="form-control" id="file_upload" name="file_upload">
                    </div>
                </div>
            </div>
            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{route('preinscripcion.cerss.show', ['id' => base64_encode($idPrealumnoUpdate)])}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="is_cerrs_update" id="is_cerrs_update" value="true">
        </form>
        <!-- Full Height Modal Right -->
            <div class="modal fade right" id="fullHeight" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                <!-- Add class .modal-full-height and then add class .modal-right (or other classes from list above) to set a position to the modal -->
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-warning" role="document">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title w-100" id="myModalLabel">INFORMACIÓN ACERCA DE...</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="text-justify">
                                    <p>
                                        LA INFORMACIÓN CONTENIDA EN ÉSTE APARTADO PERMITIRÁ AL OPERADOR DEL MÓDULO PODER CONOCER DE PRIMERA MANO
                                        LA INFORMACIÓN Y LOS CAMPOS QUE SON PUNTUALMENTE REQUERIDOS PARA OPTIMIZAR LA CARGA DE INFORMACIÓN PARA EL PROCESO DE
                                        CAPTURA DE LOS ALUMNOS DE DIFERENTES CURSOS QUE OTORGA EL INSTITUTO.
                                        <br>A CONTNUACIÓN SE ENLISTA LOS SIGUIENTES CAMPOS:

                                        <ul class="list-group z-depth-0">
                                            <li class="list-group-item justify-content-between">
                                                <b> NOMBRE DEL CERSS - NOMBRE DEL ASPIRANTE </b>
                                            </li>
                                            <li class="list-group-item justify-content-between">
                                                <b> DÍA - MES - AÑO </b>
                                            </li>
                                            <li class="list-group-item justify-content-between">
                                                <b> GENERO - ESTADO CIVIL</b>
                                            </li>
                                            <li class="list-group-item justify-content-between">
                                                <b> CURP - FICHA IDENTIFICACIÓN CERSS</b>
                                            </li>
                                            <li class="list-group-item justify-content-between">
                                                <b> ESTADO - MUNICIPIO</b>
                                            </li>
                                            <li class="list-group-item justify-content-between">
                                                <b> EXPEDIENTE </b>
                                            </li>
                                        </ul>
                                    </p>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                            </div>
                        </div>

                    </div>
            </div>
        <!-- Full Height Modal Right -->
    </div>
@endsection
@section('script_content_js')
    <script type="text/javascript">
        $(function(){

            $.validator.addMethod("CURP_CERSS", function (value, element) {
                if (value !== '') {
                    var patt = new RegExp("^[A-Z][A,E,I,O,U,X][A-Z]{2}[0-9]{2}[0-1][0-9][0-3][0-9][M,H][A-Z]{2}[B,C,D,F,G,H,J,K,L,M,N,Ñ,P,Q,R,S,T,V,W,X,Y,Z]{3}[0-9,A-Z][0-9]$");
                    return patt.test(value);
                } else {
                    return false;
                }
            }, "Ingrese una CURP valida");

            $.validator.addMethod("RFC_CERSS", function (value, element) {
                if (value !== '') {
                    var patt = new RegExp("^[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?$");
                    return patt.test(value);
                } else {
                    return false;
                }
            }, "Ingrese un RFC valido");

            $("#curp_cerss_update").keyup(function(){
               if($(this).val().length > 0)
               {
                    $(this).addClass('CURP_CERSS');
               } else {
                    $(this).removeClass('CURP_CERSS');
               }
            });

            $('#rfc_cerss_update').keyup(function(){
               if($(this).val().length > 0)
               {
                    $(this).addClass('RFC_CERSS');
               } else {
                    $(this).removeClass('RFC_CERSS');
               }
            });

            /****
            * sólo acepta números en el texbox
            */
            $('#numero_expediente_cerss_update').keypress(function (e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    //display error message
                    return false;
                }
            });
            /**
            * Validar el tamaño del archivo en 2 MB
            */
            $.validator.addMethod('filesize', function (value, element, param) {
                return this.optional(element) || (element.files[0].size <= param * 1000000)
            }, 'El tamaño del archivo debe ser menor a {0} MB');

            /****
            * sólo acepta números en el texbox
            */
            $('#anio_cerss').keypress(function (e) {
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    //display error message
                    return false;
                }
            });

            /**
            * validación nueva del SID
            */
            $('#form_sid_cerss_update').validate({
                rules: {
                    nombre_cerss: {
                        required: true,
                        minlength: 2
                    },
                    nombre_aspirante_cerss_update: {
                        required: true,
                        minlength: 2
                    },
                    genero_cerss_update: {
                        required: true
                    },
                    anio_cerss: {
                        maxlength: 4,
                        number: true
                    },
                    file_upload: {
                        extension: "pdf",
                        filesize: 2 //max size 2mb
                    },
                    numero_expediente_cerss_update: {
                        required: true
                    }
                },
                messages: {
                    nombre_cerss: {
                        required: 'Por favor ingrese el nombre del cerss',
                        minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
                    },
                    nombre_aspirante_cerss_update: {
                        required: 'Por favor ingrese su nombre',
                        minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
                    },
                    genero_cerss_update: {
                        required: 'Por favor Elegir su genero'
                    },
                    dia_cerss: {
                        required: "Por favor, seleccione el día"
                    },
                    mes_cerss: {
                        required: "Por favor, seleccione el mes"
                    },
                    anio_cerss: {
                        required: "Por favor, Ingrese el año",
                        maxlength: "Sólo acepta 4 digitos",
                        number: "Sólo se aceptan números"
                    },
                    medio_entero: {
                        required: "Por favor, seleccione una opción"
                    },
                    motivos_eleccion_sistema_capacitacion: {
                        required: "Por favor, seleccione una opción"
                    },
                    file_upload: {
                        extension: "Sólo se permiten pdf",
                    },
                    numero_expediente_cerss_update: {
                        required: "Por favor, Ingrese el número de expediente",
                    }
                }
            });

            $('#cerss_estado_update').on("change", () => {
                var IdEst =$('#cerss_estado_update').val();
                $("#cerss_estado_update option:selected").each( () => {
                    var IdEst = $('#cerss_estado_update').val();
                    var datos = {idEst: IdEst};
                    var url = '/alumnos/sid/municipios';

                    var request = $.ajax
                    ({
                        url: url,
                        method: 'POST',
                        data: datos,
                        dataType: 'json'
                    });

                    /*
                        *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                        *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                        *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                        *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
                    */


                    request.done(( respuesta ) =>
                    {
                        if (respuesta.length < 1) {
                            $("#cerss_municipio_update").empty();
                            $("#cerss_municipio_update").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        } else {
                            if(!respuesta.hasOwnProperty('error')){
                                $("#cerss_municipio_update").empty();
                                $("#cerss_municipio_update").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                                $.each(respuesta, (k, v) => {
                                    $('#cerss_municipio_update').append('<option value="' + v.muni + '">' + v.muni + '</option>');
                                });
                                $("#cerss_municipio_update").focus();
                            }else{

                                //Puedes mostrar un mensaje de error en algún div del DOM
                            }
                        }
                    });
                    request.fail(( jqXHR, textStatus ) =>
                    {
                        alert( "Hubo un error: " + textStatus );
                    });
                });
            });

        });
    </script>
@endsection
