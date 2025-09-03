// BLOQUE DE VALIDACIÓN
$.validator.addMethod('filesize', function (value, element, param) {
    if (!element || !element.files || element.files.length === 0) {
        return true;
    }
    return element.files[0].size <= param;
}, 'El archivo no debe superar los 3 MB.');

$("#form-capacitacion").validate({
    rules: {
        ultimo_grado_estudios: {
            required: true
        },
        documento_ultimo_grado: {
            required: function () {
                return $('#ultimo_grado_estudios').val() !== '1' && !registroBladeVars.tieneDocumentoUltimoGrado; // 1 Es NO ESPECIFICADO
            },
            extension: "pdf",
            filesize: 3 * 1024 * 1024 // 3MB
        },
        fecha_documento_ultimo_grado: {
            required: function () {
                return $('#ultimo_grado_estudios').val() !== '1' && !registroBladeVars.tieneDocumentoUltimoGrado; // 1 Es NO ESPECIFICADO
            },
            date: true
        },
        medio_enterado_sistema: {
            required: true
        },
        motivo_eleccion_capacitacion: {
            required: true
        },
        medio_confirmacion: {
            required: true
        }
    },
    messages: {
        ultimo_grado_estudios: "El último grado de estudios es obligatorio.",
        documento_ultimo_grado: {
            required: function () {
                return $('#ultimo_grado_estudios').val() !== '1' ? "El documento del último grado de estudios es obligatorio." : "";
            },
            extension: "El archivo debe ser un PDF.",
            filesize: "El archivo no debe superar los 3 MB."
        },
        fecha_documento_ultimo_grado: {
            required: function () {
                return $('#ultimo_grado_estudios').val() !== '1' ? "La fecha del documento es obligatoria." : "";
            },
            date: "Ingrese una fecha válida."
        },
        medio_enterado_sistema: "Este campo es obligatorio.",
        motivo_eleccion_capacitacion: "Este campo es obligatorio.",
        medio_confirmacion: "Este campo es obligatorio."
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$("#form-grupos-vulnerables").validate({
    rules: {
        'grupos_vulnerables[]': {
            required: function () {
                // Solo es requerido si el checkbox NO está seleccionado
                return !$('#pertenece_a_grupo_vulnerable').is(':checked');
            }
        }
    },
    messages: {
        'grupos_vulnerables[]': {
            required: "Debes seleccionar al menos un grupo vulnerable o 'No pertenezco a un grupo vulnerable'."
        }
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.form-check').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$("#form-contacto").validate({
    rules: {
        telefono_casa: {
            required: false
        },
        telefono_celular: {
            required: true,
            digits: true,
        },
        correo_electronico: {
            required: true,
            email: true
        },
        facebook: {
            required: false
        }
    },
    messages: {
        telefono_celular: {
            required: "El teléfono celular es obligatorio.",
            digits: "Solo números.",
            minlength: "Mínimo 10 dígitos.",
            maxlength: "Máximo 15 dígitos."
        },
        correo_electronico: {
            required: "El correo electrónico es obligatorio.",
            email: "Ingrese un correo válido."
        }
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid');
        if ($(element).val()) {
            $(element).addClass('is-valid');
        } else {
            $(element).removeClass('is-valid');
        }
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

// Validación para la sección Alumno Empleado
$("#form-empleado").validate({
    rules: {
        empresa_trabaja: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        },
        puesto_trabajo: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        },
        antiguedad_trabajo: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        },
        direccion_trabajo: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        }
    },
    messages: {
        empresa_trabaja: "El nombre de la empresa es obligatorio si eres empleado.",
        puesto_trabajo: "El puesto de trabajo es obligatorio si eres empleado.",
        antiguedad_trabajo: "La antigüedad en el trabajo es obligatoria si eres empleado.",
        direccion_trabajo: "La dirección del trabajo es obligatoria si eres empleado.",
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$("#form-datos-personales").validate({
    rules: {
        curp: {
            required: true,
            minlength: 18,
            maxlength: 18,
            alphanumeric: true
        },
        documento_curp: {
            required: function () {
                return esNuevoRegistro === true && !registroBladeVars.tieneDocumentoCURP;
            },
            extension: "pdf",
            filesize: 3 * 1024 * 1024 // 3MB
        },
        fecha_documento_curp: {
            required: true,
            date: true
        },
        nombre_s: {
            required: true
        },
        primer_apellido: {
            required: true
        },
        segundo_apellido: {
            required: true
        },
        entidad_de_nacimiento: {
            required: false
        },
        fecha_de_nacimiento: {
            required: true
        },
        sexo_input: {
            required: true
        },
        nacionalidad_input: {
            required: true
        },
        estado_civil_select: {
            required: true
        }
    },
    messages: {
        curp: {
            required: "La CURP es obligatoria.",
            minlength: "La CURP debe tener 16 caracteres.",
            maxlength: "La CURP debe tener 16 caracteres.",
            alphanumeric: "La CURP solo puede contener letras y números."
        },
        documento_curp: {
            required: "El documento CURP es obligatorio.",
            extension: "El archivo debe ser PDF.",
            filesize: "El archivo no debe superar los 3 MB."
        },
        fecha_documento_curp: {
            required: "La fecha del documento CURP es obligatoria.",
            date: "Ingrese una fecha válida."
        },
        nombre_s: {
            required: "El nombre es obligatorio."
        },
        primer_apellido: {
            required: "El primer apellido es obligatorio."
        },
        segundo_apellido: {
            required: "El segundo apellido es obligatorio."
        },
        entidad_de_nacimiento: {
            required: "La entidad de nacimiento es obligatoria."
        },
        fecha_de_nacimiento: {
            required: "La fecha de nacimiento es obligatoria.",
            date: "Ingrese una fecha válida."
        },
        sexo_input: {
            required: "El sexo es obligatorio."
        },
        nacionalidad_input: {
            required: "La nacionalidad es obligatoria."
        },
        estado_civil_select: {
            required: "El estado civil es obligatorio."
        }
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

$("#form-domicilio").validate({
    rules: {
        pais_select: {
            required: true
        },
        estado_select: {
            required: function () {
                // Requerido solo si el país seleccionado es México
                var txt = $('#pais_select option:selected').text().toUpperCase();
                return txt.includes('MEXIC');
            }
        },
        municipio_select: {
            required: function () {
                var txt = $('#pais_select option:selected').text().toUpperCase();
                return txt.includes('MEXIC');
            }
        },
        localidad: {
            required: true
        },
        codigo_postal: {
            required: true,
            digits: true,
            minlength: 5,
            maxlength: 5
        },
        domicilio: {
            required: true
        },
        colonia: {
            required: true
        }
    },
    messages: {
        pais_select: "El país es obligatorio.",
        estado_select: "El estado es obligatorio para México.",
        municipio_select: "El municipio es obligatorio para México.",
        localidad: "La localidad es obligatoria.",
        codigo_postal: {
            required: "El código postal es obligatorio.",
            digits: "El código postal debe contener solo números.",
            minlength: "El código postal debe tener 5 dígitos.",
            maxlength: "El código postal debe tener 5 dígitos."
        },
        domicilio: "La dirección es obligatoria.",
        colonia: "La colonia es obligatoria."
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

// Validación para la sección Alumno Laboral
$("#form-laboral").validate({
    rules: {
        empleado_aspirante: {
            required: false
        },
        nombre_empresa: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        },
        giro_empresa: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        },
        puesto: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        },
        antiguedad: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            },
            digits: true,
            maxlength: 2
        },
        horario_trabajo: {
            required: function () {
                return $('#empleado_aspirante').is(':checked');
            }
        }
    },
    messages: {
        nombre_empresa: "El nombre de la empresa es obligatorio si eres empleado.",
        giro_empresa: "El giro de la empresa es obligatorio si eres empleado.",
        puesto: "El puesto es obligatorio si eres empleado.",
        antiguedad: {
            required: "La antigüedad es obligatoria si eres empleado.",
            digits: "Solo números.",
            maxlength: "Máximo 2 dígitos."
        },
        horario_trabajo: "El horario de trabajo es obligatorio si eres empleado."
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});

// * Método personalizado para alfanumérico
$.validator.addMethod("alphanumeric", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
}, "Solo letras y números");

// Validación para la sección CERSS
$("#form-cerss").validate({
    rules: {
        numero_expediente: {
            required: {
                depends: function () {
                    return $('#aspirante_cerss').is(':checked');
                }
            },
            digits: true
        },
        documento_ficha_cerss: {
            required: function() {
                return $('#aspirante_cerss').is(':checked') && !registroBladeVars.tieneDocumentoFichaCerss;
            },
            extension: "pdf"
        }
    },
    messages: {
        numero_expediente: {
            required: "El número de expediente es obligatorio si eres aspirante CERSS.",
        },
        documento_ficha_cerss: {
            required: "La ficha CERSS es obligatoria si eres aspirante CERSS.",
            extension: "El archivo debe ser un PDF."
        }
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
        $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid').addClass('is-valid');
    },
    errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});
