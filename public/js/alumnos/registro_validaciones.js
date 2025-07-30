$("#form-alumno").validate({
    rules: {
        curp: {
            required: true,
            minlength: 18,
            maxlength: 18,
            alphanumeric: true
        },
        documento_curp: {
            required: true,
            extension: "pdf"
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
            required: true
        },
        fecha_de_nacimiento: {
            required: true
        },
        sexo: {
            required: true
        },
        nacionalidad: {
            required: true
        },
        estado_civil: {
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
        nombre_s: "El nombre es obligatorio.",
        primer_apellido: "El primer apellido es obligatorio.",
        segundo_apellido: "El segundo apellido es obligatorio.",
        entidad_de_nacimiento: "La entidad de nacimiento es obligatoria.",
        fecha_de_nacimiento: "La fecha de nacimiento es obligatoria.",
        sexo: "El sexo es obligatorio.",
        nacionalidad: "La nacionalidad es obligatoria.",
        estado_civil: "El estado civil es obligatorio."
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