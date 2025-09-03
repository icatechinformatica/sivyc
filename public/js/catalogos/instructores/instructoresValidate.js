$(document).ready(function () {
    $('#reginstructor').validate({
        rules: {
            nombre: {
                required: true,
                minlength: 3,
                maxlength: 40,
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/
            },
            apellido_paterno: {
                required: true,
                minlength: 3,
                maxlength: 30,
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/
            },
            apellido_materno: {
                required: true,
                minlength: 3,
                maxlength: 30,
                pattern: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/
            },
            curp: {
                required: true,
                rangelength: [18, 18],
                pattern: /^[A-Z0-9]+$/
                // minlength: 18
            },
            rfc: {
                required: true,
                rangelength: [13, 13],
                pattern: /^[A-Z0-9]+$/
                // minlength: 13
            },
            honorario: {
                required: true,
            },
            tipo_instructor: {
                required: true,
            },
            tipo_identificacion: {
                required: true,
            },
            folio_ine: {
                required: true,
                maxlength: 40,
                pattern: /^[A-Za-z0-9\-]+$/
            },
            expiracion_identificacion: {
                required: true,
            },
            sexo: {
                required: true,
            },
            estado_civil: {
                required: true,
            },
            fecha_nacimientoins: {
                required: true,
            },
            telefono: {
                required: true,
                rangelength: [10, 10],
                digits: true,
            },
            telefono_casa: {
                rangelength: [10, 10],
                digits: true,
            },
            correo: {
                required: true,
                email: true,
                maxlength: 70
            },
            entidad: {
                required: true,
            },
            municipio: {
                required: true,
            },
            domicilio: {
                required: true,
                minlength: 10,
                maxlength: 200
            },
            // //omitimos localidad
            codigo_postal: {
                required: true,
                digits: true,
                rangelength: [5, 5]
            },
            banco: {
                required: true,
            },
            clabe: {
                required: true,
                digits: true,
                maxlength: 30
            },
            numero_cuenta: {
                required: true,
                digits: true,
                maxlength: 30
            }
        },
        messages: {
            nombre: {
                required: "Introduce tu nombre",
                minlength: "Debe tener al menos 3 caracteres",
                maxlength: "No debe rebasar los 40 caracteres",
                pattern: "Solo se permiten letras"
            },
            apellido_paterno: {
                required: "Introduce tu apellido paterno",
                minlength: "Debe tener al menos 3 caracteres",
                maxlength: "No debe rebasar los 40 caracteres",
                pattern: "Solo se permiten letras"
            },
            apellido_materno: {
                required: "Introduce tu apellido materno",
                minlength: "Debe tener al menos 3 caracteres",
                maxlength: "No debe rebasar los 40 caracteres",
                pattern: "Solo se permiten letras"
            },
            curp: {
                required: "Introduce una CURP válida",
                rangelength: "La CURP debe tener exactamente 18 caracteres",
                pattern: "Solo se permiten letras mayúsculas y números"
            },
            rfc: {
                required: "Introduce un RFC válido",
                rangelength: "El RFC debe tener exactamente 13 caracteres",
                pattern: "Solo se permiten letras mayúsculas y números"
            },
            honorario: {
                required: "Selecciona una opción",
            },
            tipo_instructor: {
                required: "Selecciona una opción",
            },
            tipo_identificacion: {
                required: "Selecciona el tipo de indentificación",
            },
            folio_ine: {
                required: "Introduce el folio de identificación",
                maxlength: "No debe rebasar los 40 caracteres",
                pattern: "Solo se permiten letras, números y guiones"
            },
            expiracion_identificacion: {
                required: "Ingresa la fecha de expiración de la identificación",
            },
            sexo: {
                required: "Selecciona una opción",
            },
            estado_civil: {
                required: "Selecciona tu estado civil",
            },
            fecha_nacimientoins: {
                required: "Ingresa tu fecha de nacimiento",
            },
            telefono: {
                required: "Introduce tu numero de teléfono",
                rangelength: "El numero debe ser de 10 digitos",
                digits: "Solo se permiten números",
            },
            telefono_casa: {
                rangelength: "El numero debe ser de 10 digitos",
                digits: "Solo se permiten números",
            },
            correo: {
                required: "Introduce un correo electronico",
                email: "Ingresa un correo válido (ej. ejemplo@dominio.com)",
                maxlength: "El campo solo permie 70 caracteres como maximo"
            },
            entidad: {
                required: "Selecciona el estado de residencia",
            },
            municipio: {
                required: "Selecciona el municipio de residencia",
            },
            domicilio: {
                required: "Por favor introduce tu domicilio",
                minlength: "Debe ingresar al menos 10 caracteres",
                minlength: "Se permite un maximo de 200 caracteres"
            },
            // //omitimos localidad
            codigo_postal: {
                required: "Introduce el codigo postal",
                digits: "Solo se permiten números",
                rangelength: "El código postal debe tener exactamente 5 dígitos"
            },
            banco: {
                required: "Selecciona un banco",
            },
            clabe: {
                required: "Introduce la CLABE interbancaria",
                digits: "Solo se permiten números",
                maxlength: "La CLABE no debe exceder los 30 dígitos"
            },
            numero_cuenta: {
                required: "Introduce el número de cuenta",
                digits: "Solo se permiten números",
                maxlength: "El número de cuenta no debe exceder los 30 dígitos"
            }
        },
        // Esta parte añade las clases is-valid e is-invalid
        highlight: function (element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element) {
            $(element).addClass('is-valid').removeClass('is-invalid');
        },
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        errorPlacement: function (error, element) {
            // Coloca el mensaje debajo del input
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });


    //Formulario principal para el pre-registro
    // $('#form_modal_preregistro').validate({
    //     rules: {
    //         curp_modal: {
    //             required: true,
    //             rangelength: [18, 18],
    //             // pattern: /^[A-Z0-9]+$/
    //             pattern: /^[A-Z]{4}\d{6}[HM][A-Z]{2}[A-Z]{3}[A-Z\d]\d$/
    //             // minlength: 18
    //         },
    //         telefono_modal: {
    //             required: true,
    //             rangelength: [10, 10],
    //             digits: true,
    //         }
    //     },
    //     messages: {
    //         curp_modal: {
    //             required: "Introduce una CURP válida",
    //             rangelength: "La CURP debe tener exactamente 18 caracteres",
    //             pattern: "El formato no es válido"
    //         },
    //         telefono_modal: {
    //             required: "Introduce tu numero de teléfono",
    //             rangelength: "El numero debe ser de 10 digitos",
    //             digits: "Solo se permiten números"
    //         }
    //     },
    //     // Esta parte añade las clases is-valid e is-invalid
    //     highlight: function (element) {
    //         $(element).addClass('is-invalid').removeClass('is-valid');
    //     },
    //     unhighlight: function (element) {
    //         $(element).addClass('is-valid').removeClass('is-invalid');
    //     },
    //     errorElement: 'div',
    //     errorClass: 'invalid-feedback',
    //     errorPlacement: function (error, element) {
    //         // Coloca el mensaje debajo del input
    //         if (element.parent('.input-group').length) {
    //             error.insertAfter(element.parent());
    //         } else {
    //             error.insertAfter(element);
    //         }
    //     }
    // });

});
