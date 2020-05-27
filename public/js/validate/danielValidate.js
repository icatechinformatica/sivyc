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
    });

    $("#fecha_validacion").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
    });

    $('#fecha_actualizacion').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy'
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

    $.validator.addMethod("phoneMX", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
        phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})?[2-9]\d{2}?\d{4}$/);
    }, "Por favor especifique un número valido de teléfono");

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
            amaternoaspirante: {
                required: true
            },
            curpaspirante: {
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
                phoneMX: true
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
            amaternoaspirante: {
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

    /**
     * validación nueva del SID
     */
    $('#form_sid').validate({
        rules: {
            nombre: {
                required: true,
                minlength: 3
            },
            apellidoPaterno: {
                required: true,
                minlength: 3
            },
            apellidoMaterno: {
                required: true,
                minlength: 3
            },
            sexo: {
                required: true
            },
            curp: {
                required: true,
                CURP: true
            },
            fecha_nacimiento: {
                required: true,
                //date: true
            },
            telefono: {
                required: true,
                //phoneMX: true
            },
            domicilio: {
                required: true
            },
            colonia: {
                required: true
            },
            cp: {
                required: true,
                number: true
            },
            estado: {
                required: true
            },
            municipio: {
                required: true
            },
            estado_civil: {
                required: true
            },
            discapacidad: {
                required: true
            }
        },
        messages: {
            nombre: {
                required: 'Por favor ingrese su nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apellidoPaterno: {
                required: 'Por favor ingrese su apellido'
            },
            apellidoMaterno: {
                required: 'Por favor ingrese su apellido'
            },
            sexo: {
                required: 'Por favor Elegir su genero'
            },
            curp: {
                required: 'Por favor Ingresé la curp',
            },
            fecha_nacimiento: {
                required: 'Por favor, seleccione fecha',
                //date: 'Formato de fecha no valido'
            },
            telefono: {
                required: 'Por favor, ingrese telefóno',
            },
            domicilio: {
                required: 'Por favor, ingrese su domicilio'
            },
            colonia: {
                required: 'Por favor, ingrese la colonia'
            },
            cp: {
                required: 'Por favor, ingrese el código postal',
                number: 'Acepta sólo números'
            },
            estado: {
                required: 'Por favor, seleccione un estado'
            },
            municipio: {
                required: 'Por favor, seleccione el municipio'
            },
            estado_civil: {
                required: 'Por favor, seleccione su estado civil'
            },
            discapacidad: {
                required: 'Por favor seleccione una opción'
            }
        }
    });

    /***
     * validacion SID registro
     */
    $('#form_sid_registro').validate({
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
                extension: "pdf",
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
            especialidad_sid: {
                required: true,
            },
            cursos_sid: {
                required: true,
            }
        },
        messages: {
            especialidad_sid: {
                required: "Por favor, Seleccione la especialidad"
            },
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
                extension: "Sólo se permiten pdf",
                filesize:"El archivo debe ser menor de 2 MB",
            },
            ine: {
                extension: "Sólo se permiten pdf",
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
            cursos_sid: {
                required: "Por favor, Seleccione el curso"
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


    // escuchará los cambios del select de especialidades y enviará una petición Ajax para buscar los cursos de esa especialidad
    $('#especialidad_sid').on("change", () => {

        $("#especialidad_sid option:selected").each( () => {
            var IdEsp = $('#especialidad_sid').val();
            var datos = { idEsp: IdEsp };
            var url = '/alumnos/sid/cursos';

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
                    $("#cursos_sid").empty();
                    $("#cursos_sid").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                } else {
                    if(!respuesta.hasOwnProperty('error')){
                        $("#cursos_sid").empty();
                        $("#cursos_sid").append('<option value="" selected="selected">--SELECCIONAR--</option>');
                        $.each(respuesta, (k, v) => {
                            $('#cursos_sid').append('<option value="' + v.id + '">' + v.nombre_curso + '</option>');
                        });
                        $("#cursos_sid").focus();
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
                    $('#medio_entero_especificar').rules('add', {required: true});
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
                    $('#sistema_capacitacion_especificar').rules('add', {required: true});
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
            especialidad: {
                required: true
            },
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
            }
        },
        messages: {
            especialidad: {
                required: "Por favor, Seleccione la especialidad"
            },
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
});
