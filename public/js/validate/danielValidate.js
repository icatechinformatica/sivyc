$(function(){
    // metodos
    $('#table-one').filterTable('#myInput');
    $( "#fechanacaspirante" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    });

    $( "#fecha_nacimiento" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    }).attr('readonly', 'true').
    keypress(function(event){
      if(event.keyCode == 8){
        event.preventDefault();
      }
    });

    $("#fecha_validacion").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    }).attr('readonly', 'true').
      keypress(function(event){
        if(event.keyCode == 8){
            event.preventDefault();
        }
      });

    $('#fecha_actualizacion').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    }).attr('readonly', 'true').
      keypress(function(event){
        if(event.keyCode == 8){
            event.preventDefault();
        }
      });

    $('#fecha_curso').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    }).attr('readonly', 'true').
      keypress(function(event){
        if(event.keyCode == 8){
            event.preventDefault();
        }
      });

    $('input[type=text]').val(function () {
        return this.value.toUpperCase();
    })

    $.validator.addMethod("CURP", function (value, element) {
        if (value !== '') {
            var patt = new RegExp("^[A-Z][A,E,I,O,U,X][A-Z]{2}[0-9]{2}[0-1][0-9][0-3][0-9][M,H][A-Z]{2}[B,C,D,F,G,H,J,K,L,M,N,Ñ,P,Q,R,S,T,V,W,X,Y,Z]{3}[0-9,A-Z][0-9]$");
            return patt.test(value);
        } else {
            return false;
        }
    }, "Ingrese una CURP valida");



    $.validator.addMethod(
        "phoneMEXICO",
        function(value, element, regexp)
        {
            if (regexp.constructor != RegExp)
                regexp = new RegExp(regexp);
            else if (regexp.global)
                regexp.lastIndex = 0;
            return this.optional(element) || regexp.test(value);
        },
        "Especifique un número valido de teléfono"
    );

    $.validator.addMethod("filesize", (value, element, arg)=> {
        var minsize=1000; // representa a 1kb
        if((value>minsize)&&(value<=arg)){
            return true;
        }else{
            return false;
        }
    });

    $('#formsid').validate({
        rules: {
            nombreaspirante: {
                required: true,
                minlength: 3
            },
            apaternoaspirante: {
                required: true
            },
            apellido_pat: {
                required: true,
                CURP: true
            },
            generoaspirante: {
                required: true
            },
            fechanacaspirante: {
                required: true,
                date: true
            },
            telefonoaspirante: {
                required: true,
                //phoneMEXICO: /^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/
            },
            domicilioaspirante: {
                required: true
            },
            coloniaaspirante: {
                required: true
            },
            codigopostalaspirante: {
                required: true,
                number: true
            },
            estadoaspirante: {
                required: true
            },
            municipioaspirante: {
                required: true
            },
            estadocivil: {
                required: true
            },
            especialidadquedeseainscribirse: {
                required: true
            }
        },
        messages: {
            nombreaspirante: {
                required: 'Por favor ingrese su nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apaternoaspirante: {
                required: 'Por favor ingrese su apellido'
            },
            curpaspirante: {
                required: 'Por favor Ingresé la curp',
            },
            generoaspirante: {
                required: 'Por favor Elegir el genero'
            },
            telefonoaspirante: {
                required: 'Por favor, ingrese telefóno',
            },
            fechanacaspirante: {
                required: 'Por favor, seleccione fecha',
                date: 'Formato de fecha no valido'
            },
            domicilioaspirante: {
                required: 'Por favor, ingrese su domicilio'
            },
            coloniaaspirante: {
                required: 'Por favor, ingrese la colonia'
            },
            codigopostalaspirante: {
                required: 'Por favor, ingrese el código postal',
                number: 'Acepta sólo números'
            },
            estadoaspirante: {
                required: 'Por favor, seleccione un estado'
            },
            municipioaspirante: {
                required: 'Por favor, seleccione el municipio'
            },
            estadocivil: {
                required: 'Por favor, seleccione su estado civil'
            },
            especialidadquedeseainscribirse: {
                required: 'Por favor, seleccione la especialidad'
            }
        }
    });

    $('#sid_registro_modificacion_jefe_unidad').validate({
        rules: {
            nombre_alum_mod: {
                required: true,
                minlength: 3
            },
            apellido_pat_mod: {
                required: true,
                minlength: 2
            },
            sexo_mod: {
                required: true
            },
            curp_mod: {
                required: true,
                CURP: true
            },
            telefono_mod: {
                required: true,
            },
            estado_mod: {
                required: true
            },
            municipio_mod: {
                required: true
            },
            estado_civil_mod: {
                required: true
            },
            discapacidad_mod: {
                required: true
            },
            dia_mod: {
                required: true
            },
            mes_mod: {
                required: true
            },
            anio_mod: {
                required: true,
                maxlength: 4,
                number: true
            },
            medio_entero_mod: {
                required: true
            },
            motivos_eleccion_sistema_capacitacion_mod: {
                required: true
            },
            ultimo_grado_estudios_mod: {
                required: true
            }
        },
        messages: {
            nombre_alum_mod: {
                required: 'Por favor ingrese su nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apellido_pat_mod: {
                required: 'Por favor ingrese su apellido',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            sexo_mod: {
                required: 'Por favor Elegir su genero'
            },
            curp_mod: {
                required: 'Por favor Ingresé la curp',
            },
            telefono_mod: {
                required: 'Por favor, ingrese telefóno',
            },
            estado_mod: {
                required: 'Por favor, seleccione un estado'
            },
            municipio_mod: {
                required: 'Por favor, seleccione el municipio'
            },
            estado_civil_mod: {
                required: 'Por favor, seleccione su estado civil'
            },
            discapacidad_mod: {
                required: 'Por favor seleccione una opción'
            },
            ultimo_grado_estudios_mod: {
                required: "Agregar último grado de estudios"
            },
            dia_mod: {
                required: "Por favor, seleccione el día"
            },
            mes_mod: {
                required: "Por favor, seleccione el mes"
            },
            anio_mod: {
                required: "Por favor, Ingrese el año",
                maxlength: "Sólo acepta 4 digitos",
                number: "Sólo se aceptan números"
            },
            medio_entero_mod: {
                required: "Por favor, seleccione una opción"
            },
            motivos_eleccion_sistema_capacitacion_mod: {
                required: "Por favor, seleccione una opción"
            }
        }
    });

    /** validación de formulario modificacion normal */
    $('#sid_registro_modificacion').validate({
        rules: {
            nombre_alum_mod: {
                required: true,
                minlength: 3
            },
            apellido_pat_mod: {
                required: true,
                minlength: 2
            },
            sexo_mod: {
                required: true
            },
            telefono_mod: {
                required: true,
            },
            estado_mod: {
                required: true
            },
            municipio_mod: {
                required: true
            },
            estado_civil_mod: {
                required: true
            },
            discapacidad_mod: {
                required: true
            },
            dia_mod: {
                required: true
            },
            mes_mod: {
                required: true
            },
            anio_mod: {
                required: true,
                maxlength: 4,
                number: true
            },
            medio_entero_mod: {
                required: true
            },
            motivos_eleccion_sistema_capacitacion_mod: {
                required: true
            },
            ultimo_grado_estudios_mod: {
                required: true
            }
        },
        messages: {
            nombre_alum_mod: {
                required: 'Por favor ingrese su nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apellido_pat_mod: {
                required: 'Por favor ingrese su apellido',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            sexo_mod: {
                required: 'Por favor Elegir su genero'
            },
            telefono_mod: {
                required: 'Por favor, ingrese telefóno',
            },
            estado_mod: {
                required: 'Por favor, seleccione un estado'
            },
            municipio_mod: {
                required: 'Por favor, seleccione el municipio'
            },
            estado_civil_mod: {
                required: 'Por favor, seleccione su estado civil'
            },
            discapacidad_mod: {
                required: 'Por favor seleccione una opción'
            },
            ultimo_grado_estudios_mod: {
                required: "Agregar último grado de estudios"
            },
            dia_mod: {
                required: "Por favor, seleccione el día"
            },
            mes_mod: {
                required: "Por favor, seleccione el mes"
            },
            anio_mod: {
                required: "Por favor, Ingrese el año",
                maxlength: "Sólo acepta 4 digitos",
                number: "Sólo se aceptan números"
            },
            medio_entero_mod: {
                required: "Por favor, seleccione una opción"
            },
            motivos_eleccion_sistema_capacitacion_mod: {
                required: "Por favor, seleccione una opción"
            }
        }
    });

    /****
     *
     */
    $('#conveniosFrm').validate({
        rules: {
            no_convenio: {
                required: true
            },
            institucion: {
                required: true
            },
            telefono: {
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
            poblacion: {
                required: true
            },
            municipio: {
                required: true
            },
            nombre_titular: {
                required: true
            },
            nombre_enlace: {
                required: true
            },
            status: {
                required: true
            },
            direccion: {
                required: true
            },
            archivo_convenio: {
                required: true,
                extension: "pdf",
                filesize: 2000000   //max size 2mb
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
            nombre_titular: {
                required: "Campo requerido"
            },
            nombre_enlace: {
                required: "Campo requerido"
            },
            status: {
                required: "Campo requerido"
            },
            direccion: {
                required: "Campo requerido"
            },
            archivo_convenio: {
                extension: "Sólo se permiten pdf",
                filesize: "El archivo debe ser menor de 2 MB",
                required: "Documento requerido"
            }
        }

    });

    /**
     * form paso 2
     */
    $("#form-sid-paso2").validate({
        rules: {
            acta_nacimiento: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            },
            copia_curp: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            },
            comprobante_domicilio: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            },
            fotografias: {
                extension: "png|jpg|jpeg",
                filesize: 2000000   //max size 2mb
            },
            ine: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            },
            licencia_manejo: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            },
            grado_estudios: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            },
            documento_comprobante_migratorio: {
                extension: "pdf",
                filesize: 2000000   //max size 2mb
            }
        },
        messages: {
            acta_nacimiento: {
                extension : "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            copia_curp: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            comprobante_domicilio: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            fotografias: {
                extension: "formatos permitidos: png, jpg, jpeg",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            ine: {
                extension: "formatos permitidos: pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            licencia_manejo: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            grado_estudios: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            documento_comprobante_migratorio: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            }
        }
    });

    /**
     * validar formulario paso2UploadFiles
     */
    $("#paso2UploadFiles").validate({
        rules: {
            customFile: {
                extension: "pdf|png|jpg|jpeg",
                filesize: 2000000,   //max size 2mb
                required: true
            },
            tipoDocumento:{
                required: true
            }
        },
        messages: {
            customFile: {
                extension: "Sólo se permiten pdf, png, jpg, jpege",
                filesize:"El archivo debe ser menor de 2 MB",
                required: "Anexar el documento"
            },
            tipoDocumento: {
                required: 'Por favor, seleccione el tipo de documento',
            }
        }

    });

    

    // hacemos que los input sean mayusculos
    $('input[type=text]').val (function () {
        return this.value.toUpperCase();
    })

    $('textarea').val(function () {
        return this.value.toUpperCase();
    })


    

    $('#estado').on("change", () => {
        var IdEst =$('#estado').val();
        $("#estado option:selected").each( () => {
            var IdEst = $('#estado').val();
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
                    $("#municipio").empty();
                    $("#municipio").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                } else {
                    if(!respuesta.hasOwnProperty('error')){
                        $("#municipio").empty();
                        $("#municipio").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        $.each(respuesta, (k, v) => {
                            $('#municipio').append('<option value="' + v.muni + '">' + v.muni + '</option>');
                        });
                        $("#municipio").focus();
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

    /**
     * cambios select dependientes
     */
    $('#areaCursos').on("change", () => {
        $("#areaCursos option:selected").each( () => {
            var idArea = $('#areaCursos').val();
            var url = '/cursos/especialidad_by_area/'+ idArea;

            var request = $.ajax
            ({
                url: url,
                method: 'GET',
                dataType: 'json'
            });

            /*
                *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
            */

            request.done(( respuesta ) => {
                if (respuesta.length < 1) {
                    $("#especialidadCurso").empty();
                    $("#especialidadCurso").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                } else {
                    if(!respuesta.hasOwnProperty('error')){
                        $("#especialidadCurso").empty();
                        $("#especialidadCurso").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        $.each(respuesta, (k, v) => {
                            $('#especialidadCurso').append('<option value="' + v.id + '">' + v.nombre + '</option>');
                        });
                        $("#especialidadCurso").focus();
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
    // funcion para el cambio de estado de un selectBox
    $('#medio_entero').on("change", () => {
        $("#medio_entero option:selected").each( () => {
            var medioEntero = $('#medio_entero').val();
            if (!medioEntero) {
                $("#medio_entero_especificar").css("display", "none");
                $('#medio_entero_especificar').rules('remove', 'required');
                $('.medio_especificar').css("display", "none");
            } else {
                if (medioEntero == 0) {
                    $("#medio_entero_especificar").css("display", "block");
                    $('#medio_entero_especificar').rules('add', {required: true,
                        messages: {
                            required: "Campo Requerido"
                        }
                    });
                    $('.medio_especificar').css("display", "block");
                } else {
                    $("#medio_entero_especificar").css("display", "none");
                    $('#medio_entero_especificar').rules('remove', 'required');
                    $('.medio_especificar').css("display", "none");
                }
            }
        });
    });
    // funcion para cambio de estado en selectBox
    $('#motivos_eleccion_sistema_capacitacion').on("change", () => {
        $('#motivos_eleccion_sistema_capacitacion option:selected').each( () => {
            var motivoEleccion = $('#motivos_eleccion_sistema_capacitacion').val();
            if (!motivoEleccion) {
                $("#sistema_capacitacion_especificar").css("display", "none");
                $('#sistema_capacitacion_especificar').rules('remove', 'required');
                $('.capacitacion_especificar').css("display", "none");
            } else {
                if (motivoEleccion == 0) {
                    $("#sistema_capacitacion_especificar").css("display", "block");
                    $('#sistema_capacitacion_especificar').rules('add', {required: true,
                        messages: {
                            required: "Campo Requerido"
                        }
                    });
                    $('.capacitacion_especificar').css("display", "block");
                } else {
                    $("#sistema_capacitacion_especificar").css("display", "none");
                    $('#sistema_capacitacion_especificar').rules('remove', 'required');
                    $('.capacitacion_especificar').css("display", "none");
                }
            }
        });
    });

    $('#costo_curso').keyup(function(event) {

        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
          return value
          .replace(/(?!\.)\D/g, "")
          .replace(/(?<=\..*)\./g, "")
          .replace(/(?<=\.\d\d).*/g, "")
          .replace(/\B(?=(\d{3})+(?!\d))/g, "");
        });
    });

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
            }
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
                filesize:"El archivo debe ser menor de 2 MB",
            },
            documento_memo_actualizacion: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            documento_memo_validacion: {
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
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

    /***
     * modificaciones de funciones de flecha
    */
   var getU = (idCurso) => {
        $.ajax({
            type: 'GET',
            url: '/cursos/get_by_id/'+idCurso,
            data: idCurso, //datos a enviar al servidor
            dataType: 'json',
            success: (response) => {
                var contenidoModal = $("#contextoModalBody");
                var myModalLabel = $("#myModalLabel");
                /***
                 * modificación de una etiqueta
                 */
                myModalLabel.append(
                    response[0].nombre_curso
                );
                contenidoModal.append(
                    '<ul class="list-group z-depth-0">'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Categoria: </b> '+ response[0].categoria
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Clasificación: </b> '+ response[0].clasificacion
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Perfil: </b> '+ response[0].perfil
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Modalidad: </b> '+ response[0].modalidad
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Unidad Móvil: </b> '+ response[0].unidad_amovil
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Memo de Validación: </b> '+ response[0].memo_validacion
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> NIVEL DE ESTUDIOS: </b> ' + response[0].nivel_estudio
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> COSTO: </b> ' + response[0].costo
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> FECHA ACTUALIZACIÓN: </b> ' + response[0].fecha_actualizacion
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> FECHA VALIDACIÓN: </b> ' + response[0].fecha_validacion
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> PERFIL: </b> ' + response[0].perfil
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> ESPECIALIDAD: </b> ' + response[0].especialidad
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> Curso Tipo: </b> ' + response[0].tipo_curso
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> SOLICITUD AUTORIZACION: </b> ' + '<a href="'+response[0].documento_solicitud_autorizacion+'" target="_blank">DOCUMENTO</a>'
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> MEMO VALIDACIÓN: </b> ' + '<a href="'+response[0].documento_memo_validacion+'" target="_blank">DOCUMENTO</a>'
                  +   '</li>'
                  +   '<li class="list-group-item justify-content-between">'
                  +     '<b> MEMO ACTUALIZACIÓN: </b> ' + '<a href="'+response[0].documento_memo_actualizacion+'" target="_blank">DOCUMENTO</a>'
                  +   '</li>'
                  + '</ul>'
              );
            },
            error: () => {
                console.log("No se ha podido obtener la información")
            }
        });
    }

    /**
    *  modificación de modal bootsrap
     */
    $('#fullHeightModalRight').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        getU(id);
    });

    $('#fullHeightModalRight').on('hidden.bs.modal', function (e) {
        // delete div content
        var contenidoModal = $("#contextoModalBody");
        var myModalLabel = $("#myModalLabel");
        contenidoModal.empty();
        myModalLabel.empty();
    });

    $("#comprobante_migratorio").change(function() {
        if(this.checked) {
            // TO-DO
            $("#documento_comprobante_migratorio").prop('disabled', false);
        } else {
            $("#documento_comprobante_migratorio").prop('disabled', true);
            $("#documento_comprobante_migratorio").val('');
            $('#lbl_documento_comprobante_migratorio').html('COMPROBANTE MIGRATORIO');
            $('#documento_comprobante_migratorio').removeClass("{extension: 'pdf'}")
        }
    });

    /**
     * cambios select dependientes de tbl_unidades
     */
    $('#tblubicacion').on("change", () => {
        $("#tblubicacion option:selected").each( () => {
            var ubicacion = $('#tblubicacion').val();
            var url = '/unidades/unidades_by_ubicacion/'+ ubicacion;

            var request = $.ajax
            ({
                url: url,
                method: 'GET',
                dataType: 'json'
            });

            /*
                *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
            */

            request.done(( respuesta ) => {
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
            });

            request.fail(( jqXHR, textStatus ) =>
            {
                    alert( "Hubo un error: " + textStatus );
            });
        });
    });

    /**
     * Modificacion estado municipios
     */
    $('#estado_mod').on("change", () => {
        var IdEst =$('#estado_mod').val();
        $("#estado_mod option:selected").each( () => {
            var IdEst = $('#estado_mod').val();
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
                    $("#municipio_mod").empty();
                    $("#municipio_mod").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                } else {
                    if(!respuesta.hasOwnProperty('error')){
                        $("#municipio_mod").empty();
                        $("#municipio_mod").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        $.each(respuesta, (k, v) => {
                            $('#municipio_mod').append('<option value="' + v.muni + '">' + v.muni + '</option>');
                        });
                        $("#municipio_mod").focus();
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

    /**
     * Documentos
     *
     */
    $('#especialidad_sid_mod' && '#tipo_curso_mod').on("change", () => {

        $("#especialidad_sid_mod option:selected").each( () => {
            var IdEsp = $('#especialidad_sid_mod').val();
            var tipo = $('#tipo_curso_mod').val();
            var datos = { idEsp_mod: IdEsp, tipo_mod: tipo};
            var url = '/alumnos/sid/cursos_update';
            console.log(datos);

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
                console.log(respuesta);
                if (respuesta.length < 1) {
                    $("#curso_sid_mod").empty();
                    $("#curso_sid_mod").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                } else {
                    if(!respuesta.hasOwnProperty('error')){
                        $("#curso_sid_mod").empty();
                        $("#curso_sid_mod").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        $.each(respuesta, (k, v) => {
                            $('#curso_sid_mod').append('<option value="' + v.id + '">' + v.nombre_curso + '</option>');
                        });
                        $("#curso_sid_mod").focus();
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

    // disabled button while submit
    $("#paso2UploadFiles").submit(function (e) {

        $("#submitDocs").attr("disabled", true);

        return true;

    });


    /**
     * cambios select dependientes de tbl_unidades
     */
    $('#ubicaciones').on("change", () => {
        $("#ubicaciones option:selected").each( () => {
            var ubicacion = $('#ubicaciones').val();
            var url = '/unidades/unidad_by_ubicacion/'+ ubicacion;

            var request = $.ajax
            ({
                url: url,
                method: 'GET',
                dataType: 'json'
            });

            /*
                *Esta es una parte muy importante, aquí se  tratan los datos de la respuesta
                *se asume que se recibe un JSON correcto con dos claves: una llamada id_curso
                *y la otra llamada cursos, las cuales se presentarán como value y datos de cada option
                *del select PARA QUE ESTO FUNCIONE DEBE SER CAPAZ DE DEVOLVER UN JSON VÁLIDO
            */

            request.done(( respuesta ) => {
                if (respuesta.length < 1) {
                    $("#unidades_ubicacion").empty();
                    $("#unidades_ubicacion").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                } else {
                    if(!respuesta.hasOwnProperty('error')){
                        $("#unidades_ubicacion").empty();
                        $("#unidades_ubicacion").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        $.each(respuesta, (k, v) => {
                            $('#unidades_ubicacion').append('<option value="' + v.unidad + '">' + v.unidad + '</option>');
                        });
                        $("#unidades_ubicacion").focus();
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
