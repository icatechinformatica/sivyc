$(function(){
    $.validator.addMethod("phoneMX", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
        phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})?[2-9]\d{2}?\d{4}$/);
    }, "Por favor especifique un número valido de teléfono");

    $.validator.addMethod("formatoFecha", function(value, element) {
        return value.match('')
    });

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
      });

    $('#conveniosFrm').validate({
        rules: {
            no_convenio: {
                required: true
            },
            institucion: {
                required: true
            },
            tipo: {
                required: true
            },
            telefono:{
                required: true,
                phoneMX: true
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
            archivo_convenio: {
                required: true,
                extension: "pdf",
                filesize: 2000000
            }
        },
        messages: {
            no_convenio: {
                required: 'El Número de convenio es requerido.'
            },
            institucion: {
                required: 'El campo institución es requerido.'
            },
            tipo: {
                required: 'el campo tipo es requerido.'
            },
            telefono: {
                required: 'el telefono es requerido',
                phoneMX: 'no es un número telefonico dado.'
            },
            sector: {
                required: 'seleccione el tipo de sector.'
            },
            fecha_firma: {
                required: 'la fecha de la firma es requerida'
            },
            fecha_termino: {
                required: 'La fecha de termino es requerida.'
            },
            archivo_convenio: {
                required: "Agregar un documento",
                accept: "No es una extensión valida, son aceptado pdf.",
                filesize: "El tamaño del archivo debe de ser menor a 2 Mb."
            }
        }
    });
});

