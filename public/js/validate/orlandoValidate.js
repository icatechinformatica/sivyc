// Creado por Orlando Chavez
$(function(){
    // ---- tablas de consultas ----
        $('#table-perfprof').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            }
        });
        $('#table-instructor').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            }
        });
        $('#table-folios').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            }
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

       //Valida Instructor
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
            apellido_materno:{
                required: true,
                minlength: 3
            },
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
            tipo_honorario:{
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
        },
        messages: {
            nombre: {
                required: 'Por favor ingrese el nombre',
                minlength: jQuery.validator.format("Por favor, al menos {0} caracteres son necesarios")
            },
            apellido_paterno: {
                required: 'Por favor ingrese el apellido paterno'
            },
            apellido_materno: {
                required: 'Por favor ingrese su apellido materno'
            },
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
           tipo_honorario: {
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

    $('#rechazosupre').validate({
        rules: {
            comentario_rechazo:{
                required: true
            }
        },
        messages: {
            numero_contrato: {
                required: 'Por favor ingrese el motivo de rechazo'
            }
        }
    });

    $('#validadosupre').validate({
        rules: {
            folio_validacion:{
                required: true
            },
            fecha_validacion:{
                required: true,
                date: true
            },
            nombre_firmante:{
                required: true
            },
            ccp1:{
                required: true
            },
            ccp2:{
                required: true
            },
            ccp3:{
                required: true
            },
            ccp4:{
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
            nombre_firmante: {
                required: 'Por favor ingrese el nombre del firmante'
            },
            ccp1: {
                required: 'Por favor ingrese el nombre correctamente'
            },
            ccp2: {
                required: 'Por favor ingrese el nombre correctamente'
            },
            ccp3: {
                required: 'Por favor ingrese el nombre correctamente'
            },
            ccp4: {
                required: 'Por favor ingrese el nombre correctamente'
            },
        }
    });

});
