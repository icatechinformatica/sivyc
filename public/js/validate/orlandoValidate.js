// Creado por Orlando Chavez
$(function(){
    // ---- tablas de consultas ----
        $('#table-perfprof').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            responsive: true
        });

        $('#table-folios').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
            responsive: true
        });
    // ---- END tablas de consultas

    $.validator.addMethod("CURP", function (value, element) {
        if (value !== '') {
            var patt = new RegExp("^[A-Z][A,E,I,O,U,X][A-Z]{2}[0-9]{2}[0-1][0-9][0-3][0-9][M,H][A-Z]{2}[B,C,D,F,G,H,J,K,L,M,N,Ñ,P,Q,R,S,T,V,W,X,Y,Z]{3}[0-9,A-Z][0-9]$");
            return patt.test(value);
        } else {
            return false;
        }
    }, "Ingrese una CURP valida");

    $.validator.addMethod("RFC", function (value, element) {
        if (value !== '') {
            var patt = new RegExp("^[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?$");
            return patt.test(value);
        } else {
            return false;
        }
    }, "Ingrese un RFC valido");

    $.validator.addMethod("valueNotEquals", function(value, element){
        return 'sin especificar' !== value;
       }, "Value must not equal arg.");

    $('#registerinstructor').validate({
        rules: {
                nombre:{
                    required: true,
                    minlength: 3
                    },
                apellido_paterno:{
                    required: true,
                    minlength: 3
                    },
                curp:{
                    required:true,
                }
            },
        messages: {
            nombre: {
                required: 'Por favor ingrese el nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apellido_paterno: {
                required: 'Por favor ingrese el apellido paterno'
            },
            curp:{
                required: 'Por favor ingrese la CURP'
            }
        }
    });

       //Valida Instructor
    $('#validadoinstructor').validate({
        rules: {
            curp: {
                required: true,
                CURP: true
            },
            rfc:{
                required: true,
                RFC: true
            },
            folio_ine:{
                required: true,
            },
            sexo:{
                required: true,
                valueNotEquals: "default"
            },
            estado_civil:{
                required: true,
                valueNotEquals: "default"
            },
            fecha_nacimiento:{
                required: true,
                date: true
            },
            entidad:{
                required: true
            },
            municipio:{
                required: true
            },
            asentamiento:{
                required: true
            },
            domicilio:{
                required: true
            },
            telefono:{
                required: true,
                digits: true
            },
            correo:{
                required: true,
                email: true
            },
            banco:{
                required: true
            },
            clabe:{
                required: true,
                digits: true
            },
            numero_cuenta:{
                required: true,
                digits: true
            },
            cv:{
                required: true,
                extension: "pdf"
            },
            honorario:{
                required: true,
                valueNotEquals: "default"
            },
            registro_agente:{
                required: true
            },
            uncap_validacion:{
                required: true
            },
            memo_validacion:{
                required: true
            },
            fecha_validacion:{
                required: true,
                date: true
            },
            undidad_registra:{
                required: true,
                valueNotEquals: "default"
            }
        },
        messages: {
            curp: {
                required: 'Por favor Ingresé la CURP',
                CURP: "Por favor ingrese una CURP valida"
            },
            rfc: {
                required: 'Por favor Ingresé RFC',
                RFC: "Por favor ingrese RFC valida"
            },
            folio_ine: {
                required: 'Por favor Ingresé el folio de INE',
            },
            sexo:{
                required: 'Por favor ingrese el sexo',
                valueNotEquals: "Por favor seleccione el sexo"
            },
            estado_civil:{
                required: 'Por favor ingrese el estado civil',
                valueNotEquals: 'Por Favor ingrese el estado civil'
            },
            fecha_nacimiento: {
                required: 'Por favor ingrese la fecha de nacimiento',
                date: 'Formato de fecha no valido'
            },
            entidad: {
                required: 'Por favor ingrese la entidad',
            },
            municipio: {
                required: 'Por favor ingrese el municipio',
            },
            asentamiento: {
                required: 'Por favor ingrese el asentamiento',
            },
            domicilio: {
                required: 'Por favor ingrese el domicilio'
            },
            telefono: {
                required: 'Por favor ingrese el número de telefono',
                digits: 'Sólo se acceptan números'
            },
            correo:{
                required: 'Por favor ingrese un correo electronico',
                email: 'por favor ingrese un correo electronico valido'
            },
            banco: {
                required: 'Por favor ingrese el nombre del banco'
            },
            clabe: {
                required: 'Por favor ingrese la clabe interbancaria',
                digits: 'Sólo se aceptan números'
            },
            numero_cuenta:{
                required: 'Por favor ingrese el número de cuenta',
                digits: 'Sólo se aceptan números'
            },
            cv: {
                required: 'Por favor ingrese el archivo del curriculum',
                extension: 'Por favor ingrese el archivo en formato DF'
            },
           honorario: {
                required: 'Por favor ingrese el tipo de honorario',
                valueNotEquals: 'Por Favor ingrese el tipo de honorario'
            },
            registro_agente: {
                required: 'Por favor ingrese el registro de agente externo'
            },
            uncap_validacion: {
                required: 'Por favor ingrese la unidad de capacitacion'
            },
            memo_validacion: {
                required: 'Por favor ingrese el memorandum de validacion'
            },
            fecha_validacion: {
                required: "Por Favor ingrese la fecha de validacion",
                date: "Por favor ingrese la fecha correcta"
            },
            unidad_registra: {
                required: 'Por favor ingrese la unidad donde se esta registrando al instructor',
                valueNotEquals: 'Por Favor ingrese la unidad donde se esta registrando al instructor'
            }
        }
    });

        //Valida perfil_profesional
    $('#registerperf_prof').validate({
        rules: {
            grado_prof:{
                required: true,
            },
            area_carrera:{
                required: true,
            },
            estatus:{
                required: true,
                valueNotEquals: "default"
            },
            institucion_pais:{
                required: true,
            },
            institucion_entidad:{
                required: true,
            },
            institucion_ciudad:{
                required: true,
            },
            institucion_nombre:{
                required: true,
            },
            fecha_documento:{
                required: true,
                date: true
            },
            folio_documento:{
                required: true,
            },
            cursos_recibidos:{
                required: true,
                valueNotEquals: "sin especificar"
            },
            conocer:{
                required: true,
                valueNotEquals: "sin especificar"
            },
            stps:{
                required: true,
                valueNotEquals: "sin especificar"
            },
            capacitador_icatech:{
                required: true,
                valueNotEquals: "sin especificar"
            },
            cursos_icatech:{
                required: true,
                valueNotEquals: "default"
            },
            cursos_impartidos:{
                required: true,
                valueNotEquals: "sin especificar"
            },
            exp_lab:{
                required: true,
            },
            exp_doc:{
                required: true,
            }
        },
        messages: {
            grado_prof:{
                required: "Por favor Ingrese el Grado Profesional"
            },
            area_carrera:{
                required: "Por favor Ingrese el Area de la Carrera"
            },
            estatus:{
                required: "Por favor Ingrese el Estatus",
                valueNotEquals: "Por favor Ingrese el Estatus"
            },
            institucion_pais:{
                required: "Por favor Ingrese el Pais"
            },
            institucion_entidad:{
                required: "Por favor Ingrese la Entidad"
            },
            institucion_ciudad:{
                required: "Por favor Ingrese la Ciudad"
            },
            institucion_nombre:{
                required: "Por favor Ingrese el Nombre de la institucion"
            },
            fecha_documento:{
                required: "Por favor Ingrese la Fecha",
                date: "Por favor Ingrese la Fecha"
            },
            folio_documento:{
                required: "Por favor Ingrese el Folio"
            },
            cursos_recibidos:{
                required: "Por favor Ingrese una respuesta",
                valueNotEquals: "Por favor Ingrese una respuesta"
            },
            conocer:{
                required: "Por favor Ingrese una respuesta",
                valueNotEquals: "Por favor Ingrese una respuesta"
            },
            stps:{
                required: "Por favor Ingrese una respuesta",
                valueNotEquals: "Por favor Ingrese una respuesta"
            },
            capacitador_icatech:{
                required: "Por favor Ingrese una respuesta",
                valueNotEquals: "Por favor Ingrese una respuesta"
            },
            cursos_icatech:{
                required: "Por favor Ingrese una respuesta",
                valueNotEquals: "Por favor Ingrese una respuesta"
            },
            cursos_impartidos:{
                required: "Por favor Ingrese una respuesta",
                valueNotEquals: "Por favor Ingrese una respuesta"
            },
            exp_lab:{
                required: "Por favor Ingrese Experiencia Laboral"
            },
            exp_doc:{
                required: "Por favor Ingrese Experiencia Docente"
            }
        }
    });

    //Valida Pago
    $('#registerpago').validate({
        rules: {
            numero_contrato:{
                required: true
            },
            numero_pago:{
                required: true,
                digits: true
            },
            fecha_pago:{
                required: true,
                date: true
            },
            concepto:{
                required: true
            },
            nombre_solicita:{
                required: true
            },
            nombre_autoriza:{
                required: true
            },
            reacd02:{
                required: true,
                extension: 'pdf'
            }
        },
        messages: {
            numero_contrato: {
                required: 'Por favor ingrese el numero de contrato'
            },
            numero_pago: {
                required: 'Por favor ingrese el numero de pago',
                digits: 'Solo se aceptan numeros'
            },
            fecha_pago: {
                required: 'Por favor ingrese la fecha de pago',
                date: 'Ingrese la fecha correctamente'
            },
            concepto: {
                required: 'Por favor ingrese el concepto de pago'
            },
            nombre_solicita: {
                required: 'Por favor ingrese el nombre del solicitante'
            },
            nombre_autoriza: {
                required: 'Por favor ingrese el nombre del autorizante'
            },
            reacd02: {
                required: 'Por favor ingrese el documento REACD02',
                extension: 'Por fabvr ingrese el archivo con extension PDF'
            }
        }
    });

    //Valida parte de delegacionadmin
    $('#regsupre').validate({
        rules: {
            memorandum:{
                required: true
            },
            fecha:{
                required: true,
                date: true
            },
            unidad:{
                required: true,
                valueNotEquals: "default"
            },
            destino_puesto:{
                required: true
            },
            remitente_puesto:{
                required: true
            },
            puesto_valida:{
                required: true
            },
            puesto_elabora:{
                required: true
            },
            puesto_ccp1:{
                required: true
            },
            puesto_ccp2:{
                required: true
            },
        },
        messages: {
            memorandum:{
                required: "Por favor ingrese el Memorandum"
            },
            fecha:{
                required: "Por favor ingrese la fecha",
                date: "Por favor ingrese la fecha correcta"
            },
            unidad:{
                required: "Por favor ingrese la unidad",
                valueNotEquals: "Por favor seleccione una unidad"
            },
            destino_puesto:{
                required: "Por favor ingrese el destinatario"
            },
            remitente_puesto:{
                required: "Por favor ingrese el remitente"
            },
            puesto_valida:{
                required: "Por favor ingrese quien valida"
            },
            puesto_elabora:{
                required: "Por favor ingrese quien elabora"
            },
            puesto_ccp1:{
                required: "Por favor ingrese el CCP"
            },
            puesto_ccp2:{
                required: "Por favor ingrese el CCP"
            },
        }
    });

    $('#registersupre').validate({
        rules: {
            memorandum:{
                required: true
            },
            fecha:{
                required: true,
                date: true
            },
        },
        messages: {
            memorandum:{
                required: "Por favor ingrese el Memorandum"
            },
            fecha:{
                required: "Por favor ingrese la fecha",
                date: "Por favor ingrese la fecha correcta"
            },
        }
    });

    //Rechazo de supre
    $('#rechazosupre').validate({
        rules: {
            comentario_rechazo:{
                required: true
            }
        },
        messages: {
            comentario_rechazo: {
                required: 'Por favor ingrese el motivo de rechazo'
            }
        }
    });

    //Aceptación de supre
    $('#validadosupre').validate({
        rules: {
            folio_validacion:{
                required: true
            },
            fecha_validacion:{
                required: true,
                date: true
            },
            puesto_firmante:{
                required: true
            },
            ccpa1:{
                required: true
            },
            ccpa2:{
                required: true
            },
            ccpa3:{
                required: true
            },
            ccpa4:{
                required: true
            }
        },
        messages: {
            folio_validacion: {
                required: 'Por favor ingrese el folio de validación',
                date: 'Ingrese la fecha correcta'
            },
            fecha_validacion: {
                required: 'Por favor ingrese la fecha de validación'
            },
            puesto_firmante: {
                required: 'Por favor ingrese el nombre del firmante'
            },
            ccpa1: {
                required: 'Por favor ingrese el nombre correctamente'
            },
            ccpa2: {
                required: 'Por favor ingrese el nombre correctamente'
            },
            ccpa3: {
                required: 'Por favor ingrese el nombre correctamente'
            },
            ccpa4: {
                required: 'Por favor ingrese el nombre correctamente'
            },
        }
    });

    //Contrato
    $('#registercontrato').validate({
        rules: {
            numero_contrato:{
                required: true
            },
            perfil_instructor:{
                required: true,
                valueNotEquals: "default"
            },
            cantidad_numero:{
                required: true
            },
            cantidad_letras:{
                required: true
            },
            lugar_expedicion:{
                required: true
            },
            fecha_firma:{
                required: true,
                date: true
            },
            nombre_director:{
                required: true
            },
            unidad_capacitacion:{
                required: true
            },
            puesto_testigo1:{
                required: true
            },
            puesto_testigo2:{
                required: true
            },
            puesto_testigo3:{
                required: true
            },
            factura:{
                extension: "pdf"
            }
        },
        messages: {
            numero_contrato: {
                required: 'Por favor ingrese el numero de contrato'
            },
            perfil_instructor: {
                required: 'Por favor seleccione un perfil profesional',
                valueNotEquals: 'Por favor seleccione un perfil profesional'
            },
            cantidad_numero: {
                required: 'Por favor ingrese la cantidad exacta',
            },
            cantidad_letras: {
                required: 'Por favor ingrese la cantidad exacte en letras'
            },
            lugar_expedicion: {
                required: 'Por favor ingrese el lugar de la firma'
            },
            fecha_firma: {
                required: 'Por favor ingrese la fecha de la firma',
                date: 'Por favor ingrese la fecha exacta'
            },
            nombre_director: {
                required: 'Por favor ingrese el nombre del director'
            },
            unidad_capacitacion: {
                required: 'Por favor ingrese la unidad de capacitación'
            },
            puesto_testigo1: {
                required: 'Por favor ingrese el nombre del testigo'
            },
            puesto_testigo2: {
                required: 'Por favor ingrese el nombre del testigo'
            },
            puesto_testigo3: {
                required: 'Por favor ingrese el nombre del testigo'
            },
            factura:{
                extension: "Por favor que sea extension PDF"
            }
        }
    });

    //Solicitud de Pago
    $('#register_solpa').validate({
        rules: {
            no_memo:{
                required: true
            },
            nombre_elabora:{
                required: true
            },
            destino_puesto:{
                required: true
            },
            arch_factura:{
                required: true,
                extension: "pdf"
            },
            liquido:{
                required: true,
            },
            arch_asistencia:{
                required: true,
                extension: "pdf"
            },
            arch_evidencia:{
                required: true,
                extension: "pdf"
            },
            ccpa1:{
                required: true
            },
            ccpa2:{
                required: true
            },
            ccpa3:{
                required: true
            }
        },
        messages: {
            no_memo: {
                required: 'Por favor ingrese el numero de memorandum'
            },
            nombre_elabora: {
                required: 'Por favor ingrese el nombre de quien elabora'
            },
            destino_puesto: {
                required: 'Por favor ingrese el nombre a quien va destinado'
            },
            arch_factura: {
                required: 'Por favor ingrese la factura',
                extension: "Por favor ingrese el documento con extension PDF"
            },
            liquido:{
                required: 'Por favor ingrese el liquido final reflejado en la factura',
            },
            arch_evidencia: {
                required: 'Por favor ingrese la evidencia',
                extension: "Por favor ingrese el documento con extension PDF"
            },
            arch_asistencia: {
                required: 'Por favor ingrese la asistencia',
                extension: "Por favor ingrese el documento con extension PDF"
            },
            ccpa1: {
                required: 'Por favor ingrese el nombre'
            },
            ccpa2: {
                required: 'Por favor ingrese el nombre'
            },
            ccpa3: {
                required: 'Por favor ingrese el nombre'
            }
        }
    });

    // Registro CERSS
    $('#registercerss').validate({
        rules: {
            municipio:{
                valueNotEquals: "sin especificar"
            },
            unidad:{
                valueNotEquals: "sin especificar"
            },
        },
        messages: {
            municipio:{
                valueNotEquals: "Seleccione una Opcion Valida"
            },
            unidad:{
                valueNotEquals: "Seleccione una Opcion Valida"
            },
        }
    });
});
