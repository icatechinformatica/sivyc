$(function() {
    // $.validator.addMethod('filesize', function (value, element, arg) {
    //     var minsize = 1000;
    //     if ((value>minsize)&&(value<=arg)) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // });
    $("#registercomunicado").validate({
        // crear reglas de validación
        rules: {
            titulo: {
                required: true,
                maxlength: 255
            },
            localizacion: {
                required: true,
            },
            contenido: {
                required: true
            },
            imagen_comunicado: {
                required: true,
                accept:"image/jpeg,image/png,image/jpg",
                filesize: 2
            },
            categoria: {
                required: true,
            },
            url: {
                required: true,
                maxlength: 100
            }
        },
        // especificamos mensajes para cada regla
        messages: {
            titulo: {
                required: "Introduce el titulo",
                maxlength: "Haz excedido el límite"
            },
            localizacion: {
                required: "Introduce la localidad"
            },
            contenido: {
                required: "No hay contenido"
            },
            imagen_comunicado: {
                required: "Agregar una Imagen",
                accept: "No es una extensión valida, son aceptados jpeg,jpg,png.",
                filesize: "Archivo debe de ser menos de 2 Mb."
            },
            categoria: {
                required: "Introducir la categoría"
            },
            url: {
                required: "Introducir url",
                maxlength: 'Haz excedido el límite'
            }
        }
    });
});