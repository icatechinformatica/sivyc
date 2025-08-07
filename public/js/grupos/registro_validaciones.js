// ! Seccion Informacion general
$("#info_general_form").validate({
    rules: {
        imparticion: {
            required: true
        },
        modalidad: {
            required: true
        },
        unidad_accion_movil: {
            required: true
        },
        servicio: {
            required: true
        },
        curso: {
            required: true
        }
    },
    messages: {
        imparticion: {
            required: "Por favor seleccione el tipo de impartición"
        },
        modalidad: {
            required: "Por favor seleccione la modalidad"
        },
        unidad_accion_movil: {
            required: "Por favor seleccione la unidad/acción móvil"
        },
        servicio: {
            required: "Por favor seleccione el servicio"
        },
        curso: {
            required: "Por favor seleccione el curso"
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

// ! Ubicación
$("#ubicacion_form").validate({
    rules: {
        municipio: {
            required: true
        },
        localidad: {
            required: true
        },
        nombre_lugar: {
            required: true
        },
        calle_numero: {
            required: true
        },
        colonia: {
            required: true
        },
        codigo_postal: {
            required: true,
            minlength: 5,
            maxlength: 5,
            digits: true
        },
        referencias: {
            required: true
        }
    },
    messages: {
        municipio: {
            required: "Por favor seleccione el municipio"
        },
        localidad: {
            required: "Por favor seleccione la localidad"
        },
        nombre_lugar: {
            required: "Por favor ingrese el nombre del lugar"
        },
        calle_numero: {
            required: "Por favor ingrese la calle y número"
        },
        colonia: {
            required: "Por favor ingrese la colonia o barrio"
        },
        codigo_postal: {
            required: "Por favor ingrese el código postal",
            minlength: "El código postal debe tener 5 dígitos",
            maxlength: "El código postal debe tener 5 dígitos",
            digits: "El código postal solo debe contener números"
        },
        referencias: {
            required: "Por favor ingrese referencias"
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

// ! Organismo Publico
$("#organismo_form").validate({
    rules: {
        organismo_publico: {
            required: true
        },
        nombre_representante: {
            required: true
        },
        telefono_representante: {
            required: true
        }
    },
    messages: {
        organismo_publico: {
            required: "Por favor ingrese el nombre del organismo público"
        },
        nombre_representante: {
            required: "Por favor ingrese el nombre del representante"
        },
        telefono_representante: {
            required: "Por favor ingrese el teléfono del representante",
            minlength: "El teléfono debe tener 10 dígitos",
            maxlength: "El teléfono debe tener 10 dígitos",
            digits: "El teléfono solo debe contener números"
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

$("#opciones_form").validate({
    rules: {
        medio_virtual: {
            required: function () {
                return $("#imparticion").val() == 2; // Si la impartición es a distancia
            }
        },
        enlace_virtual: {
            required: function () {
                return $("#imparticion").val() == 2; // Si la impartición es a distancia
            }
        },
        convenio_especifico: {
            required: true
        },
        fecha_convenio: {
            required: true
        }
    },
    messages: {
        medio_virtual: {
            required: "Por favor ingrese el medio virtual"
        },
        enlace_virtual: {
            required: "Por favor ingrese el enlace virtual"
        },
        convenio_especifico: {
            required: "Por favor ingrese el convenio específico"
        },
        fecha_convenio: {
            required: "Por favor ingrese la fecha del convenio"
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
